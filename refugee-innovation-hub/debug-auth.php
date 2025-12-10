<?php
/**
 * Authentication Debug Script
 * This will show you EXACTLY what's happening with login
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Authentication Debug</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;} .error{color:red;} .success{color:green;} pre{background:#f5f5f5;padding:10px;}</style>";

// Step 1: Load config
echo "<h2>Step 1: Loading Configuration</h2>";
try {
    require_once 'api/config.php';
    echo "<p class='success'>✅ Config loaded</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Failed: " . $e->getMessage() . "</p>";
    die();
}

// Step 2: Connect to database
echo "<h2>Step 2: Database Connection</h2>";
try {
    $pdo = getDBConnection();
    echo "<p class='success'>✅ Connected to database: " . DB_NAME . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Failed: " . $e->getMessage() . "</p>";
    die();
}

// Step 3: Check users table structure
echo "<h2>Step 3: Users Table Structure</h2>";
try {
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();

    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";

    $hasIsActive = false;
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";

        if ($col['Field'] === 'is_active') {
            $hasIsActive = true;
        }
    }
    echo "</table>";

    if ($hasIsActive) {
        echo "<p class='success'>✅ is_active column EXISTS</p>";
    } else {
        echo "<p class='error'>❌ is_active column MISSING - This is the problem!</p>";
        echo "<p><strong>FIX:</strong> Run this SQL in phpMyAdmin:</p>";
        echo "<pre>ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER role;</pre>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

// Step 4: Check for admin users
echo "<h2>Step 4: Admin Users</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll();

    if (count($users) === 0) {
        echo "<p class='error'>❌ NO USERS FOUND IN DATABASE!</p>";
        echo "<p><strong>FIX:</strong> Run this SQL in phpMyAdmin:</p>";
        echo "<pre>INSERT INTO users (email, password_hash, name, role, is_active) VALUES
('admin@jrsusa.org', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin', 1);</pre>";
    } else {
        echo "<p class='success'>✅ Found " . count($users) . " user(s)</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Email</th><th>Name</th><th>Role</th><th>is_active</th><th>Password Hash (first 30 chars)</th></tr>";

        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>" . ($user['name'] ?? 'NULL') . "</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>" . (isset($user['is_active']) ? $user['is_active'] : 'COLUMN MISSING') . "</td>";
            echo "<td>" . substr($user['password_hash'], 0, 30) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

// Step 5: Test actual login
echo "<h2>Step 5: Test Login Process</h2>";

$testEmail = 'admin@jrsusa.org';
$testPassword = 'admin123';

echo "<p>Testing with:</p>";
echo "<ul>";
echo "<li><strong>Email:</strong> {$testEmail}</li>";
echo "<li><strong>Password:</strong> {$testPassword}</li>";
echo "</ul>";

try {
    // Get user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$testEmail]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<p class='error'>❌ USER NOT FOUND with email: {$testEmail}</p>";
        echo "<p><strong>FIX:</strong> Create the user with this SQL:</p>";
        echo "<pre>INSERT INTO users (email, password_hash, name, role, is_active) VALUES
('{$testEmail}', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin', 1);</pre>";
    } else {
        echo "<p class='success'>✅ User found in database</p>";

        // Check is_active
        if (isset($user['is_active'])) {
            if ($user['is_active'] == 1) {
                echo "<p class='success'>✅ User is ACTIVE</p>";
            } else {
                echo "<p class='error'>❌ User is INACTIVE (is_active = {$user['is_active']})</p>";
                echo "<p><strong>FIX:</strong> Run this SQL:</p>";
                echo "<pre>UPDATE users SET is_active = 1 WHERE email = '{$testEmail}';</pre>";
            }
        } else {
            echo "<p class='error'>❌ is_active column doesn't exist - authentication will fail!</p>";
        }

        // Test password
        echo "<p>Password hash in database:</p>";
        echo "<pre>{$user['password_hash']}</pre>";

        if (password_verify($testPassword, $user['password_hash'])) {
            echo "<p class='success'>✅ PASSWORD VERIFICATION SUCCESS!</p>";
            echo "<p><strong>Login should work with these credentials.</strong></p>";
        } else {
            echo "<p class='error'>❌ PASSWORD VERIFICATION FAILED!</p>";
            echo "<p>The password hash in the database doesn't match '{$testPassword}'</p>";
            echo "<p><strong>FIX:</strong> Reset password with this SQL:</p>";
            $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
            echo "<pre>UPDATE users SET password_hash = '{$newHash}' WHERE email = '{$testEmail}';</pre>";
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

// Step 6: Test API endpoint
echo "<h2>Step 6: API Endpoint Test</h2>";
$apiUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/api/auth.php";
echo "<p>API URL: <code>{$apiUrl}</code></p>";
echo "<p>Try accessing: <a href='{$apiUrl}' target='_blank'>{$apiUrl}</a></p>";
echo "<p>(Should show an error message - that's normal, it means the API is accessible)</p>";

// Step 7: Session test
echo "<h2>Step 7: PHP Session Test</h2>";
session_start();
$_SESSION['test'] = 'working';
if (isset($_SESSION['test'])) {
    echo "<p class='success'>✅ PHP sessions are working</p>";
    echo "<p>Session save path: " . session_save_path() . "</p>";
} else {
    echo "<p class='error'>❌ Sessions not working</p>";
}

echo "<hr>";
echo "<h2>Summary & Next Steps</h2>";
echo "<p>If you see any ❌ above, use the FIX commands provided.</p>";
echo "<p><strong>Delete this file after fixing for security!</strong></p>";
?>
