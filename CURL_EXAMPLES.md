# CURL Examples for TableyApi

Base URL: `http://localhost:8000`

## 1. Root - Health Check

```bash
curl -s http://localhost:8000/ | jq
```

**Response:**
```json
{
  "status": "Alive"
}
```

---

## 2. Auth Endpoints (`/auth`)

### 2.1 Register a new user

```bash
curl -s -X POST http://localhost:8000/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "full_name": "Admin User",
    "password": "admin123"
  }' | jq
```

**Response (success):**
```json
{
  "message": "User Registered Successfully",
  "data": null,
  "error": null
}
```

**Response (already exists):**
```json
{
  "detail": "Incorrect email or password"
}
```

### 2.2 Login

```bash
curl -s -X POST http://localhost:8000/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "admin123"
  }' | jq
```

**Response (success):**
```json
{
  "message": "User Logged In Successfully",
  "data": {
    "user": {
      "id": 1,
      "email": "admin@example.com",
      "full_name": "Admin User",
      "password": "$2b$12$...",
      "role": "admin"
    },
    "jwt_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  },
  "error": null
}
```

> **Save the `jwt_token` value** — it is required as a Bearer token for all other endpoints below.

---

## 3. Raw Materials (`/raw_materials`)

> Requires `Authorization: Bearer <token>` header.

### 3.1 List raw materials

```bash
TOKEN="<your-jwt-token-here>"

curl -s -X GET http://localhost:8000/raw_materials/ \
  -H "Authorization: Bearer $TOKEN" | jq
```

**Response (empty):**
```json
{
  "message": "Empty Data",
  "data": null,
  "error": null
}
```

**Response (with data):**
```json
{
  "message": "Successfully fetched data",
  "data": {
    "id": 1,
    "user_id": 1,
    "name": "Cacao Beans",
    "weight": 100.0,
    "unit_price": 150.0,
    "total_price": 15000.0,
    "created_at": "2025-01-01T00:00:00",
    "updated_at": "2025-01-01T00:00:00"
  },
  "error": null
}
```

---

## 4. Production Batches (`/production_batches`)

> Requires `Authorization: Bearer <token>` header.

### 4.1 List production batches

```bash
TOKEN="<your-jwt-token-here>"

curl -s -X GET http://localhost:8000/production_batches/ \
  -H "Authorization: Bearer $TOKEN" | jq
```

**Response (empty):**
```json
{
  "message": "Invalid data",
  "data": null,
  "error": null
}
```

**Response (with data):**
```json
{
  "message": "Successfully fetched data",
  "data": {
    "id": 1,
    "raw_material_id": 1,
    "roast_date": "2025-01-15",
    "milled_weight": 50.0,
    "package_pieces": 200,
    "production_cost": 5000.0,
    "created_at": "2025-01-15T00:00:00",
    "updated_at": "2025-01-15T00:00:00"
  },
  "error": null
}
```

---

## 5. Products (`/products`)

> Requires `Authorization: Bearer <token>` header.

### 5.1 List products

```bash
TOKEN="<your-jwt-token-here>"

curl -s -X GET http://localhost:8000/products/ \
  -H "Authorization: Bearer $TOKEN" | jq
```

**Response (empty):**
```json
{
  "message": "Empty data",
  "data": null,
  "error": null
}
```

**Response (with data):**
```json
{
  "message": "Successfully fetched data",
  "data": {
    "id": 1,
    "production_batches_id": 1,
    "name": "Dark Chocolate Bar",
    "selling_price": 99.0,
    "current_stock": 500,
    "created_at": "2025-01-20T00:00:00",
    "updated_at": "2025-01-20T00:00:00"
  },
  "error": null
}
```

---

## 6. Sales (`/sales`)

> Requires `Authorization: Bearer <token>` header.

### 6.1 List sales

```bash
TOKEN="<your-jwt-token-here>"

curl -s -X GET http://localhost:8000/sales/ \
  -H "Authorization: Bearer $TOKEN" | jq
```

**Response (empty):**
```json
{
  "message": "Empty Data",
  "data": null,
  "error": null
}
```

**Response (with data):**
```json
{
  "message": "Successfully fetched data",
  "data": {
    "id": 1,
    "product_id": 1,
    "sales_date": "2025-01-25",
    "total_amount": 1980.0,
    "created_at": "2025-01-25T00:00:00",
    "updated_at": "2025-01-25T00:00:00"
  },
  "error": null
}
```

---

## 7. Sale Items (`/sale_items`)

> Requires `Authorization: Bearer <token>` header.

### 7.1 List sale items

```bash
TOKEN="<your-jwt-token-here>"

curl -s -X GET http://localhost:8000/sale_items/ \
  -H "Authorization: Bearer $TOKEN" | jq
```

**Response (empty):**
```json
{
  "message": "Empty Data",
  "data": null,
  "error": null
}
```

**Response (with data):**
```json
{
  "message": "Successfully fetched data",
  "data": {
    "id": 1,
    "sale_id": 1,
    "quantity": 20,
    "unit_price": 99.0,
    "subtotal": 1980.0,
    "created_at": "2025-01-25T00:00:00",
    "updated_at": "2025-01-25T00:00:00"
  },
  "error": null
}
```

---

## Complete Workflow Example

Here is a full end-to-end workflow using a single terminal session:

```bash
# 1. Register a user
curl -s -X POST http://localhost:8000/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "full_name": "Test User", "password": "test123"}' | jq

# 2. Login and extract the token
TOKEN=$(curl -s -X POST http://localhost:8000/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "password": "test123"}' | jq -r '.data.jwt_token')

echo "TOKEN: $TOKEN"

# 3. Check raw materials (empty initially)
curl -s http://localhost:8000/raw_materials/ \
  -H "Authorization: Bearer $TOKEN" | jq

# 4. Check production batches (empty initially)
curl -s http://localhost:8000/production_batches/ \
  -H "Authorization: Bearer $TOKEN" | jq

# 5. Check products (empty initially)
curl -s http://localhost:8000/products/ \
  -H "Authorization: Bearer $TOKEN" | jq

# 6. Check sales (empty initially)
curl -s http://localhost:8000/sales/ \
  -H "Authorization: Bearer $TOKEN" | jq

# 7. Check sale items (empty initially)
curl -s http://localhost:8000/sale_items/ \
  -H "Authorization: Bearer $TOKEN" | jq
```

---

## Notes

- All endpoints except `GET /`, `POST /auth/register`, and `POST /auth/login` require JWT authentication via `Authorization: Bearer <token>` header.
- The `jq` command is used for pretty-printing JSON. Install it via `sudo apt install jq` (Linux) or `brew install jq` (macOS). If not available, omit `| jq` to see raw JSON output.
- The default server runs on `http://localhost:8000`. Adjust the base URL if using a different host/port.
- Empty responses return `"data": null` with a message like `"Empty Data"`.
- The `GET /` root endpoint does not require authentication.

