from datetime import datetime
from pydantic import BaseModel, Field

class ProductionBatches(BaseModel):
    raw_id: int = Field(min_length=1)
    roast_date: datetime = datetime
    milled_weight: float = Field(min_length=1)
    packaged_pieces: int = Field(min_length=1)
    production_cost: float = Field(min_length=1)
    created_at: datetime = Field(default_factory=datetime.now)
    updated_at: datetime = Field(default_factory=datetime.now)