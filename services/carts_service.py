from sqlalchemy.orm import Session
from models.carts import Carts


def index(db: Session):
    data = db.query(Carts).all()
    if not data:
        return {"message": "Carts not found"}
    return {
        "message": "Carts found",
        "data": data
    }


def store(db: Session, user_id: int):
    data = Carts(
        user_id=user_id,
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return {
        "message": "Cart created successfully",
        "data": data
    }


def show(db: Session, cart_id: int):
    data = db.query(Carts).filter(Carts.id == cart_id).first()

    if not data:
        return {"message": "Cart not found"}
    return {
        "message": "Cart found",
        "data": data
    }


def update(db: Session, cart_id: int, user_id: int):
    data = db.query(Carts).filter(Carts.id == cart_id).first()

    if not data:
        return {"message": "Cart not found"}

    data.user_id = user_id

    db.commit()
    db.refresh(data)

    return {
        "message": "Cart updated successfully",
        "data": data
    }


def destroy(db: Session, cart_id: int):
    data = db.query(Carts).filter(Carts.id == cart_id).first()

    if not data:
        return {"message": "Cart not found"}

    db.delete(data)
    db.commit()
    return {
        "message": "Cart deleted successfully",
        "data": cart_id
    }
