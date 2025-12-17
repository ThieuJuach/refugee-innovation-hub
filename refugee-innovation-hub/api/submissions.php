<?php
/**
 * Submissions API Endpoint
 * Handles story submission operations
 */

require_once 'config.php';

// Set JSON header only if not handling file upload
if (!isset($_FILES['image'])) {
    header('Content-Type: application/json');
}

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        // Get submissions (admin only for pending, or all if authenticated)
        if (isAuthenticated()) {
            $status = $_GET['status'] ?? 'pending';
            $stmt = $pdo->prepare("SELECT * FROM story_submissions WHERE status = ? ORDER BY submitted_at DESC");
            $stmt->execute([$status]);
            $submissions = $stmt->fetchAll();
            sendResponse($submissions);
        } else {
            sendError('Authentication required', 401);
        }
        break;
        
    case 'POST':
        // Create new submission (public)
        // Handle both JSON and form-data
        if (!empty($_POST)) {
            // Form data submission (with file upload)
            $data = $_POST;
        } else {
            // JSON submission
            $data = json_decode(file_get_contents('php://input'), true);
        }
        
        // Validate required fields
        $required = ['title', 'description', 'location', 'region', 'theme', 'innovator_name', 'contact_email'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                sendError("Field '$field' is required");
            }
        }
        
        // Validate email
        if (!filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
            sendError('Invalid email address');
        }
        
        // Handle file upload if present
        $imageUrl = $data['image_url'] ?? null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Process file upload
            $file = $_FILES['image'];
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($file['tmp_name']);
            
            if (in_array($fileType, $allowedTypes)) {
                // Validate file size (max 5MB)
                $maxSize = 5 * 1024 * 1024; // 5MB
                if ($file['size'] <= $maxSize) {
                    // Create uploads directory if it doesn't exist
                    $uploadDir = __DIR__ . '/../uploads/stories/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    // Generate unique filename
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('story_', true) . '_' . time() . '.' . $extension;
                    $filepath = $uploadDir . $filename;
                    
                    // Move uploaded file
                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        $imageUrl = '/uploads/stories/' . $filename;
                    }
                }
            }
        }
        
        $sql = "INSERT INTO story_submissions (title, description, location, region, theme, innovator_name, impact, contact_email, contact_info, image_url, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['location'],
            $data['region'],
            $data['theme'],
            $data['innovator_name'],
            $data['impact'] ?? null,
            $data['contact_email'],
            $data['contact_info'] ?? null,
            $imageUrl
        ]);
        
        $id = $pdo->lastInsertId();
        
        // Track analytics
        try {
            $analyticsStmt = $pdo->prepare("INSERT INTO site_analytics (event_type, metadata) VALUES (?, ?)");
            $analyticsStmt->execute(['submission', json_encode(['submission_id' => $id, 'type' => 'story_submission'])]);
        } catch (Exception $e) {
            // Analytics tracking failed, but don't fail the submission
            error_log('Analytics tracking error: ' . $e->getMessage());
        }
        
        sendResponse(['id' => $id, 'message' => 'Submission received successfully'], 201);
        break;
        
    case 'PUT':
        // Update submission status (admin only)
        requireAuth();
        
        $id = (int)$_GET['id'];
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['status'])) {
            $stmt = $pdo->prepare("UPDATE story_submissions SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $id]);
            
            // If approved, create story
            if ($data['status'] === 'approved') {
                $submissionStmt = $pdo->prepare("SELECT * FROM story_submissions WHERE id = ?");
                $submissionStmt->execute([$id]);
                $submission = $submissionStmt->fetch();
                
                if ($submission) {
                    // Generate slug
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $submission['title'])));
                    $slug = preg_replace('/-+/', '-', $slug);
                    $slug = trim($slug, '-');
                    
                    // Check if slug exists
                    $checkStmt = $pdo->prepare("SELECT id FROM innovation_stories WHERE slug = ?");
                    $checkStmt->execute([$slug]);
                    if ($checkStmt->fetch()) {
                        $slug .= '-' . time();
                    }
                    
                    // Insert into stories
                    $summary = substr($submission['description'], 0, 200);
                    $insertStmt = $pdo->prepare("INSERT INTO innovation_stories (title, slug, summary, description, location, region, theme, image_url, innovator_name, impact, contact_email, contact_info, is_featured)
                                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertStmt->execute([
                        $submission['title'],
                        $slug,
                        $summary,
                        $submission['description'],
                        $submission['location'],
                        $submission['region'],
                        $submission['theme'],
                        $submission['image_url'],
                        $submission['innovator_name'],
                        $submission['impact'] ?? 'Making a positive impact in the community.',
                        $submission['contact_email'],
                        $submission['contact_info'],
                        false
                    ]);
                    
                    // Track analytics
                    try {
                        $storyId = $pdo->lastInsertId();
                        $analyticsStmt = $pdo->prepare("INSERT INTO site_analytics (event_type, story_id, metadata) VALUES (?, ?, ?)");
                        $analyticsStmt->execute(['submission_approved', $storyId, json_encode(['submission_id' => $id])]);
                    } catch (Exception $e) {
                        // Analytics tracking failed, but don't fail the approval
                        error_log('Analytics tracking error: ' . $e->getMessage());
                    }
                }
            }
            
            sendResponse(['message' => 'Submission updated successfully']);
        } else {
            sendError('Status is required');
        }
        break;
        
    default:
        sendError('Method not allowed', 405);
}

