# AlertDavao 2.0 - Quick Start Guide

## Prerequisites

### Required Software:
- ✅ Node.js (for UserSide backend)
- ✅ PHP 8.3+ with pdo_mysql extension (for AdminSide)
- ✅ MySQL/MariaDB (for database)
- ✅ Composer (for Laravel dependencies)
- ✅ Expo CLI (for React Native app)

## Getting Started

### 1. Start UserSide Backend

```bash
cd UserSide
node backends/server.js
```

**Expected output:**
```
🚀 Server running at http://localhost:3000
```

**Or use the batch file (Windows):**
```bash
cd UserSide
start-backend.bat
```

### 2. Start UserSide Mobile App

```bash
cd UserSide
npm start
```

Then:
- Press `a` for Android emulator
- Press `i` for iOS simulator
- Scan QR code with Expo Go app on physical device

**The app will automatically detect and connect to the backend!**

### 3. Start AdminSide (Laravel)

```bash
cd AdminSide/admin
php artisan serve
```

**Expected output:**
```
Laravel development server started: http://127.0.0.1:8000
```

Access at: http://localhost:8000

## Troubleshooting

### UserSide: "Failed to connect to server"

**Check 1: Is backend running?**
```bash
cd UserSide
node backends/server.js
```

**Check 2: Check firewall**
- Allow Node.js through Windows Firewall
- Ensure port 3000 is open

**Check 3: Same network**
- Phone and computer must be on same WiFi
- Some public/corporate WiFi block local connections

**Check 4: View detection logs**
Look for these in console:
```
🔧 Backend Configuration:
   Platform: android
   Backend URL: http://10.0.2.2:3000
✅ Auto-detected backend URL: http://192.168.1.42:3000
```

### AdminSide: "could not find driver"

**Fix: Enable pdo_mysql**
```powershell
cd AdminSide
.\enable-pdo-mysql.ps1
```

**Verify:**
```bash
php -m | findstr pdo_mysql
```

Should show `pdo_mysql`

### AdminSide: Database connection error

**Check 1: MySQL running**
- Start MySQL service
- Verify with phpMyAdmin or MySQL Workbench

**Check 2: Database credentials**
Edit `AdminSide/admin/.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alertdavao
DB_USERNAME=root
DB_PASSWORD=1234
```

**Check 3: Database exists**
```bash
cd AdminSide/admin
php artisan migrate
```

## Common Commands

### UserSide Commands

```bash
# Install dependencies
npm install

# Start backend server
node backends/server.js

# Start mobile app
npm start

# Run on Android
npm run android

# Run on iOS
npm run ios

# Run on web
npm run web

# Install new package
npm install package-name
```

### AdminSide Commands

```bash
# Install dependencies
composer install
npm install

# Database migrations
php artisan migrate

# Fresh database (WARNING: deletes all data)
php artisan migrate:fresh

# Database seeding
php artisan db:seed

# Start development server
php artisan serve

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Generate application key
php artisan key:generate
```

## Network Configuration

### How Auto-Detection Works

The app automatically detects the best backend URL:

**For Emulators:**
- Android Emulator: `http://10.0.2.2:3000`
- iOS Simulator: `http://localhost:3000`

**For Physical Devices:**
- Scans common network IPs (192.168.1.x, 192.168.0.x, etc.)
- Tests each URL with 3-second timeout
- Uses first working connection

**Backend Server:**
- Already configured to listen on `0.0.0.0:3000`
- Accessible from any device on same network

### Manual Override (If Needed)

If auto-detection fails, you can manually specify in `UserSide/utils/networkUtils.ts`:

```typescript
// Add your specific IP to candidates
const candidates = [
  'http://YOUR_IP_HERE:3000',
  // ... other candidates
];
```

## Development Workflow

### Typical Development Session:

1. **Start Backend:**
   ```bash
   cd UserSide
   node backends/server.js
   ```

2. **Start App (in new terminal):**
   ```bash
   cd UserSide
   npm start
   ```

3. **Start AdminSide (in new terminal):**
   ```bash
   cd AdminSide/admin
   php artisan serve
   ```

Now you have:
- UserSide backend API: http://localhost:3000
- UserSide mobile app: Expo Dev Tools
- AdminSide web: http://localhost:8000

### Testing Changes:

**UserSide:**
- Mobile app hot-reloads automatically
- Backend requires restart after changes

**AdminSide:**
- Blade views hot-reload
- PHP controllers/models require page refresh
- Config changes require `php artisan config:clear`

## Port Usage

- **3000**: UserSide backend (Node.js/Express)
- **8000**: AdminSide web (Laravel)
- **3306**: MySQL database
- **19000+**: Expo development server

Make sure these ports are available!

## Environment Files

### UserSide
No .env file needed - network auto-detection handles it!

### AdminSide
`AdminSide/admin/.env` contains:
- Database credentials
- App configuration
- API keys (if any)

**Never commit `.env` to git!**

## Next Steps

1. ✅ Backend is running
2. ✅ App connects automatically
3. ✅ Database is configured
4. 📱 Test registration and login
5. 🎨 Start developing features!

## Need Help?

Check these files:
- `FIXES_APPLIED.md` - Detailed fix documentation
- `DOCUMENTATION.md` - Project documentation
- `UserSide/README.md` - UserSide specific docs
- `AdminSide/admin/README.md` - AdminSide specific docs

---

**Happy Coding! 🚀**
