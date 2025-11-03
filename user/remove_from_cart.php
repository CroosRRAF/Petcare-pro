<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$type = $_GET['type'] ?? null;
$id = $_GET['id'] ?? null;

if ($type && $id) {
    foreach ($_SESSION['cart'] as $index => $item) {
        $itemIdKey = ($type === 'products') ? 'product_id' : 'service_id';
        if ($item['type'] === $type && $item[$itemIdKey] == $id) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
            break;
        }
    }
}

header("Location: view_cart.php?message=" . urlencode("Item removed from cart."));
exit;
?>
