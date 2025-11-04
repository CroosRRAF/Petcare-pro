<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../includes/functions.php";

$conn = getDBConnection();

// Fetch food products from the database
$query = "SELECT * FROM products WHERE category = 'Food'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Food Products | Petcare Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/products.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="products-container">
        <div class="products-header">
            <h1><i class="fas fa-bone"></i> Pet Food Products</h1>
        </div>

        <div class="products-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="../assets/images/<?= htmlspecialchars($row['image_url']) ?>"
                             alt="<?= htmlspecialchars($row['name']) ?>"
                             class="product-image">
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($row['name']) ?></h3>
                            <div class="product-price">$<?= number_format($row['price'], 2) ?></div>
                            <p class="product-description"><?= htmlspecialchars($row['description']) ?></p>
                            <div class="product-actions">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <button onclick="addToCart(<?= $row['id'] ?>)" class="btn btn-primary">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                <?php else: ?>
                                    <a href="../auth/login.php" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt"></i> Login to Buy
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No food products available.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>
    <script src="../scripts/cart.js"></script>
</body>
</html>
