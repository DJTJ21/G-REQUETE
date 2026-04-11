import './bootstrap';

/**
 * G-REQUÊTES — Main JS entry point
 * Alpine.js is loaded via CDN in layouts/app.blade.php
 */

/* ──────────────────────────────────────────────────────────────
   Notification badge polling (every 60 s)
────────────────────────────────────────────────────────────── */
function startNotifPolling() {
    const badge = document.getElementById('notif-badge');
    if (!badge) return;

    const routes = {
        etudiant: '/etudiant/api/notifications/count',
        agent:    '/agent/api/notifications/count',
        admin:    '/admin/api/notifications/count',
    };

    const role = document.body.dataset.role;
    const url  = routes[role];
    if (!url) return;

    async function poll() {
        try {
            const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const n    = data.count ?? 0;
            if (n > 0) {
                badge.textContent = n > 9 ? '9+' : n;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        } catch (_) { /* ignore */ }
    }

    poll();
    setInterval(poll, 60_000);
}

/* ──────────────────────────────────────────────────────────────
   CSRF header for fetch requests
────────────────────────────────────────────────────────────── */
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
if (csrfToken) {
    window._csrfFetch = (url, opts = {}) => fetch(url, {
        ...opts,
        headers: {
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            ...(opts.headers ?? {}),
        },
    });
}

/* ──────────────────────────────────────────────────────────────
   Flash auto-dismiss (3 s)
────────────────────────────────────────────────────────────── */
function initFlashAutoDismiss() {
    const alerts = document.querySelectorAll('[x-data][x-show]');
    alerts.forEach(el => {
        setTimeout(() => {
            if (typeof el._x_dataStack !== 'undefined') {
                try { el._x_dataStack[0].show = false; } catch (_) {}
            }
        }, 4500);
    });
}

/* ──────────────────────────────────────────────────────────────
   Confirm dialogs for destructive actions
────────────────────────────────────────────────────────────── */
function initConfirmForms() {
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('submit', e => {
            if (!window.confirm(el.dataset.confirm)) e.preventDefault();
        });
        el.addEventListener('click', e => {
            if (!window.confirm(el.dataset.confirm)) e.preventDefault();
        });
    });
}

/* ──────────────────────────────────────────────────────────────
   Bootstrap on DOMContentLoaded
────────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    startNotifPolling();
    initFlashAutoDismiss();
    initConfirmForms();
});
