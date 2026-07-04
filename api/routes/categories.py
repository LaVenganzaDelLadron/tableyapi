
# Explanation:
# This file is part of the tableyapi backend and contains API route handlers for categories operations.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_admin
from api.responses import bad_request, not_found, success
from schemas.categories import Categories
from services.categories_service import index, store, show, update, destroy


router = APIRouter()

@router.get("/")
async def list_categories(db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = index(db)

    return success("Categories fetched successfully", data)

@router.post("/")
async def create_category(category: Categories, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = store(db, category.name)

    if not data:
        bad_request("Failed to create category")

    return success("Category created successfully", data)

@router.get("/{category_id}")
async def get_category(category_id: int, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = show(db, category_id)

    if not data:
        not_found("Category not found")

    return success("Category fetched successfully", data)

@router.put("/{category_id}")
async def update_category(category_id: int, category: Categories, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = update(db, category_id, category.name)

    if not data:
        not_found("Category not found")

    return success("Category updated successfully", data)

@router.delete("/{category_id}")
async def delete_category(category_id: int, db: Session = Depends(get_db), current_user=Depends(require_admin)):
    data = destroy(db, category_id)

    if not data:
        not_found("Category not found")

    return success("Category deleted successfully", data)