@extends('layouts.app')
@section('title', 'Manage Users')
@section('content')
<div class="mt-5">
    {{-- Header Section --}}
    {{-- <div class="card border-0 shadow-lg mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="fas fa-users-cog text-white" style="font-size: 3rem;"></i>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">👥 User Management</h1>
            <p class="lead text-white-50 mb-0">Manage user accounts, roles, and permissions</p>
        </div>
    </div> --}}

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" 
             style="border-radius: 15px; background: linear-gradient(45deg, #d4edda, #c3e6cb);">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3" style="color: #155724; font-size: 1.2rem;"></i>
                <span style="color: #155724; font-weight: 500;">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row mb-5">
        @php
            $totalUsers = $users->total();
            $adminCount = $users->where('role', 'admin')->count();
            $buyerCount = $users->where('role', 'buyer')->count();
            $resellerCount = $users->where('role', 'reseller')->count();
            $supplierCount = $users->where('role', 'supplier')->count();
        @endphp

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" 
                 style="border-radius: 20px; background: linear-gradient(45deg, #667eea, #764ba2);">
                <div class="card-body text-center text-white p-4">
                    <i class="fas fa-users mb-3" style="font-size: 2.5rem;"></i>
                    <h3 class="fw-bold mb-1">{{ $totalUsers }}</h3>
                    <p class="mb-0 text-white-50">Total Users</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" 
                 style="border-radius: 20px; background: linear-gradient(45deg, #28a745, #20c997);">
                <div class="card-body text-center text-white p-4">
                    <i class="fas fa-shield-alt mb-3" style="font-size: 2.5rem;"></i>
                    <h3 class="fw-bold mb-1">{{ $adminCount }}</h3>
                    <p class="mb-0 text-white-50">Administrators</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" 
                 style="border-radius: 20px; background: linear-gradient(45deg, #17a2b8, #138496);">
                <div class="card-body text-center text-white p-4">
                    <i class="fas fa-store mb-3" style="font-size: 2.5rem;"></i>
                    <h3 class="fw-bold mb-1">{{ $resellerCount }}</h3>
                    <p class="mb-0 text-white-50">Resellers</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" 
                 style="border-radius: 20px; background: linear-gradient(45deg, #fd7e14, #e0a800);">
                <div class="card-body text-center text-white p-4">
                    <i class="fas fa-shopping-cart mb-3" style="font-size: 2.5rem;"></i>
                    <h3 class="fw-bold mb-1">{{ $buyerCount + $supplierCount }}</h3>
                    <p class="mb-0 text-white-50">Buyers & Suppliers</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Users Table Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-header border-0 py-4" style="background: linear-gradient(45deg, #f8f9fa, #ffffff); border-radius: 20px 20px 0 0;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1" style="color: #2c3e50;">
                        <i class="fas fa-table me-2" style="color: #667eea;"></i>
                        User Directory
                    </h4>
                    <p class="text-muted mb-0">Showing {{ $users->count() }} of {{ $users->total() }} users</p>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($users->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-users text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                    <h3 class="text-muted mb-3">No Users Found</h3>
                    <p class="text-muted">There are no users to display at the moment.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th class="border-0 py-3 px-4" style="border-radius: 0; color: #2c3e50; font-weight: 600;">
                                    <i class="fas fa-user me-2" style="color: #667eea;"></i>User
                                </th>
                                <th class="border-0 py-3 px-4" style="color: #2c3e50; font-weight: 600;">
                                    <i class="fas fa-envelope me-2" style="color: #667eea;"></i>Email
                                </th>
                                <th class="border-0 py-3 px-4" style="color: #2c3e50; font-weight: 600;">
                                    <i class="fas fa-shield-alt me-2" style="color: #667eea;"></i>Role
                                </th>
                                <th class="border-0 py-3 px-4" style="color: #2c3e50; font-weight: 600;">
                                    <i class="fas fa-calendar me-2" style="color: #667eea;"></i>Joined
                                </th>
                                <th class="border-0 py-3 px-4" style="color: #2c3e50; font-weight: 600;">
                                    <i class="fas fa-store me-2" style="color: #667eea;"></i>Reseller Status
                                </th>
                                {{-- <th class="border-0 py-3 px-4 text-center" style="color: #2c3e50; font-weight: 600;">
                                    <i class="fas fa-cogs me-2" style="color: #667eea;"></i>Actions
                                </th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="border-bottom" style="border-color: #f1f3f4 !important; transition: all 0.2s ease;">
                                {{-- User Info --}}
                                <td class="px-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 45px; height: 45px; background: linear-gradient(45deg, #667eea, #764ba2);">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold" style="color: #2c3e50;">{{ $user->name }}</h6>
                                            <small class="text-muted">ID: #{{ $user->id }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td class="px-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-at me-2 text-muted"></i>
                                        <span style="color: #2c3e50;">{{ $user->email }}</span>
                                    </div>
                                </td>

                                {{-- Role Badge --}}
                                <td class="px-4 py-4">
                                    @switch($user->role)
                                        @case('admin')
                                            <span class="badge px-3 py-2" 
                                                  style="background: linear-gradient(45deg, #28a745, #20c997); border-radius: 25px; font-size: 0.8rem;">
                                                <i class="fas fa-shield-alt me-1"></i>Administrator
                                            </span>
                                            @break
                                        @case('reseller')
                                            <span class="badge px-3 py-2" 
                                                  style="background: linear-gradient(45deg, #17a2b8, #138496); border-radius: 25px; font-size: 0.8rem;">
                                                <i class="fas fa-store me-1"></i>Reseller
                                            </span>
                                            @break
                                        @case('supplier')
                                            <span class="badge px-3 py-2" 
                                                  style="background: linear-gradient(45def, #6f42c1, #5a3fbd); border-radius: 25px; font-size: 0.8rem;">
                                                <i class="fas fa-truck me-1"></i>Supplier
                                            </span>
                                            @break
                                        @case('buyer')
                                            <span class="badge px-3 py-2" 
                                                  style="background: linear-gradient(45deg, #fd7e14, #e0a800); border-radius: 25px; font-size: 0.8rem;">
                                                <i class="fas fa-shopping-cart me-1"></i>Buyer
                                            </span>
                                            @break
                                        @default
                                            <span class="badge px-3 py-2" 
                                                  style="background: #6c757d; border-radius: 25px; font-size: 0.8rem;">
                                                <i class="fas fa-question me-1"></i>Unknown
                                            </span>
                                    @endswitch
                                </td>

                                {{-- Join Date --}}
                                <td class="px-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-check me-2 text-muted"></i>
                                        <div>
                                            <span style="color: #2c3e50;">{{ $user->created_at->format('M d, Y') }}</span>
                                            <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </td>
                                {{-- Reseller Status
<td class="px-4 py-4">
    @if($user->latestResellerApplication)
        @if($user->latestResellerApplication->status == 'pending')
            <span class="badge bg-warning">Pending</span>
        @elseif($user->latestResellerApplication->status == 'approved')
            <span class="badge bg-success">Approved</span>
        @else
            <span class="badge bg-danger">Rejected</span>
        @endif
    @else
        <span class="badge bg-secondary">No Application</span>
    @endif
</td> --}}
                                {{-- Actions --}}
<td class="px-4 py-4 text-center">
    <div class="d-flex justify-content-center gap-2 flex-wrap">
        {{-- Edit Button --}}
        <a href="{{ route('users.edit', $user) }}" 
           class="btn btn-sm btn-outline-warning" 
           style="border-radius: 10px; border-width: 2px;"
           data-bs-toggle="tooltip" 
           title="Edit User">
            <i class="fas fa-edit"></i>
        </a>

        {{-- Delete Button --}}
        <form action="{{ route('users.destroy', $user) }}" 
              method="POST" 
              style="display:inline;"
              onsubmit="return confirmDelete('{{ $user->name }}')">
            @csrf 
            @method('DELETE')
            <button type="submit" 
                    class="btn btn-sm btn-outline-danger" 
                    style="border-radius: 10px; border-width: 2px;"
                    data-bs-toggle="tooltip" 
                    title="Delete User">
                <i class="fas fa-trash"></i>
            </button>
        </form>

        {{-- ✅ View Document Button - MOVED UP AND IMPROVED --}}
        {{-- @if($user->latestResellerApplication)
    <a href="{{ asset('storage/' . $user->latestResellerApplication->valid_id_path) }}" target="_blank" 
       class="btn btn-sm btn-outline-info">
        <i class="fas fa-id-card"></i> Valid ID
    </a>
    <a href="{{ asset('storage/' . $user->latestResellerApplication->business_path) }}" target="_blank" 
       class="btn btn-sm btn-outline-info">
        <i class="fas fa-briefcase"></i> Permit
    </a>
    <a href="{{ asset('storage/' . $user->latestResellerApplication->other_doc_path) }}" target="_blank" 
       class="btn btn-sm btn-outline-info">
        <i class="fas fa-file-alt"></i> Other
    </a>
@endif --}}


        {{-- Approval/Rejection Buttons
@if($user->latestResellerApplication && $user->latestResellerApplication->status == 'pending')
    <form action="{{ route('users.approveReseller', $user->id) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-success">
            <i class="fas fa-check"></i>
        </button>
    </form>

    <!-- Reject with reason -->
    <form action="{{ route('users.rejectReseller', $user->id) }}" method="POST" class="d-inline">
        @csrf
        <input type="text" name="rejection_reason" placeholder="Reason for rejection"
               class="form-control form-control-sm d-inline-block" style="width: 200px;" required>
        <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="fas fa-times"></i>
        </button>
    </form>
@endif --}}


    </div>
</td>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Enhanced Pagination --}}
    @if(!$users->isEmpty())
        <div class="d-flex justify-content-center mt-5">
            <nav aria-label="User pagination">
                {{ $users->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    @endif
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="deleteModalLabel" style="color: #2c3e50;">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-0">Are you sure you want to delete this user? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                    Cancel
                </button>
                <button type="button" class="btn btn-danger" style="border-radius: 10px;" onclick="proceedDelete()">
                    <i class="fas fa-trash me-1"></i>Delete User
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
        transition: all 0.2s ease;
    }
    
    .btn-outline-warning {
        border-color: #ffc107;
        color: #ffc107;
    }
    
    .btn-outline-warning:hover {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
        transform: translateY(-1px);
    }
    
    .btn-outline-danger {
        border-color: #dc3545;
        color: #dc3545;
    }
    
    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
        transform: translateY(-1px);
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(45deg, #5a6fd8, #6a42a0);
        transform: translateY(-1px);
    }
    
    .btn-outline-primary {
        border-color: #667eea;
        color: #667eea;
    }
    
    .btn-outline-primary:hover {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    .pagination .page-link {
        border-radius: 10px;
        margin: 0 2px;
        border: 2px solid #e9ecef;
        color: #667eea;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-color: #667eea;
    }
    
    .alert {
        border: none;
    }
    
    @media (max-width: 768px) {
        .display-4 {
            font-size: 2rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .table-responsive {
            font-size: 0.9rem;
        }
        
        .d-flex.gap-2 {
            flex-direction: column;
        }
    }
</style>

{{-- JavaScript --}}
<script>
let deleteForm = null;

function confirmDelete(userName) {
    if (confirm(`Are you sure you want to delete ${userName}? This action cannot be undone.`)) {
        return true;
    }
    return false;
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection