@extends('layouts.app')

@section('title', 'Pending Posts Review')

@section('content')
<div class="container mt-5">
    <h2 class="fw-bold mb-4 text-primary">
        <i class="bi bi-clipboard-check"></i> Pending Posts for Approval
    </h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse($pendingPosts as $post)
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <span class="badge {{ $post->post_type === 'supplier' ? 'bg-primary' : 'bg-success' }}">
                    {{ $post->post_type === 'supplier' ? 'Supplier Community' : 'Community Newsfeed' }}
                </span>
                <small class="text-muted ms-2">
                    <i class="bi bi-clock"></i> {{ $post->created_at->diffForHumans() }}
                </small>
            </div>
            <div>
                <i class="bi bi-person-circle"></i> 
                <strong>{{ $post->user->name }}</strong>
            </div>
        </div>

        <div class="card-body">
            <h5 class="card-title fw-bold mb-3">{{ $post->title }}</h5>
            
            <div class="card-text mb-3">
                <p class="text-muted">{{ Str::limit($post->content, 300) }}</p>
            </div>

            @if($post->image)
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $post->image) }}" 
                         class="img-fluid rounded shadow-sm" 
                         style="max-height: 300px; object-fit: cover;"
                         alt="Post image">
                </div>
            @endif

            <hr>

            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <i class="bi bi-calendar3"></i> Posted on {{ $post->created_at->format('M d, Y \a\t h:i A') }}
                </div>

                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('admin.posts.approve') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="id" value="{{ $post->id }}">
                        <input type="hidden" name="type" value="{{ $post->post_type }}">
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this post?')">
                            <i class="bi bi-check-circle"></i> Approve
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.posts.reject') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="id" value="{{ $post->id }}">
                        <input type="hidden" name="type" value="{{ $post->post_type }}">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this post?')">
                            <i class="bi bi-x-circle"></i> Reject
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="alert alert-info text-center py-5">
        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
        <h4 class="mt-3">No Pending Posts</h4>
        <p class="mb-0">There are currently no posts waiting for review.</p>
    </div>
    @endforelse

    @if($pendingPosts->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $pendingPosts->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<style>
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 500;
    }
</style>
@endsection