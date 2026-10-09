(() => {
    const body = document.body;
    document.querySelectorAll('[data-sidebar-toggle]').forEach(button => {
        button.addEventListener('click', () => body.classList.toggle('sidebar-open'));
    });
    document.querySelectorAll('[data-sidebar-close]').forEach(button => {
        button.addEventListener('click', () => body.classList.remove('sidebar-open'));
    });
    document.querySelectorAll('[data-fullscreen]').forEach(button => {
        button.addEventListener('click', async () => {
            try {
                if (!document.fullscreenElement) await document.documentElement.requestFullscreen();
                else await document.exitFullscreen();
            } catch (e) {
                // Fullscreen is optional; silently ignore unsupported browsers.
            }
        });
    });
})();
