from sqlalchemy import Column, String, Integer, ForeignKey, Float
from core.database import Base
from models.timestamp import TimestampMixin

class RawMaterials(Base):
    __tablename__ = "raw_materials"

    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey("users.id"))
    name = Column(String,nullable=False)
    weight = Column(Float,nullable=False)
    unit_price = Column(Float,nullable=False)
    total_price = Column(Float,nullable=False)