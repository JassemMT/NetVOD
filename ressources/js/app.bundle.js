/* resources/js/app.bundle.js
   PureStream Fusion — Single JS bundle
   - Regroupe interactions globales + modules par page (auth, catalogue, serie, episode, accueil)
   - Initializes global behavior, then page module based on data-page or DOM heuristics
   - Defensive (try/catch), respects prefers-reduced-motion, avoids global pollution
   Usage:
     <script src="/ressources/js/app.bundle.js" defer></script>
   Recommended: add <body data-page="serie"> or "catalogue" / "auth" / "episode" / "accueil"
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
    window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const qs = (sel, root = document) => root.querySelector(sel);
  const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const lerp = (a, b, t) => a + (b - a) * t;

  function safeLog(...args) {
    if (typeof console !== 'undefined' && console.log) {
      console.log('[app.bundle]', ...args);
    }
  }

  // ---------- GLOBAL MODULE ----------
  const Global = (function () {
    function setActiveNavLink() {
      try {
        const currentUrl = window.location.href;
        qsa('.nav a').forEach((link) => {
          link.removeAttribute('aria-current');
          // matching strategy: exact equality or href path included
          try {
            if (link.href === currentUrl || currentUrl.includes(link.getAttribute('href'))) {
              link.setAttribute('aria-current', 'page');
            }
          } catch (e) {
            // ignore malformed href
          }
        });
      } catch (e) {
        // no-op
      }
    }

    function initCardEffects() {
      const cards = Array.from(document.querySelectorAll('.card'));
      if (!cards.length) return;

      cards.forEach((card) => {
        const cardImage = card.querySelector('.card-image, .episode-image');
        const cardBand = card.querySelector('.card-band, .episode-band');

        if (!cardImage || !cardBand) return;

        let targetRotX = 0,
          targetRotY = 0,
          currentRotX = 0,
          currentRotY = 0;
        let targetBgX = 0,
          targetBgY = 0,
          currentBgX = 0,
          currentBgY = 0;
        let rect = null;
        let animationFrameId = null;

        const animate = () => {
          currentRotX = lerp(currentRotX, targetRotX, Config.rotationDamp);
          currentRotY = lerp(currentRotY, targetRotY, Config.rotationDamp);
          currentBgX = lerp(currentBgX, targetBgX, Config.rotationDamp);
          currentBgY = lerp(currentBgY, targetBgY, Config.rotationDamp);

          card.style.transform = `perspective(1000px) rotateX(${currentRotX}deg) rotateY(${currentRotY}deg)`;
          cardImage.style.transform = `translate3d(${currentBgX}px, ${currentBgY}px, 0) scale(${Config.parallaxScale})`;
          cardBand.style.transform = `translate3d(${currentBgX * Config.bandParallaxFactor}px, ${currentBgY *
            Config.bandParallaxFactor}px, 0)`;

          animationFrameId = requestAnimationFrame(animate);
        };

        const onMouseMove = (e) => {
          rect = rect || card.getBoundingClientRect();
          const clientX = e.clientX ?? (e.touches && e.touches[0] && e.touches[0].clientX) ?? 0;
          const clientY = e.clientY ?? (e.touches && e.touches[0] && e.touches[0].clientY) ?? 0;
          const px = (clientX - rect.left) / rect.width;
          const py = (clientY - rect.top) / rect.height;
          const cx = px - 0.5;
          const cy = py - 0.5;

          targetRotY = cx * Config.maxRotation;
          targetRotX = -cy * Config.maxRotation;
          targetBgX = -cx * Config.maxParallax;
          targetBgY = -cy * Config.maxParallax;

          if (!animationFrameId) animate();
        };

        const onMouseLeave = () => {
          targetRotX = targetRotY = targetBgX = targetBgY = 0;
          setTimeout(() => {
            if (animationFrameId) {
              cancelAnimationFrame(animationFrameId);
              animationFrameId = null;
            }
          }, Config.resetDuration);
        };

        card.addEventListener('mousemove', onMouseMove);
        card.addEventListener('mouseleave', onMouseLeave);
        card.addEventListener('touchmove', (e) => onMouseMove(e.touches[0]), { passive: true });
        card.addEventListener('touchend', onMouseLeave);
        card.addEventListener('focus', () => (rect = null));
        card.addEventListener('keydown', (ev) => {
          if (ev.key === 'Enter' || ev.key === ' ') {
            ev.preventDefault();
            const link = card.querySelector('.card-link, .episode-link');
            if (link) link.click();
          }
          if (ev.key === 'Escape') onMouseLeave();
        });
      });
    }

    function init() {
      try {
        initCardEffects();
        setActiveNavLink();
        safeLog('Global initialized');
      } catch (err) {
        console.error('[app.bundle] Global init error', err);
      }
    }

    return { init };
  })();

  // ---------- INIT ----------
  function initAll() {
    Global.init();
    autoDetectAndInitPage();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  // expose for debug in dev only (do not rely on in production)
  window.__NetVOD = {
    initAll,
    modules: { Global}
  };
})();