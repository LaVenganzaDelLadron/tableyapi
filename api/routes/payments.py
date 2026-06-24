from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_admin, require_customer
from api.responses import bad_request, not_found, success
from schemas.payments import Payments
from services.orders_service import show as show_order, show_for_user as show_order_for_user
from services.payments_service import apply_payment, destroy, index, show, store, update

router = APIRouter()

@router.get("/")
async def list_payments(db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = index(db)

    return success("Payments fetched successfully", data)

@router.post("/")
async def create_payment(payment: Payments, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    order = show_order_for_user(db, payment.order_id, current_user.id)
    if not order:
        not_found("Order not found")

    data = apply_payment(order, payment, db)

    if not data:
        bad_request("Invalid payment for current order state")

    return success("Payment created successfully", data)

@router.get("/{payment_id}")
async def get_payment(payment_id: int, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = show(db, payment_id)

    if not data:
        not_found("Payment not found")

    return success("Payment fetched successfully", data)

@router.put("/{payment_id}")
async def update_payment(payment_id: int, payment: Payments, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    order = show_order(db, payment.order_id)
    if not order:
        not_found("Order not found")

    data = update(db, payment_id, payment.order_id, payment.amount, payment.payment_method, payment.payment_status, payment.transaction_id)

    if not data:
        not_found("Payment not found")

    return success("Payment updated successfully", data)

@router.delete("/{payment_id}")
async def delete_payment(payment_id: int, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = destroy(db, payment_id)

    if not data:
        not_found("Payment not found")

    return success("Payment deleted successfully", data)



