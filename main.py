
# Explanation:
# This file is part of the tableyapi backend and contains Application module for main.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

import asyncio
import os

from dotenv import load_dotenv

from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from api.handlers import register_exception_handlers
from api.responses import error_payload
from cores.database import engine, Base
from cores.schema_migrations import ensure_scan_columns

from models.password_resets import PasswordResets
from models.raw_materials import RawMaterials
from models.product_batches import ProductBatches
from models.products import Products
from models.sales import Sales
from models.sale_items import SaleItems

from api.routes.auth import router as auth_router
from api.routes.sales import router as sales_router
from api.routes.sale_items import router as sale_items_router
from api.routes.products import router as products_router
from api.routes.production_batches import router as production_batches_router
from api.routes.raw_materials import router as raw_materials_router




Base.metadata.create_all(bind=engine)
ensure_scan_columns(engine)

app = FastAPI(title="TableyApi", version="1.0.0")
register_exception_handlers(app)


load_dotenv()
cors_origins = os.getenv("CORS_ORIGINS", "*").split(",")



app.add_middleware(
    CORSMiddleware,
    allow_origins=cors_origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

REQUEST_HARD_TIMEOUT = float(os.getenv("REQUEST_HARD_TIMEOUT", "45"))
TIMEOUT_EXEMPT_PREFIXES = (
    "/docs",
    "/openapi.json",
    "/redoc",
)

def _is_timeout_exempt(path: str) -> bool:
    return any(path.startswith(prefix) for prefix in TIMEOUT_EXEMPT_PREFIXES) or path.endswith("/status") or path.endswith("/logs")

@app.middleware("http")
async def request_timeout_middleware(request: Request, call_next):
    path = request.url.path or ""
    if _is_timeout_exempt(path):
        return await call_next(request)

    try:
        return await asyncio.wait_for(call_next(request), timeout=REQUEST_HARD_TIMEOUT)
    except asyncio.TimeoutError:
        return JSONResponse(
            status_code=504,
            content=error_payload(
                "Request timed out",
                "REQUEST_TIMEOUT",
                f"Request exceeded {REQUEST_HARD_TIMEOUT:.0f}s timeout",
            ),
        )


@app.get("/")
async def root():
    return {"status": "Alive"}



app.include_router(auth_router, prefix="/auth", tags=["Auth"])
app.include_router(raw_materials_router, prefix="/raw_materials", tags=["Raw Materials"])
app.include_router(production_batches_router, prefix="/production_batches", tags=["Production Batches"])
app.include_router(products_router, prefix="/products", tags=["Products"])
app.include_router(sales_router, prefix="/sales", tags=["Sales"])
app.include_router(sale_items_router, prefix="/sale_items", tags=["Sale Items"])