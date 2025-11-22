@extends('layouts.app')
@section('title', 'Supplier NewsFeed')
@section('content')
<div class="container mt-5">

    <a href="{{ route('newsfeedsupplier.index') }}" class="btn btn-secondary mb-3">Back to Feed</a>

    {{-- DEBUG INFO - Remove this after testing --}}
    {{-- @if(config('app.debug'))
    <div class="alert alert-info">
        <strong>Debug Info:</strong><br>
        Auth Check: {{ auth()->check() ? 'Yes' : 'No' }}<br>
        Is Admin: {{ auth()->check() && auth()->user()->is_admin ? 'Yes' : 'No' }}<br>
        Post Status: {{ $post->status }}<br>
        Post Featured: {{ $post->is_featured ? 'Yes' : 'No' }}<br>
        Should Show Button: {{ auth()->check() && auth()->user()->is_admin && $post->status === 'approved' ? 'YES' : 'NO' }}
    </div>
    @endif --}}

    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            {{-- Post Header --}}
            <div class="d-flex align-items-center mb-2">
                <strong>{{ $post->user->name }}</strong>
                <small class="text-muted ms-2">{{ $post->created_at->diffForHumans() }}</small>
            </div>

            {{-- Post Title + Featured Badge --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-bold mb-0">{{ $post->title }}</h5>

                @if($post->is_featured)
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-star"></i> Featured
                    </span>
                @endif
            </div>

            {{-- Admin Feature Toggle --}}
            @auth
                @if(auth()->user()->is_admin && $post->status === 'approved')
                    <div class="mb-3">
                        <form action="{{ route('newsfeedsupplier.toggleFeatured', $post) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit"
                                class="btn btn-sm {{ $post->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                                <i class="fas fa-star"></i>
                                {{ $post->is_featured ? 'Remove from Landing Page' : 'Feature on Landing Page' }}
                            </button>
                        </form>
                    </div>
                @else
                    {{-- DEBUG: Show why button is not appearing --}}
                    @if(config('app.debug'))
                        <div class="alert alert-warning small">
                            Button hidden because:
                            @if(!auth()->user()->is_admin)
                                <br>- You are not an admin
                            @endif
                            @if($post->status !== 'approved')
                                <br>- Post status is "{{ $post->status }}" (must be "approved")
                            @endif
                        </div>
                    @endif
                @endif
            @endauth

            {{-- Post Content --}}
            <p class="mt-3">{{ $post->content }}</p>

            {{-- Post Image --}}
            @if($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid rounded mb-2">
            @endif

            {{-- Reactions --}}
            <div class="d-flex align-items-center mt-2 mb-2">
                @php
                    $userReaction = $post->userReaction(auth()->id());
                @endphp
                <form method="POST" action="{{ route('newsfeedsupplier.react', $post) }}" class="d-flex gap-2">
                    @csrf
                    @foreach(['love'=>'❤️','laugh'=>'😂','wow'=>'😮'] as $type => $emoji)
                        <button type="submit" name="type" value="{{ $type }}"
                            class="btn btn-sm {{ $userReaction && $userReaction->type == $type ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ $emoji }} {{ $post->reactions->where('type',$type)->count() }}
                        </button>
                    @endforeach
                </form>
            </div>

            {{-- All Comments --}}
            <div class="mt-3">
                <h6 class="fw-bold">Comments ({{ $post->comments->count() }})</h6>

                @forelse($post->comments as $comment)
                    <div class="d-flex mb-1">
                        <strong>{{ $comment->user->name }}</strong>
                        <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-1">{{ $comment->content }}</p>
                @empty
                    <p class="text-muted small">No comments yet.</p>
                @endforelse

                {{-- Comment Form --}}
                <form method="POST" action="{{ route('newsfeedsupplier.comment', $post) }}">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="content" class="form-control" placeholder="Add a comment..." required>
                        <button class="btn btn-outline-primary" type="submit">Comment</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection