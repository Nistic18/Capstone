@extends('layouts.app')
@section('title', 'Pending Posts Review')

@section('content')
<div class="container mt-5">
    <h2 class="fw-bold mb-4 text-primary">Pending Posts for Approval</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @forelse($pendingPosts as $post)
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold">{{ $post->title }}</h5>
            <p>{{ $post->content }}</p>
            <p><strong>Posted by:</strong> {{ $post->user->name }}</p>
            <p>
                <strong>Source:</strong> 
                {{ $post instanceof \App\Models\PostSupplier ? 'Supplier Community' : 'Community' }}
            </p>

            @if($post->image)
                <img src="{{ asset('storage/'.$post->image) }}" class="img-fluid mb-3" style="max-height:200px;">
            @endif

            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.posts.approve') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $post->id }}">
                    <input type="hidden" name="type" value="{{ $post instanceof \App\Models\PostSupplier ? 'supplier' : 'community' }}">
                    <button class="btn btn-success">Approve</button>
                </form>

                <form method="POST" action="{{ route('admin.posts.reject') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $post->id }}">
                    <input type="hidden" name="type" value="{{ $post instanceof \App\Models\PostSupplier ? 'supplier' : 'community' }}">
                    <button class="btn btn-danger">Reject</button>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-info">No pending posts for review.</div>
@endforelse

    <div class="mt-4">
        {{ $pendingPosts->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
