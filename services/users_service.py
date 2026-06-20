from sqlalchemy.orm import Session
from models.users import User


def index(db: Session):
    data = db.query(User).all()
    if not data:
        return {"message": "Users not found"}
    return {
        "message": "Users found",
        "data": data
    }


def store(db: Session, email: str, fullname: str, username: str, password: str, role: str):
    data = User(
        email=email,
        fullname=fullname,
        username=username,
        password=password,
        role=role,
    )

    db.add(data)
    db.commit()
    db.refresh(data)

    return {
        "message": "User created successfully",
        "data": data
    }


def show(db: Session, user_id: int):
    data = db.query(User).filter(User.id == user_id).first()

    if not data:
        return {"message": "User not found"}
    return {
        "message": "User found",
        "data": data
    }


def update(db: Session, user_id: int, email: str, fullname: str, username: str, password: str, role: str):
    data = db.query(User).filter(User.id == user_id).first()

    if not data:
        return {"message": "User not found"}

    data.email = email
    data.fullname = fullname
    data.username = username
    data.password = password
    data.role = role

    db.commit()
    db.refresh(data)

    return {
        "message": "User updated successfully",
        "data": data
    }


def destroy(db: Session, user_id: int):
    data = db.query(User).filter(User.id == user_id).first()

    if not data:
        return {"message": "User not found"}

    db.delete(data)
    db.commit()
    return {
        "message": "User deleted successfully",
        "data": user_id
    }
