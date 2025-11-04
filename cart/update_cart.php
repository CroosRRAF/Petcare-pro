<?php
require_once '../includes/functions.php';
startSession();

// Require user to be logged in and validate AJAX request
requireLogin();

if (!isAjaxRequest() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
}

$product_id = intval($_POST['product_id'] ?? 0);
$new_quantity = intval($_POST['quantity'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($product_id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid product'], 400);
}

if ($new_quantity <= 0) {
    jsonResponse(['success' => false, 'message' => 'Quantity must be at least 1'], 400);
}

if ($new_quantity > 10) {
    jsonResponse(['success' => false, 'message' => 'Maximum quantity is 10 per item'], 400);
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

// Update quantity
$stmt = $conn->prepare("UPDATE cart SET quantity = ?, added_at = NOW() WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("iii", $new_quantity, $user_id, $product_id);

if ($stmt->execute()) {
    // Update session cart count
    $_SESSION['cart_count'] = getCartCount();

    // Get updated cart total
    $cart_total = getCartTotal();

    jsonResponse([
        'success' => true,
        'message' => 'Cart updated successfully',
        'cart_count' => $_SESSION['cart_count'],
        'cart_total' => formatPrice($cart_total)
    ]);
} else {
    jsonResponse(['success' => false, 'message' => 'Error updating cart'], 500);
}
?>
