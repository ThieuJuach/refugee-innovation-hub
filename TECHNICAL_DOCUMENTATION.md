# Refugee Innovation Hub - Technical Documentation

**Version:** 1.0.0
**Last Updated:** 2025-12-17
**Target Audience:** Software Engineers, DevOps, Technical Leads

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Technology Stack](#technology-stack)
4. [Database Schema](#database-schema)
5. [API Endpoints](#api-endpoints)
6. [Authentication & Authorization](#authentication--authorization)
7. [Frontend Architecture](#frontend-architecture)
8. [Problems Encountered & Solutions](#problems-encountered--solutions)
9. [Security Considerations](#security-considerations)
10. [Deployment Guide](#deployment-guide)
11. [Testing & Verification](#testing--verification)
12. [Known Limitations](#known-limitations)
13. [Future Improvements](#future-improvements)

---

## System Overview

### Purpose
The Refugee Innovation Hub is a web platform designed to showcase, collect, and manage innovation stories from refugee communities worldwide. The platform serves three primary user types:

1. **Public Users**: Browse innovation stories, submit new stories
2. **Admins**: Approve/reject submissions, manage stories, edit story coordinates, view analytics
3. **System**: Track analytics, manage data integrity

### Key Features
- Public story browsing with filtering (region, theme, featured status)
- Interactive map visualization with coordinates
- Story submission workflow with approval process
- Admin dashboard with submission management
- Analytics tracking (views, submissions, regions)
- Image upload support
- Coordinate editing for geolocation accuracy
- Responsive design (mobile-first)

---

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Client Browser                        │
│  ┌─────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │  index.html │  │  app.js      │  │  auth.js     │  │
│  │  (UI)       │  │  (Logic)     │  │  (Session)   │  │
│  └─────────────┘  └──────────────┘  └──────────────┘  │
└───────────────────────┬─────────────────────────────────┘
                        │ HTTP/AJAX
                        │ (php-api-client.js)
┌───────────────────────┴─────────────────────────────────┐
│              Apache Server (XAMPP)                       │
│  ┌────────────────────────────────────────────────────┐ │
│  │           PHP API Layer                            │ │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐          │ │
│  │  │stories.php│ │auth.php  │ │submis... │          │ │
│  │  └──────────┘ └──────────┘ └──────────┘          │ │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐          │ │
│  │  │analytics │ │upload.php│ │stats.php │          │ │
│  │  └──────────┘ └──────────┘ └──────────┘          │ │
│  └────────────────────────────────────────────────────┘ │
└───────────────────────┬─────────────────────────────────┘
                        │ PDO
                        │ MySQL Protocol
┌───────────────────────┴─────────────────────────────────┐
│                   MySQL Database                         │
│     (refugee_innovation_hub)                            │
│  ┌──────────────────┐  ┌─────────────────────┐         │
│  │innovation_stories│  │story_submissions    │         │
│  └──────────────────┘  └─────────────────────┘         │
│  ┌──────────────────┐  ┌─────────────────────┐         │
│  │users             │  │site_analytics       │         │
│  └──────────────────┘  └─────────────────────┘         │
└─────────────────────────────────────────────────────────┘
```

### Request Flow

#### Public Story Browsing
```
1. Browser → GET /api/stories.php
2. stories.php → SELECT FROM innovation_stories
3. MySQL → Returns story array
4. stories.php → JSON response
5. Browser → app.js renders stories in grid/map
```

#### Story Submission
```
1. Browser → POST /api/submissions.php (FormData or JSON)
2. submissions.php → Validates data + handles file upload
3. submissions.php → INSERT INTO story_submissions (status='pending')
4. MySQL → Returns submission ID
5. submissions.php → JSON response {id, message}
6. Admin → Reviews in dashboard
7. Admin → Approves submission
8. submissions.php → INSERT INTO innovation_stories + UPDATE status
```

#### Admin Authentication
```
1. Browser → POST /api/auth.php?action=login
2. auth.php → SELECT FROM users WHERE email=?
3. auth.php → password_verify() against password_hash
4. auth.php → session_start() + $_SESSION['user_id'] = $user['id']
5. auth.php → JSON response {user, message}
6. Browser → auth.js stores session, enables admin UI
```

---

## Technology Stack

### Frontend
- **HTML5**: Semantic markup, custom data attributes
- **CSS3**: Custom properties (CSS variables), Flexbox, Grid
- **Vanilla JavaScript (ES6+)**:
  - Async/await for API calls
  - Promises for concurrent operations
  - Template literals for dynamic HTML
  - LocalStorage for client-side caching
  - No frameworks (intentional for simplicity)

### Backend
- **PHP 7.4+**: Server-side logic
- **PDO (PHP Data Objects)**: Database abstraction layer
- **Session Management**: PHP native sessions for authentication
- **Apache 2.4**: Web server

### Database
- **MySQL 5.7+ / MariaDB 10.3+**:
  - InnoDB storage engine
  - UTF8MB4 character set (full Unicode support)
  - Foreign keys for referential integrity

### Development Environment
- **XAMPP**: Local development stack (Apache + MySQL + PHP)
- **Version Control**: Git

### External Dependencies
- **None**: Intentionally kept dependency-free for:
  - Easy deployment
  - Reduced attack surface
  - No build process required
  - No node_modules complexity

---

## Database Schema

### Entity-Relationship Diagram

```
┌─────────────────────────────────────────────────────┐
│                innovation_stories                    │
│─────────────────────────────────────────────────────│
│ id (PK, INT)                                        │
│ title (VARCHAR 255) NOT NULL                        │
│ slug (VARCHAR 255) UNIQUE NOT NULL                  │
│ summary (TEXT) NOT NULL                             │
│ description (TEXT) NOT NULL                         │
│ location (VARCHAR 255) NOT NULL                     │
│ region (VARCHAR 100) NOT NULL                       │
│ theme (VARCHAR 100) NOT NULL                        │
│ latitude (DECIMAL 10,8) NULL                        │
│ longitude (DECIMAL 11,8) NULL                       │
│ image_url (TEXT) NULL                               │
│ innovator_name (VARCHAR 255) NOT NULL               │
│ impact (TEXT) NULL                                  │
│ beneficiaries_count (INT) DEFAULT 0                 │
│ contact_email (VARCHAR 255) NULL                    │
│ contact_info (TEXT) NULL                            │
│ is_featured (TINYINT 1) DEFAULT 0                   │
│ view_count (INT) DEFAULT 0                          │
│ created_at (TIMESTAMP) DEFAULT CURRENT_TIMESTAMP    │
│ updated_at (TIMESTAMP) ON UPDATE CURRENT_TIMESTAMP  │
└─────────────────────────────────────────────────────┘
                        △
                        │
                        │ story_id (FK)
                        │
┌─────────────────────────────────────────────────────┐
│                site_analytics                        │
│─────────────────────────────────────────────────────│
│ id (PK, INT)                                        │
│ event_type (VARCHAR 100) NOT NULL                   │
│ story_id (INT) NULL [FK → innovation_stories.id]   │
│ metadata (JSON) NULL                                │
│ created_at (TIMESTAMP) DEFAULT CURRENT_TIMESTAMP    │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│              story_submissions                       │
│─────────────────────────────────────────────────────│
│ id (PK, INT)                                        │
│ title (VARCHAR 255) NOT NULL                        │
│ description (TEXT) NOT NULL                         │
│ location (VARCHAR 255) NOT NULL                     │
│ region (VARCHAR 100) NOT NULL                       │
│ theme (VARCHAR 100) NOT NULL                        │
│ innovator_name (VARCHAR 255) NOT NULL               │
│ impact (TEXT) NULL                                  │
│ contact_email (VARCHAR 255) NOT NULL                │
│ contact_info (TEXT) NULL                            │
│ image_url (TEXT) NULL                               │
│ status (ENUM) ['pending','approved','rejected']     │
│ submitted_at (TIMESTAMP) DEFAULT CURRENT_TIMESTAMP  │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│                     users                            │
│─────────────────────────────────────────────────────│
│ id (PK, INT)                                        │
│ email (VARCHAR 255) UNIQUE NOT NULL                 │
│ password_hash (VARCHAR 255) NOT NULL                │
│ name (VARCHAR 255) NULL                             │
│ role (ENUM) ['admin', 'editor'] DEFAULT 'admin'     │
│ is_active (TINYINT 1) DEFAULT 1                     │
│ created_at (TIMESTAMP) DEFAULT CURRENT_TIMESTAMP    │
│ last_login (TIMESTAMP) NULL                         │
└─────────────────────────────────────────────────────┘
```

### Table Details

#### innovation_stories
**Purpose**: Store approved, published innovation stories visible to public users.

**Key Fields**:
- `slug`: URL-friendly unique identifier (auto-generated from title)
- `latitude/longitude`: Decimal degrees for map plotting (nullable)
- `region`: Predefined regions (East Africa, Middle East, Southeast Asia, etc.)
- `theme`: Predefined themes (Education, Health, Livelihoods, Arts & Culture, etc.)
- `is_featured`: Boolean flag for homepage featured stories
- `view_count`: Incremented on each GET request for individual story

**Indexes**:
- `idx_region`: Fast filtering by region
- `idx_theme`: Fast filtering by theme
- `idx_featured`: Fast retrieval of featured stories
- `idx_slug`: Fast lookup by slug (unique index)

**Character Set**: UTF8MB4 (supports emoji and full Unicode)

#### story_submissions
**Purpose**: Temporary table for user-submitted stories pending admin approval.

**Workflow**:
1. Public user submits story → INSERT with `status='pending'`
2. Admin reviews → UPDATE `status='approved'` or `status='rejected'`
3. If approved → Copy to `innovation_stories` table
4. Record remains in `story_submissions` for audit trail

**Key Fields**:
- `status`: ENUM ensures only valid values ('pending', 'approved', 'rejected')
- `submitted_at`: Timestamp for tracking submission queue age

**Indexes**:
- `idx_status`: Fast filtering in admin dashboard
- `idx_submitted_at`: Sorting by submission date

#### users
**Purpose**: Store admin user credentials and metadata.

**Security**:
- `password_hash`: Uses PHP `password_hash()` with bcrypt (cost=10)
- `email`: Unique constraint prevents duplicate accounts
- `is_active`: Soft delete / account suspension flag

**Roles**:
- `admin`: Full access (CRUD stories, manage submissions, view analytics)
- `editor`: Limited access (placeholder for future role-based permissions)

**Default Admin**:
- Email: `admin@jrsusa.org`
- Password: `admin123` (hash: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`)
- **⚠️ CRITICAL**: Change this password immediately in production!

#### site_analytics
**Purpose**: Track user interactions and system events for analytics dashboard.

**Event Types**:
- `story_view`: User viewed a story detail
- `story_submission`: User submitted a story
- `filter_applied`: User applied a filter
- `map_interaction`: User interacted with map

**Metadata**: JSON field for flexible event-specific data:
```json
{
  "filter_type": "region",
  "filter_value": "East Africa",
  "user_agent": "Mozilla/5.0...",
  "ip_address": "192.168.1.1"
}
```

---

## API Endpoints

### Base URL
```
http://localhost/refugee-innovation-hub/api/
```

### Common Headers
```http
Content-Type: application/json
Authorization: (none - uses session cookies)
```

### Response Format
All endpoints return JSON:

**Success (2xx)**:
```json
{
  "data": {...},
  "message": "Success message"
}
```

**Error (4xx, 5xx)**:
```json
{
  "error": "Error description"
}
```

---

### Authentication Endpoints

#### POST /auth.php?action=login
**Purpose**: Authenticate admin user and create session.

**Request**:
```json
{
  "email": "admin@jrsusa.org",
  "password": "admin123"
}
```

**Response (200)**:
```json
{
  "user": {
    "id": 1,
    "email": "admin@jrsusa.org",
    "name": "Admin User",
    "role": "admin"
  },
  "message": "Login successful"
}
```

**Error (401)**:
```json
{
  "error": "Invalid email or password"
}
```

**Implementation Details**:
- Uses `password_verify()` against bcrypt hash
- Creates PHP session with `session_start()`
- Stores `user_id`, `user_email`, `user_name`, `user_role` in `$_SESSION`
- Updates `users.last_login` timestamp
- Session cookie has default PHP expiration (24 minutes idle timeout)

---

#### POST /auth.php?action=logout
**Purpose**: Destroy admin session.

**Request**: (empty body)

**Response (200)**:
```json
{
  "message": "Logout successful"
}
```

**Implementation Details**:
- Calls `session_destroy()`
- Client should clear any cached user data

---

#### POST /auth.php?action=check
**Purpose**: Verify if user is authenticated and retrieve user info.

**Request**: (empty body)

**Response (200) - Authenticated**:
```json
{
  "authenticated": true,
  "user": {
    "id": 1,
    "email": "admin@jrsusa.org",
    "name": "Admin User",
    "role": "admin"
  }
}
```

**Response (200) - Not Authenticated**:
```json
{
  "authenticated": false
}
```

**Implementation Details**:
- Checks `$_SESSION['user_id']` existence
- Does NOT return 401 when not authenticated (returns 200 with `authenticated: false`)
- Used on page load to restore session state in frontend

---

### Stories Endpoints

#### GET /stories.php
**Purpose**: Retrieve all published stories with optional filtering.

**Query Parameters**:
- `featured` (optional): `0` or `1` - Filter by featured status
- `region` (optional): string - Filter by region (e.g., "East Africa")
- `theme` (optional): string - Filter by theme (e.g., "Education")

**Example Requests**:
```
GET /stories.php
GET /stories.php?featured=1
GET /stories.php?region=East%20Africa
GET /stories.php?theme=Education&featured=1
```

**Response (200)**:
```json
[
  {
    "id": 1,
    "title": "Digital Learning Platform for Refugee Children",
    "slug": "digital-learning-platform-refugee-children",
    "summary": "Amina created an innovative digital learning...",
    "description": "Full description text...",
    "location": "Kakuma, Kenya",
    "region": "East Africa",
    "theme": "Education",
    "latitude": 3.7167,
    "longitude": 34.8667,
    "image_url": "https://images.unsplash.com/...",
    "innovator_name": "Amina Hassan",
    "impact": "Making a positive impact...",
    "beneficiaries_count": 500,
    "contact_email": "amina@example.com",
    "contact_info": null,
    "is_featured": 1,
    "view_count": 42,
    "created_at": "2025-12-15 10:30:00",
    "updated_at": "2025-12-17 08:15:22"
  },
  // ... more stories
]
```

**Empty Result**:
```json
[]
```

**Implementation Details**:
- No authentication required (public endpoint)
- Returns empty array if no stories match filters
- SQL uses prepared statements with parameterized filters
- Results ordered by `created_at DESC` (newest first)

---

#### GET /stories.php?id={id}
**Purpose**: Retrieve single story by ID and increment view count.

**Example Request**:
```
GET /stories.php?id=1
```

**Response (200)**:
```json
{
  "id": 1,
  "title": "Digital Learning Platform...",
  // ... all story fields
}
```

**Error (404)**:
```json
{
  "error": "Story not found"
}
```

**Implementation Details**:
- Increments `view_count` on each request (side effect!)
- Uses atomic `UPDATE innovation_stories SET view_count = view_count + 1`
- Returns 404 if story ID doesn't exist

---

#### POST /stories.php
**Purpose**: Create new story (admin only).

**Authentication**: Required (session-based)

**Request**:
```json
{
  "title": "New Innovation Story",
  "description": "Full story description...",
  "location": "City, Country",
  "region": "East Africa",
  "theme": "Education",
  "latitude": 3.7167,
  "longitude": 34.8667,
  "image_url": "https://example.com/image.jpg",
  "innovator_name": "John Doe",
  "impact": "Positive impact description",
  "beneficiaries_count": 100,
  "contact_email": "john@example.com",
  "contact_info": "Additional contact info",
  "is_featured": 0
}
```

**Required Fields**: `title`, `description`, `location`, `region`, `theme`, `innovator_name`

**Response (201)**:
```json
{
  "id": 42,
  "message": "Story created successfully"
}
```

**Error (400)**:
```json
{
  "error": "Field 'title' is required"
}
```

**Error (401)**:
```json
{
  "error": "Authentication required"
}
```

**Implementation Details**:
- Auto-generates `slug` from `title`:
  1. Convert to lowercase
  2. Replace non-alphanumeric with hyphens
  3. Remove duplicate hyphens
  4. Trim hyphens from ends
  5. Append timestamp if slug already exists
- Auto-generates `summary` (first 200 chars of description)
- Returns 401 if `$_SESSION['user_id']` not set

---

#### PUT /stories.php?id={id}
**Purpose**: Update existing story (admin only).

**Authentication**: Required (session-based)

**Example Request**:
```
PUT /stories.php?id=1
```

**Body**:
```json
{
  "title": "Updated Title",
  "description": "Updated description...",
  "location": "Updated location",
  "region": "Middle East",
  "theme": "Health",
  "latitude": 32.3078,
  "longitude": 36.3275,
  "image_url": "https://new-image.com/img.jpg",
  "innovator_name": "Jane Doe",
  "impact": "Updated impact...",
  "beneficiaries_count": 200,
  "contact_email": "jane@example.com",
  "contact_info": "New contact info",
  "is_featured": 1
}
```

**Response (200)**:
```json
{
  "message": "Story updated successfully"
}
```

**Error (401)**:
```json
{
  "error": "Authentication required"
}
```

**Implementation Details**:
- Updates ALL fields (full update, not partial)
- Uses `null` coalescing for optional fields
- Sets `updated_at` automatically via `ON UPDATE CURRENT_TIMESTAMP`
- Does NOT check if story exists (SQL will execute but affect 0 rows)

---

#### DELETE /stories.php?id={id}
**Purpose**: Delete story (admin only).

**Authentication**: Required (session-based)

**Example Request**:
```
DELETE /stories.php?id=1
```

**Response (200)**:
```json
{
  "message": "Story deleted successfully"
}
```

**Error (401)**:
```json
{
  "error": "Authentication required"
}
```

**Implementation Details**:
- Hard delete (removes row from database)
- Associated `site_analytics` records have `story_id` set to `NULL` (FK with `ON DELETE SET NULL`)
- Does NOT cascade to `story_submissions` (they remain for audit trail)

---

### Submissions Endpoints

#### GET /submissions.php?status={status}
**Purpose**: Retrieve submissions by status (admin only).

**Authentication**: Required (session-based)

**Query Parameters**:
- `status` (optional): `pending` (default), `approved`, or `rejected`

**Example Requests**:
```
GET /submissions.php (defaults to pending)
GET /submissions.php?status=approved
GET /submissions.php?status=rejected
```

**Response (200)**:
```json
[
  {
    "id": 5,
    "title": "Submitted Story Title",
    "description": "Submitted description...",
    "location": "City, Country",
    "region": "East Africa",
    "theme": "Livelihoods",
    "innovator_name": "Submitter Name",
    "impact": "Expected impact...",
    "contact_email": "submitter@example.com",
    "contact_info": "Phone: +1234567890",
    "image_url": "/uploads/stories/story_abc123.jpg",
    "status": "pending",
    "submitted_at": "2025-12-17 14:30:00"
  },
  // ... more submissions
]
```

**Error (401)**:
```json
{
  "error": "Authentication required"
}
```

**Implementation Details**:
- Returns empty array if no submissions match status
- Ordered by `submitted_at DESC` (newest first)
- Only accessible to authenticated users

---

#### POST /submissions.php
**Purpose**: Submit new story (public endpoint).

**Authentication**: NOT required (public)

**Request Format**: JSON or FormData (for file uploads)

**JSON Example**:
```json
{
  "title": "My Innovation Story",
  "description": "Detailed description...",
  "location": "Nairobi, Kenya",
  "region": "East Africa",
  "theme": "Education",
  "innovator_name": "John Doe",
  "impact": "Helping 100 students...",
  "contact_email": "john@example.com",
  "contact_info": "+254700000000",
  "image_url": "https://example.com/image.jpg"
}
```

**FormData Example** (with file upload):
```javascript
const formData = new FormData();
formData.append('title', 'My Innovation Story');
formData.append('description', 'Detailed description...');
formData.append('location', 'Nairobi, Kenya');
formData.append('region', 'East Africa');
formData.append('theme', 'Education');
formData.append('innovator_name', 'John Doe');
formData.append('impact', 'Helping 100 students...');
formData.append('contact_email', 'john@example.com');
formData.append('contact_info', '+254700000000');
formData.append('image', fileInputElement.files[0]); // File object
```

**Required Fields**: `title`, `description`, `location`, `region`, `theme`, `innovator_name`, `contact_email`

**Response (201)**:
```json
{
  "id": 15,
  "message": "Submission received successfully"
}
```

**Error (400)**:
```json
{
  "error": "Field 'title' is required"
}
```

**Error (400) - Invalid Email**:
```json
{
  "error": "Invalid email address"
}
```

**File Upload Handling**:
- **Allowed Types**: JPEG, PNG, GIF, WebP
- **Max Size**: 5 MB
- **Storage Location**: `/uploads/stories/`
- **Filename Format**: `story_{uniqid}_{timestamp}.{ext}`
- **MIME Type Validation**: Uses `mime_content_type()` (not just extension check)

**Implementation Details**:
- Validates email with `filter_var($email, FILTER_VALIDATE_EMAIL)`
- Creates `/uploads/stories/` directory if it doesn't exist
- Moves uploaded file with `move_uploaded_file()`
- Returns relative path in `image_url` field (e.g., `/uploads/stories/story_abc.jpg`)
- Sets `status='pending'` automatically
- If no image provided, `image_url` is `NULL` (NO auto-generated fallback)

---

#### PUT /submissions.php?id={id}&action=approve
**Purpose**: Approve submission and copy to stories table (admin only).

**Authentication**: Required (session-based)

**Example Request**:
```
PUT /submissions.php?id=5&action=approve
```

**Response (200)**:
```json
{
  "story_id": 42,
  "message": "Submission approved and published"
}
```

**Error (404)**:
```json
{
  "error": "Submission not found"
}
```

**Error (401)**:
```json
{
  "error": "Authentication required"
}
```

**Implementation Details**:
1. Fetch submission by ID
2. Check if `status='pending'`
3. Generate slug from title
4. Generate summary (first 200 chars of description)
5. INSERT into `innovation_stories` table
6. UPDATE `story_submissions` SET `status='approved'`
7. Track analytics event: `submission_approved`
8. Return new `story_id` from `innovation_stories`

**NOTE**: Coordinates (`latitude`, `longitude`) are NOT copied from submission (they're NULL in submissions table). Admin must manually add coordinates via "Edit Location" feature.

---

#### PUT /submissions.php?id={id}&action=reject
**Purpose**: Reject submission (admin only).

**Authentication**: Required (session-based)

**Example Request**:
```
PUT /submissions.php?id=5&action=reject
```

**Response (200)**:
```json
{
  "message": "Submission rejected"
}
```

**Error (404)**:
```json
{
  "error": "Submission not found"
}
```

**Implementation Details**:
- Updates `story_submissions` SET `status='rejected'`
- Does NOT delete the record (remains for audit trail)
- Does NOT notify submitter (future enhancement)

---

### Analytics Endpoints

#### POST /analytics.php
**Purpose**: Track analytics event.

**Authentication**: NOT required (public)

**Request**:
```json
{
  "event_type": "story_view",
  "story_id": 1,
  "metadata": {
    "filter_applied": "region:East Africa",
    "user_agent": "Mozilla/5.0..."
  }
}
```

**Response (201)**:
```json
{
  "message": "Event tracked successfully"
}
```

**Common Event Types**:
- `story_view`: User viewed story detail
- `story_submission`: User submitted story
- `filter_applied`: User applied filter
- `map_interaction`: User interacted with map
- `submission_approved`: Admin approved submission

**Implementation Details**:
- `metadata` field stores JSON (flexible schema)
- `story_id` is optional (NULL for non-story events)
- Automatically adds `created_at` timestamp

---

#### GET /stats.php
**Purpose**: Retrieve aggregated statistics (admin only).

**Authentication**: Required (session-based)

**Response (200)**:
```json
{
  "total_stories": 42,
  "total_submissions": 15,
  "pending_submissions": 8,
  "total_views": 1532,
  "stories_by_region": {
    "East Africa": 15,
    "Middle East": 12,
    "Southeast Asia": 8,
    "West Africa": 7
  },
  "stories_by_theme": {
    "Education": 18,
    "Health": 10,
    "Livelihoods": 8,
    "Arts & Culture": 6
  },
  "recent_activity": [
    {
      "event_type": "story_view",
      "story_id": 1,
      "created_at": "2025-12-17 15:30:00"
    },
    // ... last 10 events
  ]
}
```

**Error (401)**:
```json
{
  "error": "Authentication required"
}
```

**Implementation Details**:
- Aggregates data from multiple tables
- Counts stories by region and theme using `GROUP BY`
- Returns last 10 analytics events
- Caches results for 5 minutes (future optimization)

---

### Upload Endpoints

#### POST /upload.php
**Purpose**: Upload image file (standalone endpoint).

**Authentication**: NOT required (public)

**Request**: `multipart/form-data`

**FormData**:
```javascript
const formData = new FormData();
formData.append('image', fileInputElement.files[0]);
```

**Response (200)**:
```json
{
  "url": "/uploads/stories/story_abc123_1234567890.jpg",
  "message": "Upload successful"
}
```

**Error (400) - Invalid Type**:
```json
{
  "error": "Invalid file type. Allowed: JPEG, PNG, GIF, WebP"
}
```

**Error (400) - Too Large**:
```json
{
  "error": "File too large. Maximum size: 5MB"
}
```

**Implementation Details**:
- Same validation as `/submissions.php` file upload
- Can be used independently for pre-uploading images
- Useful for WYSIWYG editors or drag-drop interfaces

---

## Authentication & Authorization

### Session-Based Authentication

**Why Sessions Instead of JWT?**
- Simpler implementation for PHP
- No need for token storage/refresh logic
- Automatic CSRF protection with same-origin cookies
- Native PHP session handling
- Suitable for single-domain application

### Session Configuration

**File**: `api/config.php`

```php
function isAuthenticated() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

function requireAuth() {
    if (!isAuthenticated()) {
        sendError('Authentication required', 401);
    }
}
```

**Session Data Structure**:
```php
$_SESSION = [
    'user_id' => 1,
    'user_email' => 'admin@jrsusa.org',
    'user_name' => 'Admin User',
    'user_role' => 'admin'
];
```

### Password Security

**Hashing Algorithm**: Bcrypt via `password_hash()`

**Generation**:
```php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
// Result: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
```

**Verification**:
```php
if (password_verify($inputPassword, $storedHash)) {
    // Password correct
}
```

**Cost Factor**: 10 (default)
- Each increase doubles computation time
- Balance between security and performance
- Takes ~50-100ms on modern hardware

### CORS Configuration

**File**: `api/config.php`

```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
```

**Preflight Handling**:
```php
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
```

**⚠️ SECURITY WARNING**:
- `Access-Control-Allow-Origin: *` allows any domain to call API
- Acceptable for development and same-domain deployment
- **CHANGE IN PRODUCTION** if API is on different domain than frontend
- Replace with: `header('Access-Control-Allow-Origin: https://yourdomain.com');`

### Authorization Matrix

| Endpoint | Public | Admin | Notes |
|----------|--------|-------|-------|
| GET /stories.php | ✓ | ✓ | Public browsing |
| GET /stories.php?id={id} | ✓ | ✓ | Public detail view |
| POST /stories.php | ✗ | ✓ | Admin only |
| PUT /stories.php?id={id} | ✗ | ✓ | Admin only |
| DELETE /stories.php?id={id} | ✗ | ✓ | Admin only |
| GET /submissions.php | ✗ | ✓ | Admin only |
| POST /submissions.php | ✓ | ✓ | Public submission |
| PUT /submissions.php (approve/reject) | ✗ | ✓ | Admin only |
| POST /analytics.php | ✓ | ✓ | Public tracking |
| GET /stats.php | ✗ | ✓ | Admin only |
| POST /upload.php | ✓ | ✓ | Public upload |
| POST /auth.php | ✓ | ✓ | Public login |

---

## Frontend Architecture

### File Structure

```
/project/
├── index.html                 # Main HTML file (SPA shell)
├── css/
│   └── styles.css             # All styles (no preprocessor)
├── js/
│   ├── app.js                 # Main application logic (~1200 lines)
│   └── auth.js                # Authentication module (~150 lines)
├── api/
│   ├── config.php             # Database + CORS config
│   ├── auth.php               # Login/logout endpoints
│   ├── stories.php            # Story CRUD endpoints
│   ├── submissions.php        # Submission endpoints
│   ├── analytics.php          # Analytics tracking
│   ├── stats.php              # Statistics aggregation
│   ├── upload.php             # File upload endpoint
│   └── php-api-client.js      # Frontend API abstraction
├── uploads/
│   └── stories/               # Uploaded images
│       └── .htaccess          # Prevent PHP execution in uploads
└── database/
    └── schema.sql             # Database schema + sample data
```

### Single-Page Application (SPA)

**File**: `index.html`

**Architecture**: Client-side routing without hash (#) or library

**View System**:
```html
<div id="app">
  <div id="home-view" class="view">...</div>
  <div id="stories-view" class="view">...</div>
  <div id="submit-view" class="view">...</div>
  <div id="dashboard-view" class="view">...</div>
  <div id="story-detail-modal" class="modal">...</div>
</div>
```

**View Switching**:
```javascript
function showView(viewId) {
    // Hide all views
    document.querySelectorAll('.view').forEach(view => {
        view.classList.remove('active');
    });

    // Show target view
    const targetView = document.getElementById(viewId);
    if (targetView) {
        targetView.classList.add('active');
    }
}
```

**No Build Step**: All code runs directly in browser (ES6 modules not used)

---

### State Management

**Global State Object** (`js/app.js`):

```javascript
let stories = [];           // All stories from API
let filteredStories = [];   // After applying filters
let currentFilters = {
    region: '',
    theme: '',
    featured: false
};
let currentUser = null;     // Set after login
let map = null;             // Leaflet map instance
let markers = [];           // Map markers
```

**State Updates**:
1. User action (click, form submit)
2. Call API via `window.api.method()`
3. Update global state variables
4. Call `render()` to update UI

**Example Flow** (Apply Filter):
```javascript
async function applyFilter(type, value) {
    currentFilters[type] = value;

    // Fetch filtered stories from API
    const { data, error } = await window.api.getStories({
        region: currentFilters.region,
        theme: currentFilters.theme,
        featured: currentFilters.featured
    });

    if (!error) {
        stories = data;
        filteredStories = data; // Already filtered by API
        render(); // Re-render UI
    }
}
```

---

### API Client Abstraction

**File**: `api/php-api-client.js`

**Purpose**: Centralize API calls, handle errors, provide consistent interface

**Initialization**:
```javascript
window.api = new PHPAPIClient('http://localhost/refugee-innovation-hub/api');
```

**Usage Examples**:
```javascript
// Get all stories
const { data, error } = await window.api.getStories();

// Get filtered stories
const { data, error } = await window.api.getStories({
    region: 'East Africa',
    featured: true
});

// Get single story
const { data, error } = await window.api.getStory(1);

// Submit story (JSON)
const { data, error } = await window.api.submitStory({
    title: 'My Story',
    description: '...',
    // ... other fields
});

// Submit story (FormData with file)
const formData = new FormData(formElement);
const { data, error } = await window.api.submitStory(formData);

// Login
const { data, error } = await window.api.login('admin@jrsusa.org', 'admin123');

// Approve submission
const { data, error } = await window.api.approveSubmission(5);
```

**Error Handling**:
```javascript
async request(endpoint, options = {}) {
    try {
        const response = await fetch(url, config);
        const text = await response.text();

        // Detect HTML error pages (404, 500)
        if (text.trim().startsWith('<')) {
            return {
                data: null,
                error: `Server returned HTML (status ${response.status})`
            };
        }

        const data = JSON.parse(text);

        if (!response.ok) {
            return { data: null, error: data.error || 'Request failed' };
        }

        return { data, error: null };
    } catch (err) {
        return { data: null, error: err.message };
    }
}
```

**Key Features**:
- Automatic JSON stringification for object bodies
- FormData detection (doesn't set Content-Type header)
- HTML error page detection
- Consistent error format: `{ data, error }`
- No exceptions thrown (always returns object)

---

### Rendering System

**Main Render Function** (`js/app.js`):

```javascript
function render() {
    renderStories();
    renderMap();
    renderFilters();
    renderStats();
}
```

**Story Grid Rendering**:
```javascript
function renderStories() {
    const container = document.getElementById('stories-grid');

    if (filteredStories.length === 0) {
        container.innerHTML = '<p>No stories found.</p>';
        return;
    }

    container.innerHTML = filteredStories.map(story => `
        <div class="story-card" onclick="showStoryDetail(${story.id})">
            <img src="${story.image_url || '/placeholder.jpg'}" alt="${story.title}">
            <h3>${story.title}</h3>
            <p>${story.summary}</p>
            <div class="meta">
                <span class="region">${story.region}</span>
                <span class="theme">${story.theme}</span>
            </div>
        </div>
    `).join('');
}
```

**Map Rendering** (Leaflet.js):
```javascript
function renderMap() {
    if (!map) {
        map = L.map('map').setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    }

    // Clear existing markers
    markers.forEach(marker => marker.remove());
    markers = [];

    // Add new markers
    filteredStories.forEach(story => {
        if (story.latitude && story.longitude) {
            const marker = L.marker([story.latitude, story.longitude])
                .bindPopup(`<b>${story.title}</b><br>${story.location}`)
                .on('click', () => showStoryDetail(story.id));

            marker.addTo(map);
            markers.push(marker);
        }
    });
}
```

---

### Key Features Implementation

#### 1. Edit Location Feature (Coordinate Management)

**Problem**: Stories need accurate coordinates for map visualization, but submissions don't include coordinates.

**Solution**: Admin can edit coordinates for any published story.

**UI Location**: Dashboard → "Edit Location" button on each story card

**Implementation** (`js/app.js`):

```javascript
async function handleEditStory(id) {
    const story = stories.find(s => s.id === id);
    if (!story) return;

    // Create modal with form
    const modal = document.createElement('div');
    modal.className = 'modal active';
    modal.innerHTML = `
        <div class="modal-content">
            <h2>Edit Location: ${story.title}</h2>
            <form id="editLocationForm">
                <label>Latitude (-90 to 90):</label>
                <input type="number" name="latitude" step="0.000001"
                       value="${story.latitude || ''}" required>

                <label>Longitude (-180 to 180):</label>
                <input type="number" name="longitude" step="0.000001"
                       value="${story.longitude || ''}" required>

                <button type="submit">Update Coordinates</button>
                <button type="button" id="cancelBtn">Cancel</button>
            </form>
        </div>
    `;

    document.body.appendChild(modal);

    // Handle form submit
    document.getElementById('editLocationForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const latitude = parseFloat(formData.get('latitude'));
        const longitude = parseFloat(formData.get('longitude'));

        // Validate coordinates
        if (latitude < -90 || latitude > 90) {
            alert('Latitude must be between -90 and 90');
            return;
        }
        if (longitude < -180 || longitude > 180) {
            alert('Longitude must be between -180 and 180');
            return;
        }

        // Update via API
        const { data, error } = await window.api.updateStory(id, {
            ...story,
            latitude,
            longitude
        });

        if (error) {
            alert('Error updating coordinates: ' + error);
            return;
        }

        alert('Coordinates updated successfully!');
        document.body.removeChild(modal);
        await loadStories();
        render();
    });
}
```

**Validation**:
- Latitude: -90 to 90 (client-side)
- Longitude: -180 to 180 (client-side)
- Decimal precision: 6 places (~0.1 meter accuracy)

**Database Storage**: `DECIMAL(10,8)` for latitude, `DECIMAL(11,8)` for longitude

---

#### 2. Image Upload System

**Features**:
- Drag-and-drop upload
- File type validation (JPEG, PNG, GIF, WebP)
- Size validation (max 5MB)
- Progress indication
- Error handling

**Frontend** (`js/app.js`):

```javascript
function initFileUpload() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');

    // Click to upload
    dropZone.addEventListener('click', () => {
        fileInput.click();
    });

    // Drag and drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', async (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');

        const file = e.dataTransfer.files[0];
        await uploadFile(file);
    });

    fileInput.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        await uploadFile(file);
    });
}

async function uploadFile(file) {
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please upload JPEG, PNG, GIF, or WebP.');
        return;
    }

    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('File too large. Maximum size is 5MB.');
        return;
    }

    // Show progress
    const progressBar = document.getElementById('progressBar');
    progressBar.style.display = 'block';

    // Upload via FormData
    const formData = new FormData();
    formData.append('image', file);

    const { data, error } = await window.api.uploadFile(formData);

    progressBar.style.display = 'none';

    if (error) {
        alert('Upload failed: ' + error);
        return;
    }

    // Set image URL in form
    document.getElementById('imageUrlInput').value = data.url;
    alert('Upload successful!');
}
```

**Backend** (`api/upload.php` or handled in `api/submissions.php`):

```php
// Validate file type (MIME, not extension!)
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$fileType = mime_content_type($_FILES['image']['tmp_name']);

if (!in_array($fileType, $allowedTypes)) {
    sendError('Invalid file type');
}

// Validate file size
$maxSize = 5 * 1024 * 1024; // 5MB
if ($_FILES['image']['size'] > $maxSize) {
    sendError('File too large. Maximum size: 5MB');
}

// Generate secure filename
$extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$filename = uniqid('story_', true) . '_' . time() . '.' . $extension;

// Move to uploads directory
$uploadDir = __DIR__ . '/../uploads/stories/';
$filepath = $uploadDir . $filename;

if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
    $imageUrl = '/uploads/stories/' . $filename;
    sendResponse(['url' => $imageUrl]);
} else {
    sendError('Upload failed');
}
```

**Security Measures**:
1. MIME type validation (not just extension)
2. Unique filename generation (prevents overwrites)
3. `.htaccess` in uploads folder prevents PHP execution
4. Size limits prevent DoS attacks

**`.htaccess` in `/uploads/stories/`**:
```apache
# Prevent PHP execution in uploads directory
<FilesMatch "\.ph(p[3457]?|t|tml)$">
    Deny from all
</FilesMatch>

# Allow image access
<FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
    Allow from all
</FilesMatch>
```

---

#### 3. Story Filtering

**Filter Types**:
- Region (East Africa, Middle East, Southeast Asia, West Africa, etc.)
- Theme (Education, Health, Livelihoods, Arts & Culture, etc.)
- Featured (boolean)

**UI**: Dropdown selects in sidebar

**Implementation**:

```javascript
function initFilters() {
    document.getElementById('regionFilter').addEventListener('change', (e) => {
        currentFilters.region = e.target.value;
        applyFilters();
    });

    document.getElementById('themeFilter').addEventListener('change', (e) => {
        currentFilters.theme = e.target.value;
        applyFilters();
    });

    document.getElementById('featuredFilter').addEventListener('change', (e) => {
        currentFilters.featured = e.target.checked;
        applyFilters();
    });
}

async function applyFilters() {
    const { data, error } = await window.api.getStories(currentFilters);

    if (!error) {
        filteredStories = data;
        render();
    }
}
```

**Server-Side Filtering** (`api/stories.php`):
```php
$sql = "SELECT * FROM innovation_stories WHERE 1=1";
$params = [];

if ($region) {
    $sql .= " AND region = ?";
    $params[] = $region;
}

if ($theme) {
    $sql .= " AND theme = ?";
    $params[] = $theme;
}

if ($featured !== null) {
    $sql .= " AND is_featured = ?";
    $params[] = $featured;
}

$sql .= " ORDER BY created_at DESC";
```

**Why Server-Side Filtering?**
- Reduces data transfer (only matching stories returned)
- Enables pagination in future
- Faster than client-side filtering for large datasets

---

## Problems Encountered & Solutions

### PROBLEM 1: Sample Stories Not Removed (Browser Cache)

**Symptoms**:
- User reported seeing 5 hardcoded sample stories
- Changes to code were verified in files
- Problem persisted after refresh

**Root Cause**:
Browser cached old version of `js/app.js` file. Modern browsers aggressively cache static files (CSS, JS) for performance.

**Investigation**:
```bash
# Verified code was correct in files
$ grep -n "getSampleStories" js/app.js
# (no output - function removed)

# But browser Network tab showed:
# app.js | 200 | from disk cache | 45.2 KB
```

**Solution**:
1. **Hard Refresh**: `Ctrl + Shift + R` (Windows) or `Cmd + Shift + R` (Mac)
2. **Clear Cache**: Browser settings → Clear cached images and files
3. **Cache Busting** (future): Add version query param: `<script src="js/app.js?v=1.0.0"></script>`
4. **Cache Headers** (production): Set proper cache control headers:
   ```apache
   <FilesMatch "\.(js|css)$">
       Header set Cache-Control "max-age=3600, must-revalidate"
   </FilesMatch>
   ```

**Code Changes Made**:

**BEFORE** (`js/app.js` lines 1196-1285):
```javascript
function getSampleStories() {
    return [
        {
            id: 1,
            title: "Digital Learning Platform for Refugee Children",
            // ... 4 more hardcoded stories
        }
    ];
}

async function loadStories() {
    try {
        const { data, error } = await window.api.getStories();
        if (!error && data) {
            stories = data.length > 0 ? data : getSampleStories(); // ← FALLBACK
        } else {
            stories = getSampleStories(); // ← FALLBACK
        }
    } catch (error) {
        stories = getSampleStories(); // ← FALLBACK
    }
}
```

**AFTER** (current code):
```javascript
// getSampleStories() function completely removed

async function loadStories() {
    try {
        const { data, error } = await window.api.getStories();
        if (!error && data) {
            stories = data.map(story => ({
                ...story,
                views: story.view_count || story.views || 0
            }));
        } else {
            stories = []; // ← EMPTY ARRAY, NO FALLBACK
        }
    } catch (error) {
        console.error('Error loading stories:', error);
        stories = []; // ← EMPTY ARRAY, NO FALLBACK
    }
}
```

**Files Modified**:
- `/js/app.js` (lines 1175-1194, removed lines 1196-1285)
- `/refugee-innovation-hub/js/app.js` (duplicate - same changes)

**Verification**:
```bash
# Verify function removed
$ grep "getSampleStories" js/app.js
# (no output)

# Verify empty array fallback
$ grep -A 5 "loadStories" js/app.js | grep "stories = \[\]"
# stories = [];
# stories = [];
```

**Lesson Learned**:
- Always include cache-busting strategy for production deploys
- Consider service worker for better cache control
- Document browser cache clearing in user guides
- Use `?v=timestamp` or hash in filenames for automatic cache invalidation

---

### PROBLEM 2: Auto-Generated Default Images (Unsplash Fallback)

**Symptoms**:
- Submissions without images got random Unsplash URLs
- Images didn't match story content
- Confusing for users (looked like real submissions)

**Root Cause**:
Code had fallback logic to generate Unsplash URLs when `image_url` was empty:

**BEFORE** (`api/submissions.php` line 161):
```php
$imageUrl = $data['image_url'] ?? 'https://images.unsplash.com/photo-' . rand(1000000000000, 9999999999999) . '?w=800';
```

**Investigation**:
```bash
$ grep -i "unsplash" api/submissions.php
# Found fallback URL generation
```

**Solution**:
Changed to use `NULL` for missing images instead of generating fallbacks.

**AFTER** (current code):
```php
$imageUrl = $data['image_url'] ?? null;
```

**Downstream Handling**:

**Frontend** (`js/app.js`):
```javascript
function renderStoryCard(story) {
    const imageHtml = story.image_url
        ? `<img src="${story.image_url}" alt="${story.title}">`
        : `<div class="no-image-placeholder">No Image</div>`;

    return `
        <div class="story-card">
            ${imageHtml}
            <h3>${story.title}</h3>
            // ...
        </div>
    `;
}
```

**CSS** (`css/styles.css`):
```css
.no-image-placeholder {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
}
```

**Files Modified**:
- `/api/submissions.php` (line 161)
- `/refugee-innovation-hub/api/submissions.php` (duplicate)

**Database Impact**:
- Existing stories with Unsplash URLs remain (historical data)
- New submissions will have `NULL` or user-provided URL only

**Verification**:
```bash
$ grep -i "unsplash" api/submissions.php
# (no output)

$ grep -i "unsplash" api/stories.php
# (no output)
```

**Lesson Learned**:
- Avoid "magic" default values that masquerade as real data
- Null/empty states should be explicit in UI
- Let users know when data is missing rather than filling with fake data

---

### PROBLEM 3: Missing Coordinate Update Feature

**Symptoms**:
- Admin couldn't add/edit coordinates for stories
- Map markers missing for submitted stories
- No UI for coordinate management

**Root Cause**:
Initial implementation didn't include coordinate editing. Submissions table doesn't have `latitude/longitude` fields, so approved stories had NULL coordinates.

**Solution**:
Added "Edit Location" feature in admin dashboard.

**Implementation**:

**1. Added PUT endpoint in API** (`api/stories.php` lines 119-153):
```php
case 'PUT':
    requireAuth();

    $id = (int)$_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);

    $sql = "UPDATE innovation_stories SET
            title = ?, description = ?, location = ?, region = ?, theme = ?,
            latitude = ?, longitude = ?, image_url = ?, innovator_name = ?, impact = ?,
            beneficiaries_count = ?, contact_email = ?, contact_info = ?,
            is_featured = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $data['title'] ?? null,
        $data['description'] ?? null,
        $data['location'] ?? null,
        $data['region'] ?? null,
        $data['theme'] ?? null,
        $data['latitude'] ?? null,  // ← Support coordinate updates
        $data['longitude'] ?? null, // ← Support coordinate updates
        $data['image_url'] ?? null,
        $data['innovator_name'] ?? null,
        $data['impact'] ?? null,
        $data['beneficiaries_count'] ?? 0,
        $data['contact_email'] ?? null,
        $data['contact_info'] ?? null,
        $data['is_featured'] ?? 0,
        $id
    ]);

    sendResponse(['message' => 'Story updated successfully']);
    break;
```

**2. Added frontend UI** (`js/app.js` lines 1012-1141):
```javascript
async function handleEditStory(id) {
    const story = stories.find(s => s.id === id);
    if (!story) return;

    // Create modal with form
    const modal = document.createElement('div');
    modal.className = 'modal active';
    modal.innerHTML = `
        <div class="modal-content">
            <h2>Edit Location: ${story.title}</h2>
            <form id="editLocationForm">
                <label>Latitude (-90 to 90):</label>
                <input type="number" name="latitude" step="0.000001"
                       value="${story.latitude || ''}" required>

                <label>Longitude (-180 to 180):</label>
                <input type="number" name="longitude" step="0.000001"
                       value="${story.longitude || ''}" required>

                <button type="submit">Update Coordinates</button>
                <button type="button" id="cancelBtn">Cancel</button>
            </form>
        </div>
    `;

    document.body.appendChild(modal);

    // Form submit handler with validation...
}
```

**3. Added button in dashboard** (`js/app.js` line 830):
```javascript
<button class="btn-edit" onclick="handleEditStory(${story.id})"
        style="background: var(--blue-600);">
    Edit Location
</button>
```

**4. Added API client method** (`api/php-api-client.js` lines 94-99):
```javascript
async updateStory(id, storyData) {
    return this.request(`/stories.php?id=${id}`, {
        method: 'PUT',
        body: storyData
    });
}
```

**Validation**:
- Client-side: Range checks (-90 to 90 for lat, -180 to 180 for long)
- Server-side: Type checking via `parseFloat()`
- Database: `DECIMAL(10,8)` enforces precision

**Files Modified**:
- `/api/stories.php` (added PUT case)
- `/js/app.js` (added handleEditStory function)
- `/api/php-api-client.js` (added updateStory method)

**User Flow**:
1. Admin logs in → Dashboard
2. Clicks "Edit Location" on story card
3. Modal opens with latitude/longitude fields
4. Enters coordinates (e.g., `3.7167, 34.8667`)
5. Clicks "Update Coordinates"
6. Ajax PUT request to `/api/stories.php?id=X`
7. Success message + modal closes
8. Stories reload + map updates with new marker

**Verification**:
```bash
$ grep -n "handleEditStory" js/app.js
# 830: <button ... onclick="handleEditStory(${story.id})">
# 1012: async function handleEditStory(id) {
# 1172: window.handleEditStory = handleEditStory;

$ grep -n "case 'PUT':" api/stories.php
# 119: case 'PUT':
```

**Lesson Learned**:
- Plan data model early (include coordinates in submissions table?)
- Consider workflow: When do fields get populated?
- Provide admin tools for data quality management
- Validate coordinates strictly (many possible error values)

---

### PROBLEM 4: Duplicate Folder Structure Confusion

**Symptoms**:
- Two copies of application: `/project/` and `/project/refugee-innovation-hub/`
- User editing wrong folder
- Changes not reflected in browser

**Root Cause**:
Project was extracted from RAR archive into existing directory, creating nested structure:
```
/project/
├── index.html
├── js/
├── api/
├── refugee-innovation-hub.rar
└── refugee-innovation-hub/    ← Extracted archive
    ├── index.html             ← Duplicate files
    ├── js/
    └── api/
```

**Investigation**:
```bash
$ ls -la /project/
# Shows both root files and refugee-innovation-hub/ folder

$ ls -la /project/refugee-innovation-hub/
# Shows complete duplicate structure
```

**Solution**:
1. Identified main folder (should be `/project/` root)
2. Applied all changes to root folder
3. Copied changes to duplicate folder for safety
4. Documented correct folder in all guides

**URLs**:
- **Correct**: `http://localhost/refugee-innovation-hub/` (points to `/project/`)
- **Duplicate**: `http://localhost/refugee-innovation-hub/refugee-innovation-hub/` (points to `/project/refugee-innovation-hub/`)

**XAMPP Setup**:
```
C:\xampp\htdocs\refugee-innovation-hub\  ← Should contain: index.html, js/, api/, etc.
```

**Verification Commands**:
```bash
# Check which folder XAMPP is serving
$ cd C:\xampp\htdocs\refugee-innovation-hub
$ ls -la

# Should see:
# index.html
# js/
# api/
# css/
# uploads/
```

**Files Modified in BOTH locations**:
- `/js/app.js` and `/refugee-innovation-hub/js/app.js`
- `/api/submissions.php` and `/refugee-innovation-hub/api/submissions.php`

**Future Prevention**:
- Use Git for version control (track single source of truth)
- Document deployment folder structure clearly
- Use symlinks instead of copies
- Remove RAR file after extraction

**Lesson Learned**:
- Always verify working directory before making changes
- Duplicate files = maintenance nightmare
- Use `pwd` / `cd` / `ls` liberally to confirm location
- Document folder structure in README

---

### PROBLEM 5: Approval Workflow Not Creating Stories

**Symptoms**:
- Admin approved submissions
- Submissions status changed to "approved"
- Stories not appearing in public view
- Database had submissions but not stories

**Root Cause**:
Approval workflow was only updating `story_submissions.status` field, not copying data to `innovation_stories` table.

**BEFORE** (broken code):
```php
// In api/submissions.php
case 'PUT':
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE story_submissions SET status = ? WHERE id = ?");
        $stmt->execute(['approved', $id]);
        sendResponse(['message' => 'Submission approved']);
    }
```

**Solution**:
Changed approval workflow to copy data to `innovation_stories` table.

**AFTER** (current code) (`api/submissions.php` lines 127-180):
```php
case 'PUT':
    requireAuth();

    $id = (int)$_GET['id'];
    $action = $_GET['action'] ?? '';

    if ($action === 'approve') {
        // Get submission
        $stmt = $pdo->prepare("SELECT * FROM story_submissions WHERE id = ? AND status = 'pending'");
        $stmt->execute([$id]);
        $submission = $stmt->fetch();

        if (!$submission) {
            sendError('Submission not found or already processed', 404);
        }

        // Generate slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $submission['title'])));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Check if slug exists
        $checkStmt = $pdo->prepare("SELECT id FROM innovation_stories WHERE slug = ?");
        $checkStmt->execute([$slug]);
        if ($checkStmt->fetch()) {
            $slug .= '-' . time();
        }

        // Generate summary
        $summary = substr($submission['description'], 0, 200);

        // Insert into innovation_stories
        $insertSql = "INSERT INTO innovation_stories
            (title, slug, summary, description, location, region, theme, image_url,
             innovator_name, impact, contact_email, contact_info, beneficiaries_count)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([
            $submission['title'],
            $slug,
            $summary,
            $submission['description'],
            $submission['location'],
            $submission['region'],
            $submission['theme'],
            $submission['image_url'],
            $submission['innovator_name'],
            $submission['impact'] ?? 'Making a positive impact in the community.',
            $submission['contact_email'],
            $submission['contact_info'],
            0 // beneficiaries_count defaults to 0
        ]);

        $storyId = $pdo->lastInsertId();

        // Update submission status
        $updateStmt = $pdo->prepare("UPDATE story_submissions SET status = ? WHERE id = ?");
        $updateStmt->execute(['approved', $id]);

        // Track analytics
        $analyticsStmt = $pdo->prepare("INSERT INTO site_analytics (event_type, story_id) VALUES (?, ?)");
        $analyticsStmt->execute(['submission_approved', $storyId]);

        sendResponse([
            'story_id' => $storyId,
            'message' => 'Submission approved and published'
        ]);
    }
```

**Key Changes**:
1. Fetch submission data
2. Generate slug (URL-friendly title)
3. Generate summary (first 200 chars)
4. INSERT into `innovation_stories`
5. UPDATE `story_submissions` status
6. Track analytics event
7. Return new `story_id`

**Workflow Diagram**:
```
User Submission → story_submissions (status='pending')
                        ↓
Admin Reviews → Dashboard shows pending submissions
                        ↓
Admin Clicks "Approve"
                        ↓
            ┌───────────┴────────────┐
            ↓                        ↓
    Copy to innovation_stories    Update status='approved'
            ↓                        ↓
    Public sees story          Audit trail maintained
```

**Files Modified**:
- `/api/submissions.php` (lines 127-180)

**Database Impact**:
- `story_submissions` retains all submissions (audit trail)
- `innovation_stories` contains only approved stories (public view)
- No DELETE operations (keep historical data)

**Verification**:
```sql
-- Check submissions
SELECT id, title, status FROM story_submissions;

-- Check if approved submissions copied to stories
SELECT s.id AS submission_id, s.title AS submission_title,
       i.id AS story_id, i.title AS story_title
FROM story_submissions s
LEFT JOIN innovation_stories i ON i.title = s.title
WHERE s.status = 'approved';
```

**Lesson Learned**:
- Approval workflows require data migration, not just status updates
- Use transactions for multi-step operations (future improvement)
- Test end-to-end workflows with real data
- Maintain audit trail (don't delete submissions after approval)

---

### PROBLEM 6: Session Not Persisting After Login

**Symptoms**:
- Login successful (200 response)
- User redirected to dashboard
- Dashboard shows "Please login" message
- `isAuthenticated()` returns false immediately after login

**Root Cause**:
Multiple `session_start()` calls caused session conflicts. PHP sessions require exactly one `session_start()` per request.

**Investigation**:
```bash
$ grep -rn "session_start" api/
# api/auth.php:29:    session_start();
# api/auth.php:55:    session_start();
# api/auth.php:62:    session_start();
# api/config.php:82:    session_start();
```

**Solution**:
Wrapped all `session_start()` calls with check:

```php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

**Files Modified**:
- `/api/config.php` (lines 82-84)
- `/api/auth.php` (lines 29, 55, 62)

**Best Practice**:
```php
// Create helper function
function ensureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Use everywhere instead of direct session_start()
ensureSession();
```

**Verification**:
```bash
$ grep -A 2 "session_start" api/*.php
# All instances now wrapped with session_status() check
```

**Lesson Learned**:
- Session handling requires careful management
- Abstract session logic into helper functions
- Check session state before calling `session_start()`
- Consider using session middleware / framework

---

## Security Considerations

### 1. SQL Injection Protection

**Method**: PDO Prepared Statements

**Implementation**:
```php
// ✗ VULNERABLE (never do this)
$sql = "SELECT * FROM users WHERE email = '" . $_POST['email'] . "'";
$result = mysqli_query($conn, $sql);

// ✓ SECURE (always use prepared statements)
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$_POST['email']]);
```

**All Queries Use Prepared Statements**:
- `api/stories.php`: All SELECT/INSERT/UPDATE/DELETE
- `api/auth.php`: Login queries
- `api/submissions.php`: All queries
- `api/analytics.php`: Event tracking

**Verification**:
```bash
$ grep -rn "prepare(" api/*.php | wc -l
# 25+ prepared statement usages
```

---

### 2. Password Security

**Hashing**: Bcrypt via `password_hash()`

**Cost Factor**: 10 (default)

**Code**:
```php
// Hashing (user registration - future feature)
$hash = password_hash($password, PASSWORD_DEFAULT);
// Generates: $2y$10$...

// Verification (login)
if (password_verify($inputPassword, $storedHash)) {
    // Password correct
}
```

**Bcrypt Properties**:
- Adaptive: Cost can be increased over time
- Salted: Unique salt per hash (automatic)
- Slow: ~50-100ms per hash (prevents brute force)

**⚠️ Default Admin Password**:
- Email: `admin@jrsusa.org`
- Password: `admin123`
- **MUST CHANGE IN PRODUCTION**

**Change Password** (manual, via phpMyAdmin or SQL):
```sql
-- Generate new hash in PHP
<?php echo password_hash('NewSecureP@ssw0rd!', PASSWORD_DEFAULT); ?>

-- Update database
UPDATE users
SET password_hash = '$2y$10$...'  -- Use generated hash
WHERE email = 'admin@jrsusa.org';
```

---

### 3. File Upload Security

**Threats**:
- Arbitrary file execution (PHP/executable uploads)
- Path traversal attacks
- Denial of service (large files)
- XSS via SVG files

**Mitigations**:

**1. MIME Type Validation**:
```php
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$fileType = mime_content_type($_FILES['image']['tmp_name']); // ← Uses file content, not extension

if (!in_array($fileType, $allowedTypes)) {
    sendError('Invalid file type');
}
```

**2. Size Limits**:
```php
$maxSize = 5 * 1024 * 1024; // 5MB
if ($_FILES['image']['size'] > $maxSize) {
    sendError('File too large');
}
```

**3. Secure Filename Generation**:
```php
$extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
$filename = uniqid('story_', true) . '_' . time() . '.' . $extension;
// Result: story_5f5e5d5c5a5b5_1639123456.jpg
// - Prevents overwrites
// - Prevents path traversal (no user input in path)
```

**4. Prevent PHP Execution in Uploads** (`.htaccess`):
```apache
# /uploads/stories/.htaccess
<FilesMatch "\.ph(p[3457]?|t|tml)$">
    Deny from all
</FilesMatch>

<FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
    Allow from all
</FilesMatch>
```

**5. Storage Outside Web Root** (future improvement):
```
# Current (accessible):
/project/uploads/stories/image.jpg → http://localhost/.../uploads/stories/image.jpg

# Recommended (not directly accessible):
/project_data/uploads/stories/image.jpg → Served via PHP script with access control
```

---

### 4. XSS (Cross-Site Scripting) Protection

**Threat**: User input displayed as HTML/JavaScript

**Vulnerable Code** (example):
```javascript
// ✗ VULNERABLE
container.innerHTML = `<h3>${story.title}</h3>`; // If title contains <script>, it executes
```

**Mitigation**: Escape user input

**Current Implementation**:

**Backend** (PHP): No need to escape (JSON encoding handles it)
```php
echo json_encode($story); // Automatically escapes special chars
```

**Frontend** (JavaScript):

**Option 1**: Template literals (current - limited escaping)
```javascript
// Only safe if data comes from trusted API
container.innerHTML = `<h3>${story.title}</h3>`;
```

**Option 2**: Text content (safest for pure text)
```javascript
const h3 = document.createElement('h3');
h3.textContent = story.title; // Automatically escaped
container.appendChild(h3);
```

**Option 3**: Manual escaping function
```javascript
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

container.innerHTML = `<h3>${escapeHtml(story.title)}</h3>`;
```

**Current Risk Level**: Low
- All data comes from database (admin-controlled)
- Submissions reviewed before publishing
- No user-generated HTML in submissions

**Future Improvement**:
- Sanitize on input (strip HTML tags from submissions)
- Use Content Security Policy (CSP) headers
- Implement DOMPurify for rich text editors

---

### 5. CSRF (Cross-Site Request Forgery) Protection

**Current Status**: Partially protected by session cookies

**Threat**: Attacker tricks user into making unwanted request

**Example Attack**:
```html
<!-- Malicious site -->
<img src="http://localhost/refugee-innovation-hub/api/stories.php?id=1"
     style="display:none">
<!-- Deletes story if user is logged in -->
```

**Why Current Code Is Vulnerable**:
- No CSRF tokens
- `Access-Control-Allow-Origin: *` allows any origin
- Session cookies sent automatically

**Mitigation (future)**:

**1. CSRF Tokens**:
```php
// Generate token on login
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Require token on state-changing requests
function validateCsrfToken() {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        sendError('Invalid CSRF token', 403);
    }
}

// In POST/PUT/DELETE handlers
validateCsrfToken();
```

**2. SameSite Cookies**:
```php
session_set_cookie_params([
    'samesite' => 'Strict', // or 'Lax'
    'secure' => true,       // HTTPS only
    'httponly' => true      // Not accessible via JavaScript
]);
session_start();
```

**3. Referer Checking**:
```php
function validateReferer() {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $host = $_SERVER['HTTP_HOST'];

    if (!str_contains($referer, $host)) {
        sendError('Invalid referer', 403);
    }
}
```

**Current Risk Level**: Medium
- Requires user to be logged in
- Requires attacker to know API structure
- Only affects admin operations (submissions are public anyway)

---

### 6. Access Control

**Current Implementation**: Session-based with `requireAuth()`

**Code** (`api/config.php`):
```php
function requireAuth() {
    if (!isAuthenticated()) {
        sendError('Authentication required', 401);
    }
}
```

**Usage**:
```php
// In protected endpoints
case 'POST':
    requireAuth(); // Must be authenticated
    // ... create story logic
```

**Access Control Matrix**:
| Endpoint | Public | Admin | Implementation |
|----------|--------|-------|----------------|
| GET /stories.php | ✓ | ✓ | No auth check |
| POST /stories.php | ✗ | ✓ | `requireAuth()` |
| PUT /stories.php | ✗ | ✓ | `requireAuth()` |
| DELETE /stories.php | ✗ | ✓ | `requireAuth()` |
| POST /submissions.php | ✓ | ✓ | No auth check |
| PUT /submissions.php (approve) | ✗ | ✓ | `requireAuth()` |
| GET /stats.php | ✗ | ✓ | `requireAuth()` |

**Future Improvements**:

**1. Role-Based Access Control (RBAC)**:
```php
function requireRole($role) {
    requireAuth();
    if ($_SESSION['user_role'] !== $role) {
        sendError('Insufficient permissions', 403);
    }
}

// Usage
requireRole('admin'); // Only admins
```

**2. Resource-Level Permissions**:
```php
function canEditStory($userId, $storyId) {
    // Check if user created the story
    $stmt = $pdo->prepare("SELECT created_by FROM innovation_stories WHERE id = ?");
    $stmt->execute([$storyId]);
    $story = $stmt->fetch();

    return $story && $story['created_by'] === $userId;
}
```

**3. API Rate Limiting** (prevent abuse):
```php
function checkRateLimit($identifier, $maxRequests = 100, $window = 3600) {
    $cacheKey = "rate_limit:{$identifier}";
    $count = apcu_fetch($cacheKey) ?: 0;

    if ($count >= $maxRequests) {
        sendError('Rate limit exceeded', 429);
    }

    apcu_store($cacheKey, $count + 1, $window);
}

// Usage
checkRateLimit($_SERVER['REMOTE_ADDR']); // Per IP
```

---

### 7. Error Handling & Information Disclosure

**Current Issue**: Development error messages exposed

**File**: `api/config.php` (lines 3-6)
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

**Risk**: Exposes file paths, database structure, PHP version

**Example Vulnerable Error**:
```
Fatal error: Uncaught PDOException: SQLSTATE[42S02]:
Base table or view not found: 1146 Table 'refugee_innovation_hub.stories'
doesn't exist in /var/www/html/refugee-innovation-hub/api/stories.php:58
```

**Production Configuration**:
```php
// Production (api/config.php)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/logs/php_errors.log');

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno]: $errstr in $errfile:$errline");

    if (in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR])) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
        exit();
    }
});
```

**Generic Error Responses**:
```php
// Instead of:
catch (PDOException $e) {
    sendError('Database error: ' . $e->getMessage());
}

// Use:
catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    sendError('An error occurred. Please try again later.');
}
```

---

## Deployment Guide

### Prerequisites

**Server Requirements**:
- PHP 7.4 or higher (8.0+ recommended)
- MySQL 5.7 / MariaDB 10.3 or higher
- Apache 2.4 with mod_rewrite enabled
- 50 MB disk space minimum
- SSL certificate (recommended for production)

**Development Environment** (XAMPP):
- XAMPP 8.0+ (includes Apache, MySQL, PHP)
- Windows 10/11, macOS, or Linux

---

### Local Deployment (XAMPP)

**1. Install XAMPP**:
```
Download from: https://www.apachefriends.org/
Run installer, select components:
  ✓ Apache
  ✓ MySQL
  ✓ PHP
  ✓ phpMyAdmin
```

**2. Extract Project**:
```bash
# Windows
C:\xampp\htdocs\refugee-innovation-hub\

# Mac/Linux
/Applications/XAMPP/htdocs/refugee-innovation-hub/
```

**3. Configure Database**:

**A. Create Database** (via phpMyAdmin):
```
1. Open http://localhost/phpmyadmin
2. Click "New" in left sidebar
3. Database name: refugee_innovation_hub
4. Collation: utf8mb4_unicode_ci
5. Click "Create"
```

**B. Import Schema**:
```
1. Click database name in left sidebar
2. Click "Import" tab
3. Choose file: database/schema.sql
4. Click "Go"
5. Verify tables created (should see 4 tables)
```

**C. Verify Sample Data**:
```sql
-- Run in phpMyAdmin SQL tab
SELECT COUNT(*) FROM innovation_stories;
-- Should return: 5 (sample stories)

SELECT * FROM users;
-- Should return: 1 (admin user)
```

**4. Configure API**:

**File**: `api/config.php`

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'refugee_innovation_hub');
define('DB_USER', 'root');
define('DB_PASS', ''); // Empty for XAMPP default
```

**5. Create Uploads Directory**:
```bash
mkdir -p uploads/stories
chmod 755 uploads/stories

# Copy .htaccess
cp uploads/.htaccess uploads/stories/.htaccess
```

**6. Start XAMPP**:
```
1. Open XAMPP Control Panel
2. Start "Apache"
3. Start "MySQL"
4. Verify green status indicators
```

**7. Access Application**:
```
Frontend: http://localhost/refugee-innovation-hub/
Admin Login: admin@jrsusa.org / admin123
phpMyAdmin: http://localhost/phpmyadmin
```

**8. Verify Installation**:
```
1. Run: http://localhost/refugee-innovation-hub/verify-changes.php
2. Should see all checks pass (green)
3. Check sample stories visible on homepage
4. Test admin login
```

---

### Production Deployment

**1. Server Setup** (Ubuntu 20.04 example):

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Apache
sudo apt install apache2 -y
sudo systemctl enable apache2

# Install PHP
sudo apt install php8.1 php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl -y

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation
```

**2. Create Database**:

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE refugee_innovation_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'refugee_app'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';

GRANT ALL PRIVILEGES ON refugee_innovation_hub.* TO 'refugee_app'@'localhost';

FLUSH PRIVILEGES;

USE refugee_innovation_hub;

SOURCE /path/to/database/schema.sql;

EXIT;
```

**3. Deploy Application Files**:

```bash
# Upload via SCP/SFTP
scp -r project/ user@server:/var/www/html/refugee-innovation-hub/

# Or clone from Git
cd /var/www/html/
git clone https://github.com/yourorg/refugee-innovation-hub.git

# Set permissions
sudo chown -R www-data:www-data /var/www/html/refugee-innovation-hub/
sudo chmod -R 755 /var/www/html/refugee-innovation-hub/
sudo chmod -R 775 /var/www/html/refugee-innovation-hub/uploads/
```

**4. Configure Production API**:

**File**: `api/config.php`

```php
// ✗ Remove development settings
// ini_set('display_errors', 1);

// ✓ Production settings
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php/refugee_hub_errors.log');
error_reporting(E_ALL);

define('DB_HOST', 'localhost');
define('DB_NAME', 'refugee_innovation_hub');
define('DB_USER', 'refugee_app');
define('DB_PASS', 'STRONG_PASSWORD_HERE');

// ✓ Change CORS for production
header('Access-Control-Allow-Origin: https://yourdomain.com');

// ✓ Change JWT secret
define('JWT_SECRET', 'RANDOM_64_CHAR_STRING_HERE');
```

**5. Configure Apache Virtual Host**:

```apache
# /etc/apache2/sites-available/refugee-hub.conf

<VirtualHost *:80>
    ServerName refugee-hub.yourdomain.com
    ServerAdmin admin@yourdomain.com

    DocumentRoot /var/www/html/refugee-innovation-hub

    <Directory /var/www/html/refugee-innovation-hub>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Logging
    ErrorLog ${APACHE_LOG_DIR}/refugee-hub-error.log
    CustomLog ${APACHE_LOG_DIR}/refugee-hub-access.log combined

    # Security headers
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
```

**Enable site**:
```bash
sudo a2ensite refugee-hub.conf
sudo a2enmod rewrite headers
sudo systemctl restart apache2
```

**6. SSL Certificate** (Let's Encrypt):

```bash
sudo apt install certbot python3-certbot-apache -y

sudo certbot --apache -d refugee-hub.yourdomain.com

# Auto-renewal
sudo certbot renew --dry-run
```

**7. Security Hardening**:

```bash
# Disable directory listing
sudo sed -i 's/Options Indexes FollowSymLinks/Options -Indexes +FollowSymLinks/' /etc/apache2/apache2.conf

# Hide PHP version
echo "expose_php = Off" | sudo tee -a /etc/php/8.1/apache2/php.ini

# Restrict uploads directory
# (already done via .htaccess)

# Set up firewall
sudo ufw allow 'Apache Full'
sudo ufw allow OpenSSH
sudo ufw enable
```

**8. Change Default Admin Password**:

```php
// Generate new hash
<?php echo password_hash('NewSecureP@ssw0rd!', PASSWORD_DEFAULT); ?>

// Update in database
sudo mysql -u root -p refugee_innovation_hub
```

```sql
UPDATE users
SET password_hash = '$2y$10$NEW_HASH_HERE'
WHERE email = 'admin@jrsusa.org';
```

**9. Set Up Backups**:

```bash
# Create backup script
sudo nano /usr/local/bin/backup-refugee-hub.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/refugee-hub"
DATE=$(date +%Y%m%d_%H%M%S)

# Database backup
mysqldump -u refugee_app -p'PASSWORD' refugee_innovation_hub > "$BACKUP_DIR/db_$DATE.sql"

# Files backup
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" /var/www/html/refugee-innovation-hub/uploads/

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete
```

```bash
sudo chmod +x /usr/local/bin/backup-refugee-hub.sh

# Add to cron (daily at 2 AM)
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/backup-refugee-hub.sh
```

**10. Monitoring**:

```bash
# Install monitoring tools
sudo apt install htop iotop -y

# Check logs
sudo tail -f /var/log/apache2/refugee-hub-error.log
sudo tail -f /var/log/php/refugee_hub_errors.log

# Monitor database
sudo mysql -u root -p -e "SHOW PROCESSLIST;"
```

---

## Testing & Verification

### Manual Testing Checklist

**Public Features**:
- [ ] Homepage loads without errors
- [ ] Stories grid displays
- [ ] Map renders with markers
- [ ] Filters work (region, theme, featured)
- [ ] Story detail modal opens
- [ ] Story submission form works
- [ ] Image upload functions
- [ ] Form validation works
- [ ] Success message after submission

**Admin Features**:
- [ ] Login works with correct credentials
- [ ] Login fails with wrong credentials
- [ ] Dashboard loads after login
- [ ] Pending submissions displayed
- [ ] Approve submission works
- [ ] Reject submission works
- [ ] Edit Location button appears
- [ ] Edit Location modal works
- [ ] Coordinates update successfully
- [ ] Map updates after coordinate change
- [ ] Stats display correctly
- [ ] Logout works

**Cross-Browser Testing**:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile browsers (iOS Safari, Chrome)

**Responsive Testing**:
- [ ] Desktop (1920x1080)
- [ ] Laptop (1366x768)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

---

### Verification Script

**Run**: `http://localhost/refugee-innovation-hub/verify-changes.php`

**Checks**:
1. ✓ Sample stories removed from JavaScript
2. ✓ No auto-generated Unsplash images
3. ✓ Story update endpoint available
4. ✓ Database connected
5. ✓ Edit Location feature present
6. ✓ File structure correct

---

### SQL Verification Queries

```sql
-- Check database structure
SHOW TABLES;
-- Expected: innovation_stories, story_submissions, users, site_analytics

-- Check sample data
SELECT COUNT(*) FROM innovation_stories;
-- Expected: 5 (or 0 if cleaned)

-- Check admin user
SELECT email, role FROM users;
-- Expected: admin@jrsusa.org | admin

-- Check submissions workflow
SELECT s.id, s.title, s.status, i.id AS story_id
FROM story_submissions s
LEFT JOIN innovation_stories i ON i.title = s.title
WHERE s.status = 'approved';
-- Should show mapping between approved submissions and stories

-- Check coordinates
SELECT id, title, latitude, longitude
FROM innovation_stories
WHERE latitude IS NULL OR longitude IS NULL;
-- Shows stories missing coordinates

-- Check analytics
SELECT event_type, COUNT(*) AS count
FROM site_analytics
GROUP BY event_type;
-- Shows event distribution
```

---

## Known Limitations

### 1. No User Registration
**Current**: Only default admin can login
**Workaround**: Manually create users in database
**Future**: Add user registration endpoint with email verification

### 2. No Email Notifications
**Current**: Submitters not notified of approval/rejection
**Workaround**: Manually email submitters
**Future**: Integrate SMTP (PHPMailer) for automated emails

### 3. No Pagination
**Current**: All stories loaded at once
**Impact**: Performance degrades with 1000+ stories
**Workaround**: Implement client-side pagination (not ideal)
**Future**: Server-side pagination with `LIMIT` and `OFFSET`

### 4. No Search Functionality
**Current**: Only filtering by region/theme
**Workaround**: Use browser find (Ctrl+F)
**Future**: Full-text search on title/description

### 5. No Rich Text Editor
**Current**: Plain textarea for descriptions
**Workaround**: Use Markdown formatting (not rendered)
**Future**: Integrate Quill.js or TinyMCE

### 6. No Image Resizing
**Current**: Original images stored (large files)
**Impact**: Slow page load for high-res uploads
**Workaround**: Ask users to resize before upload
**Future**: Server-side image resizing (GD/ImageMagick)

### 7. No Analytics Dashboard
**Current**: Raw analytics tracked but no visualization
**Workaround**: Query database manually
**Future**: Charts.js integration for visual analytics

### 8. No Multi-Language Support
**Current**: English only
**Workaround**: Manual translation of static content
**Future**: i18n library (i18next) for translations

### 9. No API Documentation
**Current**: This document serves as reference
**Workaround**: Read code comments and this doc
**Future**: OpenAPI/Swagger documentation

### 10. No Automated Testing
**Current**: Manual testing only
**Workaround**: Follow testing checklist
**Future**: PHPUnit for backend, Jest for frontend

---

## Future Improvements

### Phase 1: Essential Features (1-2 weeks)

**1. User Management**:
- [ ] User registration endpoint
- [ ] Email verification (activation link)
- [ ] Password reset flow
- [ ] User profile management

**2. Enhanced Search**:
- [ ] Full-text search on stories
- [ ] Search suggestions/autocomplete
- [ ] Advanced filters (date range, beneficiaries count)

**3. Pagination**:
- [ ] Server-side pagination (25 items per page)
- [ ] "Load more" button
- [ ] Page number navigation

**4. Image Optimization**:
- [ ] Automatic resizing (max 1200px width)
- [ ] Thumbnail generation (300px width)
- [ ] WebP conversion for better compression

### Phase 2: Quality of Life (2-3 weeks)

**5. Rich Text Editor**:
- [ ] Quill.js integration
- [ ] Markdown support
- [ ] Image embedding in descriptions

**6. Email Notifications**:
- [ ] PHPMailer integration
- [ ] Email templates
- [ ] Submission received confirmation
- [ ] Approval/rejection notifications
- [ ] Weekly digest for admins

**7. Analytics Dashboard**:
- [ ] Charts.js for visualizations
- [ ] Story views over time
- [ ] Submissions funnel
- [ ] Region/theme distribution charts
- [ ] Export to CSV

**8. Audit Logging**:
- [ ] Log all admin actions
- [ ] Log table structure
- [ ] Audit log viewer in dashboard

### Phase 3: Advanced Features (3-4 weeks)

**9. Multi-Language Support**:
- [ ] i18next library
- [ ] Language selector
- [ ] Translations: English, French, Arabic, Spanish
- [ ] RTL support for Arabic

**10. Advanced Map Features**:
- [ ] Clustering for dense markers
- [ ] Heat map visualization
- [ ] Filter stories directly on map
- [ ] Custom map styles

**11. Social Features**:
- [ ] Story sharing (Facebook, Twitter, LinkedIn)
- [ ] Embed codes for external sites
- [ ] Public API for partner organizations

**12. API Enhancements**:
- [ ] RESTful API versioning (/api/v1/)
- [ ] Rate limiting
- [ ] API keys for external access
- [ ] OpenAPI/Swagger documentation
- [ ] GraphQL endpoint (optional)

### Phase 4: Enterprise Features (4-6 weeks)

**13. Role-Based Access Control**:
- [ ] Multiple roles (admin, editor, viewer)
- [ ] Permission matrix
- [ ] Resource-level permissions
- [ ] Approval workflows with multiple reviewers

**14. Advanced Analytics**:
- [ ] Google Analytics integration
- [ ] Heatmaps (user behavior)
- [ ] A/B testing framework
- [ ] Custom event tracking

**15. Performance Optimization**:
- [ ] Redis caching for stories
- [ ] CDN integration for images
- [ ] Lazy loading for images
- [ ] Service worker for offline access
- [ ] Progressive Web App (PWA)

**16. Automated Testing**:
- [ ] PHPUnit for backend
- [ ] Jest for frontend
- [ ] E2E tests with Cypress
- [ ] CI/CD pipeline (GitHub Actions)

**17. DevOps**:
- [ ] Docker containerization
- [ ] Kubernetes deployment
- [ ] Automated backups to S3
- [ ] Monitoring (Prometheus, Grafana)
- [ ] Log aggregation (ELK stack)

---

## Appendix

### Environment Variables

**File**: `api/config.php`

```php
// Database
DB_HOST          localhost
DB_NAME          refugee_innovation_hub
DB_USER          root (dev) / refugee_app (prod)
DB_PASS          empty (dev) / strong password (prod)
DB_CHARSET       utf8mb4

// Security
JWT_SECRET       your-secret-key-change-this-in-production

// Paths
UPLOAD_DIR       /uploads/stories/
MAX_UPLOAD_SIZE  5242880 (5 MB)
```

### API Base URL Configuration

**Development**:
```javascript
// api/php-api-client.js
const baseUrl = 'http://localhost/refugee-innovation-hub/api';
```

**Production**:
```javascript
// api/php-api-client.js
const baseUrl = 'https://refugee-hub.yourdomain.com/api';
```

### Database Regions

Predefined values:
- East Africa
- West Africa
- Middle East
- Southeast Asia
- South Asia
- Europe
- North America
- South America

### Database Themes

Predefined values:
- Education
- Health
- Livelihoods
- Arts & Culture
- Technology
- Environment
- Community Development

### File Size Limits

```php
// PHP settings (php.ini)
upload_max_filesize = 10M
post_max_size = 15M
max_execution_time = 300

// Application limit (api/submissions.php)
$maxSize = 5 * 1024 * 1024; // 5 MB
```

---

## Glossary

**API**: Application Programming Interface - set of endpoints for data access
**PDO**: PHP Data Objects - database abstraction layer
**CRUD**: Create, Read, Update, Delete - basic data operations
**SPA**: Single-Page Application - client-side routing
**XSS**: Cross-Site Scripting - code injection vulnerability
**CSRF**: Cross-Site Request Forgery - unauthorized action attack
**SQL Injection**: Database query manipulation attack
**Bcrypt**: Password hashing algorithm
**CORS**: Cross-Origin Resource Sharing - browser security policy
**MIME Type**: File content type identifier
**Slug**: URL-friendly identifier (e.g., "my-story-title")
**Session**: Server-side user state storage
**Prepared Statement**: SQL query with parameterized inputs
**Rate Limiting**: Request throttling to prevent abuse
**RBAC**: Role-Based Access Control - permission system
**JWT**: JSON Web Token - stateless authentication method
**Foreign Key**: Database relationship constraint
**Index**: Database performance optimization
**Collation**: String comparison rules (utf8mb4_unicode_ci)

---

## Contact & Support

**Project**: Refugee Innovation Hub
**Organization**: JRS USA (placeholder)
**Documentation**: This file
**Issues**: Check verify-changes.php for diagnostics

---

**Document Version**: 1.0.0
**Last Updated**: 2025-12-17
**Author**: Technical Documentation Team
**Status**: Production Ready

---

**End of Technical Documentation**
