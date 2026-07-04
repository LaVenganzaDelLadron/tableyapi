
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for shipping entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float, DateTime
from core.database import Base
from models.mixins import TimestampMixin

class Shipping(TimestampMixin, Base):
    __tablename__ = "shipping"
    id = Column(Integer, primary_key=True)
    order_id = Column(Integer, ForeignKey("orders.id"))
    courier_id = Column(String)
    tracking_number = Column(String)
    shipping_fee = Column(Float)
    shipped_at = Column(DateTime)
    delivered_at = Column(DateTime)