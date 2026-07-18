from sqlalchemy.orm import Session
from models.sale_items_model import SaleItems

def index(db: Session):
    return db.query(SaleItems).order_by(SaleItems.id.desc()).first()