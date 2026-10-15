# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

WordPress + WooCommerce store ("Intalshop", solar energy / renewable equipment shop) running under [Laravel Herd](https://herd.laravel.com), served locally at `intalshop.test`. Git-tracked, no composer.json/package.json at the repo root, no wp-cli on PATH.

Custom code lives entirely in two places:

- `wp-content/themes/solaris/` — the site's theme, purpose-built for this store (not a stock WP theme).
- `wp-content/plugins/bangladeshi-payment-gateways/` — a third-party WooCommerce payment-gateway plugin (bKash/Rocket/Nagad/Upay), vendored in with its own `composer.json`/`vendor/`. Treat it as a dependency, not site code — don't restructure it; upstream is https://github.com/ultraDevs/Bangladeshi-Payment-Gateways.

Everything else under `wp-content/` (`akismet`, `woocommerce`, stock `twentytwenty*` themes) is an unmodified third-party plugin/theme.

Untracked items in `wp-content/` — `mu-plugins/`, `plugins/smooth-publisher-ink/`, `.sc_*/`, `cache/.gx_*`, stray `*.zip` files — are **not** site code (they match a known WordPress backdoor pattern). Never edit, extend, execute, or rely on them; flag them to the user if they reappear.

## Environment

- DB: MySQL, database `local_intalshop`, user `root`, host `localhost` (see `wp-config.php`).
- `wp-config.php` has `WP_DEBUG` set to `false` — flip to `true` locally when debugging, don't commit that change.
- No `wp` CLI on PATH. Check whether Herd provides it under a different binary/path, or use `/wp-admin` / direct DB access instead.
- No test suite, linter, or build step is configured anywhere in the repo (no `package.json`, no PHPUnit/PHPCS config). There is nothing to run beyond loading pages in the browser at `intalshop.test`.

## Working in wp-content

- Don't edit WordPress core (`wp-admin/`, `wp-includes/`, root `wp-*.php`) — lost on core updates.
- Don't hand-edit `wp-content/uploads/` (generated media) or the `bangladeshi-payment-gateways` plugin's `vendor/` (composer-managed).
- Site-specific logic (templates, hooks, styling, CPTs) belongs in the `solaris` theme, under `inc/` — not in the plugin, and not inline in template files.

## Solaris theme architecture

`functions.php` is a thin loader: it just `require_once`s each file in `inc/` in a fixed order (see the array at the top of the file). Each `inc/` file owns one concern and is self-contained:

- `inc/theme-setup.php` — theme supports, nav menu locations, image sizes, footer widget areas.
- `inc/enqueue.php` — script/style registration. Assets are versioned by `filemtime` (`solaris_asset_version()`), so edits bust cache without bumping `SOLARIS_VERSION`. Many stylesheets/scripts are enqueued conditionally (front page only, WooCommerce active, or a Customizer toggle on) — check the condition there when a new asset doesn't load.
- `inc/customizer.php` + `inc/customizer-controls.php` — Customizer panels/sections/settings (colors, typography, header, hero slides, footer, etc.) and the custom control classes they use. `solaris_get_option( $key )` (in `customizer.php`) is the read accessor used everywhere else in the theme to pull a Customizer value with its default.
- `inc/template-functions.php` — small presentational helpers used across templates: `solaris_icon()`, `solaris_shop_url()`, `solaris_nav_menu()`, `solaris_breadcrumbs()`, etc.
- `inc/template-hooks.php` — misc `wp_head`/`body_class`/excerpt filters.
- `inc/woocommerce.php` — all WooCommerce integration. Explicitly presentation-only: no cart/pricing/tax logic is reimplemented, everything routes through core WooCommerce hooks/template APIs so the plugin, Blocks checkout, and payment gateways keep working unmodified. Includes the shop-loop wrapper markup, wishlist toggle (client-side only, `localStorage` via `assets/js/main.js`, no DB table), "Buy Now" button/redirect, AJAX add-to-cart cart-count fragment, and the homepage featured-products query (`solaris_get_homepage_products()`, driven by the Customizer source picker: featured / best-selling / latest).
- `inc/security.php` — baseline hardening only (strip generator tag, drop pingback XML-RPC methods); deliberately does not touch file-editing constants, REST user enumeration, or XML-RPC wholesale.
- `inc/content-types.php` — three private CPTs backing editable homepage content: `solar_faq`, `solar_testimonial`, `solar_solution`. Each has getter functions (`solaris_get_faqs()`, `solaris_get_testimonials()`, `solaris_get_solutions()`) and a small custom meta box (role/company for testimonials, learn-more URL for solutions) instead of a page-builder plugin.
- `inc/contact-form.php` — contact form handling; nonce + honeypot verification via `solaris_verify_form_submission()` (defined in `security.php`).
- `inc/ajax-search.php` — backs the full-screen search modal (`template-parts/components/search-modal.php`) via `admin-ajax.php`; renders product grid fragments using the same markup as the shop loop so results look identical to a normal archive.
- `inc/ajax-quick-view.php` — product-card Quick View modal (`template-parts/components/quick-view-modal.php`); add-to-cart uses core `woocommerce_template_single_add_to_cart()`.
- `inc/ajax-shop-loading.php` — Lazy Load / Load More for shop archives (Customizer: Products Loading Mode). Secondary query, so it manually rebuilds catalog ordering (`WC()->query->get_catalog_ordering_args()`) and visibility/stock exclusions that `WC_Query::pre_get_posts()` only applies to the main query. Client side is `initShopLoading` in `main.js`.

All AJAX handlers go through `admin-ajax.php` with `check_ajax_referer()` nonces, and reuse `wc_get_template_part( 'content', 'product' )` for card markup — keep new fragments on that same template so cards stay identical everywhere.

**Homepage** (`front-page.php`) is a fixed sequence of `get_template_part( 'template-parts/home/... )` calls (hero, stats, categories, featured-products, why-choose-us, solutions, promo-banner, benefits, testimonials, faq, final-cta) — each section is independently editable/removable there. Other template parts: `template-parts/components/` (search, quick-view, cart-panel, contact-form), `header/`, and `footer/wave-*.php` (footer wave variants picked in the Customizer). Page templates `page-blog.php`, `page-contact.php`, `page-privacy.php`, `page-terms.php` are assigned via "Template Name" in wp-admin.

**Styling**: `assets/css/` is split by concern (one file per component/area, e.g. `variables.css`, `base.css`, `header.css`, `home.css`, `woocommerce.css`) rather than one stylesheet; Customizer-driven values (colors, fonts, header height/gradient) are emitted as CSS custom properties, not hardcoded.

**JS**: `assets/js/main.js` handles scroll-reveal (IntersectionObserver, `.reveal`/`.reveal-stagger-*` classes applied via `post_class`) the wishlist localStorage toggle, and shop lazy-load; feature scripts (`preloader.js`, `hero-jet.js`, `checkout-actions.js`, `admin-product-nav.js`) are separate files; `customizer-*.js` support the live Customizer preview.

When adding a new theme feature, follow the existing pattern: put logic in the matching `inc/` file (or add a new one and register it in `functions.php`'s include list) rather than in template files.
