import asyncio
from types import SimpleNamespace

from api.routes import auth as auth_route
import services.auth_service as auth_service


def test_login_returns_serializable_user_payload(monkeypatch):
    monkeypatch.setattr(auth_route, "login_service", lambda db, email, password: SimpleNamespace(
        id=1,
        email="demo@example.com",
        fullname="Demo User",
        username="demo",
        role="admin",
        password="hashed",
    ))
    monkeypatch.setattr(auth_route, "create_access_token", lambda user_id, username, role, expires_minutes=60: "test-token")

    payload = asyncio.run(auth_route.login(SimpleNamespace(email="demo@example.com", password="secret"), db=None))

    data = payload.model_dump()
    assert data["message"] == "Login successful"
    assert data["data"]["session"] == "test-token"
    assert data["data"]["user"]["email"] == "demo@example.com"
    assert data["data"]["user"]["role"] == "admin"
    assert data["error"] is None
