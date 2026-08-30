# Tripdesh — Bangladesh Domestic Travel Agency (OTA)

Architecture and roadmap for Tripdesh, a WordPress-based online travel agency
platform. This document is the reference for what has been built, what it
maps to from the original product brief, and what is intentionally deferred
to later phases.

## 1. Why WordPress, and what that means for the "AI multi-agent" brief

The original brief describes a full custom OTA platform (12 AI agents,
booking engine, payment gateways, supplier portal, admin dashboards). Built
as bespoke software, that is a multi-team, multi-month project. The decision
here is to deliver the same *product* on a WordPress foundation:

- **Content & catalog** (destinations, tour packages, hotels, activities,
  transportation, blog/SEO content) map directly to WordPress custom post
  types and taxonomies — this is WordPress's core strength and gives you a
  free, familiar admin UI (Section 11 of the brief) with zero extra code.
- **Bookings** are a custom post type with a REST endpoint, generating a
  human-readable reference (`BDT-2026-000123`) — see §5.
- **The 12 AI "agents"** are not 12 separate running services. They are
  implemented as a single **AI Concierge REST endpoint** in the plugin
  (`tripdesh_ai_concierge`) that calls one LLM (Claude or GPT, configurable)
  with a system prompt encoding the concierge/destination/package/itinerary/
  pricing reasoning described in the brief. This is the practical version of
  "orchestration" for a v1: one well-prompted agent that has read access to
  the site's own destinations/tours/hotels via WP_Query, rather than 12
  independently deployed micro-agents. The architecture leaves room to split
  this into real sub-services later (§9) without changing the public API.
- **Payments (bKash/Nagad/cards)** are *not* live-integrated in this pass —
  no merchant credentials exist yet, and wiring a fake integration would be
  worse than none. The plugin defines the settings fields and the booking
  state machine (`pending → awaiting_payment → confirmed → cancelled`) so a
  real gateway integration is a matter of filling in one class (§8).

This is Phase 1 ("Foundation") from the brief's own phasing (§29): project
structure, data model, homepage, destination/tour/hotel browsing, and admin
via native WP screens. Booking payments, supplier self-service, live AI, and
marketing automation are Phases 2–5, deliberately deferred.

## 2. Repository layout

```
wp-content/
  themes/
    tripdesh/              # Front-end theme (templates, CSS, JS)
  plugins/
    tripdesh-core/          # Data model, booking logic, AI proxy, SEO, settings
```

Both are plain PHP with **no build step and no Docker runtime** — copy
`wp-content/themes/tripdesh` and `wp-content/plugins/tripdesh-core` into any
WordPress install (5.9+, PHP 7.4+), activate the plugin, activate the theme.

## 3. Data model (custom post types & taxonomies)

| Entity | CPT slug | Notes |
|---|---|---|
| Destination | `destination` | Overview, best time, how to reach, where to stay, things to do, food, budget, safety, local transport — one meta field per brief §3 |
| Tour Package | `tour_package` | Duration, price, inclusions/exclusions, itinerary (day-by-day repeater), tier (`tour_type` taxonomy: Budget/Standard/Premium/Luxury) |
| Hotel | `hotel` | Star rating, price/night, amenities, policies, room types |
| Activity | `activity` | Standalone bookable experiences |
| Transportation | `transport_option` | Bus/train/car/boat/launch options tied to a route |
| Booking | `tripdesh_booking` | Created via REST/front-end form; not publicly listed |
| Testimonial | `testimonial` | Customer reviews shown on homepage |

Shared taxonomy `tripdesh_location` tags tours/hotels/activities/transport to
a `destination` post (by matching slug), giving free cross-linking and
filtering without a relational DB layer.

`tour_type` taxonomy: `budget`, `standard`, `premium`, `luxury` (brief §4,
Agent 3).

Full field list lives in `wp-content/plugins/tripdesh-core/includes/class-post-types.php`
and `class-meta-boxes.php` — this is the "database schema" for v1; each meta
field is a `post_meta` row keyed `_tripdesh_<field>`.

## 4. AI Concierge

`POST /wp-json/tripdesh/v1/concierge`

- Accepts `{ message, language, history[] }`.
- If no API key is configured in **Settings → Tripdesh AI**, returns a
  canned bilingual fallback ("AI assistant not yet configured") instead of
  failing — the site must never look broken to a visitor.
- If a key is configured, calls the configured provider (Anthropic or
  OpenAI, chosen in settings) server-side. The API key is never sent to the
  browser.
- The system prompt is built from live data: it queries up to N published
  destinations and tour packages so recommendations stay grounded in what's
  actually bookable, instead of the model inventing packages that don't
  exist. This is the pragmatic form of Agent 1 (Concierge) + Agent 2
  (Destination) + Agent 3 (Package) fusion described in §20 (orchestration).
- The concierge **never books or charges** — it only recommends. Any action
  with financial or booking consequence goes through the human-in-the-loop
  booking form (§5), matching brief §5's "AI must not independently perform
  high-risk actions."

## 5. Booking flow

`POST /wp-json/tripdesh/v1/booking`

1. Validates the referenced tour/hotel exists and is published.
2. Creates a `tripdesh_booking` post, status `pending`.
3. Generates a reference: `BDT-{year}-{6 digit sequence}` (brief §8).
4. Sends a confirmation email to the customer and a notification email to
   the admin (`wp_mail` — swap for a transactional provider in production).
5. Returns the reference and a summary to the browser.

No payment is captured at this step (see §1 and §8). An admin marks a
booking `confirmed` from the native WP edit screen once payment is verified
manually — this is the honest v1 replacement for a live payment gateway and
matches the brief's human-in-the-loop requirement (§5) by construction.

## 6. SEO (brief §17)

`includes/class-seo-schema.php` only runs if **no** SEO plugin (Yoast,
RankMath, SEOPress) is already active, to avoid duplicate/conflicting tags.
It emits:

- Meta description (from excerpt or trimmed content), canonical URL, Open
  Graph + Twitter Card tags.
- JSON-LD: `TouristDestination` on destination pages, `TouristTrip` on tour
  packages, `Hotel` on hotel pages, `FAQPage` on the FAQ template,
  `BreadcrumbList` sitewide.
- Clean permalinks: `/destinations/coxs-bazar`, `/tours/coxs-bazar-3-days`,
  `/hotels/coxs-bazar-beach-resort` (rewrite rules in `class-post-types.php`).

Sitemap/robots.txt: delegated to WordPress core's built-in XML sitemaps
(5.5+) rather than reimplemented — it already covers these CPTs once they're
registered with `show_in_rest` + public.

## 7. Bengali/English

Theme and plugin are translation-ready (`text domain: tripdesh`, all
user-facing strings wrapped in `__()`/`_e()`). Recommended production setup:
WPML or Polylang for full bilingual content (brief §12, §23) — not bundled,
since it's a licensing/config decision for the site owner, not code. The AI
concierge accepts a `language` field (`bn`/`en`) independent of the content
language plugin.

## 8. Payments — architecture, not implementation

`includes/class-settings.php` defines (but does not wire up) fields for:

- bKash/Nagad/Rocket merchant credentials
- Card gateway (Stripe or SSLCommerz — SSLCommerz is the practical choice
  for Bangladesh since it aggregates bKash/Nagad/Rocket/cards/bank behind
  one PCI-compliant integration, vs. integrating each wallet's raw API
  separately)
- A `Tripdesh_Payment_Gateway` interface stub
  (`includes/class-payment-gateway.php`) with one method,
  `create_payment_session( $booking_id )`, so Phase 2 is "implement this
  class against SSLCommerz's API," not a redesign.

**Recommendation for Phase 2**: integrate SSLCommerz first (single
integration covers bKash/Nagad/Rocket/cards/bank transfer for BDT), add a
direct bKash Merchant API integration later only if transaction-fee
economics justify bypassing the aggregator.

## 9. Path from "one AI endpoint" to real multi-agent orchestration

When there's a reason to split the single concierge endpoint into real
services (e.g. pricing rules get too complex for a prompt, or supplier
inventory needs its own sync jobs), the natural seams are already the class
boundaries in `tripdesh-core`:

- `class-ai-concierge.php` → orchestrator, stays in WP, calls out
- Destination/Package/Hotel reasoning → already isolated by CPT, can become
  a dedicated read API for an external agent service
- Pricing (`class-pricing.php`, Phase 2) → supplier price + markup rules,
  the natural first thing to extract into its own service once suppliers
  are self-managing prices
- Support escalation, marketing content generation, review analysis
  (Agents 8–11 in the brief) are genuinely separate concerns from booking
  and are the right candidates for standalone services in Phase 4–5, once
  there's real traffic/review volume to justify them.

Until then, one well-scoped endpoint beats twelve speculative ones.

## 10. Roadmap

- **Phase 1 (this pass)** — theme + plugin, CPTs/taxonomies, homepage,
  destination/tour/hotel browsing, native WP admin, AI concierge (fallback
  mode until a key is configured), booking capture without payment, SEO.
- **Phase 2** — SSLCommerz payment integration, booking status automation,
  customer account dashboard (My Bookings) via a front-end account page.
- **Phase 3** — Supplier self-service (a restricted WP role + front-end
  submission forms for hotels/tour operators, admin approval queue).
- **Phase 4** — Live AI key wired in production, itinerary customization
  UI, WhatsApp/SMS notifications.
- **Phase 5** — SEO/marketing content automation with an approval queue,
  review analysis, analytics dashboard.
- **Phase 6** — International expansion, flights, corporate travel.

## 11. Security notes for whoever deploys this

- Never commit real API keys; all secrets live in WP options set via the
  admin UI (`Settings → Tripdesh AI`), which WordPress stores in the DB —
  for higher assurance, move them to `wp-config.php` constants instead
  (the settings class checks for a constant override first).
- The booking and concierge REST routes are rate-limited via a simple
  transient counter per IP (`class-rest-api.php`) to reduce abuse/cost from
  the AI endpoint; put a real WAF/rate limiter in front in production.
- No raw card data is ever handled by this codebase — by design, per §9,
  payment happens on the gateway's hosted page.
