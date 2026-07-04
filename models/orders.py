
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for orders entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

import enum
from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float
from cores.database import Base
from models.mixins import TimestampMixin

class OrderStatus(enum.Enum):
    PENDING = "PENDING"
    PAID = "PAID"
    SHIPPED = "SHIPPED"
    COMPLETED = "COMPLETED"
    CANCELLED = "CANCELLED"


class Orders(TimestampMixin, Base):
    __tablename__ = "orders"
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    information_id = Column(Integer, ForeignKey('informations.id'))
    total_amount = Column(Float)
    status = Column(String, default=OrderStatus.PENDING.value)
    payment_method = Column(String)