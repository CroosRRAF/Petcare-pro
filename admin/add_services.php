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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $image_url = trim($_POST['image_url']);

    if (empty($name) || empty($description) || empty($category) || empty($image_url)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO services (name, description, category, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $description, $category, $image_url);

        if ($stmt->execute()) {
            $success = "Service added successfully!";
        } else {
            $error = "Failed to add service.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service - Zyora PetCare Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/admin_dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-plus"></i> Add New Service</h1>
            <p>Create a new service for your pet care business</p>
        </div>

        <div class="admin-nav">
            <nav>
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="manage_services.php"><i class="fas fa-concierge-bell"></i> Manage Services</a></li>
                    <li><a href="add_products.php"><i class="fas fa-plus"></i> Add Product</a></li>
                    <li><a href="manage_products.php"><i class="fas fa-box"></i> Manage Products</a></li>
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

            <form action="add_services.php" method="POST" class="admin-form">
                <div class="form-group">
                    <label for="name"><i class="fas fa-tag"></i> Service Name:</label>
                    <input type="text" id="name" name="name" required placeholder="Enter service name">
                </div>

                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Description:</label>
                    <textarea id="description" name="description" required placeholder="Enter service description" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="category"><i class="fas fa-list"></i> Category:</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Grooming">Grooming</option>
                        <option value="Health">Health</option>
                        <option value="Boarding">Boarding</option>
                        <option value="Training">Training</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image_url"><i class="fas fa-image"></i> Image URL:</label>
                    <input type="url" id="image_url" name="image_url" required placeholder="Enter image URL">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Service
                    </button>
                    <a href="manage_services.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Services
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
