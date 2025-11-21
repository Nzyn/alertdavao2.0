# AlertDavao 2.0 - Complete Documentation

**Last Updated**: October 17, 2025  
**Version**: 2.0

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Quick Start Guide](#quick-start-guide)
3. [Installation & Setup](#installation--setup)
4. [Database Configuration](#database-configuration)
5. [Backend Architecture](#backend-architecture)
6. [Report Submission System](#report-submission-system)
7. [Media Upload & Evidence Management](#media-upload--evidence-management)
8. [User Profile & Address Management](#user-profile--address-management)
9. [Testing & Debugging](#testing--debugging)
10. [Troubleshooting Guide](#troubleshooting-guide)
11. [API Reference](#api-reference)

---

## Project Overview

AlertDavao 2.0 is a crime reporting system for Davao City, consisting of:
- **UserSide**: React Native mobile/web app (Expo)
- **AdminSide**: Laravel backend (optional, not used for user reports)

The application allows users to:
- Submit crime reports with details, location, and media evidence
- Track report history and status
- Manage user profiles
- Report anonymously if desired

---

## Quick Start Guide

### Prerequisites
- Node.js and npm installed
- MySQL database server
- Expo CLI (for React Native)

### Step 1: Install Backend Dependencies

```bash
cd UserSide\backends
npm install
```

This installs:
- `express` - Web framework
- `cors` - Cross-origin resource sharing
- `mysql2` - MySQL database driver
- `bcryptjs` - Password hashing
- `multer` - File upload handling

### Step 2: Configure Database

1. Create MySQL database:
```sql
CREATE DATABASE alertdavao;
```

2. Update credentials in `backends/db.js`:
```javascript
{
  host: "127.0.0.1",
  user: "root",
  password: "1234",  // Change to your password
  database: "alertdavao"
}
```

3. Create required tables (see Database Configuration section)

### Step 3: Start the Backend Server

**Option A: Using batch file (Windows)**
```bash
cd UserSide
start-backend.bat
```

**Option B: Using command line**
```bash
cd UserSide\backends
node server.js
```

Expected output:
```
🚀 Server running at http://localhost:3000
```

**Keep this terminal running!**

### Step 4: Start the React Native App

Open a **NEW** terminal:
```bash
cd UserSide
npm start
```

Press:
- `w` for web browser
- `a` for Android emulator
- `i` for iOS simulator

### Step 5: Test the Application

1. Navigate to Login page
2. Login with your credentials
3. Try submitting a report
4. Check your report history

---

## Installation & Setup

### Frontend Setup (UserSide)

```bash
cd UserSide
npm install
```

**Dependencies include**:
- Expo SDK
- React Navigation
- Axios for API calls
- AsyncStorage for local data
- Image Picker for media selection

### Backend Setup (UserSide)

```bash
cd UserSide\backends
npm install
```

**Server Configuration**:
- Port: 3000
- CORS enabled for development
- File upload limit: 25MB
- Static file serving for uploads

### AdminSide (Optional - Laravel)

```bash
cd AdminSide\admin
composer install
php artisan migrate
php artisan storage:link
php artisan serve
```

**Note**: The UserSide backend (Node.js) handles all user report operations independently.

---

## Database Configuration

### Users Table

```sql
CREATE TABLE users (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  firstname VARCHAR(50) NOT NULL,
  lastname VARCHAR(50) NOT NULL,
  contact VARCHAR(15) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  address TEXT,
  is_verified TINYINT(1) DEFAULT 0,
  profile_image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Reports Table

```sql
CREATE TABLE reports (
  report_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  location_id BIGINT UNSIGNED,
  title VARCHAR(255) NOT NULL,
  report_type VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  date_reported DATETIME NOT NULL,
  status VARCHAR(50) DEFAULT 'pending',
  is_anonymous BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (location_id) REFERENCES locations(location_id) ON DELETE SET NULL
);
```

### Locations Table

```sql
CREATE TABLE locations (
  location_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  barangay VARCHAR(255),
  latitude DOUBLE(15,8),
  longitude DOUBLE(15,8),
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL
);
```

### Report Media Table

```sql
CREATE TABLE report_media (
  media_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  report_id BIGINT UNSIGNED NOT NULL,
  media_url VARCHAR(255) NOT NULL,
  media_type VARCHAR(50) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (report_id) REFERENCES reports(report_id) ON DELETE CASCADE
);
```

### Database Setup Script

```bash
cd UserSide\backends
node checkDatabase.js
```

This script:
- Checks if tables exist
- Adds missing columns (like `address`)
- Tests database connectivity
- Validates schema

---

## Backend Architecture

### File Structure

```
UserSide/
├── backends/
│   ├── server.js              # Main Express server
│   ├── db.js                  # MySQL connection pool
│   ├── handleReport.js        # Report submission logic
│   ├── handleLogin.js         # Login authentication
│   ├── handleRegister.js      # User registration
│   ├── handleUserProfile.js   # User profile operations
│   └── package.json           # Backend dependencies
├── services/
│   ├── reportService.ts       # Report API client
│   ├── userService.ts         # User API client
│   └── directDbService.ts     # Direct database service
├── evidence/                  # Uploaded media files
└── app/
    └── (tabs)/
        ├── report.tsx         # Report submission form
        ├── history.tsx        # Report history
        ├── profile.tsx        # User profile
        └── edit-profile.tsx   # Profile editing
```

### Server Routes

**Port**: 3000

#### User Routes
```javascript
POST   /register                    // User registration
POST   /login                       // User login
GET    /api/users/:id              // Get user profile
POST   /api/users/upsert           // Update user profile
PATCH  /api/users/:id/address      // Update address only
```

#### Report Routes
```javascript
POST   /api/reports                // Submit new report
GET    /api/reports                // Get all reports (admin)
GET    /api/reports/user/:userId   // Get user's reports
```

#### Utility Routes
```javascript
GET    /api/test-connection        // Test database connection
POST   /api/query                  // Execute raw SQL (debug)
GET    /api/db-status             // Get database status
```

#### Static Files
```javascript
/evidence/:filename               // Access uploaded media
```

---

## Report Submission System

### Implementation Overview

The report submission feature allows users to submit detailed crime reports with optional media evidence and location data.

### Data Flow

```
1. User fills report form
   ↓
2. Frontend validates fields
   ↓
3. reportService.submitReport() called
   ↓
4. POST to http://localhost:3000/api/reports
   ↓
5. Backend receives request
   ↓
6. Multer processes file upload (if any)
   ↓
7. Database transaction starts
   ↓
8. Create location record → locations table
   ↓
9. Create report record → reports table
   ↓
10. Create media record → report_media table (if file uploaded)
    ↓
11. Transaction commits
    ↓
12. Success response sent
    ↓
13. Show success message
    ↓
14. Reset form & navigate to history
```

### Report Fields

| Field | Type | Required | Database Column |
|-------|------|----------|-----------------|
| Title | String | Yes | `reports.title` |
| Crime Types | Array | Yes | `reports.report_type` (comma-separated) |
| Description | Text | Yes | `reports.description` |
| Date/Time | DateTime | Yes | `reports.date_reported` |
| Location | Coordinates | No | `locations.latitude`, `locations.longitude` |
| Media | File | No | `report_media.media_url`, `report_media.media_type` |
| Anonymous | Boolean | No | `reports.is_anonymous` |

### Crime Types Available

- Theft/Robbery
- Assault/Physical Harm
- Domestic Violence
- Cybercrime
- Vandalism
- Illegal Drug
- Harassment/Stalking
- Child Abuse
- Fraud/Scamming
- Missing Person
- Others

### Submission Process

**Frontend** (`app/(tabs)/report.tsx`):
```typescript
const handleSubmit = async () => {
  // 1. Validate required fields
  if (!title || crimeTypes.length === 0 || !description || !incidentDate) {
    Alert.alert('Incomplete', 'Please fill in all required fields.');
    return;
  }

  // 2. Check user is logged in
  if (!user?.id) {
    Alert.alert('Error', 'You must be logged in to submit a report.');
    return;
  }

  // 3. Prepare report data
  const reportData = {
    title,
    crime_types: crimeTypes,
    description,
    incident_date: formattedDate,
    is_anonymous: isAnonymous,
    user_id: user.id,
    latitude: location?.latitude,
    longitude: location?.longitude,
    media: selectedMedia
  };

  // 4. Submit to backend
  const result = await reportService.submitReport(reportData);
  
  // 5. Handle response
  if (result) {
    Alert.alert('Success', 'Report submitted successfully!');
    resetForm();
    router.push('/history');
  }
};
```

**Backend** (`backends/handleReport.js`):
```javascript
async function submitReport(reportData, file) {
  const connection = await db.getConnection();
  
  try {
    await connection.beginTransaction();

    // 1. Create location record
    const [locationResult] = await connection.query(
      'INSERT INTO locations (barangay, latitude, longitude) VALUES (?, ?, ?)',
      [barangay, latitude, longitude]
    );

    // 2. Create report record
    const [reportResult] = await connection.query(
      'INSERT INTO reports (...) VALUES (...)',
      [user_id, location_id, title, report_type, description, date_reported, is_anonymous]
    );

    // 3. Create media record (if file uploaded)
    if (file) {
      await connection.query(
        'INSERT INTO report_media (report_id, media_url, media_type) VALUES (?, ?, ?)',
        [report_id, media_url, media_type]
      );
    }

    await connection.commit();
    return { success: true, report_id };
    
  } catch (error) {
    await connection.rollback();
    throw error;
  }
}
```

---

## Media Upload & Evidence Management

### Storage Configuration

**Location**: `UserSide/evidence/`

**File Naming**: `evidence-{timestamp}-{random}.{ext}`

**Example**: `evidence-1697456789012-987654321.jpg`

### Supported File Types

#### Images
- JPEG (.jpg, .jpeg) - `media_type: 'jpg'`
- PNG (.png) - `media_type: 'png'`
- GIF (.gif) - `media_type: 'gif'`

#### Videos
- MP4 (.mp4) - `media_type: 'mp4'`
- MOV (.mov) - `media_type: 'mov'`
- AVI (.avi) - `media_type: 'avi'`

### File Size Limit

**Maximum**: 25MB (25 * 1024 * 1024 bytes)

### File Upload Flow

```
1. User selects image/video via ImagePicker
   ↓
2. Frontend validates file size and type
   ↓
3. File converted to proper format for upload
   ↓
4. Appended to FormData with field name 'media'
   ↓
5. POSTed to /api/reports
   ↓
6. Multer intercepts 'media' field
   ↓
7. File saved to evidence/ folder
   ↓
8. Media URL stored in database: /evidence/{filename}
   ↓
9. File accessible via: http://localhost:3000/evidence/{filename}
```

### Multer Configuration

```javascript
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    const uploadDir = path.join(__dirname, "../evidence");
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir, { recursive: true });
    }
    cb(null, uploadDir);
  },
  filename: function (req, file, cb) {
    const timestamp = Date.now();
    const randomNum = Math.floor(Math.random() * 1000000000);
    const ext = path.extname(file.originalname);
    cb(null, `evidence-${timestamp}-${randomNum}${ext}`);
  }
});

const upload = multer({
  storage: storage,
  limits: { fileSize: 25 * 1024 * 1024 },
  fileFilter: function (req, file, cb) {
    const allowedTypes = /jpeg|jpg|png|gif|mp4|mov|avi/;
    const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
    const mimetype = allowedTypes.test(file.mimetype);
    
    if (mimetype && extname) {
      return cb(null, true);
    } else {
      cb(new Error("Only images and videos allowed!"));
    }
  }
});
```

### Database Storage

Media information is stored in `report_media` table:

```sql
INSERT INTO report_media (report_id, media_url, media_type)
VALUES (1, '/evidence/evidence-1697456789012.jpg', 'jpg');
```

### Viewing Evidence

Files are served statically via Express:

```javascript
app.use('/evidence', express.static(path.join(__dirname, '../evidence')));
```

Access URL: `http://localhost:3000/evidence/evidence-{timestamp}-{random}.{ext}`

---

## User Profile & Address Management

### Profile Features

- View and edit personal information
- Update contact details
- Manage home address
- Upload profile picture (future)
- Verification status

### Address Field Mapping

**Frontend field** → **Database column**:
- `address` → `users.address` (TEXT column)

### Update Profile Flow

1. User navigates to `/edit-profile`
2. Modifies address or other fields
3. Clicks "Save Changes"
4. Frontend calls `directDbService.insertOrUpdateUser()`
5. Backend receives POST `/api/users/upsert`
6. Database updated with new values
7. Success alert displayed
8. User redirected to `/profile`
9. Updated data displayed

### API Payload

```javascript
const dbPayload = {
  id: userData.id,
  firstname: userData.firstName,
  lastname: userData.lastName,
  email: userData.email,
  contact: userData.phone,
  address: userData.address,  // Saved to 'address' column
  is_verified: userData.isVerified ? 1 : 0,
  profile_image: userData.profileImage || null
};
```

### Backend Handler

```javascript
async function upsertUser(req, res) {
  const { id, firstname, lastname, email, contact, address, is_verified, profile_image } = req.body;
  
  // Check if user exists
  const [existing] = await db.query('SELECT * FROM users WHERE id = ?', [id]);
  
  if (existing.length > 0) {
    // Update existing user
    await db.query(
      'UPDATE users SET firstname=?, lastname=?, email=?, contact=?, address=?, is_verified=?, profile_image=? WHERE id=?',
      [firstname, lastname, email, contact, address, is_verified, profile_image, id]
    );
  } else {
    // Insert new user
    await db.query(
      'INSERT INTO users (...) VALUES (...)',
      [id, firstname, lastname, email, contact, address, is_verified, profile_image]
    );
  }
  
  res.json({ success: true, message: 'User profile saved' });
}
```

### User Authentication

Users are authenticated via login and their data is stored in AsyncStorage:

```typescript
// After successful login
await AsyncStorage.setItem('userData', JSON.stringify(user));

// Load user on app start
const userData = await AsyncStorage.getItem('userData');
const user = JSON.parse(userData);
```

---

## Testing & Debugging

### Test Report Submission

1. **Start backend**:
   ```bash
   cd UserSide\backends
   node server.js
   ```

2. **Start frontend**:
   ```bash
   cd UserSide
   npm start
   ```

3. **Submit test report**:
   - Title: "Test Report"
   - Crime Type: Select "Theft/Robbery"
   - Description: "Testing report submission"
   - Date: Select current date and time
   - Media: Optional - select a photo
   - Click "Submit Report"

4. **Expected console output**:

   **Frontend**:
   ```
   📎 Preparing media file for upload...
   ✅ Media file added to FormData
   🚀 Sending report to backend...
   Has media: YES
   ```

   **Backend**:
   ```
   📨 INCOMING REQUEST: POST /api/reports
   🎯 REPORT ENDPOINT HIT
   📦 AFTER MULTER: req.file exists? true
   ✅ Location created with ID: 1
   ✅ Report created with ID: 1
   📸 Processing file upload...
   ✅ Media uploaded successfully! Media ID: 1
   🎉 Report submitted successfully!
   ```

5. **Verify in database**:
   ```sql
   SELECT * FROM reports ORDER BY created_at DESC LIMIT 1;
   SELECT * FROM report_media ORDER BY created_at DESC LIMIT 1;
   ```

### Test Profile Update

1. Login with valid credentials
2. Navigate to Profile page
3. Click "Edit Profile"
4. Change the address field
5. Click "Save Changes"
6. Check console logs:
   ```
   💾 Saving user profile to alertdavao database...
   📍 Address to save: "Your new address"
   ✅ Database save completed successfully
   ```
7. Verify redirect to profile page
8. Verify address updated on screen
9. Check database:
   ```sql
   SELECT address FROM users WHERE id = YOUR_ID;
   ```

### Debugging Tools

**Backend Test Scripts**:
```bash
# Check database schema
node checkDatabase.js

# Test API endpoints
node testAPI.js

# Test database connection
node testDB.js
```

**Frontend Debug Button**:
- Go to Profile page
- Click "Test Database Connection"
- Check console for detailed diagnostics

### Common Test Cases

✅ **Submit report without media**
✅ **Submit report with photo**
✅ **Submit report with video**
✅ **Submit anonymous report**
✅ **Submit with location**
✅ **Submit with all fields**
✅ **Validation: missing required fields**
✅ **Validation: file too large (>25MB)**
✅ **Update user profile**
✅ **Update only address**
✅ **View report history**
✅ **Pull-to-refresh history**

---

## Troubleshooting Guide

### Backend Issues

#### "Cannot find module 'multer'"
**Solution**:
```bash
cd UserSide\backends
npm install multer
```

#### "ECONNREFUSED" or "Unable to connect to server"
**Solution**:
- Ensure backend is running: `node backends/server.js`
- Check if port 3000 is already in use
- Verify firewall isn't blocking localhost:3000

#### "ER_BAD_FIELD_ERROR" in database
**Solution**:
```sql
-- Add missing columns
ALTER TABLE reports ADD COLUMN title VARCHAR(255) AFTER location_id;
ALTER TABLE reports ADD COLUMN is_anonymous BOOLEAN DEFAULT FALSE AFTER status;
ALTER TABLE users ADD COLUMN address TEXT AFTER contact;
```

#### Backend crashes on startup
**Solution**:
- Check MySQL is running
- Verify credentials in `backends/db.js`
- Check console for specific error messages
- Ensure all dependencies installed: `npm install`

### Frontend Issues

#### "Please log in to submit a report"
**Solution**:
- Verify user is logged in
- Check AsyncStorage has user data
- Verify UserContext has valid user object

#### Form just loads, doesn't submit
**Solution**:
- Open browser console (F12)
- Look for red error messages
- Check backend is running
- Verify API URL in services is correct

#### "Failed to load reports"
**Solution**:
- Backend not responding
- Check backend running on port 3000
- Verify network connection
- Check user_id in query

### Media Upload Issues

#### File not uploading
**Solution**:
- Check file size < 25MB
- Verify file type allowed
- Ensure `evidence/` folder exists and has write permissions
- Check frontend logs: "Has media: YES"
- Check backend logs: "req.file exists? true"

#### File uploads but not in database
**Solution**:
- Check backend error logs
- Verify `report_media` table exists
- Check foreign key constraint on `report_id`
- Ensure report created before media insert

#### Wrong media_type in database
**Solution**:
- Ensure file has proper extension
- Check MIME type detection logic
- Look at backend logs for "Media Type" value

### Database Issues

#### "Cannot connect to database"
**Solution**:
```bash
# Check MySQL is running
mysql -u root -p

# Verify database exists
SHOW DATABASES;
USE alertdavao;
SHOW TABLES;
```

#### "Address not saving"
**Solution**:
- Check `address` column exists in users table
- Verify backend server is running
- Check console for save confirmation logs
- Test backend endpoint directly

### Permission Issues

#### "EACCES: permission denied" (Windows)
**Solution**:
```bash
icacls "UserSide\evidence" /grant Users:F
```

---

## API Reference

### Base URLs

- **UserSide Backend**: `http://localhost:3000/api`
- **AdminSide Backend** (optional): `http://localhost:8000/api`

### Authentication Endpoints

#### POST /register
Register a new user

**Request**:
```json
{
  "firstname": "John",
  "lastname": "Doe",
  "email": "john@example.com",
  "contact": "+1234567890",
  "password": "securepassword"
}
```

**Response**:
```json
{
  "success": true,
  "message": "User registered successfully",
  "user_id": 1
}
```

#### POST /login
Authenticate user

**Request**:
```json
{
  "email": "john@example.com",
  "password": "securepassword"
}
```

**Response**:
```json
{
  "success": true,
  "user": {
    "id": 1,
    "firstname": "John",
    "lastname": "Doe",
    "email": "john@example.com",
    "contact": "+1234567890",
    "address": "123 Main St",
    "is_verified": 0
  }
}
```

### User Profile Endpoints

#### GET /api/users/:id
Get user profile by ID

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "firstname": "John",
    "lastname": "Doe",
    "email": "john@example.com",
    "contact": "+1234567890",
    "address": "123 Main St",
    "is_verified": 0,
    "profile_image": null,
    "created_at": "2025-10-16T00:00:00.000Z",
    "updated_at": "2025-10-17T00:00:00.000Z"
  }
}
```

#### POST /api/users/upsert
Create or update user profile

**Request**:
```json
{
  "id": "1",
  "firstname": "John",
  "lastname": "Doe",
  "email": "john@example.com",
  "contact": "+1234567890",
  "address": "123 Main St",
  "is_verified": 1,
  "profile_image": "url"
}
```

**Response**:
```json
{
  "success": true,
  "message": "User profile saved successfully"
}
```

#### PATCH /api/users/:id/address
Update only user's address

**Request**:
```json
{
  "address": "456 New Street, Davao City"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Address updated successfully"
}
```

### Report Endpoints

#### POST /api/reports
Submit a new crime report

**Content-Type**: `multipart/form-data`

**Fields**:
- `title` (string, required)
- `crime_types` (JSON string, required) - e.g., `["Theft/Robbery"]`
- `description` (string, required)
- `incident_date` (datetime, required) - Format: `YYYY-MM-DD HH:mm:ss`
- `is_anonymous` (boolean, required)
- `user_id` (integer, required)
- `latitude` (float, optional)
- `longitude` (float, optional)
- `media` (file, optional) - Max 25MB

**Example Response**:
```json
{
  "success": true,
  "message": "Report submitted successfully",
  "data": {
    "report_id": 1,
    "title": "Wallet Stolen at Market",
    "report_type": "Theft/Robbery",
    "status": "pending",
    "is_anonymous": false,
    "date_reported": "2025-10-16T14:30:00.000Z",
    "location": {
      "location_id": 1,
      "latitude": 7.0731,
      "longitude": 125.6128,
      "barangay": "Lat: 7.0731, Lng: 125.6128"
    },
    "media": {
      "media_id": 1,
      "media_url": "/evidence/evidence-1697456789012.jpg",
      "media_type": "jpg"
    }
  }
}
```

#### GET /api/reports/user/:userId
Get all reports for a specific user

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "report_id": 1,
      "title": "Wallet Stolen",
      "report_type": "Theft/Robbery",
      "description": "My wallet was stolen...",
      "status": "pending",
      "is_anonymous": false,
      "date_reported": "2025-10-16T14:30:00.000Z",
      "created_at": "2025-10-16T14:35:00.000Z",
      "location": {
        "latitude": 7.0731,
        "longitude": 125.6128,
        "barangay": "Lat: 7.0731, Lng: 125.6128"
      },
      "media": [
        {
          "media_id": 1,
          "media_url": "/evidence/evidence-1697456789012.jpg",
          "media_type": "jpg"
        }
      ]
    }
  ]
}
```

#### GET /api/reports
Get all reports (admin)

**Response**: Same structure as `/api/reports/user/:userId` but includes all users' reports

### Utility Endpoints

#### GET /api/test-connection
Test database connection

**Response**:
```json
{
  "success": true,
  "message": "Database connection successful"
}
```

#### GET /api/db-status
Get database status and table information

**Response**:
```json
{
  "success": true,
  "connected": true,
  "tables": {
    "users": true,
    "reports": true,
    "locations": true,
    "report_media": true
  }
}
```

---

## Appendix

### Success Checklist

When everything is working correctly:

✅ **Backend**:
- [ ] Server starts on port 3000
- [ ] Database connection successful
- [ ] All API endpoints respond
- [ ] File uploads work
- [ ] Console logs show detailed information

✅ **Frontend**:
- [ ] App starts without errors
- [ ] Can login successfully
- [ ] Profile shows correct user
- [ ] Can submit reports
- [ ] Can view history
- [ ] Can update profile
- [ ] Media selection works

✅ **Database**:
- [ ] All tables exist
- [ ] Foreign keys configured
- [ ] Records insert successfully
- [ ] Data persists correctly

✅ **File System**:
- [ ] Evidence folder exists
- [ ] Files upload successfully
- [ ] Files accessible via URL
- [ ] Proper permissions set

### File Permissions (Windows)

```bash
# Grant full permissions to evidence folder
icacls "UserSide\evidence" /grant Users:F

# Verify permissions
icacls "UserSide\evidence"
```

### Environment Variables (Future)

For production deployment, move sensitive data to `.env`:

```env
# Database
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASSWORD=1234
DB_NAME=alertdavao

# Server
PORT=3000
NODE_ENV=production

# File Upload
MAX_FILE_SIZE=26214400
UPLOAD_DIR=./evidence
```

### Security Considerations

1. **File Uploads**:
   - File type validation
   - File size limits
   - Filename sanitization
   - Stored outside web root

2. **Database**:
   - Prepared statements (prevent SQL injection)
   - Connection pooling
   - Transaction support

3. **Authentication**:
   - Password hashing with bcrypt
   - Session management
   - Token-based auth (future)

4. **API**:
   - CORS configuration
   - Rate limiting (future)
   - Input validation
   - Error handling

### Performance Optimization

1. **Database**:
   - Indexed columns (id, email, report_id)
   - Connection pooling
   - Query optimization

2. **File Uploads**:
   - Image compression (future)
   - Thumbnail generation (future)
   - CDN integration (future)

3. **Frontend**:
   - Lazy loading
   - Image caching
   - Pagination for history

### Future Enhancements

- [ ] Push notifications for report status updates
- [ ] Admin dashboard for managing reports
- [ ] Report status workflow (pending → investigating → resolved)
- [ ] Real-time chat between users and admins
- [ ] Map view of reported incidents
- [ ] Statistical dashboard
- [ ] Export reports to PDF
- [ ] Multi-language support
- [ ] Email notifications
- [ ] SMS alerts
- [ ] Cloud storage integration (AWS S3, Cloudinary)

---

**End of Documentation**

For questions or issues, please refer to the troubleshooting section or check the console logs for detailed error messages.
