from sqlalchemy import Column, Integer, String, Enum
from core.database import Base
from models.mixins import TimestampMixin


class Categories(TimestampMixin, Base):
    __tablename__ = 'categories'
    id = Column(Integer, primary_key=True)
    name = Column(String)
