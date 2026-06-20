from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float, DateTime

from core.database import Base

class Carts(Base):
    __tablename__ = 'carts'
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey('users.id'))
    created_at = Column(DateTime)
    updated_at = Column(DateTime)