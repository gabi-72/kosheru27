
const KosheruWishlist = {
    ids: new Set(),

    async loadIds() {
        try {
            const res = await fetch('api/wishlist.php');
            if (res.status === 401) return; // Usuario no logueado, no pasa nada
            const data = await res.json();
            if (data.success) {
                this.ids = new Set((data.product_ids || []).map(id => Number(id)));
                this.syncButtons();
            }
        } catch (e) {
            console.error('Error al cargar wishlist:', e);
        }
    },

    isSaved(productId) {
        return this.ids.has(Number(productId));
    },

    syncButtons() {
        document.querySelectorAll('.btn-wishlist').forEach(btn => {
            const id = Number(btn.dataset.id);
            btn.classList.toggle('active', this.isSaved(id));
        });
    },

    async toggle(productId, btn) {
        const formData = new FormData();
        formData.append('product_id', productId);

        try {
            const res = await fetch('api/wishlist.php', { method: 'POST', body: formData });

            if (res.status === 401) {
                KosheruApp.showToast('Inicia sesión para guardar productos en tu wishlist.', 'error');
                const loginModal = document.getElementById('loginModalOverlay');
                if (loginModal) loginModal.classList.add('active');
                return;
            }

            const data = await res.json();

            if (data.success) {
                if (data.in_wishlist) {
                    this.ids.add(Number(productId));
                } else {
                    this.ids.delete(Number(productId));
                }
                if (btn) btn.classList.toggle('active', data.in_wishlist);
                KosheruApp.showToast(data.message, 'success');
            } else {
                KosheruApp.showToast(data.message || 'No se pudo actualizar tu wishlist.', 'error');
            }
        } catch (e) {
            KosheruApp.showToast('Error de conexión al actualizar tu wishlist.', 'error');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    KosheruWishlist.loadIds();

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-wishlist');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        const id = btn.dataset.id;
        if (id) KosheruWishlist.toggle(id, btn);
    });
});
