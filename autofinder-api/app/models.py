from pydantic import BaseModel
from typing import List

class ProductData(BaseModel):
    data_sku: str
    data_name: str
    data_best_price: str
    data_image: str

class ApiResponse(BaseModel):
    bstatus: bool
    smessage: str
    odata: List[ProductData]