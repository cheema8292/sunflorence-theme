# Sunflorence — WordPress / WooCommerce theme

Rebuilt from the Sunflorence Shopify theme: same sections, markup, CSS, colours (maroon · brown · bluish navy),
fonts and layouts. Homepage sections are added, removed and reordered in the WordPress admin —
the WordPress version of the Shopify customizer.

## Requirements
- WordPress 6.4+, PHP 8.0+
- **WooCommerce** (shop, cart, checkout)
- **ACF Pro** (homepage sections + Sunflorence settings). Without it the theme still works with the Shopify default texts.

## Install
1. Zip the `sunflorence` folder → **Appearance → Themes → Add New → Upload Theme**, or copy it to `wp-content/themes/`.
2. Activate **Sunflorence**.
3. **Products → Categories**: create the categories with the same slugs as the Shopify collections (skin-care, cleansers, mackups, lips …).
4. **Sunflorence → Import Shopify content** → imports the homepage sections and the About / Contact / FAQ / Track Order / Wishlist / All Categories pages (sets the Home page as front page). Then add the images and videos in each section (Shopify-hosted media can't be copied).
5. **Sunflorence** (admin menu) → logo, announcement bar, footer, contact details. **Sunflorence → Pages** → product page, category pages, search, 404 texts.
6. **Appearance → Menus**:
   - "Sidebar menu (with sub-menus)" → your categories, nested up to 3 levels
   - "Footer — Customer Care" → About, Contact, FAQ, policies
7. Policy pages (Privacy, Refund, Shipping, Terms): normal pages with the default template → Shopify policy layout. Use the slugs `refund-policy` and `shipping-policy` so the product page links to them.

## Shopify → WordPress map
| Shopify | WordPress |
|---|---|
| `sections/rr-*.liquid` (homepage) | `template-parts/sections/*.php` |
| `templates/index.json` (section order) | ACF flexible content "Homepage sections" on the front page |
| Theme settings / header & footer groups | ACF options page **Sunflorence** |
| `templates/page.*.json` (About, Contact …) | `page-templates/template-*.php` (fields on the page) |
| `rr-policy` (default page) | `page.php` |
| `rr-404`, `rr-search`, `rr-list-collections` | `404.php`, `search.php`, `page-templates/template-list-collections.php` |
| `shop-product-details` + `product-info` blocks | `woocommerce/single-product.php` + `template-parts/product/*` |
| `customer-reviews-carousel` | `template-parts/product/reviews.php` (real WooCommerce reviews) |
| `rr-collection` | `woocommerce/archive-product.php` (shop + category pages) |
| Collections | WooCommerce product categories |
| `snippets/rr-cs-card`, `rr-product-card`, makeup card | `template-parts/product/card*.php` |
| Section `<style>` / `{% style %}` | `assets/css/**` (settings → CSS variables, or rendered with the live settings) |
| Section scripts | `assets/js/**` |

## Structure
```
sunflorence/
├── style.css, functions.php, header.php, footer.php
├── front-page.php            homepage = list of sections (ACF flexible content)
├── page.php                  policy layout · 404.php · search.php · index.php
├── inc/
│   ├── setup, helpers, enqueue, nav-walker
│   ├── acf-fields.php        options pages, homepage sections, page fields
│   ├── fields/*.json         field definitions (generated from the Shopify schemas; product.json hand-made)
│   ├── woocommerce.php       cart count, cart drawer, product helpers
│   ├── newsletter.php        footer newsletter → Sunflorence → Subscribers
│   ├── forms.php             contact form (email) + order lookup
│   └── importer.php          Sunflorence → Import Shopify content
├── template-parts/
│   ├── header/  footer/
│   ├── sections/             hero, brand-logos, category-showcase, makeup-edit, featured-products,
│   │                         image-banner, bundles, routine-steps, see-in-action, tested-proven,
│   │                         faq-accordion, rewards
│   └── product/              cards · gallery · info · add-to-cart · atc-button · reviews · announcement
├── woocommerce/              single-product.php, archive-product.php
├── page-templates/           about, contact, faq, track-order, wishlist, list-collections
└── assets/
    ├── css/global/           base (tokens), header, footer, product-card
    ├── css/sections/         one file per homepage section
    ├── css/pages/            one file per page template
    ├── css/woocommerce/      product (+ blocks, reviews, announcement-bar, product-wp), collection, cart-drawer
    ├── js/global/            header, wishlist
    ├── js/sections/          per section
    ├── js/woocommerce/       product, collection, cart-drawer
    ├── data/                 Shopify content for the importer
    └── images/ fonts/
```

CSS/JS are loaded only where needed (`inc/enqueue.php`): the homepage loads only the sections it uses.

## Regenerating from the Shopify theme (`../tools`, needs Node + `npm install`)
| Script | Output |
|---|---|
| `convert-shopify.js` | `inc/fields/*.json`, section/page CSS, `assets/data/*.json` (hand-tuned `makeup-edit.css` and `bundles.css` are kept) |
| `render-product-css.js` | `css/woocommerce/product.css` (product section CSS with the live settings) |
| `render-product-blocks.js` | `css/woocommerce/product-blocks.css` + reference HTML in `tools/out/` |
| `render-section-css.js` | any section's CSS with its live settings (used for `reviews.css`, `announcement-bar.css`) |

## Build status
| Phase | Content | Status |
|---|---|---|
| 1 | Theme skeleton, header + sidebar menu, footer, homepage section system, Hero, Brand logos, Category showcase (5 designs), product card, newsletter | ✅ |
| 2 | Makeup edit, Featured products, Image banner, Bundles, Routine steps, See it in action, Tested & proven, FAQ, Rewards + importer | ✅ |
| 3 | WooCommerce: product page (gallery, price + SAVE %, benefits, variant buttons/swatches, add to cart, info tabs, reviews, related, FAQ), category pages, cart drawer | ✅ |
| 4 | Pages: About, Contact (sends email), FAQ, Track order (real order lookup), Wishlist, All categories, policy, 404, search | ✅ |

## Client changes (on top of the Shopify design)
- Product rows like theskinfit.com: ~4 products in view on desktop (2 on mobile), 8px gaps, flat cards with a discount badge — `assets/css/global/product-density.css`. Shop/category pages: 4 per row.
- **Banner grid** section: 4 category tiles, 2 banners side by side, or 1 full-width banner (images/titles/links come from the category until you upload banners).
- Brand strip: 4 products of those brands underneath (Brands taxonomy, "Brand" attribute, or the brand name in the product name).
- Bigger logo (84px desktop / 62px mobile — Sunflorence → Header).
- Homepage: "Sunflorence Rewards" replaced by **New Arrivals**; the "free samples" FAQ removed (homepage + FAQ page).
- Product page: more spacing between the blocks, sticky gallery on desktop.
- Before the Shopify import (or without ACF) the homepage and pages show the Shopify content from `assets/data/`; Shopify links (`/collections/…`, `/pages/…`) are turned into WordPress links (`sf_url()`).

## Notes
- Nothing invented: reviews are the product's real WooCommerce reviews; the hero's "growing customer count" badge,
  the about page's "97% would recommend" badge and the Shopify sample reviews were not ported.
  The about ribbon shows store promises (secure checkout, tracked shipping, returns, support) instead of product claims —
  change it with the `sf_about_ribbon` filter.
- Product page: variable products keep WooCommerce's variation form (hidden selects); the Shopify buttons/swatches drive it.
  Colour swatches use the term meta `color` (hex, e.g. from a swatches plugin) or the colour name.
- Add to cart (cards and product page) stays on the page and opens the cart drawer; the drawer and header count update via cart fragments.
- Wishlist is saved in the visitor's browser (like Shopify) — "Save to wishlist" appears on product pages once a page with slug `wishlist` exists.
- Newsletter: without a form shortcode, emails are saved under **Sunflorence → Subscribers** (CSV export).
