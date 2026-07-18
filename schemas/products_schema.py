from datetime import datetime

from pydantic import BaseModel, Field

class Products(BaseModel):
    production_batches_id: int = Field(ge=1)
    name: str = Field(min_length=1, max_length=50)
    selling_price: float = Field(ge=0.0)
    current_stock: int = Field(ge=0)
    created_at: datetime = Field(default_factory=datetime.now)
    updated_at: datetime = Field(default_factory=datetime.now)