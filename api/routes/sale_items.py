from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import success
from schemas.sale_items import SaleItems
from services.sale_items_service import (
    index as sale_items_index,
    store as sale_items_store,
)

router = APIRouter()

@router.get("/")
async def index(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = sale_items_index(db)
    return success("Fetched data successfully", data)

@router.post("/")
async def store(payload: SaleItems, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = sale_items_store(db, payload.sale_id, payload.product_id, payload.quantity, payload.unit_price, payload.subtotal)
    return success("Successfully stored data", data)

