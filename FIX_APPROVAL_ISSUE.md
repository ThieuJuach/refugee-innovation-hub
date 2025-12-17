# Fix Story Approval Issue

## The Problem

Story approvals were failing because the `innovation_stories` table was missing the `impact` column and had an incorrect data type for the `is_featured` column.

## The Solution

Run the SQL fix script to update your database structure.

## Steps to Fix

### Option 1: Using phpMyAdmin (Easiest)

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select the `refugee_innovation_hub` database from the left sidebar
3. Click on the "SQL" tab at the top
4. Copy and paste this SQL:

```sql
-- Add impact column if it doesn't exist
ALTER TABLE innovation_stories
ADD COLUMN IF NOT EXISTS impact TEXT NULL AFTER innovator_name;

-- Fix is_featured column type
ALTER TABLE innovation_stories
MODIFY COLUMN is_featured TINYINT(1) DEFAULT 0;

-- Fix users table if it has is_active column
ALTER TABLE users
MODIFY COLUMN is_active TINYINT(1) DEFAULT 1;
```

5. Click "Go" button
6. You should see success messages

### Option 2: Run the Fix Script

1. Open your project folder
2. Navigate to `database/fix_approval_issue.sql`
3. Import it via phpMyAdmin's Import feature

## Test the Fix

1. Go to your application: `http://localhost/refugee-innovation-hub/`
2. Login as admin
3. Go to Dashboard
4. Try approving a pending submission
5. It should now work without errors!

## What Was Fixed

1. **Added `impact` column** - This field stores the impact description from submissions
2. **Fixed `is_featured` type** - Changed from BOOLEAN to TINYINT(1) for MySQL compatibility
3. **Updated API code** - Changed PHP code to use `0` instead of `false` for boolean fields

## If You Still Have Issues

1. Check browser console for errors (F12 → Console tab)
2. Check PHP error logs in XAMPP
3. Verify you're logged in as admin
4. Make sure XAMPP Apache and MySQL are running
5. Check that the database table was updated:
   - phpMyAdmin → refugee_innovation_hub → innovation_stories → Structure
   - Look for `impact` column and `is_featured` TINYINT(1) type

## Need to Reset Everything?

If you want to start fresh:

1. Drop the database in phpMyAdmin
2. Re-import `database/schema.sql`
3. Run `http://localhost/refugee-innovation-hub/setup-database.php`
