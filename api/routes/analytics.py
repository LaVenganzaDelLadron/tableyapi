from fastapi import APIRouter, Depends, Query
from sqlalchemy.orm import Session
from datetime import date

from api.dependencies import get_db, require_admin
from api.responses import success
from services.analytics_service import net_summary

router = APIRouter()


@router.get("/net")
async def net(
    start_date: date | None = Query(None),
    end_date: date | None = Query(None),
    db: Session = Depends(get_db),
    current_user=Depends(require_admin),
):
    data = net_summary(db, start_date, end_date)
    return success("Net earnings computed", data)
