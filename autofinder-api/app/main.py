from fastapi import FastAPI, Query, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from .scraper import scrape_promart
from .models import ApiResponse

app = FastAPI(title="Promart Scraper API")
app.add_middleware(CORSMiddleware, allow_origins=["*"], allow_methods=["*"], allow_headers=["*"])

# Mapea tus categorías a URLs reales de Promart
CATEGORY_URLS = {
    "llantas": "https://www.promart.pe/automotriz/llantas",
    "baterias": "https://www.promart.pe/herramientas/herramientas-de-mecanica-automotriz/baterias-de-auto",
    "filtros-aceite": "https://www.promart.pe/automotriz/filtros-de-aceite",
    "focos": "https://www.promart.pe/automotriz/luces-automotrices",      # ajusta si difiere
    "amortiguadores": "https://www.promart.pe/automotriz/amortiguadores", # ajusta
    "rotulas": "https://www.promart.pe/automotriz/suspension",            # ajusta
    "rodamientos": "https://www.promart.pe/automotriz/rodamientos",       # ajusta
    "filtro-aire": "https://www.promart.pe/automotriz/filtros-de-aire",   # ajusta
    "espejos": "https://www.promart.pe/automotriz/espejos",               # ajusta
}

@app.get("/categoria/{cat}", response_model=ApiResponse)
async def get_categoria(cat: str, q: str | None = Query(default=None, min_length=2)):
    url = CATEGORY_URLS.get(cat)
    if not url:
        raise HTTPException(status_code=404, detail="Categoría no soportada")
    return await scrape_promart(url, q)