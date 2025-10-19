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

        body {
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            color: var(--dark);
            overflow-x: hidden;
        }

        /* Navbar */
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
        }
        .nav-link:hover {
            color: var(--primary) !important;
        }

        /* Buttons */
        .btn-login, .btn-hero, .btn-cta {
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-login {
            border: 2px solid var(--primary);
            color: var(--primary);
            padding: 0.5rem 1.5rem;
        }
        .btn-login:hover {
            background: var(--primary);
            color: white !important;
            transform: translateY(-2px);
        }
        .btn-hero {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            background: white;
            color: var(--primary);
            border: none;
        }
        .btn-hero:hover, .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* Hero Section */
        .hero {
            min-height: 90vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: relative;
            overflow: hidden;
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
            text-align: center;
            transition: all 0.3s ease;
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

        /* CTA */
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

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0 1.5rem;
        }
        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
        }
        .footer-links a:hover { color: white; }
    </style>
</head>

<body>
    @php
        use App\Models\LandingPageContent;
        use App\Models\LandingPageCard;

        $hero = LandingPageContent::where('section', 'hero')->first();
        $about = LandingPageContent::where('section', 'about')->first();
        $articles = LandingPageContent::where('section', 'articles')->first();
        $faq = LandingPageContent::where('section', 'faq')->first();
        $stores = LandingPageContent::where('section', 'stores')->first();
        $contact = LandingPageContent::where('section', 'contact')->first();
        $cta = LandingPageContent::where('section', 'cta')->first();

        $aboutCards = LandingPageCard::where('section', 'about')->orderBy('order')->get();
        $articleCards = LandingPageCard::where('section', 'articles')->orderBy('order')->get();
        $storeCards = LandingPageCard::where('section', 'stores')->orderBy('order')->get();
    @endphp

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
                    <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#stores">Stores</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reseller.create') }}">Apply as Supplier</a></li>
                    <li class="nav-item ms-2"><a class="btn btn-login" href="{{ route('login') }}">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1>{{ $hero->title ?? '🐠 Fresh Fish Market' }}</h1>
                    <p>{{ $hero->content ?? 'Discover the freshest seafood delivered straight to your door.' }}</p>
                    <a href="{{ route('login') }}" class="btn btn-hero">
                        <i class="fas fa-shopping-basket me-2"></i>Start Shopping
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="features" id="about">
        <div class="container">
            <div class="section-title">
                <h2>{{ $about->title ?? 'Why Choose Fish Market' }}</h2>
                <p>{{ $about->content ?? 'Quality seafood with unmatched service' }}</p>
            </div>
            <div class="row g-4 justify-content-center">
                @forelse($aboutCards as $card)
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="{{ $card->icon ?? 'fas fa-fish' }}"></i></div>
                            <h4>{{ $card->title }}</h4>
                            <p>{{ $card->content }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No cards added yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Articles Section -->
    <section class="features" id="articles" style="background: var(--light);">
        <div class="container">
            <div class="section-title">
                <h2>{{ $articles->title ?? 'Latest Articles' }}</h2>
                <p>{{ $articles->content ?? 'Learn more about seafood and healthy eating' }}</p>
            </div>
            <div class="row g-4">
                @forelse($articleCards as $card)
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="{{ $card->icon ?? 'fas fa-newspaper' }}"></i></div>
                            <h4>{{ $card->title }}</h4>
                            <p>{{ $card->content }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No article cards added yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="features" id="faq">
        <div class="container">
            <div class="section-title">
                <h2>{{ $faq->title ?? 'Frequently Asked Questions' }}</h2>
                <p>{{ $faq->content ?? 'Everything you need to know' }}</p>
            </div>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            How fresh is your seafood?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            We deliver within 24 hours of catch time — maximum freshness guaranteed.
                        </div>
                    </div>
                </div>
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            How fresh is your seafood?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            We deliver within 24 hours of catch time — maximum freshness guaranteed.
                        </div>
                    </div>
                </div>
                <div class="accordion-item mb-3">
                    <h2 class="accordion-header">
                        <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            How fresh is your seafood?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show">
                        <div class="accordion-body">
                            We deliver within 24 hours of catch time — maximum freshness guaranteed.
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
                <h2 style="color: var(--dark);">{{ $stores->title ?? 'Our Locations' }}</h2>
                <p>{{ $stores->content ?? 'Find a Fish Market store near you' }}</p>
            </div>
            <div class="row g-4">
                @forelse($storeCards as $card)
                    <div class="col-md-4">
                        <div class="feature-card">
                            <h4><i class="{{ $card->icon ?? 'fas fa-map-marker-alt text-primary me-2' }}"></i>{{ $card->title }}</h4>
                            <p class="mb-0">{{ $card->content }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No store cards added yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="features" id="contact">
        <div class="container">
            <div class="section-title">
                <h2>{{ $contact->title ?? 'Get In Touch' }}</h2>
                <p>{{ $contact->content ?? "We'd love to hear from you" }}</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="feature-card">
                        <div class="row g-4 text-center">
                            <div class="col-md-4">
                                <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                                <h5>Phone</h5>
                                <p class="text-muted">+63 123 456 7890</p>
                            </div>
                            <div class="col-md-4">
                                <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                                <h5>Email</h5>
                                <p class="text-muted">info@fishmarket.com</p>
                            </div>
                            <div class="col-md-4">
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

    <!-- Final CTA -->
    <section class="cta">
        <div class="container">
            <div class="cta-box">
                <h2>{{ $cta->title ?? 'Ready to Order Fresh Seafood?' }}</h2>
                <p>{{ $cta->content ?? 'Join thousands of happy customers enjoying premium quality fish.' }}</p>
                <a href="{{ route('login') }}" class="btn btn-cta"><i class="fas fa-user-plus me-2"></i>Get Started Now</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-fish"></i> Fish Market</h5>
                    <p style="color: rgba(255,255,255,0.7);">Your trusted source for fresh, quality seafood delivered to your door.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h6>Quick Links</h6>
                    <div class="footer-links">
                        <a href="#home">Home</a>
                        <a href="#about">About</a>
                        <a href="#articles">Articles</a>
                        <a href="#stores">Stores</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h6>Support</h6>
                    <div class="footer-links">
                        <a href="#faq">FAQ</a>
                        <a href="#contact">Contact</a>
                        <a href="{{ route('reseller.create') }}">Apply as Supplier</a>
                        <a href="{{ route('login') }}">Login</a>
                    </div>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center" style="color: rgba(255,255,255,0.6);">
                <p>&copy; 2025 Fish Market. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
