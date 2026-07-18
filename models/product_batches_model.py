from sqlalchemy import Column, Integer, String, Float, ForeignKey, Date
from core.database import Base
from models.timestamp import TimestampMixin

class ProductBatches(Base, TimestampMixin):
    __tablename__ = "product_batches"

    id = Column(Integer, primary_key=True)
    raw_material_id = Column(Integer, ForeignKey("raw_materials.id"))
    roast_date = Column(Date, nullable=False)
    milled_weight = Column(Float, nullable=False)
    package_pieces = Column(Integer, nullable=False)
    production_cost = Column(Float, nullable=False)