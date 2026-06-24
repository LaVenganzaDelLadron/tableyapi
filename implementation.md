# Implementation Plan

[Overview]
Add production-grade features to the TableyApi backend by introducing consistent validation, authorization coverage, order/payment/shipping lifecycle controls, and a standardized error/response system, backed by automated tests.

The repository already implements a layered architecture (API routes → services → models/schemas) with JWT auth and role-based dependencies. To move from a “CRUD demo” to a reliable API, the highest-risk gaps typically occur in cross-table validation, resource ownership scoping, business-rule enforcement (order lifecycle), and response consistency.

This implementation plan focuses on must-add features that prevent data leakage between users, reduce inconsistent client behavior, and ensure state transitions (orders/payments/shipping) are deterministic and idempotent. The plan is designed to fit the existing file structure: add or update logic primarily in `services/*` and `api/routes/*`, plus shared utilities and test scaffolding.

[Types]  
Introduce/extend Pydantic schema types and shared enums for lifecycle states and standardized error/response payloads.

Detailed type definitions, interfaces, enums, or data structures with complete specifications:

1. **Order lifecycle types (Pydantic) and enums**
   - Add/extend `OrderStatus` usage across schemas/services.
   - Define allowed statuses (match what the model already uses):
     - `PENDING`
     - `PAID`
     - `SHIPPED`
     - `COMPLETED`
     - `CANCELLED` (if supported)
   - Validation rules:
     - Updates to order status must use a constrained transition endpoint/service function.

2. **Standard API response wrapper (Pydantic)**
   - Add schema (or reuse existing `api/responses.py`) to define a consistent envelope:
     - `message: str`
     - `data: Any | None`
     - `error: { code: str, detail: str } | None`
   - Validation rules:
     - For success responses: `error` must be `None`.
     - For error responses: `data` must be `None`.

3. **Request validation schemas**
   - Ensure every “write” schema enforces:
     - `quantity: int` with `quantity >= 1` for cart/order items.
     - `price: Decimal` (or numeric) with `>= 0`.
     - foreign key ids (`product_id`, `cart_id`, `order_id`, `user_id`) as `int` with `> 0`.

[Files]
Update and add files across API routing, service-layer business rules, shared response/error handling, and tests.

Detailed breakdown:

- **New files**
  - `tests/test_auth_and_roles.py` — Auth and role-based access tests.
  - `tests/test_cart_scoping.py` — Ensures customer endpoints are scoped to `current_user.id`.
  - `tests/test_order_lifecycle.py` — Ensures correct order status transitions.
  - `tests/test_payment_and_shipping_idempotency.py` — Prevents duplicate payment/shipping updates.
  - `tests/conftest.py` — Pytest fixtures (TestClient, test DB setup).
  - `api/handlers.py` — Global exception handlers and response formatting.
  - `api/schemas/common.py` — Shared response/envelope schemas and error payload types.
  - `core/security.py` — Helpers for authorization checks / token verification utilities if needed.

- **Existing files to be modified**
  - `main.py`
    - Register global exception handlers from `api/handlers.py`.
    - Optionally tighten exception/timeout responses to match standardized envelope.
  - `api/routes/*`
    - Replace ad-hoc response formatting with standardized response wrapper.
    - Ensure every route calls the correct scoped service methods (ownership enforcement).
  - `api/dependencies.py`
    - Keep dependency logic but ensure it’s used everywhere.
    - Optionally add a generic `require_scoped_resource` helper pattern.
  - `services/*_service.py`
    - Add/adjust business-rule checks:
      - cart item ownership
      - order status transitions
      - payment idempotency
      - shipping creation/update constraints
    - Ensure service-layer queries always scope by user/order id.
  - `schemas/*`
    - Tighten validators (quantity/price constraints, required fields).

- **Configuration file updates**
  - Add/modify `pyproject.toml` or dependency files if missing (pytest, httpx, python-dotenv, fastapi test deps).

[Functions]
Modify service-layer functions to enforce ownership and lifecycle state transitions; update routes to rely on those functions and standardized response handling.

Detailed breakdown:

- **New functions**
  - `services/orders_service.py::transition_order_status(order: Orders, new_status: OrderStatus, by_user: User, db: Session) -> Orders`
    - Implements allowed transitions and updates timestamps.
  - `services/payments_service.py::apply_payment(order: Orders, payment_payload: ..., db: Session) -> Payments`
    - Enforces idempotency: repeated calls for the same order/payment attempt do not duplicate side effects.
  - `services/shipping_service.py::create_or_update_shipping(order: Orders, shipping_payload: ..., db: Session) -> Shipping`
    - Ensures shipping can’t be modified in invalid order states.

- **Modified functions**
  - For each customer-scoped endpoint in:
    - `services/cart_items_service.py`
    - `services/carts_service.py`
    - `services/orders_service.py`
    - `services/payments_service.py`
    - `services/shipping_service.py`
  - Required changes for each function:
    - Ensure queries include `user_id == current_user.id` or equivalent scoping.
    - Return `None` (or raise a domain exception) when scope mismatches; routes convert this to `404`.

- **Removed functions**
  - None planned (unless the repo already has duplicate/unscoped variants that must be replaced).

[Classes]
No major new ORM classes; extend/introduce Pydantic schemas and lifecycle-state handling helpers.

Detailed breakdown:

- **New classes**
  - Pydantic response wrapper models in `api/schemas/common.py`.
  - Optional domain exceptions in `services/*` or `api/handlers.py` if chosen.

- **Modified classes**
  - Existing SQLAlchemy models (if necessary) to add constraints/indexes via SQLAlchemy model definitions.

- **Removed classes**
  - None planned.

[Dependencies]
Add/confirm test dependencies and standardize error handling utilities.

Details of new packages, version changes, and integration requirements:
- Add dev dependencies:
  - `pytest`
  - `pytest-asyncio` (if testing async routes)
  - `httpx` and `fastapi.testclient`
- Ensure compatibility with current FastAPI/SQLAlchemy versions.

[Testing]
Create a focused automated test suite validating auth, authorization scoping, order lifecycle transitions, and idempotency.

Test file requirements, existing test modifications, and validation strategies:
- Add tests under `tests/`:
  - `test_auth_and_roles.py`: registration/login, JWT protected endpoints, admin vs customer checks.
  - `test_cart_scoping.py`: a customer cannot read/modify another customer’s cart/cart_items.
  - `test_order_lifecycle.py`: status transition rules and forbidden transitions.
  - `test_payment_and_shipping_idempotency.py`: repeated payment/shipping calls do not duplicate changes.
- Use a test SQLite DB (in-memory or temp file) and override `core/database.py` engine/session for tests.

[Implementation Order]
Implement features in an order that minimizes integration conflicts: shared response/error handling first, then service-layer business rules, then route wiring, then tests.

Numbered steps showing logical sequence:
1. Create `api/handlers.py` and `api/schemas/common.py` to define standardized error/success payloads.
2. Update `main.py` to register global exception handlers.
3. Harden Pydantic schemas in `schemas/*` (quantity/price/id constraints).
4. Update customer-scoped services (`carts/cart_items/orders/payments/shipping`) to ensure ownership scoping is always applied.
5. Implement order lifecycle transition function and route to use it.
6. Implement payment application idempotency and shipping creation/update rules.
7. Update routes to rely on new service functions and return standardized envelopes.
8. Add pytest scaffolding (`tests/conftest.py`) and write the core tests.
9. Run tests and iterate on any mismatched expectations.

