from sqlalchemy import Column, Integer, String, Float, Date
from cores.database import Base
from models.mixins import TimestampMixin

class RawMaterials(TimestampMixin, Base):
    __tablename__ = 'raw_materials'

    id = Column(Integer, primary_key=True)
    material_name = Column(String, nullable=False)
    quantity = Column(Float, nullable=False)
    unit = Column(String, nullable=False)
    