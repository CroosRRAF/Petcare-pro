<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Boarding Services | Petcare Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/services.css">
</head>
<body>
    <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        include '../includes/header.php';
        require_once '../includes/functions.php';

        $conn = getDBConnection();

        $query = "SELECT * FROM services WHERE category = 'Boarding'";
        $result = $conn->query($query);
    ?>

    <div class="services-container">
        <div class="services-header">
            <h2><i class="fas fa-home"></i> Pet Boarding Services</h2>
        </div>

        <div class="service-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($service = $result->fetch_assoc()): ?>
                    <div class="service-item">
                        <img src="../assets/images/<?= htmlspecialchars($service['image_url']) ?>"
                            alt="<?= htmlspecialchars($service['name']) ?>">
                        <div class="service-content">
                            <span class="category">Boarding</span>
                            <h3><?= htmlspecialchars($service['name']) ?></h3>
                            <p><?= htmlspecialchars($service['description']) ?></p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button onclick="addServiceToCart(<?= $service['id'] ?>)" class="book-now">
                                    <i class="fas fa-calendar-check"></i> Book Now
                                </button>
                            <?php else: ?>
                                <a href="../auth/login.php" class="book-now">
                                    <i class="fas fa-sign-in-alt"></i> Login to Book
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No boarding services available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php
        include '../includes/footer.php';
    ?>
    <script src="../scripts/cart.js"></script>
</body>
</html>
