/* ═══════════════════════════════════════════════════════════════════════════ */
/* PURESTREAM FUSION — INTERACTIONS GLOBALES */
/* Effet 3D parallaxe, animations, interactivité générale */
/* ═══════════════════════════════════════════════════════════════════════════ */

(function() {
  'use strict';

  /* ─────────────────────────────────────────────────────────────────────── */
  /* CONFIGURATION */
  /* ─────────────────────────────────────────────────────────────────────── */

  const config = {
    // Rotation 3D
    maxRotation: 14,        // degrés max
    rotationDamp: 0.09,     // factor interpolation (lerp)

    // Parallaxe image
    maxParallax: 36,        // pixels max
    parallaxScale: 1.12,    // zoom initial

    // Band parallaxe (subtile)
    bandParallaxFactor: 0.18,

    // Réinitialisation au mouseLeave
    resetDuration: 480,     // ms
  };

  /* ─────────────────────────────────────────────────────────────────────── */
  /* UTILITAIRES */
  /* ─────────────────────────────────────────────────────────────────────── */

  /**
   * Interpolation linéaire (lerp)
   * @param {number} a - Valeur actuelle
   * @param {number} b - Valeur cible
   * @param {number} t - Factor (0-1)
   * @returns {number}
   */
  const lerp = (a, b, t) => a + (b - a) * t;

  /**
   * Request Animation Frame wrapper avec cleanup
   */
  let globalRAF = null;
  const scheduleRAF = (callback) => {
    if (globalRAF) cancelAnimationFrame(globalRAF);
    globalRAF = requestAnimationFrame(callback);
    return globalRAF;
  };

  const cancelRAF = () => {
    if (globalRAF) {
      cancelAnimationFrame(globalRAF);
      globalRAF = null;
    }
  };

  /* ─────────────────────────────────────────────────────────────────────── */
  /* EFFET 3D PARALLAXE SUR LES CARTES */
  /* ─────────────────────────────────────────────────────────────────────── */

  function initCardEffects() {
    const cards = Array.from(document.querySelectorAll('.card'));

    if (cards.length === 0) return;

    cards.forEach((card) => {
      const cardImage = card.querySelector('.card-image');
      const cardBand = card.querySelector('.card-band');

      if (!cardImage || !cardBand) return;

      // État de rotation
      let targetRotX = 0;
      let targetRotY = 0;
      let currentRotX = 0;
      let currentRotY = 0;

      // État de parallaxe
      let targetBgX = 0;
      let targetBgY = 0;
      let currentBgX = 0;
      let currentBgY = 0;

      // Bounding rect (cache)
      let rect = null;
      let animationFrameId = null;

      /**
       * Boucle d'animation (requestAnimationFrame)
       */
      const animate = () => {
        // Interpolation douce (damping)
        currentRotX = lerp(currentRotX, targetRotX, config.rotationDamp);
        currentRotY = lerp(currentRotY, targetRotY, config.rotationDamp);
        currentBgX = lerp(currentBgX, targetBgX, config.rotationDamp);
        currentBgY = lerp(currentBgY, targetBgY, config.rotationDamp);

        // Applique transforms
        card.style.transform = `perspective(1000px) rotateX(${currentRotX}deg) rotateY(${currentRotY}deg)`;
        cardImage.style.transform = `translate3d(${currentBgX}px, ${currentBgY}px, 0) scale(${config.parallaxScale})`;

        // Band suit légèrement la parallaxe
        cardBand.style.transform = `translate3d(${currentBgX * config.bandParallaxFactor}px, ${currentBgY * config.bandParallaxFactor}px, 0)`;

        animationFrameId = requestAnimationFrame(animate);
      };

      /**
       * Handlers
       */
      const onMouseMove = (e) => {
        // Calcul bounding rect (une seule fois)
        rect = rect || card.getBoundingClientRect();

        const clientX = e.clientX;
        const clientY = e.clientY;

        // Position relative dans la carte (0-1)
        const px = (clientX - rect.left) / rect.width;
        const py = (clientY - rect.top) / rect.height;

        // Centre (-0.5 à 0.5)
        const cx = px - 0.5;
        const cy = py - 0.5;

        // Calcul rotations et parallaxe
        targetRotY = cx * config.maxRotation;
        targetRotX = -cy * config.maxRotation;
        targetBgX = -cx * config.maxParallax;
        targetBgY = -cy * config.maxParallax;

        // Démarre animation si pas déjà active
        if (!animationFrameId) {
          animate();
        }
      };

      const onMouseLeave = () => {
        // Reset targets
        targetRotX = 0;
        targetRotY = 0;
        targetBgX = 0;
        targetBgY = 0;

        // Continue animation un peu pour l'inertie, puis cleanup
        setTimeout(() => {
          if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
          }
        }, config.resetDuration);
      };

      const onTouchMove = (e) => {
        if (e.touches && e.touches[0]) {
          onMouseMove(e.touches[0]);
        }
      };

      const onTouchEnd = () => {
        onMouseLeave();
      };

      const onKeyDown = (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          const link = card.querySelector('.card-link');
          if (link) {
            link.click();
          }
        }
      };

      const onFocus = () => {
        // Réinitialise bounding rect au focus (cas mobile)
        rect = null;
      };

      const onBlur = () => {
        onMouseLeave();
      };

      /**
       * Enregistre listeners
       */
      card.addEventListener('mousemove', onMouseMove);
      card.addEventListener('mouseleave', onMouseLeave);
      card.addEventListener('touchmove', onTouchMove, { passive: true });
      card.addEventListener('touchend', onTouchEnd, { passive: true });
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
      
      // Détecte le lien actif
      if (link.href === currentUrl) {
        link.setAttribute('aria-current', 'page');
      }
    });
  }

  /* ─────────────────────────────────────────────────────────────────────── */
  /* INIT GLOBALE */
  /* ─────────────────────────────────────────────────────────────────────── */

  document.addEventListener('DOMContentLoaded', () => {
    initCardEffects();
    setActiveNavLink();

    // Logs de debug (optionnel)
    console.log('[PureStream Fusion] App initialized');
  });

  // Fallback si DOM déjà chargé
  if (document.readyState === 'loading') {
    // DOMContentLoaded s'exécutera
  } else {
    // DOM déjà prêt
    initCardEffects();
    setActiveNavLink();
    console.log('[PureStream Fusion] App initialized (no wait)');
  }

  /* ─────────────────────────────────────────────────────────────────────── */
  /* CLEANUP ON UNLOAD */
  /* ─────────────────────────────────────────────────────────────────────── */

  window.addEventListener('beforeunload', () => {
    cancelRAF();
  });

})();