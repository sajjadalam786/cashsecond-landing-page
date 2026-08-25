# CashSecond - Second-Hand / Used Mobile Phone Landing Page

A high-converting, fast-loading, mobile-first single-page landing website for a **Second-Hand / Used Mobile Phone Shop**, built with **pure Core PHP, modern Vanilla CSS, and lightweight Vanilla JavaScript**. 

Engineered strictly according to **Google Ads destination transparency requirements**, Meta Ads compliance, local search SEO/AEO, and instant WhatsApp/phone lead generation.

---

## Table of Contents
1. [Key Features](#key-features)
2. [Google Ads Transparency & Policy-Safe Standards](#google-ads-transparency--policy-safe-standards)
3. [Folder Structure](#folder-structure)
4. [Local XAMPP Setup](#local-xampp-setup)
5. [Google Sheets Integration Setup](#google-sheets-integration-setup)
6. [Business Information Customization](#business-information-customization)
7. [SEO & AEO Setup](#seo--aeo-setup)
8. [Analytics & Conversion Tracking Setup](#analytics--conversion-tracking-setup)
9. [Security & Anti-Spam Measures](#security--anti-spam-measures)
10. [Deployment on Hostinger / cPanel](#deployment-on-hostinger--cpanel)
11. [Git Commands](#git-commands)

---

## 1. Key Features

- **No Framework Bloat:** 100% Core PHP without heavy CMS or node dependencies. Loads instantaneously.
- **Conversion-Optimized 10-Section Layout:**
  1. Top announcement & compact sticky header with click-to-call & WhatsApp CTAs.
  2. Mobile hero section with keyword-tailored H1 and 32-point inspection trust badges.
  3. 4 core value proposition pillars.
  4. Interactive brand filter tabs (Apple, Samsung, OnePlus, Pixel, Xiaomi, etc.).
  5. Responsive product card catalog with transparent estimated pricing and condition grading.
  6. **Buyer's Transparency Guide:** 32-point technical testing breakdown, battery health disclosure, and legal ownership guarantees.
  7. 3-step simple buying process.
  8. Customer reviews and store trust markers.
  9. AEO-optimized FAQ accordion with valid `FAQPage` JSON-LD schema.
  10. High-converting AJAX lead form with real-time validation & instant WhatsApp fallback.
  11. Store address, operating hours, and Google Maps embed.
- **Mobile-First UX:** Ultra-compact mobile header, optimized above-the-fold CTA visibility, and sticky bottom conversion bar (`[Call Store] [WhatsApp Now] [Enquire]`).
- **FREE Google Sheets Webhook Sync:** Direct POST integration to Google Sheets via Google Apps Script with zero hardcoded credentials in public JS.
- **Complete Schema.org Markup:** `ElectronicsStore`, `LocalBusiness`, `Product`, `ItemList`, `FAQPage`, and `WebSite` schemas.
- **Compliant Legal Pages:** Privacy Policy, Terms of Service, and Trademark Disclaimer included.

---

## 2. Google Ads Transparency & Policy-Safe Standards

This landing page follows honest, policy-safe practices:
- **No Fake Discounts / Striking MRPs:** Prices are listed transparently with condition disclaimers.
- **No Fabricated Review Counters:** No fake claims like "10,000+ satisfied buyers" or "No.1 in India".
- **No Misleading Affiliation:** Prominent disclaimer clarifies that all OEM brand names (Apple, Samsung, OnePlus, etc.) belong to their respective trademark holders and that the store is an independent retailer of pre-owned electronics.
- **Transparent Disclosures:** Battery health, cosmetic grades, accessory inclusions, and warranty terms are stated upfront.
- **Accessible Legal Policies:** Privacy Policy, Terms of Service, and Disclaimer linked directly in the footer and consent text.

---

## 3. Folder Structure

```
cashsecond-landing-page/
├── index.php                 # Main single-page landing application
├── .htaccess                 # Compression, caching & security headers
├── robots.txt                # Search engine crawler directives
├── sitemap.xml               # XML sitemap for SEO indexing
├── README.md                 # Full documentation
├── config/
│   └── config.php            # Business details, catalog, SEO, tracking, webhook settings
├── includes/
│   ├── header.php            # Header, meta tags, OpenGraph, analytics hooks & nav
│   ├── footer.php            # Footer, NAP details, sticky mobile bar & scripts
│   └── schema.php            # JSON-LD Structured Data generator
├── assets/
│   ├── css/
│   │   └── style.css         # Clean, responsive CSS design system
│   ├── js/
│   │   └── script.js         # AJAX form, filters, accordion, tracking dispatchers
│   └── images/
│       ├── cashsecond-logo.png # Official brand logo (Cyan icon + wordmark + ®)
│       ├── brands/           # Vector brand logos (Apple, Samsung, OnePlus, etc.)
│       └── phones/           # Vector smartphone mockups (iPhone, Galaxy, etc.)
├── forms/
│   └── submit.php            # CSRF validation, honeypot, rate limiting, Google Sheets dispatcher & logging
├── logs/                     # Local lead CSV backup (protected by .htaccess)
└── policies/
    ├── privacy-policy.php    # Privacy Policy page
    ├── terms.php             # Terms & Conditions page
    └── disclaimer.php        # Trademark & device grading disclaimer
```

---

## 4. Local XAMPP Setup

1. Ensure **XAMPP** is installed and **Apache** is running.
2. Confirm the project folder is inside your XAMPP `htdocs` directory:
   ```
   C:\xampp\htdocs\cashsecond-landing-page\
   ```
3. Open your web browser and navigate to:
   ```
   http://localhost/cashsecond-landing-page/
   ```

---

## 5. Google Sheets Integration Setup (100% FREE)

The lead form posts enquiries directly to a Google Sheet via a lightweight Google Apps Script Web App.

### Step 1: Create the Google Sheet
1. Go to [Google Sheets](https://sheets.google.com) and create a new blank spreadsheet (e.g. `CashSecond Leads`).
2. Set up row 1 headers:
   - **Column A:** `Timestamp`
   - **Column B:** `Name`
   - **Column C:** `Phone`
   - **Column D:** `Model`
   - **Column E:** `Budget`
   - **Column F:** `Message`
   - **Column G:** `City`
   - **Column H:** `IP`

### Step 2: Create Google Apps Script
1. In the spreadsheet, click **Extensions** → **Apps Script**.
2. Replace the script code with:

```javascript
function doPost(e) {
  try {
    var sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
    var data = JSON.parse(e.postData.contents);
    
    sheet.appendRow([
      data.timestamp || new Date(),
      data.name || '',
      data.phone || '',
      data.model || '',
      data.budget || '',
      data.message || '',
      data.city || '',
      data.ip || ''
    ]);
    
    return ContentService.createTextOutput(JSON.stringify({ status: "success" }))
      .setMimeType(ContentService.MimeType.JSON);
  } catch (error) {
    return ContentService.createTextOutput(JSON.stringify({ status: "error", message: error.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}
```

### Step 3: Deploy Web App
1. Click **Deploy** → **New deployment**.
2. Choose **Web app**.
3. Set **Execute as:** `Me` and **Who has access:** `Anyone`.
4. Click **Deploy**, authorize permissions, and copy the **Web app URL**.

### Step 4: Add URL to `config/config.php`
Open `config/config.php` and set:
```php
'integrations' => [
    'google_sheets_webhook_url' => 'https://script.google.com/macros/s/YOUR_DEPLOYED_ID/exec',
    'notification_email'        => 'leads@cashsecond.in',
    'enable_local_lead_log'     => true,
],
```

---

## 6. Business Information Customization

All store information is centralized in `config/config.php`.

Placeholders to update:
```php
'business' => [
    'name'             => '[BUSINESS NAME]',        // e.g., 'CashSecond Mobile Store'
    'phone_display'    => '[PHONE]',                // e.g., '+91 98765 43210'
    'phone_raw'        => '[PHONE_RAW]',            // e.g., '+919876543210'
    'whatsapp_number'  => '[WHATSAPP]',             // e.g., '919876543210'
    'email'            => '[EMAIL]',                // e.g., 'contact@cashsecond.in'
    'address'          => '[ADDRESS]',              // e.g., 'Shop No. 12, First Floor, Central Electronics Market'
    'city'             => '[CITY]',                 // e.g., 'Mumbai'
    'state'            => '[STATE]',                // e.g., 'Maharashtra'
    'pincode'          => '[PIN]',                  // e.g., '400001'
    'opening_hours'    => '[OPENING HOURS]',        // e.g., 'Mon - Sun: 10:30 AM to 9:00 PM'
    'google_maps_embed'=> '[GOOGLE MAPS EMBED URL]',
],
```

---

## 7. SEO & AEO Setup

1. In `config/config.php`, update `site_url`, `meta_title`, and `meta_description`.
2. In `sitemap.xml`, replace `http://localhost/cashsecond-landing-page/` with your live domain.
3. In `robots.txt`, verify the sitemap URL.

---

## 8. Analytics & Conversion Tracking Setup

Add your IDs in `config/config.php`:
```php
'tracking' => [
    'ga4_measurement_id'     => 'G-XXXXXXXXXX',     // GA4 Measurement ID
    'google_ads_id'          => 'AW-XXXXXXXXX',     // Google Ads ID
    'google_ads_conv_label'  => 'AbC-D_efGhIjKL',   // Google Ads Conversion Label
    'meta_pixel_id'          => '123456789012345',  // Meta Pixel ID
],
```

---

## 9. Security & Anti-Spam Measures

- **CSRF Protection:** Cryptographically secure token verification on all POST requests.
- **Honeypot Trap:** Invisible `website_hp` field traps bots.
- **Rate Limiting:** Session-based flood protection prevents automated rapid submissions.
- **Input Sanitization:** Strips harmful HTML and escapes characters.
- **Protected Logs:** `logs/` directory contains an auto-generated `.htaccess` with `Deny from all`.

---

## 10. Deployment on Hostinger / cPanel

1. In `config/config.php`, update `'site_url'` to your live domain (e.g. `https://cashsecond.in`).
2. Upload all project files to `public_html`.
3. Verify PHP version is **7.4, 8.0, 8.1, 8.2, or 8.3**.
4. Test a lead submission to ensure data posts to Google Sheets and logs safely.

---

## 11. Git Commands

```bash
git add .
git commit -m "feat: complete Google Ads safe second-hand mobile landing page"
git push origin main
```
