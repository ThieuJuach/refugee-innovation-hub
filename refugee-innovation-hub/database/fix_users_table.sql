-- Fix Users Table - Add is_active Column
-- Run this SQL if your users table already exists and is missing the is_active column

USE refugee_innovation_hub;

-- Check if is_active column exists, add if missing
ALTER TABLE users
ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1 AFTER role;

-- Ensure all existing users are active
UPDATE users SET is_active = 1 WHERE is_active IS NULL;

-- Verify the fix
SELECT id, email, role, is_active, created_at FROM users;
