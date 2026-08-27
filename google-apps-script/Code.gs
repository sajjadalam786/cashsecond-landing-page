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

  // If sheet is empty, add header row
  if (sheet.getLastRow() === 0) {
    sheet.getRange(1, 1, 1, COLUMN_HEADERS.length).setValues([COLUMN_HEADERS]);
    var headerRange = sheet.getRange(1, 1, 1, COLUMN_HEADERS.length);
    headerRange.setBackground("#0071E3").setFontColor("#FFFFFF").setFontWeight("bold").setFontFamily("Roboto");
    sheet.setFrozenRows(1);
  }

  return sheet;
}

function doPost(e) {
  try {
    var rawPayload = (e && e.postData && e.postData.contents) ? e.postData.contents : "{}";
    var data = {};
    try {
      data = JSON.parse(rawPayload);
    } catch (parseErr) {
      data = (e && e.parameter) ? e.parameter : {};
    }

    var row = data.row || data;
    var sheet = getOrCreateSheet();

    var rowValues = [
      row.submission_date || new Date().toISOString().split("T")[0],
      row.submission_time || new Date().toLocaleTimeString("en-IN"),
      row.lead_id || "EXG-" + new Date().getTime(),
      row.full_name || "",
      row.whatsapp_number || "",
      row.email || "",
      row.pickup_address || "Mumbai (Doorstep Pickup)",
      row.pincode || "400021",
      row.pickup_date || "Today",
      row.pickup_slot || "Express (Within 6 Hours)",
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
      row.lead_timestamp || new Date().toISOString()
    ];

    // Sanitize all values so Google Sheets doesn't treat '+₹...' or '=...' as a broken formula
    for (var i = 0; i < rowValues.length; i++) {
      var val = rowValues[i];
      if (typeof val === "string") {
        var trimmed = val.trim();
        if (trimmed.charAt(0) === "+" || trimmed.charAt(0) === "=") {
          rowValues[i] = "'" + trimmed;
        }
      }
    }

    sheet.appendRow(rowValues);
    var lastRow = sheet.getLastRow();

    return ContentService.createTextOutput(JSON.stringify({
      status: "success",
      lead_id: rowValues[2],
      row_index: lastRow,
      message: "Lead inserted successfully."
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
  if (e && e.parameter && (e.parameter.lead_id || e.parameter.full_name)) {
    return doPost(e);
  }
  return ContentService.createTextOutput(JSON.stringify({
    status: "ok",
    service: "CashSecond Webhook Live",
    timestamp: new Date().toISOString()
  })).setMimeType(ContentService.MimeType.JSON);
}

function setupSheets() {
  getOrCreateSheet();
}
