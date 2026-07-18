from datetime import datetime, timezone
from sqlalchemy import Column, DateTime

def utc_now():
    return datetime.now(timezone.utc)

class TimestampMixin:
    created_at = Column(DateTime(timezone=True), default=utc_now)
    updated_at = Column(DateTime(timezone=True), default=utc_now, onupdate=utc_now)