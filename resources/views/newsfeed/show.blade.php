@extends('layouts.app')
@section('title', 'NewsFeed')
<link rel="icon" type="image/png" href="{{ asset('img/avatar/dried-fish-logo.png') }}">
@section('content')
<div class="container mt-5">

    <a href="{{ route('newsfeed.index') }}" class="btn btn-secondary mb-3">Back to Feed</a>

    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            {{-- Post Header --}}
            <div class="d-flex align-items-center mb-2">
                <strong>{{ $post->user->name }}</strong>
                <small class="text-muted ms-2">{{ $post->created_at->diffForHumans() }}</small>
            </div>

            {{-- Post Title --}}
            <h5 class="fw-bold">{{ $post->title }}</h5>

            {{-- Post Content --}}
            <p>{{ $post->content }}</p>

            {{-- Post Image --}}
            @if($post->image)
                <img src="{{ asset($post->image) }}" class="img-fluid rounded mb-2">
            @endif

            {{-- Reactions --}}
            <div class="d-flex align-items-center mt-2 mb-2">
                @php
                    $userReaction = $post->userReaction(auth()->id());
                @endphp
                <form method="POST" action="{{ route('newsfeed.react', $post) }}" class="d-flex gap-2">
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
                <form method="POST" action="{{ route('newsfeed.comment', $post) }}">
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
