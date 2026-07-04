
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for informations entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import Column, Integer, String, Enum, ForeignKey
from cores.database import Base
from models.mixins import TimestampMixin

class Informations(TimestampMixin, Base):
    __tablename__ = "informations"
    id = Column(Integer, primary_key=True, autoincrement=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    phone = Column(String)
    address = Column(String)
    city = Column(String)
    province = Column(String)
    street = Column(String)
    postal_code = Column(String)