from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float, DateTime
from core.database import Base

class OrderItems(Base):
    __tablename__ = "order_items"
    id = Column(Integer, primary_key=True)
    order_id = Column(Integer, ForeignKey('orders.id'))
    product_id = Column(Integer, ForeignKey('products.id'))
    quantity = Column(Integer, default=1)
    price = Column(Float)
    created_at = Column(DateTime)
    updated_at = Column(DateTime)
