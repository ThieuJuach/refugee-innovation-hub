<?php
/**
 * Verification Script - Check Production Changes
 * Run this file to verify all changes have been applied correctly
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Production Readiness Check</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .check {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pass {
            border-left: 4px solid #22c55e;
        }
        .fail {
            border-left: 4px solid #ef4444;
        }
        .warning {
            border-left: 4px solid #f59e0b;
        }
        h1 {
            color: #1e293b;
        }
        h2 {
            color: #475569;
            margin-top: 0;
        }
        pre {
            background: #f8fafc;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            border: 1px solid #e2e8f0;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
        }
        .status-pass { background: #dcfce7; color: #166534; }
        .status-fail { background: #fee2e2; color: #991b1b; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <h1>🔍 Production Readiness Check</h1>
    <p>This script verifies all production changes have been applied correctly.</p>

<?php

$checks = [];

// Check 1: Verify app.js doesn't have sample stories
echo "<div class='check ";
$appJsPath = __DIR__ . '/js/app.js';
$appJsContent = file_get_contents($appJsPath);
$hasSampleStories = strpos($appJsContent, 'getSampleStories') !== false;

if (!$hasSampleStories) {
    echo "pass'>";
    echo "<h2><span class='status status-pass'>✓ PASS</span> Sample Stories Removed</h2>";
    echo "<p>The main <code>js/app.js</code> file no longer contains hardcoded sample stories.</p>";
    $checks['sample_stories'] = true;
} else {
    echo "fail'>";
    echo "<h2><span class='status status-fail'>✗ FAIL</span> Sample Stories Still Present</h2>";
    echo "<p>The main <code>js/app.js</code> file still contains the <code>getSampleStories()</code> function.</p>";
    echo "<div class='info'><strong>Action Required:</strong> Clear your browser cache completely (Ctrl+Shift+Delete) and hard refresh (Ctrl+Shift+R).</div>";
    $checks['sample_stories'] = false;
}
echo "</div>";

// Check 2: Verify submissions.php doesn't have default images
echo "<div class='check ";
$submissionsPath = __DIR__ . '/api/submissions.php';
$submissionsContent = file_get_contents($submissionsPath);
$hasDefaultImage = stripos($submissionsContent, 'unsplash') !== false;

if (!$hasDefaultImage) {
    echo "pass'>";
    echo "<h2><span class='status status-pass'>✓ PASS</span> No Auto-Generated Images</h2>";
    echo "<p>The <code>api/submissions.php</code> file no longer has Unsplash fallback images.</p>";
    $checks['default_images'] = true;
} else {
    echo "fail'>";
    echo "<h2><span class='status status-fail'>✗ FAIL</span> Default Images Still Present</h2>";
    echo "<p>The <code>api/submissions.php</code> file still contains references to Unsplash images.</p>";
    $checks['default_images'] = false;
}
echo "</div>";

// Check 3: Verify stories.php has update endpoint
echo "<div class='check ";
$storiesPath = __DIR__ . '/api/stories.php';
$storiesContent = file_get_contents($storiesPath);
$hasUpdateEndpoint = strpos($storiesContent, "case 'PUT':") !== false;
$hasLatLong = strpos($storiesContent, 'latitude = ?, longitude = ?') !== false;

if ($hasUpdateEndpoint && $hasLatLong) {
    echo "pass'>";
    echo "<h2><span class='status status-pass'>✓ PASS</span> Story Update Endpoint Available</h2>";
    echo "<p>The <code>api/stories.php</code> file has a PUT endpoint that supports updating coordinates.</p>";
    $checks['update_endpoint'] = true;
} else {
    echo "fail'>";
    echo "<h2><span class='status status-fail'>✗ FAIL</span> Story Update Endpoint Missing</h2>";
    echo "<p>The <code>api/stories.php</code> file is missing the update functionality.</p>";
    $checks['update_endpoint'] = false;
}
echo "</div>";

// Check 4: Database connection
echo "<div class='check ";
try {
    require_once __DIR__ . '/api/config.php';
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "pass'>";
    echo "<h2><span class='status status-pass'>✓ PASS</span> Database Connected</h2>";
    echo "<p>Successfully connected to database: <strong>" . DB_NAME . "</strong></p>";

    // Check story count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM innovation_stories");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $storyCount = $result['count'];

    echo "<p>Current stories in database: <strong>" . $storyCount . "</strong></p>";

    if ($storyCount > 0) {
        $stmt = $pdo->query("SELECT id, title, latitude, longitude FROM innovation_stories LIMIT 5");
        $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<h3>Sample Stories:</h3>";
        echo "<table style='width:100%; border-collapse: collapse;'>";
        echo "<tr style='background:#f1f5f9;'><th style='text-align:left; padding:8px;'>ID</th><th style='text-align:left; padding:8px;'>Title</th><th style='text-align:left; padding:8px;'>Coordinates</th></tr>";

        foreach ($stories as $story) {
            $coords = ($story['latitude'] && $story['longitude'])
                ? "{$story['latitude']}, {$story['longitude']}"
                : "<span style='color:#ef4444;'>❌ Missing</span>";

            echo "<tr style='border-bottom: 1px solid #e2e8f0;'>";
            echo "<td style='padding:8px;'>{$story['id']}</td>";
            echo "<td style='padding:8px;'>{$story['title']}</td>";
            echo "<td style='padding:8px;'>{$coords}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    $checks['database'] = true;
} catch (Exception $e) {
    echo "fail'>";
    echo "<h2><span class='status status-fail'>✗ FAIL</span> Database Connection Error</h2>";
    echo "<p>Could not connect to database: " . htmlspecialchars($e->getMessage()) . "</p>";
    $checks['database'] = false;
}
echo "</div>";

// Check 5: Edit Location functionality in JavaScript
echo "<div class='check ";
$hasEditLocation = strpos($appJsContent, 'handleEditStory') !== false;
$hasEditForm = strpos($appJsContent, 'editLocationForm') !== false;

if ($hasEditLocation && $hasEditForm) {
    echo "pass'>";
    echo "<h2><span class='status status-pass'>✓ PASS</span> Edit Location Feature Available</h2>";
    echo "<p>The JavaScript includes the <code>handleEditStory()</code> function and edit location form.</p>";
    $checks['edit_location'] = true;
} else {
    echo "fail'>";
    echo "<h2><span class='status status-fail'>✗ FAIL</span> Edit Location Feature Missing</h2>";
    echo "<p>The JavaScript is missing edit location functionality.</p>";
    $checks['edit_location'] = false;
}
echo "</div>";

// Check 6: File structure
echo "<div class='check pass'>";
echo "<h2><span class='status status-pass'>✓ INFO</span> File Structure</h2>";
echo "<p>Current working directory: <code>" . __DIR__ . "</code></p>";

$importantFiles = [
    'index.html' => file_exists(__DIR__ . '/index.html'),
    'js/app.js' => file_exists(__DIR__ . '/js/app.js'),
    'api/config.php' => file_exists(__DIR__ . '/api/config.php'),
    'api/stories.php' => file_exists(__DIR__ . '/api/stories.php'),
    'api/submissions.php' => file_exists(__DIR__ . '/api/submissions.php'),
    'css/styles.css' => file_exists(__DIR__ . '/css/styles.css'),
];

echo "<table style='width:100%; border-collapse: collapse;'>";
foreach ($importantFiles as $file => $exists) {
    $status = $exists ? "<span style='color:#22c55e;'>✓ Exists</span>" : "<span style='color:#ef4444;'>✗ Missing</span>";
    echo "<tr style='border-bottom: 1px solid #e2e8f0;'>";
    echo "<td style='padding:8px;'><code>{$file}</code></td>";
    echo "<td style='padding:8px; text-align:right;'>{$status}</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// Summary
$passCount = count(array_filter($checks));
$totalCount = count($checks);

echo "<div class='check ";
if ($passCount === $totalCount) {
    echo "pass'>";
    echo "<h2><span class='status status-pass'>✓ ALL CHECKS PASSED</span></h2>";
    echo "<p>Your application is production-ready! All {$totalCount} checks passed successfully.</p>";
} else {
    echo "warning'>";
    echo "<h2><span class='status status-warning'>⚠ ISSUES FOUND</span></h2>";
    echo "<p>Passed: {$passCount} / {$totalCount} checks</p>";
    echo "<div class='info'>";
    echo "<h3>Troubleshooting Steps:</h3>";
    echo "<ol>";
    echo "<li><strong>Clear Browser Cache:</strong> Press Ctrl+Shift+Delete, select 'All Time', check 'Cached images and files', then click 'Clear data'</li>";
    echo "<li><strong>Hard Refresh:</strong> Press Ctrl+Shift+R (or Cmd+Shift+R on Mac) to force reload</li>";
    echo "<li><strong>Check File Path:</strong> Make sure you're accessing the correct folder in XAMPP (should be in htdocs)</li>";
    echo "<li><strong>Verify XAMPP:</strong> Ensure Apache and MySQL are running</li>";
    echo "<li><strong>Check Console:</strong> Press F12 and look for JavaScript errors in the Console tab</li>";
    echo "</ol>";
    echo "</div>";
}
echo "</div>";

?>

<div class="check pass">
    <h2>📋 Next Steps</h2>
    <ol>
        <li><strong>Clear Cache:</strong> Use Ctrl+Shift+Delete in your browser</li>
        <li><strong>Hard Refresh:</strong> Use Ctrl+Shift+R to force reload the page</li>
        <li><strong>Test Features:</strong>
            <ul>
                <li>Login as admin</li>
                <li>Go to Dashboard</li>
                <li>Check if "Edit Location" button appears</li>
                <li>Try updating coordinates for a story</li>
                <li>Submit a new story and verify no auto-generated images</li>
            </ul>
        </li>
        <li><strong>Access App:</strong> <a href="index.html">Open Main Application</a></li>
    </ol>
</div>

<div class="info">
    <strong>⏰ Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?><br>
    <strong>🖥 Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?><br>
    <strong>🐘 PHP Version:</strong> <?php echo phpversion(); ?>
</div>

</body>
</html>
