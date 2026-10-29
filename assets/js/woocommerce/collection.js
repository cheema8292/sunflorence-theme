/* Category pages (Shopify rr-collection): sort dropdown, filter accordion, mobile filter drawer. */
(function () {
  var sortBtn = document.getElementById('rr-sort-btn');
  var sortDrop = document.getElementById('rr-sort-dropdown');
  if (sortBtn && sortDrop) {
    function setOpen(open) {
      sortDrop.classList.toggle('open', open);
      sortBtn.classList.toggle('open', open);
      sortBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    sortBtn.addEventListener('click', function (e) { e.stopPropagation(); setOpen(!sortDrop.classList.contains('open')); });
    document.addEventListener('click', function () { setOpen(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
    sortDrop.addEventListener('click', function (e) { e.stopPropagation(); });
  }

  document.querySelectorAll('.rr-col__filter-heading').forEach(function (h) {
    h.addEventListener('click', function () { h.closest('.rr-col__filter-section').classList.toggle('is-open'); });
  });

  var toggle = document.getElementById('rr-filter-toggle');
  var overlay = document.getElementById('rr-col-drawer-overlay');
  var drawer = document.getElementById('rr-col-drawer');
  var close = document.getElementById('rr-col-drawer-close');
  if (!drawer) return;
  function openDrawer() { drawer.classList.add('open'); overlay.classList.add('open'); document.body.style.overflow = 'hidden'; }
  function closeDrawer() { drawer.classList.remove('open'); overlay.classList.remove('open'); document.body.style.overflow = ''; }
  if (toggle) toggle.addEventListener('click', openDrawer);
  if (overlay) overlay.addEventListener('click', closeDrawer);
  if (close) close.addEventListener('click', closeDrawer);
})();
