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
