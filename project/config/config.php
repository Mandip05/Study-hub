<?php
/**
 * Application Configuration
 * Study Hub LMS - Main Config
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Site Configuration
define('SITE_NAME', 'Study Hub');
define('SITE_URL', 'http://localhost/project');
define('SITE_DESCRIPTION', 'Modern Learning Management System');

// Directory Configuration
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('LIBRARY_PATH', ROOT_PATH . '/uploads/library');
define('ASSIGNMENT_PATH', ROOT_PATH . '/uploads/assignments');
define('CERTIFICATE_PATH', ROOT_PATH . '/uploads/certificates');

// File Upload Configuration
define('MAX_FILE_SIZE', 10485760); // 10MB
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'zip']);

// Security Configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Pagination
define('ITEMS_PER_PAGE', 12);

// QR Code Settings
define('QR_EXPIRY_TIME', 600); // 10 minutes

// Time Zone
date_default_timezone_set('Asia/Kathmandu');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database
require_once ROOT_PATH . '/config/database.php';

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Redirect based on role
 */
function redirectByRole($role) {
    switch($role) {
        case 'admin':
            header('Location: ' . SITE_URL . '/admin/dashboard.php');
            break;
        case 'teacher':
            header('Location: ' . SITE_URL . '/teacher/dashboard.php');
            break;
        case 'student':
            header('Location: ' . SITE_URL . '/student/dashboard.php');
            break;
        default:
            header('Location: ' . SITE_URL . '/index.php');
    }
    exit();
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/auth/login.php');
        exit();
    }
}

/**
 * Require specific role
 */
function requireRole($role) {
    requireLogin();
    if (getCurrentUserRole() !== $role) {
        header('Location: ' . SITE_URL . '/403.php');
        exit();
    }
}

/**
 * Sanitize input
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Format date
 */
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime($datetime) {
    return date('M d, Y h:i A', strtotime($datetime));
}

/**
 * Time ago function
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return 'just now';
    } elseif ($difference < 3600) {
        $minutes = floor($difference / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 86400) {
        $hours = floor($difference / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($difference < 604800) {
        $days = floor($difference / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return formatDate($datetime);
    }
}

/**
 * Log Activity
 */
function logActivity($userId, $action, $description = null) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $query = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                  VALUES (:user_id, :action, :description, :ip_address, :user_agent)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':user_id' => $userId,
            ':action' => $action,
            ':description' => $description,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Silently fail - don't break app if logging fails
        error_log('Activity logging failed: ' . $e->getMessage());
    }
}

/**
 * Send Notification
 */
function sendNotification($userId, $title, $message, $type = 'info') {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $query = "INSERT INTO notifications (user_id, title, message, type) 
                  VALUES (:user_id, :title, :message, :type)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':user_id' => $userId,
            ':title' => $title,
            ':message' => $message,
            ':type' => $type
        ]);
        return true;
    } catch (Exception $e) {
        error_log('Notification failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get unread notification count
 */
function getUnreadNotificationCount($userId) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $query = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = FALSE";
        $stmt = $conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Validate file upload
 */
function validateFileUpload($file, $allowedTypes = null, $maxSize = MAX_FILE_SIZE) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size exceeds limit'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = $allowedTypes ?? ALLOWED_FILE_TYPES;
    
    if (!in_array($extension, $allowedTypes)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    return ['success' => true, 'extension' => $extension];
}

/**
 * Generate unique filename
 */
function generateUniqueFilename($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Get or create translation
 */
function translate($key, $default = null) {
    $lang = $_SESSION['language'] ?? 'en';
    static $translations = [];
    
    if (empty($translations[$lang])) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            $query = "SELECT translation_key, translation_value FROM language_translations WHERE language_code = :lang";
            $stmt = $conn->prepare($query);
            $stmt->execute([':lang' => $lang]);
            $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            $translations[$lang] = $results;
        } catch (Exception $e) {
            $translations[$lang] = [];
        }
    }
    
    return $translations[$lang][$key] ?? $default ?? $key;
}
