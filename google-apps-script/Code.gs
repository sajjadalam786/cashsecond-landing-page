/**
 * ==============================================================================
 * CashSecond - Google Sheets Valuation Integration Script (Apps Script)
 * ==============================================================================
 * Target Spreadsheet: 1LpQdgV5PtA2-nVpzjVZPGAmVdaVBzVM8g1hCWBaApCo
 */

var COLUMN_HEADERS = [
  "Submission Date", "Submission Time", "Lead ID", "Full Name", "WhatsApp Number", "Email",
  "Pickup Address", "Pincode", "Pickup Date", "Pickup Slot", "Feedback Rating", "Feedback Comment",
  "Brand", "Model", "RAM", "Storage", "Battery Health", "Base / Max Value", "Final Estimated Value",
  "Phone Powers On", "Display Working", "Touchscreen Working", "Display Lines / Spots / Flickering",
  "Screen Cracked", "Screen Major Scratches", "Body Condition", "Phone Bent", "Body Damage",
  "Camera Glass Condition", "Missing Parts", "Rear Camera", "Front Camera", "Camera Flash",
  "Speaker", "Ear Receiver", "Microphone", "Power Button", "Volume Buttons", "Silent Switch",
  "Charging Port", "Charging Working", "Face ID / Touch ID", "WiFi", "Bluetooth",
  "Mobile Network / SIM", "GPS", "Liquid Damage",
  "Original Display", "Major Component Replaced", "Replaced Component", "Warranty Status",
  "Original Bill", "Original Box", "Original Cable / Adapter",
  "Total Tests", "Passed Tests", "Failed Tests", "Pass Percentage", "Failed Test Names",
  "Model Base Price", "Storage Adjustment", "Battery Adjustment", "Display Adjustment",
  "Body Adjustment", "Functional Test Adjustment", "Liquid Damage Adjustment", "Parts Adjustment",
  "Warranty Adjustment", "Accessories Adjustment", "Total Adjustment", "Final Estimated Exchange Value",
  "Valuation Status", "Submission Source", "Page URL", "User Agent", "Lead Timestamp"
];

function getOrCreateSheet() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  if (!ss) {
    ss = SpreadsheetApp.openById("1LpQdgV5PtA2-nVpzjVZPGAmVdaVBzVM8g1hCWBaApCo");
  }
  var sheet = ss.getSheetByName("Phone Valuations");
  if (!sheet) {
    var allSheets = ss.getSheets();
    if (allSheets.length > 0 && allSheets[0].getLastRow() === 0) {
      sheet = allSheets[0];
      sheet.setName("Phone Valuations");
    } else {
      sheet = ss.insertSheet("Phone Valuations", 0);
    }
  }
  if (sheet.getLastRow() === 0) {
    sheet.getRange(1, 1, 1, COLUMN_HEADERS.length).setValues([COLUMN_HEADERS]);
    sheet.getRange(1, 1, 1, COLUMN_HEADERS.length)
      .setBackground("#0071E3").setFontColor("#FFFFFF").setFontWeight("bold").setFontFamily("Roboto");
    sheet.setFrozenRows(1);
  }
  return sheet;
}

/**
 * Build a map of { "Column Header Name": columnIndex_1based } from the sheet's first row.
 * This makes updates immune to column reordering.
 */
function buildColumnMap(sheet) {
  var headerRow = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];
  var map = {};
  for (var i = 0; i < headerRow.length; i++) {
    var h = String(headerRow[i]).trim();
    if (h) map[h] = i + 1; // 1-based column index
  }
  return map;
}

function doPost(e) {
  try {
    var rawPayload = (e && e.postData && e.postData.contents) ? e.postData.contents : "{}";
    var data = {};
    try { data = JSON.parse(rawPayload); } catch (err) { data = (e && e.parameter) ? e.parameter : {}; }

    var sheet = getOrCreateSheet();

    // ── FEEDBACK / PICKUP UPDATE (from Thank You page) ──────────────────────
    if (data.action === "update_feedback" || data.action === "feedback") {
      var leadId  = String(data.lead_id || data.ref_id || "").trim();
      if (!leadId) {
        return ContentService.createTextOutput(JSON.stringify({ status: "error", message: "No lead_id provided." }))
          .setMimeType(ContentService.MimeType.JSON);
      }

      // Determine which values are actually non-empty (only update those)
      var updates = {};
      if (String(data.pickup_address || "").trim()) updates["Pickup Address"]   = String(data.pickup_address).trim();
      if (String(data.pincode        || "").trim()) updates["Pincode"]           = String(data.pincode).trim();
      if (String(data.pickup_date    || "").trim()) updates["Pickup Date"]       = String(data.pickup_date).trim();
      if (String(data.pickup_slot    || "").trim()) updates["Pickup Slot"]       = String(data.pickup_slot).trim();
      if (String(data.feedback_rating|| "").trim()) updates["Feedback Rating"]   = String(data.feedback_rating).trim();
      if (String(data.feedback_comment || "").trim()) updates["Feedback Comment"] = String(data.feedback_comment).trim();
      // Always stamp status when this action fires
      updates["Valuation Status"] = "Pickup Scheduled & Verified";

      // Build dynamic column map from actual sheet headers
      var colMap = buildColumnMap(sheet);

      // Search for the matching lead row
      var lastRow   = sheet.getLastRow();
      var leadColIdx = colMap["Lead ID"]; // column index (1-based)
      if (!leadColIdx) {
        return ContentService.createTextOutput(JSON.stringify({ status: "error", message: "Lead ID column not found in sheet." }))
          .setMimeType(ContentService.MimeType.JSON);
      }

      var leadColValues = sheet.getRange(2, leadColIdx, lastRow - 1, 1).getValues();
      var targetRow = -1;
      for (var r = 0; r < leadColValues.length; r++) {
        if (String(leadColValues[r][0]).trim() === leadId) {
          targetRow = r + 2; // 1-based, +1 for header, +1 for offset
          break;
        }
      }

      if (targetRow === -1) {
        return ContentService.createTextOutput(JSON.stringify({ status: "success", updated: false, message: "Lead ID not yet in sheet." }))
          .setMimeType(ContentService.MimeType.JSON);
      }

      // Write ONLY the provided fields using dynamic column positions
      var updatedFields = [];
      for (var header in updates) {
        if (colMap[header]) {
          sheet.getRange(targetRow, colMap[header]).setValue(updates[header]);
          updatedFields.push(header);
        }
      }

      return ContentService.createTextOutput(JSON.stringify({
        status: "success",
        lead_id: leadId,
        updated: true,
        fields_updated: updatedFields,
        message: "Fields updated without touching other columns."
      })).setMimeType(ContentService.MimeType.JSON);
    }

    // ── NEW VALUATION ROW INSERTION ──────────────────────────────────────────
    var row = data.row || data;

    var rowValues = [
      row.submission_date || Utilities.formatDate(new Date(), "Asia/Kolkata", "dd/MM/yyyy"),
      row.submission_time || Utilities.formatDate(new Date(), "Asia/Kolkata", "hh:mm:ss a"),
      row.lead_id || ("EXG-" + new Date().getTime()),
      row.full_name || "",
      row.whatsapp_number || "",
      row.email || "",
      row.pickup_address || "",
      row.pincode || "",
      row.pickup_date || "",
      row.pickup_slot || "",
      row.feedback_rating || "",
      row.feedback_comment || "",

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

      row.valuation_status || "Verified Online Quote",
      row.submission_source || "In-Popup Buyback Questionnaire",
      row.page_url || "",
      row.user_agent || "",
      row.lead_timestamp || Utilities.formatDate(new Date(), "Asia/Kolkata", "dd/MM/yyyy hh:mm:ss a")
    ];

    // Sanitize formula triggers
    for (var i = 0; i < rowValues.length; i++) {
      if (typeof rowValues[i] === "string") {
        var t = rowValues[i].trim();
        if (t.charAt(0) === "+" || t.charAt(0) === "=") rowValues[i] = "'" + t;
      }
    }

    sheet.appendRow(rowValues);

    return ContentService.createTextOutput(JSON.stringify({
      status: "success",
      lead_id: rowValues[2],
      row_index: sheet.getLastRow(),
      message: "Lead row inserted successfully."
    })).setMimeType(ContentService.MimeType.JSON);

  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({
      status: "error",
      error_details: err.toString(),
      stack: err.stack || ""
    })).setMimeType(ContentService.MimeType.JSON);
  }
}

function doGet(e) {
  if (e && e.parameter && e.parameter.lead_id) return doPost(e);
  return ContentService.createTextOutput(JSON.stringify({ status: "ok", service: "CashSecond Webhook Live" }))
    .setMimeType(ContentService.MimeType.JSON);
}

function setupSheets() { getOrCreateSheet(); }
