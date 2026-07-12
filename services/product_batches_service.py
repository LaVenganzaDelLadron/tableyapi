from datetime import datetime
from sqlalchemy.orm import Session
from models.product_batches import ProductBatches

def index(db: Session):
    return db.query(ProductBatches).order_by(ProductBatches.id.desc()).all()

def store(db: Session, raw_id: int, roast_date: datetime, milled_weight: float, package_pieces: int, production_cost: float):
    data = ProductBatches(raw_id=raw_id, roast_date=roast_date, milled_weight=milled_weight, package_pieces=package_pieces, production_cost=production_cost)
    db.add(data)
    db.commit()
    db.refresh(data)
    return data
