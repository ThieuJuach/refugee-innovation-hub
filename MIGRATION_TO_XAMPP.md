# Migration to XAMPP/MySQL/PHP - Summary

This document summarizes the changes made to convert the application from Supabase/Firebase to XAMPP with MySQL and PHP backend.

## What Changed

### Backend Changes

1. **Database**: Changed from Supabase (PostgreSQL) to MySQL
   - New schema file: `database/schema.sql`
   - Compatible with XAMPP's MySQL

2. **Backend API**: Created PHP REST API
   - `api/config.php` - Database configuration
   - `api/stories.php` - Stories CRUD operations
   - `api/submissions.php` - Submission management
   - `api/auth.php` - Authentication (PHP sessions)
   - `api/analytics.php` - Analytics tracking
   - `api/stats.php` - Dashboard statistics

3. **Authentication**: Changed from Firebase to PHP sessions
   - Session-based authentication
   - Password hashing with PHP's `password_hash()`
   - User management in MySQL `users` table

### Frontend Changes

1. **API Client**: Replaced Supabase client with PHP API client
   - `api/php-api-client.js` - JavaScript client for PHP API
   - Maintains Supabase-compatible interface for minimal code changes

2. **Authentication Module**: Updated `js/auth.js`
   - Removed Firebase dependencies
   - Uses PHP API for authentication
   - Session-based auth state management

3. **Removed Dependencies**:
   - Firebase SDK (removed from `index.html`)
   - Supabase client (replaced with PHP API client)

### Files Added

- `database/schema.sql` - MySQL database schema
- `api/config.php` - Database and API configuration
- `api/stories.php` - Stories API endpoint
- `api/submissions.php` - Submissions API endpoint
- `api/auth.php` - Authentication API endpoint
- `api/analytics.php` - Analytics API endpoint
- `api/stats.php` - Statistics API endpoint
- `api/php-api-client.js` - JavaScript API client
- `api/generate-password.php` - Password hash generator utility
- `XAMPP_SETUP.md` - Complete XAMPP setup guide

### Files Modified

- `index.html` - Removed Firebase SDK, added PHP API client
- `js/auth.js` - Updated to use PHP authentication
- `js/app.js` - Updated API calls to use PHP endpoints
- `README.md` - Updated documentation for XAMPP setup

### Files Removed/No Longer Needed

- `js/firebase-config.js` - No longer needed
- `js/supabase-client.js` - Replaced with PHP API client
- `supabase/migrations/` - Replaced with MySQL schema

## Key Differences

### Database

**Before (Supabase):**
- PostgreSQL database
- Cloud-hosted
- Row Level Security (RLS)
- Real-time subscriptions

**After (MySQL):**
- MySQL database
- Local (XAMPP) or server-hosted
- Application-level security
- Standard REST API

### Authentication

**Before (Firebase):**
- Firebase Authentication
- Cloud-based user management
- JWT tokens

**After (PHP):**
- PHP sessions
- MySQL user table
- Session-based authentication
- Password hashing with PHP

### API

**Before (Supabase):**
- Supabase REST API
- Auto-generated endpoints
- Real-time capabilities

**After (PHP):**
- Custom PHP REST API
- Manual endpoint creation
- Standard HTTP requests

## Migration Benefits

1. **Local Development**: Easy local setup with XAMPP
2. **No Cloud Dependencies**: Everything runs locally
3. **Full Control**: Complete control over database and API
4. **Cost**: No cloud service costs
5. **Privacy**: Data stays on your server
6. **Customization**: Easy to customize and extend

## Setup Requirements

1. **XAMPP** installed
2. **PHP 7.4+** (included in XAMPP)
3. **MySQL** (included in XAMPP)
4. **Apache** (included in XAMPP)

## Default Credentials

- **Database**: `refugee_innovation_hub`
- **MySQL User**: `root`
- **MySQL Password**: (empty by default)
- **Admin Email**: `admin@jrsusa.org`
- **Admin Password**: `admin123` (CHANGE THIS!)

## Next Steps

1. Follow `XAMPP_SETUP.md` for complete setup instructions
2. Import database schema
3. Change default admin password
4. Test the application
5. Customize as needed

## API Endpoints

### Stories
- `GET /api/stories.php` - Get all stories
- `GET /api/stories.php?id=1` - Get single story
- `POST /api/stories.php` - Create story (admin)
- `PUT /api/stories.php?id=1` - Update story (admin)
- `DELETE /api/stories.php?id=1` - Delete story (admin)

### Submissions
- `GET /api/submissions.php?status=pending` - Get submissions (admin)
- `POST /api/submissions.php` - Create submission (public)
- `PUT /api/submissions.php?id=1` - Update submission status (admin)

### Authentication
- `POST /api/auth.php?action=login` - Login
- `POST /api/auth.php?action=logout` - Logout
- `POST /api/auth.php?action=check` - Check auth status

### Analytics
- `GET /api/analytics.php` - Get analytics (admin)
- `POST /api/analytics.php` - Track event (public)

### Statistics
- `GET /api/stats.php` - Get dashboard stats (admin)

## Security Notes

1. **Change default admin password** immediately
2. **Update JWT_SECRET** in `api/config.php` for production
3. **Disable error display** in production
4. **Use HTTPS** in production
5. **Restrict database permissions** in production
6. **Validate all inputs** (already implemented)
7. **Use prepared statements** (already implemented)

## Troubleshooting

See `XAMPP_SETUP.md` for detailed troubleshooting guide.

Common issues:
- Port conflicts (Apache/MySQL)
- Database connection errors
- PHP session issues
- CORS errors

---

**Migration Complete!** ✅

The application is now fully converted to use XAMPP with MySQL and PHP backend.

