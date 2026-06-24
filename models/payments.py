import enum

from sqlalchemy import Column, Integer, String, ForeignKey, Float
from core.database import Base
from models.mixins import TimestampMixin


class PaymentMethod(enum.Enum):
    COD = "cod"
    GCASH = "gcash"


class PaymentStatus(enum.Enum):
    PENDING = "pending"
    COMPLETED = "completed"
    FAILED = "failed"

class Payments(TimestampMixin, Base):
    __tablename__ = "payments"
    id = Column(Integer, primary_key=True)
    order_id = Column(Integer, ForeignKey('orders.id'))
    amount = Column(Float)
    payment_method = Column(String, default=PaymentMethod.COD.value)
    payment_status = Column(String, default=PaymentStatus.PENDING.value)
    transaction_id = Column(String)
