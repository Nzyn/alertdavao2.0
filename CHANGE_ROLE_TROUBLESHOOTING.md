# Change Role Feature - Troubleshooting Guide

## Issue: "An error occurred while changing the user role"

### Cause Analysis
The error message suggests one of the following issues:

1. **Migration not run**
2. **Missing `role` column in users table**
3. **CSRF token issue**
4. **User not found**
5. **Permission/authentication issue**
6. **Invalid role value**

## Solution Steps

### Step 1: Verify Migration
```bash
cd AdminSide/admin
php artisan migrate:status
```

Expected output: `2025_11_19_000000_add_role_to_users_table` should show as **DONE**

If not, run:
```bash
php artisan migrate
```

### Step 2: Verify Role Column Exists
```bash
php artisan tinker
```

In tinker console:
```php
>>> $user = \App\Models\User::first();
>>> $user->role
=> "user"
>>> $user->update(['role' => 'police'])
>>> $user->refresh();
>>> $user->role
=> "police"
```

If you get errors, the column doesn't exist. Go back to Step 1.

### Step 3: Clear Cache
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

### Step 4: Check Laravel Logs
```bash
# On Windows:
type "AdminSide\admin\storage\logs\laravel.log"

# Last few lines:
powershell -Command "Get-Content 'AdminSide\admin\storage\logs\laravel.log' -Tail 50"
```

Look for errors related to:
- `changeRole` method
- Database errors
- Validation errors

### Step 5: Test API Directly
Run the test script:
```bash
cd AdminSide/admin
php test_api_call.php
```

Expected output:
```
Response:
{
    "success": true,
    "message": "User role has been changed successfully",
    "user": {
        "id": 1,
        "role": "police",
        "station_id": 1
    }
}
```

If this fails, the issue is in the API backend.
If this works but the UI fails, the issue is in the frontend/CSRF token.

### Step 6: Check Browser Console
1. Open Users page in AdminSide
2. Press F12 to open Developer Tools
3. Click "Console" tab
4. Try to change a user's role
5. Look for messages like:
   - `Changing role for user X to police`
   - `Response status: 200`
   - `Response data: {...}`

If you see errors here, the problem is in JavaScript or the HTTP request.

### Common Issues and Fixes

#### Issue: "User not found"
- Verify the user ID is correct
- User might have been deleted
- Solution: Try with a different user

#### Issue: "Validation failed"
- Role value must be one of: `user`, `police`, `admin`
- Check browser console for validation errors
- Solution: Ensure correct role value is sent

#### Issue: "No police stations available"
- When changing user to `police` role, a police station must be created first
- Solution: Create a police station through database or create a UI for it

#### Issue: "CSRF token missing" (in browser console)
- The meta tag for CSRF token is not in the page
- Solution: Refresh the page and try again
- If persists: Clear browser cache and cookies

#### Issue: 404 Not Found
- The route `/users/{id}/change-role` is not registered
- Solution: Run `php artisan route:clear` again
- Verify route is in `routes/web.php` (line 48)

#### Issue: 405 Method Not Allowed
- The endpoint expects POST but received different method
- JavaScript is sending correct method
- Solution: Clear route cache: `php artisan route:clear`

### Debug Mode

Edit `AdminSide/admin/resources/views/users.blade.php` and ensure this line is present (around line 495):
```javascript
console.log('Response data:', data);
```

Then in browser F12 console, you'll see the full response including any error messages.

### Still Not Working?

1. Clear everything:
```bash
cd AdminSide/admin
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

2. Restart Laravel development server (if running)

3. Hard refresh browser (Ctrl+Shift+Delete or Cmd+Shift+Delete)

4. Check database directly:
```sql
-- Verify role column exists
DESCRIBE users;

-- Should show a 'role' column of type enum('user','police','admin')

-- Test update directly
UPDATE users SET role = 'police' WHERE id = 1;
SELECT * FROM users WHERE id = 1;
```

5. Check file permissions:
- `storage/logs/` directory should be writable
- `storage/` directory should be writable

### Working State Verification

The feature is working correctly if:

1. ✅ Migration shows as **DONE**
2. ✅ `test_change_role.php` output shows role changed successfully
3. ✅ `test_api_call.php` shows `"success": true`
4. ✅ Browser console shows `Response status: 200`
5. ✅ User row refreshes and shows new role after confirmation
6. ✅ Database query shows updated role: `SELECT role FROM users WHERE id = 1;`

## Files Involved

- Database: `users` table, `role` column
- Migration: `database/migrations/2025_11_19_000000_add_role_to_users_table.php`
- Model: `app/Models/User.php`
- Controller: `app/Http/Controllers/UserController.php` (changeRole method)
- Route: `routes/web.php` (line 48)
- View: `resources/views/users.blade.php` (changeRole function, UI)
- Logs: `storage/logs/laravel.log`

## Contacting Support

If none of these steps work, provide:

1. Output of `php artisan migrate:status`
2. Output of `php test_api_call.php`
3. Browser console error messages (F12 > Console)
4. Last 100 lines of `storage/logs/laravel.log`
5. Exact steps to reproduce the issue
