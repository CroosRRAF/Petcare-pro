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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_products.php");
    exit;
}

$product_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_products.php");
    exit;
}

$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $image_url = $_POST['image_url'];

    if (empty($name) || empty($price) || empty($category) || empty($image_url)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, category = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("sdsssi", $name, $price, $description, $category, $image_url, $product_id);

        if ($stmt->execute()) {
            $success = "Product updated successfully!";
        } else {
            $error = "Failed to update product.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
    <link rel="stylesheet" href="../styles/admin_manage_products.css">
</head>
<body>
    <div class="container">
        <h1>Edit Product</h1>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="edit_products.php?id=<?= $product_id ?>" method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

            <label>Price:</label>
            <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>

            <label>Description:</label>
            <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>

            <label>Category:</label>
            <select name="category" required>
                <option value="Food" <?= $product['category'] === 'Food' ? 'selected' : '' ?>>Food</option>
                <option value="Tools" <?= $product['category'] === 'Tools' ? 'selected' : '' ?>>Tools</option>
            </select>

            <label>Image URL:</label>
            <input type="text" name="image_url" value="<?= htmlspecialchars($product['image_url']) ?>" required>

            <button type="submit">Update Product</button>
        </form>

        <p><a href="manage_products.php">&larr; Back to Manage Products</a></p>
    </div>
</body>
</html>
