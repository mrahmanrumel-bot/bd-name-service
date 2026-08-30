# Tripdesh — Bangladesh Domestic Travel Agency

A WordPress theme + plugin for Tripdesh, an online travel agency (OTA) for
Bangladesh domestic tourism: destinations, tour packages, hotels, bookings,
and an AI travel concierge.

See [ARCHITECTURE.md](ARCHITECTURE.md) for the full system design, data
model, and phased roadmap. This is a **Phase 1** build: content model,
browsing, native WP admin, booking capture (no live payment yet), and an
AI concierge that runs in fallback mode until you add an API key.

**The site is Bangla-first.** The customer-facing front end (and the AI
concierge, and REST API messages) render in Bengali by default; wp-admin
stays in whatever language your WordPress install is configured for
(normally English). See "Bengali/English" below for how this works and
what to do on an already-installed site.

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

## Bengali/English

**The front end defaults to Bengali.** `tripdesh-core.php` forces the
`bn_BD` locale for every non-admin request (front-end pages and REST API
responses); wp-admin is left alone, so it shows whatever language the
site is actually configured for. Both the theme and plugin ship a
`languages/tripdesh-bn_BD.mo` with professionally-worded Bengali for every
customer-facing string (menus, search, booking, chat widget, etc.) — you
don't need to configure anything for this to work.

Three things need a manual step on your end:

1. **WordPress core strings** (e.g. "Older posts", the comment form) are
   translated by a separate WordPress-maintained language pack, not
   something a theme/plugin can ship. Go to **Settings → General → Site
   Language** and add Bengali (বাংলা) — WordPress downloads its own core
   `bn_BD` translation automatically. Skipping this is harmless; a handful
   of core-only UI strings will just stay in English.
2. **Any menu you build yourself** (Appearance → Menus) is content you
   typed, not a template string — label its items in Bengali yourself.
   If you don't create a "Primary Menu", the theme's built-in fallback
   nav is already Bengali.
3. **If the plugin was activated before this update**, the tour-tier and
   travel-style taxonomy terms (Budget/Standard/Premium/Luxury, Family/
   Couple/etc.) were seeded in English at that time and won't retroactively
   change. Either rename them under **Tour Packages → Tour Tiers / Travel
   Styles** in wp-admin, or click **Import Bangla Demo Destinations** under
   Settings → Tripdesh, which re-seeds any of those terms that are still
   missing (it never touches ones that already exist).

For full bilingual *content* (separate Bengali/English versions of pages
you write), install WPML or Polylang — not bundled here, since it's a
licensing/config choice for the site owner. The AI concierge itself
replies in Bengali by default and switches to English via its own toggle,
independent of any content-translation plugin.

### Bangla demo destinations

Settings → Tripdesh has an **Import Bangla Demo Destinations** button that
creates the 21 core destinations from the brief (Bengali titles, English
slugs like `/destinations/sylhet`) and tags Sylhet/Srimangal/Jaflong/
Ratargul/Bisanakandi/Madhabkunda/Lawachara as the featured "তার দেশে
ভ্রমণ করুন" (Travel to the Land of Tea) homepage section. It's safe to run
more than once — any destination that already exists (matched by slug) is
left untouched, never overwritten or duplicated.

### Regenerating translations

Translation strings live in `tools/build-translations.php` as plain PHP
arrays (`msgid => msgstr`), not a `.pot`/external translation service —
edit that file and run `php tools/build-translations.php` to regenerate
both `.po` (for reference) and `.mo` (what WordPress actually loads)
files.

## What's intentionally not live yet

- **Payments** (bKash/Nagad/Rocket/cards) — bookings are captured and
  emailed to you for manual confirmation; see ARCHITECTURE.md §8 for the
  integration seam (`Tripdesh_Payment_Gateway::create_payment_session()`).
- **Supplier self-service portal**, **customer account dashboard**,
  **WhatsApp/SMS notifications** — Phase 2/3 per the roadmap in
  ARCHITECTURE.md §10.
