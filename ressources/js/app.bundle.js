/*!
 * resources/js/app.bundle.js
 * PureStream Fusion — Single JS bundle (refactored, non‑breaking)
 *
 * - Preserves original behavior:
 *   • 3D/parallax card effect for .card / .episode-card
 *   • marking active nav link (.nav a -> aria-current="page")
 * - Defensive: respects prefers-reduced-motion, avoids exceptions leaking,
 *   uses passive listeners where appropriate, cleans up on page hide.
 * - Exposes window.__NetVOD for debug and cleanup.
 *
 * Usage:
 *   <script src="/ressources/js/app.bundle.js" defer></script>
 */
(function () {
  'use strict';

  // ---------- CONFIG & UTILITIES ----------
  const Config = {
    maxRotation: 14,
    rotationDamp: 0.09,
    maxParallax: 36,
    parallaxScale: 1.12,
    bandParallaxFactor: 0.18,
    resetDuration: 480,
  };

  const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const qs = (sel, root = document) => {
    try {
      return root.querySelector(sel);
    } catch (e) {
      return null;
    }
  };
  const qsa = (sel, root = document) => {
    try {
      return Array.from(root.querySelectorAll(sel));
    } catch (e) {
      return [];
    }
  };
  const lerp = (a, b, t) => a + (b - a) * t;

  function safeLog(...args) {
    if (typeof console !== 'undefined' && console.log) {
      console.log('[app.bundle]', ...args);
    }
  }
  function safeError(...args) {
    if (typeof console !== 'undefined' && console.error) {
      console.error('[app.bundle]', ...args);
    }
  }

  // ---------- GLOBAL MODULE ----------
  const Global = (function () {
    // Keep references so we can clean up listeners on pagehide/unload
    const cardsState = new WeakMap();
    const listeners = [];

    function setActiveNavLink() {
      try {
        const currentUrl = window.location.href;
        qsa('.nav a').forEach((link) => {
          try {
            link.removeAttribute('aria-current');
            // Matching: either absolute href equals current URL or the href path is included
            const href = link.getAttribute('href') || '';
            if (link.href === currentUrl || (href && currentUrl.includes(href))) {
              link.setAttribute('aria-current', 'page');
            }
          } catch (err) {
            // ignore malformed hrefs
          }
        });
      } catch (err) {
        // no-op
      }
    }

    function initCardEffects() {
      // Respect user preference: do not run heavy animations if user reduced motion
      if (prefersReducedMotion) {
        safeLog('prefers-reduced-motion: skipping card effects');
        return;
      }

      // Select both catalogue cards and episode cards
      const cardEls = qsa('.card');
      if (!cardEls.length) return;

      cardEls.forEach((card) => {
        try {
          // avoid initialising twice
          if (cardsState.has(card)) return;

          const cardImage = card.querySelector('.card-image, .episode-image');
          const cardBand = card.querySelector('.card-band, .episode-band');

          if (!cardImage || !cardBand) {
            // don't initialize cards that don't have the required elements
            return;
          }

          // Per-card state
          const state = {
            targetRotX: 0,
            targetRotY: 0,
            currentRotX: 0,
            currentRotY: 0,
            targetBgX: 0,
            targetBgY: 0,
            currentBgX: 0,
            currentBgY: 0,
            rect: null,
            rafId: null,
            isAnimating: false,
          };

          // Animation loop
          const animate = () => {
            state.currentRotX = lerp(state.currentRotX, state.targetRotX, Config.rotationDamp);
            state.currentRotY = lerp(state.currentRotY, state.targetRotY, Config.rotationDamp);
            state.currentBgX = lerp(state.currentBgX, state.targetBgX, Config.rotationDamp);
            state.currentBgY = lerp(state.currentBgY, state.targetBgY, Config.rotationDamp);

            // Apply transforms
            try {
              card.style.transform = `perspective(1000px) rotateX(${state.currentRotX}deg) rotateY(${state.currentRotY}deg)`;
              cardImage.style.transform = `translate3d(${state.currentBgX}px, ${state.currentBgY}px, 0) scale(${Config.parallaxScale})`;
              cardBand.style.transform = `translate3d(${state.currentBgX * Config.bandParallaxFactor}px, ${state.currentBgY *
                Config.bandParallaxFactor}px, 0)`;
            } catch (err) {
              // element might be removed between frames
              safeError('apply transform error', err);
            }

            state.rafId = requestAnimationFrame(animate);
          };

          // Event handlers
          let lastTouchTime = 0;
          const onPointerMove = (clientX, clientY) => {
            if (!state.rect) state.rect = card.getBoundingClientRect();

            const px = (clientX - state.rect.left) / state.rect.width;
            const py = (clientY - state.rect.top) / state.rect.height;
            const cx = Math.max(-1, Math.min(1, px - 0.5));
            const cy = Math.max(-1, Math.min(1, py - 0.5));

            state.targetRotY = cx * Config.maxRotation;
            state.targetRotX = -cy * Config.maxRotation;
            state.targetBgX = -cx * Config.maxParallax;
            state.targetBgY = -cy * Config.maxParallax;

            if (!state.isAnimating) {
              state.isAnimating = true;
              animate();
            }
          };

          const onMouseMove = (e) => {
            onPointerMove(e.clientX, e.clientY);
          };

          const onTouchMove = (e) => {
            // throttle touches a little by time to avoid too many events
            const t = Date.now();
            if (t - lastTouchTime < 30) return;
            lastTouchTime = t;
            const touch = (e && e.touches && e.touches[0]) || null;
            if (touch) onPointerMove(touch.clientX, touch.clientY);
          };

          const onLeave = () => {
            // reset targets to 0
            state.targetRotX = state.targetRotY = state.targetBgX = state.targetBgY = 0;
            // stop animation after resetDuration (gives time for reset lerp)
            setTimeout(() => {
              if (state.rafId) {
                cancelAnimationFrame(state.rafId);
                state.rafId = null;
                state.isAnimating = false;
              }
            }, Config.resetDuration);
          };

          const onFocus = () => {
            state.rect = null;
          };

          const onKeyDown = (ev) => {
            if (ev.key === 'Enter' || ev.key === ' ') {
              ev.preventDefault();
              const link = card.querySelector('.card-link, .episode-link');
              if (link) link.click();
            } else if (ev.key === 'Escape') {
              onLeave();
            }
          };

          // Attach listeners
          card.addEventListener('mousemove', onMouseMove);
          card.addEventListener('mouseleave', onLeave);
          card.addEventListener('touchmove', onTouchMove, { passive: true });
          card.addEventListener('touchend', onLeave, { passive: true });
          card.addEventListener('focus', onFocus);
          card.addEventListener('blur', onFocus);
          card.addEventListener('keydown', onKeyDown);

          // store state so we can teardown later
          cardsState.set(card, {
            state,
            handlers: { onMouseMove, onLeave, onTouchMove, onFocus, onKeyDown },
          });
        } catch (err) {
          // continue with other cards
          safeError('initCardEffects: card init error', err);
        }
      });

      // push a cleanup function to listeners list to remove handlers on pagehide/unload
      listeners.push(() => {
        cardsState && qsa('.card, .episode-card').forEach((card) => {
          const entry = cardsState.get(card);
          if (!entry) return;
          try {
            const { handlers } = entry;
            card.removeEventListener('mousemove', handlers.onMouseMove);
            card.removeEventListener('mouseleave', handlers.onLeave);
            card.removeEventListener('touchmove', handlers.onTouchMove);
            card.removeEventListener('touchend', handlers.onLeave);
            card.removeEventListener('focus', handlers.onFocus);
            card.removeEventListener('blur', handlers.onFocus);
            card.removeEventListener('keydown', handlers.onKeyDown);
            if (entry.state.rafId) cancelAnimationFrame(entry.state.rafId);
          } catch (e) {
            /* ignore */
          }
        });
      });
    }

    function destroy() {
      try {
        listeners.forEach((fn) => {
          try {
            fn();
          } catch (e) {}
        });
      } catch (e) {/* ignore */}
    }

    function init() {
      try {
        setActiveNavLink();
        initCardEffects();
        safeLog('Global initialized');
      } catch (err) {
        safeError('Global init error', err);
      }
    }

    return { init, destroy };
  })();

  // ---------- INIT & LIFECYCLE ----------
  function initAll() {
    try {
      Global.init();
    } catch (err) {
      safeError('initAll error', err);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  // Cleanup on pagehide/unload so listeners / rAF are not left alive in SPA navs
  function teardown() {
    try {
      if (Global && typeof Global.destroy === 'function') Global.destroy();
    } catch (e) {
      /* ignore */
    }
  }
  if (typeof window !== 'undefined') {
    window.addEventListener('pagehide', teardown);
    window.addEventListener('beforeunload', teardown);
  }

  // Expose debug API (non-invasive)
  try {
    if (typeof window !== 'undefined') {
      window.__NetVOD = window.__NetVOD || {};
      window.__NetVOD.initAll = initAll;
      window.__NetVOD.modules = window.__NetVOD.modules || {};
      window.__NetVOD.modules.Global = Global;
    }
  } catch (e) {
    /* ignore */
  }
})();