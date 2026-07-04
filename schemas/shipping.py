
# Explanation:
# This file is part of the tableyapi backend and contains Validation and serialization schemas for shipping data.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

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