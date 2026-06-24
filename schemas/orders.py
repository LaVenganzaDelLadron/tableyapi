from datetime import datetime

from pydantic import BaseModel

from schemas.order_status import OrderStatus


class Orders(BaseModel):
    information_id: int | None = None
    total_amount: float
    payment_method: str
    status: OrderStatus | None = None
    created_at: datetime | None = None
    updated_at: datetime | None = None


class OrderStatusUpdate(BaseModel):
    status: OrderStatus

