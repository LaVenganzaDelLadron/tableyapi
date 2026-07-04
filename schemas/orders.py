
# Explanation:
# This file is part of the tableyapi backend and contains Validation and serialization schemas for orders data.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

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