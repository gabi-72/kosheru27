/**
 * Kosheru - Carrusel de Productos Móvil / Desktop con rotación automática
 */

window.KosheruCarrusel = {
    currentIndex: 0,
    autoRotateInterval: null,

    init() {
        const track = document.getElementById('featuredCarouselTrack');
        const prevBtn = document.getElementById('btnPrevSlide');
        const nextBtn = document.getElementById('btnNextSlide');

        if (!track) return;

        this.track = track;
        this.trackWrapper = track.closest('.carousel-track-wrapper');
        this.cards = track.querySelectorAll('.product-card');
        if (this.cards.length === 0) return;

        this.realCards = Array.from(this.cards);
        this.realItemCount = this.realCards.length;
        this.cloneCount = Math.min(4, this.realItemCount);

        this.setupInfiniteLoop();
        this.updateMeasurements();

        const maxIndex = () => Math.max(0, this.cards.length - this.visibleCount);
        this.currentIndex = this.cloneCount;

        const updateCarouselPosition = (useTransition = true) => {
            if (!useTransition) {
                this.track.style.transition = 'none';
                this.track.getBoundingClientRect();
            }
            this.track.style.transform = `translateX(-${this.currentIndex * this.cardWidth}px)`;
            if (!useTransition) {
                requestAnimationFrame(() => {
                    this.track.style.transition = 'transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)';
                });
            }
        };

        this.handleTransitionEnd = () => {
            const resetRight = this.realItemCount + this.cloneCount;
            const resetLeft = this.cloneCount;

            if (this.currentIndex >= resetRight) {
                this.currentIndex -= this.realItemCount;
                updateCarouselPosition(false);
            }

            if (this.currentIndex < resetLeft) {
                this.currentIndex += this.realItemCount;
                updateCarouselPosition(false);
            }
        };

        this.track.addEventListener('transitionend', this.handleTransitionEnd);

        this.navigate = (direction) => {
            const nextIndex = this.currentIndex + direction;
            const targetIndex = Math.min(maxIndex(), Math.max(0, nextIndex));
            this.currentIndex = targetIndex;
            updateCarouselPosition();
        };

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                this.navigate(-1);
                this.restartAutoRotate();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                this.navigate(1);
                this.restartAutoRotate();
            });
        }

        track.addEventListener('touchstart', (e) => {
            this.startX = e.touches[0].clientX;
        }, { passive: true });

        track.addEventListener('touchend', (e) => {
            const endX = e.changedTouches[0].clientX;
            const diffX = this.startX - endX;

            if (Math.abs(diffX) > 50) {
                this.navigate(diffX > 0 ? 1 : -1);
                this.restartAutoRotate();
            }
        }, { passive: true });

        window.addEventListener('resize', () => {
            this.updateMeasurements();
            if (this.currentIndex > Math.max(0, this.cards.length - this.visibleCount)) {
                this.currentIndex = Math.max(0, this.cards.length - this.visibleCount);
            }
            updateCarouselPosition(false);
        });

        updateCarouselPosition(false);
        this.startAutoRotate();
    },

    setupInfiniteLoop() {
        const beforeClones = this.realCards.slice(-this.cloneCount).map(card => card.cloneNode(true));
        const afterClones = this.realCards.slice(0, this.cloneCount).map(card => card.cloneNode(true));

        beforeClones.forEach(clone => this.track.insertBefore(clone, this.track.firstChild));
        afterClones.forEach(clone => this.track.appendChild(clone));

        this.cards = this.track.querySelectorAll('.product-card');
    },

    updateMeasurements() {
        const wrapperWidth = this.trackWrapper ? this.trackWrapper.getBoundingClientRect().width : window.innerWidth;
        const firstCard = this.cards[0];
        const secondCard = this.cards[1];
        const firstRect = firstCard.getBoundingClientRect();

        let stepWidth = firstRect.width;
        if (secondCard) {
            const secondRect = secondCard.getBoundingClientRect();
            stepWidth = Math.round(secondRect.left - firstRect.left);
        } else {
            const gapValue = getComputedStyle(this.track).gap;
            const gapSize = gapValue.includes('rem')
                ? parseFloat(gapValue) * parseFloat(getComputedStyle(document.documentElement).fontSize)
                : parseFloat(gapValue) || 24;
            stepWidth = Math.round(firstRect.width + gapSize);
        }

        this.cardWidth = stepWidth;
        this.visibleCount = Math.min(4, Math.max(1, Math.floor(wrapperWidth / this.cardWidth)));
    },

    startAutoRotate() {
        this.stopAutoRotate();
        this.autoRotateInterval = setInterval(() => {
            this.navigate(1);
        }, 8000);
    },

    stopAutoRotate() {
        if (this.autoRotateInterval) {
            clearInterval(this.autoRotateInterval);
            this.autoRotateInterval = null;
        }
    },

    restartAutoRotate() {
        this.startAutoRotate();
    }
};
