from typing import Optional
from datetime import date
from sqlalchemy.orm import Session
from sqlalchemy import func

from models.earnings import Earnings
from models.expenses import Expenses


def net_summary(db: Session, start_date: Optional[date] = None, end_date: Optional[date] = None):
    earnings_q = db.query(func.coalesce(func.sum(Earnings.amount), 0))
    expenses_q = db.query(func.coalesce(func.sum(Expenses.amount), 0))

    if start_date:
        earnings_q = earnings_q.filter(Earnings.entry_date >= start_date)
        expenses_q = expenses_q.filter(Expenses.entry_date >= start_date)
    if end_date:
        earnings_q = earnings_q.filter(Earnings.entry_date <= end_date)
        expenses_q = expenses_q.filter(Expenses.entry_date <= end_date)

    total_earnings = float(earnings_q.scalar() or 0)
    total_expenses = float(expenses_q.scalar() or 0)
    net = total_earnings - total_expenses

    return {
        "total_earnings": total_earnings,
        "total_expenses": total_expenses,
        "net": net,
    }
