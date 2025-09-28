@extends('layouts.app')

@section('title', 'Register - Fish Market')

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
<div class="register-container position-fixed top-0 start-0 w-100 h-100 overflow-auto">
    <div class="container-fluid py-4">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-6 col-md-8 col-sm-10 col-11">
                {{-- Registration Card --}}
                <div class="register-card card border-0 shadow-lg position-relative overflow-hidden" 
                     style="border-radius: 25px; backdrop-filter: blur(10px);">
                    
                    {{-- Decorative Background Pattern --}}
                    <div class="position-absolute w-100 h-100" style="z-index: 0; opacity: 0.03;">
                        <div class="d-flex flex-wrap">
                            @for($i = 0; $i < 80; $i++)
                                <i class="fas fa-fish m-2" style="font-size: 1.2rem; color: #667eea; transform: rotate({{ rand(-45, 45) }}deg);"></i>
                            @endfor
                        </div>
                    </div>
                    
                    {{-- Header Section --}}
                    <div class="card-header border-0 text-center py-4" 
                         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 25px 25px 0 0; position: relative; z-index: 1;">
                        <div class="mb-3">
                            <i class="fas fa-user-plus text-white" style="font-size: 3rem; animation: float 3s ease-in-out infinite;"></i>
                        </div>
                        <h2 class="text-white fw-bold mb-2">🐠 Join Fish Market</h2>
                        <p class="text-white-50 mb-0">Start your fresh fish trading journey today!</p>
                    </div>

                    <div class="card-body p-4 position-relative" style="z-index: 1;">
                        <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                            @csrf

                            {{-- Name Field --}}
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold" style="color: #2c3e50;">
                                    <i class="fas fa-user me-2 text-primary"></i>Full Name
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" 
                                          style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 15px 0 0 15px;">
                                        <i class="fas fa-id-card text-muted"></i>
                                    </span>
                                    <input id="name" 
                                           type="text" 
                                           class="form-control border-0 @error('name') is-invalid @enderror" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           required 
                                           autocomplete="name" 
                                           autofocus
                                           placeholder="Enter your full name"
                                           style="border-radius: 0 15px 15px 0; background: linear-gradient(45deg, #f8f9fa, #e9ecef); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">

                                    @error('name')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email Field --}}
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold" style="color: #2c3e50;">
                                    <i class="fas fa-envelope me-2 text-primary"></i>Email Address
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text border-0" 
                                          style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 15px 0 0 15px;">
                                        <i class="fas fa-at text-muted"></i>
                                    </span>
                                    <input id="email" 
                                           type="email" 
                                           class="form-control border-0 @error('email') is-invalid @enderror" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autocomplete="email"
                                           placeholder="Enter your email address"
                                           style="border-radius: 0 15px 15px 0; background: linear-gradient(45deg, #f8f9fa, #e9ecef); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">

                                    @error('email')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Password Fields Row --}}
                            <div class="row">
                                {{-- Password Field --}}
                                <div class="col-md-6 mb-4">
                                    <label for="password" class="form-label fw-semibold" style="color: #2c3e50;">
                                        <i class="fas fa-lock me-2 text-primary"></i>Password
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0" 
                                              style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 15px 0 0 15px;">
                                            <i class="fas fa-key text-muted"></i>
                                        </span>
                                        <input id="password" 
                                               type="password" 
                                               class="form-control border-0 @error('password') is-invalid @enderror" 
                                               name="password" 
                                               required 
                                               autocomplete="new-password"
                                               placeholder="Create password"
                                               style="border-radius: 0 0 0 0; background: linear-gradient(45deg, #f8f9fa, #e9ecef); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                                        <button class="btn border-0" 
                                                type="button" 
                                                onclick="togglePassword('password')"
                                                style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 0 15px 15px 0;">
                                            <i class="fas fa-eye text-muted" id="togglePasswordIcon"></i>
                                        </button>

                                        @error('password')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Confirm Password Field --}}
                                <div class="col-md-6 mb-4">
                                    <label for="password-confirm" class="form-label fw-semibold" style="color: #2c3e50;">
                                        <i class="fas fa-check-double me-2 text-primary"></i>Confirm
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0" 
                                              style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 15px 0 0 15px;">
                                            <i class="fas fa-shield-alt text-muted"></i>
                                        </span>
                                        <input id="password-confirm" 
                                               type="password" 
                                               class="form-control border-0" 
                                               name="password_confirmation" 
                                               required 
                                               autocomplete="new-password"
                                               placeholder="Confirm password"
                                               style="border-radius: 0 0 0 0; background: linear-gradient(45deg, #f8f9fa, #e9ecef); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                                        <button class="btn border-0" 
                                                type="button" 
                                                onclick="togglePassword('password-confirm')"
                                                style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 0 15px 15px 0;">
                                            <i class="fas fa-eye text-muted" id="togglePasswordConfirmIcon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Location Section --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-3" style="color: #2c3e50;">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>Your Location
                                </label>
                                
                                {{-- Map Container --}}
                                <div class="position-relative mb-3">
                                    <div id="map" 
                                         class="rounded shadow-sm" 
                                         style="height: 350px; border: 3px solid #e9ecef; border-radius: 20px; overflow: hidden;"></div>
                                    
                                    {{-- Map Overlay Instructions --}}
                                    {{-- <div class="position-absolute top-0 start-0 m-3 p-3 bg-white rounded shadow-sm" 
                                         style="z-index: 1000; border-radius: 15px; max-width: 250px;">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            <small class="fw-semibold text-muted">Location Setup</small>
                                        </div>
                                        <small class="text-muted">Click anywhere on the map to set your location</small>
                                    </div> --}}
                                </div>

                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">

                                {{-- Location Control Buttons --}}
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" 
                                            class="btn btn-outline-primary flex-fill" 
                                            onclick="getCurrentLocation()" 
                                            style="border-radius: 15px; border-width: 2px;">
                                        <i class="fas fa-location-arrow me-2"></i>Use My Location
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-secondary flex-fill" 
                                            onclick="resetMapView()" 
                                            style="border-radius: 15px; border-width: 2px;">
                                        <i class="fas fa-search-location me-2"></i>Reset View
                                    </button>
                                </div>

                                {{-- Location Status --}}
                                <div id="locationStatus" class="mt-2 text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-map-pin me-1"></i>
                                        <span id="locationText">No location selected</span>
                                    </small>
                                </div>
                            </div>

                            {{-- Register Button --}}
                            <div class="mb-4">
                                <button type="submit" 
                                        class="btn w-100 py-3 fw-bold text-white" 
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                                               border-radius: 15px; 
                                               border: none; 
                                               transition: all 0.3s ease;
                                               box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.4)'"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.3)'">
                                    <i class="fas fa-user-plus me-2"></i>{{ __('Create My Account') }}
                                </button>
                            </div>

                            {{-- Login Link --}}
                            <div class="text-center">
                                <div class="border-top pt-4">
                                    <p class="text-muted mb-2">Already have an account?</p>
                                    <a href="{{ route('login') }}" 
                                       class="btn btn-outline-primary py-2 px-4" 
                                       style="border-radius: 15px; border-width: 2px; transition: all 0.3s ease;"
                                       onmouseover="this.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; this.style.borderColor='#667eea'; this.style.color='white'"
                                       onmouseout="this.style.background='transparent'; this.style.borderColor='#667eea'; this.style.color='#667eea'">
                                        <i class="fas fa-sign-in-alt me-2"></i>{{ __('Sign In Instead') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Decorative Wave at Bottom --}}
                    <div class="position-absolute bottom-0 w-100" style="z-index: 0;">
                        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" style="height: 60px; width: 100%;">
                            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="url(#gradient1)"></path>
                            <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="url(#gradient2)"></path>
                            <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="url(#gradient3)"></path>
                            <defs>
                                <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#667eea;stop-opacity:0.1" />
                                    <stop offset="100%" style="stop-color:#764ba2;stop-opacity:0.1" />
                                </linearGradient>
                                <linearGradient id="gradient2" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#667eea;stop-opacity:0.2" />
                                    <stop offset="100%" style="stop-color:#764ba2;stop-opacity:0.2" />
                                </linearGradient>
                                <linearGradient id="gradient3" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#667eea;stop-opacity:0.3" />
                                    <stop offset="100%" style="stop-color:#764ba2;stop-opacity:0.3" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>

                {{-- Additional Info Card --}}
                <div class="text-center mt-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 15px; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);">
                        <div class="card-body py-3">
                            <p class="text-muted mb-0">
                                <i class="fas fa-shield-alt me-2 text-success"></i>
                                Your information is secure and encrypted
                                <i class="fas fa-fish mx-2 text-primary"></i>
                                Join our growing community of fish traders!
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
    .register-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        overflow-y: auto;
        z-index: 9999;
    }

    .register-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(102, 126, 234, 0.2) 0%, transparent 50%);
        z-index: 0;
    }

    .register-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        z-index: 1;
        position: relative;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background: rgba(248, 249, 250, 0.8) !important;
    }

    .input-group-text {
        min-width: 45px;
        justify-content: center;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
        100% {
            transform: translateY(0px);
        }
    }

    /* Map specific styles */
    .leaflet-container {
        border-radius: 20px;
    }

    .leaflet-control-container {
        display: none;
    }

    /* Button styles */
    .btn-primary {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #5a6fd8, #6a42a0);
    }

    .btn-outline-primary {
        border-color: #667eea;
        color: #667eea;
    }

    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem !important;
        }
        
        .register-card {
            margin: 1rem;
        }
        
        .btn {
            padding: 0.8rem 1rem !important;
        }

        #map {
            height: 250px !important;
        }
    }

    @media (max-width: 576px) {
        .row .col-md-6 {
            margin-bottom: 1rem;
        }
    }

    /* Form validation styles */
    .was-validated .form-control:valid {
        border-color: #28a745;
    }

    .was-validated .form-control:invalid {
        border-color: #dc3545;
    }

    /* Password strength indicator */
    .password-strength {
        height: 4px;
        border-radius: 2px;
        margin-top: 5px;
        transition: all 0.3s ease;
    }

    .strength-weak { background: #dc3545; }
    .strength-fair { background: #ffc107; }
    .strength-good { background: #28a745; }
    .strength-strong { background: #007bff; }
</style>

{{-- Custom JavaScript --}}
<script>
    // Map initialization
    var defaultLat = 14.5995;
    var defaultLng = 120.9842;
    var defaultZoom = 13;
    var map;
    var marker;

    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeMap();
        initializeFormValidation();
    });

    function initializeMap() {
        map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: ''
        }).addTo(map);

        // Click on map to set location
        map.on('click', function(e) {
            const { lat, lng } = e.latlng;
            setMarker(lat, lng);
        });
    }

    // Set marker and update hidden inputs
    function setMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        
        // Update location status
        document.getElementById('locationText').textContent = `Location selected (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
        document.getElementById('locationStatus').innerHTML = `
            <small class="text-success">
                <i class="fas fa-check-circle me-1"></i>
                <span id="locationText">Location selected (${lat.toFixed(4)}, ${lng.toFixed(4)})</span>
            </small>
        `;
    }

    // Get current location
    function getCurrentLocation() {
        if (navigator.geolocation) {
            // Show loading state
            document.getElementById('locationText').textContent = 'Getting your location...';
            
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                setMarker(lat, lng);
                map.setView([lat, lng], 16);
            }, function(error) {
                document.getElementById('locationText').textContent = 'Unable to get location';
                document.getElementById('locationStatus').innerHTML = `
                    <small class="text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <span>Unable to retrieve your location. Please click on the map.</span>
                    </small>
                `;
            });
        } else {
            document.getElementById('locationStatus').innerHTML = `
                <small class="text-danger">
                    <i class="fas fa-times-circle me-1"></i>
                    <span>Geolocation is not supported by this browser.</span>
                </small>
            `;
        }
    }

    // Reset to default view
    function resetMapView() {
        map.setView([defaultLat, defaultLng], defaultZoom);
        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }
        document.getElementById('latitude').value = "";
        document.getElementById('longitude').value = "";
        document.getElementById('locationText').textContent = 'No location selected';
        document.getElementById('locationStatus').innerHTML = `
            <small class="text-muted">
                <i class="fas fa-map-pin me-1"></i>
                <span id="locationText">No location selected</span>
            </small>
        `;
    }

    // Password toggle functionality
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(inputId === 'password' ? 'togglePasswordIcon' : 'togglePasswordConfirmIcon');
        
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

    // Form validation
    function initializeFormValidation() {
        const form = document.querySelector('.needs-validation');
        const inputs = form.querySelectorAll('.form-control');

        // Add real-time validation
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('was-validated')) {
                    validateField(this);
                }
                
                // Password strength indicator
                if (this.id === 'password') {
                    checkPasswordStrength(this.value);
                }
                
                // Password confirmation matching
                if (this.id === 'password-confirm') {
                    checkPasswordMatch();
                }
            });
        });

        // Handle form submission
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Animate to first invalid field
                const firstInvalid = form.querySelector('.form-control:invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            form.classList.add('was-validated');
        });
    }

    function validateField(field) {
        if (field.checkValidity()) {
            field.classList.add('is-valid');
            field.classList.remove('is-invalid');
        } else {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
        }
    }

    function checkPasswordMatch() {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password-confirm');
        
        if (confirmPassword.value && password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }

    function checkPasswordStrength(password) {
        // Simple password strength check
        let strength = 0;
        const passwordInput = document.getElementById('password');
        
        // Remove existing strength indicator
        const existingIndicator = passwordInput.parentNode.parentNode.querySelector('.password-strength');
        if (existingIndicator) {
            existingIndicator.remove();
        }
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/)) strength++;
        if (password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;
        
        // Create strength indicator
        const strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength mt-2';
        
        let strengthClass = '';
        let strengthText = '';
        let strengthWidth = '0%';
        
        switch(strength) {
            case 0:
            case 1:
                strengthClass = 'strength-weak';
                strengthText = 'Weak';
                strengthWidth = '25%';
                break;
            case 2:
            case 3:
                strengthClass = 'strength-fair';
                strengthText = 'Fair';
                strengthWidth = '50%';
                break;
            case 4:
                strengthClass = 'strength-good';
                strengthText = 'Good';
                strengthWidth = '75%';
                break;
            case 5:
                strengthClass = 'strength-strong';
                strengthText = 'Strong';
                strengthWidth = '100%';
                break;
        }
        
        strengthIndicator.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="text-muted">Password Strength</small>
                <small class="${strengthClass === 'strength-weak' ? 'text-danger' : strengthClass === 'strength-fair' ? 'text-warning' : 'text-success'}">${strengthText}</small>
            </div>
            <div class="progress" style="height: 4px;">
                <div class="progress-bar ${strengthClass}" style="width: ${strengthWidth}; transition: width 0.3s ease;"></div>
            </div>
        `;
        
        if (password.length > 0) {
            passwordInput.parentNode.parentNode.appendChild(strengthIndicator);
        }
    }
</script>
@endsection