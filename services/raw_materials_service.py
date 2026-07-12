from sqlalchemy.orm import Session
from models.raw_materials import RawMaterials

def index(db: Session):
    return db.query(RawMaterials).order_by(RawMaterials.id.desc()).all()


def store(db: Session, material_name: str, quantity: float, unit: str):
    data = RawMaterials(material_name=material_name, quantity=quantity, unit=unit)
    db.add(data)
    db.commit()
    db.refresh(data)
    return data