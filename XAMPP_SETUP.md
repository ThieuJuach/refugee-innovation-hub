# XAMPP Setup Guide - Refugee Innovation Hub

This guide will help you set up the Refugee Innovation Hub microsite on XAMPP with MySQL and PHP.

## Prerequisites

1. **XAMPP** installed on your computer
   - Download from: https://www.apachefriends.org/
   - Install XAMPP (includes Apache, MySQL, PHP, phpMyAdmin)

## Step-by-Step Setup

### Step 1: Install XAMPP

1. Download XAMPP from the official website
2. Run the installer
3. Select components: Apache, MySQL, PHP, phpMyAdmin
4. Choose installation directory (default: `C:\xampp`)
5. Complete the installation

### Step 2: Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Start **Apache** server (click "Start" button)
3. Start **MySQL** server (click "Start" button)
4. Both should show green "Running" status

### Step 3: Copy Project Files

1. Copy the entire `ProjectJRS1-main` folder to:
   ```
   C:\xampp\htdocs\refugee-innovation-hub
   ```
   Or any location you prefer within the `htdocs` folder.

2. Your project structure should be:
   ```
   C:\xampp\htdocs\refugee-innovation-hub\
   ├── api\
   ├── css\
   ├── js\
   ├── database\
   ├── index.html
   └── ...
   ```

### Step 4: Create Database

1. Open your web browser
2. Go to: `http://localhost/phpmyadmin`
3. Click on **"New"** in the left sidebar to create a new database
4. Database name: `refugee_innovation_hub`
5. Collation: `utf8mb4_unicode_ci`
6. Click **"Create"**

### Step 5: Import Database Schema

**Option A: Using phpMyAdmin (Recommended)**

1. In phpMyAdmin, select the `refugee_innovation_hub` database
2. Click on the **"Import"** tab
3. Click **"Choose File"**
4. Select: `database/schema.sql` from your project folder
5. Click **"Go"** at the bottom
6. You should see "Import has been successfully finished"

**Option B: Using MySQL Command Line**

1. Open Command Prompt
2. Navigate to XAMPP MySQL bin directory:
   ```bash
   cd C:\xampp\mysql\bin
   ```
3. Run:
   ```bash
   mysql -u root -p < "C:\xampp\htdocs\refugee-innovation-hub\database\schema.sql"
   ```
   (Press Enter when prompted for password - default is empty)

### Step 6: Configure Database Connection

1. Open `api/config.php` in a text editor
2. Verify these settings (default XAMPP settings):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'refugee_innovation_hub');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Empty by default in XAMPP
   ```
3. Save the file

### Step 7: Set Up Image Uploads Directory

1. The `uploads/stories/` directory should already exist
2. **Set proper permissions** (if needed):
   - Right-click on `uploads` folder → Properties → Security
   - Ensure the folder is writable by the web server
   - On Windows, this is usually automatic, but verify if uploads fail

3. **Verify directory exists:**
   - Check that `uploads/stories/` folder exists
   - If not, create it manually

### Step 8: Change Default Admin Password

**Important:** The default admin password is `admin123`. Change it immediately!

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select `refugee_innovation_hub` database
3. Click on `users` table
4. Click "Edit" on the admin user row
5. In the `password_hash` field, generate a new hash:
   - Go to: `http://localhost/refugee-innovation-hub/api/generate-password.php` (create this file - see below)
   - Or use PHP code:
     ```php
     <?php echo password_hash('your-new-password', PASSWORD_DEFAULT); ?>
     ```
6. Replace the `password_hash` value
7. Save

**Create password generator file** (`api/generate-password.php`):
```php
<?php
if (isset($_GET['password'])) {
    echo password_hash($_GET['password'], PASSWORD_DEFAULT);
} else {
    echo "Usage: ?password=yourpassword";
}
?>
```

Then visit: `http://localhost/refugee-innovation-hub/api/generate-password.php?password=yournewpassword`

### Step 9: Access the Application

1. Open your web browser
2. Go to: `http://localhost/refugee-innovation-hub/`
3. You should see the homepage!

### Step 9: Test Admin Login

1. Click "Sign In" in the navigation
2. Use these credentials:
   - **Email:** `admin@jrsusa.org`
   - **Password:** `admin123` (or your new password)
3. You should be redirected to the dashboard

## Troubleshooting

### Apache Won't Start

**Port 80 is already in use:**
1. Open XAMPP Control Panel
2. Click "Config" next to Apache
3. Select "httpd.conf"
4. Find `Listen 80` and change to `Listen 8080`
5. Save and restart Apache
6. Access site at: `http://localhost:8080/refugee-innovation-hub/`

### MySQL Won't Start

**Port 3306 is already in use:**
1. Open XAMPP Control Panel
2. Click "Config" next to MySQL
3. Select "my.ini"
4. Find `port=3306` and change to `port=3307`
5. Update `api/config.php` to use port 3307:
   ```php
   define('DB_HOST', 'localhost:3307');
   ```
6. Save and restart MySQL

### Database Connection Error

1. Check if MySQL is running in XAMPP Control Panel
2. Verify database name in `api/config.php`
3. Check username and password (default: root, empty password)
4. Verify database exists in phpMyAdmin

### PHP Errors

1. Check PHP error logs: `C:\xampp\php\logs\php_error_log`
2. Enable error display in `api/config.php` (add at top):
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

### CORS Errors

If you see CORS errors in browser console:
1. The `api/config.php` already includes CORS headers
2. Make sure you're accessing via `http://localhost` not `file://`

### Session Issues

1. Check PHP session directory is writable
2. In `api/config.php`, you can set session path:
   ```php
   ini_set('session.save_path', 'C:/xampp/tmp');
   ```

## File Permissions

Make sure these directories are writable (if needed):
- `C:\xampp\tmp` - For PHP sessions
- Database files (usually handled automatically)

## Security Notes

### For Production:

1. **Change default MySQL root password:**
   - In XAMPP Control Panel → MySQL → Config → my.ini
   - Or use phpMyAdmin to change password

2. **Update JWT_SECRET in `api/config.php`:**
   ```php
   define('JWT_SECRET', 'your-random-secret-key-here');
   ```

3. **Disable error display in production:**
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

4. **Use HTTPS in production**

5. **Restrict database user permissions**

## Default Credentials

- **Database:** `refugee_innovation_hub`
- **MySQL User:** `root`
- **MySQL Password:** (empty by default)
- **Admin Email:** `admin@jrsusa.org`
- **Admin Password:** `admin123` (CHANGE THIS!)

## Project Structure in XAMPP

```
C:\xampp\htdocs\refugee-innovation-hub\
├── api\                    # PHP API endpoints
│   ├── config.php         # Database configuration
│   ├── stories.php        # Stories API
│   ├── submissions.php    # Submissions API
│   ├── auth.php           # Authentication API
│   ├── analytics.php      # Analytics API
│   └── stats.php          # Statistics API
├── css\                   # Stylesheets
├── js\                    # JavaScript files
│   ├── app.js            # Main application
│   └── auth.js           # Authentication
├── database\              # Database files
│   └── schema.sql        # Database schema
├── index.html            # Main HTML file
└── ...
```

## Access URLs

- **Homepage:** `http://localhost/refugee-innovation-hub/`
- **phpMyAdmin:** `http://localhost/phpmyadmin`
- **API Endpoint:** `http://localhost/refugee-innovation-hub/api/stories.php`

## Next Steps

1. ✅ Database is set up
2. ✅ Application is running
3. ✅ Admin can log in
4. 📝 Review `ADMIN_GUIDE.md` for content management
5. 📝 Customize the application as needed
6. 📝 Add more stories through the submission form

## Support

If you encounter issues:
1. Check XAMPP error logs
2. Check browser console for JavaScript errors
3. Check PHP error logs
4. Verify all services are running in XAMPP Control Panel
5. Review this guide again

---

**Setup Complete!** 🎉

Your Refugee Innovation Hub is now running on XAMPP!

