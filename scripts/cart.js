/**
 * Cart functionality for Petcare Pro
 * Handles add to cart AJAX requests
 */

// Add product to cart
async function addToCart(productId, quantity = 1) {
  return addItemToCart("product", productId, quantity);
}

// Add service to cart
async function addServiceToCart(serviceId, quantity = 1) {
  return addItemToCart("service", serviceId, quantity);
}

// Generic add item to cart function
async function addItemToCart(type, itemId, quantity = 1) {
  if (!itemId) {
    showNotification("Invalid item", "error");
    return false;
  }

  // Check if user is logged in
  const isLoggedIn =
    document.querySelector(".cart-icon") !== null ||
    document.getElementById("left-sidebar") !== null;

  if (!isLoggedIn) {
    showNotification("Please login to add items to cart", "warning");
    setTimeout(() => {
      window.location.href = "/Petcare-pro/auth/login.php";
    }, 1500);
    return false;
  }

  try {
    const formData = new FormData();
    if (type === "product") {
      formData.append("product_id", itemId);
    } else {
      formData.append("service_id", itemId);
    }
    formData.append("quantity", quantity);

    // Get CSRF token from meta tag or generate one
    const csrfToken =
      document.querySelector('meta[name="csrf-token"]')?.content ||
      document.querySelector('input[name="csrf_token"]')?.value ||
      getCookie("csrf_token");

    if (csrfToken) {
      formData.append("csrf_token", csrfToken);
    }

    const response = await fetch("/Petcare-pro/cart/add_to_cart.php", {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      // Update cart count in header
      updateCartCount(result.cart_count);

      // Show success message
      showNotification(result.message, "success");
      return true;
    } else {
      showNotification(result.message || "Error adding to cart", "error");
      return false;
    }
  } catch (error) {
    console.error("Error:", error);
    showNotification("Error adding to cart. Please try again.", "error");
    return false;
  }
}

// Update cart count badge in header
function updateCartCount(count) {
  const cartBadge = document.querySelector(".cart-badge");
  const cartCount = document.querySelector(".cart-count");

  if (cartBadge) {
    cartBadge.textContent = count;
    cartBadge.style.display = count > 0 ? "inline" : "none";
  }

  if (cartCount) {
    cartCount.textContent = count;
    cartCount.style.display = count > 0 ? "inline" : "none";
  }
}

// Show notification message
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

// Get cookie value by name
function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(";").shift();
  return null;
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
            border-left: 4px solid #3c91e6;
        }

        .notification-popup.notification-info i {
            color: #3c91e6;
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
