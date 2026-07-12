from datetime import datetime
from pydantic import BaseModel, Field

class RawMaterials(BaseModel):
    material_name: str = Field(min_length=1, max_length=100)
    quantity: int = Field(min_length=1, max_length=100)
    unit: str = Field(min_length=1)
    created_at: datetime = Field(default_factory=datetime.now)
    updated_at: datetime = Field(default_factory=datetime.now)