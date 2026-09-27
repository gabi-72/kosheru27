<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/modelo/connection.php';

$carousel_products = [];
$carousel_query = mysqli_query($conn, "SELECT id, name, category, origin, price, stock, image, description, created_at FROM products ORDER BY RAND() LIMIT 8");
if ($carousel_query) {
    while ($row = mysqli_fetch_assoc($carousel_query)) {
        $row['price_formatted'] = formatCOP($row['price']);
        $carousel_products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kosheru | Superalimentos Naturales & Importados en Mosquera</title>
    <meta name="description" content="Superalimentos orgánicos, naturales e importados en Mosquera, Cundinamarca. Tofu, Tempeh, Spirulina, Matcha y más.">
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

    <?php include_once __DIR__ . '/includes/header.php'; ?>

    <!-- ===================== BANNER PROMOCIONAL ===================== -->
    <section class="promo-banner-section">
        <div class="promo-banner-viewport">
            <div class="promo-track" id="promoTrack">
                <div class="promo-slide" style="background-color: var(--color-bg-panel);">
                    <div class="promo-slide-text">
                        <span class="promo-eyebrow">Nuevo ingreso</span>
                        <h2>Matcha Ceremonial Grado A+</h2>
                        <p>Directo de Uji, Japón. Molienda en piedra volcánica para un sabor puro y una L-teanina concentrada.</p>
                        <a href="tienda.php?categoria=Tés+%26+Condimentos" class="btn-hero-primary">Ver producto →</a>
                    </div>
                    <div class="promo-slide-img"><img src="img/Matcha Ceremonial Grado A+.png" alt="Matcha Ceremonial"></div>
                </div>
                <div class="promo-slide" style="background-color: var(--color-bg-panel);">
                    <div class="promo-slide-text">
                        <span class="promo-eyebrow">Envíos</span>
                        <h2>Entrega gratis en Mosquera</h2>
                        <p>En compras superiores a $80.000 COP. Pide hoy y recíbelo en menos de 24 horas.</p>
                        <a href="tienda.php" class="btn-hero-primary">Ver tienda →</a>
                    </div>
                    <div class="promo-slide-img"><img src="img/Espirulina Azul en Polvo.jpg" alt="Spirulina Azul"></div>
                </div>
                <div class="promo-slide" style="background-color: var(--color-bg-panel);">
                    <div class="promo-slide-text">
                        <span class="promo-eyebrow">Más vendido</span>
                        <h2>Tofu Ahumado Extra Firme</h2>
                        <p>Alta pureza, rico en proteína vegetal, sin OMG ni conservantes. Origen Japón.</p>
                        <a href="tienda.php?categoria=Proteínas+Especiales" class="btn-hero-primary">Ver producto →</a>
                    </div>
                    <div class="promo-slide-img"><img src="img/Tofu Ahumado Premium.png" alt="Tofu Ahumado"></div>
                </div>
            </div>

            <button type="button" class="promo-arrow promo-arrow--prev" id="promoPrevBtn" aria-label="Anterior">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button type="button" class="promo-arrow promo-arrow--next" id="promoNextBtn" aria-label="Siguiente">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </button>
            <div class="promo-dots" id="promoDots"></div>
        </div>
    </section>

    <!-- ===================== HERO ===================== -->
    <section class="hero-section">
        <div class="hero-grid">
            <div class="hero-visual">
                <span class="badge-producto-estrella">Producto Estrella</span>
                <img class="hero-product-img" src="img/Tofu Ahumado Premium.png" alt="Tofu Ahumado Extra Firme">
                <div class="hero-featured-caption">
                    <div class="featured-info">
                        <p class="featured-origin">Origen: Japón 🇯🇵</p>
                        <h3 class="featured-title">Tofu Ahumado Extra Firme</h3>
                        <span class="featured-price">$18.900 COP</span>
                    </div>
                    <button type="button"
                    class="btn-add-featured"
                    data-id="1"
                    data-name="Tofu Orgánico Extra Firme"
                    data-price="18900"
                    data-image="img/tofu.jpg"
                    data-origin="JAPÓN 🇯🇵"
                    title="Agregar al carrito">+</button>
                    </div>
            </div>

            <div class="hero-content">
                <div class="hero-badge-pill">Superalimentos de origen</div>
                <h1 class="hero-main-title">
                    Alimentos importados para una vida<br>
                    <span class="text-orange">más consciente</span>
                </h1>
                <p class="hero-description-text">
                    En Kosheru traemos productos naturales y de origen que se distinguen por su calidad, sabor y valor nutricional. Desde tofu y tempeh hasta matcha y otros superalimentos, te ayudamos a elegir mejor cada día.
                </p>
                <div class="hero-button-group">
                    <a href="tienda.php" class="btn-hero-primary">Ver tienda</a>
                    <a href="#nosotros" class="btn-hero-secondary">Sobre nosotros</a>
                </div>
                <div class="hero-trust-row">
                    <div class="hero-trust-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        100% Orgánico
                    </div>
                    <div class="hero-trust-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Origen garantizado
                    </div>
                    <div class="hero-trust-item">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        Entrega en Mosquera
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== FRANJA DE FEATURES ===================== -->
    <section class="feature-strip">
        <div class="feature-strip-grid">
            <div class="feature-strip-item">
                <div class="feature-strip-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M12 8c0-3.3-2.7-6-6-6 0 3.3 2.7 6 6 6zM12 14c0-3.3 2.7-6 6-6 0 3.3-2.7 6-6 6z"/></svg></div>
                <div>
                    <div class="feature-strip-title">100% Natural</div>
                    <div class="feature-strip-desc">Sin aditivos ni conservantes</div>
                </div>
            </div>
            <div class="feature-strip-item">
                <div class="feature-strip-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M2 12h20M12 2a15 15 0 010 20 15 15 0 010-20z"/></svg></div>
                <div>
                    <div class="feature-strip-title">Origen garantizado</div>
                    <div class="feature-strip-desc">Trazabilidad de cada producto</div>
                </div>
            </div>
            <div class="feature-strip-item">
                <div class="feature-strip-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg></div>
                <div>
                    <div class="feature-strip-title">Alto valor nutricional</div>
                    <div class="feature-strip-desc">Seleccionados por calidad</div>
                </div>
            </div>
            <div class="feature-strip-item">
                <div class="feature-strip-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12l2-6h14l2 6M5 12v6h14v-6M9 18v2M15 18v2"/><circle cx="7" cy="18" r="1.5"/><circle cx="17" cy="18" r="1.5"/></svg></div>
                <div>
                    <div class="feature-strip-title">Entrega en Mosquera</div>
                    <div class="feature-strip-desc">Envíos a municipios aledaños</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== PANEL ARENA: CHIPS ESTILO DE VIDA ===================== -->
    <section class="color-panel">
        <div class="color-panel-header">
            <div>
                <p class="color-panel-tag">Encuentra lo tuyo</p>
                <h2 class="color-panel-title">Compra por estilo de vida</h2>
            </div>
            <a href="tienda.php" class="color-panel-link">Ver toda la tienda →</a>
        </div>
        <div class="chips-row">
            <a href="tienda.php?categoria=Veganos" class="chip-lifestyle">
                <div class="chip-lifestyle-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 20A7 7 0 019.8 6.1C15.5 5 17 9.5 17 14a7 7 0 01-6 6z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 11 13.51 11 11"/></svg></div>
                <span class="chip-lifestyle-label">Veganos</span>
            </a>
            <a href="tienda.php?categoria=sin+gluten" class="chip-lifestyle">
                <div class="chip-lifestyle-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M6 6l12 12"/></svg></div>
                <span class="chip-lifestyle-label">Sin Gluten</span>
            </a>
            <a href="tienda.php?categoria=Proteínas+Especiales" class="chip-lifestyle">
                <div class="chip-lifestyle-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6.5 6.5l11 11M4 4l2.5 2.5M17.5 17.5L20 20M9 4l2 2M13 18l2 2"/><circle cx="4.5" cy="4.5" r="2"/><circle cx="19.5" cy="19.5" r="2"/></svg></div>
                <span class="chip-lifestyle-label">Proteínas</span>
            </a>
            <a href="tienda.php?categoria=Súper+Alimentos" class="chip-lifestyle">
                <div class="chip-lifestyle-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M12 8c0-3.3-2.7-6-6-6 0 3.3 2.7 6 6 6zM12 14c0-3.3 2.7-6 6-6 0 3.3-2.7 6-6 6z"/></svg></div>
                <span class="chip-lifestyle-label">Superalimentos</span>
            </a>
            <a href="tienda.php?categoria=Tés+%26+Condimentos" class="chip-lifestyle">
                <div class="chip-lifestyle-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10h13v4a5 5 0 01-5 5H8a5 5 0 01-5-5v-4z"/><path d="M16 11h2a2 2 0 010 4h-2"/></svg></div>
                <span class="chip-lifestyle-label">Tés e Infusiones</span>
            </a>
            <a href="tienda.php?categoria=reemplazos+de+carne" class="chip-lifestyle">
                <div class="chip-lifestyle-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M8 12a4 4 0 108 0 4 4 0 00-8 0z"/></svg></div>
                <span class="chip-lifestyle-label">Reemplazos</span>
            </a>
        </div>
    </section>

    <!-- ===================== CARRUSEL DESTACADOS ===================== -->
    <section class="carousel-section">
        <div class="section-title-container">
            <p class="section-subtitle-tag">Selección de origen</p>
            <h2 class="section-headline">Nuestros Superalimentos Destacados</h2>
        </div>

        <div class="carousel-container">
            <button type="button" class="carousel-btn prev-btn" id="btnPrevSlide" title="Anterior">
                <img src="img/icons/caret-big-left.svg" alt="Anterior" class="carousel-arrow-icon" aria-hidden="true">
            </button>
            <div class="carousel-track-wrapper">
                <div class="carousel-track" id="featuredCarouselTrack">
                    <?php if (!empty($carousel_products)): ?>
                        <?php foreach ($carousel_products as $p): ?>
                            <div class="product-card" data-id="<?= htmlspecialchars($p['id']) ?>" data-category="<?= htmlspecialchars($p['category']) ?>">
                                <div class="product-origin-tag"><?= htmlspecialchars($p['origin']) ?></div>
                                <div class="product-image-container" onclick='KosheruProductDetail.open(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)' style="cursor:pointer;" title="Ver detalle del producto">
                                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy" onerror="this.src='imagenes/logo/logo.svg'">
                                </div>
                                <h3 class="product-name"><?= htmlspecialchars($p['name']) ?></h3>
                                <p class="product-description"><?= htmlspecialchars($p['description']) ?></p>
                                <div class="product-footer">
                                    <div class="product-price"><?= htmlspecialchars($p['price_formatted']) ?></div>
                                    <button type="button" class="btn-add-cart" onclick='KosheruCart.addToCart(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)' title="Agregar al carrito">+</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <h3>No hay productos disponibles</h3>
                            <p>Prueba nuevamente más tarde.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <button type="button" class="carousel-btn next-btn" id="btnNextSlide" title="Siguiente">
                <img src="img/icons/caret-big-right.svg" alt="Siguiente" class="carousel-arrow-icon" aria-hidden="true">
            </button>
        </div>
    </section>

    <!-- ===================== PANEL ARENA: NOSOTROS ===================== -->
    <section class="color-panel" id="nosotros">
        <div class="nosotros-panel-grid">
            <div>
                <p class="nosotros-tag">Quiénes somos</p>
                <h2 class="nosotros-title">Más sobre Kosheru</h2>
                <p class="nosotros-text">
                    Seleccionamos productores sostenibles y alimentos de origen con una mirada clara en sabor, calidad y bienestar. Importamos directamente superalimentos que aportan valor real a la vida diaria y los llevamos a Mosquera para que sean más accesibles para todos.
                </p>
                <a href="/tekoru/index.php" target="_blank" rel="noopener noreferrer" class="btn-conoce-mas">Conoce más</a>
            </div>
            <div class="nosotros-img-frame">
                <img src="img/portada.jpg" alt="Superalimentos Kosheru">
            </div>
        </div>
    </section>

    <!-- ===================== CONTACTO ===================== -->
    <section class="contacto-section" id="contacto">
        <div class="section-title-container" style="margin-bottom: 1.5rem;">
            <h2 class="section-headline" style="font-size: 2.2rem;">Contacto & Pedidos</h2>
            <div class="gold-divider" style="margin: 0.8rem auto 1.2rem auto;"></div>
            <p style="color: var(--color-texto-suave); font-size: 1rem; max-width: 680px; margin: 0 auto;">
                ¿Tienes preguntas sobre el origen de nuestros alimentos o deseas un pedido personalizado? Escríbenos y te responderemos de inmediato.
            </p>
            <div class="social-buttons-wrap">
                <a href="https://facebook.com" target="_blank" class="btn-social-circle" title="Facebook"><img src="img/icons/facebook-circle.svg" alt="Facebook"></a>
                <a href="https://www.instagram.com/kosher.u_?igsi=MWJrMjJ1cWZIMTRrda==" target="_blank" class="btn-social-circle" title="Instagram"><img src="img/icons/instagram.svg" alt="Instagram"></a>
                <a href="https://wa.me/573212411956" target="_blank" class="btn-social-circle" title="WhatsApp"><img src="img/icons/whatsapp.svg" alt="WhatsApp"></a>
            </div>
        </div>

        <div class="contacto-grid">
            <div class="info-contacto-card">
                <h3 class="info-card-title">Información de Contacto</h3>
                <p class="info-card-location">Estamos ubicados en Mosquera, Cundinamarca.</p>
                <p class="info-card-location" style="margin-bottom: 1.8rem;">Atendemos entregas a domicilio locales y envíos a municipios aledaños.</p>

                <div class="info-item">
                    <div class="info-icon-circle"><img src="img/icons/phone.svg" alt="Teléfono"></div>
                    <div>
                        <h4 class="info-item-label">Línea de Atención / WhatsApp</h4>
                        <p class="info-item-value">321 2411956</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon-circle"><img src="img/icons/envelope-alt.svg" alt="Correo"></div>
                    <div>
                        <h4 class="info-item-label">Correo Electrónico</h4>
                        <p class="info-item-value">Kosheru750@gmail.com</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon-circle"><img src="img/icons/clock.svg" alt="Horario"></div>
                    <div>
                        <h4 class="info-item-label">Horario de Entregas</h4>
                        <p class="info-item-value">Lunes a Sábado: 8:00 AM – 7:00 PM</p>
                        <p class="info-item-value">Domingos: 9:00 AM – 2:00 PM</p>
                    </div>
                </div>
            </div>

            <div class="form-contacto-card">
                <h3 class="form-card-title">Envíanos un Mensaje</h3>
                <div id="suggestionFormAlert" class="modal-alert"></div>
                <form id="suggestionForm">
                    <div class="modal-form-group">
                        <label for="suggName" class="modal-label">Nombre completo</label>
                        <input type="text" id="suggName" name="name" class="modal-input" placeholder="Ej. Juan Pérez" required>
                    </div>
                    <div class="modal-form-group">
                        <label for="suggEmail" class="modal-label">Correo electrónico</label>
                        <input type="email" id="suggEmail" name="email" class="modal-input" placeholder="Ej. juan@correo.com" required>
                    </div>
                    <div class="modal-form-group">
                        <label for="suggSubject" class="modal-label">Asunto</label>
                        <select id="suggSubject" name="subject" class="modal-input" required>
                            <option value="" disabled selected>Selecciona una opción</option>
                            <option value="Consulta de productos">Consulta de productos</option>
                            <option value="Pedido personalizado">Pedido personalizado</option>
                            <option value="Envíos y entregas">Envíos y entregas</option>
                            <option value="Otro">Otro asunto</option>
                        </select>
                    </div>
                    <div class="modal-form-group">
                        <label for="suggMessage" class="modal-label">Mensaje</label>
                        <textarea id="suggMessage" name="message" class="modal-input" rows="4" placeholder="Escribe tu consulta detalladamente aquí..." required></textarea>
                    </div>
                    <button type="submit" class="btn-modal-submit">Enviar Mensaje</button>
                </form>
            </div>
        </div>
    </section>

    <?php include_once __DIR__ . '/includes/footer.php'; ?>

    <script src="js/app.js"></script>
    <script src="js/theme.js"></script>
    <script src="js/banner.js"></script>
    <script src="js/carrito.js"></script>
    <script src="js/header.js"></script>
    <script src="js/carrusel.js"></script>
    <script src="js/productos.js"></script>
    <script src="js/product-detail.js"></script>
    <script src="js/auth.js"></script>
    <script src="js/sugerencias.js"></script>
</body>
</html>
