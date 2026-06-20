from sqlalchemy.orm import Session
from models.cart_items import CartItems


def index(db: Session):
    data = db.query(CartItems).all()
    if not data:
        return {"message": "Cart items not found"}
    return {
        "message": "Cart items found",
        "data": data
    }


def store(db: Session, cart_id: int, quantity: int):
    data = CartItems(
        cart_id=cart_id,
        quantity=quantity,
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return {
        "message": "Cart item created successfully",
        "data": data
    }


def show(db: Session, cart_item_id: int):
    data = db.query(CartItems).filter(CartItems.id == cart_item_id).first()

    if not data:
        return {"message": "Cart item not found"}
    return {
        "message": "Cart item found",
        "data": data
    }


def update(db: Session, cart_item_id: int, cart_id: int, quantity: int):
    data = db.query(CartItems).filter(CartItems.id == cart_item_id).first()

    if not data:
        return {"message": "Cart item not found"}

    data.cart_id = cart_id
    data.quantity = quantity

    db.commit()
    db.refresh(data)

    return {
        "message": "Cart item updated successfully",
        "data": data
    }


def destroy(db: Session, cart_item_id: int):
    data = db.query(CartItems).filter(CartItems.id == cart_item_id).first()

    if not data:
        return {"message": "Cart item not found"}

    db.delete(data)
    db.commit()
    return {
        "message": "Cart item deleted successfully",
        "data": cart_item_id
    }
