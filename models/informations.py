from sqlalchemy import Column, Integer, String, Enum, ForeignKey, DateTime
from core.database import Base

class Informations(Base):
    __tablename__ = "informations"
    id = Column(Integer, primary_key=True, autoincrement=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    phone = Column(String)
    address = Column(String)
    city = Column(String)
    province = Column(String)
    street = Column(String)
    postal_code = Column(String)
    created_at = Column(DateTime)
    updated_at = Column(DateTime)