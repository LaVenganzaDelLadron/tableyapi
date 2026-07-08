from sqlalchemy import Column, Integer, String, Float, Date
from cores.database import Base
from models.mixins import TimestampMixin


class Expenses(TimestampMixin, Base):
    __tablename__ = "expenses"

    id = Column(Integer, primary_key=True)
    description = Column(String, nullable=False)
    amount = Column(Float, nullable=False)
    entry_date = Column(Date, nullable=False)
