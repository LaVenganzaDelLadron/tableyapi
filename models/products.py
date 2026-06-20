from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float, DateTime
from core.database import Base

class Products(Base):
    __tablename__ = 'products'
    id = Column(Integer, primary_key=True)
    category_id = Column(Integer, ForeignKey('categories.id'))
    name = Column(String)
    description = Column(String)
    price = Column(Float)
    stock = Column(Integer)
    image = Column(String)
    status = Column(String)
    created_at = Column(DateTime)
    updated_at = Column(DateTime)