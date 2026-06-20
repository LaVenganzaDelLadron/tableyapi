from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import bad_request, not_found, success
from schemas.shipping import Shipping
from services.shipping_service import index, store, show, update, destroy


router = APIRouter()

@router.get("/")
async def list_shipping(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = index(db, current_user.id)

    return success("Shipping fetched successfully",data)


@router.post("/")
async def create_shipping(shipping: Shipping, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = store(db, shipping, current_user.id)

    if not data:
        bad_request("Failed to create shipping")

    return success("Shipping created successfully", data)

@router.get("/{shipping_id}")
async def get_shipping(shipping_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = show(db, shipping_id, current_user.id)

    if not data:
        not_found("Shipping not found")

    return success("Shipping fetched successfully", data)

@router.put("/{shipping_id}")
async def update_shipping(shipping_id: int, shipping: Shipping, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = update(db, shipping_id, shipping, current_user.id)

    if not data:
        not_found("Shipping not found")

    return success("Shipping updated successfully", data)

@router.delete("/{shipping_id}")
async def delete_shipping(shipping_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = destroy(db, shipping_id, current_user.id)

    if not data:
        not_found("Shipping not found")

    return success("Shipping deleted successfully", data)
