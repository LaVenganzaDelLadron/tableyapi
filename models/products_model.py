from sqlalchemy import Column, Integer, String, Float, ForeignKey
from core.database import Base
from models.timestamp import TimestampMixin

class Products(Base, TimestampMixin):
    __tablename__ = "products"

    id = Column(Integer, primary_key=True)
    production_batches_id = Column(Integer, ForeignKey("production_batches.id"))
    name = Column(String, nullable=False)
    selling_price = Column(Float, nullable=False)
    current_stock = Column(Integer, nullable=False)