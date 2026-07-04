
# Explanation:
# This file is part of the tableyapi backend and contains API route handlers for shipping operations.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_admin
from api.responses import bad_request, not_found, success
from schemas.shipping import Shipping
from services.orders_service import show as show_order
from services.shipping_service import create_or_update_shipping, destroy, index, show


router = APIRouter()

@router.get("/")
async def list_shipping(db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = index(db)

    return success("Shipping fetched successfully",data)


@router.post("/")
async def create_shipping(shipping: Shipping, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    order = show_order(db, shipping.order_id)
    if not order:
        not_found("Order not found")

    data = create_or_update_shipping(order, shipping, db)

    if not data:
        bad_request("Invalid shipping update for current order state")

    return success("Shipping created successfully", data)

@router.get("/{shipping_id}")
async def get_shipping(shipping_id: int, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = show(db, shipping_id)

    if not data:
        not_found("Shipping not found")

    return success("Shipping fetched successfully", data)

@router.put("/{shipping_id}")
async def update_shipping(shipping_id: int, shipping: Shipping, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    existing = show(db, shipping_id)
    if not existing:
        not_found("Shipping not found")
    if existing.order_id != shipping.order_id:
        bad_request("Shipping order cannot be changed")

    order = show_order(db, shipping.order_id)
    if not order:
        not_found("Order not found")

    data = create_or_update_shipping(order, shipping, db)

    if not data:
        bad_request("Invalid shipping update for current order state")

    return success("Shipping updated successfully", data)

@router.delete("/{shipping_id}")
async def delete_shipping(shipping_id: int, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = destroy(db, shipping_id)

    if not data:
        not_found("Shipping not found")

    return success("Shipping deleted successfully", data)