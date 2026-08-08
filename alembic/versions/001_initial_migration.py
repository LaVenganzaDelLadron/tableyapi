"""Initial migration - Create all tables

Revision ID: 001
Revises: 
Create Date: 2026-08-08 15:00:00.000000

"""
from alembic import op
import sqlalchemy as sa
from datetime import datetime

# revision identifiers, used by Alembic.
revision = '001'
down_revision = None
branch_labels = None
depends_on = None


def upgrade() -> None:
    # Create users table
    op.create_table(
        'user',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('username', sa.String(length=50), nullable=False),
        sa.Column('email', sa.String(length=255), nullable=False),
        sa.Column('hashed_password', sa.String(length=500), nullable=False),
        sa.Column('role', sa.Enum('admin', 'manager', 'staff', name='role'), nullable=False),
        sa.Column('is_active', sa.Boolean(), nullable=False, server_default='1'),
        sa.Column('created_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime(), nullable=False, server_default=sa.func.now(), onupdate=sa.func.now()),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('username'),
        sa.UniqueConstraint('email')
    )

    # Create raw_materials table
    op.create_table(
        'raw_materials',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('name', sa.String(length=255), nullable=False),
        sa.Column('origin', sa.String(length=255), nullable=False),
        sa.Column('current_stock', sa.Integer(), nullable=False),
        sa.Column('cost_per_unit', sa.Float(), nullable=False),
        sa.PrimaryKeyConstraint('id')
    )

    # Create product_batches table
    op.create_table(
        'product_batches',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('raw_material_id', sa.Integer(), nullable=True),
        sa.Column('roast_date', sa.Date(), nullable=False),
        sa.Column('milled_weight', sa.Float(), nullable=False),
        sa.Column('package_pieces', sa.Integer(), nullable=False),
        sa.Column('production_cost', sa.Float(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime(), nullable=False, server_default=sa.func.now(), onupdate=sa.func.now()),
        sa.ForeignKeyConstraint(['raw_material_id'], ['raw_materials.id'], ),
        sa.PrimaryKeyConstraint('id')
    )

    # Create products table
    op.create_table(
        'products',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('production_batches_id', sa.Integer(), nullable=True),
        sa.Column('name', sa.String(length=255), nullable=False),
        sa.Column('selling_price', sa.Float(), nullable=False),
        sa.Column('current_stock', sa.Integer(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime(), nullable=False, server_default=sa.func.now(), onupdate=sa.func.now()),
        sa.ForeignKeyConstraint(['production_batches_id'], ['product_batches.id'], ),
        sa.PrimaryKeyConstraint('id')
    )

    # Create sales table
    op.create_table(
        'sales',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('user_id', sa.Integer(), nullable=False),
        sa.Column('total_amount', sa.Float(), nullable=False),
        sa.Column('notes', sa.String(length=500), nullable=True),
        sa.Column('created_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime(), nullable=False, server_default=sa.func.now(), onupdate=sa.func.now()),
        sa.ForeignKeyConstraint(['user_id'], ['user.id'], ),
        sa.PrimaryKeyConstraint('id')
    )

    # Create sale_items table
    op.create_table(
        'sale_items',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('sale_id', sa.Integer(), nullable=False),
        sa.Column('product_id', sa.Integer(), nullable=False),
        sa.Column('quantity', sa.Integer(), nullable=False),
        sa.Column('unit_price', sa.Float(), nullable=False),
        sa.Column('created_at', sa.DateTime(), nullable=False, server_default=sa.func.now()),
        sa.Column('updated_at', sa.DateTime(), nullable=False, server_default=sa.func.now(), onupdate=sa.func.now()),
        sa.ForeignKeyConstraint(['product_id'], ['products.id'], ),
        sa.ForeignKeyConstraint(['sale_id'], ['sales.id'], ),
        sa.PrimaryKeyConstraint('id')
    )


def downgrade() -> None:
    op.drop_table('sale_items')
    op.drop_table('sales')
    op.drop_table('products')
    op.drop_table('product_batches')
    op.drop_table('raw_materials')
    op.drop_table('user')
