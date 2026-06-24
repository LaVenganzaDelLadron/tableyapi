from pydantic import BaseModel, Field


class CreateUser(BaseModel):
    email: str = Field(min_length=1)
    full_name: str = Field(min_length=1)
    username: str = Field(min_length=1)
    password: str = Field(min_length=6)


class LoginUser(BaseModel):
    email: str = Field(min_length=1)
    password: str


class UpdateProfile(BaseModel):
    email: str | None = None
    full_name: str | None = None
    username: str | None = None


class ChangePassword(BaseModel):
    current_password: str
    new_password: str
