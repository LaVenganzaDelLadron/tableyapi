
# Explanation:
# This file is part of the tableyapi backend and contains Validation and serialization schemas for users data.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

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


class ForgotPassword(BaseModel):
    email: str = Field(min_length=1)


class ResetPassword(BaseModel):
    token: str = Field(min_length=1)
    new_password: str = Field(min_length=6)