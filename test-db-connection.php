<?php
/**
 * Database Connection and User Verification Test
 * Access this at: http://localhost/refugee-innovation-hub/test-db-connection.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

// Test 1: Include config
echo "<h2>Test 1: Loading Configuration</h2>";
try {
    require_once 'api/config.php';
    echo "✅ Config loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Config failed: " . $e->getMessage() . "<br>";
    die();
}

// Test 2: Database connection
echo "<h2>Test 2: Database Connection</h2>";
try {
    $pdo = getDBConnection();
    echo "✅ Connected to database successfully<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    die();
}

// Test 3: Check if users table exists
echo "<h2>Test 3: Users Table</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $result = $stmt->fetch();
    if ($result) {
        echo "✅ Users table exists<br>";
    } else {
        echo "❌ Users table does not exist<br>";
        die();
    }
} catch (Exception $e) {
    echo "❌ Error checking table: " . $e->getMessage() . "<br>";
    die();
}

// Test 4: Check for admin users
echo "<h2>Test 4: Admin Users in Database</h2>";
try {
    $stmt = $pdo->query("SELECT id, email, role, is_active, created_at FROM users WHERE role = 'admin'");
    $users = $stmt->fetchAll();

    if (count($users) > 0) {
        echo "✅ Found " . count($users) . " admin user(s):<br>";
        echo "<table border='1' cellpadding='5' style='margin-top: 10px;'>";
        echo "<tr><th>ID</th><th>Email</th><th>Role</th><th>Active</th><th>Created</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>" . ($user['is_active'] ? '✅ Yes' : '❌ No') . "</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No admin users found in database<br>";
        echo "<p><strong>Action Required:</strong> You need to import the database schema.</p>";
        echo "<p>Go to phpMyAdmin → refugee_innovation_hub → Import → database/schema.sql</p>";
    }
} catch (Exception $e) {
    echo "❌ Error querying users: " . $e->getMessage() . "<br>";
}

// Test 5: Test password verification
echo "<h2>Test 5: Password Hash Verification</h2>";
try {
    $testEmail = 'admin@jrsusa.org';
    $testPassword = 'admin123';

    $stmt = $pdo->prepare("SELECT email, password_hash FROM users WHERE email = ?");
    $stmt->execute([$testEmail]);
    $user = $stmt->fetch();

    if ($user) {
        echo "✅ User found: {$user['email']}<br>";
        echo "Password hash: " . substr($user['password_hash'], 0, 20) . "...<br>";

        if (password_verify($testPassword, $user['password_hash'])) {
            echo "✅ Password verification SUCCESSFUL for '{$testPassword}'<br>";
            echo "<strong style='color: green;'>Authentication should work!</strong><br>";
        } else {
            echo "❌ Password verification FAILED for '{$testPassword}'<br>";
            echo "<strong style='color: red;'>Password hash doesn't match!</strong><br>";
            echo "<p>To fix: Run this SQL in phpMyAdmin:</p>";
            echo "<pre>UPDATE users SET password_hash = '" . password_hash('admin123', PASSWORD_DEFAULT) . "' WHERE email = 'admin@jrsusa.org';</pre>";
        }
    } else {
        echo "❌ User {$testEmail} not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Error testing password: " . $e->getMessage() . "<br>";
}

// Test 6: Session configuration
echo "<h2>Test 6: PHP Session Configuration</h2>";
echo "Session save path: " . session_save_path() . "<br>";
echo "Session name: " . session_name() . "<br>";
echo "Session cookie lifetime: " . ini_get('session.cookie_lifetime') . "<br>";

// Test 7: Generate new password hash
echo "<h2>Test 7: Password Hash Generator</h2>";
echo "<p>Copy these to reset passwords:</p>";
echo "<ul>";
echo "<li><strong>admin123:</strong> <code>" . password_hash('admin123', PASSWORD_DEFAULT) . "</code></li>";
echo "<li><strong>password123:</strong> <code>" . password_hash('password123', PASSWORD_DEFAULT) . "</code></li>";
echo "</ul>";

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>If all tests pass, authentication should work. If not, check the errors above.</p>";
echo "<p><strong>Delete this file after testing for security!</strong></p>";
?>
