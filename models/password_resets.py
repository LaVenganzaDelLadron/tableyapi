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
