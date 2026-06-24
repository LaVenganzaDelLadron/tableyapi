from datetime import datetime

from pydantic import BaseModel, Field

from schemas.order_status import OrderStatus


class Orders(BaseModel):
    information_id: int | None = Field(default=None, gt=0)
    total_amount: float = Field(ge=0)
    payment_method: str = Field(min_length=1)
    status: OrderStatus | None = None
    created_at: datetime | None = None
    updated_at: datetime | None = None


class OrderStatusUpdate(BaseModel):
    status: OrderStatus


class CheckoutOrder(BaseModel):
    information_id: int | None = Field(default=None, gt=0)
    payment_method: str = Field(min_length=1)
