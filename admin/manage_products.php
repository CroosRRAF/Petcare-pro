<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../config/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$error = $success = "";

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = "Product deleted successfully!";
    } else {
        $error = "Failed to delete product.";
    }
}

$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Petcare Pro Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/admin/common.css">
    <link rel="stylesheet" href="../styles/admin/tables.css">
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
                    <a href="dashboard.php" class="sidebar-nav-item">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="manage_products.php" class="sidebar-nav-item active">
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
                    <h1><i class="fas fa-box"></i> Manage Products</h1>
                    <p>View and manage all pet care products</p>
                </div>

                <div class="admin-main">
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-header">
                        <a href="add_products.php" class="btn-add">
                            <i class="fas fa-plus-circle"></i> Add New Product
                        </a>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($product = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="table-id"><?= $product['id'] ?></td>
                                        <td class="table-name"><?= htmlspecialchars($product['name']) ?></td>
                                        <td>$<?= number_format($product['price'], 2) ?></td>
                                        <td><?= htmlspecialchars($product['description']) ?></td>
                                        <td><span class="table-category"><?= htmlspecialchars($product['category']) ?></span></td>
                                        <td><img src="../assets/images/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="50" style="border-radius: 4px;"></td>
                                        <td class="table-actions-cell">
                                            <a href="edit_products.php?id=<?= $product['id'] ?>" class="table-action-btn edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="manage_products.php?delete=<?= $product['id'] ?>" class="table-action-btn delete" onclick="return confirm('Are you sure you want to delete this product?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
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
