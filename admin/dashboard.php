<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$product_count_query = "SELECT COUNT(*) as total_products FROM products";
$service_count_query = "SELECT COUNT(*) as total_services FROM services";

$product_count_result = $conn->query($product_count_query);
$service_count_result = $conn->query($service_count_query);

// Initialize counts
$total_products = 0;
$total_services = 0;

if ($product_count_result) {
    $product_data = $product_count_result->fetch_assoc();
    $total_products = $product_data['total_products'] ?? 0;
}

if ($service_count_result) {
    $service_data = $service_count_result->fetch_assoc();
    $total_services = $service_data['total_services'] ?? 0;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Petcare Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/admin/common.css">
    <link rel="stylesheet" href="../styles/admin/dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="admin-container">
        <div class="admin-layout">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <div class="sidebar-header">
                    <h3><i class="fas fa-tachometer-alt"></i> Admin Panel</h3>
                    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>!</p>
                </div>
                <nav class="sidebar-nav">
                    <a href="dashboard.php" class="sidebar-nav-item active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="manage_products.php" class="sidebar-nav-item">
                        <i class="fas fa-box"></i> Manage Products
                    </a>
                    <a href="manage_services.php" class="sidebar-nav-item">
                        <i class="fas fa-concierge-bell"></i> Manage Services
                    </a>
                    <a href="add_products.php" class="sidebar-nav-item">
                        <i class="fas fa-plus"></i> Add Products
                    </a>
                    <a href="add_services.php" class="sidebar-nav-item">
                        <i class="fas fa-plus"></i> Add Services
                    </a>
                    <a href="../auth/logout.php" class="sidebar-nav-item">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="admin-content">
                <div class="admin-header">
                    <h1><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h1>
                    <p>Monitor your pet care business performance</p>
                </div>

                <div class="admin-main">
                    <div class="stats">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="stat-info">
                                <h2>Total Products</h2>
                                <p class="stat-number"><?= $total_products; ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-concierge-bell"></i>
                            </div>
                            <div class="stat-info">
                                <h2>Total Services</h2>
                                <p class="stat-number"><?= $total_services; ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h2>Active Session</h2>
                                <p class="stat-number">Administrator</p>
                            </div>
                        </div>
                    </div>

                    <div class="quick-actions">
                        <h3><i class="fas fa-lightning-bolt"></i> Quick Actions</h3>
                        <div class="action-buttons">
                            <a href="add_products.php" class="action-btn primary">
                                <i class="fas fa-plus"></i> Add New Product
                            </a>
                            <a href="add_services.php" class="action-btn secondary">
                                <i class="fas fa-plus"></i> Add New Service
                            </a>
                            <a href="manage_products.php" class="action-btn">
                                <i class="fas fa-edit"></i> Manage Products
                            </a>
                            <a href="manage_services.php" class="action-btn">
                                <i class="fas fa-edit"></i> Manage Services
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Mobile Sidebar Toggle -->
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="sidebar-overlay" onclick="closeSidebar()"></div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.admin-sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }

        function closeSidebar() {
            const sidebar = document.querySelector('.admin-sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }

        // Close sidebar when clicking on a nav item (mobile)
        document.querySelectorAll('.sidebar-nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });

        // Close sidebar when pressing Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
