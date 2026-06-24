import os
import sys
from pathlib import Path

import pytest
from fastapi.testclient import TestClient
from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker

ROOT = Path(__file__).resolve().parents[1]
if str(ROOT) not in sys.path:
    sys.path.insert(0, str(ROOT))

os.environ.setdefault("JWT_SECRET_KEY", "test-secret")

from api.dependencies import get_db
from core.database import Base
from main import app
from models.cart_items import CartItems
from models.carts import Carts
from models.categories import Categories
from models.informations import Informations
from models.order_items import OrderItems
from models.orders import Orders, OrderStatus
from models.payments import Payments
from models.products import Products
from models.shipping import Shipping
from models.users import User, UserRole
from services.auth_service import create_access_token, hash_password


SQLALCHEMY_DATABASE_URL = "sqlite:///:memory:"

engine = create_engine(
    SQLALCHEMY_DATABASE_URL,
    connect_args={"check_same_thread": False},
    poolclass=__import__("sqlalchemy.pool").pool.StaticPool,
)
TestingSessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)


@pytest.fixture()
def db():
    Base.metadata.create_all(bind=engine)
    session = TestingSessionLocal()
    try:
        yield session
    finally:
        session.close()
        Base.metadata.drop_all(bind=engine)


@pytest.fixture()
def client(db):
    def override_get_db():
        try:
            yield db
        finally:
            pass

    app.dependency_overrides[get_db] = override_get_db
    with TestClient(app) as test_client:
        yield test_client
    app.dependency_overrides.clear()


def create_user(db, email: str, username: str, role: UserRole = UserRole.CUSTOMER) -> User:
    user = User(
        email=email,
        fullname=username.title(),
        username=username,
        password=hash_password("password123"),
        role=role,
    )
    db.add(user)
    db.commit()
    db.refresh(user)
    return user


def auth_headers(user: User) -> dict[str, str]:
    token = create_access_token(user.id, user.username, user.role)
    return {"Authorization": f"Bearer {token}"}


@pytest.fixture()
def customer(db):
    return create_user(db, "customer@example.com", "customer", UserRole.CUSTOMER)


@pytest.fixture()
def other_customer(db):
    return create_user(db, "other@example.com", "other", UserRole.CUSTOMER)


@pytest.fixture()
def admin(db):
    return create_user(db, "admin@example.com", "admin", UserRole.ADMIN)


@pytest.fixture()
def product(db):
    category = Categories(name="Drinks")
    db.add(category)
    db.commit()
    db.refresh(category)

    product = Products(
        category_id=category.id,
        name="Cacao",
        description="Hot cacao",
        price=99.0,
        stock=10,
        status="active",
    )
    db.add(product)
    db.commit()
    db.refresh(product)
    return product


def create_order(db, user: User, status: str = OrderStatus.PENDING.value) -> Orders:
    order = Orders(
        user_id=user.id,
        total_amount=99.0,
        payment_method="gcash",
        status=status,
    )
    db.add(order)
    db.commit()
    db.refresh(order)
    return order
