from conftest import auth_headers, create_order
from models.orders import OrderStatus


def test_allowed_order_status_transition(client, admin, customer, db):
    order = create_order(db, customer, OrderStatus.PENDING.value)

    response = client.patch(
        f"/admin/orders/{order.id}/status",
        headers=auth_headers(admin),
        json={"status": "PAID"},
    )

    assert response.status_code == 200
    assert response.json()["data"]["status"] == "PAID"


def test_forbidden_order_status_transition_returns_400(client, admin, customer, db):
    order = create_order(db, customer, OrderStatus.PENDING.value)

    response = client.patch(
        f"/admin/orders/{order.id}/status",
        headers=auth_headers(admin),
        json={"status": "COMPLETED"},
    )

    assert response.status_code == 400
    assert response.json()["error"]["code"] == "BAD_REQUEST"


def test_terminal_order_status_cannot_move(client, admin, customer, db):
    order = create_order(db, customer, OrderStatus.COMPLETED.value)

    response = client.patch(
        f"/admin/orders/{order.id}/status",
        headers=auth_headers(admin),
        json={"status": "CANCELLED"},
    )

    assert response.status_code == 400
