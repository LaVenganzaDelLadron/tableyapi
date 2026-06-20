from sqlalchemy import Column, Integer, String, Enum, DateTime
from core.database import Base


class Categories(Base):
    __tablename__ = 'categories'
    id = Column(Integer, primary_key=True)
    name = Column(String)
    created_at = Column(DateTime)
    updated_at = Column(DateTime)