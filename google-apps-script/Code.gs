/**
 * ==============================================================================
 * CashSecond - Google Sheets Valuation Integration Script (Apps Script)
 * ==============================================================================
 * 
 * INSTRUCTIONS:
 * 1. Open Google Sheets (create a new blank spreadsheet, e.g. "CashSecond Phone Valuations").
 * 2. Click Extensions → Apps Script.
 * 3. Replace all code in Code.gs with this entire file.
 * 4. Run the function `setupSheets()` once to auto-create and format all 3 sheets and columns:
 *      - "Phone Valuations" (72 formatted columns with frozen header)
 *      - "Valuation Settings" (iPhone models catalog and base prices)
 *      - "Test Configuration" (Penalty and adjustment rules)
 * 5. Click Deploy → New deployment → Select type: Web app.
 * 6. Set:
 *      - Description: "CashSecond Valuation Webhook"
 *      - Execute as: "Me"
 *      - Who has access: "Anyone" (allows server-side PHP to POST leads)
 * 7. Copy the Web App URL and paste it into config/google_sheets.php or your .env file:
 *      GOOGLE_SHEETS_WEBHOOK_URL="https://script.google.com/macros/s/YOUR_DEPLOYMENT_ID/exec"
 */

// Global Column Headers Schema matching backend GoogleSheetsService.php
var COLUMN_HEADERS = [
  // LEAD INFORMATION
  "Submission Date",
  "Submission Time",
  "Lead ID",
  "Full Name",
  "WhatsApp Number",
  "Email",
  "Pickup Address",
  "Pincode",

  // DEVICE INFORMATION
  "Brand",
  "Model",
  "RAM",
  "Storage",
  "Battery Health",
  "Base / Max Value",
  "Final Estimated Value",

  // FUNCTIONAL TESTS
  "Phone Powers On",
  "Display Working",
  "Touchscreen Working",
  "Display Lines / Spots / Flickering",
  "Screen Cracked",
  "Screen Major Scratches",
  "Body Condition",
  "Phone Bent",
  "Body Damage",
  "Camera Glass Condition",
  "Missing Parts",
  "Rear Camera",
  "Front Camera",
  "Camera Flash",
  "Speaker",
  "Ear Receiver",
  "Microphone",
  "Power Button",
  "Volume Buttons",
  "Silent Switch",
  "Charging Port",
  "Charging Working",
  "Face ID / Touch ID",
  "WiFi",
  "Bluetooth",
  "Mobile Network / SIM",
  "GPS",
  "Liquid Damage",

  // PARTS / HISTORY
  "Original Display",
  "Major Component Replaced",
  "Replaced Component",
  "Warranty Status",
  "Original Bill",
  "Original Box",
  "Original Cable / Adapter",

  // TEST SUMMARY
  "Total Tests",
  "Passed Tests",
  "Failed Tests",
  "Pass Percentage",
  "Failed Test Names",

  // VALUATION BREAKDOWN
  "Model Base Price",
  "Storage Adjustment",
  "Battery Adjustment",
  "Display Adjustment",
  "Body Adjustment",
  "Functional Test Adjustment",
  "Liquid Damage Adjustment",
  "Parts Adjustment",
  "Warranty Adjustment",
  "Accessories Adjustment",
  "Total Adjustment",
  "Final Estimated Exchange Value",

  // SYSTEM INFORMATION
  "Valuation Status",
  "Submission Source",
  "Page URL",
  "User Agent",
  "Lead Timestamp"
];

/**
 * Handle incoming POST requests from PHP backend
 */
function doPost(e) {
  try {
    if (!e || !e.postData || !e.postData.contents) {
      return ContentService.createTextOutput(JSON.stringify({
        status: "error",
        message: "No payload received."
      })).setMimeType(ContentService.MimeType.JSON);
    }

    var data = JSON.parse(e.postData.contents);
    var row = data.row || data;

    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var sheet = ss.getSheetByName("Phone Valuations");

    if (!sheet) {
      sheet = setupPhoneValuationsSheet(ss);
    }

    // Build the ordered row array according to COLUMN_HEADERS
    var rowValues = [
      row.submission_date || new Date().toISOString().split("T")[0],
      row.submission_time || new Date().toLocaleTimeString("en-IN"),
      row.lead_id || "EXG-" + new Date().getTime(),
      row.full_name || "",
      row.whatsapp_number || "",
      row.email || "",
      row.pickup_address || "",
      row.pincode || "",

      row.brand || "Apple",
      row.model || "",
      row.ram || "",
      row.storage || "",
      row.battery_health || "",
      row.base_max_value || "",
      row.final_estimated_value || "",

      row.phone_powers_on || "YES",
      row.display_working || "YES",
      row.touchscreen_working || "YES",
      row.display_lines_spots || "NO",
      row.screen_cracked || "NO",
      row.screen_major_scratches || "NO",
      row.body_condition || "YES",
      row.phone_bent || "NO",
      row.body_damage || "NO",
      row.camera_glass_condition || "NO",
      row.missing_parts || "NO",
      row.rear_camera || "YES",
      row.front_camera || "YES",
      row.camera_flash || "YES",
      row.speaker || "YES",
      row.ear_receiver || "YES",
      row.microphone || "YES",
      row.power_button || "YES",
      row.volume_buttons || "YES",
      row.silent_switch || "YES",
      row.charging_port || "YES",
      row.charging_working || "YES",
      row.face_id_touch_id || "YES",
      row.wifi || "YES",
      row.bluetooth || "YES",
      row.mobile_network_sim || "YES",
      row.gps || "YES",
      row.liquid_damage || "NO",

      row.original_display || "YES",
      row.major_component_replaced || "NO",
      row.replaced_component || "None",
      row.warranty_status || "",
      row.original_bill || "YES",
      row.original_box || "YES",
      row.original_cable_adapter || "YES",

      row.total_tests || 32,
      row.passed_tests || 0,
      row.failed_tests || 0,
      row.pass_percentage || "100%",
      row.failed_test_names || "None",

      row.model_base_price || "",
      row.storage_adjustment || "",
      row.battery_adjustment || "",
      row.display_adjustment || "",
      row.body_adjustment || "",
      row.functional_adjustment || "",
      row.liquid_damage_adjustment || "",
      row.parts_adjustment || "",
      row.warranty_adjustment || "",
      row.accessories_adjustment || "",
      row.total_adjustment || "",
      row.final_exchange_value || row.final_estimated_value || "",

      row.valuation_status || "Verified",
      row.submission_source || "In-Popup Buyback Questionnaire",
      row.page_url || "",
      row.user_agent || "",
      row.lead_timestamp || new Date().toISOString()
    ];

    // Append the row to Google Sheets
    sheet.appendRow(rowValues);

    var lastRow = sheet.getLastRow();
    // Apply styling to new row
    var range = sheet.getRange(lastRow, 1, 1, rowValues.length);
    range.setFontFamily("Roboto");
    range.setFontSize(10);
    range.setVerticalAlignment("middle");

    // Highlight final value in bold green
    sheet.getRange(lastRow, 15).setFontWeight("bold").setFontColor("#0071E3");
    sheet.getRange(lastRow, 67).setFontWeight("bold").setFontColor("#1E8E3E");

    return ContentService.createTextOutput(JSON.stringify({
      status: "success",
      lead_id: rowValues[2],
      row_index: lastRow,
      message: "Valuation row inserted into Google Sheet successfully."
    })).setMimeType(ContentService.MimeType.JSON);

  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({
      status: "error",
      message: err.toString()
    })).setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * Health check GET endpoint
 */
function doGet(e) {
  return ContentService.createTextOutput(JSON.stringify({
    status: "ok",
    service: "CashSecond Google Sheets Valuation Webhook",
    columns_count: COLUMN_HEADERS.length,
    timestamp: new Date().toISOString()
  })).setMimeType(ContentService.MimeType.JSON);
}

/**
 * Run this function once from the Apps Script editor to auto-setup the 3 Sheets
 */
function setupSheets() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  setupPhoneValuationsSheet(ss);
  setupValuationSettingsSheet(ss);
  setupTestConfigSheet(ss);
  SpreadsheetApp.flush();
}

function setupPhoneValuationsSheet(ss) {
  var sheet = ss.getSheetByName("Phone Valuations");
  if (!sheet) {
    sheet = ss.insertSheet("Phone Valuations", 0);
  }

  // Clear and insert headers
  sheet.getRange(1, 1, 1, COLUMN_HEADERS.length).setValues([COLUMN_HEADERS]);
  
  // Format Header Row
  var headerRange = sheet.getRange(1, 1, 1, COLUMN_HEADERS.length);
  headerRange.setBackground("#0071E3");
  headerRange.setFontColor("#FFFFFF");
  headerRange.setFontWeight("bold");
  headerRange.setFontFamily("Roboto");
  headerRange.setFontSize(10);
  headerRange.setHorizontalAlignment("center");
  headerRange.setVerticalAlignment("middle");
  headerRange.setWrap(true);
  
  sheet.setFrozenRows(1);
  sheet.setRowHeight(1, 40);

  return sheet;
}

function setupValuationSettingsSheet(ss) {
  var sheet = ss.getSheetByName("Valuation Settings");
  if (!sheet) {
    sheet = ss.insertSheet("Valuation Settings");
  }

  var settingsHeaders = ["Model Name", "Max Value (₹)", "MRP (₹)", "RAM", "Biometric Type", "128GB Multiplier", "256GB Multiplier", "512GB Multiplier", "1TB Multiplier"];
  var sampleRows = [
    ["Apple iPhone 16 Pro Max", 72000, 144900, "8 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 16 Pro",     62000, 119900, "8 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 16",         48500, 79900,  "8 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 15 Pro Max", 54000, 134900, "8 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 15 Pro",     48000, 109900, "8 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 15",         38500, 69900,  "6 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 14 Pro Max", 46000, 129900, "6 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 14 Pro",     41000, 104900, "6 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 14",         32000, 59900,  "6 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 13 Pro Max", 44000, 119900, "6 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 13 Pro",     39500, 99900,  "6 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 13",         23220, 49900,  "4 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 12 Pro",     24500, 84900,  "6 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 12",         19500, 44900,  "4 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone 11",         14500, 39900,  "4 GB", "Face ID", 1.0, 1.12, 1.25, 1.38],
    ["Apple iPhone SE (2022)",  13500, 39900,  "4 GB", "Touch ID", 1.0, 1.12, 1.25, 1.38]
  ];

  sheet.clear();
  sheet.getRange(1, 1, 1, settingsHeaders.length).setValues([settingsHeaders]);
  sheet.getRange(2, 1, sampleRows.length, settingsHeaders.length).setValues(sampleRows);

  var hRange = sheet.getRange(1, 1, 1, settingsHeaders.length);
  hRange.setBackground("#34C759").setFontColor("#FFFFFF").setFontWeight("bold");
  sheet.setFrozenRows(1);
  return sheet;
}

function setupTestConfigSheet(ss) {
  var sheet = ss.getSheetByName("Test Configuration");
  if (!sheet) {
    sheet = ss.insertSheet("Test Configuration");
  }

  var testHeaders = ["Test / Condition Key", "Category", "Default Rule", "Penalty / Bonus (₹)", "Description"];
  var sampleTests = [
    ["power_on",           "Device",       "Yes = Pass, No = Penalty",          -9000, "Device boots normally to home/lock screen"],
    ["display_working",    "Device",       "Yes = Pass, No = Penalty",          -5000, "Screen turns on without blackout"],
    ["touch_screen",       "Device",       "Yes = Pass, No = Penalty",          -3200, "Touch response across all corners"],
    ["display_flaws",      "Physical",     "No = Pass, Yes = Penalty",          -3800, "Lines, black ink spots or flickering"],
    ["screen_cracked",     "Physical",     "No = Pass, Yes = Penalty",          -3500, "Cracked or shattered glass"],
    ["screen_scratches",   "Physical",     "No = Pass, Yes = Penalty",          -1200, "Heavy scratches on front glass"],
    ["body_dents",         "Physical",     "Yes = Pass, No = Penalty",          -1500, "Metal frame dents or abrasions"],
    ["body_bent",          "Physical",     "No = Pass, Yes = Penalty",          -2800, "Curved or bent chassis"],
    ["body_visible_damage","Physical",     "No = Pass, Yes = Penalty",          -1600, "Chipped back glass or damage"],
    ["camera_glass_crack", "Physical",     "No = Pass, Yes = Penalty",          -1800, "Back camera glass crack"],
    ["missing_parts",      "Physical",     "No = Pass, Yes = Penalty",          -1400, "SIM tray, screws, buttons missing"],
    ["battery_health",     "Device",       "85-89%=-800, 80-84%=-1800, <80%=-3600", -800, "Battery maximum capacity %"],
    ["rear_camera",        "Multimedia",   "Yes = Pass, No = Penalty",          -3000, "Photo & video focus/zoom"],
    ["front_camera",       "Multimedia",   "Yes = Pass, No = Penalty",          -2000, "Selfie camera & portrait clarity"],
    ["camera_flash",       "Multimedia",   "Yes = Pass, No = Penalty",          -600,  "LED flash and torch light"],
    ["loudspeaker",        "Multimedia",   "Yes = Pass, No = Penalty",          -1500, "Bottom sound playback clarity"],
    ["earpiece_receiver",  "Multimedia",   "Yes = Pass, No = Penalty",          -1200, "Top call receiver speaker"],
    ["microphone",         "Multimedia",   "Yes = Pass, No = Penalty",          -1400, "Voice call microphone clarity"],
    ["power_button",       "Device",       "Yes = Pass, No = Penalty",          -800,  "Side power button click"],
    ["volume_buttons",     "Device",       "Yes = Pass, No = Penalty",          -800,  "Volume Up/Down tactile click"],
    ["silent_switch",      "Device",       "Yes = Pass, No = Penalty",          -700,  "Silent ring switch / Action button"],
    ["charging_port",      "Device",       "Yes = Pass, No = Penalty",          -1800, "Port loose pin or connection"],
    ["charges_normally",   "Device",       "Yes = Pass, No = Penalty",          -2000, "Power draw and charging"],
    ["biometrics",         "Device",       "Yes = Pass, No = Penalty",          -3000, "Face ID / Touch ID authentication"],
    ["wifi_working",       "Connectivity", "Yes = Pass, No = Penalty",          -1500, "Wi-Fi network connection"],
    ["bluetooth_working",  "Connectivity", "Yes = Pass, No = Penalty",          -1200, "AirPods/Bluetooth device pairing"],
    ["cellular_sim",       "Connectivity", "Yes = Pass, No = Penalty",          -2400, "SIM card calling and 5G data"],
    ["gps_location",       "Connectivity", "Yes = Pass, No = Penalty",          -1000, "Location tracking on Maps"],
    ["liquid_damage",      "Physical",     "No = Pass, Yes = Penalty",          -4500, "Water or moisture damage"],
    ["display_original",   "Physical",     "Yes = Pass, No = Penalty",          -3500, "Original Apple screen"],
    ["parts_replaced",     "Physical",     "No = Pass, Yes = Penalty",          -1600, "Component replacement history"],
    ["warranty_status",    "Physical",     "Under 11M = +1500, None = 0",       1500,  "Valid Apple manufacturer warranty"],
    ["bill_invoice",       "Physical",     "Yes = +600, No = 0",                600,   "Original purchase retail bill"],
    ["has_box",            "Physical",     "Yes = +600, No = 0",                600,   "Original IMEI box"],
    ["has_cable",          "Physical",     "Yes = +300, No = 0",                300,   "Original charging cable"]
  ];

  sheet.clear();
  sheet.getRange(1, 1, 1, testHeaders.length).setValues([testHeaders]);
  sheet.getRange(2, 1, sampleTests.length, testHeaders.length).setValues(sampleTests);

  var hRange = sheet.getRange(1, 1, 1, testHeaders.length);
  hRange.setBackground("#FF9500").setFontColor("#FFFFFF").setFontWeight("bold");
  sheet.setFrozenRows(1);
  return sheet;
}
