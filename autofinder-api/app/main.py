from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from .scraper import scrape_promart
from .models import ApiResponse

app = FastAPI(title="Promart Scraper API")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/llantas", response_model=ApiResponse)
async def get_llantas():
    return await scrape_promart("https://www.promart.pe/automotriz/llantas")

@app.get("/baterias", response_model=ApiResponse)
async def get_baterias():
    return await scrape_promart("https://www.promart.pe/herramientas/herramientas-de-mecanica-automotriz/baterias-de-auto")