from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_admin, require_customer
from api.responses import bad_request, not_found, success
from schemas.orders import Orders
from models.orders import OrderStatus

from services.orders_service import destroy, index, index_by_user, show, show_for_user, store, update


router = APIRouter()

@router.get("/")
async def list_orders(db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = index(db)

    return success("Orders fetched successfully", data)

@router.get("/my-orders")
async def my_orders(db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = index_by_user(db, current_user.id)

    return success("Orders fetched successfully", data)


@router.post("/")
async def create_order(order: Orders, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = store(db, current_user.id, order.information_id, order.total_amount, order.payment_method)

    if not data:
        bad_request("Failed to create order")

    return success("Order created successfully", data)

@router.get("/{order_id}")
async def get_order(order_id: int, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = show_for_user(db, order_id, current_user.id)

    if not data:
        not_found("Order not found")

    return success("Order fetched successfully", data)

@router.put("/{order_id}")
async def update_order(order_id: int, order: Orders, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    # status changes are constrained by the service.
    data = update(
        db,
        order_id,
        order.information_id,
        order.total_amount,
        order.status.value if order.status else OrderStatus.PENDING.value,
        order.payment_method,
    )

    if not data:
        not_found("Order not found")

    return success("Order updated successfully", data)



@router.delete("/{order_id}")
async def delete_order(order_id: int, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = destroy(db, order_id)

    if not data:
        not_found("Order not found")

    return success("Order deleted successfully", data)
