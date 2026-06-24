from datetime import datetime

from pydantic import BaseModel, Field


class CartItems(BaseModel):
    product_id: int = Field(gt=0)
    quantity: int = Field(ge=1)
    cart_id: int | None = Field(default=None, gt=0)

    created_at: datetime | None = None
    updated_at: datetime | None = None

