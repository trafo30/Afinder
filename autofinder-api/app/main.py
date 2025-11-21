from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from sqlalchemy import text

from .models import ApiResponse, ProductData
from .scraper import engine  # engine ya está definido allí

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "http://localhost",
        "http://127.0.0.1",
        "http://localhost:80",
        "http://127.0.0.1:80",
    ],  # en desarrollo puedes usar ["*"]
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Mantenemos el diccionario para saber qué categorías son válidas
CATEGORY_URLS = {
    "llantas": "https://www.promart.pe/automotriz/llantas",
    "baterias": "https://www.promart.pe/herramientas/herramientas-de-mecanica-automotriz/baterias-de-auto",
    "Aceite": "https://www.promart.pe/automotriz/limpieza-para-auto/aceites-para-auto",
    "Aditivos": "https://www.promart.pe/automotriz/limpieza-para-auto/aditivos-para-auto",
    "Accesorios para Exterior": "https://www.promart.pe/automotriz/accesorios-para-auto/otros-accesorios-de-exterior-para-auto",
    "Accesorios para interior": "https://www.promart.pe/automotriz/accesorios-para-auto/otros-accesorios-de-interior-para-auto",
    "rodamientos": "https://www.promart.pe/automotriz/rodamientos",
    "filtro-aire": "https://www.promart.pe/automotriz/filtros-de-aire",
    "espejos": "https://www.promart.pe/automotriz/espejos",
}


@app.get("/categoria/{categoria}", response_model=ApiResponse)
async def get_categoria(categoria: str, q: str | None = None) -> ApiResponse:
    # Validar que la categoría exista en tu catálogo
    if categoria not in CATEGORY_URLS:
        raise HTTPException(status_code=404, detail="Categoría no encontrada")

    cat = categoria.upper()  # debe coincidir con lo que guardaste desde el scraper

    # Leer productos desde MySQL
    with engine.begin() as conn:
        params = {"cat": cat}
        sql = """
            SELECT nombre, precio, imagen_url
            FROM productos
            WHERE categoria = :cat
        """

        if q:
            sql += " AND nombre LIKE :q"
            params["q"] = f"%{q}%"

        rows = conn.execute(text(sql), params).mappings().all()

    # Adaptar filas de BD al modelo ProductData que ya usa tu frontend
    productos = [
        ProductData(
            data_sku="",  # si luego agregas sku a la tabla, lo pones aquí
            data_name=row["nombre"],
            data_best_price=str(row["precio"]),
            data_image=row["imagen_url"],
        )
        for row in rows
    ]

    return ApiResponse(
        bstatus=True,
        smessage=f"{len(productos)} registros encontrados",
        odata=productos,
    )

@app.get("/buscar", response_model=ApiResponse)
async def buscar(q: str | None = None) -> ApiResponse:
    """
    Búsqueda global en todas las categorías.
    Si q es None o vacío, devuelve todos los productos (puedes limitar con LIMIT).
    """
    with engine.begin() as conn:
        params: dict = {}
        sql = """
            SELECT nombre, precio, imagen_url
            FROM productos
        """

        if q:
            sql += " WHERE nombre LIKE :q"
            params["q"] = f"%{q}%"

        rows = conn.execute(text(sql), params).mappings().all()

    productos = [
        ProductData(
            data_sku="",                      # si luego agregas sku a la tabla, lo rellenas
            data_name=row["nombre"],
            data_best_price=str(row["precio"]),
            data_image=row["imagen_url"],
        )
        for row in rows
    ]

    return ApiResponse(
        bstatus=True,
        smessage=f"{len(productos)} registros encontrados",
        odata=productos,
    )