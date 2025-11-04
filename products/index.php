<?php
require_once '../includes/functions.php';
startSession();

// Get filter parameters
$category = sanitize($_GET['category'] ?? 'all');
$search = sanitize($_GET['search'] ?? '');
$sort = sanitize($_GET['sort'] ?? 'name');
$page = intval($_GET['page'] ?? 1);
$per_page = 12;

$conn = getDBConnection();

// Build query based on filters
$where_conditions = [];
$params = [];
$param_types = '';

if ($category !== 'all') {
    $where_conditions[] = "category = ?";
    $params[] = $category;
    $param_types .= 's';
}

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ss';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM products $where_clause";
if (!empty($params)) {
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param($param_types, ...$params);
    $count_stmt->execute();
    $total_records = $count_stmt->get_result()->fetch_assoc()['total'];
} else {
    $total_records = $conn->query($count_query)->fetch_assoc()['total'];
}

// Calculate pagination
$pagination = paginate($total_records, $per_page, $page);

// Valid sort options
$valid_sorts = ['name', 'price_asc', 'price_desc', 'newest'];
if (!in_array($sort, $valid_sorts)) {
    $sort = 'name';
}

$order_clause = match($sort) {
    'price_asc' => 'ORDER BY price ASC',
    'price_desc' => 'ORDER BY price DESC',
    'newest' => 'ORDER BY created_at DESC',
    default => 'ORDER BY name ASC'
};

// Get products
$query = "SELECT * FROM products $where_clause $order_clause LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $pagination['offset'];
$param_types .= 'ii';

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get categories for filter
$categories = $conn->query("SELECT DISTINCT category FROM products ORDER BY category")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Petcare Pro</title>
    <meta name="description" content="Browse our wide selection of premium pet products including food, tools, and accessories for your beloved pets.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link rel="stylesheet" href="../styles/products.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="products-page">
        <!-- Page Header -->
        <section class="page-header">
            <div class="container">
                <div class="header-content">
                    <h1><i class="fas fa-shopping-bag"></i> Pet Products</h1>
                    <p>Premium quality products for your beloved companions</p>
                    <nav class="breadcrumb">
                        <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                        <span>/</span>
                        <span>Products</span>
                        <?php if ($category !== 'all'): ?>
                            <span>/</span>
                            <span><?= ucfirst(sanitize($category)) ?></span>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </section>

        <div class="container">
            <div class="products-layout">
                <!-- Sidebar Filters -->
                <aside class="filters-sidebar">
                    <div class="filter-section">
                        <h3><i class="fas fa-filter"></i> Filters</h3>

                        <!-- Search Filter -->
                        <div class="filter-group">
                            <label for="search-filter">Search Products</label>
                            <form method="GET" class="search-form">
                                <input type="hidden" name="category" value="<?= sanitize($category) ?>">
                                <input type="hidden" name="sort" value="<?= sanitize($sort) ?>">
                                <div class="search-input-group">
                                    <input type="text" id="search-filter" name="search"
                                           placeholder="Search products..."
                                           value="<?= sanitize($search) ?>">
                                    <button type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <!-- Category Filter -->
                        <div class="filter-group">
                            <label>Categories</label>
                            <div class="category-filters">
                                <a href="?<?= http_build_query(array_merge($_GET, ['category' => 'all', 'page' => 1])) ?>"
                                   class="category-filter <?= $category === 'all' ? 'active' : '' ?>">
                                    <i class="fas fa-th-large"></i> All Products
                                </a>
                                <?php foreach ($categories as $cat): ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['category' => $cat['category'], 'page' => 1])) ?>"
                                       class="category-filter <?= $category === $cat['category'] ? 'active' : '' ?>">
                                        <i class="fas fa-<?= $cat['category'] === 'Food' ? 'bone' : 'tools' ?>"></i>
                                        <?= sanitize($cat['category']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Clear Filters -->
                        <?php if ($category !== 'all' || !empty($search)): ?>
                            <div class="filter-group">
                                <a href="index.php" class="btn-clear-filters">
                                    <i class="fas fa-times"></i> Clear All Filters
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="products-content">
                    <!-- Products Header -->
                    <div class="products-header">
                        <div class="products-info">
                            <h2>
                                <?php if ($category !== 'all'): ?>
                                    <?= ucfirst(sanitize($category)) ?> Products
                                <?php else: ?>
                                    All Products
                                <?php endif; ?>
                                <span class="products-count">(<?= $total_records ?> <?= $total_records === 1 ? 'product' : 'products' ?>)</span>
                            </h2>

                            <?php if (!empty($search)): ?>
                                <p class="search-info">
                                    <i class="fas fa-search"></i> Results for "<strong><?= sanitize($search) ?></strong>"
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Sort Options -->
                        <div class="sort-options">
                            <label for="sort-select">Sort by:</label>
                            <select id="sort-select" onchange="updateSort(this.value)">
                                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name (A-Z)</option>
                                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <?php if (empty($products)): ?>
                        <div class="no-products">
                            <i class="fas fa-search fa-3x"></i>
                            <h3>No products found</h3>
                            <p>Try adjusting your search or filter criteria</p>
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-th-large"></i> Browse All Products
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="products-grid">
                            <?php foreach ($products as $product): ?>
                                <div class="product-card" data-product-id="<?= $product['id'] ?>">
                                    <div class="product-image-container">
                                        <img src="../assets/images/<?= sanitize($product['image_url']) ?>"
                                             alt="<?= sanitize($product['name']) ?>"
                                             class="product-image"
                                             onerror="this.src='../assets/images/placeholder-product.jpg'">

                                        <div class="product-overlay">
                                            <div class="product-actions">
                                                <button class="btn-action btn-quick-view"
                                                        onclick="quickView(<?= $product['id'] ?>)"
                                                        title="Quick View">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <?php if (isLoggedIn()): ?>
                                                    <button class="btn-action btn-wishlist"
                                                            onclick="toggleWishlist(<?= $product['id'] ?>)"
                                                            title="Add to Wishlist">
                                                        <i class="far fa-heart"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="product-info">
                                        <div class="product-category">
                                            <i class="fas fa-tag"></i> <?= sanitize($product['category']) ?>
                                        </div>

                                        <h3 class="product-name">
                                            <a href="product_detail.php?id=<?= $product['id'] ?>">
                                                <?= sanitize($product['name']) ?>
                                            </a>
                                        </h3>

                                        <p class="product-description">
                                            <?= sanitize(substr($product['description'], 0, 100)) ?>
                                            <?= strlen($product['description']) > 100 ? '...' : '' ?>
                                        </p>

                                        <div class="product-price">
                                            <?= formatPrice($product['price']) ?>
                                        </div>

                                        <div class="product-buttons">
                                            <?php if (isLoggedIn()): ?>
                                                <button class="btn btn-primary add-to-cart"
                                                        onclick="addToCart(<?= $product['id'] ?>)">
                                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                                </button>
                                            <?php else: ?>
                                                <a href="../auth/login.php" class="btn btn-primary">
                                                    <i class="fas fa-sign-in-alt"></i> Login to Buy
                                                </a>
                                            <?php endif; ?>

                                            <a href="product_detail.php?id=<?= $product['id'] ?>"
                                               class="btn btn-outline">
                                                <i class="fas fa-info-circle"></i> Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?= generatePaginationHTML($pagination, 'index.php?' . http_build_query(array_filter($_GET, function($key) { return $key !== 'page'; }, ARRAY_FILTER_USE_KEY))) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeQuickView()">&times;</span>
            <div id="quickViewContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>

    <script src="../scripts/cart.js"></script>
    <script>
        // Sort functionality
        function updateSort(sortValue) {
            const url = new URL(window.location);
            url.searchParams.set('sort', sortValue);
            url.searchParams.set('page', '1');
            window.location = url.toString();
        }

        // Wishlist functionality
        async function toggleWishlist(productId) {
            try {
                const response = await fetch('../user/toggle_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&csrf_token=<?= generateCSRFToken() ?>`
                });

                const result = await response.json();

                if (result.success) {
                    const btn = document.querySelector(`[data-product-id="${productId}"] .btn-wishlist i`);
                    btn.className = result.in_wishlist ? 'fas fa-heart' : 'far fa-heart';

                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.message || 'Error updating wishlist', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error updating wishlist', 'error');
            }
        }

        // Quick view functionality
        async function quickView(productId) {
            const modal = document.getElementById('quickViewModal');
            const content = document.getElementById('quickViewContent');

            content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
            modal.style.display = 'block';

            try {
                const response = await fetch(`product_quick_view.php?id=${productId}`);
                const html = await response.text();
                content.innerHTML = html;
            } catch (error) {
                content.innerHTML = '<div class="error">Error loading product details</div>';
            }
        }

        function closeQuickView() {
            document.getElementById('quickViewModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('quickViewModal');
            if (event.target === modal) {
                closeQuickView();
            }
        }
    </script>
</body>
</html>
