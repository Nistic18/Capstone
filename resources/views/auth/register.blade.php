@section('title', 'Register - Fish Market')
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Fish Market'))</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skins/reverse.css') }}">

    @stack('css')
</head>
{{-- Add Bootstrap 5 CSS --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

{{-- Add Bootstrap 5 JavaScript --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
<div class="register-wrapper">
    <div class="container">
        <div class="row align-items-center justify-content-center min-vh-100 py-5">
            <div class="col-12 col-xl-10">
                <div class="row justify-content-center align-items-center g-5">
                    {{-- Left Section - Branding --}}
                    <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
                        <div class="brand-section text-center text-md-start px-lg-4">
                            <div class="brand-logo mb-3">
                                <i class="fas fa-fish"></i>
                            </div>
                            <h1 class="brand-title">Join Fish Market</h1>
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

                    {{-- Right Section - Registration Form --}}
                    <div class="col-lg-6 col-md-6">
                        <div class="register-box mx-auto">
                            <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                                @csrf

                                {{-- Name Field --}}
                                <div class="mb-3">
                                    <input id="name" 
                                           type="text" 
                                           class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           required 
                                           autocomplete="name" 
                                           autofocus
                                           placeholder="Full name">

                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email Field --}}
                                <div class="mb-3">
                                    <input id="email" 
                                           type="email" 
                                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autocomplete="email"
                                           placeholder="Email address">

                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Phone Field --}}
                                <div class="mb-3">
                                    <input id="phone" 
                                           type="tel" 
                                           class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           required 
                                           autocomplete="tel"
                                           placeholder="Phone number (e.g., 09123456789)"
                                           pattern="^(09|\+639)\d{9}$"
                                           maxlength="13">

                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">
                                            Please enter a valid Philippine mobile number (e.g., 09123456789)
                                        </div>
                                    @enderror
                                </div>

                                {{-- Address Section --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-map-marker-alt me-1"></i>Address Details
                                    </label>
                                    
                                    {{-- Street Address --}}
                                    <input id="street_address" 
                                           type="text" 
                                           class="form-control form-control-lg mb-2 @error('address') is-invalid @enderror" 
                                           placeholder="Street (e.g., Phase 1 Blk 1 Lot 1)"
                                           value="{{ old('street_address') }}"
                                           required>
                                    
                                    {{-- Barangay Dropdown --}}
                                    <select id="barangay" 
                                            class="form-select form-select-lg @error('address') is-invalid @enderror" 
                                            required>
                                        <option value="" selected>Select Barangay</option>
                                        <option value="Bagbag I">Bagbag I</option>
                                        <option value="Bagbag II">Bagbag II</option>
                                        <option value="Kanluran">Kanluran</option>
                                        <option value="Ligtong I">Ligtong I</option>
                                        <option value="Ligtong II">Ligtong II</option>
                                        <option value="Ligtong III">Ligtong III</option>
                                        <option value="Ligtong IV">Ligtong IV</option>
                                        <option value="Muzon I">Muzon I</option>
                                        <option value="Muzon II">Muzon II</option>
                                        <option value="Poblacion">Poblacion</option>
                                        <option value="Sapa I">Sapa I</option>
                                        <option value="Sapa II">Sapa II</option>
                                        <option value="Sapa III">Sapa III</option>
                                        <option value="Sapa IV">Sapa IV</option>
                                        <option value="Silangan I">Silangan I</option>
                                        <option value="Silangan II">Silangan II</option>
                                        <option value="Tejeros Convention">Tejeros Convention</option>
                                        <option value="Wawa I">Wawa I</option>
                                        <option value="Wawa II">Wawa II</option>
                                        <option value="Wawa III">Wawa III</option>
                                    </select>

                                    {{-- Hidden field for complete address --}}
                                    <input type="hidden" 
                                           id="address" 
                                           name="address" 
                                           value="{{ old('address') }}">

                                    {{-- Address Preview --}}
                                    <div id="address_preview" class="mt-2 p-2 bg-light rounded" style="display: none;">
                                        <small class="text-muted">Complete Address:</small>
                                        <div class="fw-semibold text-dark" id="preview_text"></div>
                                    </div>

                                    @error('address')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Password Field --}}
                                <div class="mb-3">
                                    <div class="password-wrapper">
                                        <input id="password" 
                                               type="password" 
                                               class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                               name="password" 
                                               required 
                                               autocomplete="new-password"
                                               placeholder="Password">
                                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>

                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Confirm Password Field --}}
                                <div class="mb-3">
                                    <div class="password-wrapper">
                                        <input id="password-confirm" 
                                               type="password" 
                                               class="form-control form-control-lg" 
                                               name="password_confirmation" 
                                               required 
                                               autocomplete="new-password"
                                               placeholder="Confirm password">
                                        <button type="button" class="password-toggle" onclick="togglePassword('password-confirm')">
                                            <i class="fas fa-eye" id="togglePasswordConfirmIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Terms and Conditions --}}
                                <div class="mb-4">
                                    <div class="terms-box">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="terms" 
                                                   id="terms" 
                                                   required>
                                            <label class="form-check-label" for="terms">
                                                I agree to the <a href="#" class="terms-link" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a> and <a href="#" class="terms-link" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                                            </label>
                                            <div class="invalid-feedback">
                                                You must agree to the terms and conditions
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Register Button --}}
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                                        Create Account
                                    </button>
                                </div>

                                {{-- Divider --}}
                                <div class="divider"></div>

                                {{-- Login Link --}}
                                <div class="text-center">
                                    <p class="text-muted mb-2">Already have an account?</p>
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg fw-semibold px-4">
                                        Sign In
                                    </a>
                                </div>
                            </form>
                        </div>

                        {{-- Security Badge --}}
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

{{-- Custom CSS --}}
<style>
    body {
        background: linear-gradient(135deg, #f3e7fd 0%, #e8eaf6 100%);
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
    }

    /* Brand Section */
    .brand-section {
        padding-right: 0;
    }

    .brand-logo i {
        font-size: 4rem;
        color: #7c4dff;
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
        color: #7c4dff;
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
        color: #7c4dff;
        font-size: 1.25rem;
    }

    /* Register Box */
    .register-box {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 20px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        max-width: 500px;
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
        border-color: #7c4dff;
        box-shadow: 0 0 0 3px rgba(124, 77, 255, 0.1);
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
    #address_preview {
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
        color: #7c4dff;
    }

    /* Primary Button (Register) */
    .btn-primary {
        background: linear-gradient(135deg, #7c4dff 0%, #651fff 100%);
        border: none;
        border-radius: 8px;
        font-size: 1.125rem;
        padding: 0.875rem 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(124, 77, 255, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #651fff 0%, #6200ea 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(124, 77, 255, 0.4);
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
        border: 1.5px solid #7c4dff;
        color: #7c4dff;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #7c4dff 0%, #651fff 100%);
        border-color: #7c4dff;
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
    }
</style>

{{-- Custom JavaScript --}}
<script>
    // Initialize form validation and address handling when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeFormValidation();
        initializeAddressHandling();
        initializePhoneValidation();
    });

    // Address handling
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

        // Restore old values if validation failed
        const oldAddress = "{{ old('address') }}";
        if (oldAddress) {
            // Try to parse the old address
            const parts = oldAddress.split(', Barangay ');
            if (parts.length === 2) {
                streetInput.value = parts[0];
                const barangayPart = parts[1].split(', Rosario, Cavite')[0];
                barangaySelect.value = barangayPart;
                updateAddress();
            }
        }
    }

    // Password toggle functionality
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

    // Phone number validation
    function initializePhoneValidation() {
        const phoneInput = document.getElementById('phone');

        phoneInput.addEventListener('input', function(e) {
            // Remove any non-digit characters except + at the start
            let value = e.target.value.replace(/[^\d+]/g, '');
            
            // Ensure + is only at the start
            if (value.includes('+')) {
                const plusCount = (value.match(/\+/g) || []).length;
                if (plusCount > 1 || value.indexOf('+') !== 0) {
                    value = value.replace(/\+/g, '');
                }
            }
            
            // Limit length
            if (value.startsWith('+639')) {
                value = value.substring(0, 13); // +639XXXXXXXXX
            } else if (value.startsWith('09')) {
                value = value.substring(0, 11); // 09XXXXXXXXX
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

    // Form validation
    function initializeFormValidation() {
        const form = document.querySelector('.needs-validation');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password-confirm');
        const streetInput = document.getElementById('street_address');
        const barangaySelect = document.getElementById('barangay');
        const addressInput = document.getElementById('address');

        // Real-time password confirmation validation
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value !== passwordInput.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        passwordInput.addEventListener('input', function() {
            if (confirmPasswordInput.value && confirmPasswordInput.value !== this.value) {
                confirmPasswordInput.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        });

        // Address validation
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

        // Handle form submission
        form.addEventListener('submit', function(event) {
            validateAddress();
            
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Animate to first invalid field
                const firstInvalid = form.querySelector('.form-control:invalid, .form-select:invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            form.classList.add('was-validated');
        });
    }
</script>
