from sqlalchemy.orm import Session
from api.pagination import paginate
from models.users import User, UserRole


def index(db: Session):
    return db.query(User).all()


def index_customers(db: Session, page: int | None = None, limit: int | None = None, search: str | None = None):
    query = db.query(User).filter(User.role == UserRole.CUSTOMER)
    if search:
        pattern = f"%{search}%"
        query = query.filter(
            (User.email.ilike(pattern)) | (User.fullname.ilike(pattern)) | (User.username.ilike(pattern))
        )
    query = query.order_by(User.id.desc())
    if page is not None or limit is not None:
        return paginate(query, page or 1, limit or 20)
    return query.all()


def update_profile(db: Session, user: User, email: str | None = None, fullname: str | None = None, username: str | None = None):
    if email is not None:
        user.email = email
    if fullname is not None:
        user.fullname = fullname
    if username is not None:
        user.username = username

    db.commit()
    db.refresh(user)
    return user


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

    return data


def show(db: Session, user_id: int):
    data = db.query(User).filter(User.id == user_id).first()

    return data


def update(db: Session, user_id: int, email: str, fullname: str, username: str, password: str, role: str):
    data = db.query(User).filter(User.id == user_id).first()

    if not data:
        return None

    data.email = email
    data.fullname = fullname
    data.username = username
    data.password = password
    data.role = role

    db.commit()
    db.refresh(data)

    return data


def destroy(db: Session, user_id: int):
    data = db.query(User).filter(User.id == user_id).first()

    if not data:
        return None

    db.delete(data)
    db.commit()
    return user_id
