# TableyApi (Cacao Backend)

<div align="center">
  <img src="./chocolate.gif" alt="chocolate" />
</div>


FastAPI + SQLAlchemy backend for managing a simple cacao/business workflow:
- **Authentication** (register/login/JWT, password reset/change, profile)
- **Raw materials** inventory
- **Production batches** (turn raw materials into product batches)
- **Products** catalog and current stock
- **Sales** and **sale items**

The API is built around small “index/store” endpoints per resource (list + create).

> Base persistence: **SQLite** (`storage/app.db`).

---

## Features

### 1) REST API (FastAPI)
- App is created in `main.py` and mounted with routers under:
  - `/auth` (Authentication)
  - `/raw_materials` (Raw materials)
  - `/production_batches` (Production batches)
  - `/products` (Products)
  - `/sales` (Sales)
  - `/sale_items` (Sale items)
- Root endpoint:
  - `GET /` → `{ "status": "Alive" }`

### 2) Consistent API envelope
All successful responses use `api.responses.success(...)` which returns an `ApiResponse` model.
- Success: `success(message, data)`
- Error payloads: `error_payload(message, code, detail)`

Error handling is centralized in `api/handlers.py`:
- `HTTPException` → standardized JSON error structure
- `RequestValidationError` (422) → standardized “Validation failed”
- Unhandled exceptions → `500 Internal server error`

### 3) JWT authentication + HTTP Bearer
Authentication is handled in `services/auth_service.py`:
- Password hashing uses **PBKDF2-HMAC-SHA256**
- Access tokens are **JWT (HS256)** created by `create_access_token(...)`
- Token decoding by `decode_token(...)`

Protection is handled by `api/dependencies.py`:
- `get_current_user(...)` expects `Authorization: Bearer <token>`
- `require_role(...)`, `require_admin(...)`, `require_customer(...)` helpers exist for role-gated endpoints

Auth endpoints (`api/routes/auth.py`):
- `POST /auth/register`
- `POST /auth/login`
- `POST /auth/logout` (returns success payload)
- `GET /auth/me` (current user)
- `PUT /auth/me` (update email/full name/username)
- `POST /auth/change-password`
- `POST /auth/forgot-password` (returns `reset_token` if user exists)
- `POST /auth/reset-password`

Password reset flow:
- Reset tokens are random strings stored as **SHA256(token) hashes** in `models/password_resets.py`.
- Tokens expire after a configurable window (default 30 minutes inside `create_password_reset`).
- Reset marks the token as used (`used_at`).

### 4) CRUD-style workflow endpoints (index/store)
Each resource router typically provides:
- `GET /<resource>/` → list newest first
- `POST /<resource>/` → create a record

Implemented resources:
- **Products** (`api/routes/products.py` → `Products` model)
- **Raw materials** (`api/routes/raw_materials.py` → `RawMaterials` model)
- **Production batches** (`api/routes/production_batches.py` → `ProductBatches` model)
- **Sales** (`api/routes/sales.py` → `Sales` model)
- **Sale items** (`api/routes/sale_items.py` → `SaleItems` model)

Each “store” uses the corresponding Pydantic schema in `schemas/`.

### 5) Server hard timeout middleware
`main.py` adds `request_timeout_middleware`:
- Timeout (default **45s**) from `REQUEST_HARD_TIMEOUT`
- Exempts `/docs`, `/openapi.json`, `/redoc`
- On timeout returns **504** with standardized error payload

### 6) Database auto-create
On startup:
- `Base.metadata.create_all(bind=engine)` creates tables
- `cores/schema_migrations.py` runs `ensure_scan_columns(engine)`

Note: `ensure_scan_columns` includes logic for a generic “scans”/RBAC migration set.
The core models for this project (users/products/etc.) are still created through `create_all`.

---

## Data model (what gets stored)

### Users / Roles
- `models/users.py`
- `UserRole` enum:
  - `admin` (`UserRole.ADMIN`)
  - `customer` (`UserRole.CUSTOMER`)

### Products
- `models/products.py`
- Columns: `product_name`, `selling_price`, `current_stock`

### Raw materials
- `models/raw_materials.py`
- Columns: `material_name`, `quantity`, `unit`

### Production batches
- `models/product_batches.py`
- Columns: `raw_id` (FK to raw_materials), `roast_date`, `milled_weight`, `package_pieces`, `production_cost`

### Sales
- `models/sales.py`
- Columns: `sales_date`, `total_amount`

### Sale items
- `models/sale_items.py`
- Columns: `sale_id` (FK to sales), `product_id` (FK to products), `quantity`, `unit_price`, `subtotal`

---

## API Endpoints (by router)

### Auth (`/auth`)
- `POST /auth/register`
  - Body: `schemas/users.CreateUser`
- `POST /auth/login`
  - Body: `schemas/users.LoginUser`
  - Returns: JWT in `data.session`
- `GET /auth/me`
- `PUT /auth/me`
- `POST /auth/change-password`
- `POST /auth/forgot-password`
- `POST /auth/reset-password`

### Raw materials (`/raw_materials`)
- `GET /raw_materials/` (requires authentication)
- `POST /raw_materials/` (requires authentication)
  - Body: `schemas/raw_materials.RawMaterials`

### Production batches (`/production_batches`)
- `GET /production_batches/` (requires authentication)
- `POST /production_batches/` (requires authentication)
  - Body: `schemas/production_batches.ProductionBatches`

### Products (`/products`)
- `GET /products/` (requires authentication)
- `POST /products/` (requires authentication)
  - Body: `schemas/products.Products`

### Sales (`/sales`)
- `GET /sales/` (requires authentication)
- `POST /sales/` (requires authentication)
  - Body: `schemas/sales.Sales`

### Sale items (`/sale_items`)
- `GET /sale_items/` (requires authentication)
- `POST /sale_items/` (requires authentication)
  - Body: `schemas/sale_items.SaleItems`

---

## Local development

### 1) Environment variables
The app loads `.env` via `python-dotenv`.

At minimum you should set:
- `JWT_SECRET_KEY` (required for JWT signing)
- `CORS_ORIGINS` (optional, defaults to `*`)
- `REQUEST_HARD_TIMEOUT` (optional, defaults to `45`)

(See `.env.example` in the repo.)

### 2) Install & run
Use your project’s dependency setup (Poetry config exists via `pyproject.toml`, and dev requirements exist in `requirements-dev.txt`).

Example run:
- `uvicorn main:app --reload --port 8000`

Swagger docs:
- `GET /docs`
- OpenAPI: `GET /openapi.json`

---

## Notes / Implementation details
- DB engine is in `cores/database.py` and uses:
  - SQLite URL: `sqlite:///storage/app.db`
  - `check_same_thread=False` for SQLite compatibility
- Auth uses `HTTPBearer(auto_error=False)` and returns 401 if missing/invalid token.
- “index” endpoints return results ordered by `id.desc()`.
- “store” endpoints commit immediately and return the newly created row.

---

## GIF
This repo also includes `chocolate.gif` as a small visual asset.

