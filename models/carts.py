from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float

from core.database import Base
from models.mixins import TimestampMixin

class Carts(TimestampMixin, Base):
    __tablename__ = 'carts'
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey('users.id'))
