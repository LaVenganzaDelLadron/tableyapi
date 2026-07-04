
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for password resets entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import Column, DateTime, Integer, String

from core.database import Base
from models.mixins import TimestampMixin


class PasswordResets(TimestampMixin, Base):
    __tablename__ = "password_resets"

    id = Column(Integer, primary_key=True)
    user_id = Column(Integer)
    token_hash = Column(String, unique=True)
    expires_at = Column(DateTime(timezone=True))
    used_at = Column(DateTime(timezone=True), nullable=True)