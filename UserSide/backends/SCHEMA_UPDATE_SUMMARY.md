# Database Schema Update Summary

## Overview
Successfully updated the AlertDavao 2.0 database schema to support new features including user verification, role-based access control, police station management, messaging, and crime forecasting/analytics.

## Date: 2025-10-17

---

## 1. DATABASE SCHEMA CHANGES

### New Tables Created:
- ✅ `police_stations` - Police station locations and contact info
- ✅ `roles` - User roles (admin, police, user)
- ✅ `user_role` - Many-to-many relationship between users and roles
- ✅ `routes` - API routes/pages for permission control
- ✅ `role_route` - Many-to-many relationship for role-based permissions
- ✅ `verifications` - User identity verification requests
- ✅ `messages` - Messaging system between users
- ✅ `crime_forecasts` - SARIMA-based crime predictions
- ✅ `crime_analytics` - Crime statistics by location
- ✅ `admin_actions` - Audit log for admin activities

### Updated Tables:
- ✅ `users` - Added columns:
  - `latitude` (DOUBLE) - User's current or registered latitude
  - `longitude` (DOUBLE) - User's current or registered longitude
  - `station_id` (BIGINT UNSIGNED) - Police station assignment (FK)
  - `is_verified` (BOOLEAN) - Identity verification status

- ✅ `reports` - Added columns:
  - `title` (VARCHAR) - Report title/subject
  - `assigned_station_id` (BIGINT UNSIGNED) - Assigned police station (FK)

- ✅ `verifications` - Added columns:
  - `id_selfie` (VARCHAR) - Path to selfie with ID
  - `billing_document` (VARCHAR) - Path to proof of billing

### Foreign Key Relationships:
- `users.station_id` → `police_stations.station_id`
- `reports.assigned_station_id` → `police_stations.station_id`
- `verifications.user_id` → `users.id`
- `user_role.user_id` → `users.id`
- `user_role.role_id` → `roles.role_id`
- `messages.sender_id` → `users.id`
- `messages.receiver_id` → `users.id`
- `messages.report_id` → `reports.report_id`
- `crime_forecasts.location_id` → `locations.location_id`
- `crime_analytics.location_id` → `locations.location_id`
- `admin_actions.admin_id` → `users.id`

---

## 2. TYPESCRIPT INTERFACES UPDATED

### UserData Interface (contexts/UserContext.tsx & services/directDbService.ts)
```typescript
interface UserData {
  id: string;
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  address: string;
  latitude?: number;      // NEW
  longitude?: number;     // NEW
  stationId?: string;     // NEW - Police station assignment
  isVerified: boolean;
  profileImage?: string;
  dataSource?: 'database' | 'default';
  createdAt?: string;
  updatedAt?: string;
}
```

### Report Interface (app/(tabs)/history.tsx)
```typescript
interface Report {
  report_id: number;
  title: string;
  report_type: string;    // 'crime' or 'cybercrime'
  description: string;
  status: string;
  is_anonymous: boolean;
  date_reported: string;
  created_at: string;
  assigned_station_id?: number;  // NEW - Assigned police station
  location: {
    latitude: number;
    longitude: number;
    barangay: string;
  };
  media: Array<{
    media_id: number;
    media_url: string;
    media_type: string;    // 'image', 'video', or 'audio'
  }>;
}
```

---

## 3. BACKEND API UPDATES

### Updated Handlers:

#### handleUserProfile.js - Enhanced upsertUser()
Now handles new fields:
- `latitude` - User coordinates
- `longitude` - User coordinates
- `station_id` - Police station assignment
- `is_verified` - Verification status

### New API Endpoints (handleNewFeatures.js):

#### Police Stations
- `GET /api/police-stations` - Get all police stations
- `GET /api/police-stations/:id` - Get specific police station

#### User Roles
- `GET /api/users/:userId/roles` - Get user's assigned roles
- `POST /api/users/roles/assign` - Assign role to user

#### Verification
- `POST /api/verification` - Submit verification request
- `GET /api/verification/:userId` - Get verification status

#### Messages
- `GET /api/messages/:userId` - Get user's messages
- `POST /api/messages` - Send new message
- `PATCH /api/messages/:messageId/read` - Mark message as read

#### Crime Analytics
- `GET /api/analytics` - Get all crime analytics
- `GET /api/analytics/:locationId` - Get analytics for specific location

#### Crime Forecasts
- `GET /api/forecasts/:locationId` - Get crime forecasts for location

---

## 4. FILES MODIFIED

### Frontend TypeScript Files:
1. `UserSide/contexts/UserContext.tsx` - Updated UserData interface
2. `UserSide/services/directDbService.ts` - Updated UserData interface and data mapping
3. `UserSide/app/(tabs)/history.tsx` - Updated Report interface

### Backend JavaScript Files:
1. `UserSide/backends/handleUserProfile.js` - Enhanced upsertUser() function
2. `UserSide/backends/server.js` - Added new API routes
3. `UserSide/backends/handleNewFeatures.js` - NEW FILE with all new endpoints

### Database Scripts:
1. `UserSide/backends/database_migration.sql` - Full migration script
2. `UserSide/backends/update_schema.sql` - Quick update script (NEW)

---

## 5. DATA SEEDED

### Roles Table:
- ID 1: `admin`
- ID 2: `police`
- ID 3: `user`

### Police Stations (19 stations):
All stations from location.tsx have been seeded with:
- Station name
- Address
- Latitude/Longitude coordinates
- Contact numbers

### User Roles:
- All existing users have been assigned the default 'user' role (role_id = 3)

---

## 6. TESTING CHECKLIST

### Database Level:
- [x] All tables created successfully
- [x] Foreign keys established correctly
- [x] Police stations seeded
- [x] Default roles created
- [x] Users assigned default role

### Backend API:
- [ ] Test user CRUD with new fields (latitude, longitude, station_id)
- [ ] Test police stations endpoints
- [ ] Test user roles endpoints
- [ ] Test verification endpoints
- [ ] Test messaging endpoints
- [ ] Test analytics endpoints
- [ ] Test forecasts endpoints

### Frontend:
- [ ] User profile displays new fields
- [ ] Reports show assigned stations
- [ ] Location coordinates saved correctly

---

## 7. MIGRATION COMMANDS

### Execute Full Migration:
```powershell
Get-Content UserSide\backends\database_migration.sql | mysql -u root -p1234 alertdavao
```

### Execute Quick Updates Only:
```powershell
Get-Content UserSide\backends\update_schema.sql | mysql -u root -p1234 alertdavao
```

### Verify Tables:
```sql
USE alertdavao;
SHOW TABLES;
DESCRIBE users;
DESCRIBE reports;
DESCRIBE police_stations;
```

---

## 8. NEXT STEPS

### Immediate:
1. ✅ Restart backend server to load new endpoints
2. ⏳ Test all new API endpoints
3. ⏳ Update frontend UI to use new fields
4. ⏳ Implement verification flow in UI
5. ⏳ Create messaging interface
6. ⏳ Build analytics dashboard

### Future Enhancements:
- Implement SARIMA model for crime forecasting
- Build admin dashboard for role management
- Create police dashboard for station management
- Add geofencing features using lat/lng coordinates
- Implement real-time notifications for messages
- Build crime heatmap using analytics data

---

## 9. IMPORTANT NOTES

### Data Type Compatibility:
- Fixed incompatibility between `police_stations.station_id` (INT → BIGINT UNSIGNED)
- Updated `users.station_id` to match (INT → BIGINT UNSIGNED)
- Reports `assigned_station_id` created as BIGINT UNSIGNED from start

### Backward Compatibility:
- All existing user records retained
- All existing reports retained
- New columns added with NULL/default values
- No data loss during migration

### Security Considerations:
- OTP codes should be hashed before storage (TODO)
- Message content should be encrypted (TODO)
- Verification documents stored as file paths only
- Role-based access control framework in place

---

## 10. API USAGE EXAMPLES

### Get Police Stations:
```javascript
const stations = await fetch('http://192.168.1.42:3000/api/police-stations');
```

### Get User Roles:
```javascript
const roles = await fetch('http://192.168.1.42:3000/api/users/1/roles');
```

### Submit Verification:
```javascript
await fetch('http://192.168.1.42:3000/api/verification', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    userId: 1,
    otpCode: '123456',
    idSelfie: '/path/to/selfie.jpg',
    billingDocument: '/path/to/bill.jpg'
  })
});
```

### Send Message:
```javascript
await fetch('http://192.168.1.42:3000/api/messages', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    senderId: 1,
    receiverId: 2,
    reportId: 5,
    message: 'Report has been reviewed'
  })
});
```

---

## CONCLUSION

The database schema has been successfully updated with all new features. The backend API now supports:
- ✅ User location tracking (latitude/longitude)
- ✅ Police station management
- ✅ Role-based access control
- ✅ User verification system
- ✅ Messaging between users
- ✅ Crime analytics and forecasting framework

All changes are backward compatible and preserve existing data.

**Status:** Schema migration COMPLETE ✅
**Next Phase:** API testing and frontend integration
