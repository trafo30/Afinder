<?php
session_start();
$titulo = "Carrito - AutoFinder";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo) ?></title>
  <link rel="stylesheet" href="css/styles2.css">
    <link rel="stylesheet" href="css/styles_carrito.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
<header>
  <div class="logo">
    <a href="index.php">
      <img src="imgs/logo.png" alt="AutoFinder Logo">
    </a>
  </div>
  <form class="search" action="productos.php" method="get">
    <input type="hidden" name="modo" value="busqueda">
    <input type="text" name="q" placeholder="Buscar productos...">
  </form>
  <div class="icons">
    <div class="icon-item"><img src="imgs/corazon1.png" alt="Favoritos"><span>Favoritos</span></div>

    <a href="carrito.php" class="icon-item carrito-icon" style="text-decoration:none;">
      <img src="imgs/carrito-de-compras.png" alt="Carrito">
      <span>Carrito</span>
      <span id="cart-count" class="cart-badge">0</span>
    </a>

    <?php if (isset($_SESSION['usuario'])): ?>
      <div class="welcome"><p>Bienvenido</p><p><strong><?= htmlspecialchars($_SESSION['nombre']) ?></strong></p></div>
      <a href="logout.php" class="login-button btn-salir">Salir</a>
    <?php else: ?>
      <a href="login.php" class="login-button">Ingresar</a>
    <?php endif; ?>
  </div>
</header>

<main class="carrito-page">
  <div class="carrito-header">
    <h1>Carrito de compras</h1>
    <a href="productos.php" class="btn btn-outline">
      <i class="fa fa-arrow-left"></i> Seguir comprando
    </a>
  </div>

  <table class="carrito-table" id="carritoTabla">
    <thead>
      <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th style="text-align:right;">Subtotal</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="carritoBody">
      <!-- filas generadas por JS -->
    </tbody>
  </table>

  <div class="carrito-empty" id="carritoVacio" style="display:none;">
    Tu carrito está vacío. Agrega productos desde el catálogo.
  </div>

  <div class="carrito-actions">
    <div class="carrito-total">
      Total: <span id="carritoTotal">S/ 0.00</span>
    </div>
    <button id="btnPagar" class="btn btn-primary" disabled>
      Proceder al pago
    </button>
  </div>
</main>

<footer class="footer">
  <div class="footer-container">
    <div class="footer-section support">
      <h3>Soporte</h3>
      <p>Carretera Central Km 11.6, Lima, Perú.</p>
      <p>grupo1@autofinder.com</p>
      <p>+51 999 999 999</p>
    </div>
  </div>
  <div class="footer-bottom">
    <hr><p>&copy; AutoFinder 2025 - Todos los derechos reservados</p>
  </div>
</footer>

<div id="toast" class="toast"></div>

<script>
  // --- Utilidades carrito (mismo formato que en productos.php) ---
  const CART_KEY = 'afinder_cart';

  function getCart() {
    try {
      const raw = localStorage.getItem(CART_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch {
      return [];
    }
  }

  function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    updateCartBadge();
  }

  function updateCartBadge() {
    const cart = getCart();
    const count = cart.reduce((acc, item) => acc + item.qty, 0);
    const badge = document.getElementById('cart-count');
    if (badge) badge.textContent = count;
  }

  function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(window.__toastTimer);
    window.__toastTimer = setTimeout(() => {
      t.classList.remove('show');
    }, 1800);
  }

  // --- Pintar carrito en tabla ---
  function renderCart() {
    const cart = getCart();
    const tbody = document.getElementById('carritoBody');
    const vacio = document.getElementById('carritoVacio');
    const tabla = document.getElementById('carritoTabla');
    const totalSpan = document.getElementById('carritoTotal');
    const btnPagar = document.getElementById('btnPagar');

    tbody.innerHTML = '';

    if (!cart.length) {
      tabla.style.display = 'none';
      vacio.style.display = 'block';
      totalSpan.textContent = 'S/ 0.00';
      btnPagar.disabled = true;
      updateCartBadge();
      return;
    }

    tabla.style.display = 'table';
    vacio.style.display = 'none';
    btnPagar.disabled = false;

    let total = 0;

    cart.forEach((item, idx) => {
      const tr = document.createElement('tr');

      const subtotal = (item.price || 0) * item.qty;
      total += subtotal;

      tr.innerHTML = `
        <td>
          <div class="carrito-item">
            <img src="${item.image || ''}" alt="${item.name}">
            <div>${item.name}</div>
          </div>
        </td>
        <td>S/ ${Number(item.price || 0).toFixed(2)}</td>
        <td>
          <input type="number" class="qty-input" min="1" value="${item.qty}">
        </td>
        <td style="text-align:right;">S/ ${subtotal.toFixed(2)}</td>
        <td style="text-align:center;">
          <button class="btn btn-outline btn-remove" style="padding:4px 10px;font-size:12px;">
            <i class="fa fa-trash"></i>
          </button>
        </td>
      `;

      // cambiar cantidad
      const qtyInput = tr.querySelector('.qty-input');
      qtyInput.addEventListener('change', () => {
        let val = parseInt(qtyInput.value || '1', 10);
        if (isNaN(val) || val < 1) val = 1;
        cart[idx].qty = val;
        saveCart(cart);
        renderCart();
      });

      // eliminar
      const btnRemove = tr.querySelector('.btn-remove');
      btnRemove.addEventListener('click', (e) => {
        e.preventDefault();
        cart.splice(idx, 1);
        saveCart(cart);
        renderCart();
        showToast('Producto eliminado del carrito');
      });

      tbody.appendChild(tr);
    });

    totalSpan.textContent = 'S/ ' + total.toFixed(2);
    btnPagar.dataset.amountCents = Math.round(total * 100); // para Culqi
    updateCartBadge();
  }

  // --- Ir a Culqi con el total ---
  document.getElementById('btnPagar').addEventListener('click', () => {
    const amountCents = parseInt(
      document.getElementById('btnPagar').dataset.amountCents || '0',
      10
    );
    if (!amountCents || amountCents <= 0) {
      showToast('El carrito está vacío o el total es inválido');
      return;
    }
    // Culqi corre en http://localhost:4242 y lee ?amount=
    const url = `http://localhost:4242?amount=${amountCents}`;
    window.location.href = url;
  });

  // Inicializar
  updateCartBadge();
  renderCart();
</script>
</body>
</html>