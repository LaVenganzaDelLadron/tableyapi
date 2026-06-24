from pydantic import BaseModel, Field


class CustomerAddress(BaseModel):
    recipient_name: str = Field(min_length=1)
    phone: str = Field(min_length=1)
    address_line: str = Field(min_length=1)
    city: str = Field(min_length=1)
    province: str = Field(min_length=1)
    postal_code: str = Field(min_length=1)
    is_default: int = 0


class CustomerAddressUpdate(BaseModel):
    recipient_name: str | None = None
    phone: str | None = None
    address_line: str | None = None
    city: str | None = None
    province: str | None = None
    postal_code: str | None = None
    is_default: int | None = None
