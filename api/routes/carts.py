
# Explanation:
# This file is part of the tableyapi backend and contains API route handlers for carts operations.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_customer
from api.responses import bad_request, not_found, success
from schemas.carts import Carts
from services.carts_service import destroy_for_user, get_or_create_by_user, index_by_user, show_for_user, update


router = APIRouter()

@router.get("/")
async def list_carts(db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = index_by_user(db, current_user.id)

    return success("Carts fetched successfully", data)

@router.post("/")
async def create_cart(cart: Carts, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = get_or_create_by_user(db, current_user.id)

    if not data:
        bad_request("Failed to create cart")

    return success("Cart created successfully", data)

@router.get("/{cart_id}")
async def get_cart(cart_id: int, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = show_for_user(db, cart_id, current_user.id)

    if not data:
        not_found("Cart not found")

    return success("Cart fetched successfully", data)

@router.put("/{cart_id}")
async def update_cart(cart_id: int, cart: Carts, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = show_for_user(db, cart_id, current_user.id)
    if not data:
        not_found("Cart not found")
    data = update(db, cart_id, current_user.id)

    if not data:
        not_found("Cart not found")

    return success("Cart updated successfully", data)

@router.delete("/{cart_id}")
async def delete_cart(cart_id: int, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = destroy_for_user(db, cart_id, current_user.id)

    if not data:
        not_found("Cart not found")

    return success("Cart deleted successfully", data)