from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_admin
from api.responses import bad_request, not_found, success
from schemas.products import Products
from services.products_service import index, store, show, update, destroy


router = APIRouter()

@router.get("/")
async def list_products(search: str | None = None, db: Session = Depends(get_db)):
    data = index(db, search)

    return success("Products fetched successfully", data)

@router.post("/")
async def create_product(product: Products, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = store(db, product.category_id, product.name, product.description, product.price, product.stock, product.image, product.status)

    if not data:
        bad_request("Failed to create product")

    return success("Product created successfully", data)

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
