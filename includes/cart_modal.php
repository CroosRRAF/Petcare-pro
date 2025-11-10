<!-- Cart Modal (Pure CSS) -->
<div id="cartModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-shopping-cart"></i> Shopping Cart
            </h3>
            <button class="modal-close" onclick="closeCartModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="cartModalBody">
            <!-- Cart content will be loaded here -->
            <div class="loading-state">
                <div class="spinner"></div>
                <p>Loading cart...</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeCartModal()">Continue Shopping</button>
            <a href="../cart/view_cart.php" class="btn btn-primary">
                <i class="fas fa-eye"></i> View Full Cart
            </a>
        </div>
    </div>
</div>

<style>
/* Pure CSS Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    z-index: 10000;
    animation: fadeIn 0.3s ease;
}

.modal-overlay.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    max-width: 800px;
    width: 90%;
    max-height: 80vh;
    overflow: hidden;
    animation: slideIn 0.3s ease;
}

.modal-header {
    padding: 20px 24px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    transition: background 0.3s ease;
}

.modal-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.modal-body {
    padding: 24px;
    max-height: 400px;
    overflow-y: auto;
}

.modal-footer {
    padding: 20px 24px;
    background: #f8f9fa;
    display: flex;
    justify-content: space-between;
    gap: 12px;
}

.loading-state {
    text-align: center;
    padding: 40px 20px;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 16px;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Modal */
@media (max-width: 768px) {
    .modal-container {
        width: 95%;
        max-height: 90vh;
    }

    .modal-header, .modal-body, .modal-footer {
        padding: 16px 20px;
    }

    .modal-footer {
        flex-direction: column;
    }

    .modal-footer .btn {
        width: 100%;
    }
}
</style>

<script>
// Pure CSS Cart Modal Functions
async function loadCartModal() {
    try {
        const response = await fetch('../cart/get_cart_modal.php');
        const html = await response.text();
        document.getElementById('cartModalBody').innerHTML = html;
    } catch (error) {
        document.getElementById('cartModalBody').innerHTML =
            '<div class="alert alert-error">Error loading cart. Please try again.</div>';
    }
}

function showCartModal() {
    const modal = document.getElementById('cartModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden'; // Prevent background scroll
    loadCartModal();
}

function closeCartModal() {
    const modal = document.getElementById('cartModal');
    modal.classList.remove('show');
    document.body.style.overflow = ''; // Restore scroll
}

// Close modal when clicking overlay
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('cartModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeCartModal();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('show')) {
                closeCartModal();
            }
        });
    }
});

// Update cart count in header
function updateCartCount(count) {
    const cartBadge = document.querySelector('.cart-badge');
    const cartCount = document.querySelector('.cart-count');

    if (cartBadge) {
        cartBadge.textContent = count;
        cartBadge.style.display = count > 0 ? 'inline' : 'none';
    }

    if (cartCount) {
        cartCount.textContent = count;
        cartCount.style.display = count > 0 ? 'inline' : 'none';
    }
}

// Enhanced add to cart with modal feedback
async function addToCart(productId, quantity = 1) {
    try {
        const formData = new FormData();
        formData.append("product_id", productId);
        formData.append("quantity", quantity);

        const response = await fetch("../cart/add_to_cart.php", {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
            body: formData,
        });

        const result = await response.json();

        if (result.success) {
            updateCartCount(result.cart_count);
            showNotification(result.message, "success");

            // Show cart modal after adding
            setTimeout(() => {
                showCartModal();
            }, 1000);
        } else {
            showNotification(result.message || "Error adding to cart", "error");
        }
    } catch (error) {
        console.error("Error:", error);
        showNotification("Error adding to cart. Please try again.", "error");
    }
}

// Enhanced add service to cart
async function addServiceToCart(serviceId, quantity = 1) {
    try {
        const formData = new FormData();
        formData.append("service_id", serviceId);
        formData.append("quantity", quantity);

        const response = await fetch("../cart/add_to_cart.php", {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
            body: formData,
        });

        const result = await response.json();

        if (result.success) {
            updateCartCount(result.cart_count);
            showNotification(result.message, "success");

            // Show cart modal after adding
            setTimeout(() => {
                showCartModal();
            }, 1000);
        } else {
            showNotification(result.message || "Error booking service", "error");
        }
    } catch (error) {
        console.error("Error:", error);
        showNotification("Error booking service. Please try again.", "error");
    }
}

// Notification system
function showNotification(message, type = "info") {
    // Remove existing notifications
    const existing = document.querySelectorAll(".notification-popup");
    existing.forEach((n) => n.remove());

    const notification = document.createElement("div");
    notification.className = `notification-popup notification-${type}`;

    const icon =
        {
            success: "fa-check-circle",
            error: "fa-exclamation-circle",
            warning: "fa-exclamation-triangle",
            info: "fa-info-circle",
        }[type] || "fa-info-circle";

    notification.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(notification);

    // Show notification with animation
    setTimeout(() => notification.classList.add("show"), 100);

    // Hide notification after 3 seconds
    setTimeout(() => {
        notification.classList.remove("show");
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add notification styles if not already present
if (!document.getElementById("cart-notification-styles")) {
    const style = document.createElement("style");
    style.id = "cart-notification-styles";
    style.textContent = `
        .notification-popup {
            position: fixed;
            top: 20px;
            right: -400px;
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 280px;
            max-width: 400px;
            z-index: 10000;
            transition: right 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .notification-popup.show {
            right: 20px;
        }

        .notification-popup i {
            font-size: 20px;
        }

        .notification-popup.notification-success {
            border-left: 4px solid #50c878;
        }

        .notification-popup.notification-success i {
            color: #50c878;
        }

        .notification-popup.notification-error {
            border-left: 4px solid #e53e3e;
        }

        .notification-popup.notification-error i {
            color: #e53e3e;
        }

        .notification-popup.notification-warning {
            border-left: 4px solid #ffa726;
        }

        .notification-popup.notification-warning i {
            color: #ffa726;
        }

        .notification-popup.notification-info {
            border-left: 4px solid var(--primary-color);
        }

        .notification-popup.notification-info i {
            color: var(--primary-color);
        }

        .notification-popup span {
            color: #222;
            font-size: 14px;
            flex: 1;
        }

        @media (max-width: 768px) {
            .notification-popup {
                right: -100%;
                min-width: auto;
                max-width: calc(100% - 40px);
            }

            .notification-popup.show {
                right: 10px;
            }
        }
    `;
    document.head.appendChild(style);
}
</script>
