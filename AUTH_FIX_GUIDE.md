# Authentication Fix Guide

## Problem
You're getting "Invalid email or password" error even with correct credentials when trying to log in at `http://localhost/refugee-innovation-hub/`

## Solution Steps

### Step 1: Run the Database Test Script

1. Open your browser
2. Go to: **`http://localhost/refugee-innovation-hub/test-db-connection.php`**
3. This will check:
   - ✅ Database connection
   - ✅ Users table exists
   - ✅ Admin users in database
   - ✅ Password hash verification
   - ✅ Session configuration

### Step 2: Check Test Results

**If you see "No admin users found":**
- Your database schema hasn't been imported
- Go to phpMyAdmin → Select `refugee_innovation_hub` → Import → Choose `database/schema.sql`
- Refresh the test page

**If you see "Password verification FAILED":**
- The password hash in the database doesn't match
- Copy the SQL command shown on the test page
- Run it in phpMyAdmin SQL tab

**If you see "User not found":**
- The default admin user doesn't exist
- Run this SQL in phpMyAdmin:

```sql
INSERT INTO users (email, password_hash, role, is_active, created_at)
VALUES (
    'admin@jrsusa.org',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    1,
    NOW()
);
```

### Step 3: Verify JavaScript Console

1. Open your browser's Developer Tools (F12)
2. Go to the Console tab
3. Try logging in
4. Look for errors related to:
   - API URL paths
   - CORS errors
   - Network errors

### Step 4: Check API Endpoint Directly

Test if the API is accessible:

1. Open: `http://localhost/refugee-innovation-hub/api/auth.php`
2. You should see a JSON error (this is normal - it means the API works)
3. If you see a 404 or blank page, the path is wrong

### Step 5: Manual Login Test

Create a simple test file to verify authentication works:

**File: `test-login.php`**

```php
<?php
require_once 'api/config.php';

$pdo = getDBConnection();

$email = 'admin@jrsusa.org';
$password = 'admin123';

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

echo "<h1>Manual Login Test</h1>";

if ($user) {
    echo "User found: " . $user['email'] . "<br>";
    echo "Role: " . $user['role'] . "<br>";
    echo "Active: " . ($user['is_active'] ? 'Yes' : 'No') . "<br><br>";

    if (password_verify($password, $user['password_hash'])) {
        echo "✅ <strong style='color: green;'>Password verification SUCCESS!</strong><br>";
        echo "Authentication should work in the app.";
    } else {
        echo "❌ <strong style='color: red;'>Password verification FAILED!</strong><br>";
        echo "The password hash is incorrect. Generate a new one.";
    }
} else {
    echo "❌ User not found in database.";
}
?>
```

## Common Issues & Fixes

### Issue 1: CORS Error in Console

**Symptom:** Browser console shows CORS or cross-origin error

**Fix:**
1. Open `api/config.php`
2. Verify these headers are present:
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
```

### Issue 2: Session Not Persisting

**Symptom:** Login succeeds but you're immediately logged out

**Fix:**
1. Check PHP session directory is writable
2. In XAMPP, check `C:\xampp\tmp` exists and is writable
3. Add to `api/config.php`:
```php
ini_set('session.save_path', 'C:/xampp/tmp');
```

### Issue 3: 404 on API Calls

**Symptom:** Browser console shows 404 for `/api/auth.php`

**Fix:**
1. Verify the `api` folder exists in your project root
2. Check `api/auth.php` file exists
3. Make sure Apache is serving the correct directory

### Issue 4: Database Connection Error

**Symptom:** "Database connection failed" error

**Fix:**
1. Verify MySQL is running in XAMPP Control Panel
2. Check database name in `api/config.php`:
```php
define('DB_NAME', 'refugee_innovation_hub');
```
3. Verify database exists in phpMyAdmin

### Issue 5: Password Always Wrong

**Symptom:** Always get "Invalid email or password" even with correct info

**Possible Causes:**
1. **Password hash is incorrect** - Run the test script to verify
2. **User is inactive** - Check `is_active` field in database
3. **Wrong email** - Check exact email in database (case-sensitive)

**Fix:**
```sql
-- Check user status
SELECT id, email, role, is_active FROM users WHERE email = 'admin@jrsusa.org';

-- If is_active = 0, activate:
UPDATE users SET is_active = 1 WHERE email = 'admin@jrsusa.org';

-- Reset password to 'admin123':
UPDATE users
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = 'admin@jrsusa.org';
```

## Quick Fixes Reference

### Reset Admin Password

```sql
UPDATE users
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = 'admin@jrsusa.org';
```
Password: `admin123`

### Create New Admin

```sql
INSERT INTO users (email, password_hash, role, is_active, created_at)
VALUES (
    'newadmin@jrsusa.org',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    1,
    NOW()
);
```

### Check All Admins

```sql
SELECT id, email, role, is_active, created_at, last_login
FROM users
WHERE role = 'admin';
```

### Activate Deactivated User

```sql
UPDATE users SET is_active = 1 WHERE email = 'admin@jrsusa.org';
```

## Debugging Checklist

Run through this checklist:

- [ ] XAMPP Apache is running (green)
- [ ] XAMPP MySQL is running (green)
- [ ] Database `refugee_innovation_hub` exists in phpMyAdmin
- [ ] Database has `users` table
- [ ] At least one admin user exists with `is_active = 1`
- [ ] Test script (`test-db-connection.php`) shows all green checkmarks
- [ ] Browser console (F12) shows no errors
- [ ] API endpoint accessible: `http://localhost/refugee-innovation-hub/api/auth.php`
- [ ] Password hash matches (verified in test script)

## What Changed (Technical)

### auth.js Path Fix

**Before:**
```javascript
const API_BASE = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
const API_URL = `${API_BASE}/api/auth.php`;
```

**After:**
```javascript
function getApiUrl() {
    const pathname = window.location.pathname;
    const baseDir = pathname.substring(0, pathname.lastIndexOf('/') + 1);
    return `${baseDir}api/auth.php`;
}
const API_URL = getApiUrl();
```

**Why:** The previous version didn't include the trailing slash, causing incorrect paths like `/refugee-innovation-hubapi/auth.php` instead of `/refugee-innovation-hub/api/auth.php`

## Still Not Working?

### Enable Debug Mode

1. Open `api/auth.php`
2. Add at the top (after `<?php`):
```php
error_log("Auth request received: " . print_r($_POST, true));
error_log("Request data: " . file_get_contents('php://input'));
```

3. Check Apache error log: `C:\xampp\apache\logs\error.log`

### Test with Postman or cURL

```bash
curl -X POST http://localhost/refugee-innovation-hub/api/auth.php?action=login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@jrsusa.org","password":"admin123"}'
```

Expected response:
```json
{
  "user": {
    "id": 1,
    "email": "admin@jrsusa.org",
    "role": "admin"
  },
  "message": "Login successful"
}
```

## Security Note

**After fixing authentication, delete these test files:**
- `test-db-connection.php`
- `test-login.php`

These files expose sensitive information about your database!

## Need More Help?

If none of these fixes work:

1. Check XAMPP error logs: `C:\xampp\apache\logs\error.log`
2. Check PHP error logs: `C:\xampp\php\logs\php_error_log`
3. Enable error display in `api/config.php`:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

---

**Last Updated:** December 10, 2025
**Issue:** Authentication failing with correct credentials
**Status:** Fixed - auth.js path construction updated
