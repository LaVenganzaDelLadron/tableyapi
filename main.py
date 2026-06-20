import asyncio
import os

from dotenv import load_dotenv

from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from core.database import Base, engine
from core.schema_migrations import ensure_scan_columns


from api.routes.auth import router as auth_router
from api.routes.cart_items import router as cart_items_router
from api.routes.carts import router as carts_router
from api.routes.categories import router as categories_router
from api.routes.informations import router as informations_router
from api.routes.order_items import router as order_items_router
from api.routes.orders import router as orders_router
from api.routes.payments import router as payments_router
from api.routes.products import router as products_router
from api.routes.shipping import router as shipping_router




Base.metadata.create_all(bind=engine)
ensure_scan_columns(engine)

app = FastAPI(title="TableyApi", version="1.0.0")


load_dotenv()
cors_origins = os.getenv("CORS_ORIGINS", "*").split(",")



app.add_middleware(
    CORSMiddleware,
    allow_origins=cors_origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

EXEMPT_PATHS = {"/", "/docs", "/openapi.json", "/redoc", "/auth/register", "/auth/login"}
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
            content={"detail": f"Request exceeded {REQUEST_HARD_TIMEOUT:.0f}s timeout"},
        )


@app.middleware("http")
async def auth_middleware(request: Request, call_next):
    if request.method == "OPTIONS":
        return await call_next(request)

    if request.url.path in EXEMPT_PATHS:
        return await call_next(request)

    auth_header = request.headers.get("authorization", "")
    if not auth_header.startswith("Bearer "):
        return JSONResponse(status_code=401, content={"detail": "Authentication required"})

    token = auth_header.split(" ", 1)[1]
    from services.auth_service import decode_token

    payload = decode_token(token)
    if not payload or not payload.get("sub"):
        return JSONResponse(status_code=401, content={"detail": "Invalid or expired token"})

    request.state.user = payload
    response = await call_next(request)
    return response


@app.get("/")
async def root():
    return {"status": "Alive"}



app.include_router(auth_router, prefix="/auth", tags=["Auth"])
app.include_router(informations_router, prefix="/informations", tags=["Informations"])
app.include_router(categories_router, prefix="/categories", tags=["Categories"])
app.include_router(products_router, prefix="/products", tags=["Products"])
app.include_router(carts_router, prefix="/carts", tags=["Carts"])
app.include_router(cart_items_router, prefix="/cart-items", tags=["Cart Items"])
app.include_router(orders_router, prefix="/orders", tags=["Orders"])
app.include_router(order_items_router, prefix="/order-items", tags=["Order Items"])
app.include_router(payments_router, prefix="/payments", tags=["Payments"])
app.include_router(shipping_router, prefix="/shipping", tags=["Shipping"])

