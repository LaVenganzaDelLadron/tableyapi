from sqlalchemy import Column, Integer, String, Enum
import enum
from core.database import Base
from models.timestamp import TimestampMixin

class Role(enum.Enum):
    ADMIN = "admin"
    CUSTOMER = "customer"


class User(Base, TimestampMixin):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True)
    email = Column(String, unique=True, nullable=False)
    fullname = Column(String, nullable=False)
    password = Column(String, nullable=False)
    role = Column(Enum(Role), nullable=Role.ADMIN)