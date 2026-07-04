
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for audit logs entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import Column, Integer, String, Text

from core.database import Base
from models.mixins import TimestampMixin


class AuditLogs(TimestampMixin, Base):
    __tablename__ = "audit_logs"

    id = Column(Integer, primary_key=True)
    user_id = Column(Integer)
    action = Column(String)
    entity_type = Column(String)
    entity_id = Column(Integer)
    details = Column(Text)