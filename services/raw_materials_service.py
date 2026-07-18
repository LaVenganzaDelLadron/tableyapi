from sqlalchemy.orm import Session
from models.raw_materials_model import RawMaterials

def index(db: Session):
    return db.query(RawMaterials).order_by(RawMaterials.id.desc()).first()

