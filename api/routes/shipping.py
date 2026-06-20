from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_admin
from api.responses import bad_request, not_found, success
from schemas.shipping import Shipping
from services.shipping_service import index, store, show, update, destroy


router = APIRouter()

@router.get("/")
async def list_shipping(db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = index(db)

    return success("Shipping fetched successfully",data)


@router.post("/")
async def create_shipping(shipping: Shipping, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = store(db, shipping.order_id, shipping.courier_id, shipping.tracking_number, shipping.shipping_fee, shipping.shipped_at, shipping.delivered_at)

    if not data:
        bad_request("Failed to create shipping")

    return success("Shipping created successfully", data)

@router.get("/{shipping_id}")
async def get_shipping(shipping_id: int, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = show(db, shipping_id)

    if not data:
        not_found("Shipping not found")

    return success("Shipping fetched successfully", data)

@router.put("/{shipping_id}")
async def update_shipping(shipping_id: int, shipping: Shipping, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = update(db, shipping_id, shipping.order_id, shipping.courier_id, shipping.tracking_number, shipping.shipping_fee, shipping.shipped_at, shipping.delivered_at)

    if not data:
        not_found("Shipping not found")

    return success("Shipping updated successfully", data)

@router.delete("/{shipping_id}")
async def delete_shipping(shipping_id: int, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = destroy(db, shipping_id)

    if not data:
        not_found("Shipping not found")

    return success("Shipping deleted successfully", data)
