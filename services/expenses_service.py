from sqlalchemy.orm import Session
from models.expenses import Expenses


def index(db: Session):
    return db.query(Expenses).order_by(Expenses.entry_date.desc(), Expenses.id.desc()).all()


def store(db: Session, description: str, amount: float, category: str, entry_date):
    data = Expenses(description=description, amount=amount, category=category, entry_date=entry_date)
    db.add(data)
    db.commit()
    db.refresh(data)
    return data
