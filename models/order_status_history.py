from sqlalchemy import Column, Integer, String, ForeignKey

from core.database import Base
from models.mixins import TimestampMixin


class OrderStatusHistory(TimestampMixin, Base):
    __tablename__ = "order_status_history"

    id = Column(Integer, primary_key=True)
    order_id = Column(Integer, ForeignKey("orders.id"))
    from_status = Column(String)
    to_status = Column(String)
    changed_by_user_id = Column(Integer, nullable=True)
