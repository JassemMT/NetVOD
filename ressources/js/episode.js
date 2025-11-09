/* ═══════════════════════════════════════════════════════════════════════════ */
/* PURESTREAM FUSION — PAGE ÉPISODE */
/* À charger UNIQUEMENT sur la page de lecture d'épisode */
/* ═══════════════════════════════════════════════════════════════════════════ */

(function() {
  'use strict';

  console.log('[PureStream Fusion] Episode page.js loaded');

  /* ─────────────────────────────────────────────────────────────────────── */
  /* LECTEUR VIDÉO (exemple) */
  /* ─────────────────────────────────────────────────────────────────────── */

  function initVideoPlayer() {
    const player = document.querySelector('[data-player]');
    
    if (!player) {
      console.log('[PureStream Fusion] No video player found');
      return;
    }

    console.log('[PureStream Fusion] Video player initialized');

    // Logique lecteur (play, pause, fullscreen, etc.)
  }

  /* ─────────────────────────────────────────────────────────────────────── */
  /* BOUTONS ACTIONS */
  /* ─────────────────────────────────────────────────────────────────────── */

  function initEpisodeActions() {
    const playBtn = document.querySelector('[data-action="play"]');
    const subtitlesBtn = document.querySelector('[data-action="subtitles"]');

    if (playBtn) {
      playBtn.addEventListener('click', () => {
        console.log('[PureStream Fusion] Play clicked');
      });
    }

    if (subtitlesBtn) {
      subtitlesBtn.addEventListener('click', () => {
        console.log('[PureStream Fusion] Subtitles toggled');
      });
    }
  }

  /* ─────────────────────────────────────────────────────────────────────── */
  /* INIT */
  /* ─────────────────────────────────────────────────────────────────────── */

  const init = () => {
    initVideoPlayer();
    initEpisodeActions();
    console.log('[PureStream Fusion] Episode page interactions initialized');
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();