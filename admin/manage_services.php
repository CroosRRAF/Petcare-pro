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
    $service_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);

    if ($stmt->execute()) {
        $success = "Service deleted successfully!";
    } else {
        $error = "Failed to delete service.";
    }
}

$result = $conn->query("SELECT * FROM services");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Petcare Pro Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/admin/common.css">
    <link rel="stylesheet" href="../styles/admin/tables.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="admin-container">
        <div class="container">
            <h1><i class="fas fa-concierge-bell"></i> Manage Services</h1>

            <?php if ($error): ?>
                <div class="error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php elseif ($success): ?>
                <div class="success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <a href="add_services.php" class="btn-add"><i class="fas fa-plus-circle"></i> Add New Service</a>

            <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Image URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($service = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="table-id"><?= $service['id'] ?></td>
                        <td class="table-name"><?= htmlspecialchars($service['name']) ?></td>
                        <td><?= htmlspecialchars($service['description']) ?></td>
                        <td><span class="table-category"><?= htmlspecialchars($service['category']) ?></span></td>
                        <td><?= htmlspecialchars($service['image_url']) ?></td>
                        <td class="table-actions-cell">
                            <a href="edit_services.php?id=<?= $service['id'] ?>" class="table-action-btn edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="manage_services.php?delete=<?= $service['id'] ?>" class="table-action-btn delete"
                               onclick="return confirm('Are you sure you want to delete this service?');">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="form-nav">
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
