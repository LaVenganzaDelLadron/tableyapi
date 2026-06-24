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
