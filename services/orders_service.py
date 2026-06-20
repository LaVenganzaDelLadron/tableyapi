from sqlalchemy.orm import Session
from models.orders import OrderStatus, Orders


def index(db: Session):
    data = db.query(Orders).all()
    if not data:
        return {"message": "Orders not found"}
    return {
        "message": "Orders found",
        "data": data
    }


def index_by_user(db: Session, user_id: int):
    data = db.query(Orders).filter(Orders.user_id == user_id).all()
    return {
        "message": "Orders found",
        "data": data,
    }


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

    return {
        "message": "Order created successfully",
        "data": data
    }


def show(db: Session, order_id: int):
    data = db.query(Orders).filter(Orders.id == order_id).first()

    if not data:
        return {"message": "Order not found"}
    return {
        "message": "Order found",
        "data": data
    }


def show_for_user(db: Session, order_id: int, user_id: int):
    data = db.query(Orders).filter(Orders.id == order_id, Orders.user_id == user_id).first()

    if not data:
        return None
    return {
        "message": "Order found",
        "data": data
    }


def update(db: Session, order_id: int, information_id: int | None, total_amount: float, status: str, payment_method: str):
    data = db.query(Orders).filter(Orders.id == order_id).first()

    if not data:
        return {"message": "Order not found"}

    data.information_id = information_id
    data.total_amount = total_amount
    data.status = status
    data.payment_method = payment_method

    db.commit()
    db.refresh(data)

    return {
        "message": "Order updated successfully",
        "data": data
    }


def update_status(db: Session, order_id: int, status: str):
    allowed_statuses = {item.value for item in OrderStatus}
    if status not in allowed_statuses:
        return None

    data = db.query(Orders).filter(Orders.id == order_id).first()
    if not data:
        return None

    data.status = status
    db.commit()
    db.refresh(data)

    return {
        "message": "Order status updated successfully",
        "data": data
    }


def destroy(db: Session, order_id: int):
    data = db.query(Orders).filter(Orders.id == order_id).first()

    if not data:
        return {"message": "Order not found"}

    db.delete(data)
    db.commit()
    return {
        "message": "Order deleted successfully",
        "data": order_id
    }
