from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from starlette import status

from api.dependencies import get_current_user, get_db
from schemas.users import ChangePassword, CreateUser, LoginUser, UpdateProfile
from services.auth_service import change_password as change_password_service
from services.auth_service import create_access_token, login as login_service
from services.auth_service import register as register_service
from services.users_service import update_profile


router = APIRouter()


@router.post("/register")
async def register(user: CreateUser, db: Session = Depends(get_db)):
    result = register_service(db, user.email, user.full_name, user.username, user.password)

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

    token = create_access_token(result.id, result.username, result.role)

    return {
        "message": "Login successful",
        "data": result,
        "session": token,
    }


@router.post("/logout")
async def logout():
    return {"message": "Logout successful"}


@router.get("/me")
async def me(current_user=Depends(get_current_user)):
    return {
        "id": current_user.id,
        "email": current_user.email,
        "full_name": current_user.fullname,
        "username": current_user.username,
        "role": getattr(current_user.role, "value", current_user.role),
    }


@router.put("/me")
async def update_me(profile: UpdateProfile, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    user = update_profile(db, current_user, profile.email, profile.full_name, profile.username)
    return {
        "message": "Profile updated successfully",
        "data": {
            "id": user.id,
            "email": user.email,
            "full_name": user.fullname,
            "username": user.username,
        },
    }


@router.post("/change-password")
async def change_password(payload: ChangePassword, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    user = change_password_service(db, current_user, payload.current_password, payload.new_password)
    if not user:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Current password is incorrect")

    return {"message": "Password changed successfully"}








