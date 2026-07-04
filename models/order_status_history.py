
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for order status history entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import Column, Integer, String, ForeignKey

from cores.database import Base
from models.mixins import TimestampMixin


class OrderStatusHistory(TimestampMixin, Base):
    __tablename__ = "order_status_history"

    id = Column(Integer, primary_key=True)
    order_id = Column(Integer, ForeignKey("orders.id"))
    from_status = Column(String)
    to_status = Column(String)
    changed_by_user_id = Column(Integer, nullable=True)