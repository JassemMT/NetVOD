// resources/js/pages/episode.js
// Page-specific logic for episode playback + rating form
(function() {
  'use strict';

  // Key config
  const STORAGE_KEY_POS = (epId='') => `netvod:episode:pos:${epId}`;

  // Safe helper
  const qs = (sel, root=document) => root.querySelector(sel);

  function initVideoPlayer() {
    const player = qs('#player');
    if (!player) return;

    const src = player.currentSrc || player.querySelector('source')?.src || '';
    // attempt to derive an episode id from the URL or data attributes
    // if your server prints episode id into DOM, use it: data-episode-id on body or player
    const episodeId = player.getAttribute('data-episode-id') || (new URL(window.location.href)).searchParams.get('id') || '';

    // restore position if stored
    try {
      const key = STORAGE_KEY_POS(episodeId);
      const saved = localStorage.getItem(key);
      if (saved) {
        const pos = parseFloat(saved);
        if (!Number.isNaN(pos) && pos > 0 && pos < player.duration) {
          player.currentTime = pos;
        }
      }
    } catch (err) {
      console.warn('[episode.js] unable to restore position', err);
    }

    // save position periodically (throttled)
    let lastSaved = 0;
    const SAVE_INTERVAL = 2500; // ms

    const onTimeUpdate = () => {
      const now = Date.now();
      if (now - lastSaved < SAVE_INTERVAL) return;
      lastSaved = now;
      try {
        const key = STORAGE_KEY_POS(episodeId);
        localStorage.setItem(key, player.currentTime.toString());
      } catch (err) {
        // ignore storage errors
      }
    };

    player.addEventListener('timeupdate', onTimeUpdate);

    // keyboard: space to play/pause when player focused or body focused
    window.addEventListener('keydown', (e) => {
      // ignore when focusing inputs
      const tag = document.activeElement?.tagName?.toLowerCase();
      if (tag === 'input' || tag === 'textarea') return;
      if (e.code === 'Space') {
        // toggle only if video is visible
        if (player.offsetParent !== null) {
          e.preventDefault();
          if (player.paused) player.play(); else player.pause();
        }
      }
      // left/right for seek
      if (e.code === 'ArrowLeft') {
        player.currentTime = Math.max(0, player.currentTime - 5);
      }
      if (e.code === 'ArrowRight') {
        player.currentTime = Math.min(player.duration || Infinity, player.currentTime + 5);
      }
    });

    // on ended -> clear saved position
    player.addEventListener('ended', () => {
      try {
        const key = STORAGE_KEY_POS(episodeId);
        localStorage.removeItem(key);
      } catch (err) {}
    });
  }

  function initNoteForm() {
    const form = qs('.note-form');
    if (!form) return;

    const submit = qs('button[type="submit"]', form) || qs('input[type="submit"]', form);
    const errorEl = qs('.form-error', form);

    let submitting = false;

    form.addEventListener('submit', (e) => {
      if (submitting) {
        e.preventDefault();
        return;
      }

      // basic HTML5 validation
      if (!form.checkValidity()) {
        e.preventDefault();
        const invalid = form.querySelector(':invalid');
        const label = invalid ? (form.querySelector(`label[for="${invalid.id}"]`)?.textContent || invalid.name) : 'Formulaire invalide';
        showFormError(form, `${label} invalide ou manquant.`, invalid);
        return;
      }

      // ensure note in [1,5]
      const noteInput = qs('#note', form);
      if (noteInput) {
        const val = Number(noteInput.value);
        if (!Number.isFinite(val) || val < 1 || val > 5) {
          e.preventDefault();
          showFormError(form, 'La note doit être un nombre entre 1 et 5.', noteInput);
          return;
        }
      }

      // ok -> prevent double submit
      submitting = true;
      if (submit) {
        submit.disabled = true;
        submit.setAttribute('aria-disabled', 'true');
      }
      // let the form submit to server
    });

    function showFormError(form, message, focusField) {
      const el = qs('.form-error', form);
      if (!el) return;
      el.textContent = message;
      el.style.display = 'block';
      if (focusField && typeof focusField.focus === 'function') focusField.focus();
    }

    // Clear error on input
    Array.from(form.querySelectorAll('input')).forEach(input => {
      input.addEventListener('input', () => {
        const el = qs('.form-error', form);
        if (el) el.style.display = 'none';
      }, { passive: true });
    });
  }

  function init() {
    try {
      initVideoPlayer();
      initNoteForm();
      // debug
      // console.log('[episode.js] init');
    } catch (err) {
      console.error('[episode.js] init error', err);
    }
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();

})();