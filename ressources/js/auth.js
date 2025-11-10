// ressources/js/pages/auth.js
// Page-specific JS for authentication pages (login & register).
// - Toggle password visibility (multiple toggles supported)
// - Basic client-side validation (email validity, required fields, password match for register)
// - Prevent double-submit
// - Accessible error reporting via .form-error (role="alert")
// - Focus management
// - Safe with prefers-reduced-motion
(function() {
  'use strict';

  const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function showError(form, message, focusField) {
    if (!form) return;
    const errorEl = form.querySelector('.form-error');
    if (!errorEl) return;
    errorEl.textContent = message;
    errorEl.classList.add('is-visible');
    errorEl.setAttribute('aria-hidden', 'false');
    if (focusField && typeof focusField.focus === 'function') {
      focusField.focus();
    }
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
    if (!form) return;
    const toggles = Array.from(form.querySelectorAll('.toggle-pw'));
    if (toggles.length === 0) return;

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
        // Toggle label — keep simple and accessible
        btn.textContent = isPwd ? 'Masquer' : 'Voir';
      });
    });
  }

  function basicValidation(form) {
    // HTML5 constraint validation covers types/required patterns.
    // We augment with specific rules for register (password match).
    if (!form) return { ok: true };

    // find relevant fields
    const email = form.querySelector('input[type="email"]');
    const pwd1 = form.querySelector('input[name="password1"], input[name="password"]');
    const pwd2 = form.querySelector('input[name="password2"]');

    // email validity
    if (email && !email.checkValidity()) {
      return { ok: false, message: 'Adresse e‑mail invalide', field: email };
    }

    // password presence
    if (pwd1 && !pwd1.checkValidity()) {
      return { ok: false, message: 'Veuillez saisir votre mot de passe', field: pwd1 };
    }

    // if register page (pwd2 exists), check match
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

  function initAuthForm(form) {
    if (!form) return;

    let submitting = false;

    // init toggles if any
    initToggles(form);

    // clear errors on input
    const inputs = Array.from(form.querySelectorAll('input'));
    inputs.forEach((input) => {
      input.addEventListener('input', () => hideError(form));
    });

    // focus first input for accessibility
    window.requestAnimationFrame(() => {
      const first = form.querySelector('input:not([type="hidden"])');
      if (first) first.focus();
    });

    // submit handler
    form.addEventListener('submit', (ev) => {
      if (submitting) {
        ev.preventDefault();
        return;
      }

      const result = basicValidation(form);
      if (!result.ok) {
        ev.preventDefault();
        showError(form, result.message, result.field);
        return;
      }

      // good to go -> prevent double submit and allow browser to post
      submitting = true;
      const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.setAttribute('aria-disabled', 'true');
      }
      // allow form submit to proceed
    });

    // keyboard accessibility: allow Enter on .toggle-pw if focused (buttons already keyboard accessible)
    // nothing extra required here

    // support server-side error injection: ensure .form-error is visible if it already has content
    const errorEl = form.querySelector('.form-error');
    if (errorEl && errorEl.textContent.trim() !== '') {
      errorEl.classList.add('is-visible');
      errorEl.setAttribute('aria-hidden', 'false');
    }
  }

  function initAllAuthForms() {
    const forms = Array.from(document.querySelectorAll('.auth-form'));
    if (forms.length === 0) return;
    forms.forEach(initAuthForm);
  }

  // Public init for DOMContentLoaded
  function init() {
    // Respect user preference for reduced motion — avoid doing JS transitions that override CSS
    if (prefersReducedMotion) {
      // still wire toggles and validation but avoid visual FX (no-op here)
    }
    initAllAuthForms();
    // debug
    // console.log('[PureStream Fusion] auth.js initialized');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();