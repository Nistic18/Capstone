@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 mt-5">
        <div class="card">
            <div class="card-header">{{ __('Change Password') }}</div>
            
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card-body">
                <form method="POST" action="{{ route('profile.password') }}" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div class="form-group row mb-3">
                        <label for="current_password" class="col-md-4 col-form-label text-md-right">{{ __('Current Password') }}</label>
                        <div class="col-md-6">
                            <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required autocomplete="current-password">
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="form-group row mb-3">
                        <label for="new_password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>
                        <div class="col-md-6">
                            <div class="password-wrapper">
                                <input id="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" required autocomplete="new-password">
                                <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye" id="toggleNewPasswordIcon"></i>
                                </button>
                            </div>

                            <!-- Password Requirements -->
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

                            <!-- Password Strength -->
                            <div id="password-strength" class="mt-2">
                                <small id="password-strength-text" class="fw-semibold"></small>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0;"></div>
                                </div>
                            </div>

                            @error('new_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group row mb-4">
                        <label for="new_password_confirmation" class="col-md-4 col-form-label text-md-right">{{ __('Confirm New Password') }}</label>
                        <div class="col-md-6">
                            <div class="password-wrapper">
                                <input id="new_password_confirmation" type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" name="new_password_confirmation" required autocomplete="new-password">
                                <button type="button" class="password-toggle" onclick="togglePassword('new_password_confirmation')">
                                    <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                                </button>
                            </div>
                            @error('new_password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Confirm Password') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JS for Password Strength & Toggle -->
@push('scripts')
<script>
const newPasswordInput = document.getElementById('new_password');
const strengthText = document.getElementById('password-strength-text');
const strengthBar = document.getElementById('password-strength-bar');
const confirmPasswordInput = document.getElementById('new_password_confirmation');

newPasswordInput.addEventListener('input', () => {
    const value = newPasswordInput.value;
    updatePasswordRequirements(value);
    const strength = getPasswordStrength(value);

    if(strength.score === 0){
        strengthText.textContent = '';
        strengthBar.style.width = '0';
    } else {
        strengthText.textContent = strength.label;
        strengthText.style.color = strength.color;
        strengthBar.style.width = strength.percent + '%';
        strengthBar.style.backgroundColor = strength.color;
    }

    // Confirm password validation live
    validateConfirmPassword();
});

confirmPasswordInput.addEventListener('input', validateConfirmPassword);

function validateConfirmPassword() {
    if(confirmPasswordInput.value !== newPasswordInput.value){
        confirmPasswordInput.setCustomValidity('Passwords do not match');
    } else {
        confirmPasswordInput.setCustomValidity('');
    }
}

function updatePasswordRequirements(password){
    const lengthReq = document.getElementById('req-length');
    const uppercaseReq = document.getElementById('req-uppercase');
    const numberReq = document.getElementById('req-number');
    const specialReq = document.getElementById('req-special');

    password.length >= 8 ? lengthReq.classList.add('valid') : lengthReq.classList.remove('valid');
    /[A-Z]/.test(password) ? uppercaseReq.classList.add('valid') : uppercaseReq.classList.remove('valid');
    /[0-9]/.test(password) ? numberReq.classList.add('valid') : numberReq.classList.remove('valid');
    /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password) ? specialReq.classList.add('valid') : specialReq.classList.remove('valid');
}

function getPasswordStrength(password){
    let score = 0;
    if(password.length >= 8) score++;
    if(/[A-Z]/.test(password)) score++;
    if(/[0-9]/.test(password)) score++;
    if(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) score++;

    if(password.length === 0) return { score:0, label:'', percent:0, color:'' };

    switch(score){
        case 1: return { score, label:'Weak', percent:25, color:'#e53935' };
        case 2: return { score, label:'Fair', percent:50, color:'#ff9800' };
        case 3: return { score, label:'Good', percent:75, color:'#ffb300' };
        case 4: return { score, label:'Strong', percent:100, color:'#43a047' };
        default: return { score:0, label:'', percent:0, color:'' };
    }
}

function togglePassword(inputId){
    const passwordInput = document.getElementById(inputId);
    const iconId = inputId === 'new_password' ? 'toggleNewPasswordIcon' : 'toggleConfirmPasswordIcon';
    const toggleIcon = document.getElementById(iconId);

    if(passwordInput.type === 'password'){
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
@endpush

<style>
/* Password Requirements */
.password-requirements .requirement-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0;
}
.requirement-item.valid .requirement-icon { color: #43a047; transform: scale(1.2); }
.requirement-item.valid .requirement-text { color: #43a047; font-weight: 600; }

/* Password Toggle Button */
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
    z-index: 10;
}
.password-toggle:hover { color: #007bff; }
.password-wrapper { position: relative; }
</style>
@endsection
