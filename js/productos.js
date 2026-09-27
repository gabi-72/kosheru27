/**
 * Kosheru - Carga Dinámica y Renderizado de Productos desde MySQL
 */

const KosheruProductos = {
    allProducts: [],
    productsById: {},

    async fetchProductos(categoria = '', search = '') {
        try {
            let url = 'api/productos.php?';
            if (categoria) url += `categoria=${encodeURIComponent(categoria)}&`;
            if (search) url += `search=${encodeURIComponent(search)}&`;

            const res = await fetch(url);
            const data = await res.json();

            if (data.success) {
                this.allProducts = data.productos;
                data.productos.forEach(p => { this.productsById[p.id] = p; });
                return data.productos;
            }
            return [];
        } catch (e) {
            console.error('Error al cargar productos:', e);
            return [];
        }
    },

    /**
     * Genera el HTML de estrellas (llenas/vacías) a partir de un promedio 0-5.
     * Si el producto no tiene reseñas todavía, muestra un texto neutral en
     * vez de estrellas vacías, para no dar la impresión de "mala calificación".
     */
    renderStars(avgRating, reviewCount) {
        const rating = parseFloat(avgRating) || 0;
        const count = parseInt(reviewCount, 10) || 0;

        if (count === 0) {
            return `<div class="product-rating product-rating--empty">Sé el primero en opinar</div>`;
        }

        const rounded = Math.round(rating); // redondeo simple a estrella entera
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            starsHtml += i <= rounded ? '★' : '☆';
        }

        return `
            <div class="product-rating" title="${rating.toFixed(1)} de 5 estrellas">
                <span class="product-rating-stars">${starsHtml}</span>
                <span class="product-rating-count">(${count})</span>
            </div>
        `;
    },

    getProductImage(p) {
        if (!p.image) return 'img/Tofu Ahumado Premium.png';
        if (String(p.name || '').toLowerCase().includes('matcha')) {
            return 'img/Matcha Ceremonial Grado A+.png';
        }
        let path = p.image;
        if (path.startsWith('.../')) path = path.substring(4);

        if (path.endsWith('.svg')) {
            const filename = path.split('/').pop().toLowerCase();
            const key = filename.replace(/\.[^.]+$/, '');
            const imageMap = {
                'tofu': 'img/Tofu Ahumado Premium.png',
                'proteina-guisante': 'img/Proteína Vegetal de Arveja.jpg',
                'proteina-canamo': 'img/proteina de cañamo.jpg',
                'spirulina': 'img/Espirulina Azul en Polvo.jpg',
                'maca': 'img/Maca Andina en Polvo.jpg',
                'acai': 'img/acai.avif',
                'chia': 'img/chia.webp',
                'quinoa': 'img/Quinoa Orgánica Premium.jpg',
                'cordyceps': 'img/cordiceps.jpg',
                'melena-leon': 'img/melena de leon.jpg',
                'baobab': 'img/baobab.jpg',
                'bayas-goji': 'img/Bayas Goji Premium.jpg',
                'camu-camu': 'img/camu camu.webp',
                'cacao-nibs': 'img/nibs de cacao.webp',
                'matcha': 'img/Matcha Ceremonial Grado A+.png',
                'curcuma': 'img/Cúrcuma Molida Premium.jpg',
                'jengibre': 'img/Jengibre en Polvo.jpg',
                'te-oolong': 'img/té oolong.jpg',
                'sal-himalaya': 'img/sal rosa del himalaya.jpg',
                'ashwagandha': 'img/ashwagandha.avif',
                'garcinia': 'img/garcinia.jpg'
            };
            if (imageMap[key]) return imageMap[key];
        }
        return path;
    },

    createProductCardHTML(p) {
        const imageUrl = this.getProductImage(p);
        return `
            <div class="product-card" data-id="${p.id}" data-category="${p.category}">
                <div class="product-origin-tag">${p.origin}</div>
                <div class="product-image-container" style="cursor:pointer;" onclick="if(!event.target.closest('.btn-wishlist')) KosheruProductos.openProductModal(${p.id})" title="Ver detalle del producto">
                    <button type="button" class="btn-wishlist" data-id="${p.id}" title="Guardar en wishlist">
                        <svg viewBox="0 0 24 24"><path d="M12 21s-7.5-4.6-10-9.3C.3 8.1 2 4.5 5.5 4.5c2 0 3.5 1.1 4.5 2.6 1-1.5 2.5-2.6 4.5-2.6 3.5 0 5.2 3.6 3.5 7.2C19.5 16.4 12 21 12 21z"/></svg>
                    </button>
                    <img src="${imageUrl}" alt="${p.name}" loading="lazy" onerror="this.src='img/tofu.jpg'">
                </div>
                <h3 class="product-name" style="cursor:pointer;" onclick="KosheruProductos.openProductModal(${p.id})">${p.name}</h3>
                ${this.renderStars(p.avg_rating, p.review_count)}
                <p class="product-description">${p.description}</p>
                <div class="product-footer">
                    <div class="product-price">${p.price_formatted}</div>
                    <button type="button" class="btn-add-cart" onclick='event.stopPropagation(); KosheruCart.addToCart(${JSON.stringify(p).replace(/'/g, "&apos;")}, this)' title="Agregar al carrito">+</button>
                </div>
            </div>
        `;
    },

    /**
     * Genera una tarjeta "esqueleto" con efecto shimmer, para mostrar mientras
     * se espera la respuesta de la API en vez de un texto plano de "Cargando...".
     */
    skeletonCardHTML() {
        return `
            <div class="product-card skeleton-card">
                <div class="skeleton-block skeleton-tag"></div>
                <div class="skeleton-block skeleton-image"></div>
                <div class="skeleton-block skeleton-title"></div>
                <div class="skeleton-block skeleton-text"></div>
                <div class="skeleton-block skeleton-text-short"></div>
                <div class="skeleton-footer">
                    <div class="skeleton-block skeleton-price"></div>
                    <div class="skeleton-block skeleton-btn"></div>
                </div>
            </div>
        `;
    },

    renderSkeletonGrid(count = 8) {
        return Array.from({ length: count }).map(() => this.skeletonCardHTML()).join('');
    },

    async renderGrid(containerId, categoria = '', search = '') {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = this.renderSkeletonGrid(8);

        const productos = await this.fetchProductos(categoria, search);

        if (productos.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon"><img src="img/icons/basket.svg" alt="Sin productos" class="inline-icon" aria-hidden="true"></div>
                    <h3 class="empty-state-title">No encontramos productos</h3>
                    <p class="empty-state-desc">Intenta buscando con otro término o cambiando el filtro de categoría.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = productos.map(p => this.createProductCardHTML(p)).join('');
        if (window.KosheruWishlist) KosheruWishlist.syncButtons();
    },

    async renderCarousel(trackId) {
        const track = document.getElementById(trackId);
        if (!track) return;

        const productos = await this.fetchProductos('', '');
        if (productos.length === 0) return false;

        const featured = [...productos].sort(() => Math.random() - 0.5).slice(0, Math.min(8, productos.length));
        track.innerHTML = featured.map(p => this.createProductCardHTML(p)).join('');
        if (window.KosheruWishlist) KosheruWishlist.syncButtons();

        if (window.KosheruCarrusel) window.KosheruCarrusel.init();
        return true;
    },

    // ============== MODAL DE DETALLE DE PRODUCTO (tema crema) ==============
    ensureModal() {
        if (document.getElementById('productDetailModalOverlay')) return;

        const style = document.createElement('style');
        style.id = 'kosheruProductModalStyles';
        style.textContent = `
            #productDetailModalOverlay {
                position: fixed; inset: 0; background: rgba(74,44,23,0.45);
                backdrop-filter: blur(5px); z-index: 4000;
                display: flex; align-items: center; justify-content: center;
                padding: 1.5rem; opacity: 0; visibility: hidden; transition: all 0.25s ease;
            }
            #productDetailModalOverlay.active { opacity: 1; visibility: visible; }
            .pdm-card {
                background: #fffdf5; color: #4a2c17; max-width: 780px; width: 100%;
                max-height: 88vh; overflow-y: auto; border-radius: 22px;
                border: 1px solid #e0d7c7;
                box-shadow: 0 25px 60px rgba(74,44,23,0.28); position: relative;
                display: grid; grid-template-columns: 1fr 1fr;
            }
            .pdm-close {
                position: absolute; top: 14px; right: 14px; width: 36px; height: 36px;
                border-radius: 50%; background: rgba(74,44,23,0.08); color: #4a2c17;
                display:flex; align-items:center; justify-content:center; font-size: 1.3rem; z-index: 5;
            }
            .pdm-close:hover { background: #c0392b; color: #fff; }
            .pdm-img-wrap { background: #f5f0e8; display:flex; align-items:center; justify-content:center; padding: 2rem; }
            .pdm-img-wrap img { max-width: 100%; max-height: 320px; object-fit: contain; border-radius: 14px; }
            .pdm-info { padding: 2rem 2rem 2rem 1.5rem; display:flex; flex-direction:column; gap: 0.9rem; }
            .pdm-badges { display:flex; gap: 0.5rem; flex-wrap: wrap; }
            .pdm-badge {
                font-size: 0.75rem; font-weight: 700; padding: 0.3rem 0.8rem; border-radius: 999px;
                background: rgba(212,160,23,0.14); color: #a8790f; border: 1px solid rgba(212,160,23,0.35);
            }
            .pdm-title { font-size: 1.5rem; font-weight: 800; color: #4a2c17; }
            .pdm-price { font-size: 1.6rem; font-weight: 900; color: #c0392b; }
            .pdm-section-title { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; color: #8a7a6a; font-weight: 700; margin-top: 0.4rem; }
            .pdm-text { font-size: 0.95rem; line-height: 1.6; color: #4a2c17; }
            .pdm-stock { font-size: 0.85rem; color: #7f6f5f; }
            .pdm-add-btn {
                margin-top: auto; border: none; border-radius: 12px; padding: 0.9rem 1.2rem;
                background: linear-gradient(135deg, #e67e22, #d4a017); color: #fff; font-weight: 800; cursor: pointer;
            }
            .pdm-add-btn:hover { filter: brightness(1.08); }

            /* Estrellas de calificación */
            .product-rating {
                display: flex; align-items: center; gap: 0.4rem;
                font-size: 0.85rem; color: #7f6f5f; margin-bottom: 0.5rem;
            }
            .product-rating-stars { color: #d4a017; font-size: 0.95rem; letter-spacing: 1px; }
            .product-rating-count { color: #9c897a; font-size: 0.78rem; }
            .product-rating--empty { color: #9c897a; font-style: italic; font-size: 0.8rem; }
            .pdm-rating { display: flex; align-items: center; gap: 0.5rem; }
            .pdm-rating .product-rating-stars { font-size: 1.15rem; }

            /* Sección de reseñas dentro del modal */
            .pdm-reviews-section { border-top: 1px solid #eae1d3; margin-top: 0.5rem; padding-top: 0.75rem; }
            .pdm-reviews-list { display:flex; flex-direction:column; gap:0.6rem; max-height:170px; overflow-y:auto; margin-bottom:0.6rem; }
            .pdm-review-item { background:#f9f5ec; border-radius:10px; padding:0.6rem 0.8rem; }
            .pdm-review-item-head { display:flex; justify-content:space-between; align-items:center; gap:0.5rem; font-size:0.8rem; font-weight:700; color:#4a2c17; }
            .pdm-review-item-stars { color:#d4a017; font-size:0.8rem; white-space:nowrap; }
            .pdm-review-item-comment { font-size:0.8rem; color:#6b5847; margin-top:0.25rem; line-height:1.4; }
            .pdm-reviews-empty { font-size:0.8rem; color:#9c897a; font-style:italic; padding:0.3rem 0; }
            .pdm-star-input { display:flex; gap:0.3rem; font-size:1.5rem; cursor:pointer; color:#d4a017; margin-bottom:0.6rem; user-select:none; line-height:1; }
            .pdm-star-input span { transition: transform 0.1s ease; }
            .pdm-star-input span:hover { transform: scale(1.15); }
            .pdm-review-input, .pdm-review-textarea {
                width:100%; box-sizing:border-box; border:1px solid #e0d7c7; border-radius:8px;
                padding:0.55rem 0.7rem; font-size:0.85rem; margin-bottom:0.5rem;
                font-family: inherit; background:#fffdf5; color:#4a2c17;
            }
            .pdm-review-textarea { resize: vertical; }
            .pdm-review-submit {
                border:none; border-radius:10px; padding:0.6rem 1.1rem; background:#6b7a3c; color:#fff;
                font-weight:700; font-size:0.85rem; cursor:pointer;
            }
            .pdm-review-submit:hover { filter:brightness(1.08); }
            .pdm-review-status { font-size:0.78rem; margin-top:0.5rem; }
            .pdm-review-status.success { color:#27ae60; }
            .pdm-review-status.error { color:#c0392b; }

            @media (max-width: 680px) {
                .pdm-card { grid-template-columns: 1fr; }
                .pdm-img-wrap { padding: 1.5rem 1.5rem 0; }
            }
        `;
        document.head.appendChild(style);

        const overlay = document.createElement('div');
        overlay.id = 'productDetailModalOverlay';
        overlay.innerHTML = `
            <div class="pdm-card">
                <button type="button" class="pdm-close" id="pdmCloseBtn">&times;</button>
                <div class="pdm-img-wrap"><img id="pdmImage" src="" alt=""></div>
                <div class="pdm-info">
                    <div class="pdm-badges">
                        <span class="pdm-badge" id="pdmCategory"></span>
                        <span class="pdm-badge" id="pdmOrigin"></span>
                    </div>
                    <h2 class="pdm-title" id="pdmName"></h2>
                    <div class="pdm-rating" id="pdmRating"></div>
                    <div class="pdm-price" id="pdmPrice"></div>
                    <div class="pdm-stock" id="pdmStock"></div>
                    <div>
                        <div class="pdm-section-title">Descripción</div>
                        <p class="pdm-text" id="pdmDescription"></p>
                    </div>
                    <button type="button" class="pdm-add-btn" id="pdmAddBtn">Agregar al carrito</button>

                    <div class="pdm-reviews-section">
                        <div class="pdm-section-title">Reseñas de clientes</div>
                        <div id="pdmReviewsList" class="pdm-reviews-list"></div>

                        <div class="pdm-review-form">
                            <div class="pdm-section-title" style="margin-top:0.5rem;">Deja tu reseña</div>
                            <div class="pdm-star-input" id="pdmStarInput" aria-label="Selecciona tu calificación">
                                <span data-star="1">☆</span><span data-star="2">☆</span><span data-star="3">☆</span><span data-star="4">☆</span><span data-star="5">☆</span>
                            </div>
                            <input type="text" id="pdmReviewName" class="pdm-review-input" placeholder="Tu nombre" maxlength="100">
                            <textarea id="pdmReviewComment" class="pdm-review-textarea" rows="2" placeholder="Cuéntanos tu experiencia (opcional)" maxlength="500"></textarea>
                            <button type="button" class="pdm-review-submit" id="pdmReviewSubmit">Enviar reseña</button>
                            <div class="pdm-review-status" id="pdmReviewStatus"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        document.getElementById('pdmCloseBtn').addEventListener('click', () => this.closeProductModal());
        overlay.addEventListener('click', (e) => { if (e.target === overlay) this.closeProductModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') this.closeProductModal(); });

        // Selector de estrellas para dejar una reseña
        const starInput = document.getElementById('pdmStarInput');
        if (starInput) {
            starInput.addEventListener('click', (e) => {
                const star = e.target.closest('span[data-star]');
                if (!star) return;
                this.selectedReviewRating = parseInt(star.dataset.star, 10);
                this.updateStarInputDisplay();
            });
        }

        const submitBtn = document.getElementById('pdmReviewSubmit');
        if (submitBtn) {
            submitBtn.addEventListener('click', () => this.submitReview());
        }
    },

    openProductModal(id) {
        const p = this.productsById[id];
        if (!p) return;

        this.ensureModal();
        this.currentModalProductId = id;

        document.getElementById('pdmImage').src = this.getProductImage(p);
        document.getElementById('pdmImage').alt = p.name;
        document.getElementById('pdmCategory').textContent = p.category;
        document.getElementById('pdmOrigin').textContent = p.origin;
        document.getElementById('pdmName').textContent = p.name;
        document.getElementById('pdmRating').innerHTML = this.renderStars(p.avg_rating, p.review_count);
        document.getElementById('pdmPrice').textContent = p.price_formatted;
        document.getElementById('pdmDescription').textContent = p.description;

        // Resetear el formulario de reseña y cargar las reseñas reales del producto
        this.selectedReviewRating = 0;
        this.updateStarInputDisplay();
        document.getElementById('pdmReviewName').value = '';
        document.getElementById('pdmReviewComment').value = '';
        document.getElementById('pdmReviewStatus').textContent = '';
        document.getElementById('pdmReviewStatus').className = 'pdm-review-status';
        this.loadProductReviews(id);

        const stock = parseInt(p.stock);
        const stockEl = document.getElementById('pdmStock');
        if (stock <= 0) {
            stockEl.textContent = 'Sin stock disponible';
            stockEl.style.color = '#c0392b';
        } else if (stock <= 5) {
            stockEl.textContent = `¡Últimas ${stock} unidades disponibles!`;
            stockEl.style.color = '#d4a017';
        } else {
            stockEl.textContent = `${stock} unidades disponibles`;
            stockEl.style.color = '#7f6f5f';
        }

        const addBtn = document.getElementById('pdmAddBtn');
        addBtn.disabled = stock <= 0;
        addBtn.style.opacity = stock <= 0 ? '0.5' : '1';
        addBtn.onclick = () => {
            KosheruCart.addToCart(p);
            this.closeProductModal();
        };

        document.getElementById('productDetailModalOverlay').classList.add('active');
    },

    closeProductModal() {
        const overlay = document.getElementById('productDetailModalOverlay');
        if (overlay) overlay.classList.remove('active');
    },

    // ============== RESEÑAS (leer y enviar) ==============
    selectedReviewRating: 0,
    currentModalProductId: null,

    updateStarInputDisplay() {
        const starInput = document.getElementById('pdmStarInput');
        if (!starInput) return;
        starInput.querySelectorAll('span[data-star]').forEach(s => {
            const val = parseInt(s.dataset.star, 10);
            s.textContent = val <= this.selectedReviewRating ? '★' : '☆';
        });
    },

    async loadProductReviews(productId) {
        const listEl = document.getElementById('pdmReviewsList');
        if (!listEl) return;

        listEl.innerHTML = '<div class="pdm-reviews-empty">Cargando reseñas...</div>';

        try {
            const res = await fetch(`api/resenas.php?product_id=${productId}`);
            const data = await res.json();

            if (!data.success) {
                listEl.innerHTML = '<div class="pdm-reviews-empty">No se pudieron cargar las reseñas.</div>';
                return;
            }

            this.renderReviewsList(data.reviews);

            // Recalcula el promedio mostrado a partir de las reseñas reales
            // que acabamos de traer, así una reseña recién enviada se refleja
            // de inmediato sin esperar a recargar la página.
            const ratingEl = document.getElementById('pdmRating');
            if (ratingEl) {
                const count = data.reviews.length;
                const avg = count > 0
                    ? data.reviews.reduce((sum, r) => sum + parseInt(r.rating, 10), 0) / count
                    : 0;
                ratingEl.innerHTML = this.renderStars(avg, count);
            }
        } catch (e) {
            listEl.innerHTML = '<div class="pdm-reviews-empty">No se pudieron cargar las reseñas.</div>';
        }
    },

    renderReviewsList(reviews) {
        const listEl = document.getElementById('pdmReviewsList');
        if (!listEl) return;

        if (!reviews || reviews.length === 0) {
            listEl.innerHTML = '<div class="pdm-reviews-empty">Aún no hay reseñas. ¡Sé el primero en opinar!</div>';
            return;
        }

        const esc = (window.KosheruApp && KosheruApp.escapeHtml) ? KosheruApp.escapeHtml : (v => v);

        listEl.innerHTML = reviews.map(r => {
            const rating = parseInt(r.rating, 10) || 0;
            const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);
            return `
                <div class="pdm-review-item">
                    <div class="pdm-review-item-head">
                        <span>${esc(r.customer_name)}</span>
                        <span class="pdm-review-item-stars">${stars}</span>
                    </div>
                    ${r.comment ? `<div class="pdm-review-item-comment">${esc(r.comment)}</div>` : ''}
                </div>
            `;
        }).join('');
    },

    async submitReview() {
        const statusEl = document.getElementById('pdmReviewStatus');
        const nameInput = document.getElementById('pdmReviewName');
        const commentInput = document.getElementById('pdmReviewComment');

        const name = nameInput.value.trim();
        const comment = commentInput.value.trim();

        statusEl.textContent = '';
        statusEl.className = 'pdm-review-status';

        if (!name) {
            statusEl.textContent = 'Por favor ingresa tu nombre.';
            statusEl.classList.add('error');
            return;
        }
        if (this.selectedReviewRating < 1) {
            statusEl.textContent = 'Selecciona una calificación con las estrellas.';
            statusEl.classList.add('error');
            return;
        }
        if (!this.currentModalProductId) return;

        const formData = new FormData();
        formData.append('product_id', this.currentModalProductId);
        formData.append('customer_name', name);
        formData.append('rating', this.selectedReviewRating);
        formData.append('comment', comment);

        try {
            const res = await fetch('api/resenas.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                statusEl.textContent = data.message;
                statusEl.classList.add('success');
                nameInput.value = '';
                commentInput.value = '';
                this.selectedReviewRating = 0;
                this.updateStarInputDisplay();
                this.loadProductReviews(this.currentModalProductId);
            } else {
                statusEl.textContent = data.message || 'No se pudo enviar tu reseña.';
                statusEl.classList.add('error');
            }
        } catch (e) {
            statusEl.textContent = 'Error de conexión al enviar tu reseña.';
            statusEl.classList.add('error');
        }
    }
};

window.addEventListener('load', async () => {
    const carouselTrack = document.getElementById('featuredCarouselTrack');
    if (!carouselTrack) return;

    const rendered = await KosheruProductos.renderCarousel('featuredCarouselTrack');
    if (!rendered && window.KosheruCarrusel) {
        window.KosheruCarrusel.init();
    }
});
