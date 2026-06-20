from datetime import datetime
from pydantic import BaseModel


class Categories(BaseModel):
    name: str
    created_at: datetime | None = None
    updated_at: datetime | None = None
