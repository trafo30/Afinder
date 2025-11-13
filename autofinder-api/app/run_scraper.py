import asyncio
from app.scraper import scrape_promart
from app.main import CATEGORY_URLS  # reutilizamos el diccionario

async def main():
    for cat, url in CATEGORY_URLS.items():
        cat_upper = cat.upper()
        print(f"Scrapeando {cat_upper} desde {url} ...")
        resp = await scrape_promart(endpoint=url, categoria_actual=cat_upper, q=None)
        print(f"{cat_upper}: {resp.smessage}")

if __name__ == "__main__":
    asyncio.run(main())