from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_current_user, get_db
from api.responses import bad_request, not_found, success
from schemas.categories import Categories
from services.categories_service import index, store, show, update, destroy


router = APIRouter()

@router.get("/")
async def list_categories(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = index(db, current_user.id)

    return success("Categories fetched successfully", data)

@router.post("/")
async def create_category(category: Categories, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = store(db, category, current_user.id)

    if not data:
        bad_request("Failed to create category")

    return success("Category created successfully", data)

@router.get("/{category_id}")
async def get_category(category_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = show(db, category_id, current_user.id)

    if not data:
        not_found("Category not found")

    return success("Category fetched successfully", data)

@router.put("/{category_id}")
async def update_category(category_id: int, category: Categories, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = update(db, category_id, category, current_user.id)

    if not data:
        not_found("Category not found")

    return success("Category updated successfully", data)

@router.delete("/{category_id}")
async def delete_category(category_id: int, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    data = destroy(db, category_id, current_user.id)

    if not data:
        not_found("Category not found")

    return success("Category deleted successfully", data)
