from sqlalchemy.orm import Session
from models.orders import Orders


def index(db: Session):
    data = db.query(Orders).all()
    if not data:
        return {"message": "Orders not found"}
    return {
        "message": "Orders found",
        "data": data
    }


def store(db: Session, information_id: int, total_amount: float, status: str, payment_method: str):
    data = Orders(
        information_id=information_id,
        total_amount=total_amount,
        status=status,
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


def update(db: Session, order_id: int, information_id: int, total_amount: float, status: str, payment_method: str):
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
