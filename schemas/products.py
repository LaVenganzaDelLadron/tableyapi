from datetime import datetime
from pydantic import BaseModel

class Products(BaseModel):
    category_id: int
    name: str
    description: str
    price: float
    stock: int
    image: str
    status: str
    created_at: datetime
    updated_at: datetime