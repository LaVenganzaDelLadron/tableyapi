from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from api.dependencies import get_current_user, get_db
from api.responses import success, bad_request, error_payload
from services.product_batches_service import (
    index as product_batches_index,
)

router = APIRouter()

@router.get("/")
async def index(db: Session = Depends(get_current_user)):
    data = product_batches_index(db)
    if data is None:
        return success("Invalid data", data)
    return success("Successfully fetched data", data)