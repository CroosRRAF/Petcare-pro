<?php
// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include_once '../config/db_connect.php';

// Get search query
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

$page_title = "Search Results - Petcare Pro";
include_once '../includes/header.php';
?>

<main>
    <div class="container">
        <div class="search-results-header">
            <h1>Search Results</h1>
            <?php if (!empty($query)): ?>
                <p class="search-query">Showing results for: "<strong><?php echo htmlspecialchars($query); ?></strong>"</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($query)): ?>
            <div class="search-results">
                <!-- Products Section -->
                <section class="search-section">
                    <h2>Products</h2>
                    <div class="products-grid">
                        <?php
                        // Search products
                        $product_sql = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ? OR category LIKE ?";
                        $product_stmt = $conn->prepare($product_sql);
                        $search_term = "%$query%";
                        $product_stmt->bind_param("sss", $search_term, $search_term, $search_term);
                        $product_stmt->execute();
                        $product_results = $product_stmt->get_result();

                        if ($product_results->num_rows > 0):
                            while ($product = $product_results->fetch_assoc()):
                        ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="/Petcare-pro/assets/images/<?php echo htmlspecialchars($product['category']); ?>/<?php echo htmlspecialchars($product['image']); ?>"
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         onerror="this.src='/Petcare-pro/assets/images/placeholder.jpg'">
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                                    <div class="product-price">
                                        <span class="price">Rs. <?php echo number_format($product['price'], 2); ?></span>
                                    </div>
                                    <div class="product-actions">
                                        <button class="btn-primary add-to-cart-btn"
                                                data-product-id="<?php echo $product['id']; ?>"
                                                data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                                data-product-price="<?php echo $product['price']; ?>">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                        <a href="/Petcare-pro/products/<?php echo ($product['category'] == 'foods') ? 'foods' : 'tools'; ?>.php" class="btn-secondary">
                                            <i class="fas fa-eye"></i> View More
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <p class="no-results">No products found matching your search.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Services Section -->
                <section class="search-section">
                    <h2>Services</h2>
                    <div class="services-grid">
                        <?php
                        // Search services
                        $service_sql = "SELECT * FROM services WHERE name LIKE ? OR description LIKE ? OR category LIKE ?";
                        $service_stmt = $conn->prepare($service_sql);
                        $service_stmt->bind_param("sss", $search_term, $search_term, $search_term);
                        $service_stmt->execute();
                        $service_results = $service_stmt->get_result();

                        if ($service_results->num_rows > 0):
                            while ($service = $service_results->fetch_assoc()):
                        ?>
                            <div class="service-card">
                                <div class="service-image">
                                    <img src="/Petcare-pro/assets/images/<?php echo htmlspecialchars($service['category']); ?>/<?php echo htmlspecialchars($service['image']); ?>"
                                         alt="<?php echo htmlspecialchars($service['name']); ?>"
                                         onerror="this.src='/Petcare-pro/assets/images/placeholder.jpg'">
                                </div>
                                <div class="service-info">
                                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                                    <p class="service-description"><?php echo htmlspecialchars(substr($service['description'], 0, 150)) . '...'; ?></p>
                                    <div class="service-price">
                                        <span class="price">Rs. <?php echo number_format($service['price'], 2); ?></span>
                                    </div>
                                    <div class="service-actions">
                                        <a href="/Petcare-pro/services/<?php echo htmlspecialchars($service['category']); ?>.php" class="btn-primary">
                                            <i class="fas fa-calendar-check"></i> Book Now
                                        </a>
                                        <a href="/Petcare-pro/services/<?php echo htmlspecialchars($service['category']); ?>.php" class="btn-secondary">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <p class="no-results">No services found matching your search.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        <?php else: ?>
            <div class="no-search-query">
                <p>Please enter a search term to find products and services.</p>
                <a href="/Petcare-pro/index.php" class="btn-primary">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
.search-results-header {
    text-align: center;
    margin-bottom: 2rem;
    padding: 2rem 0;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 10px;
}

.search-results-header h1 {
    margin: 0;
    font-size: 2.5rem;
}

.search-query {
    margin: 0.5rem 0 0 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

.search-section {
    margin-bottom: 3rem;
}

.search-section h2 {
    color: var(--primary-color);
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}

.products-grid, .services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.product-card, .service-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover, .service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.product-image, .service-image {
    height: 200px;
    overflow: hidden;
}

.product-image img, .service-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-info, .service-info {
    padding: 1.5rem;
}

.product-info h3, .service-info h3 {
    margin: 0 0 0.5rem 0;
    color: var(--primary-color);
    font-size: 1.3rem;
}

.product-description, .service-description {
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.product-price, .service-price {
    margin-bottom: 1rem;
}

.price {
    font-size: 1.4rem;
    font-weight: bold;
    color: var(--secondary-color);
}

.product-actions, .service-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-primary, .btn-secondary {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.btn-secondary {
    background: #f8f9fa;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.btn-secondary:hover {
    background: var(--primary-color);
    color: white;
}

.no-results {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 2rem 0;
}

.no-search-query {
    text-align: center;
    padding: 4rem 2rem;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 2rem 0;
}

.no-search-query p {
    color: #666;
    font-size: 1.2rem;
    margin-bottom: 2rem;
}

.add-to-cart-btn {
    flex: 1;
}

@media (max-width: 768px) {
    .products-grid, .services-grid {
        grid-template-columns: 1fr;
    }

    .product-actions, .service-actions {
        flex-direction: column;
    }

    .btn-primary, .btn-secondary {
        justify-content: center;
    }
}
</style>

<script>
// Add to cart functionality
document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const productPrice = this.getAttribute('data-product-price');

            // Add to cart via AJAX
            fetch('/Petcare-pro/cart/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=1`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`${productName} added to cart!`, 'success');
                    updateCartCount();
                } else {
                    showNotification(data.message || 'Failed to add item to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while adding item to cart', 'error');
            });
        });
    });
});

// Notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;

    // Style the notification
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '1rem 1.5rem',
        borderRadius: '5px',
        color: 'white',
        fontWeight: 'bold',
        zIndex: '10000',
        opacity: '0',
        transition: 'opacity 0.3s ease'
    });

    if (type === 'success') {
        notification.style.backgroundColor = '#28a745';
    } else {
        notification.style.backgroundColor = '#dc3545';
    }

    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => notification.style.opacity = '1', 100);

    // Hide and remove notification
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}

// Update cart count
function updateCartCount() {
    fetch('/Petcare-pro/cart/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            const cartBadge = document.querySelector('.cart-badge');
            if (data.count > 0) {
                if (cartBadge) {
                    cartBadge.textContent = data.count;
                } else {
                    // Create cart badge if it doesn't exist
                    const cartIcon = document.querySelector('.cart-icon');
                    if (cartIcon) {
                        const badge = document.createElement('span');
                        badge.className = 'cart-badge';
                        badge.textContent = data.count;
                        cartIcon.appendChild(badge);
                    }
                }
            } else {
                if (cartBadge) {
                    cartBadge.remove();
                }
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}
</script>

<?php include_once '../includes/footer.php'; ?>
