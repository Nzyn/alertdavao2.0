# Use Current Location Feature - UserSide Implementation

## Overview

The "Use Current Location" feature allows users to quickly get their GPS coordinates and address when submitting a crime report in the UserSide app.

## How It Works

### 1. Report Form Flow
When a user opens the Report Crime screen:
1. They can manually enter a location in the text field
2. They can click "Use my current location" button to open the location picker
3. The location picker has multiple methods to set location:
   - Automatic GPS detection (via "Use Current Location" button in picker)
   - Search for an address manually
   - Click on the map to select a location

### 2. Permission Handling

The app requests location permissions when the user clicks "Use my current location":

**Android:**
- Requests `android.permission.ACCESS_FINE_LOCATION` (precise GPS)
- Requests `android.permission.ACCESS_COARSE_LOCATION` (network-based location)
- User will see a native permission prompt on first use

**iOS:**
- Requires `NSLocationWhenInUseUsageDescription` in app config
- User will see a permission prompt asking "AlertDavao needs your location to help you report crimes in the right area."

### 3. Location Data Captured

Once a location is selected, the app stores:
- **Address**: Human-readable location description (e.g., "Roxas Avenue, Poblacion, Davao City, Davao del Sur")
- **Latitude**: GPS latitude coordinate (e.g., 7.0731)
- **Longitude**: GPS longitude coordinate (e.g., 125.6128)

### 4. Visual Feedback

**Location Selected:**
- The location input field shows a green border
- A checkmark appears with coordinates: "Coordinates saved: 7.0731, 125.6128"
- Users can see their full address in the text field

**No Location Selected:**
- A warning appears when submitting: "No location has been selected"
- Users can choose to submit anyway or select a location

## Component Details

### LocationPickerModal.tsx

**Key Functions:**

#### `getCurrentLocation()`
- Requests foreground location permissions
- Gets current device location with high accuracy
- Reverse geocodes coordinates to human-readable address
- Updates map and selected address

**Error Handling:**
- `E_LOCATION_UNAVAILABLE`: Location services disabled
- `E_PERMISSION_DENIED`: User denied permission
- Timeout errors: GPS/Internet connection issues

#### `searchLocation(query)`
- Uses OpenStreetMap Nominatim API
- Searches for places/addresses in Philippines
- Limits results to 8 most relevant matches
- Updates map to show first result

#### `selectSearchResult(result)`
- Updates marker on map
- Sets address and coordinates
- Closes search results overlay

### report.tsx

**Key Functions:**

#### `handleUseLocation()`
- Opens the location picker modal

#### `handleLocationSelect(address, coordinates)`
- Called when user confirms location selection
- Updates the location field with address
- Stores latitude/longitude for submission

#### `handleSubmit()`
- Validates all required fields
- Warns if no location is selected
- Calls `submitReportData()` after validation

#### `submitReportData()`
- Formats incident date/time
- Prepares report data with location coordinates
- Sends to backend via `reportService.submitReport()`
- Shows success dialog on completion

## Backend Integration

When submitting a report, the following location data is sent:
```javascript
{
  title: "...",
  crimeTypes: [...],
  description: "...",
  incidentDate: "...",
  isAnonymous: false,
  location: "Roxas Avenue, Poblacion, Davao City, Davao del Sur",
  latitude: 7.0731,
  longitude: 125.6128,
  userId: "..."
}
```

## Testing

### On Android:
1. Open UserSide app
2. Go to Report Crime screen
3. Click "Use my current location" button
4. Grant location permission when prompted
5. Wait 2-3 seconds for GPS to lock
6. Address should appear in location field with green checkmark

### On iOS:
1. Open UserSide app
2. Go to Report Crime screen
3. Click "Use my current location" button
4. Grant location permission in alert
5. Wait for GPS to acquire coordinates
6. Address displays with coordinates

### On Web:
1. Click "Use my current location" button
2. Browser will ask for location permission
3. Grant permission (may need HTTPS for web version)
4. Location should be fetched

## Troubleshooting

### "Unable to get current location" Error

**Cause 1: Location Services Disabled**
- Check device settings > Location > Ensure "On"
- Android: Settings > Location > Toggle On
- iOS: Settings > Privacy > Location Services > Toggle On

**Cause 2: Permission Denied**
- User rejected permission prompt
- Android: Settings > Apps > UserSide > Permissions > Location > Allow
- iOS: Settings > Privacy > Location Services > UserSide > "While Using"

**Cause 3: GPS/Internet Issues**
- No GPS signal (try moving outside)
- No internet connection
- WiFi only (use network-based location)

**Cause 4: Device Doesn't Support Location**
- Older Android/iOS versions may not support high-accuracy GPS
- Check device specifications

### Location is Slow to Load

- High accuracy requests can take 5-10 seconds
- GPS signal strength varies by location
- Move near a window for better signal

### Wrong Address Displayed

- Reverse geocoding service (OSM Nominatim) may be inaccurate in some areas
- Users can manually edit the address field
- Manually search for correct location via search box

## Configuration Files

### app.json (Already Configured)
```json
{
  "expo": {
    "ios": {
      "infoPlist": {
        "NSLocationWhenInUseUsageDescription": "AlertDavao needs your location to help you report crimes in the right area.",
        "NSLocationAlwaysAndWhenInUseUsageDescription": "AlertDavao needs your location to help you report crimes in the right area."
      }
    },
    "android": {
      "permissions": [
        "android.permission.ACCESS_FINE_LOCATION",
        "android.permission.ACCESS_COARSE_LOCATION"
      ]
    }
  }
}
```

### package.json (Dependency)
```json
{
  "dependencies": {
    "expo-location": "^19.0.7"
  }
}
```

## Files Modified

1. **app/(tabs)/report.tsx**
   - Added location validation warning
   - Improved logging for location data
   - Added visual feedback for selected location
   - Enhanced error handling

2. **components/LocationPickerModal.tsx**
   - Improved `getCurrentLocation()` error handling
   - Better permission request messaging
   - More detailed error messages

3. **app/(tabs)/styles.js**
   - Updated `locationButton` styling (blue background, white text)
   - Added icon support in button

## Recent Improvements (This Update)

✅ **Better Error Messages**
- Specific errors for location unavailable, permission denied, timeout
- Clear instructions on how to fix each error

✅ **Visual Feedback**
- Green border and checkmark when location is selected
- Coordinates display below location field
- Button now has proper styling with icon

✅ **Location Validation**
- Warning alert when submitting without location
- Option to submit anyway if needed
- Coordinates are properly passed to backend

✅ **Enhanced Logging**
- Detailed console logs for debugging
- Tracks permission status
- Logs geocoding results

## Future Enhancements

- [ ] Auto-fill location on report screen load (with user confirmation)
- [ ] Offline location caching
- [ ] Location history suggestions
- [ ] Multiple address selection
- [ ] Map preview in report form
- [ ] Batch location updates

## Support & Contact

For issues with location features:
1. Check troubleshooting section above
2. Review console logs (use React DevTools)
3. Test with Google Maps app first (to verify GPS works)
4. Contact development team with screenshots of errors
