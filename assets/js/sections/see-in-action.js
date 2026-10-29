/* See it in action: play video in its card, arrows, drag + swipe (from Shopify rr-see-in-action). */
(function () {
  document.querySelectorAll('.rr-sia').forEach(function (sec) {
    var slider = sec.querySelector('.rr-sia__slider');
    if (!slider) return;
    var cards = Array.prototype.slice.call(slider.querySelectorAll('.rr-sia__card'));
    if (!cards.length) return;

    function stopAll() {
      cards.forEach(function (c) {
        var v = c.querySelector('.rr-sia__video');
        if (v) { if (v.pause) v.pause(); v.remove(); }
        c.classList.remove('is-playing');
      });
    }
    function playIn(card) {
      var src = card.getAttribute('data-video-src');
      var url = card.getAttribute('data-video-url');
      if (src) {
        var v = document.createElement('video');
        v.className = 'rr-sia__video'; v.src = src; v.controls = true; v.autoplay = true; v.playsInline = true; v.loop = true;
        card.appendChild(v); card.classList.add('is-playing');
      } else if (url) {
        var f = document.createElement('iframe');
        f.className = 'rr-sia__video';
        f.src = url + (url.indexOf('?') > -1 ? '&' : '?') + 'autoplay=1&playsinline=1';
        f.allow = 'autoplay; encrypted-media; picture-in-picture'; f.allowFullscreen = true; f.frameBorder = '0';
        card.appendChild(f); card.classList.add('is-playing');
      }
    }
    var moved = false;
    cards.forEach(function (card) {
      card.addEventListener('click', function (e) {
        if (moved) { moved = false; return; }
        if (e.target.closest('.rr-sia__video')) return;
        e.preventDefault();
        var playing = card.classList.contains('is-playing');
        stopAll();
        cards.forEach(function (c) { c.classList.remove('is-active'); });
        card.classList.add('is-active');
        if (!playing) playIn(card);
      });
    });

    sec.querySelectorAll('.rr-sia__nav-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        slider.scrollBy({ left: (cards[0].offsetWidth + 16) * Number(btn.getAttribute('data-dir')), behavior: 'smooth' });
      });
    });

    var isDn = false, stX = 0, stSL = 0;
    slider.addEventListener('mousedown', function (e) {
      if (e.target.closest('.rr-sia__video')) return;
      isDn = true; moved = false; slider.classList.add('is-dragging'); stX = e.pageX; stSL = slider.scrollLeft;
    });
    document.addEventListener('mouseup', function () { if (!isDn) return; isDn = false; slider.classList.remove('is-dragging'); });
    document.addEventListener('mousemove', function (e) {
      if (!isDn) return; e.preventDefault();
      if (Math.abs(e.pageX - stX) > 6) moved = true;
      slider.scrollLeft = stSL - (e.pageX - stX);
    });
    var tx = 0, tsl = 0;
    slider.addEventListener('touchstart', function (e) { tx = e.touches[0].clientX; tsl = slider.scrollLeft; }, { passive: true });
    slider.addEventListener('touchmove', function (e) { slider.scrollLeft = tsl + (tx - e.touches[0].clientX); }, { passive: true });
  });
})();
