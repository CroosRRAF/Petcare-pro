<?php
require_once '../includes/functions.php';
startSession();

// Require user to be logged in and validate AJAX request
requireLogin();

if (!isAjaxRequest() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
}

$product_id = intval($_POST['product_id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($product_id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid product'], 400);
}

$conn = getDBConnection();

// Check if item exists in cart
$stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$cart_item = $stmt->get_result()->fetch_assoc();

if (!$cart_item) {
    jsonResponse(['success' => false, 'message' => 'Item not found in cart'], 404);
}

// Remove item from cart
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);

if ($stmt->execute()) {
    // Update session cart count
    $_SESSION['cart_count'] = getCartCount();

    // Get updated cart total
    $cart_total = getCartTotal();

    jsonResponse([
        'success' => true,
        'message' => 'Item removed from cart',
        'cart_count' => $_SESSION['cart_count'],
        'cart_total' => formatPrice($cart_total)
    ]);
} else {
    jsonResponse(['success' => false, 'message' => 'Error removing item from cart'], 500);
}
?>
