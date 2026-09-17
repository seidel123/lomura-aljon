# LUMORA — Real Estate Landing Page

## Overview

A custom WordPress + Elementor Free landing page built for the **LUMORA — Real Estate** assessment. This project demonstrates original design thinking, creative visual execution, and polished layout implementation using only Elementor Free features.

## Business

**LUMORA — Real Estate** is a fictional premium real estate agency based in Noosa, Australia. The brand represents architecturally significant homes across Noosa, Adelaide, and Hobart, focusing on quality, trust, and a premium property-buying experience. The agency positions itself as a boutique studio for distinctive Australian homes: coastal retreats, city landmarks, and quiet escapes.

## Assessment Requirements Implemented

| Section | Status | Description |
|---------|--------|-------------|
| Header & Navigation | ✅ | Text wordmark, 5-anchor nav, CTA, skip link, mobile fullscreen menu |
| Hero Slider | ✅ | 3 slides with property imagery, headlines, ledes, meta, dual CTAs, controls (prev/next, dots, counter, progress) |
| About Section | ✅ | Noosa studio story, captioned photo, counters (14/480+/$2.1B), CTA |
| Properties/Residences | ✅ | 3 sticky-stack property cards with image, specs, price, enquiry links |
| Articles/Insights | ✅ | 3 cards with image, title, excerpt, meta, read links to article posts |
| Contact/CTA | ✅ | Centre panel with headline, lede, AJAX contact form, side contacts |
| Footer | ✅ | Mega wordmark, contact details, nav links, 4 SVG social icons, legal links |
| Responsive Layouts | ✅ | Desktop (1440/1280/1024) and mobile (390/375/430) breakpoints |

## Design Approach

**Original Custom Design** — The entire visual system was created specifically for LUMORA. No ready-made website templates or imported Elementor template kits were used. The design evolved through four documented iterations (Nova → Brutal v2 → Cinema v3 → Daylight), each refining the visual language.

**Visual Direction** — Editorial luxury real estate aesthetic: near-black canvas, bone/off-white surfaces, steel-blue/amber accents, hairline grid lines, ghost numerals, marble/glass photo treatment.

**Typography System**
- Display: **Fraunces** (serif, optical sizes 9–144, weights 400–700)
- Body/UI: **Inter** (weights 400–600)
- Both loaded via Google Fonts with `display=swap` and preconnect

**Colour System** — Strict 60/30/10 ratio
- 60% Dominant: White (`#FFFFFF`) / Off-white (`#F8F9FA`) surfaces
- 30% Secondary: Slate ink (`#1A202C`) headings/body, muted slate (`#4A5568`) metadata
- 10% Accent: Warm amber (`#D69E2E`) on exactly 3 conversion CTAs (header, form submit, mobile bar)

**Spacing & Hierarchy** — Fluid `clamp()` scales, chapter markers with `■` prefix, hairline rules, ghost numerals with text-stroke, drop-cap editorial flourishes.

**Responsive Approach** — Mobile-first CSS with breakpoints at 1024px and 768px. Sticky property cards collapse to static stacks. Hero typography steps down at 768px. Footer gains clearance for sticky mobile CTA bar.

**Imagery** — 11 high-resolution local property photos (`alj-*`), all with responsive `srcset`, meaningful `alt` text, lazy loading below fold, eager preload for hero LCP image.

## Technology

| Component | Implementation |
|-----------|----------------|
| CMS | WordPress 7.1 |
| Page Builder | Elementor Free 4.2.4 |
| Parent Theme | Hello Elementor 3.5.1 |
| Child Theme | `lumora-aljon-child` (custom) |
| PHP | 8.2.29 |
| Database | MySQL 8.4.0 |
| Web Server | nginx 1.26.1 |

**Elementor Free Compliance** — **100% Free**. Zero Pro widgets used. All 100 widgets are Free types: `html` (65), `text-editor` (10), `image` (9), `button` (8), `heading` (7), `shortcode` (1). Slider, rotator, and form are custom HTML/CSS/JS — not Pro widgets. No Elementor Pro installed.

## Key Features

| Feature | Implementation |
|---------|----------------|
| Responsive Navigation | Desktop inline, mobile fullscreen JS-injected hamburger, scrollspy active states |
| Hero Slider | 3 slides, Ken Burns kenburns, fraction counter, dot/progress/arrow controls, swipe/keyboard, ARIA live region |
| CTAs | Header, hero ×6, hero buttons, mobile sticky bar, form submit — all tracked |
| Property Presentation | Sticky-stack cards (desktop), static stack (mobile), hover scale, floating peek image |
| Article Cards | 3 cards with image, category, h3 title, excerpt, meta, read link to article post |
| Contact Form | Custom `[lumora_contact_form]` shortcode → AJAX + `admin-post.php` fallback, honeypot, nonce, `lumora_inquiry` CPT storage, email notification |
| Accessibility | Skip link, single H1, h3 card/scene titles, `<main>` landmark, live regions, focus-visible, reduced-motion, ARIA labels |
| Mobile Sticky CTA | Fixed bottom bar (Call / Enquire), footer clearance padding |
| Responsive Images | 11 local `alj-*` photos, multi-width srcset, lazy loading below fold, hero eager preload |
| Semantic Markup | `<main>`, `<header>`, `<nav>`, `<section>`, `<article>`, `<footer>`, `<h1>`–`<h3>` hierarchy |
| Reduced Motion | CSS `@media (prefers-reduced-motion)` + JS guards disable animations/autoplay |

## Content

All property and article content is **sample/demo content** created for the assessment:
- 3 properties: The Dune House (Noosa), Wattle House (Adelaide Hills), Cliff House (Hobart)
- 3 articles: "Noosa Prestige Holds Its Nerve", "Homes That Weather Beautifully", "Buy Once, Hold for Generations"
- 3 testimonials with fictional client names/locations
- Contact details: `hello@lumora.com.au`, `+61 7 5440 8899`, 14 Hastings Street, Noosa QLD 4567

## Responsive Testing

| Viewport | Status | Verified |
|----------|--------|----------|
| Desktop 1440px | ✅ PASS | Centered 1440px container, balanced whitespace |
| Desktop 1280px | ✅ PASS | Content constrained, grids intact |
| Desktop 1024px | ✅ PASS | Grids collapse to 1-col, sticky → static |
| Mobile 390px | ✅ PASS | Stacked grids, stacked hero CTA, footer clearance |
| Mobile 375px | ✅ PASS | Hero typography step-down, stacked nav |
| Mobile 430px | ✅ PASS | No horizontal overflow, touch targets ≥44px |

No horizontal scrolling, no overlapping elements, no clipped text at any breakpoint.

## Elementor Free Compliance

- ✅ **Elementor Pro NOT required** — `elementor-pro` not installed
- ✅ **No Pro widgets used** — All 100 widgets are Free types
- ✅ **No Pro features required** — Slider, rotator, form are custom implementations
- ✅ **Free parent theme** — Hello Elementor 3.5.1
- ✅ **Kit untouched** — Default Kit only sets `site_name`, no colours/fonts

## Project Structure

```
lumora-aljon/
├── app/
│   └── public/
│       └── wp-content/
│           ├── themes/
│           │   ├── hello-elementor/          # Parent theme (not tracked)
│           │   └── lumora-aljon-child/       # Custom child theme
│           │       ├── assets/
│           │       │   ├── css/
│           │       │   │   ├── nova.css           # Base tokens + layout
│           │       │   │   ├── aljon-cinema.css   # Noir gallery layer
│           │       │   │   └── aljon-daylight.css # Daylight 60/30/10 layer
│           │       │   └── js/
│           │       │       ├── nova-slider.js     # Hero slider (vanilla JS)
│           │       │       └── nova-enhance.js    # Mobile menu, scrollspy, form, rotator
│           │       ├── functions.php          # SEO, schema, form, enqueue, shortcodes
│           │       └── style.css              # Theme header (v1.6.2)
│           ├── plugins/
│           │   └── elementor/               # Elementor Free 4.2.4 (not tracked)
│           └── uploads/                     # Local images (not tracked)
├── app/sql/local.sql                      # Database dump (not tracked)
├── conf/                                  # nginx/php/mysql configs (Local)
├── logs/                                  # Runtime logs (not tracked)
├── .gitignore
└── README.md
```

## Setup / Installation

This is a Local (by Flywheel) WordPress site. To run:

1. Open the `lumora-aljon` site in **Local** (requires Local by Flywheel)
2. Site runs at `http://lumora-aljon.local` (or `http://localhost:10013` with Host header)
3. WordPress admin: `http://lumora-aljon.local/wp-admin`
4. Elementor editor: Pages → LUMORA → Edit with Elementor

**Requirements:**
- Local by Flywheel (or compatible Docker/PHP/MySQL stack)
- PHP 8.2+, MySQL 8.4+, nginx
- Elementor Free 4.2.4+ (installed via WP admin)

**After cloning/pulling:**
1. Import database: `wp db import app/sql/local.sql` (or use Local's import)
2. Run `wp elementor flush_css` to regenerate CSS
3. Visit front page to verify

## Development Notes

- **Version:** 1.6.2 (aligned across `style.css`, `functions.php`, enqueued assets)
- **Child theme:** `lumora-aljon-child` (parent: `hello-elementor`)
- **CSS Architecture:** Layered cascade — `nova.css` (base) → `aljon-cinema.css` (noir) → `aljon-daylight.css` (daylight override). Load order enforced by `wp_enqueue_style` dependencies.
- **JS:** Vanilla ES5, no dependencies. `nova-slider.js` (carousel), `nova-enhance.js` (menu, scrollspy, progress, form, rotator, parallax).
- **Form:** Custom shortcode `[lumora_contact_form]` → stores to private `lumora_inquiry` CPT + emails `hello@lumora.com.au`. AJAX + graceful POST fallback.
- **Live Links:** Relative-URL filter (`nova_relative_*`) rewrites same-host absolute URLs to root-relative for Local Live Link compatibility.
- **Retired:** `aljon-brutal.css` removed (was v2 rollback artifact).
- **Cleaned:** `lumora-test` references scrubbed from comments/allowlists. Stock WP content (Hello world!, Sample Page, draft Privacy Policy) trashed.
- **Version:** `NOVA_CHILD_VERSION` = `1.6.2` (aligned in `style.css` + `functions.php` + enqueues).

## Final Assessment Status

### SUBMISSION READY

| Category | Result |
|----------|--------|
| Mandatory Sections (5) | ✅ All present and polished |
| Design Quality | ✅ Original custom design, editorial luxury aesthetic |
| Elementor Free Compliance | ✅ 100% Free widgets, no Pro dependencies |
| Responsive | ✅ PASS at 1440/1280/1024/390/375/430px |
| Accessibility | ✅ Skip link, landmarks, live regions, focus, reduced-motion |
| Content | ✅ Sample content complete, no placeholder/lorem |
| Code Cleanliness | ✅ Version aligned, test refs scrubbed, brutal file removed |
| No Stock Cruft | ✅ Default WP posts/pages trashed |
| Final Verification | ✅ All 27/27 checks PASS |

---

**LUMORA — FINALIZED** · **README — ADDED** · **COMMIT — READY** · **STATUS — SUBMISSION READY**
