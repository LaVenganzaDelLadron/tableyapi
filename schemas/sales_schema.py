from datetime import datetime
from pydantic import BaseModel, Field


class Sales(BaseModel):
    product_id: int = Field(ge=1)
    sales_date: datetime = Field(default=datetime.now)
    total_amount: float = Field(ge=0.0)
    created_at: datetime = Field(default_factory=datetime.now)
    updated_at: datetime = Field(default_factory=datetime.now)