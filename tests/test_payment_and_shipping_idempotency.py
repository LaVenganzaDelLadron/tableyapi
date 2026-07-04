
# Explanation:
# This file is part of the tableyapi backend and contains Regression and behavior tests covering test payment and shipping idempotency.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from datetime import datetime, timezone

from conftest import auth_headers, create_order
from models.orders import OrderStatus
from models.payments import Payments
from models.shipping import Shipping


def test_payment_idempotency_and_paid_transition(client, db, customer):
    order = create_order(db, customer, OrderStatus.PENDING.value)
    payload = {
        "order_id": order.id,
        "amount": 99.0,
        "payment_method": "gcash",
        "payment_status": "completed",
        "transaction_id": "txn-1",
    }

    first = client.post("/payments/", headers=auth_headers(customer), json=payload)
    second = client.post("/payments/", headers=auth_headers(customer), json=payload)

    assert first.status_code == 200
    assert second.status_code == 200
    assert first.json()["data"]["id"] == second.json()["data"]["id"]
    assert db.query(Payments).filter(Payments.order_id == order.id).count() == 1
    db.refresh(order)
    assert order.status == OrderStatus.PAID.value


def test_customer_cannot_pay_for_another_customers_order(client, db, customer, other_customer):
    order = create_order(db, other_customer, OrderStatus.PENDING.value)

    response = client.post(
        "/payments/",
        headers=auth_headers(customer),
        json={
            "order_id": order.id,
            "amount": 99.0,
            "payment_method": "gcash",
            "payment_status": "completed",
            "transaction_id": "txn-other",
        },
    )

    assert response.status_code == 404


def test_shipping_obeys_order_state_and_transitions(client, db, admin, customer):
    pending_order = create_order(db, customer, OrderStatus.PENDING.value)
    invalid = client.post(
        "/shipping/",
        headers=auth_headers(admin),
        json={
            "order_id": pending_order.id,
            "courier_id": "courier-a",
            "tracking_number": "track-a",
            "shipping_fee": 10.0,
        },
    )
    assert invalid.status_code == 400

    paid_order = create_order(db, customer, OrderStatus.PAID.value)
    shipped_at = datetime.now(timezone.utc).isoformat()
    valid = client.post(
        "/shipping/",
        headers=auth_headers(admin),
        json={
            "order_id": paid_order.id,
            "courier_id": "courier-a",
            "tracking_number": "track-b",
            "shipping_fee": 10.0,
            "shipped_at": shipped_at,
        },
    )
    assert valid.status_code == 200
    assert db.query(Shipping).filter(Shipping.order_id == paid_order.id).count() == 1
    db.refresh(paid_order)
    assert paid_order.status == OrderStatus.SHIPPED.value