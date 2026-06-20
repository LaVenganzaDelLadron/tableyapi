import enum
from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float, DateTime
from core.database import Base

class OrderStatus(enum.Enum):
    ACTIVE = "active"
    INACTIVE = "inactive"

class Orders(Base):
    __tablename__ = "orders"
    id = Column(Integer, primary_key=True)
    information_id = Column(Integer, ForeignKey('information.id'))
    total_amount = Column(Float)
    status = Column(String, default=OrderStatus.ACTIVE)
    payment_method = Column(String)
    created_at = Column(DateTime)
    updated_at = Column(DateTime)