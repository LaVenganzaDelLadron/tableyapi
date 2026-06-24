from datetime import datetime


from pydantic import BaseModel, Field


class OrderItems(BaseModel):
    order_id: int = Field(gt=0)
    product_id: int = Field(gt=0)
    quantity: int = Field(ge=1)
    price: float = Field(ge=0)

    created_at: datetime
    updated_at: datetime


