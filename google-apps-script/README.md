# Google Sheets Integration Guide for CashSecond Valuations

This guide explains how to connect the CashSecond Phone Buyback valuation flow to your Google Sheet so that every completed valuation creates **ONE detailed row with all 72 columns** in your Google Sheet.

---

## Step 1: Create a Google Sheet

1. Open [Google Sheets](https://sheets.new) in your browser.
2. Name the spreadsheet: **`CashSecond Phone Valuations`**.

---

## Step 2: Paste the Apps Script Code

1. In your Google Sheet, click **Extensions** → **Apps Script**.
2. Delete any code in the editor (`Code.gs`) and replace it with the entire contents of [`google-apps-script/Code.gs`](file:///c:/xampp/htdocs/cashsecond-landing-page/google-apps-script/Code.gs).
3. Click the **Save** icon (disk icon).

---

## Step 3: Run the One-Time Setup

1. In the toolbar dropdown (next to "Debug"), select the function **`setupSheets`**.
2. Click **Run**.
3. When prompted, grant the necessary Google account permissions.
4. This will automatically create and style the 3 tabs:
   - **`Phone Valuations`**: 72 columns formatted with blue headers and frozen top row.
   - **`Valuation Settings`**: Base prices and multipliers for all iPhone models.
   - **`Test Configuration`**: Adjustment rules and penalties for each question.

---

## Step 4: Deploy as a Web App

1. In the top right corner of Apps Script, click **Deploy** → **New deployment**.
2. Click the gear icon (Select type) → choose **Web app**.
3. Configure the fields:
   - **Description**: `CashSecond Valuation Webhook`
   - **Execute as**: `Me (your Google account)`
   - **Who has access**: `Anyone` *(Note: Allows the PHP backend to securely POST valuation rows)*
4. Click **Deploy**.
5. Copy the **Web App URL** (it looks like: `https://script.google.com/macros/s/AKfycb.../exec`).

---

## Step 5: Configure the Webhook URL in CashSecond

Paste your Web App URL into either:
- **`config/google_sheets.php`** (under `'webhook_url'`), OR
- Set it as an environment variable in `.env`:
  ```env
  GOOGLE_SHEETS_WEBHOOK_URL="https://script.google.com/macros/s/AKfycb.../exec"
  ```

---

## Security & Architecture

- **Zero Secrets in Frontend**: Google credentials and webhook URLs are kept exclusively on the server backend (`forms/buyback-questionnaire.php` and `includes/GoogleSheetsService.php`).
- **Fail-Safe Logging**: Every single valuation is logged to `logs/google_sheets_payloads.jsonl` immediately upon submission.
- **Offline / Outage Queue**: If Google Sheets is temporarily unavailable, submissions are safely stored in `logs/pending_sheets_queue.jsonl` for zero data loss.
