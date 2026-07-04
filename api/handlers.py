
# Explanation:
# This file is part of the tableyapi bac utilities for handlers.
# The original code lines remain unchanged; these comments are added to explain kend and contains Shared API helpers andthe purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from fastapi import FastAPI, HTTPException, Request, status
from fastapi.exceptions import RequestValidationError
from fastapi.responses import JSONResponse

from api.responses import error_payload


def _error_response(status_code: int, message: str, code: str, detail: str | None = None) -> JSONResponse:
    return JSONResponse(
        status_code=status_code,
        content=error_payload(message=message, code=code, detail=detail),
    )


def _detail_to_message(detail) -> tuple[str, str | None]:
    if isinstance(detail, dict) and {"message", "error"}.issubset(detail.keys()):
        error = detail.get("error") or {}
        return str(detail.get("message") or "Request failed"), error.get("detail")
    if isinstance(detail, str):
        return detail, detail
    return "Request failed", str(detail)


async def http_exception_handler(request: Request, exc: HTTPException) -> JSONResponse:
    if isinstance(exc.detail, dict) and {"message", "data", "error"}.issubset(exc.detail.keys()):
        return JSONResponse(status_code=exc.status_code, content=exc.detail)

    message, detail = _detail_to_message(exc.detail)
    code_by_status = {
        status.HTTP_400_BAD_REQUEST: "BAD_REQUEST",
        status.HTTP_401_UNAUTHORIZED: "UNAUTHORIZED",
        status.HTTP_403_FORBIDDEN: "FORBIDDEN",
        status.HTTP_404_NOT_FOUND: "NOT_FOUND",
        status.HTTP_409_CONFLICT: "CONFLICT",
    }
    return _error_response(exc.status_code, message, code_by_status.get(exc.status_code, "HTTP_ERROR"), detail)


async def validation_exception_handler(request: Request, exc: RequestValidationError) -> JSONResponse:
    return _error_response(
        status.HTTP_422_UNPROCESSABLE_ENTITY,
        "Validation failed",
        "VALIDATION_ERROR",
        str(exc.errors()),
    )


async def unhandled_exception_handler(request: Request, exc: Exception) -> JSONResponse:
    return _error_response(
        status.HTTP_500_INTERNAL_SERVER_ERROR,
        "Internal server error",
        "INTERNAL_SERVER_ERROR",
        "An unexpected error occurred",
    )


def register_exception_handlers(app: FastAPI) -> None:
    app.add_exception_handler(HTTPException, http_exception_handler)
    app.add_exception_handler(RequestValidationError, validation_exception_handler)
    app.add_exception_handler(Exception, unhandled_exception_handler)