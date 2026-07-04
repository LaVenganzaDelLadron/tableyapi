
# Explanation:
# This file is part of the tableyapi backend and contains Business logic and service layer code for audit service.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy.orm import Session

from models.audit_logs import AuditLogs


def record(db: Session, user_id: int | None, action: str, entity_type: str, entity_id: int | None = None, details: str | None = None):
    entry = AuditLogs(
        user_id=user_id,
        action=action,
        entity_type=entity_type,
        entity_id=entity_id,
        details=details,
    )
    db.add(entry)
    db.commit()
    db.refresh(entry)
    return entry


def index(db: Session, page: int = 1, limit: int = 20, action: str | None = None):
    from api.pagination import paginate

    query = db.query(AuditLogs).order_by(AuditLogs.created_at.desc(), AuditLogs.id.desc())
    if action:
        query = query.filter(AuditLogs.action == action)
    return paginate(query, page, limit)