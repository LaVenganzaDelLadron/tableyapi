from fastapi import APIRouter, Depends
from sqlalchemy import func
from sqlalchemy.orm import Session

from api.dependencies import get_db, require_admin
from api.responses import bad_request, not_found, success
from models.orders import OrderStatus, Orders
from models.users import User, UserRole
from schemas.categories import Categories
from schemas.orders import OrderStatusUpdate
from schemas.products import Products
from services.categories_service import destroy as destroy_category
from services.categories_service import index as list_categories
from services.categories_service import show as show_category
from services.categories_service import store as store_category
from services.categories_service import update as update_category_service
from services.orders_service import index as list_orders
from services.orders_service import show as show_order
from services.orders_service import update_status
from services.products_service import destroy as destroy_product
from services.products_service import show as show_product
from services.products_service import store as store_product
from services.products_service import update as update_product_service
from services.users_service import index_customers


router = APIRouter(dependencies=[Depends(require_admin)])


@router.post("/products")
async def admin_create_product(product: Products, db: Session = Depends(get_db)):
    data = store_product(
        db,
        product.category_id,
        product.name,
        product.description,
        product.price,
        product.stock,
        product.image,
        product.status,
    )
    if not data:
        bad_request("Failed to create product")

    return success("Product created successfully", data)


@router.put("/products/{product_id}")
async def admin_update_product(product_id: int, product: Products, db: Session = Depends(get_db)):
    data = update_product_service(
        db,
        product_id,
        product.category_id,
        product.name,
        product.description,
        product.price,
        product.stock,
        product.image,
        product.status,
    )
    if not data:
        not_found("Product not found")

    return success("Product updated successfully", data)


@router.patch("/products/{product_id}/stock")
async def admin_update_product_stock(product_id: int, stock: int, db: Session = Depends(get_db)):
    data = show_product(db, product_id)
    if not data:
        not_found("Product not found")

    product = data
    product.stock = stock
    db.commit()
    db.refresh(product)

    return success("Product stock updated successfully", product)


@router.delete("/products/{product_id}")
async def admin_delete_product(product_id: int, db: Session = Depends(get_db)):
    data = destroy_product(db, product_id)
    if not data:
        not_found("Product not found")

    return success("Product deleted successfully", data)


@router.get("/categories")
async def admin_list_categories(db: Session = Depends(get_db)):
    return success("Categories fetched successfully", list_categories(db))


@router.post("/categories")
async def admin_create_category(category: Categories, db: Session = Depends(get_db)):
    data = store_category(db, category.name)
    if not data:
        bad_request("Failed to create category")

    return success("Category created successfully", data)


@router.get("/categories/{category_id}")
async def admin_get_category(category_id: int, db: Session = Depends(get_db)):
    data = show_category(db, category_id)
    if not data:
        not_found("Category not found")

    return success("Category fetched successfully", data)


@router.put("/categories/{category_id}")
async def admin_update_category(category_id: int, category: Categories, db: Session = Depends(get_db)):
    data = update_category_service(db, category_id, category.name)
    if not data:
        not_found("Category not found")

    return success("Category updated successfully", data)


@router.delete("/categories/{category_id}")
async def admin_delete_category(category_id: int, db: Session = Depends(get_db)):
    data = destroy_category(db, category_id)
    if not data:
        not_found("Category not found")

    return success("Category deleted successfully", data)


@router.get("/customers")
async def admin_list_customers(db: Session = Depends(get_db)):
    data = index_customers(db)
    return success("Customers fetched successfully", data)


@router.get("/orders")
async def admin_list_orders(db: Session = Depends(get_db)):
    return success("Orders fetched successfully", list_orders(db))


@router.get("/orders/{order_id}")
async def admin_get_order(order_id: int, db: Session = Depends(get_db)):
    data = show_order(db, order_id)
    if not data:
        not_found("Order not found")

    return success("Order fetched successfully", data)


@router.patch("/orders/{order_id}/status")
async def admin_update_order_status(order_id: int, payload: OrderStatusUpdate, db: Session = Depends(get_db)):
    existing = show_order(db, order_id)
    if not existing:
        not_found("Order not found")

    data = update_status(db, order_id, payload.status)
    if not data:
        bad_request("Invalid order status transition")

    return success("Order status updated successfully", data)


@router.get("/reports/sales")
async def admin_sales_report(db: Session = Depends(get_db)):
    total_orders = db.query(func.count(Orders.id)).scalar() or 0
    total_sales = db.query(func.coalesce(func.sum(Orders.total_amount), 0)).scalar() or 0
    delivered_sales = (
        db.query(func.coalesce(func.sum(Orders.total_amount), 0))
        .filter(Orders.status == OrderStatus.COMPLETED.value)
        .scalar()
        or 0
    )
    customer_count = (
        db.query(func.count(User.id))
        .filter(User.role == UserRole.CUSTOMER)
        .scalar()
        or 0
    )

    return success(
        "Sales report fetched successfully",
        {
            "total_orders": total_orders,
            "total_sales": float(total_sales),
            "delivered_sales": float(delivered_sales),
            "customer_count": customer_count,
        },
    )
