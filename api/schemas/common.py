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
