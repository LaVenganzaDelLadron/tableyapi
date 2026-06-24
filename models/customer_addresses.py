from sqlalchemy import Column, Integer, String

from core.database import Base
from models.mixins import TimestampMixin


class CustomerAddresses(TimestampMixin, Base):
    __tablename__ = "customer_addresses"

    id = Column(Integer, primary_key=True)
    user_id = Column(Integer)
    recipient_name = Column(String)
    phone = Column(String)
    address_line = Column(String)
    city = Column(String)
    province = Column(String)
    postal_code = Column(String)
    is_default = Column(Integer, default=0)
