from sqlalchemy.orm import Session
from models.sale_items import SaleItems


def index(db: Session):
    return db.query(SaleItems).order_by(SaleItems.id.desc()).all()

def store(db: Session, sale_id: int, product_id: int, quantity: int, unit_price: float, subtotal: float):
    data = SaleItems(sale_id=sale_id, product_id=product_id, quantity=quantity, unit_price=unit_price, subtotal=subtotal)
    db.add(data)
    db.commit()
    db.refresh(data)
    return data

