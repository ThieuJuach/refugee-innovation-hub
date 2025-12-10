<?php
/**
 * Authentication Debug Script
 * This script tests all components of the authentication system
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Authentication System Debug</h1>";
echo "<hr>";

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>";
try {
    $dsn = "mysql:host=localhost;dbname=refugee_innovation_hub;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "✅ <strong>SUCCESS:</strong> Connected to database<br>";
} catch (PDOException $e) {
    echo "❌ <strong>ERROR:</strong> Database connection failed: " . $e->getMessage() . "<br>";
    exit();
}

// Test 2: Check if users table exists
echo "<h2>Test 2: Check Users Table</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ <strong>SUCCESS:</strong> Users table exists<br>";
    } else {
        echo "❌ <strong>ERROR:</strong> Users table does not exist<br>";
        exit();
    }
} catch (PDOException $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
    exit();
}

// Test 3: Check users table structure
echo "<h2>Test 3: Users Table Structure</h2>";
try {
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    echo "<pre>";
    foreach ($columns as $col) {
        echo "{$col['Field']}: {$col['Type']}<br>";
    }
    echo "</pre>";
} catch (PDOException $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
}

// Test 4: Count users in database
echo "<h2>Test 4: Check Users Count</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✅ <strong>SUCCESS:</strong> Found {$result['count']} user(s) in database<br>";
} catch (PDOException $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
}

// Test 5: List all users
echo "<h2>Test 5: List All Users</h2>";
try {
    $stmt = $pdo->query("SELECT id, email, name, role, is_active FROM users");
    $users = $stmt->fetchAll();
    if (count($users) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Email</th><th>Name</th><th>Role</th><th>Active</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['name']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>" . ($user['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ <strong>WARNING:</strong> No users found in database<br>";
    }
} catch (PDOException $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
}

// Test 6: Check default admin user
echo "<h2>Test 6: Check Default Admin User</h2>";
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@jrsusa.org']);
    $admin = $stmt->fetch();

    if ($admin) {
        echo "✅ <strong>SUCCESS:</strong> Default admin user exists<br>";
        echo "<strong>Email:</strong> {$admin['email']}<br>";
        echo "<strong>Name:</strong> {$admin['name']}<br>";
        echo "<strong>Role:</strong> {$admin['role']}<br>";
        echo "<strong>Active:</strong> " . ($admin['is_active'] ? 'Yes' : 'No') . "<br>";
        echo "<strong>Password Hash:</strong> " . substr($admin['password_hash'], 0, 20) . "...<br>";
    } else {
        echo "❌ <strong>ERROR:</strong> Default admin user not found<br>";
        echo "<strong>Try this SQL:</strong><br>";
        echo "<pre>INSERT INTO users (email, password_hash, name, role, is_active) VALUES ('admin@jrsusa.org', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin', 1);</pre>";
    }
} catch (PDOException $e) {
    echo "❌ <strong>ERROR:</strong> " . $e->getMessage() . "<br>";
}

// Test 7: Test password verification
echo "<h2>Test 7: Test Password Verification</h2>";
$testPassword = 'admin123';
if (isset($admin) && $admin) {
    if (password_verify($testPassword, $admin['password_hash'])) {
        echo "✅ <strong>SUCCESS:</strong> Password 'admin123' is valid for admin@jrsusa.org<br>";
    } else {
        echo "❌ <strong>ERROR:</strong> Password 'admin123' does NOT match the stored hash<br>";
        echo "<strong>Stored hash:</strong> {$admin['password_hash']}<br>";
        echo "<strong>Test generating new hash:</strong><br>";
        $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
        echo "<pre>{$newHash}</pre>";
        echo "<strong>Update SQL:</strong><br>";
        echo "<pre>UPDATE users SET password_hash = '{$newHash}' WHERE email = 'admin@jrsusa.org';</pre>";
    }
}

// Test 8: Test session functionality
echo "<h2>Test 8: Test PHP Sessions</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ <strong>SUCCESS:</strong> PHP sessions are working<br>";
    echo "<strong>Session ID:</strong> " . session_id() . "<br>";

    // Test setting and reading session variable
    $_SESSION['test'] = 'Session test successful';
    if (isset($_SESSION['test'])) {
        echo "✅ <strong>SUCCESS:</strong> Can write and read session variables<br>";
    }
} else {
    echo "❌ <strong>ERROR:</strong> PHP sessions not working<br>";
}

// Test 9: Simulate login
echo "<h2>Test 9: Simulate Login Process</h2>";
if (isset($admin) && $admin && password_verify($testPassword, $admin['password_hash'])) {
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['user_email'] = $admin['email'];
    $_SESSION['user_name'] = $admin['name'];
    $_SESSION['user_role'] = $admin['role'];

    echo "✅ <strong>SUCCESS:</strong> Simulated login successful<br>";
    echo "<strong>Session data:</strong><br>";
    echo "<pre>";
    echo "user_id: {$_SESSION['user_id']}<br>";
    echo "user_email: {$_SESSION['user_email']}<br>";
    echo "user_name: {$_SESSION['user_name']}<br>";
    echo "user_role: {$_SESSION['user_role']}<br>";
    echo "</pre>";
} else {
    echo "❌ <strong>ERROR:</strong> Cannot simulate login - prerequisites failed<br>";
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>If all tests passed, you should be able to login with:</p>";
echo "<strong>Email:</strong> admin@jrsusa.org<br>";
echo "<strong>Password:</strong> admin123<br>";
echo "<br>";
echo "<p><a href='index.html'>Go to Login Page</a></p>";
?>
