from sqlalchemy.orm import Session
from models.earnings import Earnings


def index(db: Session):
    return db.query(Earnings).order_by(Earnings.entry_date.desc(), Earnings.id.desc()).all()


def store(db: Session, description: str, amount: float, source: str, entry_date):
    data = Earnings(description=description, amount=amount, source=source, entry_date=entry_date)
    db.add(data)
    db.commit()
    db.refresh(data)
    return data
