from sqlalchemy import Column, Integer, String, Float, Date, ForeignKey
from cores.database import Base
from models.mixins import TimestampMixin

class Products(TimestampMixin, Base):
    __tablename__ = "products"
    id = Column(Integer, primary_key=True)
    product_name = Column(String, nullable=False)
    selling_price = Column(Float, nullable=False)
    current_stock = Column(Integer, nullable=False)
    