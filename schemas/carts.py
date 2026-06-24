from datetime import datetime
from pydantic import BaseModel, Field

class Carts(BaseModel):
    user_id: int | None = Field(default=None, gt=0)
    created_at: datetime | None = None
    updated_at: datetime | None = None
