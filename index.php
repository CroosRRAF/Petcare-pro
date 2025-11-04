<?php
// No session_start() needed here - header.php handles it
require_once 'config/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petcare Pro - Your Trusted Pet Care Partner</title>
    <meta name="description" content="Petcare Pro provides premium pet care products and services including grooming, health care, boarding, and quality pet supplies.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <link rel="stylesheet" href="styles/landing.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>Welcome to Petcare Pro</h1>
            <p>Your trusted partner in providing the best care, products, and services for your beloved pets. We're dedicated to keeping your furry friends happy and healthy!</p>
            <div class="hero-buttons">
                <a href="products/index.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Shop Products</a>
                <a href="services/pet_grooming.php" class="btn btn-outline"><i class="fas fa-concierge-bell"></i> Our Services</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="section-title">
            <h2>Why Choose Us?</h2>
            <p>We provide comprehensive pet care solutions tailored to your pet's needs</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Trusted Care</h3>
                <p>Years of experience in providing professional and compassionate pet care services</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3>Premium Quality</h3>
                <p>Only the best products and services for your beloved companions</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Emergency services and support available around the clock</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <h3>Expert Team</h3>
                <p>Certified professionals who love and care for animals</p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services-section">
        <div class="section-title">
            <h2>Our Services</h2>
            <p>Comprehensive care solutions for all your pet's needs</p>
        </div>
        <div class="services-grid">
            <div class="service-card">
                <img src="assets/images/petgrooming.jpg" alt="Pet Grooming" class="service-image">
                <div class="service-content">
                    <h3><i class="fas fa-cut"></i> Pet Grooming</h3>
                    <p>Professional grooming services to keep your pets looking and feeling their best. From baths to haircuts, we do it all!</p>
                    <a href="services/pet_grooming.php" class="btn-learn-more">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="service-card">
                <img src="assets/images/pethealth.webp" alt="Health Care" class="service-image">
                <div class="service-content">
                    <h3><i class="fas fa-heartbeat"></i> Health Care</h3>
                    <p>Comprehensive veterinary services including check-ups, vaccinations, and emergency care for your pets.</p>
                    <a href="services/pet_health.php" class="btn-learn-more">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="service-card">
                <img src="assets/images/petboarding.jpg" alt="Pet Boarding" class="service-image">
                <div class="service-content">
                    <h3><i class="fas fa-home"></i> Pet Boarding</h3>
                    <p>Safe and comfortable boarding facilities where your pets can stay while you're away. They'll feel right at home!</p>
                    <a href="services/pet_boarding.php" class="btn-learn-more">Learn More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <div class="section-title">
            <h2>Featured Products</h2>
            <p>Premium quality products for your pet's well-being</p>
        </div>
        <div class="products-grid">
            <div class="product-card">
                <img src="assets/images/petfoods.jpg" alt="Pet Foods" class="product-image" onerror="this.src='https://via.placeholder.com/280x220/4CAF50/ffffff?text=Pet+Foods'">
                <div class="product-content">
                    <h3><i class="fas fa-bone"></i> Pet Foods</h3>
                    <p>Nutritious and delicious food options for all pet types</p>
                    <a href="products/foods.php" class="btn btn-primary">Browse Foods</a>
                </div>
            </div>
            <div class="product-card">
                <img src="assets/images/pettools.jpg" alt="Pet Tools" class="product-image" onerror="this.src='https://via.placeholder.com/280x220/4CAF50/ffffff?text=Pet+Supplies'">
                <div class="product-content">
                    <h3><i class="fas fa-tools"></i> Pet Supplies</h3>
                    <p>Quality tools and accessories for pet care</p>
                    <a href="products/tools.php" class="btn btn-primary">Browse Supplies</a>
                </div>
            </div>
            <div class="product-card">
                <img src="assets/images/pet-toys.jpg" alt="Pet Toys" class="product-image" onerror="this.src='https://via.placeholder.com/280x220/4CAF50/ffffff?text=Pet+Toys'">
                <div class="product-content">
                    <h3><i class="fas fa-futbol"></i> Pet Toys</h3>
                    <p>Fun and engaging toys to keep your pets active</p>
                    <a href="products/foods.php" class="btn btn-primary">Browse Toys</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="section-title">
            <h2>What Our Clients Say</h2>
            <p>Don't just take our word for it - hear from our happy customers!</p>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <i class="fas fa-quote-left testimonial-quote"></i>
                <p class="testimonial-text">"Petcare Pro has been amazing! Their grooming service is top-notch and my dog always looks fantastic. The staff is so caring and professional."</p>
                <div class="testimonial-author">
                    <img src="https://via.placeholder.com/50" alt="Client" class="author-image">
                    <div class="author-info">
                        <h4>Sarah Johnson</h4>
                        <p>Dog Owner</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <i class="fas fa-quote-left testimonial-quote"></i>
                <p class="testimonial-text">"I trust Petcare Pro with my cat's health completely. The veterinary services are excellent and they truly care about animals."</p>
                <div class="testimonial-author">
                    <img src="https://via.placeholder.com/50" alt="Client" class="author-image">
                    <div class="author-info">
                        <h4>Michael Chen</h4>
                        <p>Cat Owner</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <i class="fas fa-quote-left testimonial-quote"></i>
                <p class="testimonial-text">"The boarding facilities are wonderful! I can travel knowing my pets are in good hands. They even send me updates with photos!"</p>
                <div class="testimonial-author">
                    <img src="https://via.placeholder.com/50" alt="Client" class="author-image">
                    <div class="author-info">
                        <h4>Emily Rodriguez</h4>
                        <p>Pet Parent</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2>Ready to Give Your Pet the Best Care?</h2>
            <p>Join thousands of happy pet owners who trust Petcare Pro for their pet's needs</p>
            <div class="hero-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="user/dashboard.php" class="btn btn-primary"><i class="fas fa-tachometer-alt"></i> Go to Dashboard</a>
                    <a href="products/index.php" class="btn btn-outline"><i class="fas fa-shopping-bag"></i> Shop Now</a>
                <?php else: ?>
                    <a href="auth/register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Get Started</a>
                    <a href="auth/login.php" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
