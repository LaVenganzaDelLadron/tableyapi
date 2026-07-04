
# Explanation:
# This file is part of the tableyapi backend and contains Business logic and service layer code for carts service.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy.orm import Session
from models.carts import Carts


def index(db: Session):
    return db.query(Carts).all()


def index_by_user(db: Session, user_id: int):
    return db.query(Carts).filter(Carts.user_id == user_id).all()


def get_or_create_by_user(db: Session, user_id: int):
    data = db.query(Carts).filter(Carts.user_id == user_id).first()
    if data:
        return data

    data = Carts(user_id=user_id)
    db.add(data)
    db.commit()
    db.refresh(data)
    return data


def store(db: Session, user_id: int):
    data = Carts(
        user_id=user_id,
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return data


def show(db: Session, cart_id: int):
    data = db.query(Carts).filter(Carts.id == cart_id).first()

    return data


def show_for_user(db: Session, cart_id: int, user_id: int):
    data = db.query(Carts).filter(Carts.id == cart_id, Carts.user_id == user_id).first()

    return data


def update(db: Session, cart_id: int, user_id: int):
    data = db.query(Carts).filter(Carts.id == cart_id, Carts.user_id == user_id).first()

    if not data:
        return None

    data.user_id = user_id

    db.commit()
    db.refresh(data)

    return data


def destroy(db: Session, cart_id: int):
    data = db.query(Carts).filter(Carts.id == cart_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return cart_id


def destroy_for_user(db: Session, cart_id: int, user_id: int):
    data = db.query(Carts).filter(Carts.id == cart_id, Carts.user_id == user_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return cart_id