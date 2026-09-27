/**
 * Kosheru - Gestión de Usuarios JavaScript Admin
 */

const AdminUsuarios = {
    async cargarUsuarios() {
        const tbody = document.getElementById('usersTableBody');
        if (!tbody) return;

        tbody.innerHTML = '<tr><td colspan="9" class="text-center">Cargando usuarios...</td></tr>';

        try {
            const res = await fetch('../api/usuarios/index.php');
            const data = await res.json();

            if (data.success) {
                if (data.usuarios.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center">No hay usuarios registrados.</td></tr>';
                    return;
                }

                tbody.innerHTML = data.usuarios.map(u => `
                    <tr>
                        <td>${u.name}</td>
                        <td>${u.lastname}</td>
                        <td>${u.email}</td>
                        <td style="font-weight:700;">${u.username}</td>
                        <td style="color:#888;">${u.password_masked}</td>
                        <td>
                            <span class="factura-status-badge ${u.rol === 'admin' ? 'badge-completado' : 'badge-pendiente'}">
                                ${u.rol.toUpperCase()}
                            </span>
                        </td>
                        <td>
                            <span class="factura-status-badge ${u.state === 'activo' ? 'badge-completado' : 'badge-cancelado'}">
                                ${u.state.toUpperCase()}
                            </span>
                        </td>
                        <td>${u.created_at}</td>
                        <td>
                            <div class="action-btn-wrap">
                                <button type="button" class="btn-action-edit" onclick="AdminUsuarios.openChangePassModal(${u.id}, '${u.username}')">Cambiar Clave</button>
                                <button type="button" class="btn-action-view" onclick="AdminUsuarios.toggleRole(${u.id}, '${u.rol}')">Rol</button>
                                <button type="button" class="btn-action-delete" onclick="AdminUsuarios.toggleState(${u.id}, '${u.state}')">
                                    ${u.state === 'activo' ? 'Desactivar' : 'Activar'}
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            }
        } catch (e) {
            console.error('Error cargando usuarios:', e);
        }
    },

    openChangePassModal(userId, username) {
        document.getElementById('modalPassUserId').value = userId;
        document.getElementById('modalPassUsernameText').textContent = username;
        document.getElementById('modalPassInput').value = '';
        document.getElementById('modalPassOverlay').classList.add('active');
    },

    closePassModal() {
        document.getElementById('modalPassOverlay').classList.remove('active');
    },

    async submitChangePass(e) {
        e.preventDefault();
        const userId = document.getElementById('modalPassUserId').value;
        const newPass = document.getElementById('modalPassInput').value.trim();

        if (newPass.length < 6) {
            KosheruApp.showToast('La contraseña debe tener al menos 6 caracteres.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'change_password');
        formData.append('user_id', userId);
        formData.append('new_password', newPass);

        try {
            const res = await fetch('../api/usuarios/index.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                KosheruApp.showToast(data.message, 'success');
                this.closePassModal();
                this.cargarUsuarios();
            } else {
                KosheruApp.showToast(data.message, 'error');
            }
        } catch (err) {
            KosheruApp.showToast('Error al cambiar contraseña.', 'error');
        }
    },

    async toggleState(userId, currentState) {
        const formData = new FormData();
        formData.append('action', 'toggle_state');
        formData.append('user_id', userId);
        formData.append('state', currentState);

        try {
            const res = await fetch('../api/usuarios/index.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                KosheruApp.showToast(data.message, 'success');
                this.cargarUsuarios();
            }
        } catch (e) {
            KosheruApp.showToast('Error de conexión.', 'error');
        }
    },

    async toggleRole(userId, currentRole) {
        const formData = new FormData();
        formData.append('action', 'change_role');
        formData.append('user_id', userId);
        formData.append('rol', currentRole);

        try {
            const res = await fetch('../api/usuarios/index.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                KosheruApp.showToast(data.message, 'success');
                this.cargarUsuarios();
            }
        } catch (e) {
            KosheruApp.showToast('Error de conexión.', 'error');
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    AdminUsuarios.cargarUsuarios();

    const formPass = document.getElementById('formChangePasswordModal');
    if (formPass) {
        formPass.addEventListener('submit', (e) => AdminUsuarios.submitChangePass(e));
    }
});
