
# Explanation:
# This file is part of the tableyapi backend and contains Business logic and service layer code for shipping service.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy.orm import Session
from models.orders import Orders, OrderStatus
from models.shipping import Shipping


def index(db: Session):
    return db.query(Shipping).all()


def store(db: Session, order_id: int, courier_id: str, tracking_number: str, shipping_fee: float, shipped_at, delivered_at):
    data = Shipping(
        order_id=order_id,
        courier_id=courier_id,
        tracking_number=tracking_number,
        shipping_fee=shipping_fee,
        shipped_at=shipped_at,
        delivered_at=delivered_at,
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return data


def show(db: Session, shipping_id: int):
    data = db.query(Shipping).filter(Shipping.id == shipping_id).first()

    return data


def update(db: Session, shipping_id: int, order_id: int, courier_id: str, tracking_number: str, shipping_fee: float, shipped_at, delivered_at):
    data = db.query(Shipping).filter(Shipping.id == shipping_id).first()

    if not data:
        return None

    data.order_id = order_id
    data.courier_id = courier_id
    data.tracking_number = tracking_number
    data.shipping_fee = shipping_fee
    data.shipped_at = shipped_at
    data.delivered_at = delivered_at

    db.commit()
    db.refresh(data)

    return data


def destroy(db: Session, shipping_id: int):
    data = db.query(Shipping).filter(Shipping.id == shipping_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return shipping_id


def create_or_update_shipping(order: Orders, shipping_payload, db: Session) -> Shipping | None:
    if order.status not in {OrderStatus.PAID.value, OrderStatus.SHIPPED.value}:
        return None

    data = db.query(Shipping).filter(Shipping.order_id == order.id).first()
    if not data:
        data = Shipping(order_id=order.id)
        db.add(data)

    data.courier_id = shipping_payload.courier_id
    data.tracking_number = shipping_payload.tracking_number
    data.shipping_fee = shipping_payload.shipping_fee
    data.shipped_at = shipping_payload.shipped_at
    data.delivered_at = shipping_payload.delivered_at

    if data.delivered_at:
        if order.status != OrderStatus.SHIPPED.value:
            return None
        order.status = OrderStatus.COMPLETED.value
    elif data.shipped_at and order.status == OrderStatus.PAID.value:
        order.status = OrderStatus.SHIPPED.value

    db.commit()
    db.refresh(data)
    db.refresh(order)
    return data