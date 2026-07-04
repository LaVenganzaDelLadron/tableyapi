
# Explanation:
# This file is part of the tableyapi backend and contains Validation and serialization schemas for cart items data.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from datetime import datetime

from pydantic import BaseModel, Field


class CartItems(BaseModel):
    product_id: int = Field(gt=0)
    quantity: int = Field(ge=1)
    cart_id: int | None = Field(default=None, gt=0)

    created_at: datetime | None = None
    updated_at: datetime | None = None
