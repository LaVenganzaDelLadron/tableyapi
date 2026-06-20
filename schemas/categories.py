from datetime import datetime
from pydantic import BaseModel


class Categories(BaseModel):
    user_id: int
    name: str
    created_at: datetime
    updated_at: datetime