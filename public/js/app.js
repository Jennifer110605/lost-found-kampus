/* ============================================================
   Lost & Found Kampus — app.js
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    // ── Auto-dismiss flash alert setelah 4 detik ──────────────
    const alerts = document.querySelectorAll('.alert-float');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 4000);
    });

    // ── Konfirmasi hapus (fallback jika inline onsubmit tidak ada) ──
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // ── Active navbar link highlight berdasarkan URL ──────────
    const currentPath = window.location.pathname;
    document.querySelectorAll('#mainNav .nav-link').forEach(function (link) {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });

});
