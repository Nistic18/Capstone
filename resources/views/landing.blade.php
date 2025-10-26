<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fish Market - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero {
    min-height: 90vh;
    display: flex;
    align-items: center;
    color: white;
}
/* Hero Text Animation */
.hero .hero-text {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 1s ease forwards;
    animation-delay: 0.3s; /* Delay before starting */
}

@keyframes fadeInUp {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
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
        /* Custom styles for the landing page map */
#landing-map .leaflet-popup-content-wrapper {
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

#landing-map .custom-marker {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
    border: 3px solid white;
    background: linear-gradient(45deg, #28a745, #218838);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

.popup-content {
    padding: 15px;
    min-width: 250px;
}

.popup-content h6 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.btn-outline-light:hover {
    background-color: rgba(255,255,255,0.2);
}

@media (max-width: 768px) {
    #landing-map {
        height: 400px !important;
    }
}
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
    <!-- Privacy & Terms Modal -->
    <div class="modal fade" id="privacyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-shield-alt text-primary me-2"></i>Privacy Policy & Terms of Service</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary">Data Privacy Policy</h6>
                        <p class="small text-muted">Last updated: October 20, 2025</p>
                        <p>We respect your privacy and are committed to protecting your personal data. This policy explains how we collect, use, and safeguard your information when you visit our website.</p>
                        
                        <h6 class="mt-3 fw-bold">Information We Collect:</h6>
                        <ul class="small">
                            <li>Personal identification information (name, email, phone number, address)</li>
                            <li>Order and transaction details</li>
                            <li>Usage data and cookies for website improvement</li>
                        </ul>

                        <h6 class="mt-3 fw-bold">How We Use Your Information:</h6>
                        <ul class="small">
                            <li>Process and fulfill your orders</li>
                            <li>Communicate with you about products and services</li>
                            <li>Improve our website and customer experience</li>
                            <li>Comply with legal obligations</li>
                        </ul>

                        <h6 class="mt-3 fw-bold">Data Security:</h6>
                        <p class="small">We implement appropriate security measures to protect your personal information from unauthorized access, alteration, or disclosure.</p>
                    </div>

                    <hr>

                    <div class="mt-4">
                        <h6 class="fw-bold text-primary">Terms and Conditions</h6>
                        <p class="small text-muted">Last updated: October 20, 2025</p>
                        <p>By accessing and using Fish Market's website and services, you agree to be bound by these terms and conditions.</p>

                        <h6 class="mt-3 fw-bold">Use of Service:</h6>
                        <ul class="small">
                            <li>You must be at least 18 years old to use our services</li>
                            <li>You agree to provide accurate and complete information</li>
                            <li>You are responsible for maintaining account security</li>
                        </ul>

                        <h6 class="mt-3 fw-bold">Product Information:</h6>
                        <ul class="small">
                            <li>We strive to provide accurate product descriptions and pricing</li>
                            <li>Prices and availability are subject to change without notice</li>
                            <li>Product images are for reference only</li>
                        </ul>

                        <h6 class="mt-3 fw-bold">Orders and Payment:</h6>
                        <ul class="small">
                            <li>All orders are subject to acceptance and availability</li>
                            <li>Payment must be made in full before delivery</li>
                            <li>We reserve the right to cancel or refuse any order</li>
                        </ul>

                        <h6 class="mt-3 fw-bold">Limitation of Liability:</h6>
                        <p class="small">Fish Market shall not be liable for any indirect, incidental, or consequential damages arising from the use of our services.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary px-4" id="acceptBtn">
                        <i class="fas fa-check me-2"></i>I Accept
                    </button>
                </div>
            </div>
        </div>
    </div>
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
<section class="hero" id="home" style="
    background: linear-gradient(135deg, rgba(102,126,234,0.7), rgba(118,75,162,0.7)), 
                url('{{ $hero && $hero->image ? asset('storage/' . $hero->image) : asset('default-hero.jpg') }}') no-repeat center center;
    background-size: cover;
    position: relative;
    padding: 100px 0;
">
    <div class="container">
        <div class="row align-items-center">
            <!-- Hero Text -->
            <div class="col-lg-7 text-white hero-text">
                <h1>{{ $hero->title ?? 'Fish Market' }}</h1>
                <p>{{ $hero->content ?? 'Discover the freshest seafood delivered straight to your door.' }}</p>
                <a href="{{ route('login') }}" class="btn btn-hero">
                    <i class="fas fa-shopping-basket me-2"></i>Start Shopping
                </a>
            </div>

            <!-- 4 Random Hero Products -->
            <div class="col-lg-5">
                <div class="row g-3">
                    @foreach($heroProducts as $product)
                        <div class="col-6">
                            <div class="text-center">
                                <img src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image) : asset('default-hero.jpg') }}" 
                                     class="img-fluid rounded" 
                                     style="border: 2px solid rgba(255,255,255,0.3);"
                                     alt="{{ $product->name }}">
                                <div class="text-white mt-1">
                                    <h6 class="mb-1">{{ $product->name }}</h6>
                                    <p class="mb-1">
                                        {{ $product->description ?? 'No description' }}
                                    </p>
                                    <p class="mb-0">₱{{ number_format($product->price, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
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
            <div class="row g-4 justify-content-center">
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
        <div class="section-title text-center mb-4">
            <h2>{{ $faq->title ?? 'Frequently Asked Questions' }}</h2>
            <p>{{ $faq->content ?? 'Everything you need to know' }}</p>
        </div>

        <div class="row justify-content-center align-items-start">
            <!-- Left Column (FAQ 1-5) -->
            <div class="col-lg-6 mb-4">
                <div class="accordion" id="faqAccordionLeft">
                    <!-- FAQ 1 -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Kung bibili ng maramihan, may discount ba?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                Oo, may special discount kami para sa wholesale o bulk orders. 
                                Mas maraming piraso, mas malaking tipid.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Ano ang pinaka-best seller sa mga suki?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                Ang madalas na hinahanap ng aming mga suki ay danggit, pusit, at espada (swordfish)
                                dahil sa sarap at lutong kapag piniprito.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq3">
                                May free taste o sample pack ba bago mag-bulk order?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                Oo, pwede kang mag-request ng sample pack para matikman muna bago mag-decide bumili ng maramihan.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Paano ko malalaman kung maalat o mild ang alat ng dried fish?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                Naka-indicate sa packaging kung ito ay mild-salted o regular-salted.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq5">
                                Meron bang seasonal dried fish - like mas maraming supply tuwing tag-init?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                Oo, mas abundant ang huli at tuyo tuwing tag-init (summer season), kaya mas mura at mas sariwa ang supply sa panahong iyon.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column (FAQ 6-10) -->
            <div class="col-lg-6 mb-4">
                <div class="accordion" id="faqAccordionRight">
                    <!-- FAQ 6 -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq6">
                                Magkano ang shipping fee at paano ang delivery?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                Ang shipping fee ay depende sa location at weight ng order. Ipapadala namin via trusted courier at may tracking number ka.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 7 -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq7">
                                Pwede bang i-cancel o baguhin ang order pagkatapos mag place?
                            </button>
                        </h2>
                        <div id="faq7" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                Oo, basta within 24 hours after placing the order, kung shipped na, hindi na pwede i-cancel.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 8 -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq8">
                                Gaano katagal bago ma-deliver ang order?
                            </button>
                        </h2>
                        <div id="faq8" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                Usually, 1-3 working days depende sa location.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 9 -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq9">
                                Paano kung nasira o nabuksan ang packaging sa delivery?
                            </button>
                        </h2>
                        <div id="faq9" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                Pwede mong i-report agad sa amin (with photo proof). Kung may damage, papalitan namin o ire-refund depende sa case.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 10 -->
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq10">
                                May return & refund policy ba?
                            </button>
                        </h2>
                        <div id="faq10" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                Oo, covered ang defective, wrong item, o damaged orders.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


    <!-- Stores Section -->
    <section id="our-locations" class="py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="color: #2c3e50;">
                <i class="fas fa-map-marked-alt me-2" style="color: #667eea;"></i>
                Our Locations
            </h2>
            <p class="lead text-muted">Find fresh fish suppliers near you</p>
        </div>

        <!-- Map Card -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header border-0 py-3" style="background: linear-gradient(45deg, #667eea, #764ba2);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-white">
                                <i class="fas fa-store me-2"></i>
                                Fish Supplier Locations
                            </h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-light btn-sm" onclick="getCurrentLocationLanding()" 
                                        style="border-radius: 10px;">
                                    <i class="fas fa-location-arrow me-1"></i>My Location
                                </button>
                                <button class="btn btn-outline-light btn-sm" onclick="resetMapViewLanding()" 
                                        style="border-radius: 10px;">
                                    <i class="fas fa-sync-alt me-1"></i>Reset
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <div id="landing-map" style="height: 500px; position: relative;">
                            <!-- Loading Overlay -->
                            <div id="landing-map-loading" class="position-absolute top-50 start-50 translate-middle" style="z-index: 1000;">
                                <div class="text-center">
                                    <div class="spinner-border text-primary mb-2" role="status"></div>
                                    <p class="text-muted">Loading map...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Map Footer with Stats -->
                    <div class="card-footer border-0 py-3" style="background: #f8f9fa;">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 30px; height: 30px;">
                                        <i class="fas fa-store text-white" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-0 fw-bold" id="supplier-count">0</h6>
                                        <small class="text-muted">Suppliers</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-fish text-primary me-2" style="font-size: 1.5rem;"></i>
                                    <div class="text-start">
                                        <h6 class="mb-0 fw-bold">Fresh</h6>
                                        <small class="text-muted">Daily Catch</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-shipping-fast text-info me-2" style="font-size: 1.5rem;"></i>
                                    <div class="text-start">
                                        <h6 class="mb-0 fw-bold">Fast</h6>
                                        <small class="text-muted">Delivery</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Cards Below Map -->
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; border-left: 4px solid #28a745;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle text-success me-3" style="font-size: 2rem;"></i>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-success">How to Use</h6>
                                        <p class="mb-0 text-muted small">
                                            Click on any green marker to see supplier details and contact information.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; border-left: 4px solid #667eea;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt text-primary me-3" style="font-size: 2rem;"></i>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-primary">Find Nearby</h6>
                                        <p class="mb-0 text-muted small">
                                            Use the "My Location" button to find suppliers closest to you.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                <p class="text-muted">fishmarketnotification@gmail.com</p>
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
       <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Cookie helper functions
        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "expires=" + date.toUTCString();
            document.cookie = name + "=" + value + ";" + expires + ";path=/";
        }

        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for(let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Check if user has already accepted the privacy policy
            const hasAccepted = getCookie('privacy_accepted');

            // Show modal if user hasn't accepted yet
            if (!hasAccepted) {
                const privacyModal = new bootstrap.Modal(document.getElementById('privacyModal'));
                privacyModal.show();
            }

            // Handle accept button click
            document.getElementById('acceptBtn').addEventListener('click', function() {
                // Set cookie to remember acceptance for 15 days
                setCookie('privacy_accepted', 'true', 15);
                const privacyModal = bootstrap.Modal.getInstance(document.getElementById('privacyModal'));
                privacyModal.hide();
            });
        });
let landingMap;
let landingMarkers = [];

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeLandingMap();
});

function initializeLandingMap() {
    // Initialize map centered on Manila
    landingMap = L.map('landing-map').setView([14.5995, 120.9842], 13);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(landingMap);
    
    // Hide loading overlay
    document.getElementById('landing-map-loading').style.display = 'none';
    
    // Load supplier locations from your backend
    loadSupplierLocations();
}

// Custom icon for suppliers
const supplierIcon = L.divIcon({
    html: '<div class="custom-marker"><i class="fas fa-store"></i></div>',
    className: '',
    iconSize: [35, 35],
    iconAnchor: [17, 17]
});

// Function to load supplier locations via AJAX
function loadSupplierLocations() {
    fetch('/api/supplier-locations')
        .then(response => response.json())
        .then(suppliers => {
            let supplierCount = 0;
            
            suppliers.forEach(function(location) {
                if (location.latitude && location.longitude) {
                    const popupContent = `
                        <div class="popup-content">
                            <h6 class="fw-bold">${location.location_name || 'Supplier Location'}</h6>
                            <div class="mb-2">
                                <i class="fas fa-user text-muted me-2"></i>
                                <small>${location.user?.name || 'Unknown'}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-phone text-muted me-2"></i>
                                <small>${location.user?.phone || 'N/A'}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-envelope text-muted me-2"></i>
                                <small>${location.user?.email || 'N/A'}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <small>${location.user?.address || 'No address provided'}</small>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-success">
                                    <i class="fas fa-store me-1"></i>${location.type === 'store' ? 'Store' : 'Supply Point'}
                                </span>
                            </div>
                        </div>
                    `;
                    
                    const marker = L.marker([parseFloat(location.latitude), parseFloat(location.longitude)], { 
                        icon: supplierIcon 
                    }).addTo(landingMap);
                    
                    marker.bindPopup(popupContent, {
                        maxWidth: 300,
                        className: 'custom-popup'
                    });
                    
                    landingMarkers.push(marker);
                    supplierCount++;
                }
            });
            
            // Update supplier count
            document.getElementById('supplier-count').textContent = supplierCount;
            
            // Fit map to show all markers if there are any
            if (landingMarkers.length > 0) {
                const group = new L.featureGroup(landingMarkers);
                landingMap.fitBounds(group.getBounds().pad(0.1));
            }
        })
        .catch(error => {
            console.error('Error loading supplier locations:', error);
            document.getElementById('supplier-count').textContent = '0';
        });
}

// Get current location
function getCurrentLocationLanding() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                landingMap.setView([lat, lng], 14);
                
                // Add temporary marker for user location
                const userIcon = L.divIcon({
                    html: '<div style="width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(45deg, #ffc107, #e0a800); display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 3px 10px rgba(0,0,0,0.3);"><i class="fas fa-map-pin" style="color: #212529;"></i></div>',
                    className: '',
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });
                
                const userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(landingMap);
                userMarker.bindPopup('<strong>Your Location</strong>').openPopup();
            },
            function(error) {
                alert('Unable to get your location. Please enable location services.');
            }
        );
    } else {
        alert('Geolocation is not supported by your browser.');
    }
}

// Reset map view
function resetMapViewLanding() {
    if (landingMarkers.length > 0) {
        const group = new L.featureGroup(landingMarkers);
        landingMap.fitBounds(group.getBounds().pad(0.1));
    } else {
        landingMap.setView([14.5995, 120.9842], 13);
    }
}
    </script>
</body>
</html>
