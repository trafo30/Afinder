document.addEventListener('DOMContentLoaded', function() {
    // Configurar botones de comparar
    const compararBtns = document.querySelectorAll('.btn-comparar');
    
    compararBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Obtener el contenedor del producto
            const producto = this.closest('.producto');
            
            // Extraer los datos del producto
            const imagen = producto.querySelector('img').src;
            const marca = producto.querySelector('.marca').textContent;
            const nombre = producto.querySelector('strong').textContent;
            
            // Extraer precios de forma robusta
            const precioElement = producto.querySelector('p:not(.marca)');
            let precioActual = '';
            let precioTachado = '';
            
            // Extraer todo el texto del elemento de precio
            const precioTexto = precioElement.textContent;
            
            // Buscar el precio actual (primera parte antes del espacio)
            const precioMatch = precioTexto.match(/S\/\s*\d+\.\d+/);
            if (precioMatch) {
                precioActual = precioMatch[0];
            }
            
            // Buscar el precio tachado si existe
            const tachadoElement = precioElement.querySelector('.tachado');
            if (tachadoElement) {
                precioTachado = tachadoElement.textContent.trim();
            }
            
            // Extraer el enlace de compra original
            const enlaceCompra = producto.querySelector('.btn-comprar').href;
            
            // Crear objeto con los datos del producto
            const productoData = {
                imagen: imagen,
                marca: marca,
                nombre: nombre,
                precioActual: precioActual,
                precioTachado: precioTachado,
                enlaceCompra: enlaceCompra
            };
            
            // Guardar en sessionStorage para la página de comparación
            sessionStorage.setItem('productoComparar', JSON.stringify(productoData));
            
            // Redirigir a la página de comparación
            window.location.href = 'comparar.html';
        });
    });
});