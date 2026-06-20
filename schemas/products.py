from datetime import datetime
from pydantic import BaseModel

class Products(BaseModel):
    category_id: int
    name: str
    description: str
    price: float
    stock: int
    image: str | None = None
    status: str | None = None
    created_at: datetime | None = None
    updated_at: datetime | None = None
