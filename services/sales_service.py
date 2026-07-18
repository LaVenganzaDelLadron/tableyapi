from sqlalchemy.orm import Session
from models.sales_model import Sales


def index(db: Session):
    return db.query(Sales).order_by(Sales.id.desc()).first()