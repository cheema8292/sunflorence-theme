/* Hero slider (from Shopify rr-hero): autoplay, dots, swipe, progress bar. */
(function () {
  document.querySelectorAll('.rr-hero').forEach(function (hero) {
    var slides = Array.prototype.slice.call(hero.querySelectorAll('.rr-hero__slide'));
    var dots = Array.prototype.slice.call(hero.querySelectorAll('.rr-hero__dot'));
    var fill = hero.querySelector('.rr-hero__progress-fill');
    var duration = (parseInt(hero.getAttribute('data-speed'), 10) || 5) * 1000;
    var total = slides.length;
    if (total <= 1) return;

    var cur = 0, timer = null, progTimer = null, progStart = 0;

    function startProgress() {
      clearInterval(progTimer);
      progStart = Date.now();
      if (fill) fill.style.width = '0%';
      progTimer = setInterval(function () {
        var pct = Math.min(((Date.now() - progStart) / duration) * 100, 100);
        if (fill) fill.style.width = pct + '%';
      }, 50);
    }
    function goTo(n) {
      slides[cur].classList.remove('active');
      if (dots[cur]) dots[cur].classList.remove('active');
      cur = (n + total) % total;
      slides[cur].classList.add('active');
      if (dots[cur]) dots[cur].classList.add('active');
      startProgress();
    }
    function startAuto() { clearInterval(timer); timer = setInterval(function () { goTo(cur + 1); }, duration); }

    dots.forEach(function (dot, i) {
      dot.addEventListener('click', function () { clearInterval(timer); goTo(i); startAuto(); });
    });

    var tx = 0;
    hero.addEventListener('touchstart', function (e) { tx = e.touches[0].clientX; }, { passive: true });
    hero.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].clientX - tx;
      if (Math.abs(dx) > 40) { clearInterval(timer); goTo(cur + (dx < 0 ? 1 : -1)); startAuto(); }
    }, { passive: true });

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    startProgress();
    startAuto();
  });
})();
