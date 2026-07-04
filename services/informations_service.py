
# Explanation:
# This file is part of the tableyapi backend and contains Business logic and service layer code for informations service.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy.orm import Session
from models.informations import Informations
from models.users import User


def index(db: Session):
    return db.query(Informations).all()


def index_by_user(db: Session, user_id: int):
    return db.query(Informations).filter(Informations.user_id == user_id).all()


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

    return data

def show(db: Session, information_id: int):
    data = db.query(Informations).filter(Informations.id == information_id).first()

    return data


def show_for_user(db: Session, information_id: int, user_id: int):
    data = db.query(Informations).filter(Informations.id == information_id, Informations.user_id == user_id).first()

    return data

def update(db: Session, information_id: int, phone: str, address: str, city: str, province: str, street: str, postal_code: str):
    data = db.query(Informations).filter(Informations.id == information_id).first()

    if not data:
        return None

    data.phone = phone
    data.address = address
    data.city = city
    data.province = province
    data.street = street
    data.postal_code = postal_code

    db.commit()
    db.refresh(data)

    return data


def update_for_user(db: Session, information_id: int, user_id: int, phone: str, address: str, city: str, province: str, street: str, postal_code: str):
    data = db.query(Informations).filter(Informations.id == information_id, Informations.user_id == user_id).first()

    if not data:
        return None

    data.phone = phone
    data.address = address
    data.city = city
    data.province = province
    data.street = street
    data.postal_code = postal_code

    db.commit()
    db.refresh(data)
    return data

def destroy(db: Session, information_id: int):
    data = db.query(Informations).filter(Informations.id == information_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return information_id


def destroy_for_user(db: Session, information_id: int, user_id: int):
    data = db.query(Informations).filter(Informations.id == information_id, Informations.user_id == user_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return information_id

