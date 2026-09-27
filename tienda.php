<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/auth.php';

$search_param = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';
$cat_param = isset($_GET['categoria']) ? htmlspecialchars(trim($_GET['categoria'])) : 'Todos';

$categorias = [
    'Todos' => 'Todos',
    'Proteínas Especiales' => 'Proteínas',
    'Súper Alimentos' => 'Súper Alimentos',
    'Tés & Condimentos' => 'Tés & Condimentos',
    'Vegetarianos' => 'Vegetarianos',
    'sin gluten' => 'Sin Gluten',
    'Veganos' => 'Veganos',
    'reemplazos de carne' => 'Reemplazos de Carne',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Productos - Kosheru</title>
    <script>
        (function () {
            var saved = localStorage.getItem('kosheru_theme');
            document.documentElement.setAttribute('data-theme', saved === 'dark' ? 'dark' : 'light');
        })();
    </script>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="img/logos/Isotipo transparente.png" type="image/png">
</head>
<body>

    <?php include __DIR__ . '/includes/header.php'; ?>

    <!-- ===================== ENCABEZADO + BREADCRUMB ===================== -->
    <section class="tienda-hero">
        <div class="tienda-breadcrumb"><a href="index.php">Inicio</a> / <span>Tienda</span></div>
        <h1 class="tienda-hero-title">Tienda de Superalimentos</h1>
        <p class="tienda-hero-desc">
            Encuentra productos naturales, orgánicos y veganos, importados y de productores locales. Filtra por estilo de vida o busca por nombre, origen o categoría.
        </p>
    </section>

    <!-- ===================== CHIPS RÁPIDOS ===================== -->
    <div class="tienda-quickchips" id="quickChipsBar">
        <?php foreach ($categorias as $value => $label): ?>
            <button type="button" class="quickchip <?= ($cat_param === $value || (empty($cat_param) && $value === 'Todos')) ? 'active' : '' ?>" data-category="<?= htmlspecialchars($value) ?>">
                <?= htmlspecialchars($label) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- ===================== SIDEBAR + GRID ===================== -->
    <div class="tienda-layout">
        <aside class="filter-sidebar-card">
            <div class="filter-sidebar-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" id="tiendaSearchInput" placeholder="Buscar producto..." value="<?= $search_param ?>">
            </div>

            <div class="filter-group">
                <div class="filter-group-title">Categoría</div>
                <div id="sidebarCategoryList">
                    <?php foreach ($categorias as $value => $label): ?>
                        <div class="filter-option-row <?= ($cat_param === $value || (empty($cat_param) && $value === 'Todos')) ? 'active' : '' ?>" data-category="<?= htmlspecialchars($value) ?>">
                            <span class="radio-dot"></span> <?= htmlspecialchars($label) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="button" class="btn-clear-filters" id="btnClearFilters">Limpiar filtros</button>
        </aside>

        <main>
            <div class="tienda-results-bar">
                <div class="tienda-results-count" id="tiendaResultsCount">Cargando productos...</div>
                <select class="tienda-sort-select" id="tiendaSortSelect">
                    <option value="">Más relevantes</option>
                    <option value="precio_asc">Precio: menor a mayor</option>
                    <option value="precio_desc">Precio: mayor a menor</option>
                    <option value="nombre_az">Nombre: A-Z</option>
                </select>
            </div>

            <div class="products-grid" id="tiendaProductsGrid">
                <!-- Renderizado dinámico vía JavaScript -->
            </div>
        </main>
    </div>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="js/app.js"></script>
    <script src="js/theme.js"></script>
    <script src="js/header.js"></script>
    <script src="js/productos.js"></script>
    <script src="js/product-detail.js"></script>
    <script src="js/carrito.js"></script>
    <script src="js/checkout.js"></script>
    <script src="js/wishlist.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let activeCategory = '<?= $cat_param ?>';
            let activeSearch = '<?= $search_param ?>';
            let activeSort = '';

            const quickChips = document.querySelectorAll('#quickChipsBar .quickchip');
            const sidebarRows = document.querySelectorAll('#sidebarCategoryList .filter-option-row');
            const sortSelect = document.getElementById('tiendaSortSelect');
            const searchInput = document.getElementById('tiendaSearchInput');

            function refreshGrid() {
                const cat = activeCategory === 'Todos' ? '' : activeCategory;
                KosheruProductos.renderGrid('tiendaProductsGrid', cat, activeSearch, activeSort);
            }

            function setActiveCategory(cat) {
                activeCategory = cat;
                quickChips.forEach(c => c.classList.toggle('active', c.dataset.category === cat));
                sidebarRows.forEach(r => r.classList.toggle('active', r.dataset.category === cat));
                refreshGrid();
            }

            quickChips.forEach(chip => chip.addEventListener('click', () => setActiveCategory(chip.dataset.category)));
            sidebarRows.forEach(row => row.addEventListener('click', () => setActiveCategory(row.dataset.category)));

            if (sortSelect) {
                sortSelect.addEventListener('change', () => {
                    activeSort = sortSelect.value;
                    refreshGrid();
                });
            }

            if (searchInput) {
                let debounceTimer = null;
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        activeSearch = e.target.value.trim();
                        refreshGrid();
                    }, 300);
                });
            }

            refreshGrid();
        });

        document.addEventListener("DOMContentLoaded", () => {
            const urlParams = new URLSearchParams (window.location.search);
            const categoriaURL = urlParams.get ('cat');
            if (categoriaURL){
                const targetElement = document.querySelector('[data-category="${categoriaURL}"]');
                if (targetElement) {
                    targetElement.click();
                    targetElement.scrollIntoView({behavior:'smooth', block: 'center'});
                }
            }
        })
    </script>
</body>
</html>
