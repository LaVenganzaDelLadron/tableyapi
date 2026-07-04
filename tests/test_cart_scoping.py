
# Explanation:
# This file is part of the tableyapi backend and contains Regression and behavior tests covering test cart scoping.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from conftest import auth_headers
from models.cart_items import CartItems
from models.carts import Carts


def test_customer_cannot_access_another_customers_cart_item(client, db, customer, other_customer, product):
    cart = Carts(user_id=other_customer.id)
    db.add(cart)
    db.commit()
    db.refresh(cart)

    item = CartItems(cart_id=cart.id, product_id=product.id, quantity=1)
    db.add(item)
    db.commit()
    db.refresh(item)

    headers = auth_headers(customer)
    assert client.get(f"/cart-items/{item.id}", headers=headers).status_code == 404
    assert client.put(
        f"/cart-items/{item.id}",
        headers=headers,
        json={"product_id": product.id, "quantity": 2},
    ).status_code == 404
    assert client.delete(f"/cart-items/{item.id}", headers=headers).status_code == 404


def test_customer_can_modify_own_cart_item(client, db, customer, product):
    cart = Carts(user_id=customer.id)
    db.add(cart)
    db.commit()
    db.refresh(cart)

    item = CartItems(cart_id=cart.id, product_id=product.id, quantity=1)
    db.add(item)
    db.commit()
    db.refresh(item)

    response = client.put(
        f"/cart-items/{item.id}",
        headers=auth_headers(customer),
        json={"product_id": product.id, "quantity": 3},
    )

    assert response.status_code == 200
    assert response.json()["data"]["quantity"] == 3