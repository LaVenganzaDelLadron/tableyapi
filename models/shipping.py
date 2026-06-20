from sqlalchemy import Column, Integer, String, Enum, ForeignKey, Float, DateTime
from core.database import Base
from models.mixins import TimestampMixin

class Shipping(TimestampMixin, Base):
    __tablename__ = "shipping"
    id = Column(Integer, primary_key=True)
    order_id = Column(Integer, ForeignKey("orders.id"))
    courier_id = Column(String)
    tracking_number = Column(String)
    shipping_fee = Column(Float)
    shipped_at = Column(DateTime)
    delivered_at = Column(DateTime)
