
# Explanation:
# This file is part of the tableyapi backend and contains Business logic and service layer code for cart items service.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy.orm import Session
from models.cart_items import CartItems
from models.carts import Carts


def index(db: Session):
    return db.query(CartItems).all()


def index_by_user(db: Session, user_id: int):
    data = (
        db.query(CartItems)
        .join(Carts, CartItems.cart_id == Carts.id)
        .filter(Carts.user_id == user_id)
        .all()
    )
    return data


def store(db: Session, cart_id: int, product_id: int, quantity: int):
    data = CartItems(
        cart_id=cart_id,
        product_id=product_id,
        quantity=quantity,
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return data


def show(db: Session, cart_item_id: int):
    data = db.query(CartItems).filter(CartItems.id == cart_item_id).first()

    return data


def show_for_user(db: Session, cart_item_id: int, user_id: int):
    data = (
        db.query(CartItems)
        .join(Carts, CartItems.cart_id == Carts.id)
        .filter(CartItems.id == cart_item_id, Carts.user_id == user_id)
        .first()
    )

    return data


def update(db: Session, cart_item_id: int, cart_id: int, product_id: int, quantity: int):
    data = db.query(CartItems).filter(CartItems.id == cart_item_id).first()

    if not data:
        return None

    data.cart_id = cart_id
    data.product_id = product_id
    data.quantity = quantity

    db.commit()
    db.refresh(data)

    return data


def update_for_user(db: Session, cart_item_id: int, user_id: int, product_id: int, quantity: int):
    data = (
        db.query(CartItems)
        .join(Carts, CartItems.cart_id == Carts.id)
        .filter(CartItems.id == cart_item_id, Carts.user_id == user_id)
        .first()
    )

    if not data:
        return None

    data.product_id = product_id
    data.quantity = quantity

    db.commit()
    db.refresh(data)
    return data


def destroy(db: Session, cart_item_id: int):
    data = db.query(CartItems).filter(CartItems.id == cart_item_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return cart_item_id


def destroy_for_user(db: Session, cart_item_id: int, user_id: int):
    data = (
        db.query(CartItems)
        .join(Carts, CartItems.cart_id == Carts.id)
        .filter(CartItems.id == cart_item_id, Carts.user_id == user_id)
        .first()
    )

    if not data:
        return None

    db.delete(data)
    db.commit()
    return cart_item_id