# 📱 How to Test on Physical Device (Expo Go)

## ✅ Your Current Setup

**Your Computer's IP Address**: `192.168.1.42`  
**Backend Port**: `3000`  
**Backend URL**: `http://192.168.1.42:3000`

This has been automatically configured in `config/backend.ts`

---

## 🔧 Quick Setup Steps

### 1. **Make Sure Backend is Running**
```bash
cd UserSide\backends
node server.js
```

You should see: `🚀 Server running at http://localhost:3000`

### 2. **Check Windows Firewall**

The backend server must be accessible from your phone. Run this in PowerShell as Administrator:

```powershell
# Allow Node.js through Windows Firewall
New-NetFirewallRule -DisplayName "Node.js Server" -Direction Inbound -Program "C:\Program Files\nodejs\node.exe" -Action Allow
```

Or manually:
1. Open **Windows Defender Firewall**
2. Click **Advanced settings**
3. Click **Inbound Rules** → **New Rule**
4. Choose **Program** → Browse to `C:\Program Files\nodejs\node.exe`
5. Allow the connection on **Private** and **Public** networks

### 3. **Verify Same WiFi Network**

- Your computer and phone **MUST** be on the same WiFi network
- Check your phone's WiFi settings
- Check your computer's WiFi settings
- They should show the same network name

### 4. **Test Backend Connection**

From your phone's browser, visit:
```
http://192.168.1.42:3000/api/test-connection
```

You should see: `{"success":true,"message":"Database connection successful"}`

If you can't access it:
- ❌ Firewall is blocking
- ❌ Different WiFi networks
- ❌ Backend not running

### 5. **Start Expo App**

```bash
cd UserSide
npm start
```

Press `w` for web or scan QR code with Expo Go app

---

## 🔄 If Your IP Address Changes

Your computer's IP address might change if you:
- Reconnect to WiFi
- Restart your router
- Switch networks

### To Find Your New IP:

**Windows (PowerShell/CMD)**:
```bash
ipconfig
```
Look for "IPv4 Address" under your WiFi adapter (usually something like `192.168.x.x`)

**Mac/Linux**:
```bash
ifconfig
```
or
```bash
ip addr
```

### Update the Configuration:

1. Open `UserSide/config/backend.ts`
2. Find this line:
   ```typescript
   const LOCAL_IP = '192.168.1.42';
   ```
3. Replace with your new IP address
4. Restart the Expo app (press `r` in terminal)

---

## 🐛 Troubleshooting

### "Network request failed"
- ✅ Backend is running
- ✅ Firewall allows Node.js
- ✅ Same WiFi network
- ✅ Correct IP in `backend.ts`

### Backend responds but database fails
- ✅ MySQL is running on your computer
- ✅ Database "alertdavao" exists
- ✅ MySQL credentials correct in `backends/db.js`

### Can't scan QR code
- Make sure your phone's Expo Go app is updated
- Try pressing `w` to test on web first
- Manually type the URL shown in terminal into Expo Go

---

## 📝 Current Configuration Files

### `config/backend.ts`
```typescript
const LOCAL_IP = '192.168.1.42'; // Your computer's IP
const BACKEND_PORT = 3000;
```

### Services Using This Config
- ✅ `services/directDbService.ts` - Database operations
- ✅ `services/reportService.ts` - Report submissions

### Platform-Specific Behavior
- **Web**: Uses `http://localhost:3000`
- **Expo Go (Physical Device)**: Uses `http://192.168.1.42:3000`
- **iOS Simulator**: Uses `http://localhost:3000`
- **Android Emulator**: Uses `http://10.0.2.2:3000`

---

## ✅ Success Indicators

When everything is configured correctly:

1. **Backend Terminal Shows**:
   ```
   🚀 Server running at http://localhost:3000
   ```

2. **Expo Terminal Shows**:
   ```
   🔧 Backend Configuration:
      Platform: ios/android
      Is Device: true
      Backend URL: http://192.168.1.42:3000
      API URL: http://192.168.1.42:3000/api
   ```

3. **App Console Shows** (when you login/register):
   ```
   ✅ MySQL connection successful
   ✅ User saved to alertdavao.users successfully
   ```

4. **No "Network request failed" errors** ✨

---

**Ready to test!** 🚀

Open Expo Go on your phone and scan the QR code!
