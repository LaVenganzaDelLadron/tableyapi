from sqlalchemy import Column, Integer, String, Enum
import enum
from core.database import Base
from models.mixins import TimestampMixin

class UserRole(enum.Enum):
    ADMIN = "admin"
    CUSTOMER = "customer"

class User(TimestampMixin, Base):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True)
    email = Column(String, unique=True)
    fullname = Column(String)
    username = Column(String, unique=True)
    password = Column(String)
    role = Column(Enum(UserRole), default=UserRole.CUSTOMER)
