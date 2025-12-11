<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    // Si quieres, redirige al inicio o muestra mensaje
    header("Location: index.php");
    exit();
}

$conexion = new mysqli("localhost", "root", "", "autofinder");
$productosFav = [];

if (!$conexion->connect_error) {
    $sql = "SELECT p.id_producto, p.nombre, p.precio, p.imagen_url
            FROM favoritos f
            JOIN productos p ON p.id_producto = f.id_producto
            WHERE f.id_usuario = ?
            ORDER BY f.fecha_guardado DESC";
    $st = $conexion->prepare($sql);
    $st->bind_param("i", $_SESSION['id_usuario']);
    $st->execute();
    $rs = $st->get_result();
    while ($row = $rs->fetch_assoc()) {
        $productosFav[] = $row;
    }
    $st->close();
    $conexion->close();
}

$pageTitle = "Favoritos - AutoFinder";
include 'partials/header.php';
?>

<link rel="stylesheet" href="css/styles_favoritos.css">
<div id="toast" class="toast"></div>

<main class="fav-page">
  <section class="fav-products">
    <h1 class="fav-title">Mis favoritos</h1>
    <div class="fav-grid" id="favContainer">
      <?php if (empty($productosFav)): ?>
        <p>No tienes productos en favoritos.</p>
      <?php else: ?>
        <?php foreach ($productosFav as $p): ?>
          <div class="fav-card">
            <div class="fav-image-wrapper">
              <img src="<?= htmlspecialchars($p['imagen_url']) ?>"
                   alt="<?= htmlspecialchars($p['nombre']) ?>">
              <button
                class="fav-btn fav-active"
                data-id="<?= (int)$p['id_producto'] ?>"
                aria-label="Quitar de favoritos"
              >
                <i class="fa-regular fa-heart icon-outline"></i>
                <i class="fa-solid fa-heart icon-filled"></i>
              </button>
            </div>
            <h3 class="fav-name"><?= htmlspecialchars($p['nombre']) ?></h3>
            <p class="fav-price">S/ <?= number_format($p['precio'], 2) ?></p>
            <div class="fav-actions">
              <a href="#"
                 class="fav-btn-main primary"
                 data-id="<?= (int)$p['id_producto'] ?>"
                 data-name="<?= htmlspecialchars($p['nombre']) ?>"
                 data-price="<?= $p['precio'] ?>"
                 data-image="<?= htmlspecialchars($p['imagen_url']) ?>">
                Comprar
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</main>
<script>
  // Usa el mismo toast que en otras páginas si ya lo tienes
  function showToast(msg) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(window.__toastTimer);
    window.__toastTimer = setTimeout(() => {
      t.classList.remove('show');
    }, 1800);
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.fav-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const idp = btn.dataset.id;
        if (!idp) return;

        fetch('toggle_favorito.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'id_producto=' + encodeURIComponent(idp)
        })
        .then(r => r.json())
        .then(res => {
          if (res.status === 'ok' && res.action === 'removed') {
            // 1) Eliminar tarjeta del DOM
            const card = btn.closest('.fav-card');
            if (card) card.remove();

            // 2) Si ya no quedan tarjetas, mostrar mensaje vacío
            if (!document.querySelector('.fav-card')) {
              const grid = document.getElementById('favContainer');
              if (grid) {
                grid.innerHTML = '<p>No tienes productos en favoritos.</p>';
              }
            }

            // 3) Actualizar badge del header
            if (typeof updateFavBadge === 'function') {
              // definida en header.php
              updateFavBadge();
            } else {
              const badge = document.getElementById('fav-count');
              if (badge) {
                const current = parseInt(badge.textContent || '0', 10);
                badge.textContent = Math.max(0, current - 1);
              }
            }

            showToast('Eliminado de favoritos');
          } else if (res.status === 'error' && res.msg === 'not_logged') {
            showToast('Debes iniciar sesión para usar favoritos');
          } else {
            showToast('No se pudo actualizar favoritos');
          }
        })
        .catch(err => {
          console.error(err);
          showToast('Error al actualizar favoritos');
        });
      });
    });
  });
</script>

<script>
// Reutilizamos las mismas funciones de productos.php
// (si quieres, podrías moverlas a un .js común)

// Toast
function showToast(message) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = message;
  t.classList.add('show');
  clearTimeout(window.__toastTimer);
  window.__toastTimer = setTimeout(() => {
    t.classList.remove('show');
  }, 1800);
}

// Carrito simple (mismo formato que en productos.php)
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
  const count = cart.reduce((acc, item) => acc + (item.qty || 1), 0);
  const badge = document.getElementById('cart-count');
  if (badge) badge.textContent = count;
}
function addToCart(product) {
  const cart = getCart();
  let index = -1;
  if (product.id) {
    index = cart.findIndex(p => p.id === product.id);
  }
  if (index >= 0) {
    cart[index].qty += 1;
  } else {
    cart.push({ ...product, qty: 1 });
  }
  saveCart(cart);
  showToast('Producto agregado al carrito');
}
updateCartBadge();

function attachBuyButtons() {
  document.querySelectorAll('.btn-comprar').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const idp   = btn.dataset.id || null;
      const name  = btn.dataset.name || 'Producto';
      const price = parseFloat(btn.dataset.price || '0') || 0;
      const image = btn.dataset.image || '';
      addToCart({ id: idp, name, price, image });
    });
  });
}
</script>

<?php include 'partials/footer.php'; ?>
---