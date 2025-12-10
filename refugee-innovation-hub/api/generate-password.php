<?php
/**
 * Password Hash Generator
 * Use this to generate password hashes for admin users
 * 
 * Usage: http://localhost/refugee-innovation-hub/api/generate-password.php?password=yourpassword
 */

if (isset($_GET['password'])) {
    $password = $_GET['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<h2>Password Hash Generator</h2>";
    echo "<p><strong>Password:</strong> " . htmlspecialchars($password) . "</p>";
    echo "<p><strong>Hash:</strong> <code>" . htmlspecialchars($hash) . "</code></p>";
    echo "<p>Copy the hash above and paste it into the users table in phpMyAdmin.</p>";
} else {
    echo "<h2>Password Hash Generator</h2>";
    echo "<p>Usage: Add ?password=yourpassword to the URL</p>";
    echo "<p>Example: <code>?password=admin123</code></p>";
}

