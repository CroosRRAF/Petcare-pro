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
    <link rel="stylesheet" href="../styles/auth.css">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f8f9fa; color: #333; }
        .services-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        h1 { text-align: center; color: #495057; margin-bottom: 30px; font-size: 2.5em; }
        .services-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .service { background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; }
        .service:hover { transform: translateY(-10px); box-shadow: 0 8px 16px rgba(0,0,0,0.2); }
        .service img { width: 100%; height: 200px; object-fit: cover; }
        .service-content { padding: 20px; }
        .service h3 { color: #495057; margin-bottom: 10px; font-size: 1.2em; }
        .service p { color: #6c757d; margin-bottom: 10px; }
        .category { background-color: #e9ecef; color: #495057; padding: 4px 8px; border-radius: 4px; font-size: 12px; display: inline-block; margin-bottom: 15px; }
        .btn { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s; font-size: 16px; width: 100%; }
        .btn:hover { background: linear-gradient(135deg, #218838 0%, #17a2b8 100%); }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="services-container">
        <h1><i class="fas fa-concierge-bell"></i> Our Services</h1>
        <div class="services-grid">
            <?php while ($service = $services->fetch_assoc()): ?>
                <div class="service">
                    <img src="../assets/images/services/<?php echo $service['image_url']; ?>" alt="<?php echo $service['name']; ?>">
                    <div class="service-content">
                        <span class="category"><?php echo htmlspecialchars($service['category']); ?></span>
                        <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <button class="btn"><i class="fas fa-calendar-check"></i> Book Service</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
