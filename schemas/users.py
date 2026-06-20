from pydantic import BaseModel


class CreateUser(BaseModel):
    email: str
    full_name: str
    username: str
    password: str


class LoginUser(BaseModel):
    email: str
    password: str


class UpdateProfile(BaseModel):
    email: str | None = None
    full_name: str | None = None
    username: str | None = None


class ChangePassword(BaseModel):
    current_password: str
    new_password: str

