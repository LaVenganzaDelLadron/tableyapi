
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for carts entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float

from core.database import Base
from models.mixins import TimestampMixin

class Carts(TimestampMixin, Base):
    __tablename__ = 'carts'
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey('users.id'))