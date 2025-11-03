<?php
require_once '../includes/functions.php';
startSession();

// Get database connection
$conn = getDBConnection();

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize cart array if not already set
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = $_SESSION['cart'];

// Calculate total amount
$totalAmount = 0;
foreach ($cartItems as $item) {
    $totalAmount += $item['price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/view_cart.css">
    <title>Your Cart</title>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <h1>Your Cart</h1>
    <div class="cart-container">
        <?php if (!empty($cartItems)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td><?= ucfirst($item['type']) ?></td>
                            <td>
                                <a href="remove_from_cart.php?type=<?= urlencode($item['type']) ?>&id=<?= urlencode($item[$item['type'] === 'products' ? 'product_id' : 'service_id']) ?>"
                                   class="remove-btn">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Display total payment amount -->
            <div class="total-amount">
                <h3>Total Amount: $<?= number_format($totalAmount, 2) ?></h3>
            </div>

        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
