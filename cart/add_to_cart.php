<?php
require_once '../includes/functions.php';
startSession();

// Require user to be logged in
requireLogin();

// Handle AJAX requests
if (isAjaxRequest()) {
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate CSRF token
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            jsonResponse(['success' => false, 'message' => 'Invalid security token'], 400);
        }

        $product_id = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        $user_id = $_SESSION['user_id'];

        if ($product_id <= 0) {
            jsonResponse(['success' => false, 'message' => 'Invalid product'], 400);
        }

        if ($quantity <= 0) {
            $quantity = 1;
        }

        if ($quantity > 10) {
            jsonResponse(['success' => false, 'message' => 'Maximum quantity is 10 per item'], 400);
        }

        $conn = getDBConnection();

        // Check if product exists
        $stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if (!$product) {
            jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
        }

        // Check if item already in cart
        $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            // Update existing cart item
            $new_quantity = $existing['quantity'] + $quantity;
            if ($new_quantity > 10) {
                jsonResponse(['success' => false, 'message' => 'Maximum quantity is 10 per item'], 400);
            }

            $stmt = $conn->prepare("UPDATE cart SET quantity = ?, added_at = NOW() WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        } else {
            // Add new cart item
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, added_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        }

        if ($stmt->execute()) {
            // Update session cart count
            $_SESSION['cart_count'] = getCartCount();

            jsonResponse([
                'success' => true,
                'message' => 'Product added to cart!',
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
