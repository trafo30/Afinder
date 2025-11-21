<?php echo "<!-- PROBE ".__FILE__." ".date('H:i:s')." -->"; ?>
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AutoFinder</title>  
  <link rel="stylesheet" href="css/styles.css">
  <script defer src="js/script.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

  <!-- Modal de inicio de sesión -->
  <div id="loginModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <div class="modal-img-container">
        <img src="imgs/logo.png" alt="Logo AutoFinder">
      </div>
      <h2>Iniciar Sesión</h2>
      <form action="login.php" method="POST">
        <input type="text" name="usuario" placeholder="Usuario" class="modal-input" required>
        <input type="password" name="contrasena" placeholder="Contraseña" class="modal-input" required>
        <div class="forgot-password">
          <a href="#">¿Olvidaste tu contraseña?</a>
        </div>
        <div class="modal-buttons-column">
          <button type="submit" class="btn-ingresar">Ingresar</button>
        </div>
      </form>
      <div class="modal-buttons-column">
        <a href="registro.php" class="btn-registrarse">Registrarse</a>
      </div>
    </div>
  </div>

  <!-- Cabecera -->
<header>
  <div class="logo">
    <img src="imgs/logo.png" alt="AutoFinder Logo">
  </div>

  <form action="productos.php" method="get" class="search">
    <input type="hidden" name="modo" value="busqueda">
    <div class="search-box">
      <i class="fa-solid fa-magnifying-glass search-icon"></i>
      <input
        type="text"
        name="q"
        class="search-input"
        placeholder="Buscar productos, marcas o categorías..."
      >
      <button type="submit" class="search-btn">
        Buscar
      </button>
    </div>
  </form>

  <div class="icons">
    <!-- Favoritos -->
    <div class="icon-item">
      <img src="imgs/corazon1.png" alt="Favoritos">
      <span>Favoritos</span>
    </div>

    <!-- Carrito -->
    <div class="icon-item cart-icon" onclick="window.location.href='carrito.php'">
      <img src="imgs/carrito-de-compras.png" alt="Carrito">
      <span id="cart-count" class="cart-count">0</span>
      <span>Carrito</span>
    </div>

    <!-- Login / Usuario -->
    <?php if (isset($_SESSION['usuario'])): ?>
      <div class="welcome">
        <p>Bienvenido</p>
        <p><strong><?= htmlspecialchars($_SESSION['nombre']) ?></strong></p>
      </div>
      <a href="logout.php" class="login-button btn-salir">Salir</a>
    <?php else: ?>
      <button class="login-button">Ingresar</button>
    <?php endif; ?>
  </div>
</header>

    <!-- Filtro -->
    <div class="main">
        
        <aside class="sidebar">
            <ul>
                <li><a href="productos.php?cat=baterias">Baterías para autos</a></li>
                <li><a href="productos.php?cat=Aceite">Aceite</a></li>
                <li><a href="productos.php?cat=Aditivos">Aditivos</a></li>
                <li><a href="productos.php?cat=Accesorios para Exterior">Accesorios para exterior</a></li>
                <li><a href="productos.php?cat=Accesorios para interior">Accesorios para interiores</a></li>
                <li><a href="productos.php?cat=rodamientos">Rodamientos</a></li>
                <li><a href="productos.php?cat=filtro-aire">Filtro de aire</a></li>
                <li><a href="productos.php?cat=espejos">Espejos laterales</a></li>  
                <li><a href="productos.php?cat=llantas">Llantas</a></li>
            </ul>
            </aside>

        <!-- Principal -->
        <section class="slider">
            <div class="banner">
                <h1>Compara y ahorra</h1>
                <p>En AutoFinder, comparar precios es muy sencillo. Descubre la mejor alternativa y optimiza tu tiempo y
                    tu dinero.</p>
                <a class="btn" href="productos.php?cat=llantas">Compra Ahora →</a>
            </div>
        </section>
    </div>

    <!-- Seccion : HOY -->
    <section class="ventas">
        <h3>TOP DE HOY</h3>
        <h4>Las mejores ofertas del día</h4>
        <div class="productos">
            <div class="producto">
                <img src="imgs/foto1.1.jpg" alt="Llanta">
                <p class="marca">AUTOSTYLE</p>
                <p><strong>Llanta 185 70R14 88T Z-108</strong></p>
                <p>S/ 109.90 <span class="tachado">S/ 164.90</span></p>
                <div class="botones-producto">
                    <a href="https://sodimac.falabella.com.pe/sodimac-pe/product/127493531/Llanta-185-70R14-88T-Z-108/127493538?exp=sodimac"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto1.2.jpg" alt="Batería">
                <p class="marca">ENERJET</p>
                <p><strong>Batería para Camioneta 13 Placas 13W75 N2</strong></p>
                <p>S/ 279.90 <span class="tachado">S/ 320.00</span></p>
                <div class="botones-producto">
                    <a href="https://sodimac.falabella.com.pe/sodimac-pe/product/113331078/Bateria-para-Camioneta-13-Placas-13W75-N2/113331080?exp=sodimac"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto1.3.jpg" alt="Hidrolavadora">
                <p class="marca">KARCHER</p>
                <p><strong>Hidrolavadora Practica 1200W 103Bar Karcher</strong></p>
                <p>S/ 199.00 <span class="tachado">S/ 269.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/143352386/Hidrolavadora-Practica-1200W-103Bar-Karcher/143352387"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto1.4.jpg" alt="Silla de auto">
                <p class="marca">INFANTI</p>
                <p><strong>LB373 Silla Auto Maya Rubi</strong></p>
                <p>S/ 499.00 <span class="tachado">S/ 899.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/126657929/Silla-de-Auto-para-Bebe-%C2%BBMAYA%C2%BB-Ruby/126657930"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Seccion :  SEMANA -->
    <section class="ventas">
        <h3>TOP DE LA SEMANA</h3>
        <h4>Las mejores ofertas de la semana</h4>
        <div class="productos">
            <div class="producto">
                <img src="imgs/foto 2.1.png" alt="Llanta">
                <p class="marca">RYDANZ</p>
                <p><strong>Llanta 205/55Zr17 Roadster R02 </strong></p>
                <p>S/ 484.80 <span class="tachado">S/ 649.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.promart.pe/llanta-205-55zr17-rydanz-roadster-r02-runflat-91w-1000284439/p"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto 2.2.png" alt="Llanta">
                <p class="marca">BOSH</p>
                <p><strong>Bateria Bosch 15 Placas EFB LN3 70 AH 680 A</strong></p>
                <p>S/ 947.90 <span class="tachado">S/ 1106.90</span></p>
                <div class="botones-producto">
                    <a href="https://simple.ripley.com.pe/bateria-bosch-15-placas-efb-ln3-70-ah-680-a-pmp00001330150?s=mdco"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto1.3.jpg" alt="Llanta">
                <p class="marca">KARCHER</p>
                <p><strong>Hidrolavadora Practica 1200W 103Bar Karcher</strong></p>
                <p>S/ 199.00 <span class="tachado">S/ 269.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/143352386/Hidrolavadora-Practica-1200W-103Bar-Karcher/143352387"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="#" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto 2.3.png" alt="Llanta">
                <p class="marca">TRUPER</p>
                <p><strong>Combo gata lagarto y caja de herramientas</strong></p>
                <p>S/ 499.90 <span class="tachado">S/ 818.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/124267133/Combo-gata-lagarto-caiman-caballete-y-caja-de-herramientas/124267134"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="comparar.html" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Seccion :  MES -->
    <section class="ventas">
        <h3>TOP DEL MES</h3>
        <h4>Las mejores ofertas del mes</h4>
        <div class="productos">
            <div class="producto">
                <img src="imgs/foto 3.1.png" alt="Llanta">
                <p class="marca">ALLEN SPORTS</p>
                <p><strong>PORTABICICLETAS ALLEN SPORTS USA EZ LOAD XR200 2 BIKES</strong></p>
                <p>S/ 649.00 <span class="tachado">S/ 899.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/121794367/PORTABICICLETAS-ALLEN-SPORTS-USA-EZ-LOAD-XR200-2-BIKES/121794368"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="comparar.html" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto 3.2.png" alt="Llanta">
                <p class="marca">GENERICO</p>
                <p><strong>Porta Placa Modelo Europeo Machu Picchu Negro</strong></p>
                <p>S/ 31.00 <span class="tachado">S/ 56.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/133487075/Porta-Placa-Modelo-Europeo-Machu-Picchu-Negro/133487076"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="comparar.html" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto 3.3.png" alt="Llanta">
                <p class="marca">GOODYEAR</p>
                <p><strong>Pack x2 Llantas GOODYEAR 185 70R14 Direction Tour</strong></p>
                <p>S/ 279.00 <span class="tachado">S/ 436.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.plazavea.com.pe/k0000013782-llanta-goodyear-185-70r14-direction-tour/p"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="comparar.html" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
            <div class="producto">
                <img src="imgs/foto 3.4.png" alt="Llanta">
                <p class="marca">TRUPER</p>
                <p><strong>Hidrolavadora Eléctrica 1400W 1500 PSI HILA-1500-2 Truper</strong></p>
                <p>S/ 299.00 <span class="tachado">S/ 515.00</span></p>
                <div class="botones-producto">
                    <a href="https://www.falabella.com.pe/falabella-pe/product/114464137/Hidrolavadora-Electrica-1400W-1500-PSI-HILA-1500-2-Truper/114464141"
                        target="_blank" class="btn-comprar">Comprar en tienda</a>
                    <a href="comparar.html" class="btn-comparar">Comparar precios</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ...Footer... -->
    <footer class="footer">
        <div class="footer-container">

            <!-- Exclusive -->
            <div class="footer-section exclusive">
                <h3>Exclusivo</h3>
                <p>Obten 10% de descuento en tu primer pedido</p>
                <div class="subscribe-input">
                    <input type="email" placeholder="Ingresa su correo electronico">
                    <button><i class="fa-solid fa-play"></i></button>
                </div>
            </div>

            <!-- Support -->
            <div class="footer-section support">
                <h3>Soporte</h3>
                <p>Carretera Central Km 11.6,<br>Lima, Perú.</p>
                <p>grupo1@autofinder.com</p>
                <p>+51 999 999 999</p>
            </div>

            <!-- Account -->
            <div class="footer-section account">
                <h3>Cuenta</h3>
                <ul>
                    <li><a href="#">Mi cuenta</a></li>
                    <li><a href="#">Iniciar sesión / Registrarse</a></li>
                    <li><a href="#">Carrrito</a></li>
                    <li><a href="#">Lista de deseos</a></li>
                    <li><a href="#">Tienda</a></li>
                </ul>
            </div>

            <!-- Quick Link -->
            <div class="footer-section quick-link">
                <h3>Centro de Ayuda</h3>
                <ul>
                    <li><a href="#">Politica de privacidad</a></li>
                    <li><a href="#">Terminos de uso</a></li>
                    <li><a href="#">Preguntas frecuentes</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </div>

            <!-- Download App -->
            <div class="footer-section download-app">
                <h3>Descargar App</h3>
                <p>Ahorra $3 con la aplicación Solo para Nuevos Usuarios</p>
                <div class="app-qr">
                    <i class="fa-solid fa-qrcode" alt="QR Code"></i>
                    <div class="store-logos">
                        <div class="google-play">
                            <i class="fa-brands fa-google-play"></i>
                            <span>Google Play</span>
                        </div>
                        <div class="google-play">
                            <i class="fa-brands fa-apple"></i>
                            <span>App Store</span>
                        </div>
                    </div>
                </div>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

        </div>

        <div class="footer-bottom">
            <hr>
            <p>&copy; Copyright Universidad Tecnologica del Perú - 2025. Todos los derechos reservados</p>
        </div>
    </footer>
<script>
  const CART_KEY = 'afinder_cart';   // MISMA clave que en productos.php y carrito.php

  function updateCartBadge() {
    const badge = document.getElementById('cart-count');
    if (!badge) return;

    let count = 0;

    try {
      const raw = localStorage.getItem(CART_KEY);
      if (raw) {
        const cart = JSON.parse(raw);
        if (Array.isArray(cart)) {
          // suma cantidades (qty), no solo número de filas
          count = cart.reduce((acc, item) => acc + (item.qty || 1), 0);
        }
      }
    } catch (e) {
      console.error('Error leyendo carrito:', e);
    }

    badge.textContent = count;
  }

  // Actualizar al cargar la página
  document.addEventListener('DOMContentLoaded', updateCartBadge);

  // Opcional: si abres en otra pestaña y cambias el carrito
  window.addEventListener('storage', (ev) => {
    if (ev.key === CART_KEY) {
      updateCartBadge();
    }
  });
</script>
</body>

</html>