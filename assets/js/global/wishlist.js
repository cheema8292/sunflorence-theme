/* Wishlist (Shopify rr-wishlist): products saved in localStorage.
   - [data-sf-wishlist] buttons (product page) toggle a product: data-product = JSON {id,title,url,image,price,compare,type}
   - #rr-wl-grid (Wishlist page) lists the saved products */
(function () {
  var KEY = 'sf_wishlist';
  function get() { try { var l = JSON.parse(localStorage.getItem(KEY) || '[]'); return Array.isArray(l) ? l : []; } catch (e) { return []; } }
  function save(l) { try { localStorage.setItem(KEY, JSON.stringify(l)); } catch (e) {} }
  function has(id) { return get().some(function (p) { return String(p.id) === String(id); }); }

  /* product page buttons */
  function syncButtons() {
    document.querySelectorAll('[data-sf-wishlist]').forEach(function (b) {
      var p; try { p = JSON.parse(b.getAttribute('data-product')); } catch (e) { return; }
      var on = has(p.id);
      b.classList.toggle('is-active', on);
      b.setAttribute('aria-pressed', on ? 'true' : 'false');
      var t = b.querySelector('[data-sf-wl-text]');
      if (t) t.textContent = on ? b.getAttribute('data-on') : b.getAttribute('data-off');
    });
  }
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-sf-wishlist]');
    if (!b) return;
    e.preventDefault();
    var p; try { p = JSON.parse(b.getAttribute('data-product')); } catch (er) { return; }
    var l = get();
    var i = l.findIndex(function (x) { return String(x.id) === String(p.id); });
    if (i === -1) l.push(p); else l.splice(i, 1);
    save(l);
    syncButtons(); render();
  });

  /* wishlist page */
  function el(tag, cls, text) { var n = document.createElement(tag); if (cls) n.className = cls; if (text != null) n.textContent = text; return n; }
  function safeUrl(u) { return /^(https?:)?\/\//i.test(u || '') || /^\//.test(u || '') ? u : '#'; }
  function render() {
    var grid = document.getElementById('rr-wl-grid');
    if (!grid) return;
    var empty = document.getElementById('rr-wl-empty');
    var badge = document.getElementById('rr-wl-count-badge');
    var l = get();
    if (badge) badge.textContent = l.length + ' ' + (l.length === 1 ? badge.getAttribute('data-one') : badge.getAttribute('data-many'));
    grid.textContent = '';
    if (empty) empty.style.display = l.length ? 'none' : 'block';
    l.forEach(function (p) {
      var card = el('div', 'rr-wishlist__card');
      var imgBox = el('div', 'rr-wishlist__card-img');
      if (p.image) { var img = el('img'); img.src = safeUrl(p.image); img.alt = p.title || ''; img.loading = 'lazy'; imgBox.appendChild(img); }
      var rm = el('button', 'rr-wishlist__remove', '✕');
      rm.type = 'button'; rm.setAttribute('aria-label', grid.getAttribute('data-remove') + ' ' + (p.title || ''));
      rm.addEventListener('click', function () { save(get().filter(function (x) { return String(x.id) !== String(p.id); })); render(); syncButtons(); });
      imgBox.appendChild(rm);
      var info = el('div', 'rr-wishlist__card-info');
      var name = el('a', 'rr-wishlist__card-name', p.title || ''); name.href = safeUrl(p.url);
      info.appendChild(name);
      if (p.type) info.appendChild(el('div', 'rr-wishlist__card-type', p.type));
      var bottom = el('div', 'rr-wishlist__card-bottom');
      var price = el('div', 'rr-wishlist__card-price', p.price || '');
      if (p.compare) price.appendChild(el('del', null, p.compare));
      var view = el('a', 'rr-wishlist__add-btn', grid.getAttribute('data-view')); view.href = safeUrl(p.url);
      bottom.appendChild(price); bottom.appendChild(view);
      info.appendChild(bottom);
      card.appendChild(imgBox); card.appendChild(info);
      grid.appendChild(card);
    });
  }

  syncButtons(); render();
  window.addEventListener('storage', function (e) { if (e.key === KEY) { syncButtons(); render(); } });
})();
