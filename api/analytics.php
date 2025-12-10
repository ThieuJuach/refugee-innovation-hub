<?php
/**
 * Analytics API Endpoint
 * Handles analytics tracking and retrieval
 */

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        // Get analytics (admin only)
        requireAuth();
        
        $eventType = $_GET['event_type'] ?? null;
        $storyId = $_GET['story_id'] ?? null;
        $limit = (int)($_GET['limit'] ?? 100);
        
        $sql = "SELECT * FROM site_analytics WHERE 1=1";
        $params = [];
        
        if ($eventType) {
            $sql .= " AND event_type = ?";
            $params[] = $eventType;
        }
        
        if ($storyId) {
            $sql .= " AND story_id = ?";
            $params[] = $storyId;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $analytics = $stmt->fetchAll();
        
        sendResponse($analytics);
        break;
        
    case 'POST':
        // Track analytics event (public)
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data['event_type'])) {
            sendError('event_type is required');
        }
        
        $sql = "INSERT INTO site_analytics (event_type, story_id, metadata) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['event_type'],
            $data['story_id'] ?? null,
            json_encode($data['metadata'] ?? [])
        ]);
        
        $id = $pdo->lastInsertId();
        sendResponse(['id' => $id, 'message' => 'Analytics tracked'], 201);
        break;
        
    default:
        sendError('Method not allowed', 405);
}

