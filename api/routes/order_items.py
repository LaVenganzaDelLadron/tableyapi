from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import bad_request, not_found, success
from schemas.order_items import OrderItems
from services.order_items_service import index, store, show, update, destroy


router = APIRouter()

@router.get("/")
async def list_order_items(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = index(db, current_user.id)

    return success("Order items fetched successfully", data)

@router.post("/")
async def create_order_item(order_item: OrderItems, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = store(db, order_item, current_user.id)

    if not data:
        bad_request("Failed to create order item")

    return success("Order item created successfully", data)

@router.get("/{order_item_id}")
async def get_order_item(order_item_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = show(db, order_item_id, current_user.id)

    if not data:
        not_found("Order item not found")

    return success("Order item fetched successfully", data)

@router.put("/{order_item_id}")
async def update_order_item(order_item_id: int, order_item: OrderItems, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = update(db, order_item_id, order_item, current_user.id)

    if not data:
        not_found("Order item not found")

    return success("Order item updated successfully", data)

@router.delete("/{order_item_id}")
async def delete_order_item(order_item_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = destroy(db, order_item_id, current_user.id)

    if not data:
        not_found("Order item not found")

    return success("Order item deleted successfully", data)
