/**
 * Kosheru - Gestión de Productos e Imágenes Admin JavaScript
 */

const AdminProductos = {
    async cargarProductos() {
        const tbody = document.getElementById('prodTableBody');
        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Cargando catálogo...</td></tr>';

        try {
            const res = await fetch('../api/productos/index.php');
            const data = await res.json();

            if (data.success) {
                if (data.productos.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center">No hay productos en la tienda.</td></tr>';
                    return;
                }

                tbody.innerHTML = data.productos.map(p => `
                    <tr>
                        <td>
                            <img src="../../${p.image}" alt="${p.name}" style="width: 48px; height: 48px; object-fit: contain; border-radius: 8px; background: var(--color-beige);" onerror="this.src='../../imagenes/logo/logo.svg'">
                        </td>
                        <td style="font-weight:700; color:var(--color-cafe-oscuro);">${p.name}</td>
                        <td><span class="factura-status-badge badge-completado">${p.category}</span></td>
                        <td>${p.origin}</td>
                        <td style="font-weight:800; color:var(--color-rojo-kosheru);">${p.price_formatted}</td>
                        <td style="font-weight:700;">${p.stock} un.</td>
                        <td style="font-size:0.8rem; color:#666; max-width:200px; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">${p.description}</td>
                        <td>
                            <div class="action-btn-wrap">
                                <button type="button" class="btn-action-edit" onclick='AdminProductos.openModal(${JSON.stringify(p).replace(/'/g, "&apos;")})'>Editar</button>
                                <button type="button" class="btn-action-delete" onclick="AdminProductos.deleteProduct(${p.id}, '${p.name}')">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (e) {
            console.error('Error cargando productos:', e);
        }
    },

    openModal(p = null) {
        document.getElementById('modalProdTitle').textContent = p ? 'Editar Producto' : 'Agregar Nuevo Producto';
        document.getElementById('prodId').value = p ? p.id : '';
        document.getElementById('prodName').value = p ? p.name : '';
        document.getElementById('prodCategory').value = p ? p.category : 'Proteínas Especiales';
        document.getElementById('prodOrigin').value = p ? p.origin : 'JAPÓN 🇯🇵';
        document.getElementById('prodPrice').value = p ? p.price : '';
        document.getElementById('prodStock').value = p ? p.stock : '';
        document.getElementById('prodDescription').value = p ? p.description : '';
        document.getElementById('prodImageFile').value = '';

        document.getElementById('modalProdOverlay').classList.add('active');
    },

    closeModal() {
        document.getElementById('modalProdOverlay').classList.remove('active');
    },

    async saveProduct(e) {
        e.preventDefault();
        const form = document.getElementById('formProductModal');
        const formData = new FormData(form);

        try {
            const res = await fetch('../api/productos/index.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                KosheruApp.showToast(data.message, 'success');
                this.closeModal();
                this.cargarProductos();
            } else {
                KosheruApp.showToast(data.message, 'error');
            }
        } catch (err) {
            KosheruApp.showToast('Error de conexión al guardar producto.', 'error');
        }
    },

    async deleteProduct(id, name) {
        if (!confirm(`¿Estás seguro de eliminar el producto "${name}"?`)) return;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('product_id', id);

        try {
            const res = await fetch('../api/productos/index.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                KosheruApp.showToast(data.message, 'success');
                this.cargarProductos();
            } else {
                KosheruApp.showToast(data.message, 'error');
            }
        } catch (e) {
            KosheruApp.showToast('Error al eliminar producto.', 'error');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    AdminProductos.cargarProductos();

    const form = document.getElementById('formProductModal');
    if (form) {
        form.addEventListener('submit', (e) => AdminProductos.saveProduct(e));
    }
});
