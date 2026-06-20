from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import bad_request, not_found, success
from schemas.cart_items import CartItems
from services.cart_items_service import index, store, show, update, destroy

router = APIRouter()

@router.get("/")
async def list_cart_items(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = index(db, current_user.id)

    return success("Cart items fetched successfully", data)

@router.post("/")
async def create_cart_item(cart_item: CartItems, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = store(db, cart_item, current_user.id)

    if not data:
        bad_request("Failed to create cart item")

    return success("Cart item created successfully", data)

@router.get("/{cart_item_id}")
async def get_cart_item(cart_item_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = show(db, cart_item_id, current_user.id)

    if not data:
        not_found("Cart item not found")

    return success("Cart item fetched successfully", data)

@router.put("/{cart_item_id}")
async def update_cart_item(cart_item_id: int, cart_item: CartItems, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = update(db, cart_item_id, cart_item, current_user.id)

    if not data:
        not_found("Cart item not found")

    return success("Cart item updated successfully", data)

@router.delete("/{cart_item_id}")
async def delete_cart_item(cart_item_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = destroy(db, cart_item_id, current_user.id)

    if not data:
        not_found("Cart item not found")

    return success("Cart item deleted successfully", data)
