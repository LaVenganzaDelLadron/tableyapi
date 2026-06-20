from sqlalchemy.orm import Session
from models.shipping import Shipping


def index(db: Session):
    data = db.query(Shipping).all()
    if not data:
        return {"message": "Shipping records not found"}
    return {
        "message": "Shipping records found",
        "data": data
    }


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

    return {
        "message": "Shipping record created successfully",
        "data": data
    }


def show(db: Session, shipping_id: int):
    data = db.query(Shipping).filter(Shipping.id == shipping_id).first()

    if not data:
        return {"message": "Shipping record not found"}
    return {
        "message": "Shipping record found",
        "data": data
    }


def update(db: Session, shipping_id: int, order_id: int, courier_id: str, tracking_number: str, shipping_fee: float, shipped_at, delivered_at):
    data = db.query(Shipping).filter(Shipping.id == shipping_id).first()

    if not data:
        return {"message": "Shipping record not found"}

    data.order_id = order_id
    data.courier_id = courier_id
    data.tracking_number = tracking_number
    data.shipping_fee = shipping_fee
    data.shipped_at = shipped_at
    data.delivered_at = delivered_at

    db.commit()
    db.refresh(data)

    return {
        "message": "Shipping record updated successfully",
        "data": data
    }


def destroy(db: Session, shipping_id: int):
    data = db.query(Shipping).filter(Shipping.id == shipping_id).first()

    if not data:
        return {"message": "Shipping record not found"}

    db.delete(data)
    db.commit()
    return {
        "message": "Shipping record deleted successfully",
        "data": shipping_id
    }
