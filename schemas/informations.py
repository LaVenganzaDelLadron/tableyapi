from datetime import datetime
from pydantic import BaseModel

class Information(BaseModel):
    user_id: int
    phone: str
    address: str
    city: str
    province: str
    street: str
    postal_code: str
    created_at: datetime
    updated_at: datetime


