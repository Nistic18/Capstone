<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/avatar/dried-fish-logo.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Fish Market</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

{{-- Add Bootstrap 5 JavaScript --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
@endpush

@section('content')
<div class="login-wrapper">
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
                    {{-- Left Section - Branding --}}
                    <div class="col-lg-6 col-md-6 mb-4 mb-md-0">
                        <div class="brand-section text-center text-md-start px-lg-5">
                            <div class="brand-logo mb-3">
                                <img src="{{ asset('img/avatar/dried-fish-logo.png') }}" 
                                     alt="Dried Fish Market Logo" 
                                     class="login-logo"
                                     style="height: 220px; width: auto; filter: drop-shadow(0 4px 15px rgba(0,0,0,0.2));">
                            </div>
                            <h1 class="brand-title">Fish Market</h1>
                            <p class="brand-subtitle">Your trusted marketplace for the freshest seafood. Connect with sellers and buyers around you.</p>
                        </div>
                    </div>

                    {{-- Right Section - Login Form --}}
                    <div class="col-lg-5 col-md-5 mb-4 mb-md-0">
                        <div class="login-box mx-auto">
                            {{-- Ban/Restriction Alert --}}
                            @if($errors->has('email') && (str_contains($errors->first('email'), 'banned') || str_contains($errors->first('email'), 'restricted')))
                                <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px; border-left: 4px solid #dc3545 !important;">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-exclamation-circle me-3 mt-1" style="font-size: 1.5rem; color: #dc3545;"></i>
                                        <div>
                                            <h6 class="mb-2 fw-bold" style="color: #721c24;">Account Restricted</h6>
                                            <p class="mb-0" style="font-size: 0.95rem; line-height: 1.6;">
                                                {{ $errors->first('email') }}
                                            </p>
                                            <small class="d-block mt-2 text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                If you believe this is a mistake, please contact support.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                {{-- Email Field --}}
                                <div class="mb-3">
                                    <input id="email" 
                                           type="email" 
                                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autocomplete="email" 
                                           autofocus
                                           placeholder="Email">

                                    @error('email')
                                        @if(!str_contains($message, 'banned') && !str_contains($message, 'restricted'))
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @endif
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
                                               autocomplete="current-password"
                                               placeholder="Password">
                                        <button type="button" class="password-toggle" onclick="togglePassword()">
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>

                                    @error('password')
                                        <div id="password-error" class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Login Button --}}
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                                        Log In
                                    </button>
                                </div>

                                {{-- Forgot Password Link --}}
                                @if (Route::has('password.request'))
                                    <div class="text-center mb-3">
                                        <a href="{{ route('password.request') }}" class="forgot-link">
                                            Forgot password?
                                        </a>
                                    </div>
                                @endif

                                {{-- Divider --}}
                                <div class="divider"></div>

                                {{-- Create Account Button --}}
                                <div class="text-center">
                                    <a href="{{ route('register') }}" class="btn btn-success btn-lg fw-semibold px-4">
                                        Create new account
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
/* Login page logo styling - LARGER VERSION */
.login-logo {
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

.login-logo:hover {
    transform: scale(1.08) rotate(5deg);
    animation: none;
}

/* Responsive logo sizing for login - LARGER */
@media (max-width: 992px) {
    .login-logo {
        height: 180px !important;
    }
}

@media (max-width: 768px) {
    .login-logo {
        height: 150px !important;
    }
}

@media (max-width: 576px) {
    .login-logo {
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

.login-wrapper {
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
    font-size: 3.5rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 1rem;
}

.brand-subtitle {
    color: #37474f;
    font-size: 1.5rem;
    line-height: 1.5;
    font-weight: 400;
}

/* Login Box */
.login-box {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 20px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    max-width: 520px;
    margin: 0 auto;
}

/* Ban Alert Styling */
.alert-danger {
    animation: slideInDown 0.5s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Form Controls */
.form-control {
    border: 1.5px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
    padding: 0.875rem 1rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #0bb364;
    box-shadow: 0 0 0 3px rgba(11, 179, 100, 0.1);
    outline: none;
    background-color: #fafafa;
}

.form-control::placeholder {
    color: #9e9e9e;
}

.form-control.is-invalid {
    border-color: #e53935;
}

.form-control.is-invalid:focus {
    border-color: #e53935;
    box-shadow: 0 0 0 3px rgba(229, 57, 53, 0.1);
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
}

.password-toggle:hover {
    color: #0bb364;
}

/* Primary Button (Log In) */
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

/* Forgot Password Link */
.forgot-link {
    color: #0bb364;
    font-size: 0.938rem;
    text-decoration: none;
    transition: color 0.2s;
}

.forgot-link:hover {
    color: #289c58;
    text-decoration: underline;
}

/* Divider */
.divider {
    border-bottom: 1px solid #e0e0e0;
    margin: 1.5rem 0;
}

/* Create Account Button */
.btn-success {
    background: linear-gradient(135deg, #43a047 0%, #388e3c 100%);
    border: none;
    border-radius: 8px;
    font-size: 1.063rem;
    padding: 0.875rem 2rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(67, 160, 71, 0.3);
}

.btn-success:hover {
    background: linear-gradient(135deg, #388e3c 0%, #2e7d32 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 160, 71, 0.4);
}

.btn-success:active {
    transform: translateY(0);
}

/* Invalid Feedback */
.invalid-feedback {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Responsive Design */
@media (max-width: 991px) {
    .brand-section {
        padding-right: 0;
        text-align: center;
    }

    .brand-title {
        font-size: 2.5rem;
    }

    .brand-subtitle {
        font-size: 1.25rem;
    }

    .back-to-home {
        top: 1rem;
        left: 1rem;
    }
}

@media (max-width: 575px) {
    .brand-title {
        font-size: 2rem;
    }

    .brand-subtitle {
        font-size: 1rem;
    }

    .login-box {
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

    document.addEventListener("DOMContentLoaded", function() {
        let countdownTimer = null;

        function startCountdown(duration, display) {
            let remaining = duration;

            display.style.display = "block";
            display.classList.remove('invalid-feedback');
            display.classList.add('text-danger', 'fw-semibold');

            if (countdownTimer) clearInterval(countdownTimer);

            const update = () => {
                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;
                const formatted = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                display.textContent = `Too many failed attempts. Please wait ${formatted} before trying again.`;

                remaining--;

                if (remaining < 0) {
                    clearInterval(countdownTimer);
                    display.textContent = "You can now try logging in again.";
                    display.classList.remove('text-danger');
                    display.classList.add('text-success');
                }
            };

            update();
            countdownTimer = setInterval(update, 1000);
        }

        function initCountdown() {
            const errorElem = document.getElementById("password-error");
            if (!errorElem) return;

            const text = errorElem.textContent.trim();
            const match = text.match(/(\d{1,2}):(\d{2})/);

            if (match) {
                const minutes = parseInt(match[1], 10);
                const seconds = parseInt(match[2], 10);
                const totalSeconds = minutes * 60 + seconds;

                startCountdown(totalSeconds, errorElem);
            }
        }

        const observer = new MutationObserver(() => initCountdown());
        observer.observe(document.body, { childList: true, subtree: true });

        setTimeout(initCountdown, 300);
    });
</script>

<!-- Bootstrap 5 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>