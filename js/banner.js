/**
 * Kosheru - Banner Promocional Rotativo (Home)
 */
const KosheruBanner = {
    currentIndex: 0,
    autoplayInterval: null,

    init() {
        this.track = document.getElementById('promoTrack');
        if (!this.track) return;

        this.slides = this.track.querySelectorAll('.promo-slide');
        this.dotsContainer = document.getElementById('promoDots');
        this.prevBtn = document.getElementById('promoPrevBtn');
        this.nextBtn = document.getElementById('promoNextBtn');

        if (this.slides.length <= 1) {
            if (this.prevBtn) this.prevBtn.style.display = 'none';
            if (this.nextBtn) this.nextBtn.style.display = 'none';
            return;
        }

        this.renderDots();
        this.goTo(0);

        if (this.prevBtn) this.prevBtn.addEventListener('click', () => { this.prev(); this.restartAutoplay(); });
        if (this.nextBtn) this.nextBtn.addEventListener('click', () => { this.next(); this.restartAutoplay(); });

        this.startAutoplay();
    },

    renderDots() {
        if (!this.dotsContainer) return;
        this.dotsContainer.innerHTML = '';
        this.slides.forEach((_, i) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'promo-dot';
            dot.setAttribute('aria-label', `Ir a la diapositiva ${i + 1}`);
            dot.addEventListener('click', () => { this.goTo(i); this.restartAutoplay(); });
            this.dotsContainer.appendChild(dot);
        });
    },

    goTo(index) {
        this.currentIndex = (index + this.slides.length) % this.slides.length;
        this.track.style.transform = `translateX(-${this.currentIndex * 100}%)`;
        if (this.dotsContainer) {
            this.dotsContainer.querySelectorAll('.promo-dot').forEach((dot, i) => {
                dot.classList.toggle('active', i === this.currentIndex);
            });
        }
    },

    next() { this.goTo(this.currentIndex + 1); },
    prev() { this.goTo(this.currentIndex - 1); },

    startAutoplay() {
        this.stopAutoplay();
        this.autoplayInterval = setInterval(() => this.next(), 6000);
    },
    stopAutoplay() {
        if (this.autoplayInterval) clearInterval(this.autoplayInterval);
    },
    restartAutoplay() { this.startAutoplay(); }
};

document.addEventListener('DOMContentLoaded', () => KosheruBanner.init());
