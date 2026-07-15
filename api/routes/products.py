from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import success
from schemas.products import Products
from services.products_service import (
    index as product_index,
    store as product_store,
)


router = APIRouter()


@router.get("/")
async def index(db: Session = Depends(get_db), current_user = Depends(get_current_user)):
    data = product_index(db)
    return success("Fetch data successfully", data)

@router.post("/")
async def store(payload: Products, db: Session = Depends(get_db), current_user = Depends(get_current_user)):
    data = product_store(db, payload.product_name, payload.selling_price, payload.current_stock)
    return success("Fetch data successfully", data)