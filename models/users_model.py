from sqlalchemy import Column, Integer, String, Enum as SAEnum
import enum
from core.database import Base
from models.timestamp import TimestampMixin

class Role(enum.Enum):
    ADMIN = "admin"
    CUSTOMER = "customer"


class User(Base, TimestampMixin):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True)
    email = Column(String(255), unique=True, nullable=False)
    fullname = Column(String(255), nullable=False)
    password = Column(String(500), nullable=False)
    # Store the enum's value (e.g. 'admin', 'customer') in the DB
    role = Column(
        SAEnum(Role, values_callable=lambda obj: [e.value for e in obj], name="role"),
        nullable=False,
        default=Role.ADMIN,
    )