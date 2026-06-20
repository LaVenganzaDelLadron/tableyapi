from datetime import datetime
from pydantic import BaseModel

class Carts(BaseModel):
    user_id: int | None = None
    created_at: datetime | None = None
    updated_at: datetime | None = None
