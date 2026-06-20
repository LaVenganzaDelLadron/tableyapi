from sqlalchemy.orm import Session
from models.informations import Informations
from models.users import User


def index(db: Session):
    data = db.query(Informations).all()
    if not data:
        return {"message": "Information not found"}

    return {
        "message": "Information found",
        "data": data
    }


def index_by_user(db: Session, user_id: int):
    data = db.query(Informations).filter(Informations.user_id == user_id).all()
    return {
        "message": "Information found",
        "data": data
    }


def store(db: Session, user_id: int, phone: str, address: str, city: str, province: str, street: str, postal_code: str):
    if not db.query(User).filter(User.id == user_id).first():
        return  None

    data = Informations(
        user_id = user_id,
        phone = phone,
        address = address,
        city = city,
        province = province,
        street = street,
        postal_code = postal_code,
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return {
        "message": "Information created successfully",
        "data": data
    }

def show(db: Session, information_id: int):
    data = db.query(Informations).filter(Informations.id == information_id).first()

    if not data:
        return {"message": "Information not found"}
    return {
        "message": "Information found",
        "data": data
    }


def show_for_user(db: Session, information_id: int, user_id: int):
    data = db.query(Informations).filter(Informations.id == information_id, Informations.user_id == user_id).first()

    if not data:
        return None
    return {
        "message": "Information found",
        "data": data
    }

def update(db: Session, information_id: int, phone: str, address: str, city: str, province: str, street: str, postal_code: str):
    data = db.query(Informations).filter(Informations.id == information_id).first()

    if not data:
        return {"message": "Information not found"}

    data.phone = phone
    data.address = address
    data.city = city
    data.province = province
    data.street = street
    data.postal_code = postal_code

    db.commit()
    db.refresh(data)

    return {
        "message": "Information updated successfully",
        "data": data
    }

def destroy(db: Session, information_id: int):
    data = db.query(Informations).filter(Informations.id == information_id).first()

    if not data:
        return {"message": "Failed to delete"}

    db.delete(data)
    db.commit()
    return {
        "message": "Deleted successfully",
        "data": information_id
    }



