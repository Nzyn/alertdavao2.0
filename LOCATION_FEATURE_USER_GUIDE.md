# User Guide: Use Current Location Feature

## What is This Feature?

The "Use my current location" feature automatically finds your GPS coordinates and address when you submit a crime report in AlertDavao UserSide app. No need to manually type an address!

## How to Use (Step-by-Step)

### Step 1: Open Report Crime Screen
- Tap the **Report Crime** tab in the bottom navigation
- You'll see the crime report form

### Step 2: Fill in Required Information
- **Title**: What happened? (e.g., "Wallet stolen near market")
- **Crime Type**: Select one or more crime types
- **Description**: Provide detailed description
- **Date & Time**: When did it happen?

### Step 3: Get Your Current Location
1. Look for the blue button that says **"📍 Use my current location"**
2. **Tap the button**
3. The app will ask for permission to access your location
   - Tap **"Allow"** or **"Allow While Using App"**

### Step 4: Wait for Location Lock
- The button will show "Getting location..." while it's working
- GPS takes 2-3 seconds to lock onto your position
- Stay near a window for better signal (especially indoors)

### Step 5: Confirm Location
- Once the location is found, you'll see:
  - ✅ Green checkmark below the location field
  - 📍 Your full address in the location field
  - Coordinates saved: "7.0731, 125.6128"

### Step 6: Add Optional Details
- **Photo/Video**: Select evidence (optional)
- **Anonymous**: Check if you want to report anonymously (optional)

### Step 7: Submit Report
- Tap the **"Submit Report"** button
- Your report will be sent with your exact location

## What Location Data is Sent?

When you submit a report, the following location information is included:

| Data | Example | Used For |
|------|---------|----------|
| **Address** | "Roxas Avenue, Poblacion, Davao City" | Readable location display |
| **Latitude** | 7.0731 | Exact GPS position |
| **Longitude** | 125.6128 | Exact GPS position |

## Where to Find Location Features

### During Report Submission
```
┌─────────────────────────────┐
│ Report Crime Form           │
├─────────────────────────────┤
│ Title: _______________       │
│ Crime Type: [Select]        │
│ Location: ______________    │  ← Manual entry
│ [📍 Use current location]   │  ← GPS button
│ ✅ Coordinates saved: ...   │  ← Confirmation
│ Description: ___________    │
│ Photo/Video: [Upload]       │
│ [Submit Report]             │
└─────────────────────────────┘
```

## Visual Indicators

### Location Selected ✅
- Location field has **green border**
- **Green checkmark icon** appears
- **Coordinates display** below field

### Location Not Selected ❌
- Location field is **empty**
- **Warning appears** when you try to submit
- You can choose to submit anyway

## Troubleshooting

### "Allow location permission" prompt doesn't appear
**Solution:**
- App is already allowed or denied location access
- Check app settings: Settings > Apps > UserSide > Permissions > Location
- Ensure "Allow" is selected

### GPS takes too long to lock (>10 seconds)
**Causes:**
- Weak GPS signal (try moving to window)
- Poor internet connection
- Tall buildings blocking sky view

**Solution:**
- Go outdoors or near a window
- Check internet connection
- Retry the button

### "Location services not available"
**Causes:**
- Device location is turned OFF
- App doesn't have location permission

**Solution:**
Android:
- Go to Settings > Location > Turn ON
- Grant app permission in Settings > Apps > UserSide > Permissions

iOS:
- Go to Settings > Privacy > Location Services > Turn ON
- Enable for UserSide in Location Services

### Address shows coordinates instead of street address
**Why this happens:**
- Reverse address lookup service unavailable
- Location is in very remote area

**Solution:**
- Still works! Coordinates are accurate
- Can manually edit address field
- Location will still submit correctly

### "Device doesn't support location"
**Causes:**
- Very old Android/iOS version
- Emulator/simulator without location hardware

**Solution:**
- Use actual device instead of emulator
- Or manually enter address in location field

## Privacy & Security

### Your Location Privacy
- ✅ Your location is only sent when you submit a report
- ✅ Location is encrypted when transmitted to server
- ✅ Only police admins can see your exact coordinates
- ✅ You can report anonymously (hides your identity, not location)
- ✅ Location is tied to the crime report, not your profile

### Device Permissions
- ✅ App only accesses location when button is clicked
- ✅ App doesn't track you continuously
- ✅ You can revoke permission anytime in device settings
- ✅ Permission prompt appears every time (some devices)

## Tips for Best Results

### 📱 Device Setup
1. Ensure Location Services are **ON**
2. Grant app **"Allow While Using App"** permission (not just "Allow Once")
3. Use device's **native location service** (not just WiFi)

### 🌍 Optimal Conditions
1. **Outdoor** location works best (clear sky view)
2. **Near window** if indoors (open windows even better)
3. **Remove obstacles** between device and sky
4. **Good internet connection** for address lookup

### ⚡ Faster GPS Lock
1. Wait after enabling location (device warms up GPS)
2. Open Google Maps first (initializes GPS)
3. Then open AlertDavao for instant location
4. Use location frequently (device learns patterns)

## Frequently Asked Questions

**Q: Why does it need location permission?**
A: To verify crime reports are made from the actual location where the crime occurred.

**Q: Can I disable location sharing?**
A: Yes, manually enter address instead of using GPS button.

**Q: What if GPS is wrong by a few meters?**
A: Completely normal! GPS accuracy varies from 5-50 meters depending on signal strength.

**Q: Is my location visible to other users?**
A: No. Only police administrators can see exact coordinates in the report.

**Q: What if I change my mind about the location?**
A: Simply click the button again to get a new location, or manually edit the address.

**Q: Does it work offline?**
A: GPS coordinates work offline, but address lookup requires internet.

**Q: Can I submit multiple reports from same location?**
A: Yes! Each report has its own location timestamp.

**Q: What's the difference between GPS and Network location?**
A: GPS = precise (5-10m), Network = approximate (50-500m). App uses GPS first.

## Example Report

### What Gets Submitted:
```
Report Details:
  Title: Robbery near city hall
  Type: Theft/Robbery
  Location: A. Bonifacio Street, Davao City
  Latitude: 7.0731
  Longitude: 125.6128
  Description: Lost wallet and phone
  Date/Time: 2024-11-19 15:30:45
  Anonymous: No
```

### Police See:
- Your location on a map
- Exact coordinates for investigation
- Street address for context

## Need More Help?

If location still doesn't work after troubleshooting:

1. **Check Console Logs** (development)
2. **Test with Google Maps** (verify GPS works)
3. **Restart the app** (force refresh)
4. **Clear app cache** (reset data)
5. **Contact Support** with:
   - Device type (Android/iOS)
   - OS version
   - Error message shown
   - Steps you tried

---

**Quick Test:** Open Google Maps. If location works there, it'll work in AlertDavao!

**Last Updated:** November 2025
**Version:** 2.0.0
