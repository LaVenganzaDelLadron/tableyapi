
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

from models.audit_logs import AuditLogs
from models.customer_addresses import CustomerAddresses
from models.order_status_history import OrderStatusHistory
from models.password_resets import PasswordResets

from api.routes.auth import router as auth_router
from api.routes.admin import router as admin_router
from api.routes.cart_items import router as cart_items_router
from api.routes.carts import router as carts_router
from api.routes.categories import router as categories_router
from api.routes.informations import router as informations_router
from api.routes.order_items import router as order_items_router
from api.routes.orders import router as orders_router
from api.routes.payments import router as payments_router
from api.routes.products import router as products_router
from api.routes.shipping import router as shipping_router
from api.routes.customer_addresses import router as customer_addresses_router




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
app.include_router(admin_router, prefix="/admin", tags=["Admin"])
app.include_router(informations_router, prefix="/informations", tags=["Informations"])
app.include_router(categories_router, prefix="/categories", tags=["Categories"])
app.include_router(products_router, prefix="/products", tags=["Products"])
app.include_router(carts_router, prefix="/carts", tags=["Carts"])
app.include_router(cart_items_router, prefix="/cart-items", tags=["Cart Items"])
app.include_router(customer_addresses_router, prefix="/addresses", tags=["Addresses"])
app.include_router(orders_router, prefix="/orders", tags=["Orders"])
app.include_router(order_items_router, prefix="/order-items", tags=["Order Items"])
app.include_router(payments_router, prefix="/payments", tags=["Payments"])
app.include_router(shipping_router, prefix="/shipping", tags=["Shipping"])