from datetime import datetime
from pydantic import BaseModel


class CreateUser(BaseModel):
    email: str
    full_name: str
    username: str
    password: str
    role: str
    created_at: datetime
    updated_at: datetime


class LoginUser(BaseModel):
    email: str
    password: str




