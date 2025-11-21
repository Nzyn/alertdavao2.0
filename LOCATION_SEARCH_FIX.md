# Location Search CORS Fix

## Problem
When users tried to search for locations in the Report Crime screen, they received a CORS (Cross-Origin Resource Sharing) error:
```
Access to fetch at 'https://nominatim.openstreetmap.org/search?...' from origin 'http://localhost:8081' 
has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource.
```

This occurred because the frontend was making direct requests to the OpenStreetMap Nominatim API, which blocks cross-origin requests from browsers.

## Solution
Implemented a backend proxy service that:
1. Routes all location searches through the backend server
2. Eliminates CORS issues by making server-to-server requests
3. Provides a consistent, documented API for location services

## Changes Made

### 1. Backend Location Handler (`handleLocation.js`)
Created a new file: `/UserSide/backends/handleLocation.js`

**Endpoints:**
- `GET /api/location/search?q=search+query` - Search for locations by name
- `GET /api/location/reverse?lat=x&lon=y` - Reverse geocode coordinates to address
- `GET /api/location/distance?lat1=x&lon1=y&lat2=a&lon2=b` - Calculate distance between two points

**Features:**
- Handles timeouts gracefully
- Formats Nominatim API responses for frontend consumption
- Validates input parameters
- Returns consistent JSON responses
- Includes detailed logging for debugging

### 2. Server Routes Update (`server.js`)
Added three new routes:
```javascript
app.get("/api/location/search", searchLocation);
app.get("/api/location/reverse", reverseGeocode);
app.get("/api/location/distance", getDistance);
```

### 3. Frontend Updates (`LocationPickerModal.tsx`)
Modified location search to route through backend:

**Before (Direct Nominatim API):**
```javascript
const response = await fetch(
  `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(searchQuery)}&...`,
);
```

**After (Backend Proxy):**
```javascript
const backendUrl = process.env.EXPO_PUBLIC_BACKEND_URL || 'http://192.168.1.4:3000';
const response = await fetch(
  `${backendUrl}/api/location/search?q=${encodeURIComponent(searchQuery)}`,
);
```

**Changes applied to:**
- `searchLocation()` - Uses `/api/location/search` endpoint
- `reverseGeocodeNominatim()` - Uses `/api/location/reverse` endpoint

## API Response Formats

### Location Search
```json
{
  "success": true,
  "query": "jade valley",
  "results": [
    {
      "address": "Full address display name",
      "lat": "7.0731",
      "lon": "125.6128",
      "name": "Place name",
      "city": "Davao City"
    }
  ],
  "count": 1
}
```

### Reverse Geocoding
```json
{
  "success": true,
  "latitude": 7.0731,
  "longitude": 125.6128,
  "address": "Formatted address",
  "display_name": "Full display name from Nominatim"
}
```

## Benefits
1. ✅ Eliminates CORS errors
2. ✅ Centralizes location logic on backend
3. ✅ Better error handling and logging
4. ✅ Flexible response formatting
5. ✅ Supports future enhancements (caching, rate limiting, etc.)
6. ✅ Works on both web and mobile platforms

## Testing
To test the location search:
1. Open the Report Crime screen
2. Click on the location search input
3. Type a location (e.g., "Jade Valley", "SM City Davao")
4. Results should appear without CORS errors
5. Select a result to update the map

## Troubleshooting
If location search still doesn't work:
1. Ensure backend server is running (`npm start` in UserSide/backends)
2. Check that the backend URL is correctly detected/configured
3. Check console logs for backend errors
4. Verify Nominatim API is accessible from the server
5. Check network tab in browser DevTools for failed requests

## Files Modified
- `/UserSide/components/LocationPickerModal.tsx` - Updated search functions
- `/UserSide/backends/server.js` - Added location routes
- `/UserSide/backends/handleLocation.js` - New location service handler
