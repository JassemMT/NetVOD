/**
 * resources/js/notification.js
 * Lightweight notification (toast) controller
 *
 * - Finds server-rendered toast elements (.toast) and wires them:
 *   • auto-hide using [data-duration] (ms). 0 = sticky.
 *   • close button handling (.toast-close)
 *   • pause on hover/focus/touch
 *   • keyboard: Escape closes focused toast
 * - Creates a small container if none exists so multiple toasts stack nicely.
 * - Exposes a small API on window.NetVODNotification to create toasts client-side.
 *
 * Usage:
 *  - Include after server-side rendered HTML, or bundle with defer.
 *  - Server-rendered toasts: Notification::render() or Notification::renderAll() should output elements
 *    with class "toast" and data-duration attribute.
 *  - To create a toast from JS:
 *      window.NetVODNotification.show({ title: 'Titre', message: 'Texte', type: 'success', duration: 4000 });
 */
(function () {
  'use strict';

  // Simple utilities
  const selectAll = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const create = (tag, props = {}) => {
    const el = document.createElement(tag);
    Object.keys(props).forEach((k) => {
      if (k === 'text') el.textContent = props[k];
      else if (k === 'html') el.innerHTML = props[k];
      else el.setAttribute(k, props[k]);
    });
    return el;
  };

  const CONTAINER_ID = 'nv-notification-container';

  function ensureContainer() {
    let container = document.getElementById(CONTAINER_ID);
    if (!container) {
      container = create('div', { id: CONTAINER_ID });
      container.className = 'nv-notification-container';
      document.body.appendChild(container);
    }
    return container;
  }

  function removeToast(toast) {
    if (!toast) return;
    if (toast._hideTimeout) {
      clearTimeout(toast._hideTimeout);
      toast._hideTimeout = null;
    }
    toast.classList.add('nv-toast-hide');
    // remove from DOM after animation
    const t = setTimeout(() => {
      if (toast.parentNode) toast.parentNode.removeChild(toast);
      clearTimeout(t);
    }, 320);
  }

  function showToastElement(toast) {
    // append to container if not already in DOM or not inside container
    const container = ensureContainer();
    if (toast.parentNode !== container) container.appendChild(toast);

    // ensure accessible attributes
    toast.setAttribute('role', toast.getAttribute('role') || 'status');
    toast.setAttribute('aria-live', toast.getAttribute('aria-live') || 'polite');
    toast.setAttribute('aria-atomic', 'true');
    toast.tabIndex = toast.tabIndex || -1;

    // prepare close button
    let close = toast.querySelector('.toast-close, .nv-toast-close');
    if (!close) {
      close = create('button', { type: 'button' });
      close.className = 'nv-toast-close';
      close.innerHTML = '✕';
      close.setAttribute('aria-label', 'Fermer la notification');
      toast.appendChild(close);
    }

    close.addEventListener('click', () => removeToast(toast));

    // keyboard: Escape closes when toast is focused
    toast.addEventListener('keydown', (ev) => {
      if (ev.key === 'Escape') {
        removeToast(toast);
      }
    });

    // Pause auto-hide on hover/focus/touch
    const pause = () => {
      if (toast._hideTimeout) {
        clearTimeout(toast._hideTimeout);
        toast._hideTimeout = null;
      }
    };
    const resume = (duration) => {
      if (!duration || duration <= 0) return;
      // safety: if already scheduled, clear first
      if (toast._hideTimeout) clearTimeout(toast._hideTimeout);
      toast._hideTimeout = setTimeout(() => removeToast(toast), duration);
    };

    toast.addEventListener('mouseenter', pause);
    toast.addEventListener('mouseleave', () => {
      const d = parseInt(toast.dataset.duration || '5000', 10);
      resume(d);
    });
    toast.addEventListener('focusin', pause);
    toast.addEventListener('focusout', () => {
      const d = parseInt(toast.dataset.duration || '5000', 10);
      resume(d);
    });

    // touch: pause then resume a bit after end
    toast.addEventListener('touchstart', pause, { passive: true });
    toast.addEventListener('touchend', () => {
      const d = parseInt(toast.dataset.duration || '5000', 10);
      // small delay so users have time to interact
      setTimeout(() => resume(d), 300);
    }, { passive: true });

    // show (allow CSS animation)
    // force reflow then remove hide class
    toast.classList.remove('nv-toast-hide');
    // Auto-hide if data-duration > 0
    const duration = parseInt(toast.dataset.duration || '5000', 10);
    if (duration > 0) {
      // schedule close
      toast._hideTimeout = setTimeout(() => removeToast(toast), duration);
    }
  }

  function normalizeType(type) {
    const allowed = ['info', 'success', 'warning', 'error'];
    return allowed.includes(type) ? type : 'info';
  }

  // Create a toast element from data (title, message, type, duration)
  function createToastNode({ title = '', message = '', type = 'info', duration = 5000 } = {}) {
    const toast = create('div');
    toast.className = `nv-toast nv-toast-${normalizeType(type)}`;
    toast.dataset.duration = String(Math.max(0, parseInt(duration || 0, 10)));

    // icon
    const icons = {
      success: '✓',
      error: '✕',
      warning: '⚠',
      info: 'ℹ',
    };
    const icon = create('div', { 'aria-hidden': 'true' });
    icon.className = 'nv-toast-icon';
    icon.textContent = icons[normalizeType(type)] || 'ℹ';

    const content = create('div');
    content.className = 'nv-toast-content';

    if (title) {
      const h = create('h3', { text: title });
      h.className = 'nv-toast-title';
      content.appendChild(h);
    }
    if (message) {
      const p = create('p', { text: message });
      p.className = 'nv-toast-message';
      content.appendChild(p);
    }

    const close = create('button', { type: 'button' });
    close.className = 'nv-toast-close';
    close.setAttribute('aria-label', 'Fermer la notification');
    close.innerHTML = '✕';

    toast.appendChild(icon);
    toast.appendChild(content);
    toast.appendChild(close);

    return toast;
  }

  // Initialize server-rendered toasts (elements with class 'toast' or 'nv-toast')
  function initServerToasts() {
    // Accept both legacy .toast and our .nv-toast variants
    const nodes = selectAll('.toast, .nv-toast');
    if (!nodes.length) return;
    nodes.forEach((node) => {
      // normalize class names: keep node but add nv- prefix for consistency
      if (!node.classList.contains('nv-toast')) node.classList.add('nv-toast');
      // if there is no container, move node under container
      showToastElement(node);
    });
  }

  // Public API to create a toast programmatically
  function show({ title = '', message = '', type = 'info', duration = 5000 } = {}) {
    const node = createToastNode({ title, message, type, duration });
    showToastElement(node);
    return node;
  }

  // Expose API
  const api = {
    show,
    remove: (node) => removeToast(node),
    ensureContainer,
  };

  // Init on DOM ready
  function init() {
    try {
      initServerToasts();
    } catch (err) {
      // do not break host app
      console.error('notification.init error', err);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // attach to global namespace
  if (typeof window !== 'undefined') {
    window.NetVODNotification = window.NetVODNotification || {};
    window.NetVODNotification.show = api.show;
    window.NetVODNotification.remove = api.remove;
    window.NetVODNotification.ensureContainer = api.ensureContainer;
  }
})();