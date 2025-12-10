# Create New Admin Accounts

## Quick Reference

This guide shows you how to create additional admin accounts for the Refugee Innovation Hub.

## Pre-Made Admin Accounts (Ready to Use)

I've created a SQL file with 3 ready-to-use admin accounts. Here's how to add them:

### Step 1: Open phpMyAdmin
1. Go to: `http://localhost/phpmyadmin`
2. Select the `refugee_innovation_hub` database (left sidebar)
3. Click the **SQL** tab

### Step 2: Run the SQL File
1. Click **"Choose File"** or copy/paste from `database/add_admin_users.sql`
2. Click **"Go"**

### Step 3: Use Your New Admin Accounts

**Account 1: Field Coordinator**
- Email: `coordinator@jrsusa.org`
- Password: `JRSField2024`

**Account 2: Regional Manager**
- Email: `regional@jrsusa.org`
- Password: `JRSRegion2024`

**Account 3: Content Manager**
- Email: `content@jrsusa.org`
- Password: `JRSContent2024`

⚠️ **Security Note:** These are temporary passwords. Change them after first login!

---

## Create Custom Admin (Your Email/Password)

### Method A: Using the Password Generator

1. **Generate your password hash:**
   ```
   http://localhost/refugee-innovation-hub/api/generate-password.php?password=YourPassword123
   ```

2. **Copy the hash that appears**

3. **Run this SQL in phpMyAdmin:**
   ```sql
   INSERT INTO users (email, password_hash, role, is_active, created_at)
   VALUES (
       'yourname@jrsusa.org',
       'PASTE_YOUR_HASH_HERE',
       'admin',
       1,
       NOW()
   );
   ```

### Method B: Quick Insert with Default Password

```sql
INSERT INTO users (email, password_hash, role, is_active, created_at)
VALUES (
    'yourname@jrsusa.org',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    1,
    NOW()
);
```

**Default password:** `password123` (change it immediately!)

---

## Manage Existing Admins

### View All Admin Accounts

```sql
SELECT id, email, role, is_active, created_at, last_login
FROM users
WHERE role = 'admin'
ORDER BY created_at DESC;
```

### Deactivate an Admin (Don't Delete)

```sql
UPDATE users
SET is_active = 0
WHERE email = 'admin@example.com';
```

### Reactivate an Admin

```sql
UPDATE users
SET is_active = 1
WHERE email = 'admin@example.com';
```

### Change Admin Password

1. Generate new hash:
   ```
   http://localhost/refugee-innovation-hub/api/generate-password.php?password=NewPassword456
   ```

2. Update the password:
   ```sql
   UPDATE users
   SET password_hash = 'PASTE_NEW_HASH_HERE'
   WHERE email = 'admin@example.com';
   ```

### Delete an Admin (Permanent)

```sql
DELETE FROM users
WHERE email = 'admin@example.com';
```

⚠️ **Warning:** This is permanent! Consider deactivating instead.

---

## Password Security Best Practices

### Strong Password Requirements
- Minimum 12 characters
- Mix of uppercase and lowercase
- Include numbers
- Include special characters (!@#$%^&*)
- Don't use common words or patterns

### Good Password Examples
- `JRS@Field2024!Secure`
- `Refugee#Hub$Admin99`
- `InnovateJRS*2024!Safe`

### Password Storage
- Never store passwords in plain text
- Use the password generator to create hashes
- All passwords in database are bcrypt hashed
- Even admins cannot see other users' passwords

---

## Troubleshooting

### "Duplicate entry" Error
**Problem:** Email already exists
**Solution:** Use a different email or update existing user

### Can't Login After Creating Admin
**Problem:** Incorrect password hash
**Solution:**
1. Verify you copied the complete hash from password generator
2. Ensure no extra spaces before/after the hash
3. Try regenerating the hash

### Password Generator Not Working
**Problem:** 404 or PHP error
**Solution:**
1. Check `api/generate-password.php` exists
2. Verify Apache is running
3. Try accessing: `http://localhost/refugee-innovation-hub/api/`

### Admin Can Login But Can't Submit Stories
**Problem:** Role not set correctly
**Solution:**
```sql
UPDATE users
SET role = 'admin'
WHERE email = 'yourname@jrsusa.org';
```

---

## Database Schema Reference

### users Table Structure

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Auto-increment primary key |
| email | VARCHAR(255) | Unique email (login username) |
| password_hash | VARCHAR(255) | Bcrypt hashed password |
| role | ENUM('admin','user') | User role (use 'admin') |
| is_active | TINYINT(1) | 1 = active, 0 = deactivated |
| created_at | TIMESTAMP | Account creation date |
| last_login | TIMESTAMP | Last successful login |

---

## Quick Commands Cheat Sheet

### Create Admin (Quick)
```sql
INSERT INTO users (email, password_hash, role, is_active, created_at)
VALUES ('new@jrsusa.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NOW());
```
Password: `password123`

### List All Admins
```sql
SELECT email, is_active, created_at FROM users WHERE role = 'admin';
```

### Deactivate Admin
```sql
UPDATE users SET is_active = 0 WHERE email = 'admin@example.com';
```

### Change Password
1. Generate: `http://localhost/refugee-innovation-hub/api/generate-password.php?password=NewPass123`
2. Update: `UPDATE users SET password_hash = 'HASH' WHERE email = 'admin@example.com';`

### Delete Admin
```sql
DELETE FROM users WHERE email = 'admin@example.com';
```

---

## Next Steps After Creating Admins

1. ✅ Test each new admin account
2. ✅ Have admins change their passwords
3. ✅ Document who has admin access
4. ✅ Set up a password policy for your team
5. ✅ Consider deactivating the default admin account

---

**Created:** December 10, 2025
**For:** Refugee Innovation Hub Admin Management
