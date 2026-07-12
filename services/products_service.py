from sqlalchemy.orm import Session
from models.products import Products

def index(db: Session):
    return db.query(Products).order_by(Products.id.desc()).all()

def store(db: Session, product_name: str, selling_price: float, current_stock: int):
    data = Products(product_name=product_name, selling_price=selling_price, current_stock=current_stock)
    db.add(data)
    db.commit()
    db.refresh(data)
    return data