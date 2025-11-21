# Visual Reference: Current Location Feature

## UI Before & After

### Location Button

**BEFORE:**
```
┌──────────────────────────────────┐
│  Use my current location         │  ← Gray background
└──────────────────────────────────┘
```

**AFTER:**
```
┌──────────────────────────────────┐
│  📍  Use my current location     │  ← Blue background + icon
└──────────────────────────────────┘
```

**Styling Changes:**
- Background: `#e0e0e0` (Gray) → `#1D3557` (Dark Blue)
- Text Color: Default (Black) → `#fff` (White)
- Font Weight: Normal → Bold (600)
- Icon: None → Locate icon (📍)
- Shadow: None → Elevation effect
- Size: 12px padding → 14px padding

---

## Location Field States

### State 1: Empty (No Location Selected)
```
┌─────────────────────────────────────┐
│ Location *                          │
├─────────────────────────────────────┤
│ Input location manually...          │  ← Placeholder
│                                     │
│                                     │
├─────────────────────────────────────┤
│ [📍 Use my current location]        │
└─────────────────────────────────────┘
```
- Thin gray border
- Empty field
- Placeholder text visible
- Regular button color

---

### State 2: Getting Location (Loading)
```
┌─────────────────────────────────────┐
│ Location *                          │
├─────────────────────────────────────┤
│ [Loading spinner...]                │
│                                     │
├─────────────────────────────────────┤
│ [⏳ Getting location...]            │  ← Button shows loading
└─────────────────────────────────────┘
```
- Loading indicator
- Button disabled
- "Getting location..." text
- Spinner animation

---

### State 3: Location Selected ✅
```
┌──────────────────────────────────────────┐
│ Location *                               │
├──────────────────────────────────────────┤
│ Roxas Avenue, Poblacion, Davao City      │  ← Green border
│ Davao del Sur                            │
├──────────────────────────────────────────┤
│ ✅ Coordinates saved: 7.0731, 125.6128  │  ← Confirmation
│                                          │
├──────────────────────────────────────────┤
│ [📍 Use my current location]             │
└──────────────────────────────────────────┘
```
- **Thick green border** (#4CAF50)
- Address displayed
- **Green checkmark icon**
- **Coordinates shown**
- Indicates location is saved

---

## Location Picker Modal

### Map Interface

```
┌─────────────────────────────────┐
│  ✕  Select Location      ✓      │
├─────────────────────────────────┤
│ ┌─────────────────────────────┐ │
│ │                             │ │
│ │    Map (Leaflet)           │ │  ← Interactive map
│ │    Center: 7.0731, 125.6128│ │
│ │    📍 Draggable marker      │ │
│ │                             │ │
│ │             🎯              │ │  ← Center button
│ └─────────────────────────────┘ │
│                                 │
│ Selected Location:              │
│ Roxas Avenue, Davao City        │
│                                 │
│ [📍 Use Current Location]        │  ← Blue button
│                                 │
│ Getting location...             │  ← Status text
└─────────────────────────────────┘
```

### Search Results

```
┌─────────────────────────────────┐
│ [Search field] [🔍]             │
├─────────────────────────────────┤
│                                 │
│ Search Results:                 │
│                                 │
│ ┌──────────────────────────────┐│
│ │ 📍 Roxas Avenue Davao       ││  ← Result 1
│ │    Poblacion, Davao City     ││
│ └──────────────────────────────┘│
│                                 │
│ ┌──────────────────────────────┐│
│ │ 📍 SM City Davao             ││  ← Result 2
│ │    Lanang, Davao City        ││
│ └──────────────────────────────┘│
│                                 │
│ [Close Search]                  │
└─────────────────────────────────┘
```

---

## Form Submission Flow

### Step 1: Fill Form
```
Title: Robbery near city hall ✓
Crime Type: Theft/Robbery ✓
Location: [empty] ❌
Description: Lost wallet and phone ✓
Date & Time: 2024-11-19 15:30 ✓

[Submit Report]
```

### Step 2: Click Location Button
```
Title: Robbery near city hall ✓
Crime Type: Theft/Robbery ✓
Location: [Getting location...] ⏳
Description: Lost wallet and phone ✓
Date & Time: 2024-11-19 15:30 ✓

[📍 Getting location...]
```

### Step 3: Location Obtained
```
Title: Robbery near city hall ✓
Crime Type: Theft/Robbery ✓
Location: Roxas Avenue, Davao City ✓
         ✅ 7.0731, 125.6128 ✓
Description: Lost wallet and phone ✓
Date & Time: 2024-11-19 15:30 ✓

[📍 Use my current location]
```

### Step 4: Submit
```
┌────────────────────────────┐
│ [Submit Report]            │  ← All fields filled
└────────────────────────────┘

↓ Sending to backend...

┌────────────────────────────┐
│ Report Submitted!          │
│ Thank you for helping...   │
│ [View History]             │
└────────────────────────────┘
```

---

## Error States

### Error 1: Location Services Disabled
```
┌────────────────────────────────────┐
│ Error                              │
├────────────────────────────────────┤
│ Location services are not          │
│ available or disabled.             │
│                                    │
│ Please enable location services    │
│ on your device and try again.      │
│                                    │
│ [OK]                               │
└────────────────────────────────────┘
```

### Error 2: Permission Denied
```
┌────────────────────────────────────┐
│ Permission Required                │
├────────────────────────────────────┤
│ AlertDavao needs location           │
│ permission to use the current      │
│ location feature.                  │
│ Please grant permission in your    │
│ device settings.                   │
│                                    │
│ [Cancel]  [Try Again]              │
└────────────────────────────────────┘
```

### Error 3: GPS Timeout
```
┌────────────────────────────────────┐
│ Error                              │
├────────────────────────────────────┤
│ Location request timed out.        │
│ Please check your internet and     │
│ GPS connection.                    │
│                                    │
│ [OK]                               │
└────────────────────────────────────┘
```

---

## Coordinate Display

### Format
```
✅ Coordinates saved: 7.0731, 125.6128
   ↑                  ↑      ↑
   Icon               Lat    Lng
```

### Precision
- **Latitude**: 7.0731 (4 decimal places = ~11m accuracy)
- **Longitude**: 125.6128 (4 decimal places = ~11m accuracy)

---

## Color Scheme

| Element | Color | Hex | Usage |
|---------|-------|-----|-------|
| Button Background | Dark Blue | #1D3557 | Location button |
| Button Text | White | #fff | Button label |
| Selected Border | Green | #4CAF50 | Location field |
| Checkmark | Green | #4CAF50 | Confirmation icon |
| Error Text | Red | #E63946 | Error messages |
| Success Text | Green | #4CAF50 | Success feedback |
| Placeholder | Gray | #999 | Hint text |

---

## Interactive Elements

### Touch Targets (Mobile)
```
Location Button: 
┌─────────────────────┐
│                     │  ← 48px height
│  📍 Use Location    │     (44px minimum recommended)
│                     │
└─────────────────────┘
```

### Tap Areas
- Location button: Full width
- Checkmark icon: 16x16 px (part of text)
- Map markers: 32x32 px
- Search results: Full width, 48px height each

---

## Animation States

### Loading State
```
Button shows: "⏳ Getting location..."
Spinner: Rotating circle
Duration: 2-3 seconds for GPS lock
```

### Success State
```
Checkmark: Fade in (200ms)
Text: Green color (300ms)
Border: Green highlight (300ms)
```

### Error State
```
Alert: Slide up from bottom (200ms)
Icon: Shake animation (300ms)
Text: Red color (300ms)
```

---

## Accessibility Features

### Text Contrast
```
✅ Dark blue (#1D3557) on white: 7:1 ratio (exceeds WCAG AA)
✅ Green (#4CAF50) on white: 4.5:1 ratio (meets WCAG AA)
✅ White on blue: 7:1 ratio (excellent contrast)
```

### Touch Size
```
✅ 48px minimum height (exceeds 44px guideline)
✅ 16px minimum padding around elements
✅ Clear visual focus states
```

### Readability
```
✅ Font size 16px minimum
✅ Line height 1.5 (good spacing)
✅ Icon + text labels (clear meaning)
✅ Color + text together (not color alone)
```

---

## Responsive Design

### Mobile (320px - 480px)
```
Full width button
Vertical stacking
Large touch targets
```

### Tablet (480px - 1024px)
```
Fixed width container
Horizontal layout option
Larger map display
```

### Desktop (1024px+)
```
Centered container
Side-by-side layout
Full-size map
```

---

## Platform Differences

### Android
```
Permission prompt: Native Android style
Button: Material Design (ripple effect)
Icons: Material Design icons
Colors: Full RGB support
```

### iOS
```
Permission prompt: iOS native style
Button: iOS button appearance
Icons: System icon support
Colors: Full RGB support
```

### Web
```
Permission prompt: Browser dialog
Button: CSS styling
Icons: Font icons or SVG
Colors: Full CSS support
```

---

**Last Updated**: November 19, 2025
**Version**: 2.0.0
