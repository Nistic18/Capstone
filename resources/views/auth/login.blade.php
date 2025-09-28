@extends('layouts.app')

@section('title', 'Login - Fish Market')

{{-- Add Bootstrap 5 CSS --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

{{-- Add Bootstrap 5 JavaScript --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
@endpush

@section('content')
<div class="login-container position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-5 col-md-7 col-sm-9 col-11">
                {{-- Login Card --}}
                <div class="login-card card border-0 shadow-lg position-relative overflow-hidden" 
                     style="border-radius: 25px; backdrop-filter: blur(10px);">
                    
                    {{-- Decorative Background Pattern --}}
                    <div class="position-absolute w-100 h-100" style="z-index: 0; opacity: 0.03;">
                        <div class="d-flex flex-wrap">
                            @for($i = 0; $i < 50; $i++)
                                <i class="fas fa-fish m-2" style="font-size: 1.2rem; color: #667eea; transform: rotate({{ rand(-45, 45) }}deg);"></i>
                            @endfor
                        </div>
                    </div>
                    
                    {{-- Header Section --}}
                    <div class="card-header border-0 text-center py-4" 
                         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                border-radius: 25px 25px 0 0;
                                position: relative;
                                z-index: 1;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                justify-content: center;
                                height: 200px;
                                text-align: center;">
                        <div class="mb-3">
                            <i class="fas fa-fish text-white" style="font-size: 3rem; animation: float 3s ease-in-out infinite;"></i>
                        </div>
                        <div class="text-center">
                        <h2 class="text-white fw-bold mb-2">🐠 Fish Market</h2>
                        <p class="text-white-50 mb-0">Welcome back to the freshest marketplace!</p>
                        </div>
                    </div>

                    <div class="card-body p-5 position-relative" style="z-index: 1;">
                        <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                            @csrf

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
                                           autofocus
                                           placeholder="Enter your email address"
                                           style="border-radius: 0 15px 15px 0; background: linear-gradient(45deg, #f8f9fa, #e9ecef); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">

                                    @error('email')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Password Field --}}
                            <div class="mb-4">
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
                                           autocomplete="current-password"
                                           placeholder="Enter your password"
                                           style="border-radius: 0 0 0 0; background: linear-gradient(45deg, #f8f9fa, #e9ecef); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                                    <button class="btn border-0" 
                                            type="button" 
                                            onclick="togglePassword()"
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

                            {{-- Remember Me --}}
                            <div class="mb-4">
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input me-3" 
                                           type="checkbox" 
                                           name="remember" 
                                           id="remember" 
                                           {{ old('remember') ? 'checked' : '' }}
                                           style="border-radius: 8px; transform: scale(1.2);">
                                    <label class="form-check-label text-muted" for="remember">
                                        <i class="fas fa-history me-1"></i>Remember me for next time
                                    </label>
                                </div>
                            </div>

                            {{-- Login Button --}}
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
                                    <i class="fas fa-sign-in-alt me-2"></i>{{ __('Dive Into Fish Market') }}
                                </button>
                            </div>

                            {{-- Additional Links --}}
                            <div class="text-center">
                                @if (Route::has('password.request'))
                                    <div class="mb-3">
                                        <a href="{{ route('password.request') }}" 
                                           class="text-decoration-none fw-semibold" 
                                           style="color: #667eea;">
                                            <i class="fas fa-question-circle me-1"></i>{{ __('Forgot Your Password?') }}
                                        </a>
                                    </div>
                                @endif

                                <div class="border-top pt-4">
                                    <p class="text-muted mb-2">New to Fish Market?</p>
                                    <a href="{{ route('register') }}" 
                                       class="btn btn-outline-primary py-2 px-4" 
                                       style="border-radius: 15px; border-width: 2px; transition: all 0.3s ease;"
                                       onmouseover="this.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; this.style.borderColor='#667eea'; this.style.color='white'"
                                       onmouseout="this.style.background='transparent'; this.style.borderColor='#667eea'; this.style.color='#667eea'">
                                        <i class="fas fa-user-plus me-2"></i>{{ __('Create Your Account') }}
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
                    <div class="card border-0 shadow-sm" style="border-radius: 15px; background: rgba(255,255,255,0.8); backdrop-filter: blur(10px);">
                        <div class="card-body py-3">
                            <p class="text-muted mb-0">
                                <i class="fas fa-shield-alt me-2 text-success"></i>
                                Your data is safe and secure with us
                                <i class="fas fa-fish mx-2 text-primary"></i>
                                Join thousands of fish lovers!
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
    .login-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        overflow: hidden;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 9999;
    }

    .login-container::before {
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

    .login-card {
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

    .btn-primary {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #5a6fd8, #6a42a0);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 2rem !important;
        }
        
        .login-card {
            margin: 1rem;
        }
        
        .btn {
            padding: 0.8rem 1rem !important;
        }
    }

    /* Custom checkbox styling */
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .form-check-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    /* Button hover effects */
    .btn-outline-primary {
        border-color: #667eea;
        color: #667eea;
    }

    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
    }

    /* Animation for form validation */
    .was-validated .form-control:valid {
        border-color: #28a745;
    }

    .was-validated .form-control:invalid {
        border-color: #dc3545;
    }
</style>

{{-- Custom JavaScript --}}
<script>
    // Password toggle functionality
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');
        
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
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.needs-validation');
        const inputs = form.querySelectorAll('.form-control');

        // Add real-time validation
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.checkValidity()) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
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
    });

    // Add subtle floating animation to background fish icons
    document.addEventListener('DOMContentLoaded', function() {
        const fishIcons = document.querySelectorAll('.position-absolute .fas.fa-fish');
        fishIcons.forEach((fish, index) => {
            fish.style.animationDelay = `${index * 0.1}s`;
            fish.classList.add('animate__animated', 'animate__pulse', 'animate__infinite');
        });
    });
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection