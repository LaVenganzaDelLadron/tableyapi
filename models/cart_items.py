from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float
from core.database import Base
from models.mixins import TimestampMixin


class CartItems(TimestampMixin, Base):
    __tablename__ = 'cart_items'
    id = Column(Integer, primary_key=True)
    cart_id = Column(Integer, ForeignKey('carts.id'))
    product_id = Column(Integer, ForeignKey('products.id'))
    quantity = Column(Integer)
