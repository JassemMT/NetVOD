/* ═══════════════════════════════════════════════════════════════════════════ */
/* PURESTREAM FUSION — PAGE SÉRIE */
/* À charger UNIQUEMENT sur la page de détail d'une série */
/* ═══════════════════════════════════════════════════════════════════════════ */

(function() {
  'use strict';

  console.log('[PureStream Fusion] Serie page.js loaded');

  /* ─────────────────────────────────────────────────────────────────────── */
  /* HERO IMAGE ZOOM */
  /* ─────────────────────────────────────────────────────────────────────── */

  function initHeroImage() {
    const heroImageContainer = document.querySelector('.hero-image');
    
    if (!heroImageContainer) {
      console.log('[PureStream Fusion] No .hero-image found on this page');
      return;
    }

    const heroImg = heroImageContainer.querySelector('img');

    if (!heroImg) {
      console.log('[PureStream Fusion] No img inside .hero-image');
      return;
    }

    console.log('[PureStream Fusion] Hero image zoom effect initialized');

    const onMouseEnter = () => {
      heroImg.style.transition = 'transform 0.4s ease-out';
      heroImg.style.transform = 'scale(1.08)';
    };

    const onMouseLeave = () => {
      heroImg.style.transition = 'transform 0.5s ease-out';
      heroImg.style.transform = 'scale(1)';
    };

    const onTouchStart = () => {
      heroImg.style.transition = 'transform 0.4s ease-out';
      heroImg.style.transform = 'scale(1.08)';
    };

    const onTouchEnd = () => {
      heroImg.style.transition = 'transform 0.5s ease-out';
      heroImg.style.transform = 'scale(1)';
    };

    heroImageContainer.addEventListener('mouseenter', onMouseEnter);
    heroImageContainer.addEventListener('mouseleave', onMouseLeave);
    heroImageContainer.addEventListener('touchstart', onTouchStart, { passive: true });
    heroImageContainer.addEventListener('touchend', onTouchEnd, { passive: true });
  }

  /* ─────────────────────────────────────────────────────────────────────── */
  /* BOUTONS ACTIONS */
  /* ─────────────────────────────────────────────────────────────────────── */

  /*
  function initHeroActions() {
    const btnPrimary = document.querySelector('.btn-primary');
    const btnSecondary = document.querySelector('.btn-secondary');

    if (btnPrimary) {
      btnPrimary.addEventListener('click', (e) => {
        e.preventDefault();
        console.log('[PureStream Fusion] Regarder clicked');
        // Logique : redirection vers lecteur vidéo, modal, etc.
      });
    }

    if (btnSecondary) {
      btnSecondary.addEventListener('click', (e) => {
        e.preventDefault();
        console.log('[PureStream Fusion] Ajouter aux favoris clicked');
        // Logique : ajouter à favoris (API call, localStorage, etc.)
      });
    }
  }
  */

  /* ─────────────────────────────────────────────────────────────────────── */
  /* INIT */
  /* ─────────────────────────────────────────────────────────────────────── */

  const init = () => {
    initHeroImage();
    initHeroActions();
    console.log('[PureStream Fusion] Serie page interactions initialized');
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();