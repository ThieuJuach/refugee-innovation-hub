<?php
/**
 * File Upload Handler
 * Handles image uploads for story submissions
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method allowed', 405);
}

// Check if file was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    sendError('No file uploaded or upload error occurred');
}

$file = $_FILES['image'];

// Validate file type
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$fileType = mime_content_type($file['tmp_name']);

if (!in_array($fileType, $allowedTypes)) {
    sendError('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
}

// Validate file size (max 5MB)
$maxSize = 5 * 1024 * 1024; // 5MB in bytes
if ($file['size'] > $maxSize) {
    sendError('File size exceeds 5MB limit.');
}

// Create uploads directory if it doesn't exist
$uploadDir = __DIR__ . '/../uploads/stories/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('story_', true) . '_' . time() . '.' . $extension;
$filepath = $uploadDir . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    sendError('Failed to save uploaded file.');
}

// Return path relative to the web server root
// Auto-detect project path from request URI
$requestUri = $_SERVER['REQUEST_URI'];
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$projectPath = str_replace('/api', '', $scriptPath);

$fileUrl = $projectPath . '/uploads/stories/' . $filename;

sendResponse([
    'success' => true,
    'url' => $fileUrl,
    'filename' => $filename,
    'message' => 'File uploaded successfully'
]);

