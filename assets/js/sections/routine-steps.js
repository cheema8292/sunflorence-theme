/* Routine steps: auto-rotate, click a step or card, pause on hover (from Shopify rr-routine-steps). */
(function () {
  document.querySelectorAll('.rr-rs').forEach(function (root) {
    var cards = root.querySelectorAll('.rr-rs__card');
    var steps = root.querySelectorAll('.rr-rs__step');
    if (!cards.length) return;
    var current = 0, total = cards.length, timer = null, resumeTimer = null;
    var delay = (parseInt(root.getAttribute('data-speed'), 10) || 3) * 1000;

    function activate(idx) {
      current = (idx + total) % total;
      cards.forEach(function (c, i) { c.classList.toggle('is-active', i === current); });
      steps.forEach(function (s, i) { s.classList.toggle('is-active', i === current); });
    }
    function startAuto() { if (timer || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return; timer = setInterval(function () { activate(current + 1); }, delay); }
    function stopAuto() { clearInterval(timer); timer = null; clearTimeout(resumeTimer); resumeTimer = null; }
    function resumeLater() { clearTimeout(resumeTimer); resumeTimer = setTimeout(startAuto, 6000); }

    steps.forEach(function (step, i) { step.addEventListener('click', function () { stopAuto(); activate(i); resumeLater(); }); });
    cards.forEach(function (card, i) { card.addEventListener('click', function () { stopAuto(); activate(i); resumeLater(); }); });
    root.addEventListener('mouseenter', stopAuto);
    root.addEventListener('mouseleave', startAuto);
    startAuto();
  });
})();
