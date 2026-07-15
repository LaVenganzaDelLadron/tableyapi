from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import  success
from schemas.raw_materials import RawMaterials
from services.raw_materials_service import (
    index as raw_materials_index,
    store as raw_materials_store
)

router = APIRouter()

@router.get("/")
async def index(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = raw_materials_index(db)
    return success("Successfully fetched data", data)

@router.post("/")
async def store(payload: RawMaterials, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = raw_materials_store(db, payload.material_name, payload.quantity, payload.unit)
    return success("Successfully stored data", data)