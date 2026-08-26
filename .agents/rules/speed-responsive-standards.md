# Rule: Speed (<2.0s) & Full-Width Responsive Standards

This document establishes the performance, responsiveness, and Core Web Vitals engineering rules for the CashSecond landing page.

---

## 1. Speed & Core Web Vitals (< 2.0s Load Time)
- **Zero Heavy Frameworks:** Pure Core PHP, Vanilla CSS, and lightweight Vanilla JS only.
- **Script Loading:** All non-critical JavaScript files must be loaded with `defer` to prevent render blocking.
- **Font Strategy:**
  - Google Fonts (`Plus Jakarta Sans` / `Inter`) must be linked with `preconnect` and `font-display: swap`.
  - System font fallbacks (`-apple-system, BlinkMacSystemFont, "SF Pro Display", sans-serif`) must ensure instantaneous text rendering without flash of unstyled text (FOUT).
- **Image Optimization & CLS = 0:**
  - Every `<img>` must specify explicit `width` and `height` attributes to avoid Cumulative Layout Shift.
  - Above-the-fold hero image must have `loading="eager"` and `fetchpriority="high"`.
  - All below-the-fold media must have `loading="lazy"`.
- **Caching & Compression:**
  - `.htaccess` must enforce Gzip/Deflate compression for HTML, CSS, JS, SVG, and JSON.
  - Long cache expirations must be set for static assets (images, fonts, stylesheets).

---

## 2. Screen-Width Utilization & Responsive Design
- **Desktop (1280px+):**
  - Use expansive max container widths (`--container-max: 1280px` - `1360px`) so high-resolution desktop screens (1080p, 1440p, 4K) are fully utilized with rich presentation.
  - Full-bleed background bands, auto-scrolling marquee strips, and fluid multi-column product grids.
- **Mobile (< 768px):**
  - Edge-to-edge visual containers with comfortable padding (`clamp(16px, 4vw, 24px)`).
  - 100% full-width cards in single-column stacks or smooth horizontal snapping carousels.
  - Sticky bottom conversion bar with `padding-bottom: max(14px, env(safe-area-inset-bottom))` for modern bezel-less iPhones and Android phones.
  - Minimum touch target size of 48px × 48px for all interactive buttons and inputs.
- **No Overflow:** Enforce `overflow-x: hidden` and `max-width: 100%` on body and containers to prevent horizontal scroll bars.
