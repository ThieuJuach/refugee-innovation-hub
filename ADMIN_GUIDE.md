# Refugee Innovation Hub - Admin Guide

## Overview
This guide provides instructions for JRS staff on how to manage content, review submissions, and maintain the Refugee Innovation Hub microsite.

## Table of Contents
1. [Accessing the Admin Dashboard](#accessing-the-admin-dashboard)
2. [Reviewing Story Submissions](#reviewing-story-submissions)
3. [Managing Published Stories](#managing-published-stories)
4. [Viewing Analytics](#viewing-analytics)
5. [Content Management Best Practices](#content-management-best-practices)
6. [Troubleshooting](#troubleshooting)

---

## Accessing the Admin Dashboard

### Step 1: Sign In
1. Navigate to the microsite homepage
2. Click "Sign In" in the navigation menu
3. Enter your admin email and password
4. Click "Sign In"

**Note:** Admin accounts are stored in the MySQL database. Contact your system administrator to create new admin accounts. See `database/add_admin_users.sql` for examples.

### Step 2: Access Dashboard
After signing in, you'll see a "Dashboard" option in the navigation menu. Click it to access the admin panel.

---

## Reviewing Story Submissions

### Viewing Pending Submissions
1. Go to the Dashboard
2. Scroll to the "Pending Submissions" section
3. You'll see a table with all submissions awaiting review

### Approving a Submission
1. Review the submission details in the table
2. Click the "Approve" button next to the submission
3. Confirm the approval in the popup dialog
4. The story will be automatically published to the site

**What happens when you approve:**
- The story is added to the `innovation_stories` table
- It becomes visible in the Story Gallery
- It may appear on the homepage if marked as featured
- An analytics event is recorded

### Rejecting a Submission
1. Click the "Reject" button next to the submission
2. Confirm the rejection in the popup dialog
3. The submission status is updated to "rejected"

**Note:** Rejected submissions remain in the database but are not published. You can manually update the status later if needed.

---

## Managing Published Stories

### Viewing Published Stories
Published stories appear in:
- **Homepage:** Featured stories (marked with `is_featured: true`)
- **Story Gallery:** All published stories
- **Map Page:** Stories with location coordinates

### Making a Story Featured
To feature a story on the homepage:
1. Access phpMyAdmin at `http://localhost/phpmyadmin`
2. Navigate to the `innovation_stories` table
3. Find the story you want to feature
4. Edit the record and set `is_featured` to `1`
5. Save the changes

### Updating Story Information
1. Access phpMyAdmin at `http://localhost/phpmyadmin`
2. Navigate to `innovation_stories` table
3. Find and edit the story record
4. Update any fields as needed:
   - Title, description, impact
   - Location and coordinates (for map display)
   - Image URL
   - Contact information
   - Beneficiaries count
5. Save changes

### Adding Location Coordinates
To display stories on the interactive map:
1. Access phpMyAdmin and find the story
2. Add `latitude` and `longitude` values (decimal format)
3. Example: Nairobi, Kenya = `-1.2921, 36.8219`

**Finding Coordinates:**
- Use Google Maps: Right-click location → Coordinates
- Use online tools like latlong.net

---

## Viewing Analytics

### Dashboard Statistics
The admin dashboard displays:
- **Published Stories:** Total number of published innovations
- **Total Views:** Combined view count across all stories
- **Pending Submissions:** Number of submissions awaiting review
- **Total Submissions:** All-time submission count

### Google Analytics
For detailed analytics:
1. Access Google Analytics dashboard
2. Navigate to your property (ID: G-XXXXXXXXXX - replace with your actual ID)
3. View reports for:
   - Page views
   - User engagement
   - Story views
   - Submission activity

### Database Analytics
Analytics events are stored in the `site_analytics` table:
- `page_view`: When users visit pages
- `story_view`: When users view individual stories
- `submission`: When users submit stories
- `submission_approved`: When admins approve submissions

**To view analytics data:**
1. Access phpMyAdmin at `http://localhost/phpmyadmin`
2. Navigate to `site_analytics` table
3. Sort by `created_at` to see recent events

---

## Content Management Best Practices

### Story Quality Guidelines
When reviewing submissions, ensure:
- ✅ Clear, compelling title
- ✅ Detailed description of the innovation
- ✅ Specific impact metrics (number of beneficiaries, outcomes)
- ✅ High-quality image (if provided)
- ✅ Accurate location information
- ✅ Valid contact information

### Image Guidelines
- Recommended size: 1200x800 pixels
- Format: JPG or PNG
- File size: Under 2MB
- Use descriptive alt text
- Ensure images are relevant and high-quality

### Location Data
- Always include city and country
- Add coordinates for map display when possible
- Verify location accuracy

### Featured Stories
- Feature 3-6 stories on homepage
- Rotate featured stories regularly
- Prioritize stories with strong impact metrics
- Ensure geographic diversity

---

## Manual Content Updates (Alternative Method)

If you prefer not to use the admin dashboard, you can manage content directly through phpMyAdmin:

### Using phpMyAdmin
1. Go to `http://localhost/phpmyadmin`
2. Select `refugee_innovation_hub` database
3. Navigate to "Tables" view
4. Select the table you want to edit:
   - `innovation_stories` - Published stories
   - `story_submissions` - Pending submissions
   - `site_analytics` - Analytics data
   - `users` - Admin users

### Bulk Updates via SQL
For advanced users:
1. Create SQL INSERT statements for multiple stories
2. Execute via phpMyAdmin SQL tab
3. Or use MySQL command line

---

## Troubleshooting

### Can't Sign In
- Verify your email and password
- Check if your account exists in Firebase
- Contact system administrator for account creation

### Stories Not Appearing
- Check if story status is "approved" in `story_submissions`
- Verify story exists in `innovation_stories` table
- Clear browser cache and refresh

### Map Not Loading
- Check if Leaflet.js is loaded (check browser console)
- Verify stories have latitude/longitude coordinates
- Ensure internet connection is active

### Analytics Not Tracking
- Verify Google Analytics ID is correct in `index.html`
- Check browser console for errors
- Ensure MySQL database connection is working
- Check `site_analytics` table in phpMyAdmin

### Image Not Displaying
- Verify image URL is accessible
- Check if URL uses HTTPS
- Ensure image is not blocked by CORS

---

## Support and Resources

### Technical Support
- **Database Issues:** Check phpMyAdmin and verify XAMPP services are running
- **PHP Issues:** Check Apache error logs in XAMPP
- **Code Issues:** Review browser console for errors

### Documentation
- **PHP Docs:** https://www.php.net/docs.php
- **MySQL Docs:** https://dev.mysql.com/doc/
- **Leaflet.js Docs:** https://leafletjs.com/reference.html
- **XAMPP Guide:** See XAMPP_SETUP.md

### Contact
For additional support, contact:
- JRS/USA IT Team
- System Administrator
- Project Developer

---

## Quick Reference

### Database Tables
- `innovation_stories` - Published stories
- `story_submissions` - Pending/approved/rejected submissions
- `site_analytics` - Analytics events

### Key Fields
- `is_featured` - Show on homepage (true/false)
- `status` - Submission status (pending/approved/rejected)
- `latitude` / `longitude` - Map coordinates
- `view_count` - Story view counter

### Common Tasks
1. **Approve submission:** Dashboard → Approve button
2. **Feature story:** phpMyAdmin → Edit `is_featured` field
3. **Add coordinates:** phpMyAdmin → Add `latitude`/`longitude`
4. **View analytics:** Dashboard stats or phpMyAdmin → `site_analytics` table

---

**Last Updated:** November 2024
**Version:** 1.0

