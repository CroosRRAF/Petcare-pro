<?php
// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
    <header>
        <nav class="top-nav" id="topNav">
            <a href="/Petcare-pro/index.php" class="brand">
                <i class="fas fa-paw"></i>
                Zyora PetCare
            </a>

            <ul>
                <li><a href="/Petcare-pro/index.php" class="active">Home</a></li>
                <li class="dropdown">
                    <a href="#">Products <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i></a>
                    <div class="dropdown-menu">
                        <a href="/Petcare-pro/products/foods.php"><i class="fas fa-bone"></i> Pet Foods</a>
                        <a href="/Petcare-pro/products/tools.php"><i class="fas fa-tools"></i> Pet Tools</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#">Services <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i></a>
                    <div class="dropdown-menu">
                        <a href="/Petcare-pro/services/pet_grooming.php"><i class="fas fa-cut"></i> Grooming</a>
                        <a href="/Petcare-pro/services/pet_health.php"><i class="fas fa-heartbeat"></i> Health Care</a>
                        <a href="/Petcare-pro/services/pet_boarding.php"><i class="fas fa-home"></i> Boarding</a>
                    </div>
                </li>
                <li><a href="/Petcare-pro/pages/about.php">About</a></li>
                <li><a href="/Petcare-pro/pages/contact.php">Contact</a></li>
            </ul>

            <div class="icons">
                <button class="menu-button" id="mobile-menu-btn" aria-label="Toggle Mobile Menu">
                    <i class="fas fa-bars"></i>
                </button>

                <i class="fas fa-search" id="search-btn"></i>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'user'): ?>
                    <div class="cart-icon">
                        <a href="/Petcare-pro/user/view_cart.php">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                        <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                            <span class="cart-badge"><?php echo $_SESSION['cart_count']; ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo ($_SESSION['role'] == 'admin') ? '/Petcare-pro/admin/dashboard.php' : '/Petcare-pro/user/dashboard.php'; ?>">
                        <i class="fas fa-user"></i>
                    </a>
                <?php else: ?>
                    <a href="/Petcare-pro/auth/login.php">
                        <i class="fas fa-sign-in-alt"></i>
                    </a>
                <?php endif; ?>
            </div>
        </nav>

        <form action="/Petcare-pro/pages/search.php" method="GET" class="search-form" id="search-form">
            <input type="search" name="q" placeholder="Search products, services..." id="search-box">
            <label for="search-box" class="fas fa-search"></label>
        </form>

        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="left-sidebar" id="left-sidebar">
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <div class="tabs">
                    <h3>Admin Panel</h3>
                    <ul>
                        <li><a href="/Petcare-pro/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="/Petcare-pro/admin/manage_products.php"><i class="fas fa-box"></i> Manage Products</a></li>
                        <li><a href="/Petcare-pro/admin/manage_services.php"><i class="fas fa-concierge-bell"></i> Manage Services</a></li>
                        <li><a href="/Petcare-pro/admin/manage_orders.php"><i class="fas fa-shopping-bag"></i> Orders</a></li>
                        <li><a href="/Petcare-pro/admin/manage_users.php"><i class="fas fa-users"></i> Users</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <div class="tabs">
                    <h3>My Account</h3>
                    <ul>
                        <li><a href="/Petcare-pro/user/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="/Petcare-pro/user/my_orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a></li>
                        <li><a href="/Petcare-pro/user/view_cart.php"><i class="fas fa-shopping-cart"></i> My Cart</a></li>
                        <li><a href="/Petcare-pro/user/my_pets.php"><i class="fas fa-paw"></i> My Pets</a></li>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="settings">
                <h3>Account</h3>
                <ul>
                    <li><a href="/Petcare-pro/user/profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                    <li><a href="/Petcare-pro/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>

        <div class="toggle-btn" id="left-sidebar-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <?php endif; ?>

        <div class="mobile-sidebar" id="mobile-sidebar">
            <ul>
                <li><a href="#" id="close-mobile-sidebar"><i class="fas fa-times"></i> Close Menu</a></li>
                <li><a href="/Petcare-pro/index.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="/Petcare-pro/products/foods.php"><i class="fas fa-bone"></i> Pet Foods</a></li>
                <li><a href="/Petcare-pro/products/tools.php"><i class="fas fa-tools"></i> Pet Tools</a></li>
                <li><a href="/Petcare-pro/services/pet_grooming.php"><i class="fas fa-cut"></i> Grooming</a></li>
                <li><a href="/Petcare-pro/services/pet_health.php"><i class="fas fa-heartbeat"></i> Health Care</a></li>
                <li><a href="/Petcare-pro/services/pet_boarding.php"><i class="fas fa-home"></i> Boarding</a></li>
                <li><a href="/Petcare-pro/pages/about.php"><i class="fas fa-info-circle"></i> About</a></li>
                <li><a href="/Petcare-pro/pages/contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo ($_SESSION['role'] == 'admin') ? '/Petcare-pro/admin/dashboard.php' : '/Petcare-pro/user/dashboard.php'; ?>"><i class="fas fa-user"></i> Dashboard</a></li>
                    <li><a href="/Petcare-pro/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="/Petcare-pro/auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="/Petcare-pro/auth/register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>
    <script src="/Petcare-pro/scripts/header.js"></script>
