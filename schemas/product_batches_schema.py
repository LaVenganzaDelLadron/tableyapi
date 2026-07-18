from datetime import datetime
from pydantic import BaseModel, Field


class ProductBatchesSchema(BaseModel):
    raw_material_id: int = Field(ge=1)
    roast_date: datetime = Field(default=datetime.now)
    milled_weight: float = Field(min_length=1, max_length=50)
    package_pieces: int = Field(min_length=1, max_length=50)
    created_at: datetime = Field(default_factory=datetime.now)
    updated_at: datetime = Field(default_factory=datetime.now)
