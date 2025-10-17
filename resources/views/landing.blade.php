<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fish Market - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --dark: #191d21;
            --light: #f8f9fa;
            --accent: #4f5d73;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--dark);
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary) !important;
        }

        .nav-link {
            color: var(--dark) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        .btn-login {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-login:hover {
            background: var(--primary);
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(103, 119, 239, 0.3);
        }

        /* Hero Section */
        .hero {
            min-height: 90vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            animation: fadeInUp 0.8s ease;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            animation: fadeInUp 0.8s ease 0.2s backwards;
        }

        .btn-hero {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            background: white;
            color: var(--primary);
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease 0.4s backwards;
            text-decoration: none;
            display: inline-block;
        }

        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: var(--primary);
        }

        /* Features Section */
        .features {
            padding: 5rem 0;
            background: white;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .section-title p {
            color: var(--accent);
            font-size: 1.1rem;
        }

        .feature-card {
            padding: 2.5rem;
            border-radius: 20px;
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.8rem;
        }

        .feature-card h4 {
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--accent);
            line-height: 1.8;
        }

        /* CTA Section */
        .cta {
            padding: 5rem 0;
            background: var(--light);
        }

        .cta-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 30px;
            padding: 4rem;
            text-align: center;
            color: white;
        }

        .cta-box h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .cta-box p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .btn-cta {
            background: white;
            color: var(--primary);
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cta:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: var(--primary);
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0 1.5rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
            display: block;
            margin-bottom: 0.5rem;
        }

        .footer-links a:hover {
            color: white;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .cta-box {
                padding: 2.5rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('landing') }}">
                <i class="fas fa-fish"></i> Fish Market
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#articles">Articles</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">FAQ's</a></li>
                    <li class="nav-item"><a class="nav-link" href="#stores">Stores</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reseller.create') }}">Apply as Supplier</a></li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-login" href="{{ route('login') }}">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 hero-content">
                    <h1>🐠 Fresh Fish Market</h1>
                    <p>Discover the freshest seafood delivered straight to your door. Join thousands of satisfied customers enjoying premium quality fish daily.</p>
                    <a href="{{ route('login') }}" class="btn btn-hero">
                        <i class="fas fa-shopping-basket me-2"></i>Start Shopping
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="about">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose Fish Market</h2>
                <p>Quality seafood with unmatched service</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-fish"></i>
                        </div>
                        <h4>Premium Quality</h4>
                        <p>We source only the freshest seafood from trusted suppliers. Every fish meets our strict quality standards.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h4>Fast Delivery</h4>
                        <p>Same-day delivery available. Your seafood arrives fresh and properly packaged at your doorstep.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Safe & Secure</h4>
                        <p>Your transactions are protected with industry-leading security. Shop with confidence.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Articles Section -->
    <section class="features" id="articles" style="background: var(--light);">
        <div class="container">
            <div class="section-title">
                <h2>Latest Articles</h2>
                <p>Learn more about seafood and healthy eating</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <h4>Health Benefits</h4>
                        <p>Discover the amazing health benefits of including fish in your regular diet.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h4>Cooking Tips</h4>
                        <p>Expert tips and recipes to prepare delicious seafood dishes at home.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h4>Sustainability</h4>
                        <p>Learn about our commitment to sustainable fishing and ocean conservation.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="features" id="faq">
        <div class="container">
            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
                <p>Everything you need to know</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item border-0 mb-3" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How fresh is your seafood?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    All our seafood is delivered within 24 hours of being caught. We work directly with local fishermen to ensure maximum freshness.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 mb-3" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What are your delivery hours?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We deliver daily from 8 AM to 8 PM. Same-day delivery is available for orders placed before 2 PM.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 mb-3" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Do you offer refunds?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, we offer a 100% satisfaction guarantee. If you're not happy with your order, contact us within 24 hours for a full refund.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stores Section -->
    <section class="cta" id="stores">
        <div class="container">
            <div class="section-title">
                <h2 style="color: var(--dark);">Our Locations</h2>
                <p>Find a Fish Market store near you</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <h4><i class="fas fa-map-marker-alt text-primary me-2"></i>Manila</h4>
                        <p class="mb-0">123 Seafood Street, Manila City</p>
                        <p class="text-muted">Open: 6 AM - 8 PM Daily</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <h4><i class="fas fa-map-marker-alt text-primary me-2"></i>Makati</h4>
                        <p class="mb-0">456 Fresh Market Ave, Makati</p>
                        <p class="text-muted">Open: 6 AM - 8 PM Daily</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <h4><i class="fas fa-map-marker-alt text-primary me-2"></i>Quezon City</h4>
                        <p class="mb-0">789 Ocean Blvd, Quezon City</p>
                        <p class="text-muted">Open: 6 AM - 8 PM Daily</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="features" id="contact">
        <div class="container">
            <div class="section-title">
                <h2>Get In Touch</h2>
                <p>We'd love to hear from you</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="feature-card">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                                <h5>Phone</h5>
                                <p class="text-muted">+63 123 456 7890</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                                <h5>Email</h5>
                                <p class="text-muted">info@fishmarket.com</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                                <h5>Hours</h5>
                                <p class="text-muted">Mon-Sun: 6 AM - 8 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-box">
                <h2>Ready to Order Fresh Seafood?</h2>
                <p>Join thousands of happy customers enjoying premium quality fish</p>
                <a href="{{ route('login') }}" class="btn btn-cta">
                    <i class="fas fa-user-plus me-2"></i>Get Started Now
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3"><i class="fas fa-fish"></i> Fish Market</h5>
                    <p style="color: rgba(255,255,255,0.7);">Your trusted source for fresh, quality seafood delivered to your door.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="mb-3">Quick Links</h6>
                    <div class="footer-links">
                        <a href="#home">Home</a>
                        <a href="#about">About Us</a>
                        <a href="#articles">Articles</a>
                        <a href="#stores">Stores</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="mb-3">Support</h6>
                    <div class="footer-links">
                        <a href="#faq">FAQ's</a>
                        <a href="#contact">Contact Us</a>
                        <a href="{{ route('reseller.create') }}">Apply as Supplier</a>
                        <a href="{{ route('login') }}">Login</a>
                    </div>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 2rem 0 1rem;">
            <div class="text-center" style="color: rgba(255,255,255,0.6);">
                <p>&copy; 2025 Fish Market. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>