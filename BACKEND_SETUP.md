# Backend API Server Setup

## Your Configuration
- **PC IP Address**: `192.168.1.4`
- **Backend Port**: `3000`
- **Backend URL**: `http://192.168.1.4:3000`
- **API Base URL**: `http://192.168.1.4:3000/api`

## To Start the Backend Server

From the `UserSide` directory, run:

```bash
node backends/server.js
```

Or use npm script (if configured):
```bash
npm run start-db
```

The server will listen on:
- `http://localhost:3000` (from your PC)
- `http://192.168.1.4:3000` (from your mobile device or another PC)
- `http://10.0.2.2:3000` (from Android emulator)

## Files Updated with Your IP Address

The following files have been updated to use `192.168.1.4` instead of the generic `192.168.1.42`:

1. **`UserSide/utils/networkUtils.ts`** - Default sync URL and candidates list
2. **`UserSide/app/(tabs)/login.tsx`** - Login endpoint
3. **`UserSide/app/(tabs)/register.tsx`** - Registration endpoint
4. **`UserSide/app/(tabs)/location.tsx`** - Geocoding endpoint
5. **`UserSide/app/(tabs)/ChatScreen.tsx`** - Messaging typing status endpoints
6. **`UserSide/app/(tabs)/profile.tsx`** - Verification upload endpoint

## How to Run Expo with Backend

### On Android Physical Device (via Expo Go):
1. Make sure your PC and device are on the same WiFi network
2. Start the backend server on your PC: `node backends/server.js`
3. Run Expo: `expo start` from UserSide directory
4. Scan the QR code with Expo Go app
5. The app will use the auto-detection in `networkUtils.ts` which will test and connect to `192.168.1.4:3000`

### On Android Emulator:
1. Start the backend server
2. Run `expo start --android`
3. The app will automatically use `http://10.0.2.2:3000` (special Android emulator localhost)

### On iOS Simulator:
1. Start the backend server
2. Run `expo start --ios`
3. The app will automatically use `http://localhost:3000`

## Testing the Connection

Once the server is running, test it:

```bash
curl http://192.168.1.4:3000/api/test-connection
```

Expected response:
```json
{
  "success": true,
  "message": "Connected to alertdavao database successfully",
  "database": "alertdavao",
  "timestamp": "2025-11-19T00:00:00.000Z"
}
```

## Environment Variables (if needed)

Edit `UserSide/backends/server.js` to configure:
- **MySQL Host**: `localhost` (line 15)
- **MySQL Port**: `3306` (line 16)
- **MySQL User**: `root` (line 17)
- **MySQL Password**: `1234` (line 18)
- **Database**: `alertdavao` (line 19)

## Auto-Detection Logic

The app uses smart network detection (`networkUtils.ts`):

1. **Web**: Uses `http://localhost:3000`
2. **iOS Simulator**: Uses `http://localhost:3000`
3. **Android Emulator**: Uses `http://10.0.2.2:3000`
4. **Physical Device**: 
   - First tries `http://192.168.1.4:3000` (your PC)
   - Fallback to other common network IPs
   - Uses async detection for best connection

## Backend API Routes

The server provides these endpoints:

### Authentication
- `POST /register` - User registration
- `POST /login` - User login

### User Profile
- `GET /api/users/:id` - Get user by ID
- `POST /api/users/upsert` - Create/update user
- `PATCH /api/users/:id/address` - Update user location
- `GET /api/test-connection` - Test database connection

### Messages
- `GET /api/messages/conversations/:userId` - Get user conversations
- `POST /api/messages` - Send message
- `GET /api/messages/:userId/:otherUserId` - Get messages between users
- `POST /api/messages/typing` - Update typing status
- `GET /api/messages/typing-status/:senderId/:receiverId` - Check typing status

### Reports
- `POST /api/reports` - Submit crime report
- `GET /api/reports` - Get all reports
- `GET /api/reports/user/:userId` - Get user's reports

### Verification
- `POST /api/verification/submit` - Submit verification
- `POST /api/verification/upload` - Upload verification document
- `GET /api/verification/status/:userId` - Get verification status

### Geocoding
- `POST /api/geocode` - Convert address to coordinates

## Troubleshooting

### Port 3000 Already in Use
```bash
netstat -ano | findstr :3000
taskkill /PID <PID> /F
```

### MySQL Connection Failed
- Ensure MySQL server is running
- Check credentials in `backends/server.js`
- Verify database `alertdavao` exists

### Expo App Can't Connect
- Verify backend server is running
- Check both PCs are on same network
- Try pinging: `ping 192.168.1.4` from your device
- Check Windows firewall allows port 3000

## Next Steps

1. ✅ Backend server is running
2. ✅ API endpoints configured
3. Run: `expo start` and test the app
