from sqlalchemy.orm import Session
from models.categories import Categories


def index(db: Session):
    data = db.query(Categories).all()
    if not data:
        return {"message": "Category not found"}
    return {
        "message": "Deleted successfully",
        "data": data
    }

def store(db: Session, name: str):
    if not db.query(Categories).filter(Categories.name == name).first():
        return None

    data = Categories(name=name)

    db.add(data)
    db.commit()
    db.refresh(data)

    return {
        "message": "Category created successfully",
        "data": data
    }

def show(db: Session, category_id: int):
    data = db.query(Categories).filter(Categories.id == category_id).first()

    if not data:
        return {"message": "Category found successfully", "data": data}
    return {
        "message": "Category found",
        "data": data
    }


def update(db: Session, category_id: int, name: str):
    data = db.query(Categories).filter(Categories.id == category_id).first()

    if not data:
        return None
    data.name = name

    db.commit()
    db.refresh(data)

    return {
        "message": "Category updated successfully",
        "data": data
    }

def destroy(db: Session, category_id: int):
    data = db.query(Categories).filter(Categories.id == category_id).first()

    if not data:
        return {"message": "Category not found"}

    db.delete(data)
    db.commit()

    return {
        "message": "Category deleted successfully",
        "data": category_id
    }
