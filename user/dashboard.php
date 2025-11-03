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
$notif_stmt = $conn->prepare("SELECT message, created_at FROM notifications WHERE user_id = ? AND read_status = FALSE ORDER BY created_at DESC");
$notif_stmt->bind_param("i", $user_id);
$notif_stmt->execute();
$notifications = $notif_stmt->get_result();
$notif_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | Zyora PetCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/auth.css">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f8f9fa; color: #333; }
        .dashboard-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .welcome { text-align: center; margin-bottom: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; }
        .section { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .section:hover { transform: translateY(-5px); }
        .section h2 { color: #495057; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; }
        .section h2 i { margin-right: 10px; color: #28a745; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .btn { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s; font-size: 16px; }
        .btn:hover { background: linear-gradient(135deg, #218838 0%, #17a2b8 100%); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #495057; }
        input { width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 5px; font-size: 16px; }
        input:focus { border-color: #28a745; outline: none; box-shadow: 0 0 5px rgba(40, 167, 69, 0.5); }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .nav-links { display: flex; gap: 15px; flex-wrap: wrap; }
        .nav-links a { text-decoration: none; }
        ul { list-style: none; padding: 0; }
        ul li { padding: 10px 0; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status.pending { background-color: #fff3cd; color: #856404; }
        .status.completed { background-color: #d4edda; color: #155724; }
        .status.cancelled { background-color: #f8d7da; color: #721c24; }
        .status.failed { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="dashboard-container">
        <div class="welcome">
            <h1><i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <p>Manage your account, view orders, and explore our services.</p>
        </div>
        <?php if (isset($message)): ?>
            <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="section">
                <h2><i class="fas fa-user-edit"></i> Update Profile</h2>
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username:</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email:</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn"><i class="fas fa-save"></i> Update Profile</button>
                </form>
            </div>

            <div class="section">
                <h2><i class="fas fa-shopping-cart"></i> Current Cart Items</h2>
                <?php if ($cart_items->num_rows > 0): ?>
                    <table>
                        <tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr>
                        <?php while ($item = $cart_items->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p class="empty">Your cart is empty.</p>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2><i class="fas fa-history"></i> Previous Orders</h2>
                <?php if ($orders->num_rows > 0): ?>
                    <table>
                        <tr><th>Order ID</th><th>Total</th><th>Status</th><th>Date</th></tr>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td>$<?php echo number_format($order['total'], 2); ?></td>
                                <td><span class="status <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p class="empty">No previous orders.</p>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2><i class="fas fa-credit-card"></i> Payments</h2>
                <?php if ($payments->num_rows > 0): ?>
                    <table>
                        <tr><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr>
                        <?php while ($payment = $payments->fetch_assoc()): ?>
                            <tr>
                                <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($payment['method'] ?: 'N/A'); ?></td>
                                <td><span class="status <?php echo $payment['status']; ?>"><?php echo ucfirst($payment['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p class="empty">No payments found.</p>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2><i class="fas fa-bell"></i> Notifications</h2>
                <?php if ($notifications->num_rows > 0): ?>
                    <ul>
                        <?php while ($notif = $notifications->fetch_assoc()): ?>
                            <li>
                                <span><?php echo htmlspecialchars($notif['message']); ?></span>
                                <small><?php echo date('M d, Y H:i', strtotime($notif['created_at'])); ?></small>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="empty">No new notifications.</p>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2><i class="fas fa-compass"></i> Quick Navigation</h2>
                <div class="nav-links">
                    <a href="../index.php" class="btn"><i class="fas fa-home"></i> Home</a>
                    <a href="../pages/products.php" class="btn"><i class="fas fa-box"></i> Products</a>
                    <a href="../pages/services.php" class="btn"><i class="fas fa-concierge-bell"></i> Services</a>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
