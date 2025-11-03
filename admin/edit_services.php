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
    <title>Edit Service</title>
    <link rel="stylesheet" href="../styles/manage_services.css">
</head>
<body>
    <div class="container">
        <h1>Edit Service</h1>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="edit_services.php?id=<?= $service['id'] ?>" method="POST">
            <label for="name">Service Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($service['name']) ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($service['description']) ?></textarea>

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" value="<?= htmlspecialchars($service['category']) ?>" required>

            <label for="image_url">Image URL:</label>
            <input type="text" id="image_url" name="image_url" value="<?= htmlspecialchars($service['image_url']) ?>" required>

            <button type="submit">Update Service</button>
        </form>

        <p><a href="manage_services.php">&larr; Back to Manage Services</a></p>
    </div>
</body>
</html>
