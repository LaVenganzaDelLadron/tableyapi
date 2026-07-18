import os
import bcrypt
import jwt
from sqlalchemy.orm import Session
from models.users_model import  User, Role
from dotenv import load_dotenv
from datetime import datetime, timedelta, timezone

load_dotenv()
SECRET_KEY = os.getenv("JWT_SECRET_KEY")
ALGORITHM = os.getenv("JWT_ALGORITHM")


def role_value(role) -> str:
    value = getattr(role, "value", role)
    return str(value).lower()

def hash_password(password: str) -> str:
    return bcrypt.hashpw(password.encode("utf-8"), bcrypt.gensalt())

def verify_password(password: str, stored_hash: str) -> bool:
    try:
        return bcrypt.checkpw(password.encode("utf-8"), stored_hash)
    except Exception as e:
        print(f"An error occurred: {e}")
        return False

def create_access_token(user_id: int, role: str, expires_minutes: int = 120):
    payload = {
        "sub": str(user_id),
        "role": role_value(role),
        "exp": datetime.now(timezone.utc) + timedelta(minutes=expires_minutes),
    }
    return jwt.encode(payload, SECRET_KEY, algorithm=ALGORITHM)


def decode_token(token: str):
    try:
        return jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
    except jwt.PyJWTError:
        return None

def register(db: Session, email: str, fullname: str, password: str, role: Role = Role.ADMIN):
    print("[+] Register Account")
    # Check the account if already exists
    is_exist = db.query(User).filter((User.email == email) | (User.fullname == fullname)).first()

    if is_exist:
        print("[+] Already Registered")
        return None

    data = User(email = email, fullname = fullname, password = hash_password(password), role = role)
    db.add(data)
    db.commit()
    db.refresh(data)

    return data

def login(db: Session, email: str, password: str):
    data = db.query(User).filter(User.email == email).first()

    if not data:
        print("[-] Invalid Username or Password")
        return None

    try:
        if verify_password(password, data.password):
            # Generate JWT TOKEN upon successful login
            token = create_access_token(data.id, data.role)
            return token
        else:
            print("[-] Invalid Username or Password")
            return None
    except Exception as e:
        print(f"Error verifying a password: {e}")
        return None