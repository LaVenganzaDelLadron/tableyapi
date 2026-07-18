from sqlalchemy import Column, Integer, String, Float, ForeignKey
from core.database import Base
from models.timestamp import TimestampMixin


class SaleItems(Base, TimestampMixin):
    __tablename__ = "sale_items"

    id = Column(Integer, primary_key=True)
    sale_id = Column(Integer, ForeignKey("sales.id"))
    quantity = Column(Integer, nullable=False)
    unit_price = Column(Float, nullable=False)
    subtotal = Column(Float, nullable=False)