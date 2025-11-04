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
    <link rel="stylesheet" href="../styles/admin_dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-box"></i> Manage Products</h1>
            <p>View and manage all products in your pet care store</p>
        </div>

        <div class="admin-nav">
            <nav>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="add_products.php"><i class="fas fa-plus"></i> Add Product</a></li>
                    <li><a href="manage_services.php"><i class="fas fa-concierge-bell"></i> Manage Services</a></li>
                    <li><a href="add_services.php"><i class="fas fa-plus"></i> Add Service</a></li>
                </ul>
            </nav>
        </div>

        <div class="form-container">
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

            <h2><i class="fas fa-list"></i> Product List</h2>
        <table class="admin-table">
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
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td>$<?= number_format($product['price'], 2) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td><img src="../assets/images/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" width="50" style="border-radius: 4px;"></td>
                        <td>
                            <div class="table-actions">
                                <a href="edit_products.php?id=<?= $product['id'] ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="manage_products.php?delete=<?= $product['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="form-actions">
            <a href="add_products.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
