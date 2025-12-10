<?php
/**
 * Stories API Endpoint
 * Handles CRUD operations for innovation stories
 */

require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        // Get all stories or single story
        if (isset($_GET['id'])) {
            // Get single story
            $id = (int)$_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM innovation_stories WHERE id = ?");
            $stmt->execute([$id]);
            $story = $stmt->fetch();
            
            if ($story) {
                // Increment view count
                $updateStmt = $pdo->prepare("UPDATE innovation_stories SET view_count = view_count + 1 WHERE id = ?");
                $updateStmt->execute([$id]);
                
                sendResponse($story);
            } else {
                sendError('Story not found', 404);
            }
        } else {
            // Get all stories
            $featured = isset($_GET['featured']) ? (int)$_GET['featured'] : null;
            $region = $_GET['region'] ?? null;
            $theme = $_GET['theme'] ?? null;
            
            $sql = "SELECT * FROM innovation_stories WHERE 1=1";
            $params = [];
            
            if ($featured !== null) {
                $sql .= " AND is_featured = ?";
                $params[] = $featured;
            }
            
            if ($region) {
                $sql .= " AND region = ?";
                $params[] = $region;
            }
            
            if ($theme) {
                $sql .= " AND theme = ?";
                $params[] = $theme;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $stories = $stmt->fetchAll();
            
            sendResponse($stories);
        }
        break;
        
    case 'POST':
        // Create new story (admin only)
        requireAuth();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['title', 'description', 'location', 'region', 'theme', 'innovator_name'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                sendError("Field '$field' is required");
            }
        }
        
        // Generate slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['title'])));
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Check if slug exists
        $checkStmt = $pdo->prepare("SELECT id FROM innovation_stories WHERE slug = ?");
        $checkStmt->execute([$slug]);
        if ($checkStmt->fetch()) {
            $slug .= '-' . time();
        }
        
        $sql = "INSERT INTO innovation_stories (title, slug, summary, description, location, region, theme, latitude, longitude, image_url, innovator_name, beneficiaries_count, contact_email, contact_info, is_featured) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $summary = substr($data['description'], 0, 200);
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['title'],
            $slug,
            $summary,
            $data['description'],
            $data['location'],
            $data['region'],
            $data['theme'],
            $data['latitude'] ?? null,
            $data['longitude'] ?? null,
            $data['image_url'] ?? null,
            $data['innovator_name'],
            $data['beneficiaries_count'] ?? 0,
            $data['contact_email'] ?? null,
            $data['contact_info'] ?? null,
            $data['is_featured'] ?? false
        ]);
        
        $id = $pdo->lastInsertId();
        sendResponse(['id' => $id, 'message' => 'Story created successfully'], 201);
        break;
        
    case 'PUT':
        // Update story (admin only)
        requireAuth();
        
        $id = (int)$_GET['id'];
        $data = json_decode(file_get_contents('php://input'), true);
        
        $sql = "UPDATE innovation_stories SET 
                title = ?, description = ?, location = ?, region = ?, theme = ?, 
                latitude = ?, longitude = ?, image_url = ?, innovator_name = ?, 
                beneficiaries_count = ?, contact_email = ?, contact_info = ?, 
                is_featured = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['title'] ?? null,
            $data['description'] ?? null,
            $data['location'] ?? null,
            $data['region'] ?? null,
            $data['theme'] ?? null,
            $data['latitude'] ?? null,
            $data['longitude'] ?? null,
            $data['image_url'] ?? null,
            $data['innovator_name'] ?? null,
            $data['beneficiaries_count'] ?? 0,
            $data['contact_email'] ?? null,
            $data['contact_info'] ?? null,
            $data['is_featured'] ?? false,
            $id
        ]);
        
        sendResponse(['message' => 'Story updated successfully']);
        break;
        
    case 'DELETE':
        // Delete story (admin only)
        requireAuth();
        
        $id = (int)$_GET['id'];
        $stmt = $pdo->prepare("DELETE FROM innovation_stories WHERE id = ?");
        $stmt->execute([$id]);
        
        sendResponse(['message' => 'Story deleted successfully']);
        break;
        
    default:
        sendError('Method not allowed', 405);
}

