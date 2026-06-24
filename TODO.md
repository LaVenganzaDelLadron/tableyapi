# TODO - Order lifecycle, response envelope, and request validation

- [x] Introduce Pydantic order status enum (PENDING/PAID/SHIPPED/COMPLETED/CANCELLED) and align with model stored values

- [x] Enforce constrained order status updates via a dedicated service function (transition rules)


- [x] Standardize API responses via Pydantic response envelope (`message`, `data`, `error`)


- [ ] Update request validation schemas:


  - [x] cart_items: quantity >= 1, product_id/cart_id > 0

  - [x] order_items: quantity >= 1, product_id/order_id > 0

  - [x] order_items: price >= 0

- [ ] Update routes/services to use the response envelope and status transition service where appropriate
- [ ] Run a quick sanity check (python -m compileall / run server) to ensure no syntax/import issues

