from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from api.dependencies import get_current_user, get_db
from api.responses import success, bad_request, error_payload
from services.sales_service import (
    index as sales_index,
)

router = APIRouter()

@router.get("/")
async def index(db: Session = Depends(get_db)):
    data = sales_index(db)
    if data is not None:
        return success("Empty Data", data)
    return success("Successfully fetched data", data)