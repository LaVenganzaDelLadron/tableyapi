
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for categories entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import Column, Integer, String, Enum
from cores.database import Base
from models.mixins import TimestampMixin


class Categories(TimestampMixin, Base):
    __tablename__ = 'categories'
    id = Column(Integer, primary_key=True)
    name = Column(String)