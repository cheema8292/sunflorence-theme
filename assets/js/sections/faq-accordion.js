/* FAQ accordion (from Shopify rr-home-faq). data-single="1" = one answer open at a time. */
(function () {
  document.querySelectorAll('.rr-hfaq').forEach(function (root) {
    var single = root.getAttribute('data-single') === '1';
    var items = root.querySelectorAll('.rr-hfaq__item');
    items.forEach(function (item) {
      var btn = item.querySelector('.rr-hfaq__q');
      btn.addEventListener('click', function () {
        var open = item.classList.contains('is-open');
        if (single) {
          items.forEach(function (i) { i.classList.remove('is-open'); i.querySelector('.rr-hfaq__q').setAttribute('aria-expanded', 'false'); });
        }
        item.classList.toggle('is-open', !open);
        btn.setAttribute('aria-expanded', open ? 'false' : 'true');
      });
    });
  });
})();
