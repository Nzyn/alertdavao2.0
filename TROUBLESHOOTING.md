# AlertDavao 2.0 - Troubleshooting Guide

Quick reference for common issues and their solutions.

---

## 🔴 UserSide: "Failed to connect to server"

### Quick Checks:

**1. Is the backend running?**
```bash
cd UserSide
node backends/server.js
```
✅ Should show: `🚀 Server running at http://localhost:3000`

**2. Check app console logs**
Look for:
```
🔧 Backend Configuration:
   Platform: android
   Backend URL: http://10.0.2.2:3000
✅ Auto-detected backend URL: http://192.168.1.42:3000
```

**3. Firewall check**
- Windows: Settings → Windows Security → Firewall & network protection
- Allow Node.js through firewall
- Ensure port 3000 is open

**4. Network check**
- Phone and computer on same WiFi?
- Try visiting `http://YOUR_COMPUTER_IP:3000` in phone's browser
- Corporate/public WiFi might block local connections

### Solutions:

**If backend not running:**
```bash
cd UserSide
node backends/server.js
```

**If firewall blocking:**
```powershell
# Run as Administrator
netsh advfirewall firewall add rule name="Node.js" dir=in action=allow program="C:\Program Files\nodejs\node.exe" enable=yes
```

**If network isolated:**
- Use same WiFi network
- Disable VPN if active
- Try mobile hotspot
- Use Android emulator (auto-connects via 10.0.2.2)

**If auto-detection fails:**
Edit `UserSide/utils/networkUtils.ts`, add your IP at top of candidates:
```typescript
const candidates = [
  'http://YOUR_IP:3000', // Add this
  // ... existing candidates
];
```

---

## 🔴 AdminSide: "could not find driver"

### Quick Checks:

**1. Is pdo_mysql enabled?**
```bash
php -m | findstr pdo_mysql
```
✅ Should show: `pdo_mysql`

**2. If not enabled:**
```powershell
cd AdminSide
.\enable-pdo-mysql.ps1
```

**3. Verify after enabling:**
```bash
php -m | findstr pdo_mysql
```

### Solutions:

**If script fails:**
Manually edit php.ini:
```bash
# Find php.ini location
php --ini

# Edit the file and find this line:
;extension=pdo_mysql

# Remove the semicolon:
extension=pdo_mysql

# Save and restart terminal
```

**If still not working:**
Check php.ini path is correct:
```bash
php --ini
```
Make sure you're editing the "Loaded Configuration File" not the default path.

---

## 🔴 AdminSide: Database Connection Failed

### Quick Checks:

**1. Is MySQL running?**
- Check MySQL service in Windows Services
- Try accessing phpMyAdmin
- Check MySQL Workbench connection

**2. Check database credentials**
Edit `AdminSide/admin/.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alertdavao
DB_USERNAME=root
DB_PASSWORD=1234
```

**3. Does database exist?**
```sql
-- In MySQL client:
SHOW DATABASES;
CREATE DATABASE IF NOT EXISTS alertdavao;
```

**4. Test connection**
```bash
cd AdminSide/admin
php artisan tinker
```
Then in tinker:
```php
DB::connection()->getPdo();
```

### Solutions:

**Start MySQL service:**
```bash
# Windows
net start MySQL80

# Or use XAMPP/WAMP control panel
```

**Create database:**
```bash
cd AdminSide/admin
php artisan migrate
```

**Reset database:**
```bash
php artisan migrate:fresh
```
⚠️ WARNING: This deletes all data!

**Clear Laravel cache:**
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🔴 UserSide: App Won't Start

### Quick Checks:

**1. Dependencies installed?**
```bash
cd UserSide
npm install
```

**2. Port 19000+ available?**
Expo uses ports 19000-19999. Close other Expo instances.

**3. Check for errors:**
```bash
npm start
```
Read error messages carefully.

### Solutions:

**Clear Expo cache:**
```bash
npx expo start -c
```

**Reinstall dependencies:**
```bash
rm -rf node_modules package-lock.json
npm install
```

**Clear watchman (if on Mac/Linux):**
```bash
watchman watch-del-all
```

---

## 🔴 AdminSide: Laravel Won't Start

### Quick Checks:

**1. Dependencies installed?**
```bash
cd AdminSide/admin
composer install
npm install
```

**2. .env file exists?**
```bash
# If not, copy from example:
cp .env.example .env
php artisan key:generate
```

**3. Port 8000 available?**
Another app might be using it.

### Solutions:

**Use different port:**
```bash
php artisan serve --port=8080
```

**Clear all caches:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

**Regenerate autoload:**
```bash
composer dump-autoload
```

---

## 🔴 Network Auto-Detection Not Working

### Manual Test:

**1. Get your computer's IP:**
```bash
# Windows
ipconfig
# Look for "IPv4 Address" under your WiFi adapter
```

**2. Test backend manually:**
```bash
# On phone's browser, visit:
http://YOUR_IP:3000
```
Should show backend response or "Cannot GET /"

**3. Add to candidates:**
Edit `UserSide/utils/networkUtils.ts`:
```typescript
const candidates = [
  'http://YOUR_IP:3000', // <-- Add this
  // ... rest
];
```

### Common IP Patterns:

- **192.168.1.x** - Most home routers
- **192.168.0.x** - Some home routers
- **10.0.0.x** - Some networks
- **172.16.x.x** - Enterprise networks

---

## 🔴 Physical Device Can't Connect

### Checklist:

- [ ] Backend is running
- [ ] Phone and computer on **same WiFi**
- [ ] Firewall allows Node.js
- [ ] Port 3000 is open
- [ ] No VPN active
- [ ] Not on public/corporate WiFi (they often block local connections)

### Test:

**1. Ping computer from phone:**
Use Network Utilities app on phone, ping your computer's IP

**2. Visit backend from phone browser:**
```
http://YOUR_COMPUTER_IP:3000
```

**3. If browser works but app doesn't:**
Check app console for auto-detection logs

### Alternative:

**Use Android Emulator:**
```bash
npm run android
```
Automatically uses `http://10.0.2.2:3000` which always works.

---

## 🔴 TypeScript Errors

### Quick Fix:

**1. Check imports:**
Make sure all imports are correct:
```typescript
import { getOptimalBackendUrl } from '../config/backend';
```

**2. Reinstall types:**
```bash
cd UserSide
npm install --save-dev @types/react @types/react-native
```

**3. Restart TypeScript server:**
In VSCode: `Ctrl+Shift+P` → "TypeScript: Restart TS Server"

---

## 🔴 Expo Go App Issues

### Common Problems:

**"Couldn't start project"**
- Clear Expo cache: `npx expo start -c`
- Restart Expo Go app
- Restart Metro bundler

**"Unable to resolve module"**
- Run `npm install`
- Clear cache and restart

**"Network error"**
- Check same WiFi network
- Try LAN connection type in Expo Dev Tools
- Try Tunnel connection (slower but more reliable)

---

## 📞 Still Having Issues?

### Collect This Information:

1. **Platform:** Android/iOS, Emulator/Physical
2. **Error Message:** Exact text
3. **Console Logs:** From app and backend
4. **Network:** WiFi name, IP addresses
5. **Versions:**
   ```bash
   node --version
   php --version
   npm --version
   ```

### Check These Files:

- `FIXES_APPLIED.md` - Detailed fix documentation
- `QUICK_START.md` - Setup and commands reference
- `FIXES_SUMMARY.md` - What was changed

### Enable Debug Mode:

**UserSide:**
Add console logs in `UserSide/config/backend.ts` to see detection process

**AdminSide:**
Check Laravel logs:
```bash
tail -f AdminSide/admin/storage/logs/laravel.log
```

---

## ✅ Verification Checklist

After fixing issues, verify:

- [ ] Backend starts: `node backends/server.js`
- [ ] Backend accessible: `curl http://localhost:3000`
- [ ] App starts: `npm start`
- [ ] Auto-detection works: Check console logs
- [ ] Login/Register works: Try both
- [ ] MySQL driver enabled: `php -m | findstr pdo_mysql`
- [ ] Database connects: `php artisan tinker` → `DB::connection()->getPdo()`
- [ ] Laravel starts: `php artisan serve`

---

**Remember:** Most issues are due to:
1. Backend not running
2. Firewall blocking
3. Different WiFi networks
4. Missing dependencies

Check these first! 🎯
