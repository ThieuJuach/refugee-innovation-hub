-- Refugee Innovation Hub Database Schema for MySQL
-- Run this file in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS refugee_innovation_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE refugee_innovation_hub;

-- Table: innovation_stories
CREATE TABLE IF NOT EXISTS innovation_stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    summary TEXT NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    region VARCHAR(100) NOT NULL,
    theme VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    image_url TEXT NULL,
    innovator_name VARCHAR(255) NOT NULL,
    beneficiaries_count INT DEFAULT 0,
    contact_email VARCHAR(255) NULL,
    contact_info TEXT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_region (region),
    INDEX idx_theme (theme),
    INDEX idx_featured (is_featured),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: story_submissions
CREATE TABLE IF NOT EXISTS story_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    region VARCHAR(100) NOT NULL,
    theme VARCHAR(100) NOT NULL,
    innovator_name VARCHAR(255) NOT NULL,
    impact TEXT NULL,
    contact_email VARCHAR(255) NOT NULL,
    contact_info TEXT NULL,
    image_url TEXT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_submitted_at (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: site_analytics
CREATE TABLE IF NOT EXISTS site_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(100) NOT NULL,
    story_id INT NULL,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event_type (event_type),
    INDEX idx_story_id (story_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (story_id) REFERENCES innovation_stories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: users (for admin authentication)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NULL,
    role ENUM('admin', 'editor') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data
INSERT INTO innovation_stories (title, slug, summary, description, location, region, theme, latitude, longitude, image_url, innovator_name, beneficiaries_count, contact_email, is_featured) VALUES
('Digital Learning Platform for Refugee Children', 'digital-learning-platform-refugee-children', 'Amina created an innovative digital learning platform that provides quality education to refugee children in Kakuma camp.', 'Amina created an innovative digital learning platform that provides quality education to refugee children in Kakuma camp. The platform works offline and includes curriculum in multiple languages, making education accessible even without consistent internet connectivity.', 'Kakuma, Kenya', 'East Africa', 'Education', 3.7167, 34.8667, 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800', 'Amina Hassan', 500, 'amina@example.com', TRUE),
('Solar-Powered Water Purification System', 'solar-powered-water-purification-system', 'Mohammed developed a low-cost, solar-powered water purification system that provides clean drinking water to refugee communities.', 'Mohammed developed a low-cost, solar-powered water purification system that provides clean drinking water to refugee communities. The system uses locally available materials and can be maintained by community members.', 'Zaatari, Jordan', 'Middle East', 'Health', 32.3078, 36.3275, 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800', 'Mohammed Al-Rashid', 1000, 'mohammed@example.com', TRUE),
('Mobile App for Job Matching', 'mobile-app-job-matching', 'Fatima created a mobile application that connects refugee job seekers with local employers.', 'Fatima created a mobile application that connects refugee job seekers with local employers. The app includes skills assessment, resume building, and job matching features tailored to refugee needs.', 'Beirut, Lebanon', 'Middle East', 'Livelihoods', 33.8938, 35.5018, 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800', 'Fatima Al-Zahra', 300, 'fatima@example.com', TRUE),
('Community Garden Initiative', 'community-garden-initiative', 'Jean-Baptiste established a community garden that provides fresh vegetables to refugee families while creating income opportunities.', 'Jean-Baptiste established a community garden that provides fresh vegetables to refugee families while creating income opportunities. The garden uses sustainable farming techniques and trains community members.', 'Kigali, Rwanda', 'East Africa', 'Livelihoods', -1.9441, 30.0619, 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800', 'Jean-Baptiste Nkurunziza', 200, 'jean@example.com', FALSE),
('Refugee Artisan Marketplace', 'refugee-artisan-marketplace', 'Sara created an online marketplace that connects refugee artisans with global customers.', 'Sara created an online marketplace that connects refugee artisans with global customers. The platform showcases traditional crafts and provides fair-trade opportunities for refugee communities.', 'Kuala Lumpur, Malaysia', 'Southeast Asia', 'Arts & Culture', 3.1390, 101.6869, 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?w=800', 'Sara Ahmed', 100, 'sara@example.com', TRUE);

-- Create default admin user (password: admin123 - CHANGE THIS!)
-- Password hash for 'admin123' using password_hash() PHP function
INSERT INTO users (email, password_hash, name, role) VALUES
('admin@jrsusa.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin');

