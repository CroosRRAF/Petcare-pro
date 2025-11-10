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

if (isset($_GET['id'])) {
    $service_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();

    if (!$service) {
        header("Location: manage_services.php");
        exit;
    }
} else {
    header("Location: manage_services.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $image_url = trim($_POST['image_url']);

    if (empty($name) || empty($description) || empty($category) || empty($image_url)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, category = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $description, $category, $image_url, $service_id);

        if ($stmt->execute()) {
            $success = "Service updated successfully!";
        } else {
            $error = "Failed to update service.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service - Petcare Pro Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/admin/common.css">
    <link rel="stylesheet" href="../styles/admin/forms.css">
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
                    <h1><i class="fas fa-edit"></i> Edit Service</h1>
                    <p>Update service information</p>
                </div>

                <div class="admin-main">
                    <?php if ($error): ?>
                        <div class="error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                    <?php elseif ($success): ?>
                        <div class="success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form action="edit_services.php?id=<?= $service['id'] ?>" method="POST" class="admin-form">
                        <div class="form-group">
                            <label for="name"><i class="fas fa-concierge-bell"></i> Service Name:</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($service['name']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="description"><i class="fas fa-align-left"></i> Description:</label>
                            <textarea id="description" name="description" required><?= htmlspecialchars($service['description']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="category"><i class="fas fa-list"></i> Category:</label>
                            <input type="text" id="category" name="category" value="<?= htmlspecialchars($service['category']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="image_url"><i class="fas fa-image"></i> Image URL:</label>
                            <input type="text" id="image_url" name="image_url" value="<?= htmlspecialchars($service['image_url']) ?>" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Service
                            </button>
                            <a href="manage_services.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
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
