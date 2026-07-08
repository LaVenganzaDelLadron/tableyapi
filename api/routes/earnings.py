from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import success
from schemas.earnings import Earnings
from services.earnings_service import index, store

router = APIRouter()


@router.get("/")
async def list_earnings(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = index(db)
    return success("Earnings fetched successfully", data)


@router.post("/")
async def create_earning(payload: Earnings, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = store(db, payload.description, payload.amount, payload.source, payload.entry_date)
    return success("Earning created successfully", data)
