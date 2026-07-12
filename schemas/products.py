from pydantic import BaseModel, Field
from datetime import datetime


class Products(BaseModel):
    product_name: str = Field(min_length=1, max_length=100)
    selling_price: float = Field(min_length=1, max_length=100)
    current_stock: int = Field(min_length=1, max_length=100)
    created_at: datetime = Field(default_factory=datetime.now)
    updated_at: datetime = Field(default_factory=datetime.now)