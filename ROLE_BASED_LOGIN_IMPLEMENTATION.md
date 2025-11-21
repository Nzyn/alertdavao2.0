# Role-Based Login & Change Role Implementation

## Overview
This document summarizes the implementation of role-based login redirects and the ability to change user roles on the AdminSide.

## Changes Made

### 1. Database Migration
**File**: `AdminSide/admin/database/migrations/2025_11_19_000000_add_role_to_users_table.php`

Added a new `role` column to the `users` table with three possible values:
- `user` (default) - Regular user who can submit reports
- `police` - Police officer assigned to a station
- `admin` - Administrator with full access

### 2. User Model
**File**: `AdminSide/admin/app/Models/User.php`

Updated the `$fillable` array to include the `role` field, allowing it to be mass-assignable.

### 3. UserController - Change Role Functionality
**File**: `AdminSide/admin/app/Http/Controllers/UserController.php`

#### New Method: `changeRole()`
- Accepts a POST request with a `role` parameter
- Validates the role (must be: user, police, or admin)
- When changing to `police`: Assigns a police station and creates a PoliceOfficer record
- When changing from `police` to `user`: Removes the station assignment and deactivates the PoliceOfficer record
- Returns JSON response with success/error status

#### Updated Method: `promoteToOfficer()`
- Now also sets the `role` field to `police` when promoting a user

### 4. Routes
**File**: `AdminSide/admin/routes/web.php`

Added new route:
```php
Route::post('/users/{id}/change-role', [UserController::class, 'changeRole'])->name('users.changeRole');
```

### 5. Users Management View
**File**: `AdminSide/admin/resources/views/users.blade.php`

#### Changes:
1. **Display Type**: Changed from `{{ $user->station_id ? 'Officer' : 'User' }}` to `{{ ucfirst($user->role) }}`
   - Now displays the actual role: User, Police, or Admin

2. **Change Role Button**: Added new action in dropdown menu
   - All users can have their role changed (user, police, admin)
   - Prompt asks admin to enter the new role
   - Validates input and confirms action before proceeding

3. **JavaScript Function**: Added `changeRole(userId)` function
   - Prompts admin to enter new role
   - Validates against allowed roles
   - Confirms action before submitting
   - Updates page upon success

4. **Event Listeners**: Added listener for `.change-role-link` elements

### 6. Authentication - AuthController
**File**: `AdminSide/admin/app/Http/Controllers/AuthController.php`

Updated `login()` method to redirect based on user role:
- **User role**: Currently redirects to `http://localhost:19000` (UserSide)
  - In production, update this URL to the actual UserSide domain
- **Police/Admin role**: Redirects to the AdminSide dashboard (`route('dashboard')`)
- **Default**: Falls back to dashboard

### 7. Backend Login Response
**File**: `UserSide/backends/handleLogin.js`

Updated response to include `role` field:
```javascript
user: {
  id: user.id,
  email: user.email,
  firstname: user.firstname,
  lastname: user.lastname,
  role: user.role || 'user',  // Added role
}
```

### 8. UserSide Login Screens
**Files**:
- `UserSide/app/login.tsx`
- `UserSide/app/(tabs)/login.tsx`

Updated both login components to:
- Check user role after successful login
- Block police and admin users from logging in
- Show alert: "Police and Admin users must log in through the AdminSide dashboard."
- Allow only regular users to proceed to UserSide

## How It Works

### For AdminSide (Change Role)
1. Admin navigates to Users page
2. Clicks the three-dot menu on any user row
3. Selects "Change Role"
4. Prompted to enter new role (user, police, or admin)
5. Confirms the action
6. User's role is updated in the database
7. Page reloads to show updated role

### For Login Redirects
1. **User tries to log in with admin/police role**:
   - Admin/Police: Redirected to AdminSide dashboard
   - User: Attempts login on UserSide → Rejected with message

2. **Admin creates/promotes a user**:
   - User's role is set appropriately
   - Next login will redirect them to the correct dashboard

## Testing

### Prerequisites
- Run the migration: `php artisan migrate`
- Ensure both AdminSide (Laravel) and UserSide (Node.js) backends are running

### Test Scenarios

#### 1. Change User Role
1. Log in to AdminSide as admin
2. Go to Users page
3. Click three-dot menu on a user
4. Select "Change Role"
5. Enter "police"
6. Confirm
7. Verify role changed to "Police" in table

#### 2. Test Login Redirects
1. Create/change a user to role "user"
2. Log in with that user credentials in AdminSide
3. Should redirect to UserSide (or show appropriate message)

4. Create/change a user to role "police"
5. Log in with that credentials in AdminSide
6. Should see AdminSide dashboard

#### 3. Test UserSide Login Blocks
1. Try to log in on UserSide with admin/police user
2. Should be rejected with message: "Police and Admin users must log in through the AdminSide dashboard."

## Important Notes

### Backend URL Configuration
In `AuthController.php`, the UserSide redirect URL is hardcoded as `http://localhost:19000`. This needs to be:
- Updated for production deployment
- Ideally stored in environment configuration (`.env` file)

### Current Redirect URL
```php
return redirect('http://localhost:19000')->with('success', 'Login successful! Redirecting to user dashboard.');
```

### Future Improvement
```php
return redirect(config('app.user_side_url'))->with('success', 'Login successful! Redirecting to user dashboard.');
```

Add to `.env`:
```
USER_SIDE_URL=http://localhost:19000
```

Add to `config/app.php`:
```php
'user_side_url' => env('USER_SIDE_URL', 'http://localhost:19000'),
```

## Database State After Migration

Users table will now have:
- All existing users get `role = 'user'` by default
- Officers (those with `station_id`) should be manually set to `role = 'police'`
- Admin users should be manually set to `role = 'admin'`

### Migration Script (Optional)
If you have existing officers, run this query to update them:
```sql
UPDATE users SET role = 'police' WHERE station_id IS NOT NULL;
UPDATE users SET role = 'admin' WHERE email = 'admin@example.com'; -- Update as needed
```

## Files Modified

1. ✅ `AdminSide/admin/database/migrations/2025_11_19_000000_add_role_to_users_table.php` - Created
2. ✅ `AdminSide/admin/app/Models/User.php` - Updated
3. ✅ `AdminSide/admin/app/Http/Controllers/UserController.php` - Updated (added changeRole, updated promoteToOfficer)
4. ✅ `AdminSide/admin/routes/web.php` - Updated (added route)
5. ✅ `AdminSide/admin/resources/views/users.blade.php` - Updated (UI, buttons, JavaScript)
6. ✅ `AdminSide/admin/app/Http/Controllers/AuthController.php` - Updated (role-based redirect)
7. ✅ `UserSide/backends/handleLogin.js` - Updated (return role)
8. ✅ `UserSide/app/login.tsx` - Updated (role validation)
9. ✅ `UserSide/app/(tabs)/login.tsx` - Updated (role validation)

## Deployment Checklist

- [ ] Run migration: `php artisan migrate`
- [ ] Test change role functionality
- [ ] Test login redirects
- [ ] Update UserSide URL in `AuthController` for production
- [ ] Update environment variables if using config-based approach
- [ ] Test all three roles (user, police, admin)
- [ ] Verify no existing functionality is broken
