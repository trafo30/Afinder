<?php
session_start();
$cat = $_GET['cat'] ?? 'llantas'; // valor por defecto
$titulo = ucfirst($cat) . " - AutoFinder";

// aquí defines endpoints API según categoría
$endpoints = [
  'baterias'       => 'http://localhost:8000/baterias',
  'filtros-aceite' => 'http://localhost:8000/filtros-aceite',
  'focos'          => 'http://localhost:8000/focos',
  'amortiguadores' => 'http://localhost:8000/amortiguadores',
  'rotulas'        => 'http://localhost:8000/rotulas',
  'rodamientos'    => 'http://localhost:8000/rodamientos',
  'filtro-aire'    => 'http://localhost:8000/filtro-aire',
  'espejos'        => 'http://localhost:8000/espejos',
  'llantas'        => 'http://localhost:8000/llantas'
];

$cat = $_GET['cat'] ?? 'llantas';
$apiUrl = "http://127.0.0.1:8000/categoria/{$cat}";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo) ?></title>
  <link rel="stylesheet" href="css/styles2.css">
  <script defer src="js/comparar.js"></script>
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
  <input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>">
  <input type="text" name="q" placeholder="Buscar productos..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
</form>
  <div class="icons">
    <div class="icon-item"><img src="imgs/corazon1.png" alt="Favoritos"><span>Favoritos</span></div>
    <div class="icon-item"><img src="imgs/carrito-de-compras.png" alt="Carrito"><span>Carrito</span></div>
    <?php if (isset($_SESSION['usuario'])): ?>
      <div class="welcome"><p>Bienvenido</p><p><strong><?= htmlspecialchars($_SESSION['nombre']) ?></strong></p></div>
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
      <li><a href="?cat=baterias"       <?= $cat==='baterias'?'class="active"':'' ?>>Baterías para autos</a></li>
      <li><a href="?cat=filtros-aceite" <?= $cat==='filtros-aceite'?'class="active"':'' ?>>Filtros de aceite</a></li>
      <li><a href="?cat=focos"          <?= $cat==='focos'?'class="active"':'' ?>>Focos y ampolletas</a></li>
      <li><a href="?cat=amortiguadores" <?= $cat==='amortiguadores'?'class="active"':'' ?>>Amortiguadores</a></li>
      <li><a href="?cat=rotulas"        <?= $cat==='rotulas'?'class="active"':'' ?>>Rótulas</a></li>
      <li><a href="?cat=rodamientos"    <?= $cat==='rodamientos'?'class="active"':'' ?>>Rodamientos</a></li>
      <li><a href="?cat=filtro-aire"    <?= $cat==='filtro-aire'?'class="active"':'' ?>>Filtro de aire</a></li>
      <li><a href="?cat=espejos"        <?= $cat==='espejos'?'class="active"':'' ?>>Espejos laterales</a></li>
      <li><a href="?cat=llantas"        <?= $cat==='llantas'?'class="active"':'' ?>>Llantas</a></li>
    </ul>
  </aside>

  <section class="products">
    <h1><?= ucfirst($cat) ?> para Autos</h1>
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
    <hr><p>&copy; AutoFinder 2025 - Todos los derechos reservados</p>
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

      // data = { bstatus, smessage, odata }
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
              <a href="#" target="_blank" class="btn-comprar">Comprar</a>
              <a href="#" class="btn-comparar">Comparar</a>
            </div>`;
          container.appendChild(card);
        });
      } else {
        container.innerHTML = "<p>No se encontraron productos.</p>";
      }
    })
    .catch(err => {
      console.error("Error en fetch o JSON:", err);
      container.innerHTML = "<p>Error al cargar productos.</p>";
    });
});
</script>

</body>
</html>
