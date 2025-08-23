@extends('layouts.app')

@section('content')
<div class="container mt-5">

    {{-- Page Header --}}
    <div class="card card-body mb-4">
        <h2 class="fw-bold mb-0 text-primary">Community Newsfeed</h2>
        <a href="{{ route('newsfeed.create') }}" class="btn btn-sm btn-success mt-2">Create New Post</a>
    </div>

    {{-- Display Posts --}}
    @forelse($posts as $post)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">

                {{-- Post Header --}}
                <div class="d-flex align-items-center mb-2">
                    <strong>{{ $post->user->name }}</strong>
                    <small class="text-muted ms-2">{{ $post->created_at->diffForHumans() }}</small>
                </div>

                {{-- Post Title --}}
                <h5 class="fw-bold">{{ $post->title }}</h5>

                {{-- Post Content (Short Preview) --}}
                <p>{{ Str::limit($post->content, 150) }}</p>

                {{-- Post Image --}}
                @if($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid rounded mb-2">
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

                {{-- Comments Section (Show 3 latest) --}}
                <div class="mt-3">
                    <h6 class="fw-bold">Comments ({{ $post->comments->count() }})</h6>

                    @forelse($post->comments->sortByDesc('created_at')->take(3)->sortBy('created_at') as $comment)
                        <div class="d-flex mb-1">
                            <strong>{{ $comment->user->name }}</strong>
                            <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1">{{ $comment->content }}</p>
                    @empty
                        <p class="text-muted small">No comments yet.</p>
                    @endforelse

                    {{-- Link to view full post --}}
                    <a href="{{ route('newsfeed.show', $post) }}" class="text-primary small">View Full Comments</a>
                </div>

            </div>
        </div>
    @empty
        <div class="alert alert-info">No posts yet. Be the first to share!</div>
    @endforelse

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $posts->links() }}
    </div>

</div>
@endsection
