
# Explanation:
# This file is part of the tableyapi backend and contains Business logic and service layer code for orders service.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy.orm import Session

from models.cart_items import CartItems
from models.carts import Carts
from models.order_items import OrderItems
from models.order_status_history import OrderStatusHistory
from models.orders import Orders, OrderStatus
from models.products import Products
from models.users import User, UserRole
from api.pagination import paginate
from schemas.order_status import OrderStatus as OrderStatusSchema




def index(db: Session, page: int | None = None, limit: int | None = None, status: str | None = None):
    query = db.query(Orders).order_by(Orders.created_at.desc(), Orders.id.desc())
    if status:
        query = query.filter(Orders.status == _status_value(status))
    if page is not None or limit is not None:
        return paginate(query, page or 1, limit or 20)
    return query.all()


def index_by_user(db: Session, user_id: int, page: int | None = None, limit: int | None = None, status: str | None = None):
    query = db.query(Orders).filter(Orders.user_id == user_id).order_by(Orders.created_at.desc(), Orders.id.desc())
    if status:
        query = query.filter(Orders.status == _status_value(status))
    if page is not None or limit is not None:
        return paginate(query, page or 1, limit or 20)
    return query.all()


def store(db: Session, user_id: int, information_id: int | None, total_amount: float, payment_method: str, status: str | None = None):
    data = Orders(
        user_id=user_id,
        information_id=information_id,
        total_amount=total_amount,
        status=status or OrderStatus.PENDING.value,
        payment_method=payment_method,
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return data


def checkout_from_cart(db: Session, user_id: int, information_id: int | None, payment_method: str):
    cart = db.query(Carts).filter(Carts.user_id == user_id).first()
    if not cart:
        return None, "Cart is empty"

    cart_items = db.query(CartItems).filter(CartItems.cart_id == cart.id).all()
    if not cart_items:
        return None, "Cart is empty"

    product_ids = [item.product_id for item in cart_items]
    products = {
        product.id: product
        for product in db.query(Products).filter(Products.id.in_(product_ids)).all()
    }

    total_amount = 0.0
    snapshots = []
    for item in cart_items:
        product = products.get(item.product_id)
        if not product:
            return None, f"Product {item.product_id} no longer exists"
        if str(product.status or "").lower() != "active":
            return None, f"Product {product.name} is not available"
        if product.stock is None or product.stock < item.quantity:
            return None, f"Insufficient stock for {product.name}"

        price = float(product.price or 0)
        subtotal = price * item.quantity
        total_amount += subtotal
        snapshots.append((item, product, price, subtotal))

    order = Orders(
        user_id=user_id,
        information_id=information_id,
        total_amount=total_amount,
        status=OrderStatus.PENDING.value,
        payment_method=payment_method,
    )
    db.add(order)
    db.flush()

    for item, product, price, subtotal in snapshots:
        product.stock -= item.quantity
        db.add(OrderItems(
            order_id=order.id,
            product_id=product.id,
            product_name=product.name,
            quantity=item.quantity,
            price=price,
            subtotal=subtotal,
        ))
        db.delete(item)

    db.commit()
    db.refresh(order)
    return order, None


def show(db: Session, order_id: int):
    data = db.query(Orders).filter(Orders.id == order_id).first()

    return data


def show_for_user(db: Session, order_id: int, user_id: int):
    data = db.query(Orders).filter(Orders.id == order_id, Orders.user_id == user_id).first()

    return data


def update(db: Session, order_id: int, information_id: int | None, total_amount: float, status: str, payment_method: str):
    data = db.query(Orders).filter(Orders.id == order_id).first()

    if not data:
        return None

    data.information_id = information_id
    data.total_amount = total_amount

    # Only allow constrained status changes.
    if status != data.status:
        transition = change_order_status(db, order_id=order_id, new_status=status)
        if transition is None:
            return None

    # re-load to attach payment_method
    data = db.query(Orders).filter(Orders.id == order_id).first()
    if not data:
        return None

    data.payment_method = payment_method
    db.commit()
    db.refresh(data)

    return data




def _allowed_status_values() -> set[str]:
    return {s.value for s in OrderStatusSchema}


def _can_transition(current: str, new: str) -> bool:
    # Constrained lifecycle transitions.
    # PENDING -> PAID -> SHIPPED -> COMPLETED
    # PENDING/PAID -> CANCELLED
    if current == new:
        return False

    transitions: dict[str, set[str]] = {
        "PENDING": {"PAID", "CANCELLED"},
        "PAID": {"SHIPPED", "CANCELLED"},
        "SHIPPED": {"COMPLETED"},
        "COMPLETED": set(),
        "CANCELLED": set(),
    }
    return new in transitions.get(current, set())


def change_order_status(db: Session, order_id: int, new_status: str):
    new_status = _status_value(new_status)
    if new_status not in _allowed_status_values():
        return None

    data = db.query(Orders).filter(Orders.id == order_id).first()
    if not data:
        return None

    current_status = data.status
    if current_status not in _allowed_status_values():
        return None

    if not _can_transition(current=current_status, new=new_status):
        return None

    data.status = new_status
    _record_status_history(db, order_id, current_status, new_status, None)
    db.commit()
    db.refresh(data)

    return data


def update_status(db: Session, order_id: int, status: str):
    return change_order_status(db, order_id=order_id, new_status=status)


def _status_value(status) -> str:
    return getattr(status, "value", status)


def _role_value(user: User) -> str:
    return getattr(user.role, "value", user.role)


def transition_order_status(order: Orders, new_status: OrderStatus | OrderStatusSchema | str, by_user: User, db: Session) -> Orders | None:
    new_status_value = _status_value(new_status)
    if new_status_value not in _allowed_status_values():
        return None

    if _role_value(by_user) != UserRole.ADMIN.value and order.user_id != by_user.id:
        return None

    current_status = order.status
    if current_status == new_status_value:
        return order

    if not _can_transition(current=current_status, new=new_status_value):
        return None

    order.status = new_status_value
    _record_status_history(db, order.id, current_status, new_status_value, by_user.id)
    db.commit()
    db.refresh(order)
    return order


def status_history(db: Session, order_id: int):
    return (
        db.query(OrderStatusHistory)
        .filter(OrderStatusHistory.order_id == order_id)
        .order_by(OrderStatusHistory.created_at.asc(), OrderStatusHistory.id.asc())
        .all()
    )


def _record_status_history(db: Session, order_id: int, from_status: str, to_status: str, changed_by_user_id: int | None):
    db.add(OrderStatusHistory(
        order_id=order_id,
        from_status=from_status,
        to_status=to_status,
        changed_by_user_id=changed_by_user_id,
    ))



def destroy(db: Session, order_id: int):
    data = db.query(Orders).filter(Orders.id == order_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return order_id