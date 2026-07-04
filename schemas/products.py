
# Explanation:
# This file is part of the tableyapi backend and contains Validation and serialization schemas for products data.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from datetime import datetime
from pydantic import BaseModel, Field

class Products(BaseModel):
    category_id: int = Field(gt=0)
    name: str = Field(min_length=1)
    description: str = Field(min_length=1)
    price: float = Field(ge=0)
    stock: int = Field(ge=0)
    image: str | None = None
    status: str | None = None
    created_at: datetime | None = None
    updated_at: datetime | None = None


class StockAdjustment(BaseModel):
    quantity_delta: int
    reason: str | None = None


class ProductImageUpload(BaseModel):
    filename: str = Field(min_length=1)
    content_base64: str = Field(min_length=1)