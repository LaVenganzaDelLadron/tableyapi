from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import success
from schemas.production_batches import ProductionBatches
from services.product_batches_service import index, store


router = APIRouter()

@router.get("/")
async def index(db: Session = Depends(get_db), current_user = Depends(get_current_user)):
    data = index(db)
    return success("Fetched data successfully", data)

@router.post("/")
async def store(payload: ProductionBatches, db: Session = Depends(get_db), current_user = Depends(get_current_user)):
    data = store(db, payload.raw_id, payload.roast_date, payload.milled_weight, payload.packaged_pieces, payload.production_cost)
    return success("Stored data successfully", data)

