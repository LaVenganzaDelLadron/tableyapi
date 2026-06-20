from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from starlette import status

from api.dependencies import get_db
from schemas.users import CreateUser, LoginUser
from services.auth_service import create_access_token, login as login_service
from services.auth_service import register as register_service


router = APIRouter()


@router.post("/register")
async def register(user: CreateUser, db: Session = Depends(get_db)):
    result = register_service(db, user.email, user.full_name, user.username, user.password, user.role)

    if not result:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="User already exists")

    return {
        "message": "User registered successfully",
    }


@router.post("/login")
async def login(user: LoginUser, db: Session = Depends(get_db)):
    result = login_service(db, user.email, user.password)

    if not result:
        raise HTTPException(status_code=status.HTTP_401_UNAUTHORIZED, detail="Invalid email or password")

    token = create_access_token({result.id, result.username})

    return {
        "message": "Login successful",
        "session": token,
    }









