from datetime import datetime
from pydantic import BaseModel, Field

class Sales(BaseModel):
    sale_date: datetime = Field(default_factory=datetime.now)
    total_amount: float = Field(min_length=1)
    created_at: datetime = Field(default_factory=datetime.now)
    updated_at: datetime = Field(default_factory=datetime.now)