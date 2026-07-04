
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for users entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import Column, Integer, String, Enum
import enum
from cores.database import Base
from models.mixins import TimestampMixin

class UserRole(enum.Enum):
    ADMIN = "admin"
    CUSTOMER = "customer"

class User(TimestampMixin, Base):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True)
    email = Column(String, unique=True)
    fullname = Column(String)
    username = Column(String, unique=True)
    password = Column(String)
    role = Column(Enum(UserRole), default=UserRole.CUSTOMER)