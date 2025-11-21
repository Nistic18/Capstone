@section('title', 'Login - Fish Market')

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
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

{{-- Add Bootstrap 5 JavaScript --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
@endpush

@section('content')
<div class="login-wrapper">
    <div class="container">
        <div class="row align-items-center justify-content-center min-vh-100 py-5">
            <div class="col-12 col-xl-10">
                <div class="row justify-content-center align-items-center g-5">
                    {{-- Left Section - Branding --}}
                    <div class="col-lg-6 col-md-6 mb-4 mb-md-0">
                        <div class="brand-section text-center text-md-start px-lg-5">
                            <div class="brand-logo mb-3">
                                <i class="fas fa-fish"></i>
                            </div>
                            <h1 class="brand-title">Fish Market</h1>
                            <p class="brand-subtitle">Your trusted marketplace for the freshest seafood. Connect with sellers and buyers around you.</p>
                        </div>
                    </div>

                    {{-- Right Section - Login Form --}}
                    <div class="col-lg-5 col-md-5 mb-4 mb-md-0">
                        <div class="login-box mx-auto">
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
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
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

                        {{-- Security Badge --}}
                        {{-- <div class="text-center mt-4">
                            <p class="security-text">
                                <i class="fas fa-shield-alt me-2"></i>
                                <span class="fw-semibold">Secure & Safe</span> - Your data is protected
                            </p>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
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
            padding-right: 0;
            text-align: center;
        }

        .brand-title {
            font-size: 2.5rem;
        }

        .brand-subtitle {
            font-size: 1.25rem;
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

        // Always ensure element is visible and styled for updates
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

        update(); // immediate update on start
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

    // Observe DOM updates to restart countdown when error changes
    const observer = new MutationObserver(() => initCountdown());
    observer.observe(document.body, { childList: true, subtree: true });

    // Initial delayed start to allow Laravel to render error
    setTimeout(initCountdown, 300);
});
</script>