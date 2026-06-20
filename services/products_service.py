from sqlalchemy.orm import Session
from models.products import Products


def index(db: Session):
    data = db.query(Products).all()
    if not data:
        return {"message": "Products not found"}
    return {
        "message": "Products found",
        "data": data
    }


def store(db: Session, category_id: int, name: str, description: str, price: float, stock: int, image: str, status: str):
    data = Products(
        category_id=category_id,
        name=name,
        description=description,
        price=price,
        stock=stock,
        image=image,
        status=status,
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return {
        "message": "Product created successfully",
        "data": data
    }


def show(db: Session, product_id: int):
    data = db.query(Products).filter(Products.id == product_id).first()

    if not data:
        return {"message": "Product not found"}
    return {
        "message": "Product found",
        "data": data
    }


def update(db: Session, product_id: int, category_id: int, name: str, description: str, price: float, stock: int, image: str, status: str):
    data = db.query(Products).filter(Products.id == product_id).first()

    if not data:
        return {"message": "Product not found"}

    data.category_id = category_id
    data.name = name
    data.description = description
    data.price = price
    data.stock = stock
    data.image = image
    data.status = status

    db.commit()
    db.refresh(data)

    return {
        "message": "Product updated successfully",
        "data": data
    }


def destroy(db: Session, product_id: int):
    data = db.query(Products).filter(Products.id == product_id).first()

    if not data:
        return {"message": "Product not found"}

    db.delete(data)
    db.commit()
    return {
        "message": "Product deleted successfully",
        "data": product_id
    }

