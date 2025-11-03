<?php
require_once '../includes/functions.php';
startSession();

// Get database connection
$conn = getDBConnection();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_type = '';
$item_id = 0;

// Check if product or service ID is set
if (isset($_GET['product_id'])) {
    $cart_type = 'products';
    $item_id = intval($_GET['product_id']);
} elseif (isset($_GET['service_id'])) {
    $cart_type = 'services';
    $item_id = intval($_GET['service_id']);
} else {
    die("Invalid request. No product or service selected.");
}

// Fetch item details
if ($cart_type === 'products') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
} else {
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
}

$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    die("Invalid product or service.");
}

// If user confirmed addition
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $item_key = $cart_type === 'products' ? 'product_id' : 'service_id';

    // Check if item is already in cart
    $exists = false;
    foreach ($_SESSION['cart'] as $cart_item) {
        if ($cart_item[$item_key] == $item_id) {
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        $_SESSION['cart'][] = [
            'type' => $cart_type,
            $item_key => $item_id,
            'name' => $item['name'],
            'price' => $item['price'] ?? 0,
            'image_url' => $item['image_url'] ?? '',
        ];
        $message = "Item added to cart successfully!";
    } else {
        $message = "Item is already in your cart.";
    }

    // Redirect to view cart
    header("Location: view_cart.php?message=" . urlencode($message));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <title>Add to Cart</title>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="cart-confirmation">
        <h2>Do you want to add <b><?= htmlspecialchars($item['name']) ?></b> to your cart?</h2>
        <p>Price: $<?= number_format($item['price'], 2) ?></p>

        <a href="add_to_cart.php?<?= $cart_type === 'products' ? 'product_id=' . $item_id : 'service_id=' . $item_id ?>&confirm=yes" class="confirm-btn">Yes, Add to Cart</a>
        <button onclick="showOptions()" class="cancel-btn">No</button>

        <div id="cart-options" style="display: none;">
            <a href="view_cart.php" class="view-cart-btn">View Cart</a>
            <button onclick="hideOptions()" class="close-btn">Close</button>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        function showOptions() {
            document.getElementById('cart-options').style.display = 'block';
        }

        function hideOptions() {
            document.getElementById('cart-options').style.display = 'none';
        }
    </script>
</body>
</html>
<style>
    .cart-confirmation {
        text-align: center;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        width: 50%;
        box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
    }

    .cart-confirmation h2 {
        margin-bottom: 10px;
    }

    .confirm-btn, .cancel-btn, .view-cart-btn, .close-btn {
        display: inline-block;
        padding: 10px 15px;
        margin: 10px;
        border: none;
        cursor: pointer;
        font-size: 16px;
        text-decoration: none;
        color: white;
        border-radius: 5px;
    }

    .confirm-btn {
        background: #28a745;
    }

    .confirm-btn:hover {
        background: #218838;
    }

    .cancel-btn {
        background: red;
    }

    .cancel-btn:hover {
        background: darkred;
    }

    .view-cart-btn {
        background: #007bff;
    }

    .view-cart-btn:hover {
        background: #0056b3;
    }

    .close-btn {
        background: #6c757d;
    }

    .close-btn:hover {
        background: #5a6268;
    }

    #cart-options {
        margin-top: 20px;
        display: none;
    }

</style>
