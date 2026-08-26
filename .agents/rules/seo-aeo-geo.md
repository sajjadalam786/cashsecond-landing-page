# Rule: SEO, AEO & GEO Pre-Flight Protocol

This document governs the mandatory standards for Search Engine Optimization (SEO), Answer Engine Optimization (AEO), and Generative Engine Optimization (GEO).

---

## 1. SEO Standards (Traditional Organic Search)
- **Title Tag:** Must be concise, keyword-rich, and follow the format: `[Primary Action] + [Target Keyword] | [Brand Name]`. Keep under 60 characters.
- **Meta Description:** Must provide an enticing, actionable summary (150-160 characters) with clear intent.
- **Canonical URL:** Must always be explicitly set via `<link rel="canonical" href="...">`.
- **Social Tags:** Both OpenGraph (`og:title`, `og:description`, `og:image`, `og:url`, `og:type`) and Twitter Cards (`twitter:card`, `twitter:title`, `twitter:description`, `twitter:image`) must be present.
- **Headings Structure:** Exactly one `<h1>` per page. Sub-sections must strictly follow `<h2>`, `<h3>` hierarchy.
- **Image Optimization:** Every `<img>` must contain a meaningful, contextual `alt` attribute.
- **Crawlability:** Maintain accurate `sitemap.xml` and restrictive `robots.txt` disallowing internal config/log paths.

---

## 2. AEO Standards (Answer Engine Optimization for Voice & Chat AI)
- **Direct Answer Format:** Each FAQ and value proposition must begin with a 1-2 sentence direct, factual answer before elaboration.
- **Structured Data:** Use valid Schema.org `FAQPage` JSON-LD schema containing verbatim matches to on-page FAQ content.
- **Step-by-Step Lists:** Process steps (e.g. 3-step valuation, 32-point inspection) must use numbered or ordered semantic structures (`<ol>` or identifiable numbered cards) easily parsed by Siri, Google Assistant, and Perplexity.

---

## 3. GEO Standards (Generative Engine Optimization for LLM Search & AI Overviews)
- **Entity Signals:** Clearly define the business entity in `schema.php` as `ElectronicsStore`, `LocalBusiness`, and `Organization`.
- **Disambiguation:** Explicitly declare the brand relationship: CashSecond is an independent pre-owned electronics platform and not an official Apple Inc. partner.
- **Factual Attributes:** Present structured specifications for all models (base prices, condition tiers, inspection parameters) in machine-readable JSON-LD and semantic tables.
- **Local Citations & NAP:** Ensure 100% Name, Address, Phone (NAP) consistency across Schema.org, footer, header, and metadata.
