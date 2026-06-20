from fastapi import APIRouter
from pydantic import BaseModel

router = APIRouter(tags=["v1"])


class UserIn(BaseModel):
    name: str
    email: str


class UserOut(BaseModel):
    id: int
    name: str
    email: str


sample_users = [
    {"id": 1, "name": "Alice", "email": "alice@example.com"},
    {"id": 2, "name": "Bob", "email": "bob@example.com"},
]


@router.get("/health")
def health_check() -> dict[str, str]:
    return {"status": "ok", "service": "tabley-api"}


@router.get("/users", response_model=list[UserOut])
def list_users() -> list[UserOut]:
    return [UserOut(**user) for user in sample_users]


@router.post("/users", response_model=UserOut)
def create_user(payload: UserIn) -> UserOut:
    new_user = {
        "id": len(sample_users) + 1,
        "name": payload.name,
        "email": payload.email,
    }
    sample_users.append(new_user)
    return UserOut(**new_user)
