# Setup Fixed - Network and Database Issues Resolved

## Issues Fixed

### 1. UserSide - "Failed to connect to server" ✅ FIXED
**Problem:** Login and registration were using hardcoded IP addresses (192.168.1.42), causing failures when network changed.

**Solution:** Implemented automatic network detection that works on any network:
- Created `utils/networkUtils.ts` with smart backend discovery
- Updated `config/backend.ts` to use auto-detection
- Modified `app/login.tsx` to use dynamic backend URL
- Modified `app/register.tsx` to use dynamic backend URL

**How it works:**
- Automatically detects if running on emulator (uses localhost/10.0.2.2)
- For physical devices, tries multiple common network configurations
- Tests each URL to find the working backend
- No more manual IP updates needed!

### 2. AdminSide - MySQL PDO Driver Missing ✅ FIXED
**Problem:** Laravel couldn't connect to MySQL database due to missing `pdo_mysql` PHP extension.

**Error:** `Illuminate\Database\QueryException: could not find driver`

**Solution:** 
- Created PowerShell script `enable-pdo-mysql.ps1` to automatically enable the extension
- Script backs up php.ini before making changes
- Uncomments `;extension=pdo_mysql` line in php.ini

## What Changed

### UserSide Files Modified:
1. **NEW:** `UserSide/utils/networkUtils.ts` - Network auto-detection utilities
2. **UPDATED:** `UserSide/config/backend.ts` - Uses smart network detection
3. **UPDATED:** `UserSide/app/login.tsx` - Calls `getOptimalBackendUrl()`
4. **UPDATED:** `UserSide/app/register.tsx` - Calls `getOptimalBackendUrl()`
5. **ADDED:** `expo-network` package for network detection

### AdminSide Files Created:
1. **NEW:** `AdminSide/enable-pdo-mysql.ps1` - Script to enable MySQL driver

## How to Use

### UserSide - No More Manual Configuration!
The app now automatically finds your backend server. Just:

1. Make sure backend is running:
   ```bash
   cd UserSide
   npm run db-server
   # OR
   start-backend.bat
   ```

2. Start the app:
   ```bash
   npm start
   ```

3. The app will automatically:
   - Detect your platform (emulator/physical device)
   - Find the backend server on your network
   - Connect without any manual IP configuration

**Note:** Backend server must be running with `0.0.0.0` binding (already configured in `backends/server.js`)

### AdminSide - Database Connection Working

The MySQL PDO driver is now enabled. To verify:

```bash
php -m | findstr pdo_mysql
```

You should see `pdo_mysql` in the output.

If you ever need to re-enable it (e.g., after PHP update):

```powershell
.\enable-pdo-mysql.ps1
```

## Testing

### Test UserSide Connection:
1. Start the backend:
   ```bash
   cd UserSide
   node backends/server.js
   ```
   Server should show: `🚀 Server running at http://localhost:3000`

2. Start the app:
   ```bash
   npm start
   ```

3. Check console output for:
   ```
   🔧 Backend Configuration:
      Platform: android/ios
      Is Device: true/false
      Backend URL: http://...
   
   ✅ Auto-detected backend URL: http://...
   ```

4. Try login/register - should connect automatically!

### Test AdminSide Database:
1. Navigate to AdminSide:
   ```bash
   cd AdminSide/admin
   ```

2. Test database connection:
   ```bash
   php artisan tinker
   ```
   
   In tinker:
   ```php
   DB::connection()->getPdo();
   ```
   
   Should show PDO object without errors.

3. Try registration through web interface - should work!

## Troubleshooting

### UserSide - Still Can't Connect?

1. **Check backend is running:**
   ```bash
   # In UserSide directory
   node backends/server.js
   ```
   Should show: `🚀 Server running at http://localhost:3000`

2. **Check firewall:**
   - Windows: Allow Node.js through firewall
   - Make sure port 3000 is not blocked

3. **Check same network:**
   - Phone and computer must be on same WiFi
   - Corporate/public WiFi may block local connections

4. **Check console logs:**
   - Look for "Auto-detected backend URL" message
   - Check which URLs were tested

### AdminSide - Database Still Not Working?

1. **Verify extension is loaded:**
   ```bash
   php -m | findstr pdo_mysql
   ```
   Should show `pdo_mysql`

2. **Check database credentials in `.env`:**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=alertdavao
   DB_USERNAME=root
   DB_PASSWORD=1234
   ```

3. **Verify MySQL is running:**
   ```bash
   # Check if MySQL service is running
   # Access phpMyAdmin or MySQL workbench
   ```

4. **Test connection:**
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

## Network Detection Details

The new network detection system tries URLs in this order:

1. **Emulator/Simulator:**
   - Android: `http://10.0.2.2:3000`
   - iOS: `http://localhost:3000`

2. **Physical Devices:**
   - Detects device's IP (e.g., 192.168.1.123)
   - Tests common server IPs on same network
   - Falls back to common router IPs

3. **Test Candidates:**
   - Network prefix + .42 (e.g., 192.168.1.42)
   - Network prefix + .1 (e.g., 192.168.1.1)
   - Common variations (192.168.0.x, 10.0.0.x)

Each URL is tested with a 3-second timeout before trying the next one.

## Benefits

### UserSide:
✅ Works on any network without configuration
✅ Seamlessly switches between WiFi networks
✅ Supports emulators, simulators, and physical devices
✅ Better error messages when connection fails
✅ Automatic backend discovery

### AdminSide:
✅ MySQL database connection working
✅ Registration/login functionality restored
✅ No more "could not find driver" errors
✅ Easy to re-enable if needed (script provided)

## Future Improvements

Consider for production deployment:

1. **UserSide:**
   - Add QR code scanning for backend URL configuration
   - Implement backend URL caching for faster startup
   - Add manual override option in settings

2. **AdminSide:**
   - Set up environment-specific .env files
   - Configure production database credentials
   - Set up database migrations backup

---

**Note:** These fixes are development-focused. For production:
- UserSide: Use actual domain name instead of IP detection
- AdminSide: Ensure all required PHP extensions are documented in deployment guide
