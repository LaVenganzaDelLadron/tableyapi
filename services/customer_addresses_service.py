from sqlalchemy.orm import Session

from models.customer_addresses import CustomerAddresses


def index_by_user(db: Session, user_id: int):
    return db.query(CustomerAddresses).filter(CustomerAddresses.user_id == user_id).all()


def store(db: Session, user_id: int, payload):
    if payload.is_default:
        _clear_default(db, user_id)

    address = CustomerAddresses(user_id=user_id, **payload.model_dump())
    db.add(address)
    db.commit()
    db.refresh(address)
    return address


def update_for_user(db: Session, address_id: int, user_id: int, payload):
    address = db.query(CustomerAddresses).filter(
        CustomerAddresses.id == address_id,
        CustomerAddresses.user_id == user_id,
    ).first()
    if not address:
        return None

    values = payload.model_dump(exclude_unset=True)
    if values.get("is_default"):
        _clear_default(db, user_id)
    for key, value in values.items():
        setattr(address, key, value)

    db.commit()
    db.refresh(address)
    return address


def destroy_for_user(db: Session, address_id: int, user_id: int):
    address = db.query(CustomerAddresses).filter(
        CustomerAddresses.id == address_id,
        CustomerAddresses.user_id == user_id,
    ).first()
    if not address:
        return None

    db.delete(address)
    db.commit()
    return address_id


def _clear_default(db: Session, user_id: int):
    db.query(CustomerAddresses).filter(CustomerAddresses.user_id == user_id).update({"is_default": 0})
