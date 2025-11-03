from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from bs4 import BeautifulSoup
import time
from .models import ApiResponse, ProductData


def configure_driver():
    """Configura el driver de Selenium con opciones mejoradas"""
    chrome_options = Options()
    chrome_options.add_argument("--headless=new")  # Nueva sintaxis para headless
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--disable-gpu")
    chrome_options.add_argument("--disable-extensions")
    chrome_options.add_argument("--disable-infobars")
    chrome_options.add_argument("--disable-notifications")
    chrome_options.add_argument("--ignore-certificate-errors")
    chrome_options.add_argument("--allow-running-insecure-content")
    chrome_options.add_argument("--remote-debugging-port=9222")
    chrome_options.add_argument("--window-size=1920,1080")

    # Configuración para evitar bloqueos de red
    chrome_options.add_argument("--disable-web-security")
    chrome_options.add_argument("--allow-insecure-localhost")

    # Deshabilitar sandboxing y otros features de seguridad para desarrollo
    chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])
    chrome_options.add_experimental_option("useAutomationExtension", False)
    service = webdriver.ChromeService()
    driver = webdriver.Chrome(service=service, options=chrome_options)

    # Configuración adicional para evitar bloqueos
    driver.execute_cdp_cmd("Network.setCacheDisabled", {"cacheDisabled": False})
    driver.execute_cdp_cmd(
        "Network.setUserAgentOverride",
        {
            "userAgent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
        },
    )

    return driver


async def scrape_promart(endpoint: str) -> ApiResponse:
    """Función principal de scraping para Promart llantas"""
    driver = configure_driver()
    product_data = []

    try:

        driver.get(endpoint)

        # Esperar a que cargue el contenido dinámico
        WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, ".js-prod .container__item--product"))
        )

        # Scroll para cargar más productos
        last_height = driver.execute_script("return document.body.scrollHeight")
        while True:
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(2)
            new_height = driver.execute_script("return document.body.scrollHeight")
            if new_height == last_height:
                break
            last_height = new_height

        # Parsear el HTML con BeautifulSoup
        soup = BeautifulSoup(driver.page_source, 'html.parser')

        # Encontrar todos los productos
        product_items = soup.select('.js-prod .container__item--product')

        for item in product_items:
            try:
                # Extraer datos de los atributos data-*
                sku = item.get('data-sku', '')
                name = item.get('data-name', '')
                price = item.get('data-best-price', '')

                # Extraer la primera imagen (tomamos la primera img dentro de images__wrap)
                image_div = item.find('div', class_='images__wrap')
                image_url = image_div.find('img')['src'] if image_div else ''

                product_data.append(ProductData(
                    data_sku=sku,
                    data_name=name,
                    data_best_price=price,
                    data_image=image_url
                ))
            except Exception as e:
                print(f"Error procesando un producto: {e}")
                continue

    finally:
        driver.quit()

    # Construir respuesta según formato requerido
    return ApiResponse(
        bstatus=True,
        smessage=f"{len(product_data)} registros encontrados",
        odata=product_data
    )