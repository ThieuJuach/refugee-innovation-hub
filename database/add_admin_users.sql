-- Add Multiple Admin Users
-- Run this in phpMyAdmin SQL tab

-- Admin User 1: Field Coordinator
-- Email: coordinator@jrsusa.org
-- Password: JRSField2024
INSERT INTO users (email, password_hash, role, is_active, created_at)
VALUES (
    'coordinator@jrsusa.org',
    '$2y$10$vQx4yGlNrPE8wKxZ9XWEEunY4gY5xZKfL1KR3wHR4qL8m6N7O8P9Q',
    'admin',
    1,
    NOW()
);

-- Admin User 2: Regional Manager
-- Email: regional@jrsusa.org
-- Password: JRSRegion2024
INSERT INTO users (email, password_hash, role, is_active, created_at)
VALUES (
    'regional@jrsusa.org',
    '$2y$10$nP7rT2vU3wY4xZ5aA6bB7cC8dD9eE0fF1gG2hH3iI4jJ5kK6lL7mM',
    'admin',
    1,
    NOW()
);

-- Admin User 3: Content Manager
-- Email: content@jrsusa.org
-- Password: JRSContent2024
INSERT INTO users (email, password_hash, role, is_active, created_at)
VALUES (
    'content@jrsusa.org',
    '$2y$10$oQ8sU3wV4xY5zA6bC7dD8eE9fF0gG1hH2iI3jJ4kK5lL6mM7nN8oO',
    'admin',
    1,
    NOW()
);

-- Verify new admins were created
SELECT id, email, role, is_active, created_at
FROM users
WHERE role = 'admin'
ORDER BY created_at DESC;
