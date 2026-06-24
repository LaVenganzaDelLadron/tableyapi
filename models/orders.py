import enum
from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float
from core.database import Base
from models.mixins import TimestampMixin

class OrderStatus(enum.Enum):
    PENDING = "PENDING"
    PAID = "PAID"
    SHIPPED = "SHIPPED"
    COMPLETED = "COMPLETED"
    CANCELLED = "CANCELLED"


class Orders(TimestampMixin, Base):
    __tablename__ = "orders"
    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    information_id = Column(Integer, ForeignKey('informations.id'))
    total_amount = Column(Float)
    status = Column(String, default=OrderStatus.PENDING.value)
    payment_method = Column(String)
