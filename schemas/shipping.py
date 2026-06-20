from datetime import datetime
from pydantic import BaseModel

class Shipping(BaseModel):
    order_id: int
    courier_id: int
    tracking_number: int
    shipping_fee: float
    shipped_at: datetime
    delivered_at: datetime
    created_at: datetime | None = None
    updated_at: datetime | None = None
