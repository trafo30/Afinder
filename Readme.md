## Instalacion Database

El proyecto requiere una base de datos MySQL de nombre autofinder, en la carpeta database se encuentra el script autofinder.sql

## Instalacion FrontEnd

Para iniciar el proyecto necesita un servidor de aplicaciones apache con soporte a php, puede utilizar XAMPP o WAMPP y copiar el directorio autofinder.

ingresar a su direccion local:

```bash
  http://localhost/autofinder/afinder/Public
  
```


## Instalacion BackEnd

Para iniciar el proyecto hecho en python debe seguir los siguientes pasos ubicado en el directorio autofinder-api:


### Creando el entorno
```bash
  python-m venv .venv
```
### Instalando dependencias
```bash
  pip install -r requirements.txt

  .\.venv\Scripts\activate
```
### Arrancando el proyecto
```bash
  python -m uvicorn app.main:app --reload
```

### Actualizar productos scraper
```bash
  python run_scraper.py
```