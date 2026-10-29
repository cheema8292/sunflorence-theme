/* Header (from Shopify rr-header):
   - transparent over the homepage hero, solid after scrolling / on other pages
   - spacer below the fixed header on pages without a hero
   - left drawer with accordion sub-menus (hover opens on desktop, tap on mobile) */
(function () {
  var hf = document.getElementById('rr-hf');
  var nav = document.getElementById('rr-nav');
  var spacer = document.getElementById('rr-spacer');
  var ham = document.getElementById('rr-ham');
  var drawer = document.getElementById('rr-drawer');
  if (!nav) return;

  var hasHero = !!document.querySelector('.rr-hero');

  function updateSpacer() {
    if (hf) document.documentElement.style.setProperty('--rr-header-h', hf.offsetHeight + 'px');
    if (spacer) spacer.style.height = hasHero ? '0px' : (hf ? hf.offsetHeight + 'px' : '0px');
  }
  function onScroll() { nav.classList.toggle('scrolled', window.scrollY > 10); }

  if (hasHero) { onScroll(); window.addEventListener('scroll', onScroll, { passive: true }); }
  else { nav.classList.add('scrolled'); }
  updateSpacer();
  window.addEventListener('resize', updateSpacer);

  /* Drawer */
  function openDrawer() { if (!drawer) return; drawer.classList.add('open'); ham.setAttribute('aria-expanded', 'true'); document.body.style.overflow = 'hidden'; }
  function closeDrawer() { if (!drawer) return; drawer.classList.remove('open'); ham.setAttribute('aria-expanded', 'false'); document.body.style.overflow = ''; }
  if (ham) ham.addEventListener('click', openDrawer);
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDrawer(); });
  if (!drawer) return;
  drawer.addEventListener('click', function (e) { if (e.target.closest('[data-drawer-close]')) closeDrawer(); });

  /* Accordion: one open per level; the parent name opens its sub-menu ("View all" link inside goes to the page) */
  function setOpen(item, open) {
    if (open) {
      Array.prototype.forEach.call(item.parentNode.children, function (sib) {
        if (sib !== item && sib.classList.contains('is-open')) setOpen(sib, false);
      });
    }
    item.classList.toggle('is-open', open);
    var b = item.querySelector(':scope > .rr-dl__row > .rr-dl__toggle');
    if (b) b.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  drawer.addEventListener('click', function (e) {
    var trigger = e.target.closest('.rr-dl__toggle, a[data-rr-parent]');
    if (!trigger) return;
    e.preventDefault();
    var item = trigger.closest('.rr-dl__item');
    setOpen(item, !item.classList.contains('is-open'));
  });
  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    drawer.querySelectorAll('.rr-dl__item').forEach(function (item) {
      var t;
      item.addEventListener('mouseenter', function () { clearTimeout(t); t = setTimeout(function () { if (!item.classList.contains('is-open')) setOpen(item, true); }, 140); });
      item.addEventListener('mouseleave', function () { clearTimeout(t); });
    });
  }
})();
