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
                                <small>{{ $app->address }}</small>
                            </td>
                            <td class="px-4 py-4">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- View Documents Button --}}
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            style="border-radius: 10px;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewDocsModal{{ $app->id }}">
                                        <i class="fas fa-file-image me-1"></i>View Documents
                                    </button>

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

                                {{-- View Documents Modal --}}
                                <div class="modal fade" id="viewDocsModal{{ $app->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                            <div class="modal-header border-0" style="background: linear-gradient(45deg, #667eea, #764ba2);">
                                                <h5 class="modal-title fw-bold text-white">
                                                    <i class="fas fa-folder-open me-2"></i>
                                                    Application Documents - {{ $app->business_name }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                {{-- Application Info Summary --}}
                                                <div class="alert alert-light border mb-4">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <strong><i class="fas fa-building me-2 text-primary"></i>Business Name:</strong>
                                                            <p class="mb-0 ms-4">{{ $app->business_name }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong><i class="fas fa-envelope me-2 text-primary"></i>Email:</strong>
                                                            <p class="mb-0 ms-4">{{ $app->email_address }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong><i class="fas fa-phone me-2 text-primary"></i>Phone:</strong>
                                                            <p class="mb-0 ms-4">{{ $app->phone_number ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong><i class="fas fa-id-card me-2 text-primary"></i>License ID:</strong>
                                                            <p class="mb-0 ms-4">{{ $app->business_license_id }}</p>
                                                        </div>
                                                        <div class="col-12">
                                                            <strong><i class="fas fa-map-marker-alt me-2 text-primary"></i>Address:</strong>
                                                            <p class="mb-0 ms-4">{{ $app->address }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Documents Grid --}}
                                                <h6 class="mb-3 fw-bold text-secondary">
                                                    <i class="fas fa-images me-2"></i>Submitted Documents
                                                </h6>

                                                <div class="row g-4">
                                                    {{-- Business Permit --}}
                                                    @if($app->business_permit_photo)
                                                    <div class="col-md-6">
                                                        <div class="document-card">
                                                            <div class="document-header">
                                                                <i class="fas fa-file-contract me-2"></i>
                                                                <span>Business Permit</span>
                                                            </div>
                                                            <div class="document-body">
                                                                <img src="{{ asset('storage/' . $app->business_permit_photo) }}" 
                                                                     alt="Business Permit"
                                                                     class="document-image"
                                                                     onclick="openImageModal('{{ asset('storage/' . $app->business_permit_photo) }}', 'Business Permit')">
                                                                <a href="{{ asset('storage/' . $app->business_permit_photo) }}" 
                                                                   target="_blank" 
                                                                   class="btn btn-sm btn-outline-primary mt-2 w-100">
                                                                    <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    {{-- Sanitation Certificate --}}
                                                    @if($app->sanitation_cert_photo)
                                                    <div class="col-md-6">
                                                        <div class="document-card">
                                                            <div class="document-header">
                                                                <i class="fas fa-file-medical me-2"></i>
                                                                <span>Sanitation Certificate</span>
                                                            </div>
                                                            <div class="document-body">
                                                                <img src="{{ asset('storage/' . $app->sanitation_cert_photo) }}" 
                                                                     alt="Sanitation Certificate"
                                                                     class="document-image"
                                                                     onclick="openImageModal('{{ asset('storage/' . $app->sanitation_cert_photo) }}', 'Sanitation Certificate')">
                                                                <a href="{{ asset('storage/' . $app->sanitation_cert_photo) }}" 
                                                                   target="_blank" 
                                                                   class="btn btn-sm btn-outline-primary mt-2 w-100">
                                                                    <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    {{-- Government ID 1 --}}
                                                    @if($app->govt_id_photo_1)
                                                    <div class="col-md-6">
                                                        <div class="document-card">
                                                            <div class="document-header">
                                                                <i class="fas fa-id-card me-2"></i>
                                                                <span>Government ID #1</span>
                                                            </div>
                                                            <div class="document-body">
                                                                <img src="{{ asset('storage/' . $app->govt_id_photo_1) }}" 
                                                                     alt="Government ID 1"
                                                                     class="document-image"
                                                                     onclick="openImageModal('{{ asset('storage/' . $app->govt_id_photo_1) }}', 'Government ID #1')">
                                                                <a href="{{ asset('storage/' . $app->govt_id_photo_1) }}" 
                                                                   target="_blank" 
                                                                   class="btn btn-sm btn-outline-primary mt-2 w-100">
                                                                    <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    {{-- Government ID 2 --}}
                                                    @if($app->govt_id_photo_2)
                                                    <div class="col-md-6">
                                                        <div class="document-card">
                                                            <div class="document-header">
                                                                <i class="fas fa-id-card me-2"></i>
                                                                <span>Government ID #2</span>
                                                            </div>
                                                            <div class="document-body">
                                                                <img src="{{ asset('storage/' . $app->govt_id_photo_2) }}" 
                                                                     alt="Government ID 2"
                                                                     class="document-image"
                                                                     onclick="openImageModal('{{ asset('storage/' . $app->govt_id_photo_2) }}', 'Government ID #2')">
                                                                <a href="{{ asset('storage/' . $app->govt_id_photo_2) }}" 
                                                                   target="_blank" 
                                                                   class="btn btn-sm btn-outline-primary mt-2 w-100">
                                                                    <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
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

{{-- Image Preview Modal --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="imagePreviewTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="imagePreviewSrc" src="" alt="Document Preview" style="max-width: 100%; max-height: 85vh; border-radius: 10px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>


{{-- CSS --}}
<style>
.table tbody tr:hover { background-color: #f8f9fa; transition: none; }
.document-card { border:2px solid #e9ecef; border-radius:15px; overflow:hidden; transition: none; background:#fff; }
.document-header { background: linear-gradient(45deg,#667eea,#764ba2); color:white; padding:12px 15px; font-weight:600; font-size:14px; }
.document-body { padding:15px; }
.document-image { width:100%; height:300px; object-fit:contain; border-radius:10px; cursor:pointer; border:2px solid #e9ecef; }
.modal-xl { max-width:1200px; }
#imagePreviewModal .modal-content { background: rgba(0,0,0,0.95) !important; }
#imagePreviewModal { z-index:1060; }
.modal[id^="viewDocsModal"] { z-index:1050; }
.modal-backdrop.show + .modal-backdrop.show { opacity:0 !important; }
.modal-backdrop { opacity:0.6 !important; }
body.modal-open { overflow:hidden; }
body, 
h1, h2, h3, h4, h5, h6, 
p, span, a, div, input, select, button, label {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
}
</style>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el){ return new bootstrap.Tooltip(el); });

    // Image Preview Modal
    window.openImageModal = function(imageSrc, title){
        event.stopPropagation();
        document.getElementById('imagePreviewSrc').src = imageSrc;
        document.getElementById('imagePreviewTitle').textContent = title;
        const previewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'), {backdrop:true, keyboard:true});
        previewModal.show();
    }

    // Close modal on click outside image
    document.getElementById('imagePreviewModal').addEventListener('click', function(e){
        if(e.target === this || e.target.classList.contains('modal-body')){
            bootstrap.Modal.getInstance(this).hide();
        }
    });
});
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection