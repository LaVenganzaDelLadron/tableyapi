from sqlalchemy import Column, Integer, String, Float, Date, ForeignKey
from cores.database import Base
from models.mixins import TimestampMixin

class SaleItems(TimestampMixin, Base):
    __tablename__ = "sale_items"

    id = Column(Integer, primary_key=True)
    sale_id = Column(Integer, ForeignKey('sales.id'))
    product_id = Column(Integer, ForeignKey('products.id'))
    quantity = Column(Integer)
    unit_price = Column(Float)
    subtotal = Column(Float)