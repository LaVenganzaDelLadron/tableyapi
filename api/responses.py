from typing import Any

from fastapi import HTTPException
from starlette import status

from api.schemas.common import ApiResponse


def success(message: str, data: Any = None):
    return ApiResponse.success(message=message, data=data)


def error_payload(message: str, code: str, detail: str | None = None) -> dict[str, Any]:
    return ApiResponse.failure(message=message, code=code, detail=detail or message).model_dump()


def bad_request(message: str, code: str = "BAD_REQUEST") -> None:
    raise HTTPException(
        status_code=status.HTTP_400_BAD_REQUEST,
        detail=error_payload(message, code),
    )


def not_found(message: str, code: str = "NOT_FOUND") -> None:
    raise HTTPException(
        status_code=status.HTTP_404_NOT_FOUND,
        detail=error_payload(message, code),
    )

