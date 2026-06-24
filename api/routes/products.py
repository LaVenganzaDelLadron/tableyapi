import base64
import binascii
import os
import re
import secrets
from pathlib import Path

from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_admin
from api.responses import bad_request, not_found, success
from schemas.products import ProductImageUpload, Products
from services.products_service import index, store, show, update, destroy


router = APIRouter()

@router.get("/")
async def list_products(
    search: str | None = None,
    page: int | None = None,
    limit: int | None = None,
    db: Session = Depends(get_db),
):
    data = index(db, search, page, limit, public_only=True)

    return success("Products fetched successfully", data)

@router.post("/")
async def create_product(product: Products, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = store(db, product.category_id, product.name, product.description, product.price, product.stock, product.image, product.status)

    if not data:
        bad_request("Failed to create product")

    return success("Product created successfully", data)


@router.post("/images")
async def upload_product_image(payload: ProductImageUpload, current_user=Depends(require_admin)):
    suffix = Path(payload.filename).suffix.lower()
    if suffix not in {".jpg", ".jpeg", ".png", ".webp", ".gif"}:
        bad_request("Unsupported image type")

    try:
        content = base64.b64decode(payload.content_base64, validate=True)
    except (binascii.Error, ValueError):
        bad_request("Invalid image content")

    if len(content) > 5 * 1024 * 1024:
        bad_request("Image exceeds 5MB limit")

    storage_dir = Path("storage/product-images")
    storage_dir.mkdir(parents=True, exist_ok=True)
    safe_name = re.sub(r"[^a-zA-Z0-9_.-]", "-", Path(payload.filename).stem)[:50] or "product"
    filename = f"{safe_name}-{secrets.token_hex(8)}{suffix}"
    path = storage_dir / filename
    path.write_bytes(content)

    return success("Product image uploaded successfully", {"image": str(path)})

@router.get("/{product_id}")
async def get_product(product_id: int, db: Session = Depends(get_db)):
    data = show(db, product_id)

    if not data:
        not_found("Product not found")

    return success("Product fetched successfully", data)

@router.put("/{product_id}")
async def update_product(product_id: int, product: Products, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = update(db, product_id, product.category_id, product.name, product.description, product.price, product.stock, product.image, product.status)

    if not data:
        not_found("Product not found")

    return success("Product updated successfully", data)

@router.delete("/{product_id}")
async def delete_product(product_id: int, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = destroy(db, product_id)

    if not data:
        not_found("Product not found")

    return success("Product deleted successfully", data)
