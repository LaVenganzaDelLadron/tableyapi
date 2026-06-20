from datetime import datetime
from pydantic import BaseModel

class Carts(BaseModel):
    user_id: int
    created_at: datetime
    updated_at: datetime