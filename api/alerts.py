from typing import Any, Generic, TypeVar
from pydantic import BaseModel

T = TypeVar("T")

class AlertError(BaseModel):
    code: str
    message: str

class ApiAlert(BaseModel, Generic[T]):
    message: str
    data: T | None = None
    error: AlertError | None = None

    @classmethod
    def success(cls, message: str, data: Any = None) -> "ApiAlert[Any]":
        return cls(message=message, data=data, error=None)

    @classmethod
    def failure(cls, message: str, code: str, detail: str) -> "ApiAlert[Any]":
        return cls(message=message, data=None, error=AlertError(code=code, message=detail))