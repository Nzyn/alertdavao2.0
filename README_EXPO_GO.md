# AlertDavao 2.0 - Expo Go Setup (Fixed!)

## 🎯 Quick Start (DO THIS NOW)

### Important Discovery
Your app uses a **Laravel backend on port 8000**, NOT a Node.js backend on port 3000!

### Step 1: Close Any Node Backends
Stop any running node processes. This backend won't work.

### Step 2: Start Laravel Backend
```bash
cd alertdavao2.0/AdminSide/admin
php artisan serve
```

Wait for:
```
Laravel development server started on [http://127.0.0.1:8000]
```

### Step 3: Update Backend URL
`.env.local` has been updated to:
```
EXPO_PUBLIC_BACKEND_URL=http://192.168.1.4:8000
```

### Step 4: Start Expo
```bash
cd alertdavao2.0/UserSide
npx expo start
```

### Step 5: Scan QR Code
Open Expo Go app on your phone and scan the QR code.

**Done!** Barangays should now load.

---

## 🚀 Easy Start Scripts (Windows)

Double-click these files to start everything:

### Backend
```
START_BACKEND.bat
```
- Installs dependencies if needed
- Starts Laravel on http://192.168.1.4:8000

### Frontend
```
START_EXPO.bat
```
- Installs dependencies if needed
- Starts Expo development server

---

## 📝 How to Set Up Every Time

### Terminal 1 - Backend
```bash
# In root directory
cd alertdavao2.0/AdminSide/admin
php artisan serve
```

**Keep this running. Ctrl+C stops it.**

### Terminal 2 - Frontend
```bash
# In root directory
cd alertdavao2.0/UserSide
npx expo start
```

**Press 'w' for web, 'i' for iOS, 'a' for Android emulator, or scan QR with Expo Go**

---

## 🔧 Configuration

### `.env.local` (UserSide)
```
EXPO_PUBLIC_BACKEND_URL=http://192.168.1.4:8000
```

- Port **8000** is Laravel
- IP **192.168.1.4** is your computer on local network

### `.env` (AdminSide/admin)
Database connection - check it's configured:
```
DB_HOST=127.0.0.1
DB_USER=root
DB_PASSWORD=your_password
DB_NAME=alertdavao2
DB_CONNECTION=mysql
```

---

## 🧪 Testing

### Test 1: Backend Working
Open in browser:
```
http://192.168.1.4:8000/api/barangays
```

Should see JSON data like:
```json
[
  {"location_id": 1, "barangay": "Barangay Name", "latitude": 7.0833, "longitude": 125.6389},
  ...
]
```

### Test 2: App Connected
Check app logs for:
```
📍 Backend URL: http://192.168.1.4:8000
✅ Barangays fetched successfully
```

---

## 📱 Expo Go on Phone

1. Install **Expo Go** app (App Store or Google Play)
2. Make sure phone is on same WiFi as computer
3. Run `npx expo start` in UserSide terminal
4. Scan the QR code with Expo Go
5. App loads on your phone!

### Reload/Restart
- Shake phone → "Show console" to see logs
- Shake phone → "Reload" to restart app
- Or press 'r' in terminal

---

## 🔌 Network Requirements

- ✅ Phone and computer on **same WiFi**
- ✅ Laravel backend running on port 8000
- ✅ Expo running on your computer
- ✅ `.env.local` has correct IP (192.168.1.4)

### Find Your IP
```bash
ipconfig
# Look for "IPv4 Address" like 192.168.1.4
```

If different, update `.env.local`:
```
EXPO_PUBLIC_BACKEND_URL=http://YOUR_IP:8000
```

Then restart Expo.

---

## 🐛 Troubleshooting

### "Network request failed" Error

**99% of the time:**
1. ✅ Is Laravel running? (should see "Laravel development server started")
2. ✅ Is Expo running? (should show QR code)
3. ✅ Are both on same WiFi? (phone and computer)
4. ✅ Is `.env.local` port 8000? (not 3000)
5. ✅ Did you restart Expo after changing `.env.local`? (Ctrl+C, then `npx expo start`)

### Barangays Not Loading

1. Check Laravel is responding:
   ```bash
   # In browser on computer
   http://localhost:8000/api/barangays
   # Should see JSON
   ```

2. Check `.env.local`:
   ```bash
   # In UserSide directory
   cat .env.local
   # Should show: EXPO_PUBLIC_BACKEND_URL=http://192.168.1.4:8000
   ```

3. Restart Expo:
   ```bash
   # In UserSide terminal, press Ctrl+C
   npx expo start
   ```

### Login Fails But Barangays Loads

Laravel might not have login endpoint. Check:
```
AdminSide/admin/routes/api.php
```

You might need to create login route:
```php
Route::post('/login', [AuthController::class, 'login']);
```

### "Cannot GET /api/barangays"

This means Laravel isn't running or the route doesn't exist.

**Check Laravel is running:**
```bash
php artisan serve
# Should show "development server started"
```

**Check route exists:**
```bash
php artisan route:list | grep barangays
# Should show: GET /api/barangays → BarangayController@getAll
```

---

## 📦 Architecture

```
Your Computer
├── Laravel Backend (port 8000)
│   ├── AdminSide/admin
│   ├── Routes: /api/barangays, /api/users, /api/reports, etc
│   └── Database: MariaDB
│
└── Expo Dev Server
    ├── UserSide
    ├── Connects to Laravel on 192.168.1.4:8000
    └── Serves React Native code

Your Phone (Same WiFi)
└── Expo Go App
    ├── Scans QR code
    ├── Downloads app from Expo server
    └── Connects to Laravel API
```

---

## 🎓 Key Differences from Node Backend

| Node.js (Wrong) | Laravel (Correct) |
|-----------------|-------------------|
| Port 3000 | Port 8000 |
| `npm start` | `php artisan serve` |
| Environment: `.env` | Environment: `.env` |
| Routes: Node Express | Routes: Laravel |
| No API routes at /api | API routes at /api/* |

**This project uses LARAVEL, not Node.js!**

---

## ✅ Checklist

- [ ] Stopped any Node.js backend processes
- [ ] Started Laravel: `php artisan serve` in AdminSide/admin
- [ ] Updated `.env.local` to port 8000
- [ ] Started Expo: `npx expo start` in UserSide
- [ ] Phone is on same WiFi as computer
- [ ] Can access `http://192.168.1.4:8000/api/barangays` in browser
- [ ] Scanned QR code with Expo Go
- [ ] Barangays loaded in app

When all checked ✅ → **Should be working!**

---

## 📞 Support

See these files for help:
- `CRITICAL_FIX.md` - Why Laravel on port 8000
- `TROUBLESHOOTING.md` - Detailed troubleshooting
- `AdminSide/admin/routes/api.php` - Available endpoints

Good luck! 🚀
