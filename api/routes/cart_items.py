
# Explanation:
# This file is part of the tableyapi backend and contains API route handlers for cart items operations.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_customer
from api.responses import bad_request, not_found, success
from schemas.cart_items import CartItems
from services.cart_items_service import destroy_for_user, index_by_user, show_for_user, store, update_for_user
from services.carts_service import get_or_create_by_user

router = APIRouter()

@router.get("/")
async def list_cart_items(db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = index_by_user(db, current_user.id)

    return success("Cart items fetched successfully", data)

@router.post("/")
async def create_cart_item(cart_item: CartItems, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    cart = get_or_create_by_user(db, current_user.id)
    data = store(db, cart.id, cart_item.product_id, cart_item.quantity)

    if not data:
        bad_request("Failed to create cart item")

    return success("Cart item created successfully", data)

@router.get("/{cart_item_id}")
async def get_cart_item(cart_item_id: int, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = show_for_user(db, cart_item_id, current_user.id)

    if not data:
        not_found("Cart item not found")

    return success("Cart item fetched successfully", data)

@router.put("/{cart_item_id}")
async def update_cart_item(cart_item_id: int, cart_item: CartItems, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = update_for_user(db, cart_item_id, current_user.id, cart_item.product_id, cart_item.quantity)

    if not data:
        not_found("Cart item not found")

    return success("Cart item updated successfully", data)

@router.delete("/{cart_item_id}")
async def delete_cart_item(cart_item_id: int, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = destroy_for_user(db, cart_item_id, current_user.id)

    if not data:
        not_found("Cart item not found")

    return success("Cart item deleted successfully", data)