@extends('layouts.app')

@section('title', 'Reset Password - Fish Market')

@section('content')
<div class="reset-password-wrapper">
    {{-- Back to Home Button --}}
    <div class="back-to-home">
        <a href="{{ url('/') }}" class="btn-back">
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
                                     class="reset-logo"
                                     style="height: 220px; width: auto; filter: drop-shadow(0 4px 15px rgba(0,0,0,0.2));">
                            </div>
                            <h1 class="brand-title">Reset Password</h1>
                            <p class="brand-subtitle">Create a new password for your Fish Market account. Make sure it's strong and secure.</p>
                        </div>
                    </div>

                    {{-- Right Section - Reset Password Form --}}
                    <div class="col-lg-5 col-md-5 mb-4 mb-md-0">
                        <div class="reset-box mx-auto">
                            {{-- Display any status messages --}}
                            @if (session('status'))
                                <div class="alert alert-success border-0 mb-4" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('status') }}
                                </div>
                            @endif

                            {{-- Display error if token is invalid --}}
                            @if ($errors->has('email'))
                                <div class="alert alert-danger border-0 mb-4" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    {{ $errors->first('email') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf

                                <input type="hidden" name="token" value="{{ $token ?? request()->route('token') }}">

                                {{-- Email Field --}}
                                <div class="mb-3">
                                    <input id="email" 
                                           type="email" 
                                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           name="email" 
                                           value="{{ $email ?? old('email') ?? request()->email }}" 
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
                                               autocomplete="new-password"
                                               placeholder="New Password">
                                        <button type="button" class="password-toggle" onclick="togglePassword('password', 'togglePasswordIcon')">
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>

                                    @error('password')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
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
                                               placeholder="Confirm Password">
                                        <button type="button" class="password-toggle" onclick="togglePassword('password-confirm', 'toggleConfirmIcon')">
                                            <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Reset Password Button --}}
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                                        Reset Password
                                    </button>
                                </div>

                                {{-- Divider --}}
                                <div class="divider"></div>

                                {{-- Back to Login Link --}}
                                <div class="text-center">
                                    <a href="{{ route('login') }}" class="back-link">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Login
                                    </a>
                                </div>
                            </form>
                        </div>

                        {{-- Security Badge --}}
                        <div class="text-center mt-4">
                            <p class="security-text">
                                <i class="fas fa-shield-alt me-2"></i>
                                <span class="fw-semibold">Secure & Safe</span> - Your data is protected
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
    /* Reset page logo styling - matches login */
    .reset-logo {
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

    .reset-logo:hover {
        transform: scale(1.08) rotate(5deg);
        animation: none;
    }

    /* Responsive logo sizing for reset - matches login */
    @media (max-width: 992px) {
        .reset-logo {
            height: 180px !important;
        }
    }

    @media (max-width: 768px) {
        .reset-logo {
            height: 150px !important;
        }
    }

    @media (max-width: 576px) {
        .reset-logo {
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
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .reset-password-wrapper {
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

    /* Reset Box */
    .reset-box {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 10px 20px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        max-width: 520px;
        margin: 0 auto;
    }

    /* Alert Styles */
    .alert {
        border-radius: 8px;
        padding: 1rem;
        font-size: 0.938rem;
    }

    .alert-success {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        color: #2e7d32;
    }

    .alert-danger {
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
        color: #c62828;
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

    /* Back to Login Link */
    .back-link {
        color: #0bb364;
        font-size: 0.938rem;
        text-decoration: none;
        transition: color 0.2s;
        font-weight: 500;
    }

    .back-link:hover {
        color: #289c58;
        text-decoration: underline;
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

        .reset-box {
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

    /* Font family override for all elements */
    body, 
    h1, h2, h3, h4, h5, h6, 
    p, span, a, div, input, select, button, label {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
    }
</style>

{{-- Custom JavaScript --}}
<script>
    // Password toggle functionality
    function togglePassword(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
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
</script>
@endsection