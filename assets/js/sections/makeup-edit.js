/* Makeup edit: category tabs (from Shopify rr-makeup-edit). Add to bag uses WooCommerce ajax add-to-cart. */
(function () {
  document.querySelectorAll('.rr-mk').forEach(function (root) {
    var viewAll = root.querySelector('[data-mk-view-all]');
    root.querySelectorAll('.rr-mk__tab').forEach(function (tab) {
      tab.addEventListener('click', function () {
        root.querySelectorAll('.rr-mk__tab').forEach(function (t) { t.classList.remove('is-active'); t.setAttribute('aria-selected', 'false'); });
        root.querySelectorAll('.rr-mk__panel').forEach(function (p) { p.classList.remove('is-active'); });
        tab.classList.add('is-active'); tab.setAttribute('aria-selected', 'true');
        var panel = root.querySelector('[id="' + tab.getAttribute('aria-controls') + '"]');
        if (panel) panel.classList.add('is-active');
        if (viewAll && tab.dataset.viewAll) viewAll.href = tab.dataset.viewAll;
      });
    });
  });
})();
