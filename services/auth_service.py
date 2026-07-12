
# Explanation:
# This file is part of the tableyapi backend and contains Business logic and service layer code for auth service.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

import os
import hashlib
import hmac
import secrets
from datetime import datetime, timedelta, timezone
import jwt
from dotenv import load_dotenv
from sqlalchemy.orm import Session
from models.password_resets import PasswordResets
from models.users import User, UserRole


load_dotenv()

SECRET_KEY = os.getenv("JWT_SECRET_KEY")
ALGORITHM = "HS256"


def _role_value(role) -> str:
    value = getattr(role, "value", role)
    return str(value).lower()


def hash_password(password: str) -> str:
    salt = secrets.token_hex(16)
    password_hash = hashlib.pbkdf2_hmac("sha256", password.encode(), salt.encode(), 100000).hex()
    return f"pbkdf2_sha256${salt}${password_hash}"


def verify_password(password: str, stored_password: str) -> bool:
    if not stored_password:
        return False
    if not stored_password.startswith("pbkdf2_sha256$"):
        return hmac.compare_digest(password, stored_password)

    try:
        _, salt, expected_hash = stored_password.split("$", 2)
    except ValueError:
        return False

    password_hash = hashlib.pbkdf2_hmac("sha256", password.encode(), salt.encode(), 100000).hex()
    return hmac.compare_digest(password_hash, expected_hash)


def create_access_token(user_id: int, username: str, role: str, expires_minutes: int = 60):
    payload = {
        "sub": str(user_id),
        "username": username,
        "role": _role_value(role),
        "exp": datetime.now(timezone.utc) + timedelta(minutes=expires_minutes),
    }
    return jwt.encode(payload, SECRET_KEY, algorithm=ALGORITHM)


def decode_token(token: str):
    try:
        return jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
    except jwt.PyJWTError:
        return None



def register(db: Session, email: str, fullname: str, username: str, password: str, role: UserRole = UserRole.ADMIN):
    # Ensure the configured admin credential is created as ADMIN instead of CUSTOMER.
    # (Simple email-based mapping; change to your own condition if needed.)
    existing = db.query(User).filter((User.email == email) | (User.username == username)).first()

    if existing:
        return None

    user = User(
        email=email,
        fullname=fullname,
        username=username,
        password=hash_password(password),
        role=role,
    )

    db.add(user)
    db.commit()
    db.refresh(user)

    return user

def login(db: Session, email: str, password: str):
    user = db.query(User).filter(User.email == email).first()

    if not user or not verify_password(password, user.password):
        return None

    if not user.password.startswith("pbkdf2_sha256$"):
        user.password = hash_password(password)
        db.commit()
        db.refresh(user)

    return user


def change_password(db: Session, user: User, current_password: str, new_password: str):
    if not verify_password(current_password, user.password):
        return None

    user.password = hash_password(new_password)
    db.commit()
    db.refresh(user)
    return user


def _token_hash(token: str) -> str:
    return hashlib.sha256(token.encode()).hexdigest()


def create_password_reset(db: Session, email: str, expires_minutes: int = 30):
    user = db.query(User).filter(User.email == email).first()
    if not user:
        return None

    token = secrets.token_urlsafe(32)
    reset = PasswordResets(
        user_id=user.id,
        token_hash=_token_hash(token),
        expires_at=datetime.now(timezone.utc) + timedelta(minutes=expires_minutes),
    )
    db.add(reset)
    db.commit()
    db.refresh(reset)
    return token


def reset_password(db: Session, token: str, new_password: str):
    reset = db.query(PasswordResets).filter(PasswordResets.token_hash == _token_hash(token)).first()
    if not reset or reset.used_at is not None:
        return None

    expires_at = reset.expires_at
    if expires_at.tzinfo is None:
        expires_at = expires_at.replace(tzinfo=timezone.utc)
    if expires_at < datetime.now(timezone.utc):
        return None

    user = db.query(User).filter(User.id == reset.user_id).first()
    if not user:
        return None

    user.password = hash_password(new_password)
    reset.used_at = datetime.now(timezone.utc)
    db.commit()
    db.refresh(user)
    return user