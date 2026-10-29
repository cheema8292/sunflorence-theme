/* Featured products slider (from Shopify rr-products): arrows, progress bar, autoplay, drag + swipe. */
(function () {
  document.querySelectorAll('.rr-products').forEach(function (sec) {
    var slider = sec.querySelector('.rr-products__slider');
    var prev = sec.querySelector('[data-dir="-1"]');
    var next = sec.querySelector('[data-dir="1"]');
    var pfill = sec.querySelector('.rr-products__progress-fill');
    if (!slider) return;
    var cards = Array.prototype.slice.call(slider.querySelectorAll('.rr-prod-card'));
    var total = cards.length;
    if (!total) return;

    function visibleCount() {
      var w = cards[0].offsetWidth + parseFloat(getComputedStyle(slider).gap || 0);
      return Math.max(1, Math.round(slider.clientWidth / w));
    }
    function getIdx() {
      var sl = slider.getBoundingClientRect(), best = 0, bestD = Infinity;
      cards.forEach(function (c, i) { var d = Math.abs(c.getBoundingClientRect().left - sl.left); if (d < bestD) { bestD = d; best = i; } });
      return best;
    }
    function updateProgress() {
      if (!pfill) return;
      var vis = visibleCount(), maxIdx = Math.max(1, total - vis), idx = getIdx();
      pfill.style.width = Math.min(100, Math.max(((idx + 1) / (maxIdx + 1)) * 100, (vis / total) * 100)) + '%';
    }
    function goTo(idx, instant) {
      if (!cards[idx]) return;
      var target = cards[idx].offsetLeft;
      if (instant) {
        var pb = slider.style.scrollBehavior;
        slider.style.scrollBehavior = 'auto';
        slider.scrollLeft = target;
        requestAnimationFrame(function () { slider.style.scrollBehavior = pb || ''; updateProgress(); });
      } else {
        slider.scrollTo({ left: target, behavior: 'smooth' });
      }
    }
    function nextSlide() { var i = getIdx(), m = Math.max(0, total - visibleCount()); if (i >= m) goTo(0, true); else goTo(i + 1); }
    function prevSlide() { var i = getIdx(); if (i <= 0) goTo(Math.max(0, total - visibleCount()), true); else goTo(i - 1); }

    var autoT = null, resT = null, isDn = false, stX = 0, stSL = 0, moved = false;
    function startAuto() { if (autoT || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return; autoT = setInterval(nextSlide, 4000); }
    function stopAuto() { clearInterval(autoT); autoT = null; clearTimeout(resT); resT = null; }
    function resumeLater() { clearTimeout(resT); resT = setTimeout(startAuto, 8000); }

    if (prev) prev.addEventListener('click', function () { stopAuto(); prevSlide(); resumeLater(); });
    if (next) next.addEventListener('click', function () { stopAuto(); nextSlide(); resumeLater(); });
    slider.addEventListener('scroll', function () { requestAnimationFrame(updateProgress); }, { passive: true });
    slider.addEventListener('mouseenter', stopAuto);
    slider.addEventListener('mouseleave', function () { if (!isDn) startAuto(); });

    slider.addEventListener('mousedown', function (e) { isDn = true; moved = false; stopAuto(); slider.classList.add('is-dragging'); stX = e.pageX; stSL = slider.scrollLeft; });
    document.addEventListener('mouseup', function () { if (!isDn) return; isDn = false; slider.classList.remove('is-dragging'); resumeLater(); });
    document.addEventListener('mousemove', function (e) { if (!isDn) return; e.preventDefault(); if (Math.abs(e.pageX - stX) > 6) moved = true; slider.scrollLeft = stSL - (e.pageX - stX); });
    // a drag should not open the product
    slider.addEventListener('click', function (e) { if (moved) { e.preventDefault(); moved = false; } }, true);

    var tx = 0, tsl = 0;
    slider.addEventListener('touchstart', function (e) { stopAuto(); tx = e.touches[0].clientX; tsl = slider.scrollLeft; }, { passive: true });
    slider.addEventListener('touchmove', function (e) { slider.scrollLeft = tsl + (tx - e.touches[0].clientX); }, { passive: true });
    slider.addEventListener('touchend', resumeLater, { passive: true });

    updateProgress();
    startAuto();
  });
})();
