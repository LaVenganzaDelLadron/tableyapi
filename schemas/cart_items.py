from datetime import datetime
from pydantic import BaseModel

class CartItems(BaseModel):
    cart_id: int
    product_id: int
    quantity: int
    created_at: datetime
    updated_at: datetime