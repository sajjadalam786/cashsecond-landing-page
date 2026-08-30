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

    // ── SEND NOTIFICATION EMAIL TO STORE ADMINS WITH EMOJIS ─────────────────
    try {
      var adminEmail = "wholesalehouse2016@gmail.com, Cashsecondoffice@gmail.com";
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
                    + "• 📦 Original Box:      " + (row.original_box === "YES" ? "✅ YES (Include in verification)" : "❌ NO") + "\n"
                    + "• ⚡ Original Charger:  " + (row.original_cable_adapter === "YES" ? "✅ YES (Include in verification)" : "❌ NO") + "\n"
                    + "• 🧾 Purchase Bill:     " + (row.original_bill === "YES" ? "✅ YES (Include in verification)" : "❌ NO") + "\n\n"
                    + "📋 INSPECTOR SUMMARY:\n"
                    + "• Reported Issues:      " + (row.failed_test_names && row.failed_test_names !== "None" ? "⚠️ " + row.failed_test_names : "✅ Clean Device (All Tests Passed)") + "\n"
                    + "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

      var htmlBody = "<div style='font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,sans-serif;max-width:650px;margin:0 auto;background:#F5F5F7;padding:20px;color:#1D1D1F;'>"
                   + "<div style='background:#FFFFFF;border-radius:16px;padding:24px;border:1px solid #E5E5EA;box-shadow:0 4px 16px rgba(0,0,0,0.06);'>"
                   + "<div style='border-bottom:2px solid #0071E3;padding-bottom:14px;margin-bottom:18px;'>"
                   + "<div style='display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;'>"
                   + "<span style='background:#0071E3;color:#FFFFFF;font-size:11px;font-weight:bold;padding:4px 10px;border-radius:20px;text-transform:uppercase;'>Onsite Inspection Lead</span>"
                   + "<span style='font-size:12px;color:#86868B;font-weight:600;'>Ref: " + leadIdStr + "</span>"
                   + "</div>"
                   + "<h2 style='margin:0;font-size:22px;color:#111111;'>" + devModel + " <span style='color:#6E6E73;font-size:16px;'>(" + devStorage + ")</span></h2>"
                   + "<div style='font-size:26px;font-weight:800;color:#1E8E3E;margin-top:6px;'>" + devVal + " <span style='font-size:14px;font-weight:500;color:#86868B;'>Base: " + devBase + "</span></div>"
                   + "</div>"

                   + "<!-- Customer Information -->"
                   + "<div style='background:#F5F5F7;border-radius:12px;padding:14px 16px;margin-bottom:16px;'>"
                   + "<h4 style='margin:0 0 10px 0;font-size:13px;color:#0071E3;text-transform:uppercase;letter-spacing:0.04em;'>👤 Customer Information</h4>"
                   + "<table style='width:100%;font-size:13.5px;line-height:1.7;'>"
                   + "<tr><td style='width:35%;color:#6E6E73;'>Customer Name:</td><td><strong>" + leadName + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Phone Number:</td><td><a href='tel:" + cleanPhone + "' style='color:#0071E3;font-weight:bold;text-decoration:none;'>+91 " + cleanPhone + "</a></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Email Address:</td><td>" + (row.email || "Not provided") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Submission Time:</td><td>" + timeStr + "</td></tr>"
                   + "</table>"
                   + "</div>"

                   + "<!-- Action Buttons -->"
                   + "<div style='display:flex;gap:10px;margin-bottom:18px;'>"
                   + "<a href='" + waLink + "' target='_blank' style='flex:1;background:#25D366;color:#FFFFFF;text-align:center;padding:12px;border-radius:10px;font-weight:bold;text-decoration:none;font-size:14px;display:block;'>💬 Open WhatsApp Chat</a>"
                   + "<a href='tel:" + cleanPhone + "' style='flex:1;background:#0071E3;color:#FFFFFF;text-align:center;padding:12px;border-radius:10px;font-weight:bold;text-decoration:none;font-size:14px;display:block;'>📞 Call Customer</a>"
                   + "</div>"

                   + "<!-- Display & Screen Section -->"
                   + "<div style='border:1px solid #E5E5EA;border-radius:12px;padding:16px;margin-bottom:16px;'>"
                   + "<h4 style='margin:0 0 10px 0;font-size:13px;color:#111111;text-transform:uppercase;letter-spacing:0.04em;'>🖥️ Screen &amp; Display Check</h4>"
                   + "<table style='width:100%;font-size:13px;line-height:1.8;border-collapse:collapse;'>"
                   + "<tr><td style='width:45%;color:#6E6E73;'>Display Power / Working</td><td>" + (row.display_working === "YES" ? "✅ Working (Clear)" : "<strong style='color:#D70015;'>❌ Fault / Blackout</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Touchscreen Response</td><td>" + (row.touchscreen_working === "YES" ? "✅ Responsive" : "<strong style='color:#D70015;'>❌ Faulty</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Front Screen Glass</td><td>" + (row.screen_cracked === "NO" ? "✅ Intact (No Cracks)" : "<strong style='color:#D70015;'>❌ Glass Cracked</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Screen Scratch Level</td><td><strong>" + (row.screen_major_scratches || "Scratch-Free") + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Lines / Dots / Spots</td><td>" + (row.display_lines_spots === "NO" ? "✅ Clean (No Defects)" : "<strong style='color:#D70015;'>❌ Defect Present</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Display Originality</td><td>" + (row.original_display === "YES" ? "✅ Original Apple Screen" : "<strong style='color:#E37400;'>⚠️ Replaced Screen</strong>") + "</td></tr>"
                   + "</table>"
                   + "</div>"

                   + "<!-- Body & Chassis Section -->"
                   + "<div style='border:1px solid #E5E5EA;border-radius:12px;padding:16px;margin-bottom:16px;'>"
                   + "<h4 style='margin:0 0 10px 0;font-size:13px;color:#111111;text-transform:uppercase;letter-spacing:0.04em;'>📱 Body &amp; Frame Condition</h4>"
                   + "<table style='width:100%;font-size:13px;line-height:1.8;border-collapse:collapse;'>"
                   + "<tr><td style='width:45%;color:#6E6E73;'>Metal Frame Marks</td><td><strong>" + (row.body_condition || "Clean Metal Frame") + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Chassis / Bent Body</td><td>" + (row.phone_bent === "NO" ? "✅ Flat &amp; Straight" : "<strong style='color:#D70015;'>❌ Frame Bent / Curved</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Back Glass Condition</td><td>" + (row.body_damage === "NO" ? "✅ Intact" : "<strong style='color:#D70015;'>❌ Back Glass Broken</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Camera Lens Glass</td><td>" + (row.camera_glass_condition === "NO" ? "✅ Clear &amp; Intact" : "<strong style='color:#D70015;'>❌ Glass Broken</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Parts / Screws</td><td>" + (row.missing_parts === "NO" ? "✅ All Intact" : "<strong style='color:#D70015;'>❌ Missing Parts</strong>") + "</td></tr>"
                   + "</table>"
                   + "</div>"

                   + "<!-- Functional Hardware Section -->"
                   + "<div style='border:1px solid #E5E5EA;border-radius:12px;padding:16px;margin-bottom:16px;'>"
                   + "<h4 style='margin:0 0 10px 0;font-size:13px;color:#111111;text-transform:uppercase;letter-spacing:0.04em;'>⚙️ Hardware &amp; Component Tests</h4>"
                   + "<table style='width:100%;font-size:13px;line-height:1.8;border-collapse:collapse;'>"
                   + "<tr><td style='width:45%;color:#6E6E73;'>Front Camera</td><td>" + (row.front_camera === "YES" ? "✅ Working" : "<strong style='color:#D70015;'>❌ Faulty</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Rear Main Camera</td><td>" + (row.rear_camera === "YES" ? "✅ Working" : "<strong style='color:#D70015;'>❌ Faulty</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Camera Flash</td><td>" + (row.camera_flash === "YES" ? "✅ Working" : "<strong style='color:#D70015;'>❌ Faulty</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Face ID / Biometrics</td><td>" + (row.face_id_touch_id === "YES" ? "✅ Working" : "<strong style='color:#D70015;'>❌ Broken</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Charging Port</td><td>" + (row.charging_port === "YES" ? "✅ Port &amp; Fast Charge OK" : "<strong style='color:#D70015;'>❌ Port Issue</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Speaker &amp; Audio</td><td>" + (row.speaker === "YES" ? "✅ Loudspeaker OK" : "<strong style='color:#D70015;'>❌ Audio Issue</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Earpiece / Receiver</td><td>" + (row.ear_receiver === "YES" ? "✅ Clear Call Audio" : "<strong style='color:#D70015;'>❌ Receiver Issue</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Microphone</td><td>" + (row.microphone === "YES" ? "✅ Working" : "<strong style='color:#D70015;'>❌ Faulty</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Physical Buttons</td><td>" + (row.power_button === "YES" && row.volume_buttons === "YES" ? "✅ Power &amp; Volume OK" : "<strong style='color:#D70015;'>❌ Button Issue</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Silent Switch</td><td>" + (row.silent_switch === "YES" ? "✅ Working" : "<strong style='color:#D70015;'>❌ Faulty</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Wi-Fi &amp; Bluetooth</td><td>" + (row.wifi === "YES" && row.bluetooth === "YES" ? "✅ Connected OK" : "<strong style='color:#D70015;'>❌ Wireless Issue</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Mobile SIM / Network</td><td>" + (row.mobile_network_sim === "YES" ? "✅ Signal OK" : "<strong style='color:#D70015;'>❌ Network Issue</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>GPS Location</td><td>" + (row.gps === "YES" ? "✅ Working" : "<strong style='color:#D70015;'>❌ Faulty</strong>") + "</td></tr>"
                   + "</table>"
                   + "</div>"

                   + "<!-- Battery, Warranty & Inclusions -->"
                   + "<div style='border:1px solid #E5E5EA;border-radius:12px;padding:16px;margin-bottom:16px;'>"
                   + "<h4 style='margin:0 0 10px 0;font-size:13px;color:#111111;text-transform:uppercase;letter-spacing:0.04em;'>🔋 Battery, History &amp; Inclusions</h4>"
                   + "<table style='width:100%;font-size:13px;line-height:1.8;border-collapse:collapse;'>"
                   + "<tr><td style='width:45%;color:#6E6E73;'>Battery Health</td><td><strong>" + (row.battery_health || "Above 80%") + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Liquid / Water Damage</td><td>" + (row.liquid_damage === "NO" ? "✅ Safe (No Water Damage)" : "<strong style='color:#D70015;'>❌ Liquid Damaged</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Component Repairs</td><td>" + (row.major_component_replaced === "NO" ? "✅ None (Original)" : "<strong style='color:#E37400;'>⚠️ Replaced: " + (row.replaced_component || "Parts") + "</strong>") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Warranty Period</td><td><strong>" + (row.warranty_status || "6 to 11 Months") + "</strong></td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Original Box</td><td>" + (row.original_box === "YES" ? "✅ YES (Verify Box)" : "❌ NO") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Original Charger / Cable</td><td>" + (row.original_cable_adapter === "YES" ? "✅ YES (Verify Cable)" : "❌ NO") + "</td></tr>"
                   + "<tr><td style='color:#6E6E73;'>Purchase Invoice / Bill</td><td>" + (row.original_bill === "YES" ? "✅ YES (Verify Bill)" : "❌ NO") + "</td></tr>"
                   + "</table>"
                   + "</div>"

                   + "<!-- Reported Issues Summary -->"
                   + "<div style='background:#F9F9FB;border:1px dashed #D2D2D7;border-radius:10px;padding:12px 14px;font-size:12.5px;color:#48484A;'>"
                   + "<strong>📋 Reported Issues by Customer:</strong> " + (row.failed_test_names && row.failed_test_names !== "None" ? "<span style='color:#D70015;font-weight:700;'>" + row.failed_test_names + "</span>" : "<span style='color:#1E8E3E;font-weight:700;'>None (All Checks Passed)</span>")
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
  var to = "wholesalehouse2016@gmail.com, Cashsecondoffice@gmail.com";
  var subject = "📱 Test Lead Email from CashSecond Google Apps Script";
  var body = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
           + "📱 CashSecond Email Test Successful!\n"
           + "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
           + "Your Google Apps Script is now authorized and ready to send leads automatically without any passwords.\n";
  MailApp.sendEmail(to, subject, body);
  Logger.log("Test email sent to " + to);
}
