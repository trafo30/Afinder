<?php
session_start();

// Modo de visualización: por categoría o búsqueda global
$modo = $_GET['modo'] ?? 'categoria';      // 'categoria' | 'busqueda'
$cat  = $_GET['cat']  ?? 'llantas';        // slug de categoría (baterias, aceite, etc.)
$q    = trim($_GET['q'] ?? '');

// Definir título y endpoint según el modo
if ($modo === 'busqueda') {
    $titulo = "Búsqueda: " . ($q !== '' ? $q : "Todos") . " - AutoFinder";
    $apiUrl = "http://127.0.0.1:8000/buscar";
} else {
    $titulo = ucfirst($cat) . " - AutoFinder";
    $apiUrl = "http://127.0.0.1:8000/categoria/{$cat}";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo) ?></title>
  <link rel="stylesheet" href="css/styles2.css">
  <link rel="stylesheet" href="css/styles.css">
  <script defer src="js/comparar.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<div id="toast" class="toast"></div>
<body>
<header>
  <div class="logo">
    <a href="index.php">
      <img src="imgs/logo.png" alt="AutoFinder Logo">
    </a>
  </div>

  <!-- Buscador GLOBAL: ignora la categoría, busca en todas -->
<form class="search" action="productos.php" method="get">
  
  <input type="hidden" name="modo" value="busqueda">

  <div class="search-box">
      <i class="fa-solid fa-magnifying-glass search-icon"></i>

      <input type="text"
             name="q"
             class="search-input"
             placeholder="Buscar productos..."
             value="<?= htmlspecialchars($q) ?>">

      <button type="submit" class="search-btn">Buscar</button>
  </div>

</form>
  <div class="icons">
    <div class="icon-item"><img src="imgs/corazon1.png" alt="Favoritos"><span>Favoritos</span></div>

<a href="carrito.php" class="icon-item carrito-icon" style="text-decoration:none;">
      <img src="imgs/carrito-de-compras.png" alt="Carrito">
      <span>Carrito</span>
      <span id="cart-count" class="cart-badge">0</span>
    </a>

        
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

<main class="container">
  <aside class="filters">
    <h2>Categorías</h2>
    <ul>
      <li>
        <a href="?modo=categoria&cat=baterias"
           <?= $modo === 'categoria' && $cat === 'baterias' ? 'class="active"' : '' ?>>
           Baterías para autos
        </a>
      </li>
      <li>
        <a href="?modo=categoria&cat=Aceite"
           <?= $modo === 'categoria' && $cat === 'Aceite' ? 'class="active"' : '' ?>>
           Aceite
        </a>
      </li>
      <li>
        <a href="?modo=categoria&cat=Aditivos"
           <?= $modo === 'categoria' && $cat === 'Aditivos' ? 'class="active"' : '' ?>>
           Aditivos
        </a>
      </li>
      <li>
        <a href="?modo=categoria&cat=Accesorios para Exterior"
           <?= $modo === 'categoria' && $cat === 'Accesorios para Exterior' ? 'class="active"' : '' ?>>
           Accesorios para exterior
        </a>
      </li>
      <li>
        <a href="?modo=categoria&cat=Accesorios para interior"
           <?= $modo === 'categoria' && $cat === 'Accesorios para interior' ? 'class="active"' : '' ?>>
           Accesorios para interior
        </a>
      </li>
      <li>
        <a href="?modo=categoria&cat=rodamientos"
           <?= $modo === 'categoria' && $cat === 'rodamientos' ? 'class="active"' : '' ?>>
           Rodamientos
        </a>
      </li>
      <li>
        <a href="?modo=categoria&cat=filtro-aire"
           <?= $modo === 'categoria' && $cat === 'filtro-aire' ? 'class="active"' : '' ?>>
           Filtro de aire
        </a>
      </li>
      <li>
        <a href="?modo=categoria&cat=espejos"
           <?= $modo === 'categoria' && $cat === 'espejos' ? 'class="active"' : '' ?>>
           Espejos laterales
        </a>
      </li>
      <li>
        <a href="?modo=categoria&cat=llantas"
           <?= $modo === 'categoria' && $cat === 'llantas' ? 'class="active"' : '' ?>>
           Llantas
        </a>
      </li>
    </ul>
  </aside>

  <section class="products">
    <?php if ($modo === 'busqueda'): ?>
      <h1>Resultados para "<?= htmlspecialchars($q) ?>"</h1>
    <?php else: ?>
      <h1><?= ucfirst($cat) ?> para Autos</h1>
    <?php endif; ?>

    <div class="product-grid" id="productContainer"></div>
    <div class="pagination">
      <button>&lt;</button><button class="active">1</button><button>2</button><button>3</button><button>&gt;</button>
    </div>
  </section>
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
    <hr>
    <p>&copy; AutoFinder 2025 - Todos los derechos reservados</p>
  </div>
</footer>

<script>
window.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('productContainer');
  const params = new URLSearchParams(location.search);
  const q = (params.get('q') || '').trim();

  const url = new URL("<?= $apiUrl ?>");
  if (q) url.searchParams.set('q', q);

  console.log("Llamando a API:", url.toString());

  fetch(url.toString())
    .then(res => {
      console.log("HTTP status:", res.status);
      if (!res.ok) {
        throw new Error("HTTP " + res.status);
      }
      return res.json();
    })
    .then(data => {
      console.log("JSON recibido:", data);

      if (data.bstatus && Array.isArray(data.odata) && data.odata.length > 0) {
        container.innerHTML = "";
        data.odata.forEach(item => {
          const card = document.createElement('div');
          card.className = 'product-card';
          card.innerHTML = `
            <img src="${item.data_image}" alt="${item.data_name}">
            <h3>${item.data_name}</h3>
            <p class="price">S/ ${item.data_best_price}</p>
            <div class="botones-producto">
              <a href="#"
                class="btn-comprar"
                data-sku="${item.data_sku || ''}"
                data-name="${item.data_name}"
                data-price="${item.data_best_price}"
                data-image="${item.data_image}">
                Comprar
              </a>
              <a href="#" class="btn-comparar">Comparar</a>
            </div>`;
          container.appendChild(card);
        });

        attachBuyButtons();
      } else {
        container.innerHTML = "<p>No se encontraron productos.</p>";
      }
    })
    .catch(err => {
      console.error("Error en fetch o JSON:", err);
      container.innerHTML = "<p>Error al cargar productos.</p>";
    });
});
// --- Toast reutilizable ---
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

// --- Carrito en localStorage (solo para contar y guardar productos) ---
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

function addToCart(product) {
  const cart = getCart();
  const index = cart.findIndex(p => p.sku === product.sku && p.sku !== '');

  if (index >= 0) {
    cart[index].qty += 1;
  } else {
    cart.push({ ...product, qty: 1 });
  }
  saveCart(cart);
  showToast('Producto agregado al carrito');
}

// Al cargar la página, refrescar badge
updateCartBadge();

// Conectar botones de compra cuando los productos ya están pintados
function attachBuyButtons() {
  document.querySelectorAll('.btn-comprar').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const sku   = btn.dataset.sku || '';
      const name  = btn.dataset.name || 'Producto';
      const price = parseFloat(btn.dataset.price || '0') || 0;
      const image = btn.dataset.image || '';

      addToCart({ sku, name, price, image });
    });
  });
}

</script>

</body>
</html>