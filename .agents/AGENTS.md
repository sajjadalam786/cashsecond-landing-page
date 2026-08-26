# CashSecond Landing Page - Agent Guidelines & Project Goals

This file defines the mandatory coding, architectural, and operational rules for the **CashSecond** website/landing page.

---

## 🎯 7 Core Project Goals

### 1. Workspace Rules & Autonomous Alignment
- All modifications must strictly adhere to the guidelines documented in `.agents/` and `.agents/rules/`.
- Every feature, section, or script edit must comply with modern standards without adding unnecessary dependencies.

### 2. SEO, AEO & GEO Pre-Flight Requirement
- **Always** review and implement SEO, AEO (Answer Engine Optimization), and GEO (Generative Engine Optimization) best practices before writing code:
  - **SEO:** Canonical URLs, Open Graph, Twitter Cards, semantic HTML hierarchy (single `<h1>`, logical `<h2>`/`<h3>`), clean `robots.txt`, and XML sitemaps.
  - **AEO (Voice & Direct Answers):** Structured Q&A formatting, `FAQPage` JSON-LD schema, direct concise answers, definition lists, tabular comparisons.
  - **GEO (AI Search & Google AI Overviews):** Clear brand entity identification, verified Schema.org (`ElectronicsStore`, `LocalBusiness`, `Product`, `ItemList`, `BreadcrumbList`), transparent facts, and unambiguous pricing/condition grading.

### 3. Sub-2-Second Page Load Speed (< 2.0s)
- **Zero Framework Bloat:** Maintain pure Core PHP, Vanilla CSS, and Vanilla JavaScript. No heavy frontend frameworks (React, Vue) or jQuery.
- **Resource Optimization:**
  - Defer non-critical JavaScript.
  - Use `loading="lazy"` on all below-the-fold media and `loading="eager"` with `fetchpriority="high"` on above-the-fold hero images.
  - Specify explicit `width` and `height` attributes on all images to eliminate Cumulative Layout Shift (CLS = 0).
  - Serve static assets with Gzip/Deflate compression and browser caching via `.htaccess`.
  - Use Google Fonts with `font-display: swap` and DNS preconnects.

### 4. Full-Width Screen Utilization & Responsive Design
- **Desktop (1280px+):** Use generous container widths (`1280px` - `1360px`) with edge-to-edge full-bleed section bands, tickers, and fluid card grids so content never looks cramped on large displays.
- **Mobile (< 768px):** 100% full-width cards, edge-to-edge scroll strips, safe area padding (`env(safe-area-inset-bottom)`), zero horizontal overflow, and minimum 48px touch targets.
- **Fluid Layouts:** Implement fluid spacing and responsive typography using `clamp()`.

### 5. Strict Google Ads Policy Compliance
- **Destination Transparency:** Display registered physical business address (Arcadia Building, NCPA Marg, Nariman Point, Mumbai), clickable phone (`+91 897633 2211`), and official email.
- **Prominent Disclaimers:** Clarify independent retailer status and state clearly that Apple, iPhone, and iOS are registered trademarks of Apple Inc.
- **Honest Claims:** Use clear wording like "Estimated Resale Value" subject to physical inspection. Avoid exaggerated or deceptive claims (e.g. "Highest price in the world guaranteed").
- **Legal Navigation:** Prominently link Privacy Policy, Terms of Service, Disclaimer, Buyback Policy, and Cookie Policy in the footer.
- **Consent & Safety:** Provide transparent opt-in consent on lead forms, SSL/HTTPS readiness, and CSRF protection.

### 6. Easy Maintenance & Modular Folder Structure
- Maintain clean separation of concerns:
  - `config/config.php`: Central configuration and single source of truth for business contact, SEO defaults, webhook endpoints, and feature flags.
  - `data/`: Structured device catalog (`catalog.php`, `catalog.json`).
  - `includes/`: Modular PHP components (`header.php`, `footer.php`, `schema.php`).
  - `policies/`: Individual legal policy pages (`privacy-policy.php`, `terms.php`, `disclaimer.php`, `buyback-policy.php`, `cookie-policy.php`).
  - `forms/`: Backend AJAX form handlers (`submit.php`).
  - `assets/`: Structured stylesheets (`css/`), scripts (`js/`), and optimized media (`images/`).

### 7. Premium Typography & Apple-Grade Aesthetics
- High-end typography pairing modern sans-serif fonts (`Plus Jakarta Sans` / `Inter` / Apple SF Pro system stack).
- Apple-inspired design tokens: rich dark accents (`#0B0D10`, `#15181E`), crisp borders (`#E5E5E7`), subtle glassmorphism (`backdrop-filter: blur(20px)`), vibrant CTA accents (`#0071E3`, `#25D366`), and refined micro-animations.

---

## 📁 Rule Files Reference
- Detailed SEO/AEO/GEO rules: [.agents/rules/seo-aeo-geo.md](file:///Applications/XAMPP/xamppfiles/htdocs/cashsecond-landing-page/.agents/rules/seo-aeo-geo.md)
- Detailed Google Ads rules: [.agents/rules/google-ads-compliance.md](file:///Applications/XAMPP/xamppfiles/htdocs/cashsecond-landing-page/.agents/rules/google-ads-compliance.md)
- Detailed Speed & Responsive rules: [.agents/rules/speed-responsive-standards.md](file:///Applications/XAMPP/xamppfiles/htdocs/cashsecond-landing-page/.agents/rules/speed-responsive-standards.md)
