from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from starlette import status
from sqlalchemy.exc import OperationalError

from api.dependencies import get_db
from api.responses import success
from api.alerts import ApiAlert
from schemas.users_schema import RegisterUser, LoginUser
from services.auth_service import register as register_service, create_access_token
from services.auth_service import login as login_service


router = APIRouter()

@router.post("/register", response_model=ApiAlert)
async def register(user: RegisterUser, db: Session = Depends(get_db)):
    try:
        data = register_service(db, user.email, user.full_name, user.password)
    except OperationalError:
        raise HTTPException(status_code=status.HTTP_503_SERVICE_UNAVAILABLE, detail="Database connection error")
    if not data:
        # register_service returns None when the user already exists
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="User already registered")

    token = create_access_token(data.id, data.role)

    return success(
        "User Registered Successfully",
        {
            "user": {
                "id": data.id,
                "email": data.email,
                "full_name": data.fullname,
                "role": getattr(data.role, "value", data.role),
            },
            "jwt_token": token,
        },
    )

@router.post("/login", response_model=ApiAlert)
async def login(user: LoginUser, db: Session = Depends(get_db)):
    result = login_service(db, user.email, user.password)

    if not result:
        raise HTTPException(status_code=400, detail="Incorrect email or password")

    token = create_access_token(result.id, result.role)
    return success(
        "User Logged In Successfully",
        {
            "user": {
                "id": result.id,
                "email": result.email,
                "full_name": result.fullname,
                "role": getattr(result.role, "value", result.role),
            },
            "jwt_token": token,
        },
    )