from sqlalchemy.orm import Session
from models.payments import Payments


def index(db: Session):
    data = db.query(Payments).all()
    if not data:
        return {"message": "Payments not found"}
    return {
        "message": "Payments found",
        "data": data
    }


def store(db: Session, order_id: int, amount: float, payment_method: str, payment_status: str, transaction_id: str):
    data = Payments(
        order_id=order_id,
        amount=amount,
        payment_method=payment_method,
        payment_status=payment_status,
        transaction_id=transaction_id,
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return {
        "message": "Payment created successfully",
        "data": data
    }


def show(db: Session, payment_id: int):
    data = db.query(Payments).filter(Payments.id == payment_id).first()

    if not data:
        return {"message": "Payment not found"}
    return {
        "message": "Payment found",
        "data": data
    }


def update(db: Session, payment_id: int, order_id: int, amount: float, payment_method: str, payment_status: str, transaction_id: str):
    data = db.query(Payments).filter(Payments.id == payment_id).first()

    if not data:
        return {"message": "Payment not found"}

    data.order_id = order_id
    data.amount = amount
    data.payment_method = payment_method
    data.payment_status = payment_status
    data.transaction_id = transaction_id

    db.commit()
    db.refresh(data)

    return {
        "message": "Payment updated successfully",
        "data": data
    }


def destroy(db: Session, payment_id: int):
    data = db.query(Payments).filter(Payments.id == payment_id).first()

    if not data:
        return {"message": "Payment not found"}

    db.delete(data)
    db.commit()
    return {
        "message": "Payment deleted successfully",
        "data": payment_id
    }
