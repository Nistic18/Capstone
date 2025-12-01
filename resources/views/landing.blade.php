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
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

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
            animation-delay: 0.3s;
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
            --primary: #0bb364;
            --secondary: #0bb364;
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
        .btn-login, .btn-hero, .btn-cta, .btn-supplier {
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
        
        .btn-supplier {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-hero:hover, .btn-cta:hover, .btn-supplier:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .btn-supplier:hover {
            background: white;
            color: var(--primary);
        }

        /* Hero Section */
        .hero {
            min-height: 90vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #0bb364 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #0bb364 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #0bb364 100%);
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
        
        .footer-links a:hover { 
            color: white; 
        }
        
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
        
        .justify-text {
            text-align: justify;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        @media (max-width: 576px) {
            .hero-buttons {
                flex-direction: column;
            }
            .hero-buttons .btn {
                width: 100%;
            }
        }
        
        /* Hero Product Images - Fixed Size */
        .hero-product-image-wrapper {
            width: 100%;
            height: 180px;
            overflow: hidden;
            border-radius: 15px;
            border: 3px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .hero-product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
        }
        
        .hero-product-image:hover {
            transform: scale(1.05);
        }
        
        @media (max-width: 768px) {
            .hero-product-image-wrapper {
                height: 150px;
            }
        }
        
        .map-search-container {
            position: relative;
        }

        .map-search-input {
            border-radius: 25px;
            border: 2px solid #e0e0e0;
            padding: 0.6rem 2.5rem 0.6rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .map-search-input:focus {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
            outline: none;
        }

        .map-search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(45deg, #667eea, #0bb364);
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .map-search-btn:hover {
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .map-search-btn i {
            color: white;
            font-size: 0.9rem;
        }

        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            margin-top: 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .search-suggestions.active {
            display: block;
        }

        .suggestion-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .suggestion-item:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.1), rgba(11, 179, 100, 0.1));
            padding-left: 20px;
        }

        .suggestion-item:last-child {
            border-bottom: none;
            border-radius: 0 0 15px 15px;
        }

        .suggestion-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(45deg, #667eea, #0bb364);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .suggestion-icon i {
            color: white;
            font-size: 0.8rem;
        }

        .suggestion-text {
            flex: 1;
        }

        .suggestion-title {
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            font-size: 0.9rem;
        }

        .suggestion-subtitle {
            color: #7f8c8d;
            font-size: 0.75rem;
            margin: 0;
        }

        .search-loading {
            padding: 16px;
            text-align: center;
            color: #7f8c8d;
        }

        .search-loading .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
        }

        .map-controls-wrapper {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .map-search-container {
                width: 100%;
                margin-bottom: 8px;
            }
            
            .map-controls-wrapper {
                flex-direction: column;
                width: 100%;
            }
            
            .map-controls-wrapper > * {
                width: 100%;
            }
        }
        
        body, 
        h1, h2, h3, h4, h5, h6, 
        p, span, a, div, input, select, button, label {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
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
                    <li class="nav-item"><a class="nav-link" href="#stores">Stores</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                    <li class="nav-item ms-2"><a class="btn btn-login" href="{{ route('login') }}">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home" style="
        background: linear-gradient(135deg, rgba(102, 216, 130, 0.7), rgba(40, 156, 88, 0.7)), 
                    url('{{ $hero && $hero->image ? asset('storage/' . $hero->image) : asset('default-hero.jpg') }}') no-repeat center center;
        background-size: cover;
        position: relative;
        padding: 100px 0;
    ">
        <div class="container">
            <div class="row align-items-center">
                <!-- Hero Text -->
                <div class="col-lg-7 text-white hero-text">
                    <h1 style="font-size: 3rem; font-weight: 800;">{{ $hero->title ?? 'Fish Market' }}</h1>
                    <p style="font-size: 1.5rem; font-weight: 500;"> {{ $hero->content ?? 'Discover the freshest seafood delivered straight to your door.' }}</p>
                    <div class="hero-buttons">
                        <a href="{{ route('login') }}" class="btn btn-hero">
                            <i class="fas fa-shopping-basket me-2"></i>Start Shopping
                        </a>
                        <a href="{{ route('reseller.create') }}" class="btn btn-supplier">
                            <i class="fas fa-store me-2"></i>Apply as Supplier
                        </a>
                    </div>
                </div>

                <!-- 4 Random Hero Products -->
                <div class="col-lg-5">
                    <div class="row g-3">
                        @foreach($heroProducts as $product)
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="hero-product-image-wrapper">
                                        <img src="{{ $product->images->first() ? asset('storage/' . $product->images->first()->image) : asset('default-hero.jpg') }}" 
                                             class="hero-product-image" 
                                             alt="{{ $product->name }}">
                                    </div>
                                    <div class="text-white mt-2 justify-text">
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <p class="mb-1 small">
                                            {{ Str::limit($product->description ?? 'No description', 60) }}
                                        </p>
                                        <p class="mb-0 fw-bold">₱{{ number_format($product->price, 2) }}</p>
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
                <h2>{{ $about->title ?? 'About Dried FishMart' }}</h2>
                <p>{{ $about->content ?? 'Connecting communities through quality dried fish trade' }}</p>
            </div>

            <!-- Mission & Vision -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="feature-card h-100" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(11, 179, 100, 0.05));">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #667eea 0%, #0bb364 100%);">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4 class="mb-3">Our Mission</h4>
                        <p class="text-start" style="line-height: 1.8;">
                            Dried FishMart is dedicated to improving the traditional dried fish trade by creating a safe, reliable, and easy-to-use online platform with real-time inventory updates, clear pricing, location features, and simple communication tools. Our mission is to build stronger connections among suppliers, resellers, and buyers, make trading faster and more organized, and support smart decision-making while helping the local industry go digital and uplifting the livelihood of the Rosario, Cavite community.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-card h-100" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(11, 179, 100, 0.05));">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #667eea 0%, #0bb364 100%);">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h4 class="mb-3">Our Vision</h4>
                        <p class="text-start" style="line-height: 1.8;">
                            To establish Dried FishMart as a trusted community-based online marketplace that helps transform the dried fish industry of Rosario, Cavite into a modern, inclusive, and sustainable trade where suppliers, resellers, and buyers are better connected, supported by technology, and given wider opportunities to grow and succeed.
                        </p>
                    </div>
                </div>
            </div>

            <!-- History Section -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="feature-card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(11, 179, 100, 0.05));">
                        <div class="feature-icon mx-auto mb-4" style="background: linear-gradient(135deg, #667eea 0%, #0bb364 100%);">
                            <i class="fas fa-history"></i>
                        </div>
                        <h4 class="mb-4">History of Dried Fish in Rosario, Cavite</h4>
                        <div class="text-start" style="line-height: 1.9; text-align: justify;">
                            <p class="mb-3">
                                Rosario, which was once known as <strong>Salinas</strong>, has long been recognized as a fishing community along Manila Bay. Because of the plentiful daily catch, residents developed traditional methods to preserve fish so it could last longer without refrigeration. The most common techniques were <strong>salting and sun-drying (daing/tuyo)</strong> and <strong>smoking (tinapa)</strong>.
                            </p>
                            <p class="mb-3">
                                Through the years, Rosario became widely known for its distinct smoked fish called <strong>"Tinapang Salinas"</strong>, usually made from tamban, bangus, or galunggong. Its unique flavor and quality earned the town recognition as the <strong>"Smoked Fish Capital of Cavite."</strong>
                            </p>
                            <p class="mb-3">
                                Despite Cavite's industrial growth, many families continue the fish-curing tradition today. The practices of cleaning, salting, drying, and smoking fish over coconut husks or native wood have been passed down for generations. To honor this cultural heritage, Rosario celebrates the <strong>Tinapa Festival</strong> every October, showcasing the town's specialty product and the vital role of fishing and fish-processing in the lives of its people.
                            </p>
                            <p class="mb-0">
                                Today, dried and smoked fish from Rosario remain an essential part of local markets in Cavite and nearby provinces—providing livelihood, strengthening community identity, and preserving its rich fishing history.
                            </p>
                        </div>
                        <div class="mt-4 text-center">
                            <span class="badge bg-success px-4 py-2" style="font-size: 0.95rem;">
                                <i class="fas fa-award me-2"></i>Smoked Fish Capital of Cavite
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Cards (if any exist in database) -->
            {{-- <div class="section-title mt-5">
                <h2>Why Choose Dried FishMart</h2>
                <p>Quality seafood with unmatched service</p>
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
                    <!-- Default feature cards if none exist in database -->
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-fish"></i></div>
                            <h4>Fresh Quality</h4>
                            <p>Traditional preservation methods passed down through generations</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-handshake"></i></div>
                            <h4>Community-Based</h4>
                            <p>Supporting local suppliers and empowering the Rosario community</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="fas fa-laptop"></i></div>
                            <h4>Modern Platform</h4>
                            <p>Easy-to-use online marketplace with real-time updates</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section> --}}

<!-- Latest Articles Section -->
    @if($latestPost)
    <section id="articles" class="py-5" style="background: white;">
        <div class="container">
            <div class="section-title">
                <h2>{{ $articles->title ?? 'Updates and Announcement' }}</h2>
                <p>{{ $articles->content ?? 'Stay updated with news and updates' }}</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-body">
                            <h5 class="fw-bold mb-2">{{ $latestPost->title }}</h5>
                            <p style="text-align: justify;">{{ Str::limit($latestPost->content, 1000) }}
                                @if(strlen($latestPost->content) > 1000)
                                    <a href="{{ route('newsfeedsupplier.show', $latestPost) }}" class="text-primary">Read more</a>
                                @endif
                            </p>
                            @if($latestPost->image)
                                <img src="{{ asset('storage/' . $latestPost->image) }}" class="img-fluid rounded mb-2" alt="Post image">
                            @endif
                            <small class="text-muted">
                                By {{ $latestPost->user->name }} • {{ $latestPost->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Fish Species Guide Section -->
    <section id="fish-guide" class="py-5" style="background: #f8f9fa;">
        <div class="container">
            <div class="section-title">
                <h2><i class="fas fa-book-open me-2"></i>Fish Species Guide</h2>
                <p>Learn about common dried fish varieties and their seasonal availability</p>
            </div>

            <!-- Fish Species Accordion -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="accordion" id="fishSpeciesAccordion">
                        
                        <!-- Tawilis -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish1">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Tawilis (Freshwater Sardine)</strong>
                                </button>
                            </h2>
                            <div id="fish1" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Tawilis is a small, silvery fish found only in Taal Lake. It evolved from marine sardines that adapted to freshwater after the lake separated from the sea. Known for its delicate texture and mild flavor, Tawilis is the world's only freshwater sardine—a rare result of volcanic history.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> Populations shift with seasonal lake conditions. A closed season allows Tawilis to spawn, followed by an open season with regulated fishing to help sustain the species.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tamban -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish2">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Tamban (Round Scad)</strong>
                                </button>
                            </h2>
                            <div id="fish2" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Tamban is a small, streamlined fish commonly found in Philippine seas. Its silver body and thin golden stripe give it a lively appearance. It forms large schools near the surface and has a mild, slightly oily taste.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> More abundant during cooler months when plankton increases. Numbers decrease during warm months, affecting supply for markets and processing.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hasa-Hasa -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish3">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Hasa-Hasa (Short Mackerel)</strong>
                                </button>
                            </h2>
                            <div id="fish3" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Hasa-hasa is a medium-sized fish with a silver-blue body and mild flavor. Common in coastal areas, it is ideal for frying, grilling, or simmering. It is a dependable catch for many local communities.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> More common during cooler months when food is plentiful. Numbers drop during warmer seasons, affecting supply consistency.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alumahan -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish4">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Alumahan (Mackerel)</strong>
                                </button>
                            </h2>
                            <div id="fish4" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Alumahan is a medium-sized striped mackerel with a firm texture and slightly rich taste. It is commonly prepared by frying, grilling, or simmering and is a staple food for many coastal households.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> Easier to catch in cooler months when food is abundant. Warmer months lead to decreased catch and unstable market supply.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bisugo -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish5">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Bisugo (Threadfin Bream)</strong>
                                </button>
                            </h2>
                            <div id="fish5" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Bisugo is a small, pinkish fish with soft flesh and a light, delicate flavor. It is commonly used in simple everyday Filipino dishes.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> More abundant in cooler months; supply decreases during warmer seasons, affecting market availability.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Danggit -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish6">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Danggit (Rabbitfish)</strong>
                                </button>
                            </h2>
                            <div id="fish6" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Danggit is a small, flat fish popular fresh or dried. When dried, it becomes crispy when fried, making it a favorite breakfast dish in many Filipino households.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> Plentiful during the dry season when sea conditions and drying conditions are ideal. Rainy season reduces catch and drying production.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Espada -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish7">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Espada (Beltfish)</strong>
                                </button>
                            </h2>
                            <div id="fish7" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Espada is a long, slender, bright silver fish with soft white meat. Commonly found in deeper waters, it is used in frying, grilling, and soups due to its mild and clean flavor.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> Moves closer to coastal fishing areas during cooler months, increasing catch. Supply drops during warmer months.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Salay-Salay -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish8">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Salay-Salay (Slipmouth Fish)</strong>
                                </button>
                            </h2>
                            <div id="fish8" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Salay-salay is a slender fish known for its flexible, extendable mouth. It has soft flesh and a gentle flavor, often cooked by frying, steaming, or adding to soups.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> More abundant in cooler months. Catch decreases in warmer periods, reducing market availability.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hipon -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish9">
                                    <i class="fas fa-shrimp me-3 text-primary"></i>
                                    <strong>Hipon (Shrimp)</strong>
                                </button>
                            </h2>
                            <div id="fish9" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Hipon, or shrimp, is a small shellfish found in coastal and freshwater areas. Its soft, slightly sweet meat is used in soups, stir-fried dishes, and fried snacks, making it a vital catch for many fishers.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> Easier to catch during dry seasons when water is calmer and clearer. Rainy seasons reduce supply due to water changes.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Takla -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish10">
                                    <i class="fas fa-shrimp me-3 text-primary"></i>
                                    <strong>Takla (Small Shrimp)</strong>
                                </button>
                            </h2>
                            <div id="fish10" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Takla is a tiny shrimp variety commonly used in Filipino cooking. Despite its small size, it adds flavor to dishes and is often dried for longer storage.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> Most available during dry season when harvesting conditions are optimal. Supply becomes limited during rainy months.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pusit -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish11">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Pusit (Squid)</strong>
                                </button>
                            </h2>
                            <div id="fish11" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Pusit, or squid, is a popular seafood with tender, white meat and a mild, slightly sweet flavor. It can be grilled, stuffed, fried, or added to stews. When dried, it becomes a chewy, flavorful snack or ingredient that's easy to store and transport.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> Peak catches occur during cooler months when squid migrate closer to shore. Warmer months see reduced availability and higher prices.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tunsoy -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish12">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Tunsoy (Sardine)</strong>
                                </button>
                            </h2>
                            <div id="fish12" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Tunsoy is a small, oily fish similar to sardines, commonly found in Philippine waters. Rich in omega-3 fatty acids, it's often dried or canned and is a nutritious, affordable protein source for many Filipino families.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> More abundant during cooler months when schools are larger. Supply decreases in warmer seasons, affecting processing and market availability.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Galunggong -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish13">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Galunggong / GG (Round Scad)</strong>
                                </button>
                            </h2>
                            <div id="fish13" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Galunggong, commonly called GG, is one of the most popular and affordable fish in the Philippines. This silvery, medium-sized round scad has a mild flavor and firm texture, making it perfect for frying, grilling, or stewing. It's a staple in Filipino households and markets nationwide.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> Peak season is during the cooler months (November to February) when large schools migrate. During warm months, supply drops significantly, often leading to price increases and importation.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dilis -->
                        <div class="accordion-item mb-3 border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fish14">
                                    <i class="fas fa-fish me-3 text-primary"></i>
                                    <strong>Dilis (Anchovy)</strong>
                                </button>
                            </h2>
                            <div id="fish14" class="accordion-collapse collapse" data-bs-parent="#fishSpeciesAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3">Dilis, or anchovies, are tiny, silvery fish packed with flavor and nutrition. Often sun-dried and salted, they become crispy when fried and are a beloved breakfast staple or snack. They're also used to add umami depth to sauces and dishes throughout Filipino cuisine.</p>
                                    <div class="alert alert-info mb-0">
                                        <strong><i class="fas fa-calendar-alt me-2"></i>Seasonal Topic:</strong> Best harvested during dry season when drying conditions are ideal. Rainy months reduce both catch and drying quality, limiting market supply and affecting prices.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
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
    <section id="stores" class="py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold" style="color: #2c3e50;">
                    <i class="fas fa-map-marked-alt me-2" style="color: #667eea;"></i>
                    Our Locations
                </h2>
                <p class="lead text-muted">Find fresh fish suppliers near you</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                        <div class="card-header border-0 py-3" style="background: linear-gradient(45deg, #667eea, #0bb364);">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold text-white">
                                    <i class="fas fa-store me-2"></i>
                                    Fish Supplier Locations
                                </h5>
                                <div class="map-controls-wrapper">
                                <!-- Enhanced Search Input -->
                                <div class="map-search-container" style="width: 250px;">
                                    <input 
                                        type="text" 
                                        id="map-search-input" 
                                        class="form-control map-search-input" 
                                        placeholder="Search location in PH..."
                                        autocomplete="off"
                                    >
                                    <button class="map-search-btn" onclick="performSearch()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <div id="search-suggestions" class="search-suggestions"></div>
                                </div>
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
                                <div id="landing-map-loading" class="position-absolute top-50 start-50 translate-middle" style="z-index: 1000;">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary mb-2" role="status"></div>
                                        <p class="text-muted">Loading map...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
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
                                    <i class="fas fa-search text-primary me-3" style="font-size: 2rem;"></i>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-primary">Search Locations</h6>
                                        <p class="mb-0 text-muted small">
                                            Type any city or place in the Philippines to search.
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
                <h2>{{ $contact->title ?? 'We’re Here to Listen' }}</h2>
                <p>{{ $contact->content ?? "If you have concerns, complaints, or suggestions, you may reach us through the following channels. Our team is committed to assisting you as quickly as possible." }}</p>
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
// Complete improved map script with comprehensive error handling
// Replace your existing script section with this

let landingMap;
let landingMarkers = [];
let currentSearchMarker;
let searchTimeout;

// Initialize map when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing map...');
    initializeLandingMap();
});

function initializeLandingMap() {
    try {
        // Initialize map centered on Philippines
        landingMap = L.map('landing-map').setView([12.8797, 121.7740], 6);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(landingMap);

        // Hide loading indicator
        const loadingEl = document.getElementById('landing-map-loading');
        if (loadingEl) {
            loadingEl.style.display = 'none';
        }

        console.log('Map initialized successfully');

        // Load supplier locations
        loadSupplierLocations();
    } catch (error) {
        console.error('Error initializing map:', error);
        showMapError('Failed to initialize map. Please refresh the page.');
    }
}

// Custom marker icon
const supplierIcon = L.divIcon({
    html: '<div class="custom-marker"><i class="fas fa-store"></i></div>',
    className: '',
    iconSize: [35, 35],
    iconAnchor: [17, 17]
});

function loadSupplierLocations() {
    console.log('📍 Loading supplier locations...');
    console.log('🌐 API Endpoint:', window.location.origin + '/api/supplier-locations');
    
    // Show loading state
    updateSupplierCount('Loading...');
    
    fetch('/api/supplier-locations', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('📥 Response received');
        console.log('Status:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.json();
    })
    .then(suppliers => {
        console.log('✅ Data parsed successfully');
        console.log('📦 Raw data:', suppliers);
        console.log('📊 Number of locations:', suppliers.length);
        
        if (!Array.isArray(suppliers)) {
            throw new Error('Expected array of suppliers, got: ' + typeof suppliers);
        }
        
        // Clear existing markers
        console.log('🧹 Clearing existing markers...');
        landingMarkers.forEach(marker => landingMap.removeLayer(marker));
        landingMarkers = [];
        
        let validCount = 0;
        let invalidCount = 0;
        
        suppliers.forEach((location, index) => {
            console.log(`\n🔍 Processing location ${index + 1}:`, {
                id: location.id,
                name: location.location_name,
                lat: location.latitude,
                lng: location.longitude,
                user: location.user?.name
            });
            
            const lat = parseFloat(location.latitude);
            const lng = parseFloat(location.longitude);
            
            // Validate coordinates
            if (isNaN(lat) || isNaN(lng)) {
                console.warn(`⚠️ Invalid coordinates (NaN):`, { lat, lng, original: location });
                invalidCount++;
                return;
            }
            
            if (lat === 0 && lng === 0) {
                console.warn(`⚠️ Zero coordinates:`, location.location_name);
                invalidCount++;
                return;
            }
            
            // Check if coordinates are within reasonable bounds
            if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                console.warn(`⚠️ Coordinates out of bounds:`, { lat, lng });
                invalidCount++;
                return;
            }
            
            // Create popup content
            const popupContent = `
                <div class="popup-content">
                    <h6 class="fw-bold">${escapeHtml(location.location_name || 'Supplier Location')}</h6>
                    <div class="mb-2">
                        <i class="fas fa-user text-muted me-2"></i>
                        <small>${escapeHtml(location.user?.name || 'Unknown')}</small>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-phone text-muted me-2"></i>
                        <small>${escapeHtml(location.user?.phone || 'N/A')}</small>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-envelope text-muted me-2"></i>
                        <small>${escapeHtml(location.user?.email || 'N/A')}</small>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        <small>${escapeHtml(location.user?.address || 'No address provided')}</small>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-success">
                            <i class="fas fa-store me-1"></i>${location.type === 'store' ? 'Store' : 'Supply Point'}
                        </span>
                    </div>
                </div>
            `;
            
            try {
                const marker = L.marker([lat, lng], { 
                    icon: supplierIcon,
                    title: location.location_name
                }).addTo(landingMap);
                
                marker.bindPopup(popupContent, {
                    maxWidth: 300,
                    className: 'custom-popup'
                });
                
                landingMarkers.push(marker);
                validCount++;
                console.log(`✅ Marker ${validCount} created successfully at [${lat}, ${lng}]`);
            } catch (markerError) {
                console.error('❌ Error creating marker:', markerError);
                invalidCount++;
            }
        });
        
        // Log summary
        console.log('\n📊 Summary:');
        console.log(`✅ Valid markers: ${validCount}`);
        console.log(`❌ Invalid/skipped: ${invalidCount}`);
        console.log(`📍 Total markers on map: ${landingMarkers.length}`);
        
        // Update UI
        updateSupplierCount(validCount);
        
        // Fit map to markers
        if (landingMarkers.length > 0) {
            const group = new L.featureGroup(landingMarkers);
            landingMap.fitBounds(group.getBounds().pad(0.1));
            console.log('🗺️ Map bounds adjusted to show all markers');
        } else {
            console.warn('⚠️ No valid markers to display');
            showNoDataMessage();
        }
    })
    .catch(error => {
        console.error('❌ Error loading supplier locations:', error);
        updateSupplierCount(0);
        showMapError(`Failed to load locations: ${error.message}`);
    });
}

function updateSupplierCount(count) {
    const countEl = document.getElementById('supplier-count');
    if (countEl) {
        countEl.textContent = count;
    }
}

function showMapError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger position-absolute top-50 start-50 translate-middle';
    errorDiv.style.zIndex = '1000';
    errorDiv.style.maxWidth = '80%';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        ${message}
        <button type="button" class="btn-close float-end" onclick="this.parentElement.remove()"></button>
    `;
    document.getElementById('landing-map').appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 5000);
}

function showNoDataMessage() {
    const noDataDiv = document.createElement('div');
    noDataDiv.className = 'alert alert-info position-absolute top-50 start-50 translate-middle text-center';
    noDataDiv.style.zIndex = '1000';
    noDataDiv.style.maxWidth = '80%';
    noDataDiv.innerHTML = `
        <i class="fas fa-info-circle me-2"></i>
        <strong>No supplier locations available yet.</strong><br>
        <small>Suppliers can add their locations through their dashboard.</small>
    `;
    document.getElementById('landing-map').appendChild(noDataDiv);
    setTimeout(() => noDataDiv.remove(), 5000);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getCurrentLocationLanding() {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser.');
        return;
    }
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            console.log('📍 User location:', { lat, lng });
            landingMap.setView([lat, lng], 14);
            
            const userIcon = L.divIcon({
                html: `<div style="width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(45deg, #ffc107, #e0a800); display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 3px 10px rgba(0,0,0,0.3);">
                    <i class="fas fa-map-pin" style="color: #212529;"></i>
                </div>`,
                className: '',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
            
            const userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(landingMap);
            userMarker.bindPopup('<strong>Your Location</strong>').openPopup();
        },
        function(error) {
            console.error('Geolocation error:', error);
            alert('Unable to get your location. Please enable location services.');
        }
    );
}

function resetMapViewLanding() {
    if (landingMarkers.length > 0) {
        const group = new L.featureGroup(landingMarkers);
        landingMap.fitBounds(group.getBounds().pad(0.1));
    } else {
        landingMap.setView([12.8797, 121.7740], 6); // Center on Philippines
    }
}

// Search functionality
document.getElementById('map-search-input')?.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
        document.getElementById('search-suggestions').classList.remove('active');
        return;
    }
    
    searchTimeout = setTimeout(() => searchLocations(query), 300);
});

document.getElementById('map-search-input')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.map-search-container')) {
        document.getElementById('search-suggestions')?.classList.remove('active');
    }
});

function searchLocations(query) {
    const suggestionsDiv = document.getElementById('search-suggestions');
    if (!suggestionsDiv) return;
    
    suggestionsDiv.innerHTML = `
        <div class="search-loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    suggestionsDiv.classList.add('active');
    
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=ph&limit=5`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                displaySuggestions(data);
            } else {
                suggestionsDiv.innerHTML = `
                    <div class="suggestion-item">
                        <div class="suggestion-icon">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="suggestion-text">
                            <p class="suggestion-title">No results found</p>
                            <p class="suggestion-subtitle">Try a different location</p>
                        </div>
                    </div>
                `;
            }
        })
        .catch(err => {
            console.error('Search error:', err);
            suggestionsDiv.innerHTML = `
                <div class="suggestion-item">
                    <div class="suggestion-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="suggestion-text">
                        <p class="suggestion-title">Error searching</p>
                        <p class="suggestion-subtitle">Please try again</p>
                    </div>
                </div>
            `;
        });
}

function displaySuggestions(locations) {
    const suggestionsDiv = document.getElementById('search-suggestions');
    if (!suggestionsDiv) return;
    
    const icons = {
        'city': 'fas fa-city',
        'town': 'fas fa-building',
        'village': 'fas fa-home',
        'municipality': 'fas fa-map-signs',
        'province': 'fas fa-map',
        'region': 'fas fa-globe-asia'
    };
    
    suggestionsDiv.innerHTML = locations.map(location => {
        const icon = icons[location.type] || 'fas fa-map-marker-alt';
        const name = escapeHtml(location.display_name);
        
        return `
            <div class="suggestion-item" onclick="selectLocation(${location.lat}, ${location.lon}, '${name.replace(/'/g, "\\'")}')">
                <div class="suggestion-icon">
                    <i class="${icon}"></i>
                </div>
                <div class="suggestion-text">
                    <p class="suggestion-title">${name.split(',')[0]}</p>
                    <p class="suggestion-subtitle">${name}</p>
                </div>
            </div>
        `;
    }).join('');
    
    suggestionsDiv.classList.add('active');
}

function selectLocation(lat, lon, name) {
    landingMap.setView([lat, lon], 14);
    
    if (currentSearchMarker) {
        landingMap.removeLayer(currentSearchMarker);
    }
    
    const searchIcon = L.divIcon({
        html: `<div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(45deg, #ff6b6b, #ee5a6f); display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 4px 12px rgba(238, 90, 111, 0.4); animation: bounce 1s ease infinite;">
            <i class="fas fa-search" style="color: white; font-size: 1rem;"></i>
        </div>`,
        className: '',
        iconSize: [40, 40],
        iconAnchor: [20, 20]
    });
    
    currentSearchMarker = L.marker([lat, lon], { icon: searchIcon }).addTo(landingMap);
    currentSearchMarker.bindPopup(`<strong>${name}</strong><br><small class="text-muted">Search Result</small>`).openPopup();
    
    document.getElementById('search-suggestions')?.classList.remove('active');
    const inputEl = document.getElementById('map-search-input');
    if (inputEl) {
        inputEl.value = name.split(',')[0];
    }
}

function performSearch() {
    const input = document.getElementById('map-search-input');
    if (!input) return;
    
    const query = input.value.trim();
    if (!query) return;
    
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=ph&limit=1`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                selectLocation(data[0].lat, data[0].lon, data[0].display_name);
            } else {
                alert('Location not found in the Philippines. Please try a different search term.');
            }
        })
        .catch(err => {
            console.error('Search error:', err);
            alert('Error searching location. Please try again.');
        });
}

// Add bounce animation for search marker
const style = document.createElement('style');
style.textContent = `
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
`;
document.head.appendChild(style);

    </script>
</body>
</html>