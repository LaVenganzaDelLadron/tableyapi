from datetime import datetime
from pydantic import BaseModel, Field


class Categories(BaseModel):
    name: str = Field(min_length=1)
    created_at: datetime | None = None
    updated_at: datetime | None = None
