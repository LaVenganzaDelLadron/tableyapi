
# Explanation:
# This file is part of the tableyapi backend and contains Validation and serialization schemas for informations data.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from datetime import datetime
from pydantic import BaseModel, Field

class Informations(BaseModel):
    user_id: int | None = Field(default=None, gt=0)
    phone: str = Field(min_length=1)
    address: str = Field(min_length=1)
    city: str = Field(min_length=1)
    province: str = Field(min_length=1)
    street: str = Field(min_length=1)
    postal_code: str = Field(min_length=1)
    created_at: datetime | None = None
    updated_at: datetime | None = None
