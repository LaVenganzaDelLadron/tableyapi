from types import SimpleNamespace

from fastapi import FastAPI
from fastapi.testclient import TestClient

import api.dependencies as api_dependencies
import api.routes.earnings as earnings_route
from api.routes.earnings import router as earnings_router


def test_earnings_list_allows_authenticated_customer(monkeypatch):
    app = FastAPI()
    app.include_router(earnings_router, prefix="/earnings")

    def fake_current_user():
        return SimpleNamespace(role="customer")

    def fake_index(db):
        return [{"id": 1, "description": "Consulting"}]

    monkeypatch.setattr(earnings_route, "index", fake_index)
    app.dependency_overrides[api_dependencies.get_db] = lambda: None
    app.dependency_overrides[earnings_route.get_current_user] = fake_current_user

    client = TestClient(app)
    response = client.get("/earnings/")

    assert response.status_code == 200
    assert response.json()["data"][0]["description"] == "Consulting"
