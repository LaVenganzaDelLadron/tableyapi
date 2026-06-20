from datetime import datetime
from pydantic import BaseModel

class Payments(BaseModel):
    order_id: int
    amount: float
    payment_method: str
    payment_status: str
    transaction_id: int
    created_at: datetime
    updated_at: datetime