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
    future=True
)

def configure_driver():
    chrome_options = Options()
    chrome_options.add_argument("--headless=new")
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--disable-gpu")
    chrome_options.add_argument("--window-size=1920,1080")

    driver = webdriver.Chrome(options=chrome_options)
    # si la página tarda más de 20 s en cargar, cortamos
    driver.set_page_load_timeout(20)
    return driver


async def scrape_promart(endpoint: str, q: str | None = None) -> ApiResponse:
    driver = configure_driver()
    products: list[ProductData] = []

    try:
        # 1) cargar página con timeout
        try:
            driver.get(endpoint)
        except TimeoutException:
            print(f"[TIMEOUT] Cargando {endpoint}, detengo carga.")
            # paramos la carga y seguimos con lo que haya
            driver.execute_script("window.stop();")

        # 2) esperar aparición de productos, pero con tope de 10s
        try:
            WebDriverWait(driver, 10).until(
                EC.presence_of_any_elements_located(
                    (By.CSS_SELECTOR, ".js-prod .container__item--product, li[data-sku]")
                )
            )
        except TimeoutException:
            print(f"[TIMEOUT] No se encontraron productos visibles en {endpoint}")
            return ApiResponse(
                bstatus=False,
                smessage="No se encontraron productos (timeout / cambio de página)",
                odata=[],
            )

        # 3) scroll para cargar más resultados (opcional)
        last_h = 0
        while True:
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(1.2)
            h = driver.execute_script("return document.body.scrollHeight")
            if h == last_h:
                break
            last_h = h

        # 4) parsear HTML
        soup = BeautifulSoup(driver.page_source, "html.parser")
        items = soup.select(".js-prod .container__item--product, li[data-sku]")

        for item in items:
            try:
                sku = item.get("data-sku", "")
                name = item.get("data-name", "")
                price = item.get("data-best-price", "")

                img_tag = item.select_one(".images__wrap img") or item.find("img")
                image_url = img_tag["src"] if img_tag and img_tag.has_attr("src") else ""

                # pequeño filtro por búsqueda q (opcional)
                if q and q.lower() not in name.lower():
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
# filtro por q en nombre o sku
        if q:
            ql = q.casefold()
            products = [p for p in products if ql in p.data_name.casefold() or ql in p.data_sku.casefold()]
            
        # --- Guardar en base de datos antes de retornar ---
        from sqlalchemy import create_engine, text

        engine = create_engine("mysql+pymysql://root:@localhost/autofinder?charset=utf8mb4", future=True)

        UPSERT_SQL = text("""
        INSERT INTO productos
        (categoria, nombre, marca, descripcion, estado, precio, imagen_url, id_tienda)
        VALUES
        (:cat, :nombre, :marca, :descr, :estado, :precio, :img, :id_tienda)
        ON DUPLICATE KEY UPDATE
        marca = VALUES(marca),
        descripcion = VALUES(descripcion),
        estado = VALUES(estado),
        precio = VALUES(precio),
        imagen_url = VALUES(imagen_url),
        actualizado_at = CURRENT_TIMESTAMP
        """)

        def upsert_batch(conn, cat, items):
            for it in items:
                conn.execute(UPSERT_SQL, {
                    "cat": cat,
                    "nombre": it.data_name,
                    "marca": "",
                    "descr": "",
                    "estado": "disponible",
                    "precio": it.data_best_price,
                    "img": it.data_image,
                    "id_tienda": None
                })

        with engine.begin() as conn:
            upsert_batch(conn, categoria_actual, products)

            api_response = ApiResponse(
                bstatus=True,
                smessage=f"{len(products)} registros",
                odata=products
            )


