<?php
require_once '../includes/functions.php';
startSession();

// Require user to be logged in
requireLogin();

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

// Get cart items with product details
$query = "SELECT c.*, p.name, p.price, p.image_url, p.description
          FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = ?
          ORDER BY c.added_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate totals
$subtotal = 0;
$total_items = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $total_items += $item['quantity'];
}

// Calculate shipping (free for orders over ₱2000)
$shipping = $subtotal >= 2000 ? 0 : 150;
$total = $subtotal + $shipping;

// Update session cart count
$_SESSION['cart_count'] = $total_items;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Petcare Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/cart.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="cart-page">
        <!-- Page Header -->
        <section class="page-header">
            <div class="container">
                <div class="header-content">
                    <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
                    <nav class="breadcrumb">
                        <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                        <span>/</span>
                        <span>Cart</span>
                    </nav>
                </div>
            </div>
        </section>

        <div class="container">
            <?php if (empty($cart_items)): ?>
                <!-- Empty Cart -->
                <div class="empty-cart">
                    <div class="empty-cart-content">
                        <i class="fas fa-shopping-cart fa-4x"></i>
                        <h2>Your cart is empty</h2>
                        <p>Looks like you haven't added any products to your cart yet.</p>
                        <a href="../products/index.php" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Start Shopping
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Cart Content -->
                <div class="cart-layout">
                    <!-- Cart Items -->
                    <div class="cart-items">
                        <div class="cart-header">
                            <h2>Your Items (<?= $total_items ?> <?= $total_items === 1 ? 'item' : 'items' ?>)</h2>
                            <button class="btn btn-outline btn-clear-cart" onclick="clearCart()">
                                <i class="fas fa-trash"></i> Clear Cart
                            </button>
                        </div>

                        <div class="cart-items-list">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="cart-item" data-product-id="<?= $item['product_id'] ?>">
                                    <div class="item-image">
                                        <img src="../assets/images/<?= sanitize($item['image_url']) ?>"
                                             alt="<?= sanitize($item['name']) ?>"
                                             onerror="this.src='../assets/images/placeholder-product.jpg'">
                                    </div>

                                    <div class="item-details">
                                        <h3 class="item-name">
                                            <a href="../products/product_detail.php?id=<?= $item['product_id'] ?>">
                                                <?= sanitize($item['name']) ?>
                                            </a>
                                        </h3>
                                        <p class="item-description">
                                            <?= sanitize(substr($item['description'], 0, 100)) ?>
                                            <?= strlen($item['description']) > 100 ? '...' : '' ?>
                                        </p>
                                        <div class="item-price">
                                            <?= formatPrice($item['price']) ?> each
                                        </div>
                                    </div>

                                    <div class="item-quantity">
                                        <label for="qty-<?= $item['product_id'] ?>">Quantity:</label>
                                        <div class="quantity-controls">
                                            <button class="qty-btn" onclick="updateQuantity(<?= $item['product_id'] ?>, <?= $item['quantity'] - 1 ?>)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number"
                                                   id="qty-<?= $item['product_id'] ?>"
                                                   class="quantity-input"
                                                   value="<?= $item['quantity'] ?>"
                                                   min="1"
                                                   max="10"
                                                   onchange="updateQuantity(<?= $item['product_id'] ?>, this.value)">
                                            <button class="qty-btn" onclick="updateQuantity(<?= $item['product_id'] ?>, <?= $item['quantity'] + 1 ?>)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="item-total">
                                        <div class="item-total-price">
                                            <?= formatPrice($item['price'] * $item['quantity']) ?>
                                        </div>
                                        <button class="btn-remove-item"
                                                onclick="removeItem(<?= $item['product_id'] ?>)"
                                                title="Remove from cart">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Continue Shopping -->
                        <div class="continue-shopping">
                            <a href="../products/index.php" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </div>

                    <!-- Cart Summary -->
                    <div class="cart-summary">
                        <div class="summary-card">
                            <h3><i class="fas fa-calculator"></i> Order Summary</h3>

                            <div class="summary-details">
                                <div class="summary-row">
                                    <span>Subtotal (<?= $total_items ?> items):</span>
                                    <span class="price"><?= formatPrice($subtotal) ?></span>
                                </div>

                                <div class="summary-row">
                                    <span>Shipping:</span>
                                    <span class="price">
                                        <?php if ($shipping === 0): ?>
                                            <span class="free-shipping">FREE</span>
                                        <?php else: ?>
                                            <?= formatPrice($shipping) ?>
                                        <?php endif; ?>
                                    </span>
                                </div>

                                <?php if ($subtotal < 2000 && $subtotal > 0): ?>
                                    <div class="shipping-notice">
                                        <i class="fas fa-info-circle"></i>
                                        Add <?= formatPrice(2000 - $subtotal) ?> more for free shipping!
                                    </div>
                                <?php endif; ?>

                                <hr>

                                <div class="summary-row total-row">
                                    <span><strong>Total:</strong></span>
                                    <span class="price"><strong><?= formatPrice($total) ?></strong></span>
                                </div>
                            </div>

                            <div class="checkout-actions">
                                <a href="checkout.php" class="btn btn-primary btn-checkout">
                                    <i class="fas fa-credit-card"></i> Proceed to Checkout
                                </a>

                                <div class="payment-icons">
                                    <i class="fab fa-cc-visa" title="Visa"></i>
                                    <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                                    <i class="fab fa-paypal" title="PayPal"></i>
                                    <i class="fas fa-mobile-alt" title="Mobile Payment"></i>
                                </div>

                                <div class="security-note">
                                    <i class="fas fa-shield-alt"></i>
                                    <small>Your payment information is secure and encrypted</small>
                                </div>
                            </div>
                        </div>

                        <!-- Recommended Products -->
                        <?php
                        // Get recommended products (simple recommendation based on categories in cart)
                        $cart_categories = array_unique(array_column($cart_items, 'category'));
                        if (!empty($cart_categories)) {
                            $placeholders = str_repeat('?,', count($cart_categories) - 1) . '?';
                            $recommend_query = "SELECT * FROM products
                                              WHERE category IN ($placeholders)
                                              AND id NOT IN (SELECT product_id FROM cart WHERE user_id = ?)
                                              ORDER BY RAND() LIMIT 3";

                            $stmt = $conn->prepare($recommend_query);
                            $params = array_merge($cart_categories, [$user_id]);
                            $types = str_repeat('s', count($cart_categories)) . 'i';
                            $stmt->bind_param($types, ...$params);
                            $stmt->execute();
                            $recommended = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                            if (!empty($recommended)): ?>
                                <div class="recommended-products">
                                    <h4><i class="fas fa-heart"></i> You might also like</h4>
                                    <div class="recommended-list">
                                        <?php foreach ($recommended as $product): ?>
                                            <div class="recommended-item">
                                                <img src="../assets/images/<?= sanitize($product['image_url']) ?>"
                                                     alt="<?= sanitize($product['name']) ?>"
                                                     onerror="this.src='../assets/images/placeholder-product.jpg'">
                                                <div class="recommended-info">
                                                    <h5><?= sanitize($product['name']) ?></h5>
                                                    <div class="price"><?= formatPrice($product['price']) ?></div>
                                                    <button class="btn btn-sm btn-primary"
                                                            onclick="addToCart(<?= $product['id'] ?>)">
                                                        <i class="fas fa-plus"></i> Add
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif;
                        } ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Update item quantity
        async function updateQuantity(productId, newQuantity) {
            if (newQuantity < 1 || newQuantity > 10) {
                showNotification('Quantity must be between 1 and 10', 'error');
                return;
            }

            try {
                const response = await fetch('update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=${newQuantity}`
                });

                const result = await response.json();

                if (result.success) {
                    location.reload(); // Reload to update totals
                } else {
                    showNotification(result.message || 'Error updating quantity', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error updating quantity', 'error');
            }
        }

        // Remove item from cart
        async function removeItem(productId) {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }

            try {
                const response = await fetch('remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}`
                });

                const result = await response.json();

                if (result.success) {
                    location.reload(); // Reload to update cart
                } else {
                    showNotification(result.message || 'Error removing item', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error removing item', 'error');
            }
        }

        // Clear entire cart
        async function clearCart() {
            if (!confirm('Are you sure you want to clear your entire cart?')) {
                return;
            }

            try {
                const response = await fetch('clear_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: ''
                });

                const result = await response.json();

                if (result.success) {
                    location.reload(); // Reload to show empty cart
                } else {
                    showNotification(result.message || 'Error clearing cart', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error clearing cart', 'error');
            }
        }

        // Add recommended product to cart
        async function addToCart(productId) {
            try {
                const response = await fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=1`
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Product added to cart!', 'success');
                    // Update cart count in header
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.textContent = result.cart_count;
                        cartCount.style.display = result.cart_count > 0 ? 'inline' : 'none';
                    }
                } else {
                    showNotification(result.message || 'Error adding to cart', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error adding to cart', 'error');
            }
        }

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                ${message}
            `;

            document.body.appendChild(notification);

            // Show notification
            setTimeout(() => notification.classList.add('show'), 100);

            // Hide notification after 3 seconds
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
