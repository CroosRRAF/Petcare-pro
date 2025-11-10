<?php
require_once '../includes/functions.php';
startSession();

// Get database connection
$conn = getDBConnection();

// Require user to be logged in (but not admin)
requireLogin();

if (isAdmin()) {
    redirect('../admin/index.php');
}

$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    if (!empty($username) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $email, $user_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $message = "Profile updated successfully.";
    } else {
        $message = "Invalid input.";
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch cart items
$cart_stmt = $conn->prepare("SELECT c.quantity, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_items = $cart_stmt->get_result();
$cart_stmt->close();

// Fetch previous orders
$order_stmt = $conn->prepare("SELECT id, total, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$orders = $order_stmt->get_result();
$order_stmt->close();

// Fetch payments
$payment_stmt = $conn->prepare("SELECT p.amount, p.method, p.status, p.created_at FROM payments p JOIN orders o ON p.order_id = o.id WHERE o.user_id = ? ORDER BY p.created_at DESC");
$payment_stmt->bind_param("i", $user_id);
$payment_stmt->execute();
$payments = $payment_stmt->get_result();
$payment_stmt->close();

// Fetch notifications
$notif_stmt = $conn->prepare("SELECT message, created_at FROM notifications WHERE user_id = ? AND read_status = FALSE ORDER BY created_at DESC LIMIT 5");
$notif_stmt->bind_param("i", $user_id);
$notif_stmt->execute();
$notifications = $notif_stmt->get_result();
$notif_stmt->close();

// Calculate statistics
$total_orders = $orders->num_rows;
$orders->data_seek(0); // Reset pointer

$total_spent = 0;
$orders_temp = $orders;
while ($order = $orders_temp->fetch_assoc()) {
    if ($order['status'] === 'completed') {
        $total_spent += $order['total'];
    }
}
$orders->data_seek(0); // Reset pointer

$cart_count = $cart_items->num_rows;
$cart_items->data_seek(0); // Reset pointer

$unread_notifications = $notifications->num_rows;
$notifications->data_seek(0); // Reset pointer
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Petcare Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome">
            <h1>
                <i class="fas fa-user-circle"></i>
                Welcome back, <?php echo htmlspecialchars($user['username']); ?>!
            </h1>
            <p>Manage your account, track orders, and explore our pet care services.</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                <i class="fas fa-<?php echo strpos($message, 'success') !== false ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-shopping-bag"></i>
                <h3><?php echo $total_orders; ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <h3><?php echo $cart_count; ?></h3>
                <p>Items in Cart</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign"></i>
                <h3>$<?php echo number_format($total_spent, 2); ?></h3>
                <p>Total Spent</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-bell"></i>
                <h3><?php echo $unread_notifications; ?></h3>
                <p>Notifications</p>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Update Profile Section -->
            <div class="section">
                <h2><i class="fas fa-user-edit"></i> Update Profile</h2>
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <!-- Current Cart Section -->
            <div class="section">
                <h2><i class="fas fa-shopping-cart"></i> Current Cart</h2>
                <?php if ($cart_items->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $cart_total = 0;
                            while ($item = $cart_items->fetch_assoc()):
                                $item_total = $item['quantity'] * $item['price'];
                                $cart_total += $item_total;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="price">$<?php echo number_format($item_total, 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <tr style="font-weight: bold; background-color: #f8f9fa;">
                                <td colspan="3" style="text-align: right;">Cart Total:</td>
                                <td class="price">$<?php echo number_format($cart_total, 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="margin-top: 20px;">
                        <a href="../cart/view_cart.php" class="btn">
                            <i class="fas fa-eye"></i> View Full Cart
                        </a>
                    </div>
                <?php else: ?>
                    <div class="empty">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Your cart is empty.</p>
                        <a href="../products/index.php" class="btn" style="margin-top: 15px;">
                            <i class="fas fa-shopping-bag"></i> Start Shopping
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Orders Section -->
            <div class="section">
                <h2><i class="fas fa-history"></i> Recent Orders</h2>
                <?php if ($orders->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $order_count = 0;
                            while ($order = $orders->fetch_assoc()):
                                if ($order_count >= 5) break; // Show only 5 recent orders
                                $order_count++;
                            ?>
                                <tr>
                                    <td><strong>#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td class="price">$<?php echo number_format($order['total'], 2); ?></td>
                                    <td><span class="status <?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php if ($orders->num_rows > 5): ?>
                        <div style="margin-top: 20px;">
                            <a href="my_orders.php" class="btn btn-outline">
                                <i class="fas fa-list"></i> View All Orders
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty">
                        <i class="fas fa-box-open"></i>
                        <p>No orders yet. Start shopping to see your orders here!</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Payment History Section -->
            <div class="section">
                <h2><i class="fas fa-credit-card"></i> Payment History</h2>
                <?php if ($payments->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $payment_count = 0;
                            while ($payment = $payments->fetch_assoc()):
                                if ($payment_count >= 5) break; // Show only 5 recent payments
                                $payment_count++;
                            ?>
                                <tr>
                                    <td class="price">$<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($payment['method'] ?: 'N/A'); ?></td>
                                    <td><span class="status <?php echo strtolower($payment['status']); ?>"><?php echo ucfirst($payment['status']); ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty">
                        <i class="fas fa-wallet"></i>
                        <p>No payment history available.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Notifications Section -->
            <div class="section">
                <h2><i class="fas fa-bell"></i> Recent Notifications</h2>
                <?php if ($notifications->num_rows > 0): ?>
                    <ul>
                        <?php while ($notif = $notifications->fetch_assoc()): ?>
                            <li>
                                <span><i class="fas fa-info-circle" style="color: var(--primary-color); margin-right: 8px;"></i><?php echo htmlspecialchars($notif['message']); ?></span>
                                <small><?php echo date('M d, H:i', strtotime($notif['created_at'])); ?></small>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty">
                        <i class="fas fa-bell-slash"></i>
                        <p>No new notifications.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Navigation Section -->
            <div class="section">
                <h2><i class="fas fa-rocket"></i> Quick Actions</h2>
                <div class="quick-actions">
                    <a href="../products/foods.php" class="quick-action-btn">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Shop Products</span>
                    </a>
                    <a href="../services/pet_grooming.php" class="quick-action-btn">
                        <i class="fas fa-cut"></i>
                        <span>Book Grooming</span>
                    </a>
                    <a href="../cart/view_cart.php" class="quick-action-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span>View Cart</span>
                    </a>
                    <a href="my_orders.php" class="quick-action-btn">
                        <i class="fas fa-box"></i>
                        <span>My Orders</span>
                    </a>
                    <a href="../contact.php" class="quick-action-btn">
                        <i class="fas fa-envelope"></i>
                        <span>Contact Us</span>
                    </a>
                    <a href="../auth/logout.php" class="quick-action-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
