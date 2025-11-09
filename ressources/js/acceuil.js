/* ═══════════════════════════════════════════════════════════════════════════ */
/* PURESTREAM FUSION — PAGE ACCUEIL */
/* À charger UNIQUEMENT sur la page d'accueil */
/* ═══════════════════════════════════════════════════════════════════════════ */

(function() {
  'use strict';

  console.log('[PureStream Fusion] Accueil page.js loaded');

  /* ─────────────────────────────────────────────────────────────────────── */
  /* CAROUSEL / SLIDER (exemple) */
  /* ─────────────────────────────────────────────────────────────────────── */

  function initCarousel() {
    const carousel = document.querySelector('[data-carousel]');
    
    if (!carousel) {
      console.log('[PureStream Fusion] No carousel found');
      return;
    }

    console.log('[PureStream Fusion] Carousel initialized');
    // Logique carousel
  }

  /* ─────────────────────────────────────────────────────────────────────── */
  /* INIT */
  /* ─────────────────────────────────────────────────────────────────────── */

  const init = () => {
    initCarousel();
    console.log('[PureStream Fusion] Accueil page interactions initialized');
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();