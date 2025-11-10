<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Health Services | Petcare Pro</title>
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

    $query = "SELECT * FROM services WHERE category = 'Health'";
    $result = $conn->query($query);
    ?>

    <div class="services-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1>
                    <i class="fas fa-heartbeat"></i> Pet Health Services
                </h1>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="services-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($service = $result->fetch_assoc()): ?>
                    <div class="service-card">
                        <div class="card-image-container">
                            <img src="../assets/images/<?= htmlspecialchars($service['image_url']) ?>"
                                alt="<?= htmlspecialchars($service['name']) ?>"
                                class="card-image"
                                onerror="this.src='../assets/images/placeholder-service.jpg'">
                            <div class="card-overlay">
                                <div class="overlay-content">
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <button onclick="addServiceToCart(<?= $service['id'] ?>)" class="btn btn-primary">
                                            <i class="fas fa-calendar-check"></i> Book Now
                                        </button>
                                    <?php else: ?>
                                        <a href="../auth/login.php" class="btn btn-primary">
                                            <i class="fas fa-sign-in-alt"></i> Login to Book
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-header">
                                <span class="badge badge-primary">Health</span>
                                <h3 class="card-title"><?= htmlspecialchars($service['name']) ?></h3>
                            </div>
                            <p class="card-description"><?= htmlspecialchars($service['description']) ?></p>
                            <div class="card-actions">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <button onclick="addServiceToCart(<?= $service['id'] ?>)" class="btn btn-primary">
                                        <i class="fas fa-calendar-check"></i> Book Now
                                    </button>
                                <?php else: ?>
                                    <a href="../auth/login.php" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt"></i> Login to Book
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-heartbeat fa-4x"></i>
                    <h3>No health services available</h3>
                    <p>Please check back later for our health services.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <!-- Cart Modal -->
    <?php include '../includes/cart_modal.php'; ?>
</body>
</html>
