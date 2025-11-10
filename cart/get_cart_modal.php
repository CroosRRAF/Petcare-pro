<?php
require_once '../includes/functions.php';
startSession();

$user_id = $_SESSION['user_id'] ?? null;
$conn = getDBConnection();

if (!$user_id) {
    echo '<div class="text-center py-4">
        <i class="fas fa-sign-in-alt fa-3x text-muted mb-3"></i>
        <h5>Please log in to view your cart</h5>
        <a href="../auth/login.php" class="btn btn-primary">Login</a>
    </div>';
    exit;
}

// Get cart items with product/service details
$query = "SELECT c.*, p.name, p.price, p.image_url, p.description, 'product' as type
          FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = ?
          UNION
          SELECT c.*, s.name, 0 as price, s.image_url, s.description, 'service' as type
          FROM cart c
          JOIN services s ON c.service_id = s.id
          WHERE c.user_id = ?
          ORDER BY added_at DESC LIMIT 5";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate totals
$subtotal = 0;
$total_items = 0;
$has_services = false;
foreach ($cart_items as $item) {
    if ($item['type'] === 'product') {
        $subtotal += $item['price'] * $item['quantity'];
    } elseif ($item['type'] === 'service') {
        $has_services = true;
    }
    $total_items += $item['quantity'];
}
?>

<?php if (empty($cart_items)): ?>
    <div class="cart-empty">
        <i class="fas fa-shopping-cart fa-3x"></i>
        <h5>Your cart is empty</h5>
        <p>Add some products or services to get started!</p>
        <div class="cart-empty-actions">
            <a href="../products/foods.php" class="btn btn-primary btn-sm">Browse Products</a>
            <a href="../services/pet_grooming.php" class="btn btn-outline btn-sm">Browse Services</a>
        </div>
    </div>
<?php else: ?>
    <div class="cart-items-list">
        <?php foreach ($cart_items as $item): ?>
            <div class="cart-item-modal">
                <img src="../assets/images/<?= $item['type'] === 'product' ? 'products' : 'services' ?>/<?= sanitize($item['image_url']) ?>"
                     alt="<?= sanitize($item['name']) ?>"
                     class="cart-item-image">

                <div class="cart-item-details">
                    <h6 class="cart-item-name"><?= sanitize($item['name']) ?></h6>
                    <div class="cart-item-meta">
                        <?php if ($item['type'] === 'service'): ?>
                            <span class="price-on-request">Price on request</span>
                        <?php else: ?>
                            <?= formatPrice($item['price']) ?> × <?= $item['quantity'] ?>
                        <?php endif; ?>
                        <span class="badge badge-<?= $item['type'] === 'product' ? 'success' : 'primary' ?>">
                            <?= ucfirst($item['type']) ?>
                        </span>
                    </div>
                </div>

                <div class="cart-item-actions">
                    <div class="cart-item-price">
                        <?php if ($item['type'] === 'service'): ?>
                            <span class="contact-pricing">Contact us</span>
                        <?php else: ?>
                            <?= formatPrice($item['price'] * $item['quantity']) ?>
                        <?php endif; ?>
                    </div>
                    <button class="btn btn-sm btn-danger"
                            onclick="removeFromCartModal(<?= $item['id'] ?>, '<?= $item['type'] ?>')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (count($cart_items) >= 5): ?>
            <div class="cart-more-items">
                <small>Showing 5 most recent items</small>
            </div>
        <?php endif; ?>
    </div>

    <div class="cart-divider"></div>

    <div class="cart-summary-modal">
        <div class="summary-row">
            <span>Subtotal (<?= $total_items ?> items):</span>
            <strong>
                <?php if ($has_services): ?>
                    <?= formatPrice($subtotal) ?> + Services (Price on request)
                <?php else: ?>
                    <?= formatPrice($subtotal) ?>
                <?php endif; ?>
            </strong>
        </div>

        <?php if (!$has_services): ?>
            <div class="summary-row">
                <span>Shipping:</span>
                <strong class="<?= $subtotal >= 2000 ? 'text-success' : '' ?>">
                    <?= $subtotal >= 2000 ? 'FREE' : formatPrice(150) ?>
                </strong>
            </div>

            <div class="cart-divider"></div>

            <div class="summary-row total-row">
                <span>Total:</span>
                <span class="total-price">
                    <?php
                    $shipping = $subtotal >= 2000 ? 0 : 150;
                    $total = $subtotal + $shipping;
                    echo formatPrice($total);
                    ?>
                </span>
            </div>

            <?php if ($subtotal < 2000): ?>
                <div class="shipping-notice">
                    <i class="fas fa-info-circle"></i>
                    Add <?= formatPrice(2000 - $subtotal) ?> more for free shipping!
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="summary-row">
                <span>Shipping:</span>
                <strong>Contact for pricing</strong>
            </div>

            <div class="cart-divider"></div>

            <div class="summary-row total-row">
                <span>Total:</span>
                <span class="total-price">Price on request</span>
            </div>

            <div class="service-notice">
                <i class="fas fa-info-circle"></i>
                Service pricing will be provided upon booking confirmation.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<style>
/* Cart Modal Content Styles */
.cart-empty {
    text-align: center;
    padding: 40px 20px;
}

.cart-empty i {
    color: #ccc;
    margin-bottom: 16px;
}

.cart-empty h5 {
    color: #666;
    margin-bottom: 8px;
}

.cart-empty p {
    color: #999;
    margin-bottom: 20px;
}

.cart-empty-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.cart-items-list {
    margin-bottom: 20px;
}

.cart-item-modal {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 12px;
    background: #fafafa;
}

.cart-item-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
}

.cart-item-details {
    flex: 1;
}

.cart-item-name {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.cart-item-meta {
    font-size: 12px;
    color: #666;
}

.badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    margin-left: 8px;
}

.badge-primary {
    background: var(--primary-color);
    color: white;
}

.badge-success {
    background: #50c878;
    color: white;
}

.cart-item-actions {
    text-align: right;
}

.cart-item-price {
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 4px;
}

.cart-more-items {
    text-align: center;
    margin: 16px 0;
}

.cart-more-items small {
    color: #999;
}

.cart-divider {
    height: 1px;
    background: #e0e0e0;
    margin: 20px 0;
}

.cart-summary-modal {
    padding: 0;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    font-size: 14px;
}

.total-row {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.total-price {
    color: var(--primary-color);
    font-size: 18px;
}

.text-success {
    color: #50c878 !important;
}

.shipping-notice {
    background: #e8f5e8;
    padding: 12px;
    border-radius: 6px;
    font-size: 12px;
    color: #2e7d32;
    margin-top: 12px;
}

.shipping-notice i {
    margin-right: 6px;
}

.contact-pricing {
    color: var(--primary-color);
    font-style: italic;
    font-size: 12px;
}

.price-on-request {
    color: #666;
    font-style: italic;
    font-size: 12px;
}

.service-notice {
    background: #e3f2fd;
    padding: 12px;
    border-radius: 6px;
    font-size: 12px;
    color: #1565c0;
    margin-top: 12px;
}

.service-notice i {
    margin-right: 6px;
}
</style>

<script>
// Remove item from cart in modal
async function removeFromCartModal(cartId, type) {
    if (!confirm('Remove this item from cart?')) return;

    try {
        const response = await fetch('../cart/remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cart_id=${cartId}`
        });

        const result = await response.json();

        if (result.success) {
            // Reload modal content
            loadCartModal();
            // Update cart count
            updateCartCount(result.cart_count);
            showNotification('Item removed from cart', 'success');
        } else {
            showNotification(result.message || 'Error removing item', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error removing item', 'error');
    }
}
</script>
