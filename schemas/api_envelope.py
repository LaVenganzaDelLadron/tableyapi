from typing import Any, Generic, Optional, TypeVar

from pydantic import BaseModel


T = TypeVar("T")


class ApiError(BaseModel):
    code: str
    detail: str


class ApiResponse(BaseModel, Generic[T]):
    message: str
    data: Optional[T] = None
    error: Optional[ApiError] = None

    # Validation rules
    def model_post_init(self, __context: Any) -> None:  # pydantic v2 hook
        if self.error is None:
            return
        # error responses: data must be None
        self.data = None

    @classmethod
    def success(cls, message: str, data: Optional[Any] = None) -> "ApiResponse[Any]":
        return cls(message=message, data=data, error=None)

    @classmethod
    def failure(cls, message: str, code: str, detail: str) -> "ApiResponse[Any]":
        return cls(message=message, data=None, error=ApiError(code=code, detail=detail))


# Aliases for clarity
ApiSuccessResponse = ApiResponse
ApiErrorResponse = ApiResponse


