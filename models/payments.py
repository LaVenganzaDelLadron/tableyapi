from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float
from core.database import Base
from models.mixins import TimestampMixin

class PaymentMethod(Enum):
    COD = "cod"
    GCASH = "gcash"

class PaymentStatus(Enum):
    PENDING = "pending"
    COMPLETED = "completed"
    FAILED = "failed"

class Payments(TimestampMixin, Base):
    __tablename__ = "payments"
    id = Column(Integer, primary_key=True)
    order_id = Column(Integer, ForeignKey('orders.id'))
    amount = Column(Float)
    payment_method = Column(String, default=PaymentMethod.COD)
    payment_status = Column(String, default=PaymentStatus.PENDING)
    transaction_id = Column(String)
