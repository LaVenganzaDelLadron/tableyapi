from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float
from core.database import Base
from models.mixins import TimestampMixin

class OrderItems(TimestampMixin, Base):
    __tablename__ = "order_items"
    id = Column(Integer, primary_key=True)
    order_id = Column(Integer, ForeignKey('orders.id'))
    product_id = Column(Integer, ForeignKey('products.id'))
    quantity = Column(Integer, default=1)
    price = Column(Float)
    product_name = Column(String)
    subtotal = Column(Float)
