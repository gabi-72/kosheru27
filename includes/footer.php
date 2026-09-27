<footer class="kosheru-footer">
    <style>
        .kosheru-footer {
            background-color: #f5f0e8;
            color: #4a2c17;
            padding: 4rem 1.5rem 2rem 1.5rem;
            margin-top: auto;
            border-top: 5px solid #d4a017;
            transition: background-color 0.25s ease, color 0.25s ease, border-color 0.25s ease;
        }
        .kosheru-footer .footer-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 2.5rem;
            margin-bottom: 3rem;
        }
        .kosheru-footer .footer-logo-img { height: 45px; width: auto; }
        .kosheru-footer .footer-col-title {
            color: #4a2c17;
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kosheru-footer .footer-tagline {
            font-size: 0.9rem;
            color: #6b5847;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .kosheru-footer .footer-location {
            font-size: 0.85rem;
            color: #a8790f;
            font-weight: 700;
        }
        .kosheru-footer .footer-link-list {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            font-size: 0.95rem;
        }
        .kosheru-footer .footer-link-list li,
        .kosheru-footer .footer-link { color: #6b5847; }
        .kosheru-footer .footer-link:hover { color: #c0392b; }
        .kosheru-footer .footer-payment-icon {
            filter: invert(28%) sepia(18%) saturate(700%) hue-rotate(343deg) brightness(0.85);
        }
        .kosheru-footer .footer-copy {
            max-width: 1280px;
            margin: 0 auto;
            padding-top: 2rem;
            border-top: 1px solid #e0d7c7;
            text-align: center;
            font-size: 0.85rem;
            color: #8c7a68;
        }

        /* ===== Modo Oscuro ===== */
        html[data-theme="dark"] .kosheru-footer {
            background-color: #140e0c;
            color: #f5f0e8;
            border-top-color: #f1c40f;
        }
        html[data-theme="dark"] .kosheru-footer .footer-col-title { color: #ffffff; }
        html[data-theme="dark"] .kosheru-footer .footer-tagline,
        html[data-theme="dark"] .kosheru-footer .footer-link-list li,
        html[data-theme="dark"] .kosheru-footer .footer-link { color: #d9cdc0; }
        html[data-theme="dark"] .kosheru-footer .footer-location { color: #f1c40f; }
        html[data-theme="dark"] .kosheru-footer .footer-copy {
            color: #b3a597;
            border-top-color: rgba(255,255,255,0.12);
        }
        html[data-theme="dark"] .kosheru-footer .footer-payment-icon {
            filter: brightness(0) invert(1);
        }
        html[data-theme="dark"] .kosheru-footer .footer-logo-img {
            filter: brightness(1.5);
        }

        /* ===== Botón flotante de WhatsApp ===== */
        .btn-whatsapp-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 58px;
            height: 58px;
            background-color: #25D366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.45);
            z-index: 1500;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .btn-whatsapp-float:hover {
            transform: scale(1.08);
            box-shadow: 0 10px 26px rgba(37, 211, 102, 0.6);
        }
        .btn-whatsapp-float svg {
            width: 30px;
            height: 30px;
            color: #ffffff;
            position: relative;
            z-index: 2;
        }
        .btn-whatsapp-float::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background-color: rgba(37, 211, 102, 0.55);
            animation: whatsappPulse 2.2s ease-out infinite;
            z-index: 1;
        }
        @keyframes whatsappPulse {
            0% { transform: scale(1); opacity: 0.65; }
            100% { transform: scale(1.9); opacity: 0; }
        }
        @media (prefers-reduced-motion: reduce) {
            .btn-whatsapp-float::before { animation: none; }
        }
        @media (max-width: 480px) {
            .btn-whatsapp-float {
                bottom: 16px;
                right: 16px;
                width: 52px;
                height: 52px;
            }
            .btn-whatsapp-float svg { width: 25px; height: 25px; }
        }
        .footer-logo-img {
             width: 350px; /* Cambia este valor al tamaño que desees */
            height: auto; /* Mantiene la proporción original para que no se estire */
        }
    </style>

    <div class="footer-grid">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <img src="img/logos/Isotipo transparente.png" width="200px" alt="Kosheru Isologo" class="footer-logo-img">
            </div>
            <p class="footer-tagline">
                Tienda especialista en superalimentos 100% naturales, orgánicos e importados de la más alta pureza y origen garantizado.
            </p>
            <div class="footer-location">
                Mosquera, Cundinamarca, Colombia
            </div>
        </div>

        <div>
            <h4 class="footer-col-title">Navegación</h4>
            <ul class="footer-link-list">
                <li><a href="index.php" class="footer-link">Inicio</a></li>
                <li><a href="tienda.php" class="footer-link">Tienda de Superalimentos</a></li>
                <li><a href="mis-compras.php" class="footer-link">Consultar Mis Compras</a></li>
                <li><a href="login.php" class="footer-link">Mi Cuenta</a></li>
            </ul>
        </div>

        <div>
            <h4 class="footer-col-title">Categorías</h4>
            <ul class="footer-link-list">
                <li><a href="tienda.php?cat=Prote%C3%ADnas%20Especiales">Proteínas Especiales</a></li>
                <li><a href="tienda.php?cat=S%C3%BAper%20Alimentos">Súper Alimentos</a></li>
                <li><a href="tienda.php?cat=T%C3%A9s%20%26%20Condimentos">Tés & Condimentos</a></li>
            </ul>
        </div>

        <div>
            <h4 class="footer-col-title">Métodos de Pago</h4>
            <p class="footer-tagline">
                Aceptamos Nequi, Bancolombia, Davivienda, Tarjetas de Crédito/Débito, PSE y Efectivo Contra Entrega.
            </p>
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <img src="img/icons/credit-card-alt.svg" alt="Tarjeta de crédito" class="footer-payment-icon">
                <img src="img/icons/mobile.svg" alt="Pago móvil" class="footer-payment-icon">
                <img src="img/icons/bank.svg" alt="Banco" class="footer-payment-icon">
                <img src="img/icons/dollar-circle.svg" alt="Efectivo" class="footer-payment-icon">
            </div>
        </div>
    </div>

    <div class="footer-copy">
        &copy; <?= date('Y') ?> Kosheru S.A.S. Todos los derechos reservados. Superalimentos naturales e importados.
    </div>
</footer>

<!-- Botón flotante de WhatsApp — visible en toda la página, fuera del footer -->
<a href="https://wa.me/573212411956?text=Hola%2C%20quisiera%20hacer%20una%20consulta%20sobre%20sus%20productos"
   target="_blank"
   rel="noopener noreferrer"
   class="btn-whatsapp-float"
   aria-label="Escríbenos por WhatsApp"
   title="¿Necesitas ayuda? Escríbenos por WhatsApp">
    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.29-1.39a9.9 9.9 0 0 0 4.75 1.21h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.8 14.1c-.24.68-1.4 1.32-1.93 1.4-.5.08-1.13.11-1.82-.11-.42-.13-.96-.31-1.65-.61-2.9-1.25-4.8-4.16-4.94-4.35-.14-.19-1.18-1.57-1.18-3 0-1.42.75-2.12 1.02-2.41.27-.29.58-.36.78-.36.19 0 .39 0 .56.01.18.01.42-.07.66.5.24.58.83 2 .9 2.15.07.15.12.32.02.51-.1.19-.15.31-.29.48-.14.17-.3.38-.43.51-.14.14-.29.29-.13.57.17.29.75 1.24 1.61 2.01 1.11.99 2.04 1.3 2.32 1.44.29.14.45.12.62-.07.17-.19.71-.83.9-1.11.19-.29.38-.24.63-.14.26.1 1.63.77 1.91.91.29.14.48.21.55.32.07.12.07.68-.17 1.36z"/>
    </svg>
</a>
