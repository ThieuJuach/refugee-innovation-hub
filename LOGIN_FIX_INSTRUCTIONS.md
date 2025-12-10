# Login Issue - Fix Instructions

## Problem
Cannot login to the Refugee Innovation Hub admin dashboard.

## Solution Steps

Follow these steps **IN ORDER** to fix your login issue:

---

### Step 1: Make Sure XAMPP is Running

1. Open XAMPP Control Panel
2. Make sure **Apache** is running (green highlight)
3. Make sure **MySQL** is running (green highlight)
4. If they're not running, click "Start" for both

---

### Step 2: Setup the Database

1. Open your web browser
2. Go to: `http://localhost/refugee-innovation-hub/setup-database.php`
3. This script will:
   - Create the database if it doesn't exist
   - Create all required tables
   - Create the default admin user
   - Show you confirmation messages

4. **IMPORTANT:** Look for green checkmarks (✅) - they mean success
5. If you see any red X marks (❌), read the error message

**Expected Result:** You should see "Setup Complete!" at the bottom with login credentials.

---

### Step 3: Test the Authentication System

1. Go to: `http://localhost/refugee-innovation-hub/test-auth.php`
2. This script will run 9 tests to verify everything is working
3. Check that all tests pass (green checkmarks ✅)

**What Each Test Does:**
- Test 1: Checks database connection
- Test 2: Checks if users table exists
- Test 3: Shows table structure
- Test 4: Counts users in database
- Test 5: Lists all users
- Test 6: Checks for admin user
- Test 7: Tests password verification
- Test 8: Tests PHP sessions
- Test 9: Simulates a complete login

**Expected Result:** All 9 tests should pass with green checkmarks.

---

### Step 4: Try Logging In

1. Go to: `http://localhost/refugee-innovation-hub/index.html`
2. Click "Sign In" in the navigation
3. Use these credentials:
   - **Email:** `admin@jrsusa.org`
   - **Password:** `admin123`
4. Click "Sign In"

**Expected Result:** You should be redirected to the Dashboard.

---

## Default Login Credentials

```
Email: admin@jrsusa.org
Password: admin123
```

**IMPORTANT:** Change this password after your first login!

---

## Troubleshooting

### If Step 2 (setup-database.php) Fails:

**Problem:** "Cannot connect to MySQL"
- **Solution:** Make sure MySQL is started in XAMPP Control Panel

**Problem:** "Database connection failed"
- **Solution:**
  1. Open phpMyAdmin: `http://localhost/phpmyadmin`
  2. Check if you can see the left sidebar with databases
  3. If not, MySQL is not running properly

### If Step 3 (test-auth.php) Shows Errors:

**Problem:** Test 6 fails - "Default admin user not found"
- **Solution:** Run setup-database.php again (Step 2)

**Problem:** Test 7 fails - "Password does NOT match"
- **Solution:**
  1. The test will show you SQL to fix it
  2. Go to phpMyAdmin: `http://localhost/phpmyadmin`
  3. Click on `refugee_innovation_hub` database
  4. Click "SQL" tab
  5. Copy and paste the UPDATE SQL shown in the test
  6. Click "Go"

**Problem:** Test 8 fails - "PHP sessions not working"
- **Solution:**
  1. Check if Apache is running in XAMPP
  2. Restart Apache in XAMPP Control Panel
  3. Run the test again

### If Step 4 (Login) Fails:

**Problem:** "Invalid email or password"
- **Solution:**
  1. Make sure you're using the exact credentials (case-sensitive)
  2. Email: `admin@jrsusa.org` (all lowercase)
  3. Password: `admin123` (all lowercase, no spaces)

**Problem:** Login button does nothing
- **Solution:**
  1. Open browser Developer Tools (F12)
  2. Go to "Console" tab
  3. Check for JavaScript errors
  4. Look for any red error messages
  5. Take a screenshot and share it

**Problem:** "Network error" or "Fetch failed"
- **Solution:**
  1. Check that Apache is running
  2. Verify the URL is: `http://localhost/refugee-innovation-hub/index.html`
  3. Open Developer Tools (F12)
  4. Go to "Network" tab
  5. Try logging in again
  6. Look for failed requests (red)
  7. Click on them to see details

---

## Additional Verification

### Check Browser Console
1. Open the login page
2. Press F12 to open Developer Tools
3. Go to "Console" tab
4. Look for any errors (red text)
5. Try logging in
6. Watch for new errors

### Check Network Requests
1. Open Developer Tools (F12)
2. Go to "Network" tab
3. Try logging in
4. Look for a request to `auth.php?action=login`
5. Click on it to see:
   - Status (should be 200)
   - Response (should show user data)
   - Any errors

---

## Files Created/Modified

The following files were created or modified to fix the login issue:

### New Files:
- `setup-database.php` - Automated database setup
- `test-auth.php` - Authentication system testing
- `LOGIN_FIX_INSTRUCTIONS.md` - This file

### Modified Files:
- `api/config.php` - Fixed session handling
- `api/auth.php` - Fixed session handling
- `js/auth.js` - Using direct URLs
- `api/php-api-client.js` - Using direct URLs
- `js/app.js` - Using direct URLs

---

## What Was Fixed

1. **Session Management:** Added proper session status checks to prevent warnings
2. **URL Structure:** Changed from dynamic URL construction to direct URLs
3. **Database Setup:** Created automated setup script
4. **Testing Tools:** Created comprehensive authentication testing script

---

## Next Steps After Login Works

1. Change the default admin password
2. Delete or move the test scripts:
   - `test-auth.php`
   - `setup-database.php`
3. Start adding your stories!

---

## Still Having Issues?

If you've followed all steps and login still doesn't work:

1. Run `test-auth.php` again
2. Take a screenshot of the results
3. Open browser Developer Tools (F12)
4. Go to Console tab
5. Take a screenshot of any errors
6. Share both screenshots for further help

---

## Contact

If you need additional help, provide:
- Screenshot of `test-auth.php` results
- Screenshot of browser console errors
- The exact error message you're seeing
