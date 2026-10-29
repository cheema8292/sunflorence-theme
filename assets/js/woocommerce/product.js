/* Product page: gallery, variant picker ↔ WooCommerce variations, price/SAVE badge, AJAX add to cart,
   info tabs, reviews carousel. Markup = Shopify shop-product-details (see template-parts/product/*). */
(function () {
  var P = window.sfProduct || {};

  /* ── money (WooCommerce price format) ── */
  function money(n) {
    var d = P.decimals == null ? 2 : P.decimals;
    var parts = Number(n || 0).toFixed(d).split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, P.thousand || ',');
    var num = parts.join(P.decimal || '.');
    return (P.format || '%1$s%2$s').replace('%1$s', P.symbol || '$').replace('%2$s', num);
  }

  /* ── gallery: thumbnails swap the main image ── */
  var gallery = document.querySelector('[data-sf-gallery]');
  function showImage(index) {
    if (!gallery) return;
    var wraps = gallery.querySelectorAll('.shop-image-zoom-wrapper');
    var thumbs = gallery.querySelectorAll('.shop-thumbnail');
    if (!wraps[index]) return;
    wraps.forEach(function (w, i) {
      var on = i === index;
      w.classList.toggle('active', on);
      var img = w.querySelector('.shop-main-image');
      if (img) img.classList.toggle('active', on);
    });
    thumbs.forEach(function (t, i) { t.classList.toggle('active', i === index); });
    var t = thumbs[index];
    if (t && t.parentNode) {
      var box = t.parentNode;
      var left = t.offsetLeft - (box.clientWidth - t.offsetWidth) / 2;
      box.scrollTo({ left: Math.max(0, left), behavior: 'smooth' });
    }
    gallery.setAttribute('data-index', index);
  }
  function showMedia(id) {
    if (!gallery || !id) return;
    var wraps = gallery.querySelectorAll('.shop-image-zoom-wrapper');
    for (var i = 0; i < wraps.length; i++) {
      if (wraps[i].getAttribute('data-media-id') === String(id)) { showImage(i); return; }
    }
  }
  if (gallery) {
    gallery.querySelectorAll('.shop-thumbnail').forEach(function (t) {
      function go() { showImage(Number(t.getAttribute('data-index'))); }
      t.addEventListener('click', go);
      t.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); go(); } });
    });
    // swipe on the main image (mobile)
    var main = gallery.querySelector('.shop-image-container');
    var count = gallery.querySelectorAll('.shop-image-zoom-wrapper').length;
    var sx = 0, sy = 0;
    if (main && count > 1) {
      main.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; sy = e.touches[0].clientY; }, { passive: true });
      main.addEventListener('touchend', function (e) {
        var dx = e.changedTouches[0].clientX - sx, dy = e.changedTouches[0].clientY - sy;
        if (Math.abs(dx) < 40 || Math.abs(dx) < Math.abs(dy)) return;
        var cur = Number(gallery.getAttribute('data-index') || 0);
        showImage((cur + (dx < 0 ? 1 : -1) + count) % count);
      }, { passive: true });
    }
  }

  /* ── price block + button price ── */
  function setPrice(price, regular) {
    var onSale = regular > price && price > 0;
    var cur = document.querySelector('[data-sf-price-current]');
    var cmp = document.querySelector('[data-sf-price-compare]');
    var save = document.querySelector('[data-sf-price-save]');
    var pct = document.querySelector('[data-sf-price-pct]');
    if (cur) { cur.textContent = money(price); cur.classList.toggle('on-sale', onSale); }
    if (cmp) { cmp.textContent = onSale ? money(regular) : ''; cmp.hidden = !onSale; }
    if (save) save.hidden = !onSale;
    if (pct && onSale) pct.textContent = Math.round((regular - price) / regular * 100);
    var bp = document.querySelector('[data-sf-btn-price]');
    var bc = document.querySelector('[data-sf-btn-compare]');
    if (bp) bp.textContent = money(price);
    if (bc) { bc.textContent = onSale ? '(' + money(regular) + ')' : ''; bc.hidden = !onSale; }
  }

  /* ── variant picker (radios) → WooCommerce selects ── */
  var form = document.querySelector('form.variations_form.sf-pform');
  var $ = window.jQuery;
  if (form && $) {
    var $form = $(form);
    var btn = form.querySelector('.shop-add-to-cart-button');
    var label = btn && btn.querySelector('[data-sf-btn-label]');
    var baseText = btn ? btn.getAttribute('data-text') : '';

    form.querySelectorAll('[data-sf-attr]').forEach(function (opt) {
      var select = form.querySelector('select[name="' + opt.getAttribute('data-sf-attr') + '"]');
      var shown = opt.querySelector('[data-sf-selected]');
      opt.querySelectorAll('.simple-variant-picker__radio').forEach(function (r) {
        r.addEventListener('change', function () {
          if (!r.checked) return;
          if (shown) shown.textContent = r.getAttribute('data-name');
          if (select) { select.value = r.value; $(select).trigger('change'); }
        });
      });
    });

    // mark values that are not available with the other current choices
    $form.on('woocommerce_update_variation_values', function () {
      form.querySelectorAll('[data-sf-attr]').forEach(function (opt) {
        var select = form.querySelector('select[name="' + opt.getAttribute('data-sf-attr') + '"]');
        if (!select) return;
        opt.querySelectorAll('.simple-variant-picker__radio').forEach(function (r) {
          var o = select.querySelector('option[value="' + (window.CSS && CSS.escape ? CSS.escape(r.value) : r.value) + '"]');
          var ok = o && !o.disabled && o.classList.contains('enabled');
          r.closest('.simple-variant-picker__value-container').classList.toggle('is-unavailable', !ok);
        });
      });
    });

    $form.on('found_variation', function (e, v) {
      setPrice(Number(v.display_price), Number(v.display_regular_price));
      if (v.image_id) showMedia(v.image_id);
      if (label) label.textContent = v.is_in_stock ? baseText : (P.i18n && P.i18n.soldOut) || 'Sold out';
      if (btn) btn.disabled = !v.is_in_stock || !v.is_purchasable;
    });
    $form.on('reset_data', function () {
      if (label) label.textContent = baseText;
    });
    // copy the preselected buttons into WooCommerce's selects once its script is ready (also for "any" attributes)
    setTimeout(function () {
      form.querySelectorAll('[data-sf-attr]').forEach(function (opt) {
        var select = form.querySelector('select[name="' + opt.getAttribute('data-sf-attr') + '"]');
        var r = opt.querySelector('.simple-variant-picker__radio:checked');
        if (select && r && !select.value) select.value = r.value;
      });
      $form.find('.variations select').first().trigger('change');
    }, 0);
  }

  /* ── add to cart without leaving the page (opens the cart drawer) ── */
  document.querySelectorAll('form.sf-pform').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      var btn = f.querySelector('.shop-add-to-cart-button');
      if (!window.fetch || !window.FormData || !btn) return;
      if (btn.classList.contains('disabled') || btn.classList.contains('wc-variation-selection-needed')) return; // WooCommerce shows its message
      e.preventDefault();
      var data = new FormData(f);
      if (btn.name) data.append(btn.name, btn.value);
      var spinner = btn.querySelector('.loading__spinner');
      var msg = f.querySelector('.sf-atc-msg');
      btn.classList.add('loading'); btn.setAttribute('aria-busy', 'true');
      if (spinner) spinner.classList.remove('hidden');
      if (msg) msg.hidden = true;
      fetch(f.getAttribute('action') || location.href, { method: 'POST', body: data, credentials: 'same-origin' })
        .then(function (r) { return r.text(); })
        .then(function (html) {
          var doc = new DOMParser().parseFromString(html, 'text/html');
          var err = doc.querySelector('.woocommerce-error li, .woocommerce-error, .wc-block-components-notice-banner.is-error');
          if (err) throw new Error(err.textContent.trim());
          if ($) {
            $(document.body).trigger('wc_fragment_refresh');
            $(document.body).one('wc_fragments_refreshed', function () { $(document.body).trigger('sf:open-cart'); });
          } else {
            location.href = P.cartUrl || '/cart/';
          }
        })
        .catch(function (er) {
          if (msg) { msg.textContent = er.message || (P.i18n && P.i18n.error) || 'Could not add to cart.'; msg.hidden = false; }
        })
        .then(function () {
          btn.classList.remove('loading'); btn.removeAttribute('aria-busy');
          if (spinner) spinner.classList.add('hidden');
        });
    });
  });

  /* ── info tabs (Shopify product_faq block: one open at a time) ── */
  document.querySelectorAll('.product-faq .faq-container').forEach(function (box) {
    var single = box.getAttribute('data-accordion') === 'true';
    box.querySelectorAll('.faq-question').forEach(function (q) {
      function toggle() {
        var item = q.closest('.faq-item');
        var open = !item.classList.contains('active');
        if (single) box.querySelectorAll('.faq-item.active').forEach(function (o) {
          o.classList.remove('active'); o.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
        });
        item.classList.toggle('active', open);
        q.setAttribute('aria-expanded', open ? 'true' : 'false');
      }
      q.addEventListener('click', toggle);
      q.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); } });
    });
  });

  /* ── reviews carousel (Shopify customer-reviews-carousel.js: wraps around at both ends) ── */
  var rs = document.querySelector('.reviews-transformation-section');
  var rc = rs && rs.querySelector('.reviews-container');
  if (rc) {
    var step = function () {
      var c = rc.querySelector('.review-card');
      return c ? c.offsetWidth + (parseInt(getComputedStyle(rc).gap, 10) || 20) : 350;
    };
    var prev = rs.querySelector('.nav-button.prev'), next = rs.querySelector('.nav-button.next');
    if (prev) prev.addEventListener('click', function () {
      if (rc.scrollLeft <= 5) rc.scrollTo({ left: rc.scrollWidth, behavior: 'smooth' });
      else rc.scrollBy({ left: -step(), behavior: 'smooth' });
    });
    if (next) next.addEventListener('click', function () {
      if (rc.scrollLeft + rc.clientWidth >= rc.scrollWidth - 5) rc.scrollTo({ left: 0, behavior: 'smooth' });
      else rc.scrollBy({ left: step(), behavior: 'smooth' });
    });
  }
})();
