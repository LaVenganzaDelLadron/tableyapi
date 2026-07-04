
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for cart items entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float
from cores.database import Base
from models.mixins import TimestampMixin


class CartItems(TimestampMixin, Base):
    __tablename__ = 'cart_items'
    id = Column(Integer, primary_key=True)
    cart_id = Column(Integer, ForeignKey('carts.id'))
    product_id = Column(Integer, ForeignKey('products.id'))
    quantity = Column(Integer)