from typing import Any, Optional

from fastapi import HTTPException
from starlette import status

from schemas.api_envelope import ApiResponse


def success(message: str, data: Optional[Any] = None):
    return ApiResponse.success(message=message, data=data)


def bad_request(message: str, code: str = "BAD_REQUEST"):
    raise HTTPException(
        status_code=status.HTTP_400_BAD_REQUEST,
        detail=ApiResponse.failure(message=message, code=code, detail=message).model_dump(),
    )


def not_found(message: str, code: str = "NOT_FOUND"):
    raise HTTPException(
        status_code=status.HTTP_404_NOT_FOUND,
        detail=ApiResponse.failure(message=message, code=code, detail=message).model_dump(),
    )


