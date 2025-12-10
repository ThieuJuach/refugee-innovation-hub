# Quick Deployment Guide - Refugee Innovation Hub

## What You Need to Know

Your Refugee Innovation Hub is now **100% ready** for deployment on XAMPP. All requested changes have been implemented:

✅ **Story submission restricted to admins only**
✅ **Admins can delete stories from dashboard**
✅ **JRS USA fonts and branding applied (Open Sans font, JRS colors)**
✅ **Full XAMPP compatibility with relative paths**
✅ **All MVP features verified and working**

## Quick Start (3 Steps)

### Step 1: Copy to XAMPP
1. Copy the entire project folder to: `C:\xampp\htdocs\`
2. Rename the folder to something simple like `refugee-hub` (optional)

**Result:** Your project is now at `C:\xampp\htdocs\refugee-hub\`

### Step 2: Set Up Database
1. Start XAMPP and run **Apache** and **MySQL**
2. Open browser and go to: `http://localhost/phpmyadmin`
3. Click **"New"** to create database
4. Database name: `refugee_innovation_hub`
5. Click **"Import"** tab
6. Choose file: `database/schema.sql`
7. Click **"Go"**

**Result:** Database is ready with all tables and sample data!

### Step 3: Access Your Site
1. Open browser
2. Go to: `http://localhost/refugee-hub/` (or whatever you named the folder)
3. Click **"Sign In"**
4. Login credentials:
   - **Email:** admin@jrsusa.org
   - **Password:** admin123

**Result:** You're in! 🎉

## What Changed (Summary)

### 1. Submit Story - Admin Only
- **Before:** Anyone could submit stories
- **After:** Only logged-in admins can submit stories
- **User sees:** "Admin Access Required" message if not logged in

### 2. Delete Stories Functionality
- **New Feature:** Admin dashboard now shows "Published Stories" section
- **Actions:** View and Delete buttons for each story
- **Safety:** Confirmation dialog before deletion

### 3. JRS USA Branding
- **Font:** Changed to Open Sans (matches JRS USA website)
- **Colors:** Updated to JRS USA color scheme:
  - Primary Blue: #214885
  - Teal Accent: #38C5B5
  - Orange Links: #dc7d11
- **Result:** Professional, consistent branding throughout

### 4. JavaScript File Improvements
- **auth.js:** Now uses relative paths (works anywhere in XAMPP)
- **app.js:** Added admin checks and delete functionality
- **Result:** More reliable, easier to deploy

### 5. XAMPP Compatibility
- **Before:** Hardcoded localhost paths
- **After:** Automatic path detection
- **Benefit:** Works in any folder, any setup

## Testing Your Deployment

### Test as Public User (Not Logged In)
1. ✅ Go to homepage - should see stories and hero section
2. ✅ Click "Stories" - should see gallery with filters
3. ✅ Click on a story - should see full details
4. ✅ Click "Map" - should see interactive map
5. ✅ Click "Submit Story" - should see **"Admin Access Required"**

### Test as Admin (Logged In)
1. ✅ Click "Sign In" and log in
2. ✅ Should see "Dashboard" link in navigation
3. ✅ Go to Dashboard - should see statistics
4. ✅ See "Pending Submissions" section
5. ✅ See **NEW "Published Stories" section**
6. ✅ Click "View" on a story - should navigate to story
7. ✅ Click "Delete" on a story - should ask for confirmation
8. ✅ Confirm delete - story should be removed
9. ✅ Click "Submit Story" - should see form (admin only)
10. ✅ Submit a story - should appear in pending submissions

## Important Security Steps

### CHANGE DEFAULT PASSWORD (Required!)

**Method 1: Using Password Generator**
1. Go to: `http://localhost/refugee-hub/api/generate-password.php?password=YourNewPassword123`
2. Copy the hash that appears
3. Go to: `http://localhost/phpmyadmin`
4. Open `refugee_innovation_hub` database
5. Click `users` table
6. Click "Edit" on the admin user
7. Paste the hash into `password_hash` field
8. Save

**Method 2: Using phpMyAdmin**
1. Go to: `http://localhost/phpmyadmin`
2. Open `refugee_innovation_hub` database
3. Click SQL tab
4. Run this SQL (replace with your password):
```sql
UPDATE users
SET password_hash = '$2y$10$...'
WHERE email = 'admin@jrsusa.org';
```

## File Structure Overview

```
refugee-hub/
├── api/                    # Backend PHP files
│   ├── auth.php           # ✨ Updated - relative paths
│   ├── stories.php        # Story operations
│   ├── submissions.php    # Submission handling
│   └── ...
├── css/
│   └── styles.css         # ✨ Updated - JRS branding
├── js/
│   ├── app.js             # ✨ Updated - admin checks & delete
│   └── auth.js            # ✨ Updated - relative paths
├── database/
│   └── schema.sql         # Database structure
├── uploads/
│   └── stories/           # Image uploads go here
├── index.html             # Main page
├── CHANGES_SUMMARY.md     # Detailed change log
├── XAMPP_SETUP.md         # Full setup instructions
└── DEPLOYMENT_GUIDE.md    # This file
```

## Troubleshooting

### Port Already in Use
**Problem:** Apache won't start, port 80 in use
**Solution:**
1. XAMPP Control Panel → Config → httpd.conf
2. Change `Listen 80` to `Listen 8080`
3. Access site at: `http://localhost:8080/refugee-hub/`

### Database Connection Failed
**Problem:** Can't connect to database
**Solution:**
1. Check MySQL is running (green in XAMPP)
2. Verify database name in `api/config.php`
3. Default password is empty (no password)

### Upload Failed
**Problem:** Can't upload images
**Solution:**
1. Check `uploads/stories/` folder exists
2. Verify folder is writable
3. Check file is under 5MB
4. Must be JPEG, PNG, GIF, or WebP

### Page Not Found
**Problem:** 404 error
**Solution:**
1. Verify folder name matches URL
2. Check Apache is running
3. Make sure `index.html` is in the root folder

## Production Readiness

### Current Status: ✅ MVP Complete

The application is ready for:
- ✅ Local testing and development
- ✅ Internal use by JRS field teams
- ✅ Story collection and management
- ✅ Public viewing of stories

### Before Going Live (Production):
When deploying to a public server:
1. Change `JWT_SECRET` in `api/config.php`
2. Set strong MySQL password
3. Disable error display in `api/config.php`
4. Enable HTTPS
5. Set up automatic backups
6. Add Google Analytics tracking ID

## Support Resources

### Documentation Files
- **CHANGES_SUMMARY.md** - All changes made in detail
- **XAMPP_SETUP.md** - Complete XAMPP setup guide
- **ADMIN_GUIDE.md** - How to use admin features
- **MVP_FEATURES.md** - All implemented features
- **README.md** - Project overview

### Default Credentials
- **Database:** refugee_innovation_hub
- **MySQL User:** root
- **MySQL Pass:** (empty)
- **Admin Email:** admin@jrsusa.org
- **Admin Pass:** admin123 ⚠️ CHANGE THIS!

### Access URLs (Default)
- **Site:** http://localhost/refugee-hub/
- **phpMyAdmin:** http://localhost/phpmyadmin
- **Password Gen:** http://localhost/refugee-hub/api/generate-password.php?password=newpass

## Next Steps

1. **Test everything** using the checklist above
2. **Change admin password** (critical!)
3. **Start using the system** - add real stories
4. **Share with field teams** - they can submit stories
5. **Monitor the dashboard** - approve/reject submissions

## Questions?

All features requested have been implemented:
1. ✅ Admin-only story submission
2. ✅ Delete story functionality
3. ✅ JavaScript improvements
4. ✅ XAMPP compatibility
5. ✅ JRS USA branding

The system is **fully functional** and **ready for deployment**.

---

**Version:** 1.0 Production Ready
**Last Updated:** December 10, 2025
**Status:** ✅ Ready to Deploy
