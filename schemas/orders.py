from datetime import datetime
from pydantic import BaseModel

class Orders(BaseModel):
    user_id: int
    information_id: int
    total_amount: float
    status: str
    payment_method: str
    created_at: datetime
    updated_at: datetime