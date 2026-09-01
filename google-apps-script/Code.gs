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

    var leadIdStr  = String(rowValues[2] || "").trim();
    var scriptCache = CacheService.getScriptCache();
    var isDuplicate = leadIdStr ? scriptCache.get("lead_sent_" + leadIdStr) : null;

    if (!isDuplicate) {
      if (leadIdStr) {
        scriptCache.put("lead_sent_" + leadIdStr, "1", 300); // 5-minute deduplication window
      }
      sheet.appendRow(rowValues);

      // ── SEND SINGLE NOTIFICATION EMAIL TO STORE ADMINS FROM CASHSECOND ────
      try {
        var adminEmail = "wholesalehouse2016@gmail.com, Cashsecondoffice@gmail.com";
        var leadName   = String(row.full_name || "Customer").trim();
        var leadPhone  = String(row.whatsapp_number || "").trim();
        var cleanPhone = leadPhone.replace(/[^0-9]/g, "");
        var devModel   = String(row.model || "iPhone").trim();
        var devStorage = String(row.storage || "").trim();
        var devValRaw  = String(row.final_estimated_value || "₹0").trim();
        var devBaseRaw = String(row.base_max_value || "").trim();
        var devVal     = devValRaw.indexOf("₹") === -1 ? ("₹" + devValRaw) : devValRaw;
        var devBase    = devBaseRaw.indexOf("₹") === -1 ? ("₹" + devBaseRaw) : devBaseRaw;
        var timeStr    = String(rowValues[0] + " " + rowValues[1]);

      var waLink = "https://wa.me/91" + cleanPhone + "?text=" + encodeURIComponent("Hi " + leadName + ", this is CashSecond regarding your iPhone valuation of " + devVal + " for " + devModel + " (" + devStorage + "). Ref: " + leadIdStr);

      var emailSubject = "📱 New Lead: " + devModel + " (" + devStorage + ") — " + devVal + " | " + leadName + " [" + leadIdStr + "]";

      var emailBody = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    + "📱 ONSITE INSPECTION LEAD | CashSecond\n"
                    + "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    + "👤 CUSTOMER DETAILS:\n"
                    + "• Name:     " + leadName + "\n"
                    + "• Phone:    " + leadPhone + " (Click to Call: tel:" + cleanPhone + ")\n"
                    + "• WhatsApp: " + waLink + "\n"
                    + "• Email:    " + (row.email || "Not provided") + "\n"
                    + "• Lead ID:  " + leadIdStr + "\n"
                    + "• Time:     " + timeStr + "\n\n"
                    + "💰 VALUATION SUMMARY:\n"
                    + "• Device:          " + devModel + " (" + devStorage + ")\n"
                    + "• Final Valuation: " + devVal + "\n"
                    + "• Base Price:      " + devBase + "\n"
                    + "• Valuation Status: " + (row.valuation_status || "Verified Online Quote") + "\n\n"
                    + "🖥️ DISPLAY & SCREEN INSPECTION:\n"
                    + "• Screen Display:       " + (row.display_working === "YES" ? "✅ Working (Clear & Bright)" : "❌ Display Fault / Blackout") + "\n"
                    + "• Touchscreen:          " + (row.touchscreen_working === "YES" ? "✅ Responsive & Smooth" : "❌ Touch Not Working / Ghost Touch") + "\n"
                    + "• Front Screen Glass:   " + (row.screen_cracked === "NO" ? "✅ Intact (No Cracks)" : "❌ Front Glass Cracked") + "\n"
                    + "• Screen Scratches:     " + (row.screen_major_scratches ? (row.screen_major_scratches.indexOf("Minor") !== -1 ? "⚠️ 1-2 Minor Scratches" : (row.screen_major_scratches.indexOf("Heavy") !== -1 ? "❌ Heavy Scratches" : (row.screen_major_scratches.indexOf("3-4") !== -1 ? "⚠️ 3-4 Scratches" : "✅ Scratch-Free Screen"))) : "✅ Scratch-Free Screen") + "\n"
                    + "• Lines / Spots / Flaws:" + (row.display_lines_spots === "NO" ? "✅ Clean (No Lines or Ink Spots)" : "❌ Lines / Ink Spots / Flickering Detected") + "\n"
                    + "• Original Display:     " + (row.original_display === "YES" ? "✅ Original Apple Screen" : "⚠️ Replaced Screen") + "\n\n"
                    + "📱 BODY & FRAME INSPECTION:\n"
                    + "• Frame / Body Marks:   " + (row.body_condition ? (row.body_condition.indexOf("Clean") !== -1 ? "✅ Clean Metal Frame (Like New)" : (row.body_condition.indexOf("Heavy") !== -1 ? "❌ Heavy Dents/Marks" : "⚠️ Minor Dents / Scratches")) : "✅ Clean Metal Frame") + "\n"
                    + "• Chassis / Frame Bent: " + (row.phone_bent === "NO" ? "✅ Flat & Straight" : "❌ Body Curved / Bent") + "\n"
                    + "• Back Glass:           " + (row.body_damage === "NO" ? "✅ Back Glass Intact" : "❌ Back Glass Broken / Cracked") + "\n"
                    + "• Camera Glass:         " + (row.camera_glass_condition === "NO" ? "✅ Camera Glass Clear & Intact" : "❌ Camera Glass Cracked") + "\n"
                    + "• Missing Screws/Parts: " + (row.missing_parts === "NO" ? "✅ All Parts & Screws Intact" : "❌ Missing Parts") + "\n\n"
                    + "⚙️ HARDWARE & FUNCTIONAL TESTS:\n"
                    + "• Front Selfie Camera:  " + (row.front_camera === "YES" ? "✅ Working" : "❌ Faulty") + "\n"
                    + "• Rear Main Camera:     " + (row.rear_camera === "YES" ? "✅ Working" : "❌ Faulty") + "\n"
                    + "• Camera Flash / Torch: " + (row.camera_flash === "YES" ? "✅ Working" : "❌ Faulty") + "\n"
                    + "• Face ID / Touch ID:   " + (row.face_id_touch_id === "YES" ? "✅ Biometrics Working OK" : "❌ Face ID / Touch ID Broken") + "\n"
                    + "• Charging Port:        " + (row.charging_port === "YES" ? "✅ Port Working & Fast Charge OK" : "❌ Charging Port Issue") + "\n"
                    + "• Loudspeaker & Sound:  " + (row.speaker === "YES" ? "✅ Loudspeaker Clear" : "❌ Speaker Distorted / Muffled") + "\n"
                    + "• Earpiece & Receiver:  " + (row.ear_receiver === "YES" ? "✅ Earpiece Clear" : "❌ Call Audio Issue") + "\n"
                    + "• Microphone:           " + (row.microphone === "YES" ? "✅ Mic Working Clear" : "❌ Mic Faulty") + "\n"
                    + "• Physical Buttons:     " + (row.power_button === "YES" && row.volume_buttons === "YES" ? "✅ Power & Volume Buttons OK" : "❌ Button Issue") + "\n"
                    + "• Silent / Mute Switch: " + (row.silent_switch === "YES" ? "✅ Working" : "❌ Faulty") + "\n"
                    + "• Wi-Fi & Bluetooth:    " + (row.wifi === "YES" && row.bluetooth === "YES" ? "✅ Wireless Connectivity OK" : "❌ Wireless Issue") + "\n"
                    + "• Mobile SIM / Network: " + (row.mobile_network_sim === "YES" ? "✅ SIM & Calling Network OK" : "❌ SIM / Network Issue") + "\n"
                    + "• GPS Location:         " + (row.gps === "YES" ? "✅ GPS Working" : "❌ GPS Faulty") + "\n\n"
                    + "🔋 BATTERY, HISTORY & INCLUSIONS:\n"
                    + "• Battery Health:       " + (row.battery_health ? (row.battery_health.indexOf("Above") !== -1 ? "🟢 " + row.battery_health : (row.battery_health.indexOf("Below") !== -1 ? "🔴 " + row.battery_health : "⚠️ " + row.battery_health)) : "🟢 Above 80% (Healthy)") + "\n"
                    + "• Liquid / Water Damage:" + (row.liquid_damage === "NO" ? "✅ Safe (No Liquid Damage)" : "❌ Liquid Damaged") + "\n"
                    + "• Major Repair History: " + (row.major_component_replaced === "NO" ? "✅ No Major Replacements" : "⚠️ Replaced: " + (row.replaced_component || "Component")) + "\n"
                    + "• Warranty Status:      " + (row.warranty_status || "6 to 11 Months (Under Warranty)") + "\n"
                    + "• 📦 Original Box:      " + (row.original_box === "YES" ? "✅ YES (Available)" : "❌ NO (Missing)") + "\n"
                    + "• ⚡ Original Charger:  " + (row.original_cable_adapter === "YES" ? "✅ YES (Available)" : "❌ NO (Missing)") + "\n"
                    + "• 🧾 Purchase Bill:     " + (row.original_bill === "YES" ? "✅ YES (Available)" : "❌ NO (Missing)") + "\n\n"
                    + "📋 INSPECTOR SUMMARY:\n"
                    + "• Reported Issues:      " + (row.failed_test_names && row.failed_test_names !== "None" ? "⚠️ " + row.failed_test_names : "✅ Clean Device (All Tests Passed)") + "\n"
                    + "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

      /* ============================================================
         CALCULATION VERIFICATION AUDIT (CAN BE COMMENTED OUT LATER)
         ============================================================ */
      if (row.calculation_audit) {
        emailBody += "\n" + String(row.calculation_audit).trim() + "\n";
      }
      /* ============================================================ */

      var tagPass = function(t) { return "<strong style='color:#1E8E3E;background:#E8F8EE;padding:2px 8px;border-radius:6px;font-size:12.5px;display:inline-block;white-space:nowrap;'>" + t + "</strong>"; };
      var tagFail = function(t) { return "<strong style='color:#D70015;background:#FFF0F0;padding:2px 8px;border-radius:6px;font-size:12.5px;display:inline-block;white-space:nowrap;'>" + t + "</strong>"; };
      var tagWarn = function(t) { return "<strong style='color:#E37400;background:#FFF6E6;padding:2px 8px;border-radius:6px;font-size:12.5px;display:inline-block;white-space:nowrap;'>" + t + "</strong>"; };

      var makeRow = function(label, tag) {
        return "<tr>"
             + "<td style='padding:7px 8px 7px 0;color:#555558;font-size:13px;line-height:1.4;border-bottom:1px solid #F2F2F5;vertical-align:middle;'>" + label + "</td>"
             + "<td align='right' style='padding:7px 0 7px 8px;border-bottom:1px solid #F2F2F5;vertical-align:middle;text-align:right;white-space:nowrap;'>" + tag + "</td>"
             + "</tr>";
      };

      var htmlBody = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'>"
                   + "<style type='text/css'>"
                   + "body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }"
                   + "table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }"
                   + "body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #F2F2F7; }"
                   + "@media only screen and (max-width: 620px) {"
                   + "  .main-table { width: 100% !important; max-width: 100% !important; }"
                   + "  .content-box { padding: 16px 12px !important; border-radius: 8px !important; }"
                   + "  .btn-wrap { display: block !important; width: 100% !important; margin-bottom: 8px !important; }"
                   + "  .btn-spacer { display: none !important; }"
                   + "  .device-title { font-size: 19px !important; }"
                   + "  .price-title { font-size: 23px !important; }"
                   + "  .section-card { padding: 12px 10px !important; margin-bottom: 12px !important; }"
                   + "}"
                   + "</style></head>"
                   + "<body style='margin:0;padding:0;background-color:#F2F2F7;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;'>"
                   + "<table border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color:#F2F2F7;padding:16px 8px;'>"
                   + "<tr><td align='center'>"
                   + "<table class='main-table' border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width:600px;width:100%;background-color:#FFFFFF;border-radius:14px;border:1px solid #E5E5EA;box-shadow:0 3px 12px rgba(0,0,0,0.05);overflow:hidden;'>"
                   + "<tr><td class='content-box' style='padding:22px 20px;'>"

                   + "<!-- Header Badge & Model Title -->"
                   + "<div style='border-bottom:2px solid #0071E3;padding-bottom:14px;margin-bottom:16px;'>"
                   + "<table border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:8px;'>"
                   + "<tr><td align='left'><span style='background:#0071E3;color:#FFFFFF;font-size:11px;font-weight:bold;padding:4px 9px;border-radius:20px;text-transform:uppercase;letter-spacing:0.04em;'>Onsite Valuation Lead</span></td>"
                   + "<td align='right'><span style='font-size:11.5px;color:#86868B;font-weight:600;'>Ref: " + leadIdStr + "</span></td></tr>"
                   + "</table>"
                   + "<h2 class='device-title' style='margin:0;font-size:21px;color:#111111;line-height:1.3;'>" + devModel + " <span style='color:#6E6E73;font-size:15px;font-weight:normal;'>(" + devStorage + ")</span></h2>"
                   + "<div class='price-title' style='font-size:25px;font-weight:800;color:#1E8E3E;margin-top:6px;'>" + devVal + " <span style='font-size:13.5px;font-weight:500;color:#86868B;margin-left:4px;'>Base: " + devBase + "</span></div>"
                   + "</div>"

                   + "<!-- Customer Information Card -->"
                   + "<div class='section-card' style='background:#F5F5F7;border-radius:10px;padding:12px 14px;margin-bottom:16px;'>"
                   + "<div style='font-size:12px;font-weight:700;color:#0071E3;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;'>👤 Customer Contact Details</div>"
                   + "<table border='0' cellpadding='0' cellspacing='0' width='100%' style='font-size:13px;line-height:1.7;'>"
                   + "<tr><td style='color:#6E6E73;width:34%;padding:2px 0;'>Full Name:</td><td style='color:#111111;font-weight:700;padding:2px 0;'>" + leadName + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;padding:2px 0;'>Phone:</td><td style='padding:2px 0;'><a href='tel:" + cleanPhone + "' style='color:#0071E3;font-weight:700;text-decoration:none;'>+91 " + cleanPhone + "</a></td></tr>"
                   + "<tr><td style='color:#6E6E73;padding:2px 0;'>Email:</td><td style='color:#111111;padding:2px 0;'>" + (row.email || "Not provided") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;padding:2px 0;'>Date / Time:</td><td style='color:#111111;padding:2px 0;'>" + timeStr + "</td></tr>"
                   + "</table>"
                   + "</div>"

                   + "<!-- Responsive CTA Action Buttons -->"
                   + "<table border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:18px;'>"
                   + "<tr>"
                   + "<td class='btn-wrap' width='49%' align='center' style='vertical-align:top;'><a href='" + waLink + "' target='_blank' style='display:block;width:100%;box-sizing:border-box;background:#25D366;color:#FFFFFF;text-align:center;padding:11px 8px;border-radius:8px;font-weight:bold;text-decoration:none;font-size:13.5px;'>💬 WhatsApp</a></td>"
                   + "<td class='btn-spacer' width='2%'></td>"
                   + "<td class='btn-wrap' width='49%' align='center' style='vertical-align:top;'><a href='tel:" + cleanPhone + "' style='display:block;width:100%;box-sizing:border-box;background:#0071E3;color:#FFFFFF;text-align:center;padding:11px 8px;border-radius:8px;font-weight:bold;text-decoration:none;font-size:13.5px;'>📞 Call Client</a></td>"
                   + "</tr>"
                   + "</table>"

                   + "<!-- Section 1: Screen & Display Check -->"
                   + "<div class='section-card' style='border:1px solid #E5E5EA;border-radius:10px;padding:14px;margin-bottom:14px;'>"
                   + "<div style='font-size:12.5px;font-weight:700;color:#111111;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;'>🖥️ Screen &amp; Display Check</div>"
                   + "<table border='0' cellpadding='0' cellspacing='0' width='100%'>"
                   + makeRow('Display Power / Blackout', (row.display_working === "YES" ? tagPass("✅ Working (Clear)") : tagFail("❌ Fault / Blackout")))
                   + makeRow('Touchscreen Response', (row.touchscreen_working === "YES" ? tagPass("✅ Responsive") : tagFail("❌ Touch Issue")))
                   + makeRow('Front Screen Glass', (row.screen_cracked === "NO" ? tagPass("✅ Intact (No Cracks)") : tagFail("❌ Glass Cracked")))
                   + makeRow('Screen Scratches', (row.screen_major_scratches && (row.screen_major_scratches.indexOf("Heavy") !== -1 || row.screen_major_scratches.indexOf("Scratches") !== -1) ? tagFail(row.screen_major_scratches) : tagPass("✅ Scratch-Free")))
                   + makeRow('Lines / Dots / Ink Spots', (row.display_lines_spots === "NO" ? tagPass("✅ Clean Display") : tagFail("❌ Lines / Dots Present")))
                   + makeRow('Display Originality', (row.original_display === "YES" ? tagPass("✅ Original Apple Screen") : tagWarn("⚠️ Replaced Screen")))
                   + "</table>"
                   + "</div>"

                   + "<!-- Section 2: Body & Frame Condition -->"
                   + "<div class='section-card' style='border:1px solid #E5E5EA;border-radius:10px;padding:14px;margin-bottom:14px;'>"
                   + "<div style='font-size:12.5px;font-weight:700;color:#111111;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;'>📱 Body &amp; Frame Condition</div>"
                   + "<table border='0' cellpadding='0' cellspacing='0' width='100%'>"
                   + makeRow('Frame Scratches / Marks', (row.body_condition && row.body_condition.indexOf("Clean") !== -1 ? tagPass("✅ Clean Metal Frame") : tagFail("❌ Has Dents / Body Scratches")))
                   + makeRow('Chassis / Bent Frame', (row.phone_bent === "NO" ? tagPass("✅ Flat &amp; Straight") : tagFail("❌ Frame Bent / Curved")))
                   + makeRow('Back Glass Condition', (row.body_damage === "NO" ? tagPass("✅ Intact") : tagFail("❌ Back Glass Broken")))
                   + makeRow('Camera Lens Glass', (row.camera_glass_condition === "NO" ? tagPass("✅ Clear &amp; Intact") : tagFail("❌ Glass Broken")))
                   + makeRow('Parts / Screws', (row.missing_parts === "NO" ? tagPass("✅ All Intact") : tagFail("❌ Missing Parts")))
                   + "</table>"
                   + "</div>"

                   + "<!-- Section 3: Hardware & Component Tests -->"
                   + "<div class='section-card' style='border:1px solid #E5E5EA;border-radius:10px;padding:14px;margin-bottom:14px;'>"
                   + "<div style='font-size:12.5px;font-weight:700;color:#111111;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;'>⚙️ Hardware &amp; Component Tests</div>"
                   + "<table border='0' cellpadding='0' cellspacing='0' width='100%'>"
                   + makeRow('Front Selfie Camera', (row.front_camera === "YES" ? tagPass("✅ Working") : tagFail("❌ Faulty")))
                   + makeRow('Rear Main Camera', (row.rear_camera === "YES" ? tagPass("✅ Working") : tagFail("❌ Faulty")))
                   + makeRow('Camera Flash', (row.camera_flash === "YES" ? tagPass("✅ Working") : tagFail("❌ Faulty")))
                   + makeRow('Face ID / Fingerprint', (row.face_id_touch_id === "YES" ? tagPass("✅ Working") : tagFail("❌ Broken")))
                   + makeRow('Charging Port', (row.charging_port === "YES" ? tagPass("✅ Fast Charge OK") : tagFail("❌ Port Issue")))
                   + makeRow('Loudspeaker', (row.speaker === "YES" ? tagPass("✅ Loudspeaker OK") : tagFail("❌ Audio Issue")))
                   + makeRow('Earpiece / Receiver', (row.ear_receiver === "YES" ? tagPass("✅ Clear Call Audio") : tagFail("❌ Receiver Issue")))
                   + makeRow('Microphone &amp; Audio IC', (row.microphone === "YES" ? tagPass("✅ Working") : tagFail("❌ Faulty")))
                   + makeRow('Power Button', (row.power_button === "YES" ? tagPass("✅ Responsive") : tagFail("❌ Button Issue")))
                   + makeRow('Volume Buttons', (row.volume_buttons === "YES" ? tagPass("✅ Responsive") : tagFail("❌ Button Issue")))
                   + makeRow('Silent / Mute Switch', (row.silent_switch === "YES" ? tagPass("✅ Working") : tagFail("❌ Faulty")))
                   + makeRow('Wi-Fi &amp; Bluetooth', (row.wifi === "YES" && row.bluetooth === "YES" ? tagPass("✅ Connected OK") : tagFail("❌ Wireless Issue")))
                   + makeRow('Cellular SIM / Network', (row.mobile_network_sim === "YES" ? tagPass("✅ Signal OK") : tagFail("❌ Network Issue")))
                   + makeRow('GPS Location', (row.gps === "YES" ? tagPass("✅ Working") : tagFail("❌ Faulty")))
                   + "</table>"
                   + "</div>"

                   + "<!-- Section 4: Battery, History & Inclusions -->"
                   + "<div class='section-card' style='border:1px solid #E5E5EA;border-radius:10px;padding:14px;margin-bottom:14px;'>"
                   + "<div style='font-size:12.5px;font-weight:700;color:#111111;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;'>🔋 Battery, History &amp; Inclusions</div>"
                   + "<table border='0' cellpadding='0' cellspacing='0' width='100%'>"
                   + makeRow('Battery Health', (row.battery_health && row.battery_health.indexOf("Above") !== -1 ? tagPass("🟢 " + row.battery_health) : tagFail("🔴 " + (row.battery_health || "Below 80%"))))
                   + makeRow('Liquid / Water Damage', (row.liquid_damage === "NO" ? tagPass("✅ Safe (No Water Damage)") : tagFail("❌ Liquid Damaged")))
                   + makeRow('Component Repairs', (row.major_component_replaced === "NO" ? tagPass("✅ None (Original)") : tagWarn("⚠️ Replaced: " + (row.replaced_component || "Parts"))))
                   + makeRow('Purchase Timeline', "<strong>" + (row.warranty_status || "Under 11 Months") + "</strong>")
                   + makeRow('📦 Original Box', (row.original_box === "YES" ? tagPass("✅ Available (YES)") : tagFail("❌ Missing (NO)")))
                   + makeRow('⚡ Original Charger / Cable', (row.original_cable_adapter === "YES" ? tagPass("✅ Available (YES)") : tagFail("❌ Missing (NO)")))
                   + makeRow('🧾 Purchase Invoice / Bill', (row.original_bill === "YES" ? tagPass("✅ Available (YES)") : tagFail("❌ Missing (NO)")))
                   + "</table>"
                   + "</div>"

                   + "<!-- Reported Faults Summary Alert Banner -->"
                   + "<div style='background:#FFF5F5;border:1px solid #FFD2D2;border-radius:10px;padding:12px 14px;'>"
                   + "<div style='font-size:12.5px;font-weight:700;color:#111111;margin-bottom:4px;'>📋 Reported Faults &amp; Deductions:</div>"
                   + (row.failed_test_names && row.failed_test_names !== "None" ? "<div style='color:#D70015;font-weight:700;font-size:13px;line-height:1.4;'>⚠️ " + row.failed_test_names + "</div>" : "<div style='color:#1E8E3E;font-weight:700;font-size:13px;line-height:1.4;'>✅ Clean Device (No Faults Reported)</div>")
                   + "</div>";

      /* ============================================================
         CALCULATION VERIFICATION AUDIT (CAN BE COMMENTED OUT LATER)
         ============================================================ */
      if (row.calculation_audit) {
        var cleanAudit = String(row.calculation_audit)
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;");

        htmlBody += "<!-- Step-by-Step Calculation Audit Section -->"
                 + "<div style='margin-top:20px;background:#F8F9FA;border:1.5px solid #0071E3;border-radius:12px;padding:16px 18px;text-align:left;'>"
                 + "<div style='font-size:13.5px;font-weight:800;color:#0071E3;margin-bottom:8px;'>"
                 + "📊 Step-by-Step Valuation Calculation Audit (Verification):"
                 + "</div>"
                 + "<pre style='background:#FFFFFF;border:1px solid #E5E5EA;border-radius:8px;padding:12px;font-size:12px;line-height:1.6;color:#1C1C1E;font-family:monospace,Consolas,Courier,monospace;white-space:pre-wrap;margin:0;'>"
                 + cleanAudit
                 + "</pre>"
                 + "<div style='font-size:11px;color:#8E8E93;margin-top:6px;'>* This calculation audit block is active for verification. You can easily comment it out in Code.gs.</div>"
                 + "</div>";
      }
      /* ============================================================ */

      htmlBody += "</td></tr></table>"
                + "</td></tr></table>"
                + "</body></html>";

      var mailResult = "pending";
      try {
        MailApp.sendEmail({
          to: adminEmail,
          name: "CashSecond",
          subject: emailSubject,
          body: emailBody,
          htmlBody: htmlBody
        });
        mailResult = "sent_via_MailApp";
      } catch (mErr1) {
        try {
          GmailApp.sendEmail(adminEmail, emailSubject, emailBody, { 
            name: "CashSecond",
            htmlBody: htmlBody 
          });
          mailResult = "sent_via_GmailApp";
        } catch (mErr2) {
          mailResult = "error: " + mErr1.toString() + " | " + mErr2.toString();
        }
      }
    } catch (mailErr) {
      mailResult = "outer_error: " + mailErr.toString();
    }
  }

    return ContentService.createTextOutput(JSON.stringify({
      status: "success",
      lead_id: rowValues[2],
      row_index: sheet.getLastRow(),
      mail_status: mailResult,
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
  return ContentService.createTextOutput(JSON.stringify({ status: "ok", service: "CashSecond Webhook Live" }))
    .setMimeType(ContentService.MimeType.JSON);
}

function setupSheets() { getOrCreateSheet(); }

/**
 * Run this function in Apps Script to test email delivery & grant permissions
 */
function testEmail() {
  var to = "wholesalehouse2016@gmail.com, Cashsecondoffice@gmail.com";
  var subject = "📱 Test Lead Email from CashSecond Google Apps Script";
  var body = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
           + "📱 CashSecond Email Test Successful!\n"
           + "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
           + "Your Google Apps Script is now authorized and ready to send leads automatically without any passwords.\n";
  MailApp.sendEmail(to, subject, body);
  Logger.log("Test email sent to " + to);
}
