<?php
session_start();
require_once '../config/db_connect.php';

// Fetch services
$stmt = $conn->prepare("SELECT id, name, description, category, image_url FROM services");
$stmt->execute();
$services = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services | Petcare Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/services.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="services-container">
        <div class="services-header">
            <h1><i class="fas fa-concierge-bell"></i> Our Services</h1>
        </div>

        <div class="services-grid">
            <?php while ($service = $services->fetch_assoc()): ?>
                <div class="service">
                    <img src="../assets/images/services/<?php echo $service['image_url']; ?>"
                         alt="<?php echo $service['name']; ?>">
                    <div class="service-content">
                        <span class="category"><?php echo htmlspecialchars($service['category']); ?></span>
                        <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button onclick="addServiceToCart(<?php echo $service['id']; ?>)" class="btn">
                                <i class="fas fa-calendar-check"></i> Book Service
                            </button>
                        <?php else: ?>
                            <a href="../auth/login.php" class="btn">
                                <i class="fas fa-sign-in-alt"></i> Login to Book
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../scripts/cart.js"></script>
</body>
</html>
