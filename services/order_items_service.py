from sqlalchemy.orm import Session
from models.order_items import OrderItems


def index(db: Session):
    return db.query(OrderItems).all()


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

    return data


def show(db: Session, order_item_id: int):
    data = db.query(OrderItems).filter(OrderItems.id == order_item_id).first()

    return data


def update(db: Session, order_item_id: int, order_id: int, product_id: int, quantity: int, price: float):
    data = db.query(OrderItems).filter(OrderItems.id == order_item_id).first()


    if not data:
        return None

    data.order_id = order_id
    data.product_id = product_id
    data.quantity = quantity
    data.price = price

    db.commit()
    db.refresh(data)

    return data


def destroy(db: Session, order_item_id: int):
    data = db.query(OrderItems).filter(OrderItems.id == order_item_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return order_item_id
