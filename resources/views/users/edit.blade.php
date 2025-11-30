@extends('layouts.app')
@section('title', 'Manage User')
@section('content')
<div class="mt-5">
    {{-- Header Section --}}
    {{-- <div class="card border-0 shadow-lg mb-5" style="background: linear-gradient(135deg, #667eea 0%, #0bb364 100%); border-radius: 20px;">
        <div class="card-body text-center py-4">
            <div class="mb-3">
                <i class="fas fa-user-edit text-white" style="font-size: 2.5rem;"></i>
            </div>
            <h1 class="h3 fw-bold text-white mb-2">Edit User Account</h1>
            <p class="text-white-50 mb-0">Manage user information and permissions</p>
        </div>
    </div> --}}

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item">
                <a href="{{ route('users.index') }}" class="text-decoration-none" style="color: #667eea;">
                    <i class="fas fa-users me-1"></i>Users
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page" style="color: #6c757d;">
                <i class="fas fa-edit me-1"></i>Edit {{ $user->name }}
            </li>
        </ol>
    </nav>

    {{-- Main Form Card --}}
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header border-0 text-center py-4" style="background: linear-gradient(45deg, #f8f9fa, #ffffff); border-radius: 20px 20px 0 0;">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 60px; height: 60px; background: linear-gradient(45deg, #667eea, #0bb364);">
                            <i class="fas fa-user text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="text-start">
                            <h4 class="fw-bold mb-0" style="color: #2c3e50;">{{ $user->name }}</h4>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('users.update', $user) }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Name Field --}}
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold" style="color: #2c3e50;">
                                <i class="fas fa-user me-2" style="color: #667eea;"></i>Full Name
                            </label>
                            <div class="position-relative">
                                <input type="text" id="name" name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       style="border-radius: 15px; border: 2px solid #e9ecef; padding-left: 45px;" 
                                       required>
                                <i class="fas fa-id-badge position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                        </div>

                        {{-- Email Field --}}
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold" style="color: #2c3e50;">
                                <i class="fas fa-envelope me-2" style="color: #667eea;"></i>Email Address
                            </label>
                            <div class="position-relative">
                                <input type="email" id="email" name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       style="border-radius: 15px; border: 2px solid #e9ecef; padding-left: 45px;" 
                                       required>
                                <i class="fas fa-at position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="valid-feedback">Valid email address!</div>
                            </div>
                        </div>

                        {{-- Role Field --}}
                        <div class="mb-4">
                            <label for="role" class="form-label fw-semibold" style="color: #2c3e50;">
                                <i class="fas fa-shield-alt me-2" style="color: #667eea;"></i>User Role
                            </label>
                            <div class="position-relative">
                                <select id="role" name="role" 
                                        class="form-select @error('role') is-invalid @enderror" 
                                        style="border-radius: 15px; border: 2px solid #e9ecef; padding-left: 45px;" 
                                        required>
                                    <option value="">Choose a role...</option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                        🛡️ Administrator
                                    </option>
                                    <option value="buyer" {{ old('role', $user->role) === 'buyer' ? 'selected' : '' }}>
                                        🛒 Buyer
                                    </option>
                                    <option value="supplier" {{ old('role', $user->role) === 'supplier' ? 'selected' : '' }}>
                                        🚚 Supplier
                                    </option>
                                </select>
                                <i class="fas fa-user-tag position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="valid-feedback">Role selected!</div>
                            </div>
                            <small class="text-muted mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                Choose the appropriate role for this user's permissions
                            </small>
                        </div>

                        {{-- Role Descriptions --}}
                        <div class="mb-4">
                            <div class="card" style="background: #f8f9fa; border: none; border-radius: 15px;">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-2" style="color: #2c3e50;">
                                        <i class="fas fa-info-circle me-2" style="color: #667eea;"></i>
                                        Role Permissions
                                    </h6>
                                    <div class="row g-2 small">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-danger me-2">🛡️</span>
                                                <strong>Admin:</strong> Full system access
                                            </div>
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-primary me-2">🛒</span>
                                                <strong>Buyer:</strong> Purchase products only
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-warning me-2">🚚</span>
                                                <strong>Supplier:</strong> Wholesale provider
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
{{-- Reset Password Field --}}
<div class="mb-4">
    <label for="password" class="form-label fw-semibold" style="color: #2c3e50;">
        <i class="fas fa-key me-2" style="color: #667eea;"></i>New Password
    </label>
    <div class="position-relative">
        <input type="password" id="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               style="border-radius: 15px; border: 2px solid #e9ecef; padding-left: 45px;">
        <i class="fas fa-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="valid-feedback">Strong password!</div>
    </div>
    <small class="text-muted">
        <i class="fas fa-info-circle me-1"></i>
        Leave blank if you don’t want to change the password.
    </small>
</div>
                        {{-- Action Buttons --}}
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('users.index') }}" 
                               class="btn btn-outline-secondary px-4" 
                               style="border-radius: 15px; border-width: 2px;">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" 
                                    class="btn btn-success px-4" 
                                    style="border-radius: 15px; background: linear-gradient(45deg, #28a745, #20c997); border: none;">
                                <i class="fas fa-save me-2"></i>Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- User Activity Summary (Optional Enhancement) --}}
    <div class="row justify-content-center mt-4">
        <div class="col-lg-8 col-xl-6">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header border-0 py-3" style="background: linear-gradient(45deg, #f8f9fa, #ffffff); border-radius: 20px 20px 0 0;">
                    <h6 class="fw-bold mb-0" style="color: #2c3e50;">
                        <i class="fas fa-chart-line me-2" style="color: #667eea;"></i>
                        Account Summary
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 rounded-3" style="background: rgba(102, 126, 234, 0.1);">
                                <i class="fas fa-calendar-alt mb-2" style="color: #667eea; font-size: 1.5rem;"></i>
                                <p class="mb-0 small text-muted">Member Since</p>
                                <strong style="color: #2c3e50;">{{ $user->created_at->format('M Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 rounded-3" style="background: rgba(40, 167, 69, 0.1);">
                                <i class="fas fa-user-check mb-2" style="color: #28a745; font-size: 1.5rem;"></i>
                                <p class="mb-0 small text-muted">Current Role</p>
                                <strong style="color: #2c3e50;">{{ ucfirst($user->role) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 rounded-3" style="background: rgba(253, 126, 20, 0.1);">
                                <i class="fas fa-clock mb-2" style="color: #fd7e14; font-size: 1.5rem;"></i>
                                <p class="mb-0 small text-muted">Last Updated</p>
                                <strong style="color: #2c3e50;">{{ $user->updated_at->diffForHumans() }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-success {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-success:hover {
        background: linear-gradient(45deg, #218838, #1ea085);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        transform: translateY(-1px);
    }
    
    .card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "→";
        color: #667eea;
    }
    
    .badge {
        font-size: 0.7rem;
    }
    
    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .form-control.is-valid:focus,
    .form-select.is-valid:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }
        
        .d-flex.gap-3 {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
        }
    }
</style>

{{-- Form Validation JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap form validation
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Real-time validation feedback
    const inputs = document.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    });
});
</script>

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection