<?php
// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include_once '../config/db_connect.php';

header('Content-Type: application/json');

// Initialize cart count
$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    // Get cart count from database for logged-in users
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cart_count = $row['total_items'] ? (int)$row['total_items'] : 0;
    }
} else {
    // Get cart count from session for guest users
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += (int)$item['quantity'];
        }
    }
}

// Update session cart count
$_SESSION['cart_count'] = $cart_count;

echo json_encode([
    'count' => $cart_count,
    'success' => true
]);
?>
