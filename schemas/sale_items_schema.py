from datetime import datetime
from pydantic import BaseModel, Field

class SaleItems(BaseModel):
    sale_id: int = Field(ge=1)
    quantity: int = Field(ge=1)
    unit_price: float = Field(ge=0.0)
    subtotal: float = Field(ge=0.0)
    created_at: datetime = Field(default_factory=datetime.now)
    updated_at: datetime = Field(default_factory=datetime.now)