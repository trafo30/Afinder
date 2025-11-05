from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from bs4 import BeautifulSoup
import time
from .models import ApiResponse, ProductData

def configure_driver():
    opts = Options()
    opts.add_argument("--headless=new")
    opts.add_argument("--no-sandbox")
    opts.add_argument("--disable-dev-shm-usage")
    opts.add_argument("--window-size=1920,1080")
    # Selenium 4 usa Service (selenium-manager resuelve el binario)
    service = Service()
    return webdriver.Chrome(service=service, options=opts)

async def scrape_promart(endpoint: str, q: str | None = None) -> ApiResponse:
    driver = configure_driver()
    products: list[ProductData] = []
    try:
        driver.get(endpoint)    
        try:
                WebDriverWait(driver, 30).until(
                    EC.presence_of_any_elements_located(
                        (By.CSS_SELECTOR, ".js-prod .container__item--product, .productItem, li[data-sku]")
                    )
                )
        except Exception:
                print(f"[TIMEOUT] No se encontraron productos visibles en {endpoint}")


        # scroll para cargar todo
        last_h = 0
        while True:
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(1.2)
            h = driver.execute_script("return document.body.scrollHeight")
            if h == last_h:
                break
            last_h = h

        soup = BeautifulSoup(driver.page_source, "html.parser")
        items = soup.select(".js-prod .container__item--product")

        if not items:
            # intenta un selector alternativo por si la plantilla cambia
            items = soup.select(".productItem, .product-item, li[data-sku]")

        print(f"[SCRAPER] URL: {endpoint} | items: {len(items)}")

        for it in items:
            try:
                sku   = it.get("data-sku", "")
                name  = it.get("data-name", "")
                price = it.get("data-best-price", "")
                img_wrap = it.find("div", class_="images__wrap")
                img = img_wrap.find("img")["src"] if img_wrap and img_wrap.find("img") else ""

                products.append(ProductData(
                    data_sku=sku,
                    data_name=name,
                    data_best_price=price,
                    data_image=img
                ))
            except Exception:
                continue
    finally:
        driver.quit()

    # filtro por q en nombre o sku
    if q:
        ql = q.casefold()
        products = [p for p in products if ql in p.data_name.casefold() or ql in p.data_sku.casefold()]

    return ApiResponse(
        bstatus=True,
        smessage=f"{len(products)} registros",
        odata=products
    )