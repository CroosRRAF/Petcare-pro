<?php
require_once '../includes/functions.php';
startSession();

// Get database connection
$conn = getDBConnection();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID");
}

$product_id = intval($_GET['id']);

// Fetch product details to verify its existence
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found");
}

$product = $result->fetch_assoc();

// Add product to the cart session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Store product ID and quantity in the cart
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity']++;
} else {
    $_SESSION['cart'][$product_id] = [
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => 1
    ];
}

$stmt->close();
$conn->close();

header("Location: view_cart.php");
exit;
?>
