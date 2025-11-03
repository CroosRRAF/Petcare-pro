<?php
/**
 * Common Functions for Zyora PetCare
 * This file contains reusable functions used throughout the application
 */

/**
 * Database connection function
 */
function getDBConnection() {
    static $conn = null;

    if ($conn === null) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "petcare";
        $port = 3308; // XAMPP default port for MySQL

        try {
            $conn = new mysqli($servername, $username, $password, $dbname, $port);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Set charset to prevent encoding issues
            $conn->set_charset("utf8");

        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    return $conn;
}

/**
 * Start session if not already started
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    startSession();
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Require user to be logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('auth/login.php');
    }
}

/**
 * Require admin access
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('../index.php?error=access_denied');
    }
}

/**
 * Redirect function
 */
function redirect($url) {
    // Check if URL is relative or absolute
    if (!preg_match('/^https?:\/\//', $url)) {
        // If relative, make it relative to the site root
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $script_dir = dirname($_SERVER['SCRIPT_NAME']);

        // Clean up the path
        if ($script_dir !== '/') {
            $url = rtrim($script_dir, '/') . '/' . ltrim($url, '/');
        } else {
            $url = '/' . ltrim($url, '/');
        }

        $url = $protocol . '://' . $host . $url;
    }

    header("Location: " . $url);
    exit();
}

/**
 * Sanitize input data
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }

    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    // Minimum 8 characters, at least one uppercase, one lowercase, one number
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/', $password);
}

/**
 * Hash password securely
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    startSession();

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    startSession();

    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token HTML input
 */
function getCSRFInput() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Flash message functions
 */
function setFlash($type, $message) {
    startSession();
    $_SESSION['flash'][$type] = $message;
}

function getFlash($type) {
    startSession();

    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }

    return null;
}

function hasFlash($type) {
    startSession();
    return isset($_SESSION['flash'][$type]);
}

/**
 * Display flash messages
 */
function displayFlashes() {
    $types = ['success', 'error', 'warning', 'info'];

    foreach ($types as $type) {
        $message = getFlash($type);
        if ($message) {
            $icon = [
                'success' => 'fas fa-check-circle',
                'error' => 'fas fa-exclamation-circle',
                'warning' => 'fas fa-exclamation-triangle',
                'info' => 'fas fa-info-circle'
            ][$type];

            echo '<div class="alert alert-' . $type . '">';
            echo '<i class="' . $icon . '"></i> ' . sanitize($message);
            echo '</div>';
        }
    }
}

/**
 * Format price for display
 */
function formatPrice($price) {
    return '₱' . number_format($price, 2);
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

/**
 * Get cart count for current user
 */
function getCartCount() {
    if (!isLoggedIn()) {
        return 0;
    }

    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total'] ? intval($row['total']) : 0;
}

/**
 * Get cart total for current user
 */
function getCartTotal() {
    if (!isLoggedIn()) {
        return 0;
    }

    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];

    $query = "SELECT SUM(c.quantity * p.price) as total
              FROM cart c
              JOIN products p ON c.product_id = p.id
              WHERE c.user_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row['total'] ? floatval($row['total']) : 0;
}

/**
 * Log error message
 */
function logError($message, $file = null) {
    $log_dir = __DIR__ . '/../storage/logs/';

    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $log_file = $log_dir . 'error_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $file_info = $file ? " in $file" : '';
    $log_message = "[$timestamp] $message$file_info" . PHP_EOL;

    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
}

/**
 * Upload image file
 */
function uploadImage($file, $upload_dir = 'assets/images/uploads/') {
    // Create upload directory if it doesn't exist
    $full_upload_dir = __DIR__ . '/../' . $upload_dir;
    if (!is_dir($full_upload_dir)) {
        mkdir($full_upload_dir, 0755, true);
    }

    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }

    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }

    // Validate file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }

    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $file_path = $full_upload_dir . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return $upload_dir . $filename;
    }

    return false;
}

/**
 * Send email (basic implementation)
 */
function sendEmail($to, $subject, $message, $from = 'noreply@zyora-petcare.com') {
    $headers = [
        'From' => $from,
        'Reply-To' => $from,
        'Content-Type' => 'text/html; charset=UTF-8'
    ];

    $headers_string = '';
    foreach ($headers as $key => $value) {
        $headers_string .= "$key: $value\r\n";
    }

    return mail($to, $subject, $message, $headers_string);
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Check if request is AJAX
 */
function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Return JSON response
 */
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Pagination helper
 */
function paginate($total_records, $records_per_page = 10, $current_page = 1) {
    $total_pages = ceil($total_records / $records_per_page);
    $offset = ($current_page - 1) * $records_per_page;

    return [
        'total_records' => $total_records,
        'records_per_page' => $records_per_page,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'offset' => $offset,
        'has_previous' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}

/**
 * Generate pagination HTML
 */
function generatePaginationHTML($pagination, $base_url = '') {
    if ($pagination['total_pages'] <= 1) {
        return '';
    }

    $html = '<nav class="pagination-nav"><ul class="pagination">';

    // Previous button
    if ($pagination['has_previous']) {
        $prev_page = $pagination['current_page'] - 1;
        $html .= '<li><a href="' . $base_url . '?page=' . $prev_page . '" class="pagination-btn">Previous</a></li>';
    }

    // Page numbers
    $start_page = max(1, $pagination['current_page'] - 2);
    $end_page = min($pagination['total_pages'], $pagination['current_page'] + 2);

    for ($i = $start_page; $i <= $end_page; $i++) {
        $active_class = ($i == $pagination['current_page']) ? ' active' : '';
        $html .= '<li><a href="' . $base_url . '?page=' . $i . '" class="pagination-btn' . $active_class . '">' . $i . '</a></li>';
    }

    // Next button
    if ($pagination['has_next']) {
        $next_page = $pagination['current_page'] + 1;
        $html .= '<li><a href="' . $base_url . '?page=' . $next_page . '" class="pagination-btn">Next</a></li>';
    }

    $html .= '</ul></nav>';

    return $html;
}
?>
