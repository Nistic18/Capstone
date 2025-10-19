@extends('layouts.app')
@section('title', 'Manage Users')
@section('content')
<div class="mt-5">
    {{-- Success/Error Alert --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3" style="color: #155724; font-size: 1.2rem;"></i>
                <span style="color: #155724; font-weight: 500;">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-3" style="font-size: 1.2rem;"></i>
                <span style="font-weight: 500;">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Pending Applications Section --}}
    @if(isset($pendingApplications) && $pendingApplications->count() > 0)
    <div class="card border-0 shadow-sm mb-5" style="border-radius: 20px;">
        <div class="card-header border-0 py-4" style="background: linear-gradient(45deg, #ff6b6b, #ee5a6f); border-radius: 20px 20px 0 0;">
            <h4 class="fw-bold mb-0 text-white">
                <i class="fas fa-clock me-2"></i>
                Pending Supplier Applications ({{ $pendingApplications->count() }})
            </h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th class="border-0 py-3 px-4">Business Name</th>
                            <th class="border-0 py-3 px-4">Email</th>
                            <th class="border-0 py-3 px-4">Phone</th>
                            <th class="border-0 py-3 px-4">Location</th>
                            <th class="border-0 py-3 px-4">Documents</th>
                            <th class="border-0 py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingApplications as $app)
                        <tr class="border-bottom">
                            <td class="px-4 py-4">
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $app->business_name }}</h6>
                                    <small class="text-muted">License: {{ $app->business_license_id }}</small>
                                </div>
                            </td>
                            <td class="px-4 py-4">{{ $app->email_address }}</td>
                            <td class="px-4 py-4">{{ $app->phone_number ?? 'N/A' }}</td>
                            <td class="px-4 py-4">
                                <small>{{ $app->city }}, {{ $app->province }}<br>{{ $app->country }}</small>
                            </td>
                            <td class="px-4 py-4">
                                <a href="{{ asset('storage/' . $app->pdf_file) }}" target="_blank" 
                                   class="btn btn-sm btn-outline-info" style="border-radius: 10px;">
                                    <i class="fas fa-file-pdf me-1"></i>View Documents
                                </a>
                            </td>
                            <td class="px-4 py-4">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Approve Button --}}
                                    <form action="{{ route('users.approveSupplier', $app->id) }}" method="POST" 
                                          onsubmit="return confirm('Approve this supplier application? A new account will be created with default password.')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" 
                                                style="border-radius: 10px;" data-bs-toggle="tooltip" title="Approve Application">
                                            <i class="fas fa-check me-1"></i>Approve
                                        </button>
                                    </form>

                                    {{-- Reject Button --}}
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            style="border-radius: 10px;" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#rejectModal{{ $app->id }}"
                                            title="Reject Application">
                                        <i class="fas fa-times me-1"></i>Reject
                                    </button>
                                </div>

                                {{-- Reject Modal --}}
                                <div class="modal fade" id="rejectModal{{ $app->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title fw-bold">Reject Application</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('users.rejectSupplier', $app->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <p class="mb-3">Rejecting application for: <strong>{{ $app->business_name }}</strong></p>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Reason for Rejection <span class="text-danger">*</span></label>
                                                        <textarea name="rejection_reason" class="form-control" rows="4" 
                                                                  placeholder="Enter reason for rejection..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-times me-1"></i>Reject Application
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
                    <i class="fas fa-truck mb-3" style="font-size: 2.5rem;"></i>
                    <h3 class="fw-bold mb-1">{{ $supplierCount }}</h3>
                    <p class="mb-0 text-white-50">Suppliers</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" 
                 style="border-radius: 20px; background: linear-gradient(45deg, #fd7e14, #e0a800);">
                <div class="card-body text-center text-white p-4">
                    <i class="fas fa-shopping-cart mb-3" style="font-size: 2.5rem;"></i>
                    <h3 class="fw-bold mb-1">{{ $buyerCount + $resellerCount }}</h3>
                    <p class="mb-0 text-white-50">Buyers</p>
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
                                <th class="border-0 py-3 px-4">User</th>
                                <th class="border-0 py-3 px-4">Email</th>
                                <th class="border-0 py-3 px-4">Role</th>
                                <th class="border-0 py-3 px-4">Joined</th>
                                <th class="border-0 py-3 px-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr class="border-bottom">
                                <td class="px-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 45px; height: 45px; background: linear-gradient(45deg, #667eea, #764ba2);">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                            <small class="text-muted">ID: #{{ $user->id }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4">{{ $user->email }}</td>

                                <td class="px-4 py-4">
                                    @switch($user->role)
                                        @case('admin')
                                            <span class="badge px-3 py-2" style="background: linear-gradient(45deg, #28a745, #20c997); border-radius: 25px;">
                                                <i class="fas fa-shield-alt me-1"></i>Administrator
                                            </span>
                                            @break
                                        @case('supplier')
                                            <span class="badge px-3 py-2" style="background: linear-gradient(45deg, #6f42c1, #5a3fbd); border-radius: 25px;">
                                                <i class="fas fa-truck me-1"></i>Supplier
                                            </span>
                                            @break
                                        @case('buyer')
                                            <span class="badge px-3 py-2" style="background: linear-gradient(45deg, #fd7e14, #e0a800); border-radius: 25px;">
                                                <i class="fas fa-shopping-cart me-1"></i>Buyer
                                            </span>
                                            @break
                                        @default
                                            <span class="badge px-3 py-2" style="background: #6c757d; border-radius: 25px;">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                    @endswitch
                                </td>

                                <td class="px-4 py-4">
                                    <div>
                                        <span>{{ $user->created_at->format('M d, Y') }}</span>
                                        <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('users.edit', $user) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           style="border-radius: 10px;">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('users.destroy', $user) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Delete {{ $user->name }}?')">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    style="border-radius: 10px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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

    {{-- Pagination --}}
    @if(!$users->isEmpty())
        <div class="d-flex justify-content-center mt-5">
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>

<style>
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
        transition: all 0.2s ease;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) {
        return new bootstrap.Tooltip(el);
    });
});
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection