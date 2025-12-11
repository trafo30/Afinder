<?php
session_start();
$pageTitle = "Carrito - AutoFinder";
include 'partials/header.php';
?>

<!-- CSS específico de carrito -->
<link rel="stylesheet" href="css/styles_carrito.css">

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

<?php
// Incluir FOOTER global
include 'partials/footer.php';
?>

<div id="toast" class="toast"></div>

<!-- Modal flotante para Culqi -->
<div id="modalPago" class="modal-pago">
  <div class="modal-pago-overlay"></div>
  <div class="modal-pago-content">
    <button id="cerrarModalPago" class="modal-pago-close">&times;</button>
    <iframe id="pagoFrame" src="" frameborder="0"></iframe>
  </div>
</div>

<script>
  // --- Utilidades base del carrito ---

  function getCart() {
    try {
      const raw = localStorage.getItem('afinder_cart');
      if (!raw) return [];
      const cart = JSON.parse(raw);
      return Array.isArray(cart) ? cart : [];
    } catch (e) {
      console.error('Error leyendo carrito:', e);
      return [];
    }
  }

  function saveCart(cart) {
    try {
      localStorage.setItem('afinder_cart', JSON.stringify(cart));
    } catch (e) {
      console.error('Error guardando carrito:', e);
    }
  }

  function updateCartBadge() {
    const cart = getCart();
    const count = cart.reduce((acc, item) => acc + (item.qty || 1), 0);
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
      btnPagar.dataset.amountCents = '0';
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
    document.getElementById('btnPagar').dataset.amountCents = Math.round(total * 100); // para Culqi
    updateCartBadge();
  }

  // --- Abrir modal Culqi con iframe ---
  const modalPago   = document.getElementById('modalPago');
  const pagoFrame   = document.getElementById('pagoFrame');
  const cerrarModal = document.getElementById('cerrarModalPago');
  const overlayPago = document.querySelector('.modal-pago-overlay');

  document.getElementById('btnPagar').addEventListener('click', (e) => {
    e.preventDefault();

    const amountCents = parseInt(
      document.getElementById('btnPagar').dataset.amountCents || '0',
      10
    );

    if (!amountCents || amountCents <= 0) {
      showToast('El carrito está vacío o el total es inválido');
      return;
    }

    // Datos del usuario desde la sesión PHP
    const nombre   = "<?= isset($_SESSION['nombre'])   ? addslashes($_SESSION['nombre'])   : '' ?>";
    const apellido = "<?= isset($_SESSION['apellido']) ? addslashes($_SESSION['apellido']) : '' ?>";
    const email    = "<?= isset($_SESSION['correo'])   ? addslashes($_SESSION['correo'])   : '' ?>";
    const celular  = "<?= isset($_SESSION['celular'])  ? addslashes($_SESSION['celular'])  : '' ?>";

    const params = new URLSearchParams({
      amount:   amountCents.toString(),
      nombre:   nombre,
      apellido: apellido,
      email:    email,
      celular:  celular,
    });

    const url = `http://localhost:4242?${params.toString()}`;

    // cargar la página de Culqi dentro del iframe y mostrar modal
    pagoFrame.src = url;
    modalPago.classList.add('open');
  });

  function cerrarModalPagoFn() {
    modalPago.classList.remove('open');
    pagoFrame.src = ''; // opcional: limpiar iframe
  }

  cerrarModal.addEventListener('click', cerrarModalPagoFn);
  overlayPago.addEventListener('click', cerrarModalPagoFn);

  // Inicializar
  updateCartBadge();
  renderCart();
</script>
