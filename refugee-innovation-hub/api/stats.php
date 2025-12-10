<?php
/**
 * Statistics API Endpoint
 * Returns dashboard statistics
 */

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    sendError('Method not allowed', 405);
}

requireAuth();

$pdo = getDBConnection();

// Get statistics
$stats = [];

// Published stories count
$stmt = $pdo->query("SELECT COUNT(*) as count FROM innovation_stories");
$stats['publishedStories'] = (int)$stmt->fetch()['count'];

// Total views
$stmt = $pdo->query("SELECT SUM(view_count) as total FROM innovation_stories");
$stats['totalViews'] = (int)($stmt->fetch()['total'] ?? 0);

// Pending submissions
$stmt = $pdo->query("SELECT COUNT(*) as count FROM story_submissions WHERE status = 'pending'");
$stats['pendingSubmissions'] = (int)$stmt->fetch()['count'];

// Total submissions
$stmt = $pdo->query("SELECT COUNT(*) as count FROM story_submissions");
$stats['totalSubmissions'] = (int)$stmt->fetch()['count'];

sendResponse($stats);

