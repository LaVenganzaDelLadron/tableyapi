
# Explanation:
# This file is part of the tableyapi backend and contains Database model definitions for mixins entities.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from datetime import datetime, timezone

from sqlalchemy import Column, DateTime


def utc_now():
    return datetime.now(timezone.utc)


class TimestampMixin:
    created_at = Column(DateTime(timezone=True), default=utc_now)
    updated_at = Column(DateTime(timezone=True), default=utc_now, onupdate=utc_now)