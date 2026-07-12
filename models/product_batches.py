from sqlalchemy import Column, Integer, String, Float, Date, ForeignKey
from cores.database import Base
from models.mixins import TimestampMixin

class ProductBatches(TimestampMixin, Base):
    __tablename__ = "product_batches"

    id = Column(Integer, primary_key=True)
    raw_id = Column(Integer, ForeignKey('raw_materials.id'))
    roast_date = Column(Date)
    milled_weight = Column(Float)
    package_pieces = Column(Integer)
    production_cost: float = Column(Float)
    