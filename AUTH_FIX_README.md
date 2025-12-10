# Authentication Fix - COMPLETE SOLUTION

## The Problem
You were getting "Invalid email or password" error when trying to log in at `http://localhost/refugee-innovation-hub/`

## Root Causes Found & Fixed

### Issue 1: Missing `is_active` Column in Database ✅ FIXED
The authentication code expected an `is_active` field in the users table, but it was missing from the schema.

### Issue 2: Incorrect API Path Construction ✅ FIXED
The JavaScript path construction was missing a trailing slash, causing incorrect API URLs.

---

## SOLUTION - Follow These Steps

### Option A: Fresh Database Setup (RECOMMENDED)

**If you haven't imported data or just getting started:**

1. **Drop existing database** (if it exists):
   - Go to phpMyAdmin: `http://localhost/phpmyadmin`
   - Select `refugee_innovation_hub` database
   - Click "Operations" tab → "Drop the database"

2. **Import updated schema**:
   - Go to phpMyAdmin
   - Click "New" to create database: `refugee_innovation_hub`
   - Click "Import" tab
   - Choose file: `database/schema.sql` (the updated one)
   - Click "Go"

3. **Done!** The admin account will be created with:
   - Email: `admin@jrsusa.org`
   - Password: `admin123`

### Option B: Fix Existing Database

**If you want to keep existing data:**

1. Go to phpMyAdmin: `http://localhost/phpmyadmin`
2. Select `refugee_innovation_hub` database
3. Click "SQL" tab
4. Run this SQL:

```sql
-- Add is_active column
ALTER TABLE users
ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER role;

-- Set all existing users to active
UPDATE users SET is_active = 1;

-- Verify the fix
SELECT id, email, role, is_active FROM users;
```

Or simply import the fix file:
- Click "Import" tab
- Choose: `database/fix_users_table.sql`
- Click "Go"

---

## Verify the Fix

### Step 1: Run Database Test

Go to: `http://localhost/refugee-innovation-hub/test-db-connection.php`

**You should see all green checkmarks ✅**

Look for:
- ✅ Config loaded successfully
- ✅ Connected to database successfully
- ✅ Users table exists
- ✅ Found admin user(s)
- ✅ Password verification SUCCESSFUL

### Step 2: Test Login

1. Go to: `http://localhost/refugee-innovation-hub/`
2. Click "Sign In"
3. Enter:
   - Email: `admin@jrsusa.org`
   - Password: `admin123`
4. Should redirect to Dashboard successfully

### Step 3: Clean Up

**For security, delete test files after verification:**
- `test-db-connection.php`
- `test-login.php` (if you created it)

---

## What Was Fixed (Technical Details)

### 1. Database Schema (`database/schema.sql`)

**Before:**
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NULL,
    role ENUM('admin', 'editor') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

**After:**
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NULL,
    role ENUM('admin', 'editor') DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,  -- ← ADDED THIS
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

### 2. Authentication Path (`js/auth.js`)

**Before:**
```javascript
const API_BASE = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
const API_URL = `${API_BASE}/api/auth.php`;
// Result: /refugee-innovation-hubapi/auth.php ❌
```

**After:**
```javascript
function getApiUrl() {
    const pathname = window.location.pathname;
    const baseDir = pathname.substring(0, pathname.lastIndexOf('/') + 1);
    return `${baseDir}api/auth.php`;
}
const API_URL = getApiUrl();
// Result: /refugee-innovation-hub/api/auth.php ✅
```

---

## Troubleshooting

### Still getting "Invalid email or password"?

1. **Check user exists and is active:**
   ```sql
   SELECT id, email, role, is_active FROM users WHERE email = 'admin@jrsusa.org';
   ```
   - Should show `is_active = 1`
   - If `is_active` column doesn't exist, run Option B fix above

2. **Reset password:**
   ```sql
   UPDATE users
   SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
   WHERE email = 'admin@jrsusa.org';
   ```
   Password will be: `admin123`

3. **Check browser console (F12):**
   - Look for errors in Console tab
   - Check Network tab for failed API requests
   - Verify API URL is: `/refugee-innovation-hub/api/auth.php`

4. **Verify Apache & MySQL are running:**
   - XAMPP Control Panel should show both services green

### Can't access test page?

If `test-db-connection.php` shows 404:
- Verify file is in project root: `C:\xampp\htdocs\refugee-innovation-hub\test-db-connection.php`
- Make sure Apache is running
- Check URL exactly: `http://localhost/refugee-innovation-hub/test-db-connection.php`

### Database connection errors?

Check `api/config.php` settings:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'refugee_innovation_hub');
define('DB_USER', 'root');
define('DB_PASS', ''); // Empty by default in XAMPP
```

---

## Files Changed

### New Files Created:
- ✅ `test-db-connection.php` - Database verification tool
- ✅ `database/fix_users_table.sql` - SQL to fix existing database
- ✅ `AUTH_FIX_GUIDE.md` - Detailed troubleshooting guide
- ✅ `AUTH_FIX_README.md` - This file

### Files Updated:
- ✅ `js/auth.js` - Fixed API path construction
- ✅ `database/schema.sql` - Added `is_active` column

### Files in Both Locations:
All changes have been made to both:
- `/project/` (root)
- `/project/refugee-innovation-hub/` (subfolder)

---

## Quick Reference

### Default Credentials
- Email: `admin@jrsusa.org`
- Password: `admin123`
- **⚠️ Change this after first login!**

### Key URLs
- Site: `http://localhost/refugee-innovation-hub/`
- phpMyAdmin: `http://localhost/phpmyadmin`
- Test Script: `http://localhost/refugee-innovation-hub/test-db-connection.php`
- API: `http://localhost/refugee-innovation-hub/api/auth.php`

### Quick SQL Commands

**Check admin users:**
```sql
SELECT id, email, role, is_active FROM users;
```

**Reset password to 'admin123':**
```sql
UPDATE users
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = 'admin@jrsusa.org';
```

**Activate user:**
```sql
UPDATE users SET is_active = 1 WHERE email = 'admin@jrsusa.org';
```

---

## Success Checklist

After completing the fix:

- [ ] Ran database test script - all green ✅
- [ ] Can login successfully with admin@jrsusa.org / admin123
- [ ] Dashboard loads and shows statistics
- [ ] Can submit stories (admin only)
- [ ] Can approve/reject submissions
- [ ] Can delete stories
- [ ] Deleted test files for security
- [ ] Changed default admin password

---

## Need More Help?

If authentication still doesn't work after these fixes:

1. Check full guides:
   - `AUTH_FIX_GUIDE.md` - Comprehensive troubleshooting
   - `XAMPP_SETUP.md` - Complete XAMPP setup
   - `DEPLOYMENT_GUIDE.md` - Quick deployment guide

2. Check logs:
   - Apache: `C:\xampp\apache\logs\error.log`
   - PHP: `C:\xampp\php\logs\php_error_log`

3. Enable detailed errors in `api/config.php`:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

---

**Status:** ✅ FIXED - Ready to use!
**Last Updated:** December 10, 2025
**Issues Fixed:** Database schema + API path construction
