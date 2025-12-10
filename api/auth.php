<?php
/**
 * Authentication API Endpoint
 * Handles user login and session management
 */

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'POST':
        $action = $_GET['action'] ?? 'login';
        
        if ($action === 'login') {
            // Login
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['email']) || empty($data['password'])) {
                sendError('Email and password are required');
            }
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($data['password'], $user['password_hash'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Update last login
                $updateStmt = $pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                sendResponse([
                    'user' => [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'name' => $user['name'],
                        'role' => $user['role']
                    ],
                    'message' => 'Login successful'
                ]);
            } else {
                sendError('Invalid email or password', 401);
            }
        } elseif ($action === 'logout') {
            // Logout
            session_start();
            session_destroy();
            sendResponse(['message' => 'Logout successful']);
        } elseif ($action === 'check') {
            // Check authentication status
            session_start();
            if (isAuthenticated()) {
                $stmt = $pdo->prepare("SELECT id, email, name, role FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
                sendResponse(['authenticated' => true, 'user' => $user]);
            } else {
                sendResponse(['authenticated' => false]);
            }
        } else {
            sendError('Invalid action', 400);
        }
        break;
        
    default:
        sendError('Method not allowed', 405);
}

