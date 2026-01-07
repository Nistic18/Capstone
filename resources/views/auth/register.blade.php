<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/avatar/dried-fish-logo.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Fish Market</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    
    <style>
    /* Register page logo styling - LARGE VERSION */
.register-logo {
    animation: logoFloat 3s ease-in-out infinite;
    transition: transform 0.3s ease;
}

@keyframes logoFloat {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.register-logo:hover {
    transform: scale(1.08) rotate(5deg);
    animation: none;
}

/* Responsive logo sizing for register - LARGER */
@media (max-width: 992px) {
    .register-logo {
        height: 180px !important;
    }
}

@media (max-width: 768px) {
    .register-logo {
        height: 150px !important;
    }
}

@media (max-width: 576px) {
    .register-logo {
        height: 130px !important;
    }
}

/* Brand section improvements */
.brand-logo {
    margin-bottom: 2rem;
}

.brand-title {
    animation: fadeInUp 0.8s ease-out;
    animation-delay: 0.2s;
    animation-fill-mode: both;
}

.brand-subtitle {
    animation: fadeInUp 0.8s ease-out;
    animation-delay: 0.4s;
    animation-fill-mode: both;
}

.features-list {
    animation: fadeInUp 0.8s ease-out;
    animation-delay: 0.6s;
    animation-fill-mode: both;
}

.feature-item {
    transition: transform 0.3s ease;
}

.feature-item:hover {
    transform: translateX(10px);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
        body {
            background: linear-gradient(135deg, #d4f1e5 0%, #c8e6c9 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Back to Home Button */
        .back-to-home {
            position: absolute;
            top: 2rem;
            left: 2rem;
            z-index: 1000;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            color: #0bb364;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-back:hover {
            background: #0bb364;
            color: white;
            transform: translateX(-5px);
            box-shadow: 0 4px 12px rgba(11, 179, 100, 0.3);
        }

        .btn-back i {
            font-size: 1rem;
            transition: transform 0.3s ease;
        }

        .btn-back:hover i {
            transform: translateX(-3px);
        }

        /* Brand Section */
        .brand-section {
            padding-right: 0;
        }

        .brand-logo i {
            font-size: 4rem;
            color: #0bb364;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .brand-title {
            color: #0bb364;
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .brand-subtitle {
            color: #37474f;
            font-size: 1.25rem;
            line-height: 1.5;
            font-weight: 400;
        }

        .features-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #37474f;
            font-size: 1rem;
        }

        .feature-item i {
            color: #0bb364;
            font-size: 1.25rem;
        }

        /* Register Box */
        .register-box {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 20px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 900px;
            margin: 0 auto;
        }

        /* Form Controls */
        .form-control,
        .form-select {
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            padding: 0.875rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0bb364;
            box-shadow: 0 0 0 3px rgba(11, 179, 100, 0.1);
            outline: none;
            background-color: #fafafa;
        }

        .form-control::placeholder {
            color: #9e9e9e;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #e53935;
        }

        .form-control.is-invalid:focus,
        .form-select.is-invalid:focus {
            border-color: #e53935;
            box-shadow: 0 0 0 3px rgba(229, 57, 53, 0.1);
        }

        .form-label {
            color: #37474f;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        /* Address Preview */
        #address_preview,
        #name_preview {
            border: 1px solid #e0e0e0;
            font-size: 0.9rem;
        }

        /* Password Toggle */
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #757575;
            cursor: pointer;
            padding: 0.5rem;
            transition: color 0.2s;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #0bb364;
        }

        /* Primary Button */
        .btn-primary {
            background: linear-gradient(135deg, #66d88e 0%, #0bb364 100%);
            border: none;
            border-radius: 8px;
            font-size: 1.125rem;
            padding: 0.875rem 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(11, 179, 100, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0bb364 0%, #289c58 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(11, 179, 100, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Outline Buttons */
        .btn-outline-secondary {
            border: 1.5px solid #e0e0e0;
            color: #616161;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            padding: 0.625rem 1rem;
        }

        .btn-outline-secondary:hover {
            background-color: #f5f5f5;
            border-color: #bdbdbd;
            color: #424242;
        }

        .btn-outline-primary {
            border: 1.5px solid #0bb364;
            color: #0bb364;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #66d88e 0%, #0bb364 100%);
            border-color: #0bb364;
            color: white;
        }

        /* Divider */
        .divider {
            border-bottom: 1px solid #e0e0e0;
            margin: 1.5rem 0;
        }

        /* Security Text */
        .security-text {
            font-size: 0.875rem;
            color: #616161;
            margin: 0;
        }

        .security-text i {
            color: #43a047;
        }

        /* Invalid Feedback */
        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block !important;
        }

        /* Terms Box */
        .terms-box {
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .terms-link {
            color: #0bb364;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .terms-link:hover {
            color: #289c58;
            text-decoration: underline;
        }

        .form-check-input:checked {
            background-color: #0bb364;
            border-color: #0bb364;
        }

        .form-check-input:focus {
            border-color: #0bb364;
            box-shadow: 0 0 0 0.25rem rgba(11, 179, 100, 0.25);
        }

        .form-check-input.is-invalid {
            border-color: #e53935;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 12px;
            border: none;
        }

        .modal-header {
            padding: 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
            max-height: 60vh;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
        }

        /* Password Requirements */
        .password-requirements {
            border: 1px solid #e0e0e0;
            font-size: 0.875rem;
        }

        .requirement-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0;
            transition: all 0.3s ease;
        }

        .requirement-icon {
            font-size: 0.5rem;
            color: #bdbdbd;
            transition: all 0.3s ease;
        }

        .requirement-text {
            color: #757575;
            transition: all 0.3s ease;
        }

        .requirement-item.valid .requirement-icon {
            color: #43a047;
            transform: scale(1.2);
        }

        .requirement-item.valid .requirement-text {
            color: #43a047;
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .brand-section {
                text-align: center;
            }

            .brand-title {
                font-size: 2.5rem;
            }

            .brand-subtitle {
                font-size: 1.125rem;
            }

            .features-list {
                align-items: center;
            }

            .back-to-home {
                top: 1rem;
                left: 1rem;
            }
        }

        @media (max-width: 767px) {
            .row.g-2 > .col-md-4 {
                margin-bottom: 0.5rem;
            }
            
            .row.g-2 > .col-md-4:last-child {
                margin-bottom: 0;
            }
        }

        @media (max-width: 575px) {
            .brand-title {
                font-size: 2rem;
            }

            .brand-subtitle {
                font-size: 1rem;
            }

            .register-box {
                padding: 1.5rem;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .btn-back {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }

            .btn-back span {
                display: none;
            }

            .btn-back i {
                margin-right: 0 !important;
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
    <div class="register-wrapper">
        {{-- Back to Home Button --}}
        <div class="back-to-home">
            <a href="{{ route('landing') }}" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>
                <span>Back to Home</span>
            </a>
        </div>

        <div class="container">
            <div class="row align-items-center justify-content-center min-vh-100 py-5">
                <div class="col-12 col-xl-10">
                    <div class="row justify-content-center align-items-center g-5">
                        <!-- Left Section - Branding -->
                        <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
                            <div class="brand-section text-center text-md-start px-lg-4">
                                <div class="brand-logo mb-3">
                                    <img src="{{ asset('img/avatar/dried-fish-logo.png') }}" 
                                         alt="Dried Fish Market Logo" 
                                         class="register-logo"
                                         style="height: 220px; width: auto; filter: drop-shadow(0 4px 15px rgba(0,0,0,0.2));">
                                </div>
                                <h1 class="brand-title">Join Dried Fish Market</h1>
                                <p class="brand-subtitle">Create your account and start connecting with buyers and sellers in your area. Join our growing community today!</p>
                                
                                <div class="features-list mt-4">
                                    <div class="feature-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Connect with local fish traders</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Fresh seafood marketplace</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Secure and trusted platform</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Section - Registration Form -->
                        <div class="col-lg-7 col-md-8">
                            <div class="register-box mx-auto">
                                <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                                    @csrf
                                    <div class="row g-4">

                                        <!-- Left Column -->
                                        <div class="col-md-6">
                                            <!-- Full Name -->
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="fas fa-user me-1"></i>Full Name
                                                </label>
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <input id="first_name" type="text" 
                                                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                                               required autocomplete="given-name" autofocus 
                                                               placeholder="First name"
                                                               value="{{ old('first_name', session('first_name')) }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input id="middle_name" type="text" 
                                                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                                               autocomplete="additional-name" 
                                                               placeholder="Middle (optional)"
                                                               value="{{ old('middle_name', session('middle_name')) }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input id="last_name" type="text" 
                                                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                                               required autocomplete="family-name" 
                                                               placeholder="Last name"
                                                               value="{{ old('last_name', session('last_name')) }}">
                                                    </div>
                                                </div>
                                                <input type="hidden" id="name" name="name" value="{{ old('name') }}">
                                                <div id="name_preview" class="mt-2 p-2 bg-light rounded" style="display: none;">
                                                    <small class="text-muted">Full Name:</small>
                                                    <div class="fw-semibold text-dark" id="name_preview_text"></div>
                                                </div>
                                                @error('name')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <!-- Email Field -->
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="fas fa-envelope me-1"></i>Email Address
                                                </label>
                                                <input id="email" type="email" 
                                                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                                       name="email" 
                                                       value="{{ old('email') }}" 
                                                       required autocomplete="email" 
                                                       placeholder="Email address">
                                                @error('email')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <!-- Phone Field -->
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="fas fa-phone me-1"></i>Phone Number
                                                </label>
                                                <input id="phone" type="tel" 
                                                       class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                                                       name="phone" 
                                                       value="{{ old('phone') }}" 
                                                       required autocomplete="tel" 
                                                       placeholder="Phone (e.g., 09123456789)" 
                                                       pattern="^(09|\+639)\d{9}$" 
                                                       maxlength="13">
                                                @error('phone')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <!-- Password Field -->
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="fas fa-lock me-1"></i>Password
                                                </label>
                                                <div class="password-wrapper">
                                                    <input id="password" type="password" 
                                                           class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                                           name="password" 
                                                           required autocomplete="new-password" 
                                                           placeholder="Password">
                                                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                                    </button>
                                                </div>
                                                <div class="password-requirements mt-2 p-2 bg-light rounded">
                                                    <small class="text-muted d-block mb-2 fw-semibold">Password must contain:</small>
                                                    <div class="requirement-item" id="req-length">
                                                        <i class="fas fa-circle requirement-icon"></i>
                                                        <span class="requirement-text">At least 8 characters</span>
                                                    </div>
                                                    <div class="requirement-item" id="req-uppercase">
                                                        <i class="fas fa-circle requirement-icon"></i>
                                                        <span class="requirement-text">One uppercase letter (A-Z)</span>
                                                    </div>
                                                    <div class="requirement-item" id="req-number">
                                                        <i class="fas fa-circle requirement-icon"></i>
                                                        <span class="requirement-text">One number (0-9)</span>
                                                    </div>
                                                    <div class="requirement-item" id="req-special">
                                                        <i class="fas fa-circle requirement-icon"></i>
                                                        <span class="requirement-text">One special character (!@#$%^&*)</span>
                                                    </div>
                                                </div>
                                                <div id="password-strength" class="mt-2">
                                                    <small id="password-strength-text" class="fw-semibold"></small>
                                                    <div class="progress mt-1" style="height: 6px;">
                                                        <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0;"></div>
                                                    </div>
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <!-- Confirm Password Field -->
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="fas fa-lock me-1"></i>Confirm Password
                                                </label>
                                                <div class="password-wrapper">
                                                    <input id="password-confirm" type="password" 
                                                           class="form-control form-control-lg" 
                                                           name="password_confirmation" 
                                                           required autocomplete="new-password" 
                                                           placeholder="Confirm password">
                                                    <button type="button" class="password-toggle" onclick="togglePassword('password-confirm')">
                                                        <i class="fas fa-eye" id="togglePasswordConfirmIcon"></i>
                                                    </button>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- Right Column -->
                                        <div class="col-md-6">
                                            <!-- Address Section -->
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="fas fa-map-marker-alt me-1"></i>Address Details
                                                </label>
                                                <input id="street_address" type="text" 
                                                       class="form-control form-control-lg mb-2 @error('address') is-invalid @enderror" 
                                                       placeholder="Street (e.g., Phase 1 Blk 1 Lot 1)" 
                                                       value="{{ old('street_address', session('street_address')) }}"
                                                       required>
                                                <select id="barangay" 
                                                        class="form-select form-select-lg @error('address') is-invalid @enderror" 
                                                        required>
                                                    <option value="">Select Barangay</option>
                                                    <option value="Bagbag I" {{ old('barangay', session('barangay')) == 'Bagbag I' ? 'selected' : '' }}>Bagbag I</option>
                                                    <option value="Bagbag II" {{ old('barangay', session('barangay')) == 'Bagbag II' ? 'selected' : '' }}>Bagbag II</option>
                                                    <option value="Kanluran" {{ old('barangay', session('barangay')) == 'Kanluran' ? 'selected' : '' }}>Kanluran</option>
                                                    <option value="Ligtong I" {{ old('barangay', session('barangay')) == 'Ligtong I' ? 'selected' : '' }}>Ligtong I</option>
                                                    <option value="Ligtong II" {{ old('barangay', session('barangay')) == 'Ligtong II' ? 'selected' : '' }}>Ligtong II</option>
                                                    <option value="Ligtong III" {{ old('barangay', session('barangay')) == 'Ligtong III' ? 'selected' : '' }}>Ligtong III</option>
                                                    <option value="Ligtong IV" {{ old('barangay', session('barangay')) == 'Ligtong IV' ? 'selected' : '' }}>Ligtong IV</option>
                                                    <option value="Muzon I" {{ old('barangay', session('barangay')) == 'Muzon I' ? 'selected' : '' }}>Muzon I</option>
                                                    <option value="Muzon II" {{ old('barangay', session('barangay')) == 'Muzon II' ? 'selected' : '' }}>Muzon II</option>
                                                    <option value="Poblacion" {{ old('barangay', session('barangay')) == 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                                                    <option value="Sapa I" {{ old('barangay', session('barangay')) == 'Sapa I' ? 'selected' : '' }}>Sapa I</option>
                                                    <option value="Sapa II" {{ old('barangay', session('barangay')) == 'Sapa II' ? 'selected' : '' }}>Sapa II</option>
                                                    <option value="Sapa III" {{ old('barangay', session('barangay')) == 'Sapa III' ? 'selected' : '' }}>Sapa III</option>
                                                    <option value="Sapa IV" {{ old('barangay', session('barangay')) == 'Sapa IV' ? 'selected' : '' }}>Sapa IV</option>
                                                    <option value="Silangan I" {{ old('barangay', session('barangay')) == 'Silangan I' ? 'selected' : '' }}>Silangan I</option>
                                                    <option value="Silangan II" {{ old('barangay', session('barangay')) == 'Silangan II' ? 'selected' : '' }}>Silangan II</option>
                                                    <option value="Tejeros Convention" {{ old('barangay', session('barangay')) == 'Tejeros Convention' ? 'selected' : '' }}>Tejeros Convention</option>
                                                    <option value="Wawa I" {{ old('barangay', session('barangay')) == 'Wawa I' ? 'selected' : '' }}>Wawa I</option>
                                                    <option value="Wawa II" {{ old('barangay', session('barangay')) == 'Wawa II' ? 'selected' : '' }}>Wawa II</option>
                                                    <option value="Wawa III" {{ old('barangay', session('barangay')) == 'Wawa III' ? 'selected' : '' }}>Wawa III</option>
                                                </select>
                                                <input type="hidden" id="address" name="address" value="{{ old('address') }}">
                                                <div id="address_preview" class="mt-2 p-2 bg-light rounded" style="display: none;">
                                                    <small class="text-muted">Complete Address:</small>
                                                    <div class="fw-semibold text-dark" id="preview_text"></div>
                                                </div>
                                                @error('address')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <!-- Terms and Conditions -->
                                            <div class="mb-4">
                                                <div class="terms-box">
                                                    <div class="form-check">
                                                        <input class="form-check-input @error('terms') is-invalid @enderror" 
                                                               type="checkbox" 
                                                               name="terms" 
                                                               id="terms" 
                                                               {{ old('terms') ? 'checked' : '' }}
                                                               required>
                                                        <label class="form-check-label" for="terms">
                                                            I agree to the <a href="#" class="terms-link" data-bs-toggle="modal" data-bs-target="#privacyModal">Terms and Conditions & Privacy Policy</a>
                                                        </label>
                                                        @error('terms')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Register Button -->
                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                                </button>
                                            </div>

                                            <!-- Divider -->
                                            <div class="divider"></div>

                                            <!-- Login Link -->
                                            <div class="text-center">
                                                <p class="text-muted mb-2">Already have an account?</p>
                                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg fw-semibold px-4">
                                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Security Badge -->
                            <div class="text-center mt-4">
                                <p class="security-text">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    <span class="fw-semibold">Secure Registration</span> - Your data is encrypted
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy & Terms Modal -->
    <div class="modal fade" id="privacyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="privacyModalLabel">
                        <i class="fas fa-shield-alt text-primary me-2"></i>Privacy Policy & Terms of Service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <!-- Privacy Policy Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-user-shield me-2"></i>Data Privacy Policy
                        </h6>
                        <p class="small text-muted mb-3">Last updated: October 20, 2025</p>
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

                    <hr class="my-4">

                    <!-- Terms and Conditions Section -->
                    <div class="mt-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-file-contract me-2"></i>Terms and Conditions
                        </h6>
                        <p class="small text-muted mb-3">Last updated: October 20, 2025</p>
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
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                    <button type="button" class="btn btn-primary px-4" id="acceptBtn">
                        <i class="fas fa-check me-2"></i>I Accept
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password Strength Indicator
        const passwordInput = document.getElementById('password');
        const strengthText = document.getElementById('password-strength-text');
        const strengthBar = document.getElementById('password-strength-bar');

        passwordInput.addEventListener('input', function() {
            const value = passwordInput.value;
            
            // Update requirements indicators
            updatePasswordRequirements(value);
            
            // Update strength indicator
            const strength = getPasswordStrength(value);

            if (strength.score === 0) {
                strengthText.textContent = '';
                strengthBar.style.width = '0';
            } else {
                strengthText.textContent = strength.label;
                strengthText.style.color = strength.color;
                strengthBar.style.width = strength.percent + '%';
                strengthBar.style.backgroundColor = strength.color;
            }
        });

        function updatePasswordRequirements(password) {
            // Length requirement
            const lengthReq = document.getElementById('req-length');
            if (password.length >= 8) {
                lengthReq.classList.add('valid');
            } else {
                lengthReq.classList.remove('valid');
            }

            // Uppercase requirement
            const uppercaseReq = document.getElementById('req-uppercase');
            if (/[A-Z]/.test(password)) {
                uppercaseReq.classList.add('valid');
            } else {
                uppercaseReq.classList.remove('valid');
            }

            // Number requirement
            const numberReq = document.getElementById('req-number');
            if (/[0-9]/.test(password)) {
                numberReq.classList.add('valid');
            } else {
                numberReq.classList.remove('valid');
            }

            // Special character requirement
            const specialReq = document.getElementById('req-special');
            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
                specialReq.classList.add('valid');
            } else {
                specialReq.classList.remove('valid');
            }
        }

        function getPasswordStrength(password) {
            let score = 0;

            if (password.length >= 8) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) score++;

            if (password.length === 0) return { score: 0, label: '', percent: 0, color: '' };

            switch (score) {
                case 1:
                    return { score, label: 'Weak', percent: 25, color: '#e53935' };
                case 2:
                    return { score, label: 'Fair', percent: 50, color: '#ff9800' };
                case 3:
                    return { score, label: 'Good', percent: 75, color: '#ffb300' };
                case 4:
                    return { score, label: 'Strong', percent: 100, color: '#43a047' };
                default:
                    return { score: 0, label: '', percent: 0, color: '' };
            }
        }

        // Password Toggle
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const iconId = inputId === 'password' ? 'togglePasswordIcon' : 'togglePasswordConfirmIcon';
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Name Handling
        function initializeNameHandling() {
            const firstNameInput = document.getElementById('first_name');
            const middleNameInput = document.getElementById('middle_name');
            const lastNameInput = document.getElementById('last_name');
            const nameInput = document.getElementById('name');
            const namePreview = document.getElementById('name_preview');
            const namePreviewText = document.getElementById('name_preview_text');

            function updateFullName() {
                const firstName = firstNameInput.value.trim();
                const middleName = middleNameInput.value.trim();
                const lastName = lastNameInput.value.trim();

                if (firstName || middleName || lastName) {
                    const nameParts = [firstName, middleName, lastName].filter(part => part !== '');
                    const fullName = nameParts.join(' ');
                    
                    nameInput.value = fullName;
                    namePreviewText.textContent = fullName;
                    namePreview.style.display = fullName ? 'block' : 'none';
                } else {
                    nameInput.value = '';
                    namePreview.style.display = 'none';
                }
            }

            firstNameInput.addEventListener('input', updateFullName);
            middleNameInput.addEventListener('input', updateFullName);
            lastNameInput.addEventListener('input', updateFullName);

            function validateName() {
                if (!firstNameInput.value.trim() || !lastNameInput.value.trim()) {
                    firstNameInput.setCustomValidity('Please enter your first name');
                    lastNameInput.setCustomValidity('Please enter your last name');
                    return false;
                } else {
                    firstNameInput.setCustomValidity('');
                    lastNameInput.setCustomValidity('');
                    return true;
                }
            }

            firstNameInput.addEventListener('blur', validateName);
            lastNameInput.addEventListener('blur', validateName);
        }

        // Address Handling
        function initializeAddressHandling() {
            const streetInput = document.getElementById('street_address');
            const barangaySelect = document.getElementById('barangay');
            const addressInput = document.getElementById('address');
            const addressPreview = document.getElementById('address_preview');
            const previewText = document.getElementById('preview_text');

            function updateAddress() {
                const street = streetInput.value.trim();
                const barangay = barangaySelect.value;

                if (street && barangay) {
                    const completeAddress = `${street}, Barangay ${barangay}, Rosario, Cavite`;
                    addressInput.value = completeAddress;
                    previewText.textContent = completeAddress;
                    addressPreview.style.display = 'block';
                } else {
                    addressInput.value = '';
                    addressPreview.style.display = 'none';
                }
            }

            streetInput.addEventListener('input', updateAddress);
            barangaySelect.addEventListener('change', updateAddress);

            function validateAddress() {
                if (!streetInput.value.trim() || !barangaySelect.value) {
                    streetInput.setCustomValidity('Please enter your street address');
                    barangaySelect.setCustomValidity('Please select a barangay');
                    return false;
                } else {
                    streetInput.setCustomValidity('');
                    barangaySelect.setCustomValidity('');
                    return true;
                }
            }

            streetInput.addEventListener('input', validateAddress);
            barangaySelect.addEventListener('change', validateAddress);
        }

        // Phone Validation
        function initializePhoneValidation() {
            const phoneInput = document.getElementById('phone');

            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^\d+]/g, '');
                
                if (value.includes('+')) {
                    const plusCount = (value.match(/\+/g) || []).length;
                    if (plusCount > 1 || value.indexOf('+') !== 0) {
                        value = value.replace(/\+/g, '');
                    }
                }
                
                if (value.startsWith('+639')) {
                    value = value.substring(0, 13);
                } else if (value.startsWith('09')) {
                    value = value.substring(0, 11);
                }
                
                e.target.value = value;
            });

            phoneInput.addEventListener('blur', function(e) {
                const value = e.target.value;
                const pattern = /^(09|\+639)\d{9}$/;
                
                if (value && !pattern.test(value)) {
                    e.target.setCustomValidity('Please enter a valid Philippine mobile number');
                } else {
                    e.target.setCustomValidity('');
                }
            });
        }

        // Form Validation
        function initializeFormValidation() {
            const form = document.querySelector('.needs-validation');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password-confirm');
            const confirmError = document.getElementById('password-confirm-error');
            
            let userHasTypedInConfirm = false;

            confirmPasswordInput.addEventListener('focus', function() {
                userHasTypedInConfirm = true;
            });

            confirmPasswordInput.addEventListener('input', function() {
                // Only validate if user has started typing in this field
                if (userHasTypedInConfirm && this.value.length > 0 && this.value !== passwordInput.value) {
                    this.classList.add('is-invalid');
                    confirmError.style.display = 'block';
                } else {
                    this.classList.remove('is-invalid');
                    confirmError.style.display = 'none';
                }
            });

            confirmPasswordInput.addEventListener('blur', function() {
                // Check on blur only if field has value and user has interacted with it
                if (userHasTypedInConfirm && this.value.length > 0 && this.value !== passwordInput.value) {
                    this.classList.add('is-invalid');
                    confirmError.style.display = 'block';
                } else if (this.value.length > 0 && this.value === passwordInput.value) {
                    this.classList.remove('is-invalid');
                    confirmError.style.display = 'none';
                }
            });

            passwordInput.addEventListener('input', function() {
                // Only validate if confirm password has been touched and has value
                if (userHasTypedInConfirm && confirmPasswordInput.value.length > 0) {
                    if (confirmPasswordInput.value !== this.value) {
                        confirmPasswordInput.classList.add('is-invalid');
                        confirmError.style.display = 'block';
                    } else {
                        confirmPasswordInput.classList.remove('is-invalid');
                        confirmError.style.display = 'none';
                    }
                }
            });

            form.addEventListener('submit', function(event) {
                // DON'T manually validate password match on submit
                // Let HTML5 required attribute handle empty fields
                // Only show custom error if both fields have values and don't match
                if (passwordInput.value.length > 0 && 
                    confirmPasswordInput.value.length > 0 && 
                    passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.classList.add('is-invalid');
                    confirmError.style.display = 'block';
                    event.preventDefault();
                    event.stopPropagation();
                    confirmPasswordInput.focus();
                    return false;
                }

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    const firstInvalid = form.querySelector('.form-control:invalid, .form-select:invalid, .form-check-input:invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
                form.classList.add('was-validated');
            });
        }

        // Accept Terms Button
        document.getElementById('acceptBtn').addEventListener('click', function() {
            document.getElementById('terms').checked = true;
            const modal = bootstrap.Modal.getInstance(document.getElementById('privacyModal'));
            if (modal) {
                modal.hide();
            }
        });

        // Initialize all functions when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initializeFormValidation();
            initializeAddressHandling();
            initializePhoneValidation();
            initializeNameHandling();

            // Trigger address preview on page load if values exist
            const streetInput = document.getElementById('street_address');
            const barangaySelect = document.getElementById('barangay');
            if (streetInput.value || barangaySelect.value) {
                streetInput.dispatchEvent(new Event('input'));
            }

            // Trigger name preview on page load if values exist
            const firstNameInput = document.getElementById('first_name');
            if (firstNameInput.value) {
                firstNameInput.dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>