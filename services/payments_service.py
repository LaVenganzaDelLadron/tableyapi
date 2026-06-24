from sqlalchemy.orm import Session
from models.orders import Orders, OrderStatus
from models.payments import Payments


def index(db: Session):
    return db.query(Payments).all()


def store(db: Session, order_id: int, amount: float, payment_method: str, payment_status: str, transaction_id: str):
    existing = db.query(Payments).filter(
        Payments.order_id == order_id,
        Payments.transaction_id == transaction_id,
    ).first()
    if existing:
        return existing

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

    return data


def show(db: Session, payment_id: int):
    data = db.query(Payments).filter(Payments.id == payment_id).first()

    return data


def update(db: Session, payment_id: int, order_id: int, amount: float, payment_method: str, payment_status: str, transaction_id: str):
    data = db.query(Payments).filter(Payments.id == payment_id).first()

    if not data:
        return None

    data.order_id = order_id
    data.amount = amount
    data.payment_method = payment_method
    data.payment_status = payment_status
    data.transaction_id = transaction_id

    db.commit()
    db.refresh(data)

    return data


def destroy(db: Session, payment_id: int):
    data = db.query(Payments).filter(Payments.id == payment_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return payment_id


def _is_successful_status(status: str) -> bool:
    return status.lower() in {"paid", "completed", "success", "successful"}


def apply_payment(order: Orders, payment_payload, db: Session) -> Payments | None:
    successful = _is_successful_status(payment_payload.payment_status)
    if successful and order.status not in {OrderStatus.PENDING.value, OrderStatus.PAID.value, OrderStatus.SHIPPED.value, OrderStatus.COMPLETED.value}:
        return None

    payment = store(
        db,
        order.id,
        payment_payload.amount,
        payment_payload.payment_method,
        payment_payload.payment_status,
        payment_payload.transaction_id,
    )

    if successful:
        if order.status == OrderStatus.PENDING.value:
            order.status = OrderStatus.PAID.value
            db.commit()
            db.refresh(order)

    return payment
