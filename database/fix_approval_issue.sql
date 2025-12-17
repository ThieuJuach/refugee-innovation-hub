-- Fix for Story Approval Issue
-- Run this in phpMyAdmin to fix the innovation_stories table

USE refugee_innovation_hub;

-- Add impact column if it doesn't exist
ALTER TABLE innovation_stories
ADD COLUMN IF NOT EXISTS impact TEXT NULL AFTER innovator_name;

-- Fix is_featured column type to use TINYINT
ALTER TABLE innovation_stories
MODIFY COLUMN is_featured TINYINT(1) DEFAULT 0;

-- Fix users table if it has is_active column
ALTER TABLE users
MODIFY COLUMN is_active TINYINT(1) DEFAULT 1;
