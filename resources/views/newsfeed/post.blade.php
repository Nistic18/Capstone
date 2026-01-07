@extends('layouts.app')
@section('title', 'NewsFeed')
<link rel="icon" type="image/png" href="{{ asset('img/avatar/dried-fish-logo.png') }}">
@section('content')
<div class="container mt-5">

    {{-- Page Header --}}
    <div class="card card-body mb-4">
        <h2 class="fw-bold mb-0 text-primary">Create a New Post</h2>
    </div>

    {{-- Post Form --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('newsfeed.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="text" name="title" class="form-control" placeholder="Title of your post" required>
                </div>
                <div class="mb-3">
                    <textarea name="content" class="form-control" placeholder="Description..." required
                        style="height: 400px; resize: vertical; overflow-y: auto;"></textarea>
                </div>
                <div class="mb-3">
                    <input type="file" name="image" class="form-control">
                </div>

                {{-- Buttons --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('newsfeed.index') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Post</button>
                </div>
            </form>
        </div>
    </div>
{{-- Success Message --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Validation Errors --}}
@if($errors->any())
    <div class="alert alert-danger">
        <strong>Oops! Something went wrong:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

</div>
@endsection
