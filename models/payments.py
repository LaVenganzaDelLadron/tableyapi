from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float, DateTime
from core.database import Base

class PaymentMethod(Enum):
    COD = "cod"
    GCASH = "gcash"

class PaymentStatus(Enum):
    PENDING = "pending"
    COMPLETED = "completed"
    FAILED = "failed"

class Payments(Base):
    __tablename__ = "payments"
    id = Column(Integer, primary_key=True)
    order_id = Column(Integer, ForeignKey('orders.id'))
    amount = Column(Float)
    payment_method = Column(String, default=PaymentMethod.COD)
    payment_status = Column(String, default=PaymentStatus.PENDING)
    transaction_id = Column(String)
    created_at = Column(DateTime)
    updated_at = Column(DateTime)