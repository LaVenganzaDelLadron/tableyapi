from datetime import datetime
from pydantic import BaseModel, Field

class Payments(BaseModel):
    order_id: int = Field(gt=0)
    amount: float = Field(ge=0)
    payment_method: str = Field(min_length=1)
    payment_status: str = Field(min_length=1)
    transaction_id: str = Field(min_length=1)
    created_at: datetime | None = None
    updated_at: datetime | None = None
