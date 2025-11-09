/* ═══════════════════════════════════════════════════════════════════════════ */
/* PURESTREAM FUSION — PAGE CATALOGUE */
/* À charger UNIQUEMENT sur la page catalogue */
/* ═══════════════════════════════════════════════════════════════════════════ */

(function() {
  'use strict';

  console.log('[PureStream Fusion] Catalogue page.js loaded');

  /* ─────────────────────────────────────────────────────────────────────── */
  /* FILTRES / RECHERCHE (exemple) */
  /* ─────────────────────────────────────────────────────────────────────── */

  function initFilters() {
    const filterInputs = document.querySelectorAll('[data-filter]');
    
    if (filterInputs.length === 0) {
      console.log('[PureStream Fusion] No filters found');
      return;
    }

    filterInputs.forEach((input) => {
      input.addEventListener('change', (e) => {
        console.log('[PureStream Fusion] Filter changed:', e.target.value);
        // Logique de filtrage
      });
    });
  }

  /* ─────────────────────────────────────────────────────────────────────── */
  /* PAGINATION (exemple) */
  /* ─────────────────────────────────────────────────────────────────────── */

  function initPagination() {
    const paginationBtns = document.querySelectorAll('[data-pagination]');
    
    if (paginationBtns.length === 0) {
      console.log('[PureStream Fusion] No pagination found');
      return;
    }

    paginationBtns.forEach((btn) => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        console.log('[PureStream Fusion] Pagination:', btn.dataset.pagination);
        // Logique de pagination
      });
    });
  }

  /* ─────────────────────────────────────────────────────────────────────── */
  /* INIT */
  /* ─────────────────────────────────────────────────────────────────────── */

  const init = () => {
    initFilters();
    initPagination();
    console.log('[PureStream Fusion] Catalogue page interactions initialized');
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();