from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_admin
from api.responses import success
from schemas.expenses import Expenses
from services.expenses_service import index, store

router = APIRouter()


@router.get("/")
async def list_expenses(db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = index(db)
    return success("Expenses fetched successfully", data)


@router.post("/")
async def create_expense(payload: Expenses, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = store(db, payload.description, payload.amount, payload.category, payload.entry_date)
    return success("Expense created successfully", data)
