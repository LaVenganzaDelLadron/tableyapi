from sqlalchemy.orm import Session
from models.products import Products


def index(db: Session, search: str | None = None):
    query = db.query(Products)
    if search:
        pattern = f"%{search}%"
        query = query.filter(
            (Products.name.ilike(pattern)) | (Products.description.ilike(pattern))
        )

    return query.all()


def store(db: Session, category_id: int, name: str, description: str, price: float, stock: int, image: str | None = None, status: str | None = None):
    data = Products(
        category_id=category_id,
        name=name,
        description=description,
        price=price,
        stock=stock,
        image=image,
        status=status or "active",
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return data


def show(db: Session, product_id: int):
    data = db.query(Products).filter(Products.id == product_id).first()

    return data


def update(db: Session, product_id: int, category_id: int, name: str, description: str, price: float, stock: int, image: str | None = None, status: str | None = None):
    data = db.query(Products).filter(Products.id == product_id).first()

    if not data:
        return None

    data.category_id = category_id
    data.name = name
    data.description = description
    data.price = price
    data.stock = stock
    data.image = image
    data.status = status or data.status

    db.commit()
    db.refresh(data)

    return data


def destroy(db: Session, product_id: int):
    data = db.query(Products).filter(Products.id == product_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return product_id
