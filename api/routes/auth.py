from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from starlette import status

from api.dependencies import get_current_user, get_db
from api.responses import success
from schemas.users_schema import RegisterUser, LoginUser
from services.auth_service import register as register_service, create_access_token
from services.auth_service import login as login_service


router = APIRouter()

@router.post("/register", response_model=RegisterUser)
async def register(user: RegisterUser, db: Session = Depends(get_current_user)):
    data = register_service(db, user.email, user.full_name, user.password)
    if not data:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Incorrect email or password")

    return success("User Registered Successfully")

@router.post("/login", response_model=LoginUser)
async def login(user: LoginUser, db: Session = Depends(get_db)):
    result = login_service(db, user.email, user.password)

    if not result:
        raise HTTPException(status_code=400, detail="Incorrect email or password")

    token = create_access_token(result.id, result.role)
    return success(
        "User Logged In Successfully",
        {
            "user":{
                "id": result.id,
                "email": result.email,
                "full_name": result.full_name,
                "password": result.password,
                "role": result.role,
            },
            "jwt_token": token,
        }
    )