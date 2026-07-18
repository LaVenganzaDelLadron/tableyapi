from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from api.dependencies import get_current_user, get_db
from api.responses import success
from services.products_service import (
    index as products_index,
)
router = APIRouter()

@router.get("/")
async def index(db: Session = Depends(get_current_user)):
    data = products_index(db)
    if data is not None:
        return success("Empty data", data)
    return success("Successfully fetched data", data)