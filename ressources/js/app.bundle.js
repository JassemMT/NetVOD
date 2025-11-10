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

  // ---------- AUTH MODULE (login + register) ----------
  const Auth = (function () {
    function showError(form, message, focusField) {
      if (!form) return;
      const errorEl = form.querySelector('.form-error');
      if (!errorEl) return;
      errorEl.textContent = message;
      errorEl.classList.add('is-visible');
      errorEl.setAttribute('aria-hidden', 'false');
      if (focusField && typeof focusField.focus === 'function') focusField.focus();
    }

    function hideError(form) {
      if (!form) return;
      const errorEl = form.querySelector('.form-error');
      if (!errorEl) return;
      errorEl.textContent = '';
      errorEl.classList.remove('is-visible');
      errorEl.setAttribute('aria-hidden', 'true');
    }

    function initToggles(form) {
      const toggles = qsa('.toggle-pw', form);
      toggles.forEach((btn) => {
        btn.addEventListener('click', (ev) => {
          ev.preventDefault();
          const wrapper = btn.closest('.input-with-toggle');
          if (!wrapper) return;
          const input = wrapper.querySelector('input[type="password"], input[type="text"]');
          if (!input) return;
          const isPwd = input.getAttribute('type') === 'password';
          input.setAttribute('type', isPwd ? 'text' : 'password');
          btn.setAttribute('aria-pressed', String(isPwd));
          btn.textContent = isPwd ? 'Masquer' : 'Voir';
        });
      });
    }

    function validateForm(form) {
      const email = form.querySelector('input[type="email"]');
      const pwd1 = form.querySelector('input[name="password1"], input[name="password"]');
      const pwd2 = form.querySelector('input[name="password2"]');

      if (email && !email.checkValidity()) {
        return { ok: false, message: 'Adresse e‑mail invalide', field: email };
      }
      if (pwd1 && !pwd1.checkValidity()) {
        return { ok: false, message: 'Veuillez saisir votre mot de passe', field: pwd1 };
      }
      if (pwd2) {
        if (!pwd2.checkValidity()) {
          return { ok: false, message: 'Veuillez confirmer votre mot de passe', field: pwd2 };
        }
        if (pwd1 && pwd1.value !== pwd2.value) {
          return { ok: false, message: 'Les mots de passe ne correspondent pas', field: pwd2 };
        }
      }
      return { ok: true };
    }

    function initForm(form) {
      if (!form) return;
      let submitting = false;
      initToggles(form);

      qsa('input', form).forEach((i) => i.addEventListener('input', () => hideError(form), { passive: true }));
      window.requestAnimationFrame(() => {
        const first = form.querySelector('input:not([type="hidden"])');
        if (first) first.focus();
      });

      form.addEventListener('submit', (ev) => {
        if (submitting) {
          ev.preventDefault();
          return;
        }
        const res = validateForm(form);
        if (!res.ok) {
          ev.preventDefault();
          showError(form, res.message, res.field);
          return;
        }
        submitting = true;
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitBtn) {
          setTimeout(() => {
            submitBtn.disabled = true;
            submitBtn.setAttribute('aria-disabled', 'true');
          }, 20);
        }
      });
    }

    function init() {
      try {
        const forms = qsa('.auth-form');
        if (!forms.length) return safeLog('Auth: no auth forms');
        forms.forEach(initForm);
        safeLog('Auth initialized');
      } catch (err) {
        console.error('[app.bundle] Auth init error', err);
      }
    }

    return { init };
  })();

  // ---------- CATALOGUE MODULE ----------
  const Catalogue = (function () {
    function initFilters() {
      const filterInputs = qsa('[data-filter]');
      if (!filterInputs.length) return;
      filterInputs.forEach((input) => {
        input.addEventListener('change', (e) => {
          // Placeholder: implement filtering or emit event
          safeLog('filter changed', e.target.value);
        });
      });
    }

    function initPagination() {
      const paginationBtns = qsa('[data-pagination]');
      if (!paginationBtns.length) return;
      paginationBtns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          safeLog('pagination', btn.dataset.pagination);
        });
      });
    }

    function init() {
      try {
        initFilters();
        initPagination();
        safeLog('Catalogue initialized');
      } catch (err) {
        console.error('[app.bundle] Catalogue init error', err);
      }
    }

    return { init };
  })();

  // ---------- SERIE MODULE (detail Série) ----------
  const Serie = (function () {
    function initHeroImage() {
      const container = qs('.hero-image');
      if (!container) return;
      const img = container.querySelector('img');
      if (!img) return;
      const enter = () => {
        if (!prefersReducedMotion) {
          img.style.transition = 'transform 0.4s ease-out';
          img.style.transform = 'scale(1.08)';
        }
      };
      const leave = () => {
        if (!prefersReducedMotion) {
          img.style.transition = 'transform 0.5s ease-out';
          img.style.transform = 'scale(1)';
        }
      };
      container.addEventListener('mouseenter', enter);
      container.addEventListener('mouseleave', leave);
      container.addEventListener('touchstart', enter, { passive: true });
      container.addEventListener('touchend', leave, { passive: true });
    }

    function initHeroActions() {
      const btnPrimary = qs('.btn-primary');
      const btnSecondary = qs('.btn-secondary');
      if (btnPrimary) {
        btnPrimary.addEventListener('click', (e) => {
          // Placeholder: go to player or open modal
          safeLog('Regarder clicked');
        });
      }
      if (btnSecondary) {
        btnSecondary.addEventListener('click', (e) => {
          e.preventDefault();
          safeLog('Ajouter aux favoris clicked');
        });
      }
    }

    function init() {
      try {
        initHeroImage();
        initHeroActions();
        safeLog('Serie initialized');
      } catch (err) {
        console.error('[app.bundle] Serie init error', err);
      }
    }

    return { init };
  })();

  // ---------- EPISODE MODULE (player + note form) ----------
  const Episode = (function () {
    function initVideo() {
      const player = qs('#player');
      if (!player) return;

      // Apply aspect ratio fallback if metadata available
      function applyAspect() {
        try {
          const w = player.videoWidth;
          const h = player.videoHeight;
          if (w && h) {
            if (CSS && CSS.supports && CSS.supports('aspect-ratio: 16/9')) {
              // apply to container instead to preserve style
              const parent = player.closest('.episode-player');
              if (parent) parent.style.aspectRatio = `${w}/${h}`;
            }
          }
        } catch (e) {}
      }

      if (player.readyState >= 1) applyAspect();
      else player.addEventListener('loadedmetadata', applyAspect, { once: true });

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
        try {
          const key = `netvod:episode:pos:${episodeId}`;
          localStorage.removeItem(key);
        } catch (err) {}
      });

      // keyboard controls
      window.addEventListener('keydown', (e) => {
        const tag = document.activeElement?.tagName?.toLowerCase();
        if (tag === 'input' || tag === 'textarea') return;
        if (e.code === 'Space' && player.offsetParent !== null) {
          e.preventDefault();
          if (player.paused) player.play();
          else player.pause();
        }
        if (e.code === 'ArrowLeft') player.currentTime = Math.max(0, player.currentTime - 5);
        if (e.code === 'ArrowRight') player.currentTime = Math.min(player.duration || Infinity, player.currentTime + 5);
      });
    }

    function initNoteForm() {
      const form = qs('.note-form');
      if (!form) return;
      let submitting = false;
      const submit = form.querySelector('button[type="submit"], input[type="submit"]');
      const errorEl = form.querySelector('.form-error');

      function showFormError(message, focusField) {
        if (!errorEl) return;
        errorEl.textContent = message;
        errorEl.style.display = 'block';
        if (focusField && typeof focusField.focus === 'function') focusField.focus();
      }

      form.addEventListener('submit', (e) => {
        if (submitting) {
          e.preventDefault();
          return;
        }
        if (!form.checkValidity()) {
          e.preventDefault();
          const invalid = form.querySelector(':invalid');
          const label = invalid ? (form.querySelector(`label[for="${invalid.id}"]`)?.textContent || invalid.name) : 'Formulaire invalide';
          showFormError(`${label} invalide ou manquant.`, invalid);
          return;
        }
        const noteInput = qs('#note', form);
        if (noteInput) {
          const val = Number(noteInput.value);
          if (!Number.isFinite(val) || val < 1 || val > 5) {
            e.preventDefault();
            showFormError('La note doit être un nombre entre 1 et 5.', noteInput);
            return;
          }
        }
        submitting = true;
        if (submit) {
          submit.disabled = true;
          submit.setAttribute('aria-disabled', 'true');
        }
      });

      qsa('input', form).forEach((i) => i.addEventListener('input', () => { if (errorEl) errorEl.style.display = 'none'; }, { passive: true }));
    }

    function init() {
      try {
        initVideo();
        initNoteForm();
        safeLog('Episode initialized');
      } catch (err) {
        console.error('[app.bundle] Episode init error', err);
      }
    }

    return { init };
  })();

  // ---------- ACCUEIL MODULE (placeholder) ----------
  const Accueil = (function () {
    function initCarousel() {
      const carousel = qs('[data-carousel]');
      if (!carousel) return;
      safeLog('carousel found (placeholder)');
      // implement as needed
    }

    function init() {
      try {
        initCarousel();
        safeLog('Accueil initialized');
      } catch (err) {
        console.error('[app.bundle] Accueil init error', err);
      }
    }

    return { init };
  })();

  // ---------- BOOTSTRAP / ROUTING ----------
  function detectPageFromBody() {
    const body = document.body;
    return body && body.dataset && body.dataset.page ? body.dataset.page : null;
  }

  function initPageModule(pageName) {
    const map = {
      auth: Auth,
      catalogue: Catalogue,
      serie: Serie,
      episode: Episode,
      accueil: Accueil,
      home: Accueil,
    };
    const mod = map[pageName];
    if (mod && typeof mod.init === 'function') {
      try {
        mod.init();
        return true;
      } catch (err) {
        console.error('[app.bundle] error initializing module', pageName, err);
      }
    }
    return false;
  }

  function autoDetectAndInitPage() {
    // Prefer explicit data-page on body
    const page = detectPageFromBody();
    if (page) {
      safeLog('detected page via data-page:', page);
      if (initPageModule(page)) return;
    }

    // Fallback heuristics: presence of elements
    if (qs('.auth-form')) {
      Auth.init();
      return;
    }
    if (qs('.grid')) {
      Catalogue.init();
      return;
    }
    if (qs('.hero-serie')) {
      Serie.init();
      return;
    }
    if (qs('#player')) {
      Episode.init();
      return;
    }
    if (qs('[data-carousel]')) {
      Accueil.init();
      return;
    }
  }

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
    modules: { Global, Auth, Catalogue, Serie, Episode, Accueil },
  };
})();