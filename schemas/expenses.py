from datetime import date, datetime
from pydantic import BaseModel, Field


class Expenses(BaseModel):
    description: str = Field(min_length=1)
    amount: float = Field(ge=0)
    entry_date: date
    created_at: datetime | None = None
    updated_at: datetime | None = None
