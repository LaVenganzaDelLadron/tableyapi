from datetime import datetime
from pydantic import BaseModel, Field

class Shipping(BaseModel):
    order_id: int = Field(gt=0)
    courier_id: str = Field(min_length=1)
    tracking_number: str = Field(min_length=1)
    shipping_fee: float = Field(ge=0)
    shipped_at: datetime | None = None
    delivered_at: datetime | None = None
    created_at: datetime | None = None
    updated_at: datetime | None = None
