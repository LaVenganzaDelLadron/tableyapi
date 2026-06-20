from datetime import datetime
from pydantic import BaseModel

class OrderItems(BaseModel):
    order_id: int
    product_id: int
    quantity: int
    price: float
    created_at: datetime
    updated_at: datetime