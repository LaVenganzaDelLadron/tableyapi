from sqlalchemy.orm import Session
from models.order_items import OrderItems


def index(db: Session):
    data = db.query(OrderItems).all()
    if not data:
        return {"message": "Order items not found"}
    return {
        "message": "Order items found",
        "data": data
    }


def store(db: Session, order_id: int, product_id: int, quantity: int, price: float):
    data = OrderItems(
        order_id=order_id,
        product_id=product_id,
        quantity=quantity,
        price=price,
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return {
        "message": "Order item created successfully",
        "data": data
    }


def show(db: Session, order_item_id: int):
    data = db.query(OrderItems).filter(OrderItems.id == order_item_id).first()

    if not data:
        return {"message": "Order item not found"}
    return {
        "message": "Order item found",
        "data": data
    }


def update(db: Session, order_item_id: int, order_id: int, product_id: int, quantity: int, price: float):
    data = db.query(OrderItems).filter(OrderItems.id == order_item_id).first()

    if not data:
        return {"message": "Order item not found"}

    data.order_id = order_id
    data.product_id = product_id
    data.quantity = quantity
    data.price = price

    db.commit()
    db.refresh(data)

    return {
        "message": "Order item updated successfully",
        "data": data
    }


def destroy(db: Session, order_item_id: int):
    data = db.query(OrderItems).filter(OrderItems.id == order_item_id).first()

    if not data:
        return {"message": "Order item not found"}

    db.delete(data)
    db.commit()
    return {
        "message": "Order item deleted successfully",
        "data": order_item_id
    }
