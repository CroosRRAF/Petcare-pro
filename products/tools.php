<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../includes/functions.php";

$conn = getDBConnection();

// Fetch tool products from the database
$query = "SELECT * FROM products WHERE category = 'Tools'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Care Tools | Petcare Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/products.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="products-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1>
                    <i class="fas fa-tools"></i> Pet Care Tools
                </h1>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="products-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($product = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="card-image-container">
                            <img src="../assets/images/<?= htmlspecialchars($product['image_url']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                class="card-image"
                                onerror="this.src='../assets/images/placeholder-product.jpg'">
                            <div class="card-overlay">
                                <div class="overlay-content">
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <button onclick="addToCart(<?= $product['id'] ?>)" class="btn btn-success">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                    <?php else: ?>
                                        <a href="../auth/login.php" class="btn btn-success">
                                            <i class="fas fa-sign-in-alt"></i> Login to Buy
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-header">
                                <span class="badge badge-success">Tools</span>
                                <h3 class="card-title"><?= htmlspecialchars($product['name']) ?></h3>
                            </div>
                            <p class="card-description"><?= htmlspecialchars($product['description']) ?></p>
                            <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                            <div class="card-actions">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <button onclick="addToCart(<?= $product['id'] ?>)" class="btn btn-success">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                <?php else: ?>
                                    <a href="../auth/login.php" class="btn btn-success">
                                        <i class="fas fa-sign-in-alt"></i> Login to Buy
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-tools fa-4x"></i>
                    <h3>No tools available</h3>
                    <p>Please check back later for our pet care tools.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>

    <!-- Cart Modal -->
    <?php include '../includes/cart_modal.php'; ?>
</body>
</html>
