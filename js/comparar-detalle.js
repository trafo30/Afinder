document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos del producto de sessionStorage
    const productoData = JSON.parse(sessionStorage.getItem('productoComparar'));
    const productoDetalle = document.getElementById('producto-detalle');
    const tiendasContainer = document.getElementById('tiendas-container');
    
    if (productoData) {
        // Extraer el enlace de compra original
        const enlaceCompra = productoData.enlaceCompra;
        
        // Construir HTML para los precios
        let preciosHTML = '';
        if (productoData.precioActual) {
            preciosHTML += `<p class="precio-actual">${productoData.precioActual}</p>`;
        }
        if (productoData.precioTachado) {
            preciosHTML += `<span class="precio-tachado">${productoData.precioTachado}</span>`;
        }
        
        // Mostrar datos del producto con los precios
        productoDetalle.innerHTML = `
            <div class="imagen-producto">
                <img src="${productoData.imagen}" alt="${productoData.nombre}">
            </div>
            <div class="info-producto">
                <p class="marca">${productoData.marca}</p>
                <h1 class="nombre">${productoData.nombre}</h1>
                <h1 class="precios">
                    ${productoData.preciosHTML}
                </h1>
                
                <!-- Contenedor de botones similar al de la página principal -->
                <div class="botones-producto-detalle">
                    <a href="${enlaceCompra}" target="_blank" class="btn-comprar-detalle">COMPRAR</a>
                </div>
            </div>
        `;
        
        // Mostrar tiendas con precios comparativos (ejemplo)
        // Extraer el valor numérico del precio actual
        let precioNumerico = 0;
        if (productoData.precioActual) {
            precioNumerico = parseFloat(productoData.precioActual.replace('S/', '').replace(/\s/g, '').trim());
        }
        
        const tiendas = [
            { 
                nombre: "Falabella", 
                logo: "imagenes/logo-falabella.png", 
                precio: `S/ ${(precioNumerico - 5).toFixed(2)}`
            },
            { 
                nombre: "Ripley", 
                logo: "imagenes/logo-ripley.png", 
                precio: `S/ ${(precioNumerico + 10).toFixed(2)}`
            },
            { 
                nombre: "PlazaVea", 
                logo: "imagenes/logo-plazavea.png", 
                precio: `S/ ${(precioNumerico - 15).toFixed(2)}`
            },
            { 
                nombre: "Sodimac", 
                logo: "imagenes/logo-sodimac.png", 
                precio: `S/ ${(precioNumerico - 8).toFixed(2)}`
            }
        ];
        
        // Generar HTML para las tiendas
        let tiendasHTML = '';
        tiendas.forEach(tienda => {
            tiendasHTML += `
                <div class="tienda">
                    <img src="${tienda.logo}" alt="${tienda.nombre}">
                    <p class="precio-tienda">${tienda.precio}</p>
                    <a href="#" class="btn-comprar-tienda">Comprar en ${tienda.nombre}</a>
                </div>
            `;
        });
        
        tiendasContainer.innerHTML = tiendasHTML;
    } else {
        productoDetalle.innerHTML = `
            <div class="error-mensaje">
                <h2>No se encontró información del producto</h2>
                <p>Por favor, selecciona un producto para comparar desde la página principal</p>
                <a href="index.html" class="btn-volver">Volver al inicio</a>
            </div>
        `;
    }
});