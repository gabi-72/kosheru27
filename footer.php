<footer class="footer-main">
    <div class="footer-grid">
        <div>
            <img src="img/logo/isotipo transparente.png" alt="Kosheru" style="height: 40px; width: auto; margin-bottom: 1rem;">
            <p style="font-size: 0.88rem; color: var(--color-texto-suave); line-height: 1.6; margin-bottom: 1rem; max-width: 300px;">
                Tienda especialista en superalimentos 100% naturales, orgánicos e importados de la más alta pureza y origen garantizado.
            </p>
            <div style="font-size: 0.82rem; color: var(--color-rojo-kosheru); font-weight: 700;">Mosquera, Cundinamarca, Colombia</div>
        </div>

        <div>
            <h4 class="footer-col-title">Navegación</h4>
            <ul class="footer-link-list">
                <li><a href="index.php">Inicio</a></li>
                <li><a href="tienda.php">Tienda</a></li>
                <li><a href="mis-compras.php">Mis Compras</a></li>
                <li><a href="login.php">Mi Cuenta</a></li>
            </ul>
        </div>

        <div>
            <h4 class="footer-col-title">Categorías</h4>
            <ul class="footer-link-list">
                <li>Proteínas Especiales</li>
                <li>Súper Alimentos</li>
                <li>Tés & Condimentos</li>
                <li>Adaptógenos Importados</li>
            </ul>
        </div>

        <div>
            <h4 class="footer-col-title">Contacto</h4>
            <ul class="footer-link-list">
                <li>321 241 1956</li>
                <li>Kosheru750@gmail.com</li>
                <li>Lun-Sáb 8AM - 7PM</li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom-bar">
        &copy; <?= date('Y') ?> Kosheru S.A.S. Todos los derechos reservados.
    </div>
</footer>

<!-- Modal de Detalle de Producto (reutilizable en todo el sitio) -->
<div class="product-detail-overlay" id="productDetailOverlay">
    <div class="product-detail-card">
        <button type="button" class="btn-close-product-detail" id="btnCloseProductDetail" title="Cerrar">&times;</button>
        <div class="product-detail-grid">
            <div class="product-detail-image">
                <img id="pdImage" src="" alt="">
            </div>
            <div class="product-detail-info">
                <span class="product-detail-origin" id="pdOrigin"></span>
                <h2 id="pdName"></h2>
                <div class="product-detail-category" id="pdCategory"></div>
                <p class="product-detail-description" id="pdDescription"></p>
                <div class="product-detail-meta">
                    <div>
                        <span>Precio</span>
                        <strong class="price-highlight" id="pdPrice"></strong>
                    </div>
                    <div>
                        <span>Disponibilidad</span>
                        <strong id="pdStock"></strong>
                    </div>
                </div>
                <button type="button" class="btn-hero-primary" id="pdAddToCart">Añadir al carrito</button>
            </div>
        </div>
    </div>
</div>
