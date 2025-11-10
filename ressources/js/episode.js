// resources/js/pages/episode.js
(function() {
  'use strict';

  const qs = (sel, root = document) => root.querySelector(sel);

  function initVideoPlayer() {
    const player = qs('#player');
    if (!player) return;

    // Si le navigateur ne calcule pas encore la hauteur, on attend loadedmetadata
    const applyAspectFromMetadata = () => {
      try {
        const w = player.videoWidth;
        const h = player.videoHeight;
        if (w && h) {
          // applique un aspect-ratio inline si le navigateur le supporte
          if ('aspectRatio' in player.style || CSS.supports('aspect-ratio: 16/9')) {
            player.style.aspectRatio = `${w}/${h}`; // fallback utile
          } else {
            // fallback : on règle le parent (padding hack non nécessaire si CSS a fallback)
            const parent = player.closest('.episode-player');
            if (parent) {
              parent.style.paddingTop = `${(h / w) * 100}%`;
            }
          }
        }
      } catch (err) {
        console.warn('[episode.js] unable to apply aspect from metadata', err);
      }
    };

    if (player.readyState >= 1) {
      // metadata déjà chargées
      applyAspectFromMetadata();
    } else {
      player.addEventListener('loadedmetadata', applyAspectFromMetadata, { once: true });
    }

    // --- le reste de votre code existant (restore/save position, raccourcis, etc.) ---
    // restore position
    const episodeId = player.getAttribute('data-episode-id') || (new URL(window.location.href)).searchParams.get('id') || '';
    try {
      const key = `netvod:episode:pos:${episodeId}`;
      const saved = localStorage.getItem(key);
      if (saved) {
        const pos = parseFloat(saved);
        if (!Number.isNaN(pos) && pos > 0) player.currentTime = pos;
      }
    } catch (err) {}

    let lastSaved = 0;
    const SAVE_INTERVAL = 2500;
    player.addEventListener('timeupdate', () => {
      const now = Date.now();
      if (now - lastSaved < SAVE_INTERVAL) return;
      lastSaved = now;
      try {
        const key = `netvod:episode:pos:${episodeId}`;
        localStorage.setItem(key, player.currentTime.toString());
      } catch (err) {}
    });

    player.addEventListener('ended', () => {
      try { localStorage.removeItem(`netvod:episode:pos:${episodeId}`); } catch (e) {}
    });

    // keyboard controls
    window.addEventListener('keydown', (e) => {
      const tag = document.activeElement?.tagName?.toLowerCase();
      if (tag === 'input' || tag === 'textarea') return;
      if (e.code === 'Space' && player.offsetParent !== null) {
        e.preventDefault();
        if (player.paused) player.play(); else player.pause();
      }
      if (e.code === 'ArrowLeft') { player.currentTime = Math.max(0, player.currentTime - 5); }
      if (e.code === 'ArrowRight') { player.currentTime = Math.min(player.duration || Infinity, player.currentTime + 5); }
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initVideoPlayer);
  else initVideoPlayer();
})();