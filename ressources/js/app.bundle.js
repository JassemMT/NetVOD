/*!
 * resources/js/app.bundle.js
 * Simplified app bundle + inlined notification handler
 *
 * - Keeps nav active marker + 3D/parallax card effect
 * - Adds lightweight notification (toast) init (close + auto-hide)
 * - Defensive, respects prefers-reduced-motion, exposes minimal debug API
 */
(function () {
  'use strict';

  // ----- Config & Utils -----
  const Config = {
    maxRotation: 14,
    maxParallax: 36,
    parallaxScale: 1.12,
    bandParallaxFactor: 0.18,
    resetDelay: 420, // ms before stopping RAF after mouseleave
  };

  const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const qsa = (sel, root = document) => {
    try { return Array.from((root || document).querySelectorAll(sel)); }
    catch (e) { return []; }
  };

  const lerp = (a, b, t) => a + (b - a) * t;

  function safeLog(...args) {
    if (typeof console !== 'undefined' && console.log) console.log('[app.bundle]', ...args);
  }
  function safeError(...args) {
    if (typeof console !== 'undefined' && console.error) console.error('[app.bundle]', ...args);
  }

  // ----- Nav active link (small, defensive) -----
  function setActiveNavLink() {
    try {
      const current = window.location.href;
      qsa('.nav a').forEach((a) => {
        try {
          a.removeAttribute('aria-current');
          const href = a.getAttribute('href') || '';
          if (a.href === current || (href && current.includes(href))) {
            a.setAttribute('aria-current', 'page');
          }
        } catch (e) { /* ignore malformed link */ }
      });
    } catch (e) {
      safeError('setActiveNavLink failed', e);
    }
  }

  // ----- Card 3D / Parallax effect (concise) -----
  function initCardEffects() {
    if (prefersReducedMotion) {
      safeLog('reduced-motion: skipping card effects');
      return;
    }

    const cards = qsa('.card');
    if (!cards.length) return;

    cards.forEach((card) => {
      try {
        // prevent double init
        if (card.__nv_init) return;
        card.__nv_init = true;

        const cardImage = card.querySelector('.card-image, .episode-image');
        const cardBand = card.querySelector('.card-band, .episode-band');
        if (!cardImage || !cardBand) return;

        // per-card state stored on element
        const state = {
          tRotX: 0, tRotY: 0, cRotX: 0, cRotY: 0,
          tBgX: 0, tBgY: 0, cBgX: 0, cBgY: 0,
          rect: null, raf: null, isAnimating: false,
        };
        card.__nv_state = state;

        function animate() {
          state.cRotX = lerp(state.cRotX, state.tRotX, 0.09);
          state.cRotY = lerp(state.cRotY, state.tRotY, 0.09);
          state.cBgX = lerp(state.cBgX, state.tBgX, 0.09);
          state.cBgY = lerp(state.cBgY, state.tBgY, 0.09);

          try {
            card.style.transform = `perspective(1000px) rotateX(${state.cRotX}deg) rotateY(${state.cRotY}deg)`;
            cardImage.style.transform = `translate3d(${state.cBgX}px, ${state.cBgY}px, 0) scale(${Config.parallaxScale})`;
            cardBand.style.transform = `translate3d(${state.cBgX * Config.bandParallaxFactor}px, ${state.cBgY * Config.bandParallaxFactor}px, 0)`;
          } catch (e) {
            safeError('card animate apply fail', e);
          }

          state.raf = requestAnimationFrame(animate);
        }

        let lastTouchTime = 0;
        function onPointer(clientX, clientY) {
          if (!state.rect) state.rect = card.getBoundingClientRect();
          const px = (clientX - state.rect.left) / state.rect.width;
          const py = (clientY - state.rect.top) / state.rect.height;
          const cx = Math.max(-1, Math.min(1, px - 0.5));
          const cy = Math.max(-1, Math.min(1, py - 0.5));

          state.tRotY = cx * Config.maxRotation;
          state.tRotX = -cy * Config.maxRotation;
          state.tBgX = -cx * Config.maxParallax;
          state.tBgY = -cy * Config.maxParallax;

          if (!state.isAnimating) {
            state.isAnimating = true;
            state.raf = requestAnimationFrame(animate);
          }
        }

        const onMouseMove = (e) => onPointer(e.clientX, e.clientY);
        const onTouchMove = (e) => {
          const t = Date.now();
          if (t - lastTouchTime < 30) return;
          lastTouchTime = t;
          const touch = e && e.touches && e.touches[0];
          if (touch) onPointer(touch.clientX, touch.clientY);
        };

        function reset() {
          state.tRotX = state.tRotY = state.tBgX = state.tBgY = 0;
          setTimeout(() => {
            if (state.raf) {
              cancelAnimationFrame(state.raf);
              state.raf = null;
              state.isAnimating = false;
            }
          }, Config.resetDelay);
        }

        const onFocus = () => { state.rect = null; };
        const onKey = (ev) => {
          if (ev.key === 'Enter' || ev.key === ' ') {
            ev.preventDefault();
            const link = card.querySelector('.card-link, .episode-link');
            if (link) link.click();
          } else if (ev.key === 'Escape') {
            reset();
          }
        };

        card.addEventListener('mousemove', onMouseMove);
        card.addEventListener('mouseleave', reset);
        card.addEventListener('touchmove', onTouchMove, { passive: true });
        card.addEventListener('touchend', reset, { passive: true });
        card.addEventListener('focus', onFocus);
        card.addEventListener('blur', onFocus);
        card.addEventListener('keydown', onKey);

        // lightweight teardown record
        card.__nv_handlers = { onMouseMove, reset, onTouchMove, onFocus, onKey };
      } catch (e) {
        safeError('init card failed', e);
      }
    });
  }

  // ----- Simple cleanup for SPA/pagehide -----
  function teardown() {
    qsa('.card, .episode-card').forEach((card) => {
      try {
        if (!card.__nv_init) return;
        const h = card.__nv_handlers || {};
        card.removeEventListener('mousemove', h.onMouseMove);
        card.removeEventListener('mouseleave', h.reset);
        card.removeEventListener('touchmove', h.onTouchMove);
        card.removeEventListener('touchend', h.reset);
        card.removeEventListener('focus', h.onFocus);
        card.removeEventListener('blur', h.onFocus);
        card.removeEventListener('keydown', h.onKey);
        if (card.__nv_state && card.__nv_state.raf) cancelAnimationFrame(card.__nv_state.raf);
        delete card.__nv_init;
        delete card.__nv_state;
        delete card.__nv_handlers;
      } catch (e) { /* ignore */ }
    });
  }

  // ----- Notifications (inlined simple handler) -----
  // This matches your small notification JS: target element id="notificationToast"
  function closeToast() {
    try {
      const toast = document.getElementById('notificationToast');
      if (toast) {
        // use the same animation name you used in CSS
        toast.style.animation = 'slide-out-right 0.3s ease forwards';
        setTimeout(() => {
          if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 300);
      }
    } catch (e) {
      safeError('closeToast error', e);
    }
  }

  function initNotifications() {
    try {
      const toast = document.getElementById('notificationToast');
      if (!toast) return;

      // wire close button if present
      const closeBtn = toast.querySelector('.toast-close') || toast.querySelector('.nv-toast-close');
      if (closeBtn) {
        closeBtn.addEventListener('click', closeToast);
      }

      // auto-close
      const duration = parseInt(toast.dataset.duration || toast.getAttribute('data-duration') || '5000', 10) || 5000;
      if (duration > 0) {
        setTimeout(() => {
          closeToast();
        }, duration);
      }

      // allow Esc to close when toast focused
      toast.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape') closeToast();
      });
    } catch (e) {
      safeError('initNotifications failed', e);
    }
  }

  // ----- Init on DOM ready -----
  function initAll() {
    try {
      setActiveNavLink();
      initCardEffects();
      initNotifications();
      safeLog('app.bundle initialized');
    } catch (e) {
      safeError('initAll error', e);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  // cleanup on pagehide/unload
  if (typeof window !== 'undefined') {
    window.addEventListener('pagehide', teardown);
    window.addEventListener('beforeunload', teardown);
  }

  // Expose minimal debug API (non-invasive)
  try {
    if (typeof window !== 'undefined') {
      window.__NetVOD = window.__NetVOD || {};
      window.__NetVOD.initAll = initAll;
      window.__NetVOD.destroyCards = teardown;
      window.__NetVOD.closeToast = closeToast;
    }
  } catch (e) { /* ignore */ }
})();