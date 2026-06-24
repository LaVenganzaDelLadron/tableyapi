from conftest import auth_headers


def test_register_login_and_me(client):
    register_response = client.post(
        "/auth/register",
        json={
            "email": "new@example.com",
            "full_name": "New User",
            "username": "newuser",
            "password": "password123",
        },
    )
    assert register_response.status_code == 200
    assert register_response.json()["error"] is None

    login_response = client.post(
        "/auth/login",
        json={"email": "new@example.com", "password": "password123"},
    )
    assert login_response.status_code == 200
    token = login_response.json()["data"]["session"]

    me_response = client.get("/auth/me", headers={"Authorization": f"Bearer {token}"})
    assert me_response.status_code == 200
    assert me_response.json()["data"]["email"] == "new@example.com"


def test_role_guards(client, customer, admin):
    customer_admin_response = client.get("/admin/customers", headers=auth_headers(customer))
    assert customer_admin_response.status_code == 403
    assert customer_admin_response.json()["error"]["code"] == "FORBIDDEN"

    admin_customer_response = client.get("/carts/", headers=auth_headers(admin))
    assert admin_customer_response.status_code == 403
    assert admin_customer_response.json()["error"]["code"] == "FORBIDDEN"
