from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import success
from schemas.sales import Sales
from services.sales_service import index, store

router = APIRouter()

@router.get("/")
async def index(db: Session = Depends(get_db), current_user = Depends(get_current_user)):
    data = index(db)
    return success("Fetched data successfully", data)

@router.post("/")
async def store(payload: Sales, db: Session = Depends(get_db), current_user = Depends(get_current_user)):
    data = store(db, payload.sale_date, payload.total_amount)
    return success("Stored data successfully", data)