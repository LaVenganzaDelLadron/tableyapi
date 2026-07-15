from datetime import datetime
from pydantic import BaseModel, Field

class SaleItems(BaseModel):
    sale_id: int = Field(min_length=1)
    product_id: int = Field(min_length=1)
    quantity: int = Field(min_length=1)
    unit_price: float = Field(min_length=1)
    subtotal: float = Field(min_length=1)
    created_at: datetime = Field(default_factory=datetime.now)
    updated_at: datetime = Field(default_factory=datetime.now)