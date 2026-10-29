/* Category showcase: tabs, slider arrows, featured slider (from Shopify rr-category-showcase). */

(function () {
  if (window.__rrCsInit) return;
  window.__rrCsInit = true;

  document.addEventListener('click', function (e) {
    /* Tabs */
    var tab = e.target.closest('.rr-cs__tab');
    if (tab) {
      var root = tab.closest('.rr-cs');
      root.querySelectorAll('.rr-cs__tab').forEach(function (t) { t.classList.remove('is-active'); t.setAttribute('aria-selected', 'false'); });
      root.querySelectorAll('.rr-cs__panel').forEach(function (p) { p.classList.remove('is-active'); });
      tab.classList.add('is-active'); tab.setAttribute('aria-selected', 'true');
      var panel = root.querySelector('[id="' + tab.getAttribute('aria-controls') + '"]');
      if (panel) panel.classList.add('is-active');
      var va = root.querySelector('[data-cs-view-all]');
      if (va && tab.dataset.viewAll) va.href = tab.dataset.viewAll;
      /* Spotlight: jump the featured slider to the same subcategory */
      var feat = root.querySelector('.rr-cs__feature');
      if (feat && feat._rrGo && panel) {
        var slides = feat.querySelectorAll('.rr-cs__fslide');
        for (var k = 0; k < slides.length; k++) {
          if (slides[k].getAttribute('data-block-id') === panel.getAttribute('data-block-id')) { feat._rrGo(k, true); break; }
        }
      }
      return;
    }

    /* Featured slider controls */
    var fbtn = e.target.closest('[data-feature-dir], [data-feature-go]');
    if (fbtn) {
      var f = fbtn.closest('.rr-cs__feature');
      if (f && f._rrGo) {
        if (fbtn.hasAttribute('data-feature-go')) f._rrGo(Number(fbtn.getAttribute('data-feature-go')), true);
        else f._rrGo(f._rrIndex + Number(fbtn.getAttribute('data-feature-dir')), true);
      }
      return;
    }

    /* Slider arrows */
    var arrow = e.target.closest('.rr-cs__arrow');
    if (arrow) {
      var r = arrow.closest('.rr-cs');
      var track = r.querySelector('.rr-cs__panel.is-active .rr-cs__items');
      if (!track) return;
      var card = track.firstElementChild;
      var step = card ? card.getBoundingClientRect().width + 20 : track.clientWidth * 0.8;
      track.scrollBy({ left: step * Number(arrow.dataset.csDir), behavior: 'smooth' });
      return;
    }

    /* Add to cart is handled by WooCommerce (ajax_add_to_cart) */
  });

  /* Featured slider: pick a random product per subcategory, then auto-rotate */
  function initFeatures() {
    document.querySelectorAll('.rr-cs__feature:not([data-ready])').forEach(function (f) {
      f.setAttribute('data-ready', '');
      var slides = Array.prototype.slice.call(f.querySelectorAll('.rr-cs__fslide'));
      slides.forEach(function (sl) {
        var cands = sl.querySelectorAll('.rr-cs__fcand');
        if (cands.length < 2) return;
        var pick = Math.floor(Math.random() * cands.length);
        for (var c = 0; c < cands.length; c++) cands[c].hidden = (c !== pick);
      });
      var dots = f.querySelectorAll('.rr-cs__dot');
      var speed = Math.max(2, Number(f.getAttribute('data-autoplay')) || 4) * 1000;
      f.style.setProperty('--cs-autoplay', speed + 'ms');
      var timer = null, paused = false;
      f._rrIndex = 0;
      function restartProgress() {
        f.classList.remove('is-running'); void f.offsetWidth;
        if (!paused && slides.length > 1) f.classList.add('is-running');
      }
      function schedule() {
        clearTimeout(timer);
        if (paused || slides.length < 2) return;
        timer = setTimeout(function () { f._rrGo(f._rrIndex + 1); }, speed);
      }
      f._rrGo = function (i, user) {
        if (!slides.length) return;
        i = (i + slides.length) % slides.length;
        slides[f._rrIndex].classList.remove('is-active');
        if (dots[f._rrIndex]) dots[f._rrIndex].classList.remove('is-active');
        f._rrIndex = i;
        slides[i].classList.add('is-active');
        if (dots[i]) dots[i].classList.add('is-active');
        restartProgress(); schedule();
      };
      f.addEventListener('mouseenter', function () { paused = true; clearTimeout(timer); f.classList.remove('is-running'); });
      f.addEventListener('mouseleave', function () { paused = false; restartProgress(); schedule(); });
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) paused = true;
      restartProgress(); schedule();
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initFeatures); else initFeatures();
  

})();
