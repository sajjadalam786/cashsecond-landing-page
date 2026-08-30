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

    // ── SEND NOTIFICATION EMAIL TO STORE ADMIN WITH EMOJIS ───────────────────
    try {
      var adminEmail = "wholesalehouse2016@gmail.com";
      var leadName   = String(row.full_name || "Customer").trim();
      var leadPhone  = String(row.whatsapp_number || "").trim();
      var cleanPhone = leadPhone.replace(/[^0-9]/g, "");
      var devModel   = String(row.model || "iPhone").trim();
      var devStorage = String(row.storage || "").trim();
      var devVal     = String(row.final_estimated_value || "").trim();
      var devBase    = String(row.base_max_value || "").trim();
      var leadIdStr  = String(rowValues[2] || "").trim();
      var timeStr    = String(rowValues[0] + " " + rowValues[1]);

      var waLink = "https://wa.me/91" + cleanPhone + "?text=" + encodeURIComponent("Hi " + leadName + ", this is CashSecond regarding your iPhone valuation of " + devVal + " for " + devModel + " (" + devStorage + "). Ref: " + leadIdStr);

      var emailSubject = "📱 New Lead: " + devModel + " (" + devStorage + ") — " + devVal + " | " + leadName + " [" + leadIdStr + "]";

      var emailBody = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    + "📱 NEW iPHONE VALUATION LEAD | CashSecond\n"
                    + "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                    + "👤 CUSTOMER DETAILS:\n"
                    + "• Name:     " + leadName + "\n"
                    + "• Phone:    " + leadPhone + " (Click to Call: tel:" + cleanPhone + ")\n"
                    + "• WhatsApp: " + waLink + "\n"
                    + "• Email:    " + (row.email || "Not provided") + "\n"
                    + "• Lead ID:  " + leadIdStr + "\n"
                    + "• Time:     " + timeStr + "\n"
                    + "💰 VALUATION SUMMARY:\n"
                    + "• Device:          " + devModel + " (" + devStorage + ")\n"
                    + "• Final Valuation: " + devVal + "\n"
                    + "• Base Price:      " + devBase + "\n"
                    + "• Status:          Verified Online Quote\n"
                    + "📋 PHONE CONDITION & HEALTH:\n"
                    + "• 🖥️ Screen:       " + (row.display_working === "YES" ? "✅ Display Working (Clear)" : "❌ Display Fault / Blackout") + "\n"
                    + "• 🔍 Glass:        " + (row.screen_cracked === "NO" ? "✅ Front Glass Intact" : "❌ Front Glass Cracked") + "\n"
                    + "• ✨ Scratches:    " + (row.screen_major_scratches ? (row.screen_major_scratches.indexOf("Minor") !== -1 ? "⚠️ 1-2 Minor Scratches" : (row.screen_major_scratches.indexOf("Heavy") !== -1 ? "❌ Heavy Scratches" : "✅ Scratch-Free Screen")) : "✅ Scratch-Free Screen") + "\n"
                    + "• 📱 Body/Frame:   " + (row.body_condition ? (row.body_condition.indexOf("Clean") !== -1 ? "✅ Clean Metal Frame" : "⚠️ Has Dents / Body Scratches") : "✅ Clean Metal Frame") + "\n"
                    + "• 🔄 Chassis Bent: " + (row.phone_bent === "NO" ? "✅ Frame Flat & Straight" : "❌ Body Curved / Bent") + "\n"
                    + "• 🔨 Back Glass:   " + (row.body_damage === "NO" ? "✅ Back Glass Intact" : "❌ Back Glass Broken") + "\n"
                    + "• 🔋 Battery:      " + (row.battery_health ? (row.battery_health.indexOf("Above") !== -1 ? "🟢 " + row.battery_health : (row.battery_health.indexOf("Below") !== -1 ? "🔴 " + row.battery_health : "⚠️ " + row.battery_health)) : "🟢 Above 80% (Healthy)") + "\n"
                    + "• 📸 Cameras:      " + (row.front_camera === "YES" && row.rear_camera === "YES" ? "✅ Front & Rear Cameras Working" : "❌ Camera Faulty") + "\n"
                    + "• 👤 Face ID:      " + (row.face_id_touch_id === "YES" ? "✅ Face ID / Biometrics OK" : "❌ Face ID Broken") + "\n"
                    + "• 🔌 Charging:     " + (row.charging_port === "YES" ? "✅ Charging Port & Fast Charge OK" : "❌ Charging Port Issue") + "\n"
                    + "• 🔊 Audio:        " + (row.speaker === "YES" ? "✅ Loudspeaker & Earpiece Clear" : "❌ Audio Issue") + "\n"
                    + "• 📶 Wireless:     " + (row.wifi === "YES" && row.bluetooth === "YES" ? "✅ Wi-Fi & Bluetooth OK" : "❌ Wireless Issue") + "\n"
                    + "• 📅 Warranty:     " + (row.warranty_status || "6 to 11 Months (Under Warranty)") + "\n"
                    + "• 📦 Accessories:  📦 Box: " + row.original_box + " | ⚡ Charger: " + row.original_cable_adapter + " | 🧾 Bill: " + row.original_bill + "\n"
                    + "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

      var htmlBody = "<div style='font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,sans-serif;max-width:620px;margin:0 auto;background:#F5F5F7;padding:20px;color:#1D1D1F;'>"
                   + "<div style='background:#FFFFFF;border-radius:16px;padding:24px;border:1px solid #E5E5EA;box-shadow:0 4px 16px rgba(0,0,0,0.06);'>"
                   + "<div style='border-bottom:2px solid #0071E3;padding-bottom:12px;margin-bottom:18px;'>"
                   + "<span style='background:#0071E3;color:#FFFFFF;font-size:11px;font-weight:bold;padding:3px 10px;border-radius:20px;text-transform:uppercase;'>New Valuation Lead</span>"
                   + "<h2 style='margin:8px 0 2px 0;font-size:22px;color:#111111;'>" + devModel + " <span style='color:#6E6E73;font-size:16px;'>(" + devStorage + ")</span></h2>"
                   + "<div style='font-size:26px;font-weight:800;color:#1E8E3E;margin-top:4px;'>" + devVal + "</div>"
                   + "</div>"
                   + "<div style='background:#F5F5F7;border-radius:12px;padding:14px 16px;margin-bottom:18px;'>"
                   + "<h4 style='margin:0 0 10px 0;font-size:14px;color:#0071E3;text-transform:uppercase;'>👤 Customer Information</h4>"
                   + "<p style='margin:4px 0;'><strong>Name:</strong> " + leadName + "</p>"
                   + "<p style='margin:4px 0;'><strong>Phone:</strong> <a href='tel:" + cleanPhone + "' style='color:#0071E3;font-weight:bold;'>+91 " + cleanPhone + "</a></p>"
                   + "<p style='margin:4px 0;'><strong>Email:</strong> " + (row.email || "Not provided") + "</p>"
                   + "<p style='margin:4px 0;font-size:12px;color:#86868B;'><strong>Ref ID:</strong> " + leadIdStr + " &bull; " + timeStr + "</p>"
                   + "</div>"
                   + "<div style='display:flex;gap:10px;margin-bottom:20px;'>"
                   + "<a href='" + waLink + "' target='_blank' style='flex:1;background:#25D366;color:#FFFFFF;text-align:center;padding:12px;border-radius:10px;font-weight:bold;text-decoration:none;display:block;'>💬 Open WhatsApp Chat</a>"
                   + "<a href='tel:" + cleanPhone + "' style='flex:1;background:#0071E3;color:#FFFFFF;text-align:center;padding:12px;border-radius:10px;font-weight:bold;text-decoration:none;display:block;'>📞 Call Customer</a>"
                   + "</div>"
                   + "<div style='border:1px solid #E5E5EA;border-radius:12px;padding:16px;'>"
                   + "<h4 style='margin:0 0 12px 0;font-size:14px;color:#111111;text-transform:uppercase;'>📋 Device Condition</h4>"
                   + "<table style='width:100%;font-size:13.5px;line-height:1.8;border-collapse:collapse;'>"
                   + "<tr><td style='color:#6E6E73;width:40%;'>🖥️ Screen Display</td><td><strong>" + (row.display_working === "YES" ? "✅ Working (Clear)" : "❌ Display Fault") + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>🔍 Glass</td><td><strong>" + (row.screen_cracked === "NO" ? "✅ Intact" : "❌ Cracked") + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>✨ Scratches</td><td><strong>" + row.screen_major_scratches + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>📱 Body &amp; Frame</td><td><strong>" + row.body_condition + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>🔋 Battery</td><td><strong>" + row.battery_health + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>📸 Cameras</td><td><strong>" + (row.front_camera === "YES" && row.rear_camera === "YES" ? "✅ Working" : "❌ Faulty") + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>👤 Face ID</td><td><strong>" + (row.face_id_touch_id === "YES" ? "✅ Working" : "❌ Broken") + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>🔌 Charging Port</td><td><strong>" + (row.charging_port === "YES" ? "✅ Working" : "❌ Issue") + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>📦 Accessories</td><td><strong>Box: " + row.original_box + " &bull; Cable: " + row.original_cable_adapter + " &bull; Bill: " + row.original_bill + "</strong></td></tr>"
                   + "</table>"
                   + "</div>"
                   + "</div></div>";

      var mailResult = "pending";
      try {
        MailApp.sendEmail({
          to: adminEmail,
          subject: emailSubject,
          body: emailBody,
          htmlBody: htmlBody
        });
        mailResult = "sent_via_MailApp";
      } catch (mErr1) {
        try {
          GmailApp.sendEmail(adminEmail, emailSubject, emailBody, { htmlBody: htmlBody });
          mailResult = "sent_via_GmailApp";
        } catch (mErr2) {
          mailResult = "error: " + mErr1.toString() + " | " + mErr2.toString();
        }
      }
    } catch (mailErr) {
      mailResult = "outer_error: " + mailErr.toString();
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
  if (e && e.parameter && e.parameter.lead_id) return doPost(e);
  return ContentService.createTextOutput(JSON.stringify({ status: "ok", service: "CashSecond Webhook Live" }))
    .setMimeType(ContentService.MimeType.JSON);
}

function setupSheets() { getOrCreateSheet(); }

/**
 * Run this function in Apps Script to test email delivery & grant permissions
 */
function testEmail() {
  var to = "wholesalehouse2016@gmail.com";
  var subject = "📱 Test Lead Email from CashSecond Google Apps Script";
  var body = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
           + "📱 CashSecond Email Test Successful!\n"
           + "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
           + "Your Google Apps Script is now authorized and ready to send leads automatically without any passwords.\n";
  MailApp.sendEmail(to, subject, body);
  Logger.log("Test email sent to " + to);
}
