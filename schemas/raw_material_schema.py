from datetime import datetime
from pydantic import BaseModel, Field


class RawMaterialSchema(BaseModel):
    user_id: int = Field(ge=1)
    name: str = Field(min_length=1, max_length=50)
    weight: float = Field(ge=0.0)
    unit_price: float = Field(ge=0.0)
    total_price: float = Field(ge=0.0)
    created_at: datetime = Field(default_factory=datetime.now)
    updated_at: datetime = Field(default_factory=datetime.now)