from sqlalchemy.orm import Session
from models.products_model import Products

def index(db: Session):
    return db.query(Products).order_by(Products.id.desc()).first()