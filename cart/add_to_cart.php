<?php
require_once '../includes/functions.php';
startSession();

// Require user to be logged in
requireLogin();

// Handle AJAX requests
if (isAjaxRequest()) {
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
        $service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : null;
        $quantity = intval($_POST['quantity'] ?? 1);
        $user_id = $_SESSION['user_id'];

        // Validate that either product_id or service_id is provided
        if (!$product_id && !$service_id) {
            jsonResponse(['success' => false, 'message' => 'No product or service selected'], 400);
        }

        if ($product_id && $service_id) {
            jsonResponse(['success' => false, 'message' => 'Cannot add both product and service at once'], 400);
        }

        if ($quantity <= 0) {
            $quantity = 1;
        }

        if ($quantity > 10) {
            jsonResponse(['success' => false, 'message' => 'Maximum quantity is 10 per item'], 400);
        }

        $conn = getDBConnection();
        $item_type = $product_id ? 'product' : 'service';
        $item_id = $product_id ? $product_id : $service_id;

        // Check if item exists
        if ($item_type === 'product') {
            $stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id = ?");
        } else {
            $stmt = $conn->prepare("SELECT id, name, 0 as price FROM services WHERE id = ?");
        }

        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();

        if (!$item) {
            jsonResponse(['success' => false, 'message' => ucfirst($item_type) . ' not found'], 404);
        }

        // Check if item already in cart
        if ($item_type === 'product') {
            $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ? AND item_type = 'product'");
            $stmt->bind_param("ii", $user_id, $item_id);
        } else {
            $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND service_id = ? AND item_type = 'service'");
            $stmt->bind_param("ii", $user_id, $item_id);
        }

        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            // Update existing cart item
            $new_quantity = $existing['quantity'] + $quantity;
            if ($new_quantity > 10) {
                jsonResponse(['success' => false, 'message' => 'Maximum quantity is 10 per item'], 400);
            }

            if ($item_type === 'product') {
                $stmt = $conn->prepare("UPDATE cart SET quantity = ?, added_at = NOW() WHERE user_id = ? AND product_id = ? AND item_type = 'product'");
                $stmt->bind_param("iii", $new_quantity, $user_id, $item_id);
            } else {
                $stmt = $conn->prepare("UPDATE cart SET quantity = ?, added_at = NOW() WHERE user_id = ? AND service_id = ? AND item_type = 'service'");
                $stmt->bind_param("iii", $new_quantity, $user_id, $item_id);
            }
        } else {
            // Add new cart item
            if ($item_type === 'product') {
                $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, service_id, item_type, quantity, added_at) VALUES (?, ?, NULL, 'product', ?, NOW())");
                $stmt->bind_param("iii", $user_id, $item_id, $quantity);
            } else {
                $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, service_id, item_type, quantity, added_at) VALUES (?, NULL, ?, 'service', ?, NOW())");
                $stmt->bind_param("iii", $user_id, $item_id, $quantity);
            }
        }

        if ($stmt->execute()) {
            // Update session cart count
            $_SESSION['cart_count'] = getCartCount();

            jsonResponse([
                'success' => true,
                'message' => ucfirst($item_type) . ' added to cart!',
                'cart_count' => $_SESSION['cart_count']
            ]);
        } else {
            jsonResponse(['success' => false, 'message' => 'Error adding to cart'], 500);
        }
    }
}

// If not AJAX, redirect to cart view
redirect('view_cart.php');
?>
