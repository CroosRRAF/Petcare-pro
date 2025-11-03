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
    <title>Pet Care Tools</title>
    <link rel="stylesheet" href="../styles/products.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>
    <h1>Pet Care Tools</h1>

    <div class="product-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-item">
                    <img src="../assets/images/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p>Price: $<?= number_format($row['price'], 2) ?></p>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <a href="../user/add_to_cart.php?product_id=<?= urlencode($row['id']) ?>" class="buy-now-btn">Buy Now</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No tools available.</p>
        <?php endif; ?>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>
</html>
