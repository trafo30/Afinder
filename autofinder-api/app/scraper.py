from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException, WebDriverException
from bs4 import BeautifulSoup
from sqlalchemy import create_engine, text
import time
from .models import ApiResponse, ProductData

# Configuración de la base de datos MySQL
engine = create_engine(
    "mysql+pymysql://root:@localhost/autofinder?charset=utf8mb4",
    future=True,
)

# Constante: id_tienda de PROMART (ajusta al valor real en tu BD)
ID_TIENDA_PROMART = 1

# SQL de upsert para la tabla productos
UPSERT_SQL = text("""
INSERT INTO productos
(id_tienda, nombre, descripcion, marca, categoria, estado, precio, imagen_url)
VALUES
(:id_tienda, :nombre, :descr, :marca, :cat, :estado, :precio, :img)
ON DUPLICATE KEY UPDATE
  descripcion    = VALUES(descripcion),
  marca          = VALUES(marca),
  categoria      = VALUES(categoria),
  estado         = VALUES(estado),
  precio         = VALUES(precio),
  imagen_url     = VALUES(imagen_url),
  actualizado_at = CURRENT_TIMESTAMP
""")


def configure_driver():
    chrome_options = Options()
    chrome_options.add_argument("--headless=new")
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--disable-gpu")
    chrome_options.add_argument("--window-size=1920,1080")

    driver = webdriver.Chrome(options=chrome_options)
    driver.set_page_load_timeout(60)
    return driver


def upsert_batch(conn, cat: str, items: list[ProductData], id_tienda: int):
    """Inserta/actualiza productos en la tabla productos."""
    for it in items:
        try:
            precio_val = float(it.data_best_price or 0)
        except (ValueError, TypeError):
            precio_val = 0.0

        conn.execute(UPSERT_SQL, {
            "id_tienda": id_tienda,
            "nombre": (it.data_name or "")[:150],
            "descr": "",
            "marca": "",
            "cat": cat,
            "estado": "nuevo",
            "precio": precio_val,
            "img": (it.data_image or "")[:255] if it.data_image else None,
        })


async def scrape_promart(endpoint: str, categoria_actual: str, q: str | None = None) -> ApiResponse:
    driver = configure_driver()
    products: list[ProductData] = []

    try:
        # 1) Cargar página
        try:
            driver.get(endpoint)
        except TimeoutException:
            print(f"[TIMEOUT] Cargando {endpoint}, continúo con lo que haya...")

        # pequeño margen extra para que termine de ejecutar JS
        time.sleep(3)

        # 2) Intentar esperar a que haya productos visibles (sin return en timeout)
        selector_base = ".js-prod .container__item--product, li[data-sku]"
        try:
            WebDriverWait(driver, 20).until(
                EC.presence_of_all_elements_located(
                    (By.CSS_SELECTOR, selector_base)
                )
            )
        except TimeoutException:
            print(f"[TIMEOUT] Esperando productos en {endpoint}, intentaré parsear igual...")

        # 3) Scroll para cargar más resultados (con límite)
        last_h = 0
        for _ in range(10):
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(1.0)
            h = driver.execute_script("return document.body.scrollHeight")
            if h == last_h:
                break
            last_h = h

        # 4) Parsear HTML con varios posibles selectores
        soup = BeautifulSoup(driver.page_source, "html.parser")

        selectors = [
            ".js-prod .container__item--product",
            "li[data-sku]",
            ".vtex-product-summary-2-x-container",   # layout alternativo típico
            ".product-item",                         # reserva, por si acaso
        ]

        items = []
        for sel in selectors:
            items = soup.select(sel)
            if items:
                print(f"[DEBUG] {endpoint} -> {len(items)} items usando selector '{sel}'")
                break

        if not items:
            print(f"[DEBUG] {endpoint} -> 0 items en el DOM con los selectores probados")
            return ApiResponse(
                bstatus=False,
                smessage="No se encontraron productos (selectores no coincidieron / página vacía)",
                odata=[],
            )

        # 5) Extraer productos
        for item in items:
            try:
                sku = item.get("data-sku", "")
                name = item.get("data-name", "")
                price = item.get("data-best-price", "")

                img_tag = item.select_one(".images__wrap img") or item.find("img")
                image_url = img_tag["src"] if img_tag and img_tag.has_attr("src") else ""

                if q and q.lower() not in (name or "").lower():
                    continue

                products.append(
                    ProductData(
                        data_sku=sku,
                        data_name=name,
                        data_best_price=price,
                        data_image=image_url,
                    )
                )
            except Exception as e:
                print(f"Error procesando producto: {e}")
                continue

        # 6) Guardar en BD
        if products:
            with engine.begin() as conn:
                upsert_batch(conn, categoria_actual, products, ID_TIENDA_PROMART)

        # 7) Respuesta API
        return ApiResponse(
            bstatus=True,
            smessage=f"{len(products)} registros encontrados",
            odata=products,
        )

    except WebDriverException as e:
        print(f"[WEBDRIVER ERROR] {e}")
        return ApiResponse(
            bstatus=False,
            smessage="Error interno del scraper",
            odata=[],
        )
    finally:
        driver.quit()
