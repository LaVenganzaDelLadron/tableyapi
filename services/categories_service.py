from sqlalchemy.orm import Session
from models.categories import Categories


def index(db: Session):
    return db.query(Categories).all()

def store(db: Session, name: str):
    if db.query(Categories).filter(Categories.name == name).first():
        return None

    data = Categories(name=name)

    db.add(data)
    db.commit()
    db.refresh(data)

    return data

def show(db: Session, category_id: int):
    data = db.query(Categories).filter(Categories.id == category_id).first()

    return data


def update(db: Session, category_id: int, name: str):
    data = db.query(Categories).filter(Categories.id == category_id).first()

    if not data:
        return None
    data.name = name

    db.commit()
    db.refresh(data)

    return data

def destroy(db: Session, category_id: int):
    data = db.query(Categories).filter(Categories.id == category_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()

    return category_id
