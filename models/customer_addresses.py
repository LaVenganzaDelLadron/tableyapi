
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for customer addresses entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import Column, Integer, String

from core.database import Base
from models.mixins import TimestampMixin


class CustomerAddresses(TimestampMixin, Base):
    __tablename__ = "customer_addresses"

    id = Column(Integer, primary_key=True)
    user_id = Column(Integer)
    recipient_name = Column(String)
    phone = Column(String)
    address_line = Column(String)
    city = Column(String)
    province = Column(String)
    postal_code = Column(String)
    is_default = Column(Integer, default=0)