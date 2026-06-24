from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_customer
from api.responses import bad_request, not_found, success
from schemas.informations import Informations
from services.informations_service import destroy_for_user, index_by_user, show_for_user, store, update_for_user


router = APIRouter()

@router.get("/")
async def list_information(db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = index_by_user(db, current_user.id)

    return success("Information fetched successfully", data)

@router.post("/")
async def create_information(information: Informations, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = store(
        db,
        current_user.id,
        information.phone,
        information.address,
        information.city,
        information.province,
        information.street,
        information.postal_code,
    )

    if not data:
        bad_request("Failed to create information")

    return success("Information created successfully", data)

@router.get("/{information_id}")
async def get_information(information_id: int, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = show_for_user(db, information_id, current_user.id)

    if not data:
        not_found("Information not found")

    return success("Information fetched successfully", data)

@router.put("/{information_id}")
async def update_information(information_id: int, information: Informations, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = update_for_user(
        db,
        information_id,
        current_user.id,
        information.phone,
        information.address,
        information.city,
        information.province,
        information.street,
        information.postal_code,
    )

    if not data:
        not_found("Information not found")

    return success("Information updated successfully", data)

@router.delete("/{information_id}")
async def delete_information(information_id: int, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = destroy_for_user(db, information_id, current_user.id)

    if not data:
        not_found("Information not found")

    return success("Information deleted successfully", data)
