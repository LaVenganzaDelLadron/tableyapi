from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from api.dependencies import get_current_user, get_db
from api.responses import success
from services.raw_materials_service import(
    index as raw_material_index,
)

router = APIRouter()

@router.get("/")
async def read_root(db: Session = Depends(get_current_user)):
    data = raw_material_index(db)
    if data is None:
        return success("Empty Data", data)
    return success("Successfully fetched data", data)