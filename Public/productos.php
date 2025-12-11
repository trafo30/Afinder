<?php
// --- LÓGICA PHP INICIAL ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// --- FAVORITOS DEL USUARIO EN BD ---
$userFavIds = [];
if (isset($_SESSION['id_usuario'])) {
    $conexion = new mysqli("localhost", "root", "", "autofinder");
    if (!$conexion->connect_error) {
        $sqlFav = "SELECT id_producto FROM favoritos WHERE id_usuario = ?";
        $st = $conexion->prepare($sqlFav);
        $st->bind_param("i", $_SESSION['id_usuario']);
        $st->execute();
        $rs = $st->get_result();
        while ($row = $rs->fetch_assoc()) {
            $userFavIds[] = (int)$row['id_producto'];
        }
        $st->close();
        $conexion->close();
    }
}

// Título para el <head> de header.php
$pageTitle = $titulo;

// Incluir HEADER global
include 'partials/header.php';
?>

<!-- Variables globales para JS -->
<script>
  const USER_ID  = <?= isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : 'null' ?>;
  const USER_FAVS = <?= json_encode($userFavIds) ?>;
</script>

<!-- CSS adicional de esta página (productos) -->
<link rel="stylesheet" href="css/styles2.css">

<!-- Toast global para mensajes -->
<div id="toast" class="toast"></div>

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

    <!-- paginación dinámica -->
    <div class="pagination" id="pagination"></div>
  </section>
</main>

<script>
const ITEMS_PER_PAGE = 12;
let productsData = [];
let currentPage = 1;
const userFavSet = new Set((USER_FAVS || []).map(Number));

window.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('productContainer');
  const params = new URLSearchParams(location.search);
  const q = (params.get('q') || '').trim();

  const pageParam = parseInt(params.get('page') || '1', 10);
  if (!isNaN(pageParam) && pageParam > 0) {
    currentPage = pageParam;
  }

  const url = new URL("<?= $apiUrl ?>");
  if (q) url.searchParams.set('q', q);

  fetch(url.toString())
    .then(res => {
      if (!res.ok) throw new Error("HTTP " + res.status);
      return res.json();
    })
    .then(data => {
      if (data.bstatus && Array.isArray(data.odata) && data.odata.length > 0) {
        productsData = data.odata;
        renderProductsPage();
      } else {
        container.innerHTML = "<p>No se encontraron productos.</p>";
        document.getElementById('pagination').innerHTML = '';
      }
    })
    .catch(err => {
      console.error("Error en fetch o JSON:", err);
      container.innerHTML = "<p>Error al cargar productos.</p>";
      document.getElementById('pagination').innerHTML = '';
    });
});

// --- Scroll al inicio de la sección de productos ---
function scrollToProductsTop() {
  const productsSection = document.querySelector('.products');
  if (!productsSection) return;

  const currentY = window.scrollY || window.pageYOffset;

  // Posición objetivo (un poco por encima de la sección, para no pegarla al borde)
  const headerOffset = 90; // ajusta si tu header es más alto/bajo
  const targetY =
    productsSection.getBoundingClientRect().top + currentY - headerOffset;

  const distance = Math.abs(targetY - currentY);

  // Si casi ya estoy en el sitio (por ejemplo < 150px), no hagas nada
  if (distance < 150) return;

  window.scrollTo({
    top: targetY,
    behavior: 'smooth',
  });
}

// --- Render de una página de productos ---
function renderProductsPage() {
  const container = document.getElementById('productContainer');
  const totalItems = productsData.length;
  const totalPages = Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));

  if (currentPage > totalPages) currentPage = totalPages;
  if (currentPage < 1) currentPage = 1;

  container.innerHTML = '';

  const start = (currentPage - 1) * ITEMS_PER_PAGE;
  const end = start + ITEMS_PER_PAGE;
  const pageItems = productsData.slice(start, end);

  if (!pageItems.length) {
    container.innerHTML = "<p>No se encontraron productos.</p>";
    document.getElementById('pagination').innerHTML = '';
    return;
  }

  const promises = [];

  pageItems.forEach(item => {
    const card = document.createElement('div');
    card.className = 'product-card';

    const nombre = item.data_name || '';
    const precioNum = parseFloat(item.data_best_price || '0') || 0;
    const imagen = item.data_image || '';
    const precioTexto = `S/ ${precioNum.toFixed(2)}`;

    const p = fetch(
      `get_producto_por_nombre_precio.php?nombre=${encodeURIComponent(nombre)}&precio=${encodeURIComponent(precioNum)}`
    )
      .then(r => r.json())
      .then(prod => {
        const idp = prod.id_producto || '';
        const isFavorite = idp && userFavSet.has(Number(idp));

        card.innerHTML = `
          <div class="product-image-wrapper">
            <img src="${imagen}" alt="${nombre}">
            <button 
              class="fav-btn ${isFavorite ? 'fav-active' : ''}" 
              data-id="${idp}"
              aria-label="Añadir a favoritos"
            >
              <i class="fa-regular fa-heart icon-outline"></i>
              <i class="fa-solid fa-heart icon-filled"></i>
            </button>
          </div>
          <h3>${nombre}</h3>
          <p class="price">${precioTexto}</p>
          <div class="botones-producto">
            <a href="#"
              class="btn-comprar"
              data-id="${idp}"
              data-name="${nombre}"
              data-price="${precioNum}"
              data-image="${imagen}">
              Comprar
            </a>
            <a href="#" class="btn-comparar">Comparar</a>
          </div>
        `;
        container.appendChild(card);
      })
      .catch(err => {
        console.error("Error obteniendo id_producto:", err);
        card.innerHTML = `
          <div class="product-image-wrapper">
            <img src="${imagen}" alt="${nombre}">
          </div>
          <h3>${nombre}</h3>
          <p class="price">${precioTexto}</p>
          <div class="botones-producto">
            <a href="#"
              class="btn-comprar"
              data-name="${nombre}"
              data-price="${precioNum}"
              data-image="${imagen}">
              Comprar
            </a>
            <a href="#" class="btn-comparar">Comparar</a>
          </div>
        `;
        container.appendChild(card);
      });

    promises.push(p);
  });

  Promise.all(promises).then(() => {
    attachBuyButtons();
    attachFavLogic();
    renderPagination(totalPages);
    scrollToProductsTop();
  });
}

// --- Generar botones de paginación ---
function renderPagination(totalPages) {
  const pag = document.getElementById('pagination');
  pag.innerHTML = '';

  if (totalPages <= 1) return;

  const makeBtn = (label, page, options = {}) => {
    const btn = document.createElement('button');
    btn.textContent = label;

    if (options.active) btn.classList.add('active');
    if (options.disabled) {
      btn.disabled = true;
      btn.classList.add('disabled');
    } else {
      btn.addEventListener('click', () => {
        currentPage = page;
        renderProductsPage();
      });
    }
    pag.appendChild(btn);
  };

  makeBtn('<', currentPage - 1, { disabled: currentPage === 1 });
  for (let p = 1; p <= totalPages; p++) {
    makeBtn(String(p), p, { active: p === currentPage });
  }
  makeBtn('>', currentPage + 1, { disabled: currentPage === totalPages });
}

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

// --- Carrito en localStorage (usar id en vez de sku) ---
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

// --- Lógica de favoritos usando BD ---
function attachFavLogic() {
  document.querySelectorAll('.fav-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const idp = btn.dataset.id;

      if (!idp) {
        showToast('No se pudo identificar el producto');
        return;
      }

      if (!USER_ID) {
        showToast('Debes iniciar sesión para usar favoritos');
        // si quieres, aquí puedes abrir el modal de login
        return;
      }

      fetch("toggle_favorito.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id_producto=" + encodeURIComponent(idp)
      })
      .then(r => r.json())
      .then(res => {
        if (res.status === "error" && res.msg === "not_logged") {
          showToast("Debes iniciar sesión para usar favoritos");
          return;
        }

        if (res.action === "added") {
          btn.classList.add("fav-active");
          userFavSet.add(Number(idp));
          adjustFavBadge(1);              // <-- incrementa contador
          showToast("Añadido a favoritos");
        } else if (res.action === "removed") {
          btn.classList.remove("fav-active");
          userFavSet.delete(Number(idp));
          adjustFavBadge(-1);             // <-- disminuye contador
          showToast("Eliminado de favoritos");
        } else {
          showToast("No se pudo actualizar favoritos");
        }
      })
      .catch(err => {
        console.error(err);
        showToast("Error al actualizar favoritos");
      });
    });
  });
}
</script>

<?php
include 'partials/footer.php';
?>