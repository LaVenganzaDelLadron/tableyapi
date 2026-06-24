from api.pagination import paginate
from sqlalchemy.orm import Session
from models.orders import Orders, OrderStatus
from models.payments import Payments


def index(db: Session, page: int | None = None, limit: int | None = None, payment_status: str | None = None):
    query = db.query(Payments).order_by(Payments.created_at.desc(), Payments.id.desc())
    if payment_status:
        query = query.filter(Payments.payment_status == payment_status)
    if page is not None or limit is not None:
        return paginate(query, page or 1, limit or 20)
    return query.all()


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
    if round(float(payment_payload.amount), 2) != round(float(order.total_amount or 0), 2):
        return None

    transaction = db.query(Payments).filter(Payments.transaction_id == payment_payload.transaction_id).first()
    if transaction and transaction.order_id != order.id:
        return None
    if transaction:
        return transaction

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
