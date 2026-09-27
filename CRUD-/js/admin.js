/**
 * Kosheru - Core JavaScript Admin Utilities
 */
document.addEventListener('DOMContentLoaded', () => {
    const btnAdminMobileToggle = document.getElementById('btnAdminMobileToggle');
    const adminSidebar = document.getElementById('adminSidebar');

    if (btnAdminMobileToggle && adminSidebar) {
        btnAdminMobileToggle.addEventListener('click', () => {
            adminSidebar.classList.toggle('show-mobile');
        });
    }
});
