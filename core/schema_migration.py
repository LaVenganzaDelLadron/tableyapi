
# Explanation:
# This file is part of the tableyapi backend and contains Core application infrastructure and support code for schema migrations.
# The original code lines remain unchanged; these comments are added to explain the purpose of the module.
# Read the surrounding imports and logic together to understand how this file contributes to the application.

from sqlalchemy import inspect, text


SCAN_COLUMN_SQL = {
    "proposed_plan": "TEXT",
    "parent_scan_id": "INTEGER",
    "executed_command": "TEXT",
    "stdout": "TEXT",
    "stderr": "TEXT",
    "return_code": "INTEGER",
    "error_message": "TEXT",
    "approved_at": "DATETIME",
    "pid": "INTEGER",
    "log_path": "TEXT",
    "exit_path": "TEXT",
    "timed_out": "INTEGER",
    "cancelled_at": "DATETIME",
}

TABLE_COLUMN_SQL = {
    "orders": {
        "user_id": "INTEGER",
    },
    "cart_items": {
        "product_id": "INTEGER",
    },
    "order_items": {
        "product_name": "TEXT",
        "subtotal": "FLOAT",
    },
    "shipping": {
        "created_at": "DATETIME",
        "updated_at": "DATETIME",
    },
}

TIMESTAMP_TABLES = (
    "cart_items",
    "carts",
    "categories",
    "informations",
    "order_items",
    "orders",
    "payments",
    "products",
    "shipping",
    "users",
    "audit_logs",
    "customer_addresses",
    "order_status_history",
    "password_resets",
)


def ensure_scan_columns(engine):
    inspector = inspect(engine)
    if "scans" not in inspector.get_table_names():
        ensure_rbac_columns(engine)
        return

    _ensure_columns(engine, "scans", SCAN_COLUMN_SQL)
    ensure_rbac_columns(engine)


def ensure_rbac_columns(engine):
    inspector = inspect(engine)
    existing_tables = set(inspector.get_table_names())
    for table_name, columns in TABLE_COLUMN_SQL.items():
        if table_name in existing_tables:
            _ensure_columns(engine, table_name, columns)

    _backfill_timestamps(engine, existing_tables)


def _ensure_columns(engine, table_name: str, columns: dict[str, str]):
    inspector = inspect(engine)
    existing_columns = {column["name"] for column in inspector.get_columns(table_name)}
    missing_columns = [
        (column_name, column_type)
        for column_name, column_type in columns.items()
        if column_name not in existing_columns
    ]
    if not missing_columns:
        return

    with engine.begin() as connection:
        for column_name, column_type in missing_columns:
            connection.execute(text(f"ALTER TABLE {table_name} ADD COLUMN {column_name} {column_type}"))


def _backfill_timestamps(engine, existing_tables: set[str]):
    now_sql = "CURRENT_TIMESTAMP"
    with engine.begin() as connection:
        for table_name in TIMESTAMP_TABLES:
            if table_name not in existing_tables:
                continue

            inspector = inspect(engine)
            existing_columns = {column["name"] for column in inspector.get_columns(table_name)}
            if "created_at" in existing_columns:
                connection.execute(text(f"UPDATE {table_name} SET created_at = {now_sql} WHERE created_at IS NULL"))
            if "updated_at" in existing_columns:
                connection.execute(text(f"UPDATE {table_name} SET updated_at = {now_sql} WHERE updated_at IS NULL"))