/* Cart drawer: opens from the header cart icon and after "add to cart"; +/−/remove update the cart via AJAX.
   Contents come from WooCommerce cart fragments (.sf-cd__content), so they stay in sync on every page. */
(function ($) {
  var drawer = document.getElementById('sf-cart-drawer');
  if (!drawer || !$) return;
  var panel = drawer.querySelector('.sf-cd__panel');
  var lastFocus = null;

  function open() {
    lastFocus = document.activeElement;
    drawer.hidden = false;
    requestAnimationFrame(function () { drawer.classList.add('is-open'); });
    document.body.style.overflow = 'hidden';
    panel.focus();
  }
  function close() {
    drawer.classList.remove('is-open');
    document.body.style.overflow = '';
    setTimeout(function () { drawer.hidden = true; }, 300);
    if (lastFocus && lastFocus.focus) lastFocus.focus();
  }

  document.addEventListener('click', function (e) {
    var cartLink = e.target.closest('.rr-nav__cart');
    if (cartLink) { e.preventDefault(); open(); return; }
    if (e.target.closest('[data-sf-cart-close]')) close();
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && drawer.classList.contains('is-open')) close(); });

  // product cards ("+" / "Add to bag") and the product page
  $(document.body).on('added_to_cart sf:open-cart', open);

  // quantity / remove
  function update(item, qty) {
    item.classList.add('is-updating');
    $.post(sfCart.ajaxUrl, { action: 'sf_cart_qty', nonce: sfCart.nonce, key: item.getAttribute('data-key'), qty: qty })
      .done(function (res) {
        if (!res || !res.fragments) { $(document.body).trigger('wc_fragment_refresh'); return; }
        $.each(res.fragments, function (sel, html) { $(sel).replaceWith(html); });
        // lets wc-cart-fragments store the new fragments (sessionStorage) for the next page
        $(document.body).trigger('removed_from_cart', [res.fragments, res.cart_hash]);
      })
      .fail(function () { item.classList.remove('is-updating'); $(document.body).trigger('wc_fragment_refresh'); });
  }
  drawer.addEventListener('click', function (e) {
    var item = e.target.closest('.sf-cd__item');
    if (!item || item.classList.contains('is-updating')) return;
    var cur = parseInt(item.querySelector('.sf-cd__qty span').textContent, 10) || 1;
    var step = e.target.closest('[data-sf-qty]');
    if (step) update(item, Math.max(0, cur + Number(step.getAttribute('data-sf-qty'))));
    else if (e.target.closest('[data-sf-remove]')) update(item, 0);
  });
})(window.jQuery);
