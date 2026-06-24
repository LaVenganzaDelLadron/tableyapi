from sqlalchemy.orm import Session
from api.pagination import paginate
from models.products import Products


def index(db: Session, search: str | None = None, page: int | None = None, limit: int | None = None, public_only: bool = False, status: str | None = None):
    query = db.query(Products)
    if public_only:
        query = query.filter(Products.status == "active", Products.stock > 0)
    elif status:
        query = query.filter(Products.status == status)
    if search:
        pattern = f"%{search}%"
        query = query.filter(
            (Products.name.ilike(pattern)) | (Products.description.ilike(pattern))
        )

    query = query.order_by(Products.id.desc())
    if page is not None or limit is not None:
        return paginate(query, page or 1, limit or 20)
    return query.all()


def low_stock(db: Session, threshold: int = 5, page: int = 1, limit: int = 20):
    query = db.query(Products).filter(Products.stock <= threshold).order_by(Products.stock.asc(), Products.id.asc())
    return paginate(query, page, limit)


def adjust_stock(db: Session, product_id: int, quantity_delta: int, reason: str | None = None):
    data = db.query(Products).filter(Products.id == product_id).first()
    if not data:
        return None
    new_stock = (data.stock or 0) + quantity_delta
    if new_stock < 0:
        return False
    data.stock = new_stock
    db.commit()
    db.refresh(data)
    return data


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
