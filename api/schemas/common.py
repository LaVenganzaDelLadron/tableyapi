
# Explanation:
# This file is part of the tableyapi backend and contains API schema definitions for common payloads.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from typing import Any, Generic, TypeVar

from pydantic import BaseModel


T = TypeVar("T")


class ApiError(BaseModel):
    code: str
    detail: str


class ApiResponse(BaseModel, Generic[T]):
    message: str
    data: T | None = None
    error: ApiError | None = None

    @classmethod
    def success(cls, message: str, data: Any = None) -> "ApiResponse[Any]":
        return cls(message=message, data=data, error=None)

    @classmethod
    def failure(cls, message: str, code: str, detail: str) -> "ApiResponse[Any]":
        return cls(message=message, data=None, error=ApiError(code=code, detail=detail))