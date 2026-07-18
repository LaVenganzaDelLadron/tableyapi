from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from api.dependencies import get_current_user, get_db
from api.responses import success
from services.sale_items_service import (
    index as sale_items_index,
)

router = APIRouter()

@router.get("/")
async def get(db: Session = Depends(get_current_user)):
    data = sale_items_index(db)
    if data is None:
        return success("Empty Data", data)
    return success("Successfully fetched data", data)