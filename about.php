<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Petcare Pro</title>
    <meta name="description" content="Learn about Petcare Pro - your trusted partner for comprehensive pet care services and premium pet products.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <style>
        .about-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .about-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .about-header h1 {
            font-size: 2.5rem;
            color: #3c91e6;
            margin-bottom: 20px;
        }

        .about-header p {
            font-size: 1.2rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 60px;
        }

        .about-text h2 {
            color: #3c91e6;
            margin-bottom: 20px;
        }

        .about-text p {
            line-height: 1.6;
            margin-bottom: 20px;
            color: #555;
        }

        .about-image {
            text-align: center;
        }

        .about-image img {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
        }

        .feature-card i {
            font-size: 3rem;
            color: #3c91e6;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .feature-card p {
            color: #666;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .about-content {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .about-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="about-container">
        <section class="about-header">
            <h1><i class="fas fa-paw"></i> About Petcare Pro</h1>
            <p>Your trusted partner in providing exceptional care for your beloved pets. We combine expertise, compassion, and cutting-edge services to ensure your pets live happy, healthy lives.</p>
        </section>

        <section class="about-content">
            <div class="about-text">
                <h2>Our Mission</h2>
                <p>At Petcare Pro, we believe that pets are family. Our mission is to provide comprehensive, high-quality care services and products that enhance the lives of pets and bring peace of mind to their owners.</p>

                <h2>Our Story</h2>
                <p>Founded with a passion for animal welfare, Petcare Pro has grown from a small local pet care service into a comprehensive pet care solution provider. Our team of experienced veterinarians, groomers, and pet care specialists work together to offer the best possible care for your pets.</p>

                <h2>Why Choose Us?</h2>
                <p>We pride ourselves on our commitment to excellence, personalized service, and use of modern, humane techniques. Every pet that walks through our doors receives individual attention and care tailored to their specific needs.</p>
            </div>

            <div class="about-image">
                <img src="assets/images/placeholder-about.jpg" alt="Happy pets at Petcare Pro" onerror="this.style.display='none'">
                <div style="width: 100%; height: 300px; background: linear-gradient(135deg, #3c91e6, #2a6ebb); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                    <div style="text-align: center;">
                        <i class="fas fa-paw" style="font-size: 4rem; margin-bottom: 20px;"></i>
                        <h3>Quality Pet Care</h3>
                    </div>
                </div>
            </div>
        </section>

        <section class="features">
            <div class="feature-card">
                <i class="fas fa-heartbeat"></i>
                <h3>Expert Care</h3>
                <p>Our certified professionals provide the highest standard of veterinary and grooming services using state-of-the-art equipment.</p>
            </div>

            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Safe Environment</h3>
                <p>We maintain strict hygiene standards and create a stress-free environment for your pets during their visits.</p>
            </div>

            <div class="feature-card">
                <i class="fas fa-clock"></i>
                <h3>Convenient Hours</h3>
                <p>Extended hours and emergency services ensure your pet gets the care they need when they need it most.</p>
            </div>

            <div class="feature-card">
                <i class="fas fa-star"></i>
                <h3>Premium Products</h3>
                <p>We offer a carefully curated selection of high-quality pet food, toys, and supplies from trusted brands.</p>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
