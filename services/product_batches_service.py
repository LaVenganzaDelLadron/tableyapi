from sqlalchemy.orm import Session
from models.product_batches_model import ProductBatches

def index(db: Session):
    return db.query(ProductBatches).order_by(ProductBatches.id.desc()).first()
