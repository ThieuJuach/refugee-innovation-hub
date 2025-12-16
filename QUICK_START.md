# Quick Start Guide - Refugee Innovation Hub

## 🚀 Get Started in 5 Minutes

### Step 1: Install XAMPP (2 minutes)
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP (accept all defaults)
3. Start XAMPP Control Panel
4. Start **Apache** and **MySQL** services

### Step 2: Setup Project (2 minutes)
1. Copy the project folder to `C:\xampp\htdocs\refugee-innovation-hub`
2. Open browser: `http://localhost/phpmyadmin`
3. Create database: `refugee_innovation_hub`
4. Click "Import" and select `database/schema.sql`
5. Click "Go" to import

### Step 3: Setup Database (1 minute)
1. Open browser: `http://localhost/refugee-innovation-hub/setup-database.php`
2. This will:
   - Create default admin user
   - Add sample stories
   - Verify database structure
3. Note the admin credentials shown

### Step 4: Configure Google Analytics (Optional)
1. Go to https://analytics.google.com
2. Create a property
3. Copy your Measurement ID (G-XXXXXXXXXX)
4. Replace `G-XXXXXXXXXX` in `index.html` with your ID

### Step 5: Run the Application
1. Open browser: `http://localhost/refugee-innovation-hub/`
2. Browse the site
3. Click "Sign In" to access admin dashboard
4. Use credentials: `admin@jrsusa.org` / `admin123`

## ✅ Test the Application

1. **Home Page**: Should show featured stories
2. **Story Gallery**: Browse and filter stories
3. **Submit Story**: Fill out the form and submit
4. **Map**: View stories on interactive map
5. **Admin Login**: Sign in to review submissions

## 🔑 Create Additional Admin Users

1. Run the script: `http://localhost/refugee-innovation-hub/api/generate-password.php?password=yourpassword`
2. Copy the generated hash
3. Open phpMyAdmin: `http://localhost/phpmyadmin`
4. Go to `users` table
5. Insert new row with email and password hash

Or use SQL:
```sql
INSERT INTO users (email, password, full_name, role, created_at)
VALUES ('newemail@jrsusa.org', 'PASTE_HASH_HERE', 'Admin Name', 'admin', NOW());
```

## 📝 Sample Data

The setup script includes 5 sample stories that demonstrate:
- Different regions (East Africa, Middle East, Southeast Asia)
- Different themes (Education, Health, Livelihoods, Arts & Culture)
- Location coordinates for map display

## 🎯 Next Steps

- Review `ADMIN_GUIDE.md` for content management
- Customize colors and branding in `css/styles.css`
- Add your own stories through the submission form
- Update default admin password in phpMyAdmin

## ❓ Need Help?

- Check `README.md` for detailed documentation
- Review `ADMIN_GUIDE.md` for admin tasks
- See `XAMPP_SETUP.md` for troubleshooting
- Check browser console for errors
- Verify XAMPP services are running

## 🔧 Troubleshooting

### Apache won't start
- Port 80 may be in use by another service
- Stop IIS or other web servers
- Or configure Apache to use different port

### MySQL won't start
- Port 3306 may be in use
- Check if another MySQL instance is running

### Login doesn't work
- Run `test-auth.php` to verify database
- Check `api/config.php` database settings
- Verify `users` table exists

---

**Ready to go!** 🎉
