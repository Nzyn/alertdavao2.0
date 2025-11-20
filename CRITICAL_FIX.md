# 🚨 CRITICAL FIX - Barangays Not Loading

## The Problem

The barangays aren't loading because **Laravel backend is NOT running on port 3000**!

You were trying to use a Node.js backend, but the actual API is a **Laravel backend** in the `AdminSide/admin` directory.

### What You Have:

```
alertdavao2.0/
├── AdminSide/
│   └── admin/          ← LARAVEL BACKEND (api.php routes here)
│       ├── app/
│       ├── routes/
│       │   └── api.php    ← Has /api/barangays route
│       └── .env
└── UserSide/           ← React Native App
```

## The Solution

### Step 1: Stop Any Node.js Backend

Press Ctrl+C in any backend terminal. The Node backend is wrong.

### Step 2: Run the Laravel Backend

**In a new terminal:**

```bash
cd alertdavao2.0/AdminSide/admin
php artisan serve
```

**Expected output:**
```
Laravel development server started on [http://127.0.0.1:8000]
```

**Wait for the "Laravel development server started" message.**

### Step 3: The Backend is Now on Port 8000, Not 3000!

**Update `.env.local` in UserSide:**

```
EXPO_PUBLIC_BACKEND_URL=http://192.168.1.4:8000
```

### Step 4: Restart Expo

In the Expo terminal:
1. Press Ctrl+C
2. Run `npx expo start`
3. Wait for QR code

### Step 5: Reload App

Scan the QR code again or press 'r' in Expo terminal.

**NOW the barangays should load!**

---

## Quick Start Guide (Correct Way)

### Every Time You Develop:

**Terminal 1 - Laravel Backend:**
```bash
cd alertdavao2.0/AdminSide/admin
php artisan serve
# Waits for HTTP requests
```

**Terminal 2 - Expo Frontend:**
```bash
cd alertdavao2.0/UserSide
npx expo start
# Shows QR code
```

**Phone - Expo Go:**
1. Open Expo Go app
2. Scan QR code
3. App connects to Laravel on http://192.168.1.4:8000

---

## Updated .env.local

Update your `UserSide/.env.local` to:

```
EXPO_PUBLIC_BACKEND_URL=http://192.168.1.4:8000
```

This tells the app where to find the Laravel API endpoints.

---

## API Endpoints Available

These are the actual endpoints provided by Laravel:

```
GET  /api/barangays                        → Get all barangays
GET  /api/users                            → Get all users
POST /api/reports                          → Submit a report
GET  /api/reports                          → Get all reports
GET  /api/test                             → Test connection
```

All of these are served by the Laravel backend on port 8000.

---

## Important Environment Variables

### Laravel (`AdminSide/admin/.env`)
- `APP_PORT=8000` (or use `php artisan serve` default)
- `DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`
- `DB_CONNECTION=mysql`

### React Native (`UserSide/.env.local`)
- `EXPO_PUBLIC_BACKEND_URL=http://192.168.1.4:8000`

Make sure these match!

---

## Troubleshooting

### Still seeing "Cannot GET /api/barangays"?

1. ✅ Stop any Node backends (Ctrl+C)
2. ✅ Start Laravel: `php artisan serve` in AdminSide/admin
3. ✅ Wait for "development server started" message
4. ✅ Update `.env.local` to port 8000
5. ✅ Restart Expo (Ctrl+C, `npx expo start`)
6. ✅ Reload app (scan QR or press 'r')

### Laravel won't start?

```bash
cd alertdavao2.0/AdminSide/admin

# Try clearing cache
php artisan config:clear
php artisan cache:clear

# Then start
php artisan serve
```

### Still getting network error?

```bash
# 1. Test endpoint in browser
http://192.168.1.4:8000/api/barangays

# 2. Check if you see JSON (not error page)
# If yes → Backend is fine, check .env.local
# If no → Backend not responding, restart Laravel
```

---

##Summary

| Before | After |
|--------|-------|
| Trying to use Node backend | Use Laravel backend ✓ |
| Port 3000 | Port 8000 ✓ |
| `/api/barangays` returned 404 | `/api/barangays` returns JSON ✓ |
| Barangays not loading | Barangays load ✓ |

The key difference: **This project uses Laravel on port 8000, not a Node backend on port 3000.**

---

## Next Steps

1. ✅ Stop Node backend (Ctrl+C)
2. ✅ Start Laravel backend (`php artisan serve` in AdminSide/admin)
3. ✅ Update `.env.local` to `http://192.168.1.4:8000`
4. ✅ Restart Expo (`npx expo start`)
5. ✅ Reload app
6. ✅ Barangays should now load!

Good luck! 🚀
