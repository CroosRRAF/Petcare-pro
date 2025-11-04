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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $image_url = trim($_POST['image_url']);

    if (empty($name) || empty($price) || empty($category) || empty($image_url)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, category, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $name, $price, $description, $category, $image_url);
        if ($stmt->execute()) {
            $success = "Product added successfully!";
        } else {
            $error = "Failed to add product.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Petcare Pro Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/admin/common.css">
    <link rel="stylesheet" href="../styles/admin/forms.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="admin-container">
        <div class="container">
            <h1><i class="fas fa-plus-circle"></i> Add New Product</h1>

            <?php if ($error): ?>
                <div class="error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php elseif ($success): ?>
                <div class="success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form action="add_products.php" method="POST" class="admin-form">
                <div class="form-group">
                    <label for="name"><i class="fas fa-tag"></i> Product Name:</label>
                    <input type="text" id="name" name="name" required placeholder="Enter product name">
                </div>

                <div class="form-group">
                    <label for="price"><i class="fas fa-dollar-sign"></i> Price:</label>
                    <input type="number" id="price" name="price" step="0.01" required placeholder="0.00">
                </div>

                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Description:</label>
                    <textarea id="description" name="description" placeholder="Enter product description"></textarea>
                </div>

                <div class="form-group">
                    <label for="category"><i class="fas fa-list"></i> Category:</label>
                    <select id="category" name="category" required>
                        <option value="Food">Food</option>
                        <option value="Tools">Tools</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image_url"><i class="fas fa-image"></i> Image URL:</label>
                    <input type="text" id="image_url" name="image_url" placeholder="e.g., food/1.jpg" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Add Product
                    </button>
                    <a href="manage_products.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>

            <div class="form-nav">
                <a href="manage_products.php"><i class="fas fa-arrow-left"></i> Back to Manage Products</a>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
