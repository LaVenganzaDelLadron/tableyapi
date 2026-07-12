from datetime import datetime
from sqlalchemy.orm import Session
from models.sales import Sales

def index(db: Session):
    return db.query(Sales).order_by(Sales.id.desc()).all()

def store(db: Session, sale_date: datetime, total_amount: float):
    data = Sales(sales_date=sale_date, total_amount=total_amount)
    db.add(data)
    db.commit()
    db.refresh(data)
    return data