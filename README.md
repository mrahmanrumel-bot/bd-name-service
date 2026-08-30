# Tripdesh — Bangladesh Domestic Travel Agency

A WordPress theme + plugin for Tripdesh, an online travel agency (OTA) for
Bangladesh domestic tourism: destinations, tour packages, hotels, bookings,
and an AI travel concierge.

See [ARCHITECTURE.md](ARCHITECTURE.md) for the full system design, data
model, and phased roadmap. This is a **Phase 1** build: content model,
browsing, native WP admin, booking capture (no live payment yet), and an
AI concierge that runs in fallback mode until you add an API key.

## What's in this repo

```
wp-content/
  themes/tripdesh/          Front-end theme
  plugins/tripdesh-core/    Data model, booking, AI concierge, SEO
```

No build step, no Docker, no bundler — plain PHP/CSS/JS you copy onto a
WordPress installation.

## Requirements

- WordPress 5.9+
- PHP 7.4+
- MySQL/MariaDB (whatever your WordPress host uses)

## Installation

1. Get a WordPress site running (any host — Hostinger, SiteGround,
   Cloudways, a self-managed LAMP/LEMP server, etc.). This repo does not
   include WordPress core.
2. Copy `wp-content/plugins/tripdesh-core` into your site's
   `wp-content/plugins/` directory.
3. Copy `wp-content/themes/tripdesh` into your site's `wp-content/themes/`
   directory.
4. In WP Admin → Plugins, activate **Tripdesh Core**.
5. In WP Admin → Appearance → Themes, activate **Tripdesh**.
6. Go to Settings → Reading and set "Your homepage displays" to a static
   page is *not* needed — the theme's `front-page.php` renders
   automatically once the theme is active.
7. Go to Settings → Permalinks and click **Save Changes** once (flushes
   rewrite rules so `/destinations/...`, `/tours/...`, `/hotels/...` URLs
   work).
8. Go to Settings → Tripdesh and configure:
   - AI provider + API key (optional — leave blank to keep the AI chat in
     fallback/no-key mode)
   - Currency, contact phone/email/WhatsApp
   - Payment gateway (informational only in this phase — see
     ARCHITECTURE.md §8)
9. Create content:
   - **Destinations** (Cox's Bazar, Sylhet, Sajek Valley, etc.) with the
     Tripdesh Details meta box filled in (best time to visit, how to
     reach, etc.)
   - **Tour Packages**, tagging each with a `tripdesh_location` term
     matching the destination's slug so it shows up on that destination's
     page, and a `tour_type` term (Budget/Standard/Premium/Luxury)
   - **Hotels**, **Activities**, **Transportation**, **Testimonials**
10. Create pages using the page templates under **Page Attributes → Template**:
    - "About" → `template-about.php`
    - "Contact" → `template-contact.php`
    - "FAQ" → `template-faq.php` (write questions as H3 headings
      immediately followed by a paragraph answer — the schema output
      parses that pattern automatically)
    - "AI Trip Planner" → `template-ai-trip-planner.php`
11. Under Appearance → Menus, create a "Primary Menu" and assign it to the
    "Primary Menu" location (the theme falls back to a sensible default
    nav if you skip this).

## Bilingual (Bengali/English)

The theme and plugin are translation-ready (`text domain: tripdesh`). For
full bilingual content (separate Bengali/English pages), install WPML or
Polylang — not bundled here, since it's a licensing/config choice for the
site owner. The AI concierge itself already replies in Bengali or English
based on the visitor's toggle, independent of any content-translation
plugin.

## What's intentionally not live yet

- **Payments** (bKash/Nagad/Rocket/cards) — bookings are captured and
  emailed to you for manual confirmation; see ARCHITECTURE.md §8 for the
  integration seam (`Tripdesh_Payment_Gateway::create_payment_session()`).
- **Supplier self-service portal**, **customer account dashboard**,
  **WhatsApp/SMS notifications** — Phase 2/3 per the roadmap in
  ARCHITECTURE.md §10.
