from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_customer
from api.responses import not_found, success
from schemas.customer_addresses import CustomerAddress, CustomerAddressUpdate
from services.customer_addresses_service import destroy_for_user, index_by_user, store, update_for_user


router = APIRouter()


@router.get("/")
async def list_addresses(db: Session = Depends(get_db), current_user=Depends(require_customer)):
    return success("Addresses fetched successfully", index_by_user(db, current_user.id))


@router.post("/")
async def create_address(address: CustomerAddress, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    return success("Address created successfully", store(db, current_user.id, address))


@router.put("/{address_id}")
async def update_address(address_id: int, address: CustomerAddressUpdate, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = update_for_user(db, address_id, current_user.id, address)
    if not data:
        not_found("Address not found")
    return success("Address updated successfully", data)


@router.delete("/{address_id}")
async def delete_address(address_id: int, db: Session = Depends(get_db), current_user=Depends(require_customer)):
    data = destroy_for_user(db, address_id, current_user.id)
    if not data:
        not_found("Address not found")
    return success("Address deleted successfully", data)
