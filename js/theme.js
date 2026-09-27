/**
 * Kosheru - Toggle de Tema Claro / Oscuro (persistente en localStorage)
 */
const KosheruTheme = {
    STORAGE_KEY: 'kosheru_theme',

    getCurrent() {
        return document.documentElement.getAttribute('data-theme') || localStorage.getItem(this.STORAGE_KEY) || 'light';
    },

    apply(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem(this.STORAGE_KEY, theme);
        this.updateButtons(theme);
    },

    toggle() {
        this.apply(this.getCurrent() === 'dark' ? 'light' : 'dark');
    },

    updateButtons(theme) {
        document.querySelectorAll('.btn-theme-toggle').forEach(btn => {
            btn.setAttribute('aria-label', theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
            const label = btn.querySelector('.theme-toggle-label');
            if (label) label.textContent = theme === 'dark' ? 'Modo claro' : 'Modo oscuro';
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    KosheruTheme.apply(KosheruTheme.getCurrent());
    document.querySelectorAll('.btn-theme-toggle').forEach(btn => {
        btn.addEventListener('click', () => KosheruTheme.toggle());
    });
});
