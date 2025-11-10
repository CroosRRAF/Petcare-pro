<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Petcare Pro</title>
    <meta name="description" content="Get in touch with Petcare Pro for all your pet care needs. Contact us for appointments, inquiries, or emergency services.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="styles/header.css">
    <link rel="stylesheet" href="styles/footer.css">
    <style>
        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .contact-header h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .contact-header p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 60px;
        }

        .contact-form-section h2,
        .contact-info-section h2 {
            color: var(--primary-color);
            margin-bottom: 30px;
        }

        .contact-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            background: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
        }

        .contact-info {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .contact-item i {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-right: 15px;
            margin-top: 5px;
        }

        .contact-item h3 {
            color: #333;
            margin-bottom: 5px;
        }

        .contact-item p {
            color: #666;
            margin: 0;
        }

        .business-hours {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .business-hours h4 {
            color: #333;
            margin-bottom: 15px;
        }

        .business-hours p {
            margin: 5px 0;
            color: #666;
        }

        .emergency-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .emergency-notice h4 {
            color: #856404;
            margin-bottom: 10px;
        }

        .emergency-notice p {
            color: #856404;
            margin: 0;
        }

        @media (max-width: 768px) {
            .contact-content {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .contact-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="contact-container">
        <section class="contact-header">
            <h1><i class="fas fa-envelope"></i> Contact Us</h1>
            <p>We're here to help with all your pet care needs. Reach out to us for appointments, questions, or emergency services.</p>
        </section>

        <section class="contact-content">
            <div class="contact-form-section">
                <h2>Send us a Message</h2>
                <form class="contact-form" action="#" method="post">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone">
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            <div class="contact-info-section">
                <h2>Get in Touch</h2>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Address</h3>
                            <p>123 Pet Care Street<br>Animal District, City 12345<br>Country</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Phone</h3>
                            <p>Main: +1 (555) 123-4567<br>Emergency: +1 (555) 987-6543</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>info@petcarepro.com<br>emergency@petcarepro.com</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h3>Business Hours</h3>
                            <div class="business-hours">
                                <h4>Regular Hours</h4>
                                <p>Monday - Friday: 8:00 AM - 7:00 PM</p>
                                <p>Saturday: 9:00 AM - 5:00 PM</p>
                                <p>Sunday: 10:00 AM - 4:00 PM</p>
                            </div>
                        </div>
                    </div>

                    <div class="emergency-notice">
                        <h4><i class="fas fa-exclamation-triangle"></i> Emergency Services</h4>
                        <p>Available 24/7 for urgent pet care needs. Call our emergency line immediately.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Form submission handling
        document.querySelector('.contact-form').addEventListener('submit', function(e) {
            e.preventDefault();

            // Simple form validation
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#e53e3e';
                    isValid = false;
                } else {
                    field.style.borderColor = '#ddd';
                }
            });

            if (isValid) {
                // In a real application, you would send this to a server
                alert('Thank you for your message! We will get back to you soon.');
                this.reset();
            } else {
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html>
