from datetime import datetime
from pydantic import BaseModel

class CartItems(BaseModel):
    product_id: int
    quantity: int
    cart_id: int | None = None
    created_at: datetime | None = None
    updated_at: datetime | None = None
