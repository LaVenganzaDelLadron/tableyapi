from sqlalchemy.orm import Session

from models.orders import Orders, OrderStatus
from models.users import User, UserRole
from schemas.order_status import OrderStatus as OrderStatusSchema




def index(db: Session):
    return db.query(Orders).all()


def index_by_user(db: Session, user_id: int):
    return db.query(Orders).filter(Orders.user_id == user_id).all()


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
    db.commit()
    db.refresh(order)
    return order



def destroy(db: Session, order_id: int):
    data = db.query(Orders).filter(Orders.id == order_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return order_id
