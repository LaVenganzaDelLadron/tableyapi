from datetime import datetime
from pydantic import BaseModel, Field

class Informations(BaseModel):
    user_id: int | None = Field(default=None, gt=0)
    phone: str = Field(min_length=1)
    address: str = Field(min_length=1)
    city: str = Field(min_length=1)
    province: str = Field(min_length=1)
    street: str = Field(min_length=1)
    postal_code: str = Field(min_length=1)
    created_at: datetime | None = None
    updated_at: datetime | None = None

