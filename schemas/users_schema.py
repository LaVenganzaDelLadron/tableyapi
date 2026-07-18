from pydantic import BaseModel, Field

class RegisterUser(BaseModel):
    email: str = Field(min_length=5, max_length=50)
    full_name: str = Field(min_length=5, max_length=50)
    password: str = Field(min_length=5, max_length=50)

class LoginUser(BaseModel):
    email: str
    password: str
