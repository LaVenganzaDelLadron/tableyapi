from fastapi import FastAPI

from app.api.v1.router import router as v1_router
from app.config import get_settings

settings = get_settings()

app = FastAPI(
    title=settings.app_name,
    version="1.0.0",
    description="FastAPI backend scaffold for the tabley API project.",
)

app.include_router(v1_router, prefix="/api/v1")


@app.get("/")
def root() -> dict[str, str]:
    return {
        "message": f"Welcome to {settings.app_name}",
        "environment": settings.app_env,
    }


@app.get("/health")
def health() -> dict[str, str]:
    return {"status": "ok", "app": settings.app_name}


if __name__ == "__main__":
    import uvicorn

    uvicorn.run("app.main:app", host="0.0.0.0", port=8000, reload=True)
