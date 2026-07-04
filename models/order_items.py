
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for order items entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float
from cores.database import Base
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