from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import bad_request, not_found, success
from schemas.carts import Carts
from services.carts_service import index, store, show, update, destroy


router = APIRouter()

@router.get("/")
async def list_carts(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = index(db, current_user.id)

    return success("Carts fetched successfully", data)

@router.post("/")
async def create_cart(cart: Carts, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = store(db, cart, current_user.id)

    if not data:
        bad_request("Failed to create cart")

    return success("Cart created successfully", data)

@router.get("/{cart_id}")
async def get_cart(cart_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = show(db, cart_id, current_user.id)

    if not data:
        not_found("Cart not found")

    return success("Cart fetched successfully", data)

@router.put("/{cart_id}")
async def update_cart(cart_id: int, cart: Carts, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = update(db, cart_id, cart, current_user.id)

    if not data:
        not_found("Cart not found")

    return success("Cart updated successfully", data)

@router.delete("/{cart_id}")
async def delete_cart(cart_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = destroy(db, cart_id, current_user.id)

    if not data:
        not_found("Cart not found")

    return success("Cart deleted successfully", data)
