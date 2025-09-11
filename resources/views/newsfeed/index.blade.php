@extends('layouts.app')
@section('title', 'Community Newsfeed')

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
<div class="mt-5">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px; border: none;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px; border: none;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px; border: none;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- Header Section --}}
    <div class="card border-0 shadow-lg mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="fas fa-users text-white" style="font-size: 3rem;"></i>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">🐟 Community Newsfeed</h1>
            <p class="lead text-white-50 mb-4">Stay connected with the fish market community</p>
            
            {{-- Quick Stats --}}
            <div class="d-flex justify-content-center gap-4 flex-wrap">
                <div class="d-flex align-items-center px-3 py-2 rounded-pill" 
                     style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                    <i class="fas fa-newspaper text-white me-2"></i>
                    <span class="text-white">{{ $posts->total() }} Posts</span>
                </div>
                <div class="d-flex align-items-center px-3 py-2 rounded-pill" 
                     style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                    <i class="fas fa-users text-white me-2"></i>
                    <span class="text-white">Community Hub</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color: #2c3e50;">
                <i class="fas fa-stream me-2" style="color: #667eea;"></i>
                Latest Posts
            </h2>
            <p class="text-muted mb-0">What's happening in the community</p>
        </div>
        
        <a class="btn btn-success px-4 py-2" 
           href="{{ route('newsfeed.create') }}" 
           style="border-radius: 15px; background: linear-gradient(45deg, #28a745, #20c997); border: none;">
            <i class="fas fa-plus me-2"></i>Create New Post
        </a>
    </div>

    {{-- Posts Feed --}}
    @forelse($posts as $post)
        <div class="card border-0 shadow-sm mb-4 post-card" 
             style="border-radius: 20px; transition: all 0.3s ease;"
             onmouseover="this.style.transform='translateY(-2px)'; this.classList.add('shadow-lg')"
             onmouseout="this.style.transform='translateY(0)'; this.classList.remove('shadow-lg')">
            
            {{-- Post Header --}}
            <div class="card-header border-0 py-4" style="background: transparent; border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        {{-- User Avatar --}}
                        <div class="me-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; background: linear-gradient(45deg, #667eea, #764ba2) !important;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        </div>
                        
                        {{-- User Info --}}
                        <div>
                            <h6 class="fw-bold mb-0" style="color: #2c3e50;">{{ $post->user->name }}</h6>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $post->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    
                    {{-- Post Actions Dropdown --}}
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm" type="button" 
                                style="border-radius: 10px; border: none; background: rgba(108, 117, 125, 0.1);"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu shadow-sm" style="border-radius: 10px;">
                            <li>
                                <a class="dropdown-item" href="{{ route('newsfeed.show', $post) }}">
                                    <i class="fas fa-eye me-2"></i>View Full Post
                                </a>
                            </li>
                            @if(auth()->id() == $post->user_id)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-warning" href="{{ route('newsfeed.edit', $post) }}">
                                        <i class="fas fa-edit me-2"></i>Edit Post
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="confirmDeletePost({{ $post->id }})">
                                        <i class="fas fa-trash me-2"></i>Delete Post
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Post Content --}}
            <div class="card-body pt-0">
                {{-- Post Title --}}
                <h5 class="fw-bold mb-3" style="color: #2c3e50; line-height: 1.4;">
                    {{ $post->title }}
                </h5>

                {{-- Post Content --}}
                <p class="text-dark mb-3" style="line-height: 1.6;">
                    {{ Str::limit($post->content, 200) }}
                    @if(strlen($post->content) > 200)
                        <a href="{{ route('newsfeed.show', $post) }}" class="text-primary text-decoration-none fw-semibold">
                            Read more
                        </a>
                    @endif
                </p>

                {{-- Post Image --}}
                @if($post->image)
                    <div class="post-image-container mb-4" style="border-radius: 15px; overflow: hidden;">
                        <img src="{{ asset('storage/' . $post->image) }}" 
                             class="img-fluid w-100" 
                             style="max-height: 400px; object-fit: cover; cursor: pointer;"
                             alt="Post image"
                             onclick="openImageModal('{{ asset('storage/' . $post->image) }}')">
                    </div>
                @endif

                {{-- Engagement Stats --}}
                <div class="d-flex justify-content-between align-items-center py-2 mb-3" 
                     style="border-top: 1px solid #f8f9fa; border-bottom: 1px solid #f8f9fa;">
                    <div class="d-flex align-items-center gap-3">
                        <span class="small text-muted">
                            <i class="fas fa-heart text-danger me-1"></i>
                            {{ $post->reactions->count() }} reactions
                        </span>
                        <span class="small text-muted">
                            <i class="fas fa-comment text-info me-1"></i>
                            {{ $post->comments->count() }} comments
                        </span>
                    </div>
                </div>

                {{-- Reactions Bar --}}
                <div class="d-flex align-items-center gap-2 mb-4">
                    @php
                        $userReaction = $post->userReaction(auth()->id());
                    @endphp
                    @foreach(['love'=>'❤️','laugh'=>'😂','wow'=>'😮'] as $type => $emoji)
                        <form method="POST" action="{{ route('newsfeed.react', $post) }}" class="reaction-form flex-fill">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <button type="submit"
                                    class="btn w-100 reaction-btn {{ $userReaction && $userReaction->type == $type ? 'active' : '' }}"
                                    style="border-radius: 15px; 
                                           border: 2px solid {{ $userReaction && $userReaction->type == $type ? '#667eea' : '#e9ecef' }};
                                           background: {{ $userReaction && $userReaction->type == $type ? 'rgba(102, 126, 234, 0.1)' : 'white' }};
                                           color: {{ $userReaction && $userReaction->type == $type ? '#667eea' : '#6c757d' }};">
                                <span class="me-1" style="font-size: 1.1em;">{{ $emoji }}</span>
                                <span class="fw-semibold reaction-count">{{ $post->reactions->where('type',$type)->count() }}</span>
                            </button>
                        </form>
                    @endforeach
                </div>

                {{-- Comments Section --}}
                <div class="comments-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0" style="color: #2c3e50;">
                            <i class="fas fa-comments me-2" style="color: #667eea;"></i>
                            Comments ({{ $post->comments->count() }})
                        </h6>
                        @if($post->comments->count() > 3)
                            <a href="{{ route('newsfeed.show', $post) }}" 
                               class="btn btn-outline-primary btn-sm" 
                               style="border-radius: 10px;">
                                <i class="fas fa-expand-alt me-1"></i>View All
                            </a>
                        @endif
                    </div>

                    {{-- Recent Comments --}}
                    @forelse($post->comments->sortByDesc('created_at')->take(3)->sortBy('created_at') as $comment)
                        <div class="comment-item p-3 mb-2 rounded" style="background: #f8f9fa;">
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-2">
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 35px; height: 35px; background: linear-gradient(45deg, #6c757d, #495057) !important;">
                                        <i class="fas fa-user text-white" style="font-size: 0.8rem;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="small" style="color: #2c3e50;">{{ $comment->user->name }}</strong>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-0 small" style="margin-left: 47px; color: #495057;">{{ $comment->content }}</p>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="fas fa-comment text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="text-muted small mb-0">No comments yet. Be the first to comment!</p>
                        </div>
                    @endforelse

                    {{-- Quick Comment Form --}}
                    <div class="mt-3 pt-3" style="border-top: 1px solid #e9ecef;">
                        <form method="POST" action="{{ route('newsfeed.comment', $post) }}" class="d-flex gap-2">
                            @csrf
                            <div class="me-2">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 35px; height: 35px; background: linear-gradient(45deg, #667eea, #764ba2) !important;">
                                    <i class="fas fa-user text-white" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="input-group">
                                    <input type="text" name="content" class="form-control" 
                                           placeholder="Write a comment..." 
                                           style="border-radius: 20px 0 0 20px; border: 2px solid #e9ecef;"
                                           required>
                                    <button class="btn btn-primary" type="submit"
                                            style="border-radius: 0 20px 20px 0; background: linear-gradient(45deg, #667eea, #764ba2); border: none;">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-newspaper text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
            <h3 class="text-muted mb-3">No Posts Yet</h3>
            <p class="text-muted mb-4">Be the first to share something with the community!</p>
            <a href="{{ route('newsfeed.create') }}" class="btn btn-primary btn-lg" 
               style="border-radius: 25px; background: linear-gradient(45deg, #667eea, #764ba2); border: none;">
                <i class="fas fa-plus me-2"></i>Create Your First Post
            </a>
        </div>
    @endforelse

    {{-- Enhanced Pagination --}}
    @if(!$posts->isEmpty())
        <div class="d-flex justify-content-center mt-5">
            <nav aria-label="Newsfeed pagination">
                {{ $posts->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    @endif
</div>

{{-- Image Modal --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalImage" class="img-fluid w-100" style="border-radius: 0 0 20px 20px;" alt="Post image preview">
            </div>
        </div>
    </div>
</div>

{{-- Toast Container for notifications --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <!-- Toasts will be dynamically inserted here -->
</div>

{{-- Custom CSS --}}
<style>
    .post-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    .reaction-btn {
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .reaction-btn:hover {
        transform: translateY(-1px);
        border-color: #667eea !important;
        background: rgba(102, 126, 234, 0.1) !important;
        color: #667eea !important;
    }
    
    .reaction-btn.active {
        transform: scale(1.05);
        font-weight: 600;
    }
    
    .comment-item {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }
    
    .comment-item:hover {
        background: #e9ecef !important;
        border-color: rgba(102, 126, 234, 0.2) !important;
    }
    
    .post-image-container img:hover {
        transform: scale(1.02);
        transition: transform 0.3s ease;
    }
    
    .btn-success {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-success:hover {
        background: linear-gradient(45deg, #218838, #1ea085);
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
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .dropdown-menu {
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .dropdown-item:hover {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }
    
    /* Bootstrap 5 Pagination Styles */
    .pagination .page-link {
        border-radius: 10px;
        margin: 0 2px;
        border: 2px solid #e9ecef;
        color: #667eea;
        padding: 0.5rem 0.75rem;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-color: #667eea;
        color: white;
    }
    
    .pagination .page-link:hover {
        background-color: rgba(102, 126, 234, 0.1);
        border-color: #667eea;
        color: #667eea;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .display-4 {
            font-size: 2rem;
        }
        
        .d-flex.gap-3 {
            flex-direction: column;
        }
        
        .reaction-btn {
            font-size: 0.875rem;
            padding: 0.5rem;
        }
        
        .comment-item {
            margin-bottom: 0.5rem;
        }
        
        .modal-dialog {
            margin: 1rem;
        }
    }
    
    /* Loading animation */
    .loading {
        opacity: 0.7;
        pointer-events: none;
    }
    
    /* Toast notification styles */
    .toast {
        border-radius: 10px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>

{{-- JavaScript --}}
<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Add loading states to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if(submitBtn) {
                const originalContent = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
                submitBtn.disabled = true;
                
                // Re-enable after 3 seconds in case of network issues
                setTimeout(() => {
                    submitBtn.innerHTML = originalContent;
                    submitBtn.disabled = false;
                }, 3000);
            }
        });
    });
});

// Image Modal
function openImageModal(imageSrc) {
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageSrc;
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

// Delete Post Confirmation with Bootstrap Modal
function confirmDeletePost(postId) {
    // Create a Bootstrap modal for confirmation
    const modalHtml = `
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Are you sure you want to delete this post? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="deletePost(${postId})" data-bs-dismiss="modal">
                            <i class="fas fa-trash me-1"></i>Delete Post
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if present
    const existingModal = document.getElementById('deleteConfirmModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    modal.show();
    
    // Clean up after modal is hidden
    modal._element.addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Delete Post Function
function deletePost(postId) {
    // Create and submit delete form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/newsfeed/${postId}`;
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Add DELETE method
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    
    form.appendChild(csrfInput);
    form.appendChild(methodInput);
    document.body.appendChild(form);
    
    form.submit();
}

// Toast notification function
function showToast(message, type = 'info') {
    const toastContainer = document.querySelector('.toast-container');
    const toastId = 'toast-' + Date.now();
    
    const toastHtml = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas ${type === 'success' ? 'fa-check-circle text-success' : 'fa-info-circle text-info'} me-2"></i>
                <strong class="me-auto">Notification</strong>
                <small class="text-muted">now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toast = new bootstrap.Toast(document.getElementById(toastId));
    toast.show();
    
    // Remove toast element after it's hidden
    document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// Reaction button animation
document.addEventListener('click', function(e) {
    if(e.target.closest('.reaction-btn')) {
        const btn = e.target.closest('.reaction-btn');
        btn.style.transform = 'scale(0.95)';
        setTimeout(() => {
            btn.style.transform = '';
        }, 150);
    }
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Smooth scroll for anchor links
document.addEventListener('click', function(e) {
    const target = e.target.closest('a[href^="#"]');
    if (target) {
        e.preventDefault();
        const targetId = target.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
});
</script>
@endsection