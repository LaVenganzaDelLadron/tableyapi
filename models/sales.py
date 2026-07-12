from sqlalchemy import Column, Integer, String, Float, Date
from cores.database import Base
from models.mixins import TimestampMixin

class Sales(TimestampMixin, Base):
    __tablename__ = "sales"

    id = Column(Integer, primary_key=True)
    sales_date = Column(Date, nullable=False)
    total_amount = Column(Float, nullable=False)
