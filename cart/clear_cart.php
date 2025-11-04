<?php
require_once '../includes/functions.php';
startSession();

// Require user to be logged in and validate AJAX request
requireLogin();

if (!isAjaxRequest() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
}

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

// Clear all items from cart
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Update session cart count
    $_SESSION['cart_count'] = 0;

    jsonResponse([
        'success' => true,
        'message' => 'Cart cleared successfully',
        'cart_count' => 0
    ]);
} else {
    jsonResponse(['success' => false, 'message' => 'Error clearing cart'], 500);
}
?>
