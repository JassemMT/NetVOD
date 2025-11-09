/* ═══════════════════════════════════════════════════════════════════════════ */
/* PURESTREAM FUSION — INTERACTIONS GLOBALES */
/* À charger sur TOUTES les pages */
/* ═══════════════════════════════════════════════════════════════════════════ */

(function() {
  'use strict';

  console.log('[PureStream Fusion] Global app.js loaded');

  /* ─────────────────────────────────────────────────────────────────────── */
  /* CONFIGURATION */
  /* ─────────────────────────────────────────────────────────────────────── */

  const config = {
    // Rotation 3D
    maxRotation: 14,
    rotationDamp: 0.09,

    // Parallaxe
    maxParallax: 36,
    parallaxScale: 1.12,
    bandParallaxFactor: 0.18,

    // Reset
    resetDuration: 480,
  };

  /* ─────────────────────────────────────────────────────────────────────── */
  /* UTILITAIRES */
  /* ─────────────────────────────────────────────────────────────────────── */

  /**
   * Interpolation linéaire (lerp)
   */
  const lerp = (a, b, t) => a + (b - a) * t;

  /* ─────────────────────────────────────────────────────────────────────── */
  /* EFFET 3D PARALLAXE — CARTES */
  /* ─────────────────────────────────────────────────────────────────────── */

  function initCardEffects() {
    const cards = Array.from(document.querySelectorAll('.card, .episode-card'));

    if (cards.length === 0) return;

    cards.forEach((card) => {
      const cardImage = card.querySelector('.card-image, .episode-image');
      const cardBand = card.querySelector('.card-band, .episode-band');

      if (!cardImage || !cardBand) return;

      let targetRotX = 0, targetRotY = 0;
      let currentRotX = 0, currentRotY = 0;
      let targetBgX = 0, targetBgY = 0;
      let currentBgX = 0, currentBgY = 0;
      let rect = null;
      let animationFrameId = null;

      const animate = () => {
        currentRotX = lerp(currentRotX, targetRotX, config.rotationDamp);
        currentRotY = lerp(currentRotY, targetRotY, config.rotationDamp);
        currentBgX = lerp(currentBgX, targetBgX, config.rotationDamp);
        currentBgY = lerp(currentBgY, targetBgY, config.rotationDamp);

        card.style.transform = `perspective(1000px) rotateX(${currentRotX}deg) rotateY(${currentRotY}deg)`;
        cardImage.style.transform = `translate3d(${currentBgX}px, ${currentBgY}px, 0) scale(${config.parallaxScale})`;
        cardBand.style.transform = `translate3d(${currentBgX * config.bandParallaxFactor}px, ${currentBgY * config.bandParallaxFactor}px, 0)`;

        animationFrameId = requestAnimationFrame(animate);
      };

      const onMouseMove = (e) => {
        rect = rect || card.getBoundingClientRect();

        const px = (e.clientX - rect.left) / rect.width;
        const py = (e.clientY - rect.top) / rect.height;
        const cx = px - 0.5;
        const cy = py - 0.5;

        targetRotY = cx * config.maxRotation;
        targetRotX = -cy * config.maxRotation;
        targetBgX = -cx * config.maxParallax;
        targetBgY = -cy * config.maxParallax;

        if (!animationFrameId) animate();
      };

      const onMouseLeave = () => {
        targetRotX = 0;
        targetRotY = 0;
        targetBgX = 0;
        targetBgY = 0;

        setTimeout(() => {
          if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
          }
        }, config.resetDuration);
      };

      const onTouchMove = (e) => {
        if (e.touches?.[0]) onMouseMove(e.touches[0]);
      };

      const onKeyDown = (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          const link = card.querySelector('.card-link, .episode-link');
          if (link) link.click();
        }
      };

      const onFocus = () => { rect = null; };
      const onBlur = () => { onMouseLeave(); };

      card.addEventListener('mousemove', onMouseMove);
      card.addEventListener('mouseleave', onMouseLeave);
      card.addEventListener('touchmove', onTouchMove, { passive: true });
      card.addEventListener('touchend', onMouseLeave, { passive: true });
      card.addEventListener('keydown', onKeyDown);
      card.addEventListener('focus', onFocus);
      card.addEventListener('blur', onBlur);
    });
  }

  /* ─────────────────────────────────────────────────────────────────────── */
  /* NAVIGATION ACTIVE STATE */
  /* ─────────────────────────────────────────────────────────────────────── */

  function setActiveNavLink() {
    const currentUrl = window.location.href;
    const navLinks = Array.from(document.querySelectorAll('.nav a'));

    navLinks.forEach((link) => {
      link.removeAttribute('aria-current');
      if (link.href === currentUrl || window.location.href.includes(link.href)) {
        link.setAttribute('aria-current', 'page');
      }
    });
  }

  /* ─────────────────────────────────────────────────────────────────────── */
  /* INIT */
  /* ─────────────────────────────────────────────────────────────────────── */

  const init = () => {
    initCardEffects();
    setActiveNavLink();
    console.log('[PureStream Fusion] Global interactions initialized');
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();