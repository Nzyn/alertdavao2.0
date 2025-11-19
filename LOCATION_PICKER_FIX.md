# Location Picker Fix - Use My Current Location

## Issue
The "Use my current location" button in the UserSide reporting feature had the following problems:

1. **Duplicate UI sections**: The location display was shown twice (in bottomControls and addressContainer), causing layout issues
2. **No feedback during loading**: The button didn't change text while fetching location
3. **No disabled state**: The button remained clickable while loading, potentially causing multiple requests

## Changes Made

### File: `UserSide/components/LocationPickerModal.tsx`

#### 1. Removed duplicate "Selected Address Display" section
- **Removed**: Lines that contained a duplicate `<View style={styles.addressContainer}>` section
- **Impact**: Fixes layout overlap and ensures the map takes proper space

#### 2. Enhanced button with loading state
- **Added**: `disabled={loading}` prop to the "Use Current Location" button
- **Added**: Conditional text that shows "Getting location..." while `loading === true`
- **Impact**: Provides clear user feedback when location is being fetched

#### 3. Kept single loading indicator
- Consolidated to one loading indicator in the bottomControls section
- Removed redundant loading display

## Testing
To verify the fix works:
1. Open the report crime screen in UserSide
2. Click "Use my current location" button
3. Button should:
   - Show "Getting location..." text while fetching
   - Be disabled (no double-clicks possible)
   - Update with the fetched address
   - Re-enable after location is obtained

## Code Quality
- No TypeScript errors
- Proper component structure maintained
- All JSX closing tags correct
- Consistent with existing styling patterns
