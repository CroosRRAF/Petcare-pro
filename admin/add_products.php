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
    <title>Add Product</title>
    <link rel="stylesheet" href="../styles/admin_manage_products.css">
    <!-- <link rel="stylesheet" href="../styles/admin_manage_services.css"> -->
</head>
<body>
    <div class="container">
        <h1>Add New Product</h1>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="add_products.php" method="POST">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="Food">Food</option>
                <option value="Tools">Tools</option>
            </select>

            <label for="image_url">Image URL:</label>
            <input type="text" id="image_url" name="image_url" placeholder="e.g., food/1.jpg" required>

            <button type="submit">Add Product</button>
        </form>

        <p><a href="manage_products.php">&larr; Back to Manage Products</a></p>
    </div>
</body>
</html>
