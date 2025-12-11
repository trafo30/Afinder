<?php
if (!isset($pageTitle)) {
    $pageTitle = "AutoFinder";
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Contador de favoritos del usuario en BD
$favCount = 0;
if (isset($_SESSION['id_usuario'])) {
    $cx = new mysqli("localhost", "root", "", "autofinder");
    if (!$cx->connect_error) {
        $sql = "SELECT COUNT(*) AS c FROM favoritos WHERE id_usuario = ?";
        $st  = $cx->prepare($sql);
        $st->bind_param("i", $_SESSION['id_usuario']);
        $st->execute();
        $res = $st->get_result()->fetch_assoc();
        $favCount = (int)($res['c'] ?? 0);
        $st->close();
        $cx->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>  
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

      <?php if (isset($_SESSION['login_error'])): ?>
        <p class="login-error" style="color:red; margin-top:10px;">
          <?= $_SESSION['login_error']; ?>
        </p>

        <script>
          // abrir modal automáticamente si hubo error
          document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("loginModal").style.display = "block";
          });
        </script>

        <?php unset($_SESSION['login_error']); ?>
      <?php endif; ?>
    </div>
  </div>
  

  <!-- Cabecera -->
  <header>
    <div class="logo">
      <a href="index.php">
        <img src="imgs/logo.png" alt="AutoFinder Logo">
      </a>
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
      <div class="icon-item" onclick="window.location.href='favoritos.php'">
        <img src="imgs/corazon1.png" alt="Favoritos">
        <span id="fav-count" class="fav-count"><?= $favCount ?></span>
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
  
  <!-- Script del carrito y favoritos en el header -->
  <script>
    const CART_KEY = 'afinder_cart';

    function updateCartBadge() {
      const badge = document.getElementById('cart-count');
      if (!badge) return;

      let count = 0;

      try {
        const raw = localStorage.getItem(CART_KEY);
        if (raw) {
          const cart = JSON.parse(raw);
          if (Array.isArray(cart)) {
            count = cart.reduce((acc, item) => acc + (item.qty || 1), 0);
          }
        }
      } catch (e) {
        console.error('Error leyendo carrito:', e);
      }

      badge.textContent = count;
    }

    // Ajustar contador de favoritos (se usa desde productos.php y favoritos.php)
    function adjustFavBadge(delta) {
      const badge = document.getElementById('fav-count');
      if (!badge) return;
      let current = parseInt(badge.textContent || '0', 10);
      if (isNaN(current)) current = 0;
      let next = current + delta;
      if (next < 0) next = 0;
      badge.textContent = next;
    }

    document.addEventListener('DOMContentLoaded', updateCartBadge);

    window.addEventListener('storage', (ev) => {
      if (ev.key === CART_KEY) {
        updateCartBadge();
      }
    });
  </script>
