<?php
/**
 * Database Setup Script
 * This script will create the database and insert the default admin user
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Setup Script</h1>";
echo "<hr>";

// Step 1: Connect to MySQL (without database)
echo "<h2>Step 1: Connect to MySQL Server</h2>";
try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✅ <strong>SUCCESS:</strong> Connected to MySQL server<br>";
} catch (PDOException $e) {
    echo "❌ <strong>ERROR:</strong> Cannot connect to MySQL: " . $e->getMessage() . "<br>";
    echo "<p>Make sure XAMPP is running and MySQL is started.</p>";
    exit();
}

// Step 2: Create database
echo "<h2>Step 2: Create Database</h2>";
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS refugee_innovation_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ <strong>SUCCESS:</strong> Database 'refugee_innovation_hub' created or already exists<br>";
} catch (PDOException $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
    exit();
}

// Step 3: Select database
echo "<h2>Step 3: Select Database</h2>";
try {
    $pdo->exec("USE refugee_innovation_hub");
    echo "✅ <strong>SUCCESS:</strong> Using database 'refugee_innovation_hub'<br>";
} catch (PDOException $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
    exit();
}

// Step 4: Create users table
echo "<h2>Step 4: Create Users Table</h2>";
try {
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        name VARCHAR(255) NULL,
        role ENUM('admin', 'editor') DEFAULT 'admin',
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);
    echo "✅ <strong>SUCCESS:</strong> Users table created or already exists<br>";
} catch (PDOException $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
    exit();
}

// Step 5: Check if admin user exists
echo "<h2>Step 5: Check Admin User</h2>";
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@jrsusa.org']);
    $admin = $stmt->fetch();

    if ($admin) {
        echo "✅ <strong>INFO:</strong> Admin user already exists<br>";
        echo "<strong>Email:</strong> {$admin['email']}<br>";
        echo "<strong>Name:</strong> {$admin['name']}<br>";
    } else {
        echo "⚠️ <strong>INFO:</strong> Admin user does not exist, creating...<br>";

        // Create admin user
        $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, name, role, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['admin@jrsusa.org', $passwordHash, 'Admin User', 'admin', 1]);

        echo "✅ <strong>SUCCESS:</strong> Admin user created<br>";
        echo "<strong>Email:</strong> admin@jrsusa.org<br>";
        echo "<strong>Password:</strong> admin123<br>";
    }
} catch (PDOException $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
    exit();
}

// Step 6: Create other tables
echo "<h2>Step 6: Create Other Tables</h2>";

// Innovation stories table
try {
    $sql = "CREATE TABLE IF NOT EXISTS innovation_stories (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);
    echo "✅ <strong>SUCCESS:</strong> innovation_stories table created<br>";
} catch (PDOException $e) {
    echo "⚠️ <strong>WARNING:</strong> " . $e->getMessage() . "<br>";
}

// Story submissions table
try {
    $sql = "CREATE TABLE IF NOT EXISTS story_submissions (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);
    echo "✅ <strong>SUCCESS:</strong> story_submissions table created<br>";
} catch (PDOException $e) {
    echo "⚠️ <strong>WARNING:</strong> " . $e->getMessage() . "<br>";
}

// Site analytics table
try {
    $sql = "CREATE TABLE IF NOT EXISTS site_analytics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_type VARCHAR(100) NOT NULL,
        story_id INT NULL,
        metadata JSON NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_event_type (event_type),
        INDEX idx_story_id (story_id),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $pdo->exec($sql);
    echo "✅ <strong>SUCCESS:</strong> site_analytics table created<br>";
} catch (PDOException $e) {
    echo "⚠️ <strong>WARNING:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>✅ Setup Complete!</h2>";
echo "<p>Your database is now set up and ready to use.</p>";
echo "<p><strong>Login credentials:</strong></p>";
echo "<ul>";
echo "<li><strong>Email:</strong> admin@jrsusa.org</li>";
echo "<li><strong>Password:</strong> admin123</li>";
echo "</ul>";
echo "<p><a href='test-auth.php'>Run Authentication Tests</a> | <a href='index.html'>Go to Login Page</a></p>";
?>
