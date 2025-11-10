<?php
require_once '../includes/functions.php';
startSession();

// Require user to be logged in and validate AJAX request
requireLogin();

if (!isAjaxRequest() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
}

$cart_id = intval($_POST['cart_id'] ?? 0);
$product_id = intval($_POST['product_id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($cart_id <= 0 && $product_id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Invalid cart item'], 400);
}

$conn = getDBConnection();

// Determine which parameter was provided and build the appropriate query
if ($cart_id > 0) {
    // Remove by cart ID (for modal, can be product or service)
    $stmt = $conn->prepare("SELECT id, item_type FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
} else {
    // Remove by product ID (for view_cart.php, products only)
    $stmt = $conn->prepare("SELECT id, item_type FROM cart WHERE product_id = ? AND user_id = ? AND item_type = 'product'");
    $stmt->bind_param("ii", $product_id, $user_id);
}

$stmt->execute();
$cart_item = $stmt->get_result()->fetch_assoc();

if (!$cart_item) {
    jsonResponse(['success' => false, 'message' => 'Item not found in cart'], 404);
}

// Remove item from cart
$stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_item['id'], $user_id);

if ($stmt->execute()) {
    // Update session cart count
    $_SESSION['cart_count'] = getCartCount();

    jsonResponse([
        'success' => true,
        'message' => ucfirst($cart_item['item_type']) . ' removed from cart',
        'cart_count' => $_SESSION['cart_count']
    ]);
} else {
    jsonResponse(['success' => false, 'message' => 'Error removing item from cart'], 500);
}
?>
