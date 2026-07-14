from datetime import datetime
from pydantic import BaseModel, Field

class ProductionBatches(BaseModel):
    raw_id: int = Field(ge=1)
    roast_date: datetime
    milled_weight: float = Field(ge=0)
    packaged_pieces: int = Field(ge=0)
    production_cost: float = Field(ge=0)
    created_at: datetime = Field(default_factory=datetime.now)
    updated_at: datetime = Field(default_factory=datetime.now)

