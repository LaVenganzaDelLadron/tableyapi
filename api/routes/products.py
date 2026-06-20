from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import bad_request, not_found, success
from schemas.products import Products
from services.products_service import index, store, show, update, destroy


router = APIRouter()

@router.get("/")
async def list_products(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = index(db, current_user.id)

    return success("Products fetched successfully", data)

@router.post("/")
async def create_product(product: Products, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = store(db, product, current_user.id)

    if not data:
        bad_request("Failed to create product")

    return success("Product created successfully", data)

@router.get("/{product_id}")
async def get_product(product_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = show(db, product_id, current_user.id)

    if not data:
        not_found("Product not found")

    return success("Product fetched successfully", data)

@router.put("/{product_id}")
async def update_product(product_id: int, product: Products, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = update(db, product_id, product, current_user.id)

    if not data:
        not_found("Product not found")

    return success("Product updated successfully", data)

@router.delete("/{product_id}")
async def delete_product(product_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = destroy(db, product_id, current_user.id)

    if not data:
        not_found("Product not found")

    return success("Product deleted successfully", data)
