<?php

// TEMPORARY: show all errors for debugging. Remove in production.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


/**
 * Database Configuration
 * Update these values to match your XAMPP MySQL setup
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'refugee_innovation_hub');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty
define('DB_CHARSET', 'utf8mb4');

// JWT Secret for authentication (change this to a random string)
define('JWT_SECRET', 'your-secret-key-change-this-in-production');

// CORS settings (for development)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
// Set JSON header only if not handling file upload
if (empty($_FILES)) {
    header('Content-Type: application/json');
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * Database Connection
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
        exit();
    }
}

/**
 * Send JSON Response
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

/**
 * Send Error Response
 */
function sendError($message, $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    session_start();
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Require authentication
 */
function requireAuth() {
    if (!isAuthenticated()) {
        sendError('Authentication required', 401);
    }
}

