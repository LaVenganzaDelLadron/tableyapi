from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import bad_request, not_found, success
from schemas.payments import Payments
from services.payments_service import index, store, show, update, destroy

router = APIRouter()

@router.get("/")
async def list_payments(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = index(db, current_user.id)

    return success("Payments fetched successfully", data)

@router.post("/")
async def create_payment(payment: Payments, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = store(db, payment, current_user.id)

    if not data:
        bad_request("Failed to create payment")

    return success("Payment created successfully", data)

@router.get("/{payment_id}")
async def get_payment(payment_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = show(db, payment_id, current_user.id)

    if not data:
        not_found("Payment not found")

    return success("Payment fetched successfully", data)

@router.put("/{payment_id}")
async def update_payment(payment_id: int, payment: Payments, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = update(db, payment_id, payment, current_user.id)

    if not data:
        not_found("Payment not found")

    return success("Payment updated successfully", data)

@router.delete("/{payment_id}")
async def delete_payment(payment_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = destroy(db, payment_id, current_user.id)

    if not data:
        not_found("Payment not found")

    return success("Payment deleted successfully", data)





