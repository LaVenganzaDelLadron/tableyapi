from sqlalchemy import Column, Integer, String, Date, ForeignKey, Float
from core.database import Base
from models.timestamp import TimestampMixin

class Sales(Base, TimestampMixin):
    __tablename__ = "sales"

    id = Column(Integer, primary_key=True)
    product_id = Column(Integer, ForeignKey("products.id"))
    sales_date = Column(Date, nullable=False)
    total_amount = Column(Float, nullable=False)
