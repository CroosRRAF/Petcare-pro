<?php
session_start();
require_once '../config/db_connect.php';

// Fetch products
$stmt = $conn->prepare("SELECT id, name, price, description, category, image_url FROM products");
$stmt->execute();
$products = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Petcare Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/products.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="products-container">
        <div class="products-header">
            <h1><i class="fas fa-box"></i> Our Products</h1>
        </div>

        <div class="products-grid">
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="../assets/images/products/<?php echo $product['image_url']; ?>"
                         alt="<?php echo $product['name']; ?>"
                         class="product-image">
                    <div class="product-info">
                        <span class="category"><?php echo htmlspecialchars($product['category']); ?></span>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                        <div class="product-actions">
                            <a href="../user/add_to_cart.php?product_id=<?php echo $product['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
