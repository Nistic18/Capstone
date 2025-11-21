@extends('layouts.app')
@section('title', 'Edit Post')

{{-- Add Bootstrap 5 CSS --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

{{-- Add Bootstrap 5 JavaScript --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
@endpush

@section('content')
<div class="container mt-5">
    {{-- Header Section --}}
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg" style="border-radius: 20px;">
                <div class="card-header text-center py-4" 
                     style="background: linear-gradient(135deg, #667eea 0%, #088a50 100%); border-radius: 20px 20px 0 0;">
                    <h2 class="text-white fw-bold mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Post
                    </h2>
                </div>

                <div class="card-body p-5">
                    {{-- Back Button --}}
                    <div class="mb-4">
                        <a href="{{ route('newsfeedsupplier.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to newsfeedsupplier
                        </a>
                    </div>

                    {{-- Edit Form --}}
                    <form method="POST" action="{{ route('newsfeedsupplier.update', $post) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Title Field --}}
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">
                                <i class="fas fa-heading me-2 text-primary"></i>Post Title
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $post->title) }}"
                                   placeholder="Enter your post title..."
                                   style="border-radius: 15px; border: 2px solid #e9ecef;"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Content Field --}}
                        <div class="mb-4">
                            <label for="content" class="form-label fw-bold">
                                <i class="fas fa-paragraph me-2 text-primary"></i>Post Content
                            </label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="8"
                                      placeholder="What's on your mind? Share your thoughts with the community..."
                                      style="border-radius: 15px; border: 2px solid #e9ecef; resize: vertical;"
                                      required>{{ old('content', $post->content) }}</textarea>
                            <div class="form-text">
                                <span id="charCount">{{ strlen($post->content) }}</span>/1000 characters
                            </div>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Current Image --}}
                        @if($post->image)
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-image me-2 text-primary"></i>Current Image
                                </label>
                                <div class="card" style="border-radius: 15px;">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <img src="{{ asset('storage/' . $post->image) }}" 
                                                     class="img-fluid rounded" 
                                                     style="max-height: 150px; object-fit: cover;">
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remove_image" id="removeImage" value="1">
                                                    <label class="form-check-label text-danger" for="removeImage">
                                                        <i class="fas fa-trash me-2"></i>Remove current image
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Image Upload Field --}}
                        <div class="mb-4">
                            <label for="image" class="form-label fw-bold">
                                <i class="fas fa-camera me-2 text-primary"></i>{{ $post->image ? 'Change Image' : 'Add Image' }} (Optional)
                            </label>
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*"
                                   style="border-radius: 15px; border: 2px solid #e9ecef;">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Maximum file size: 2MB. Supported formats: JPG, PNG, GIF, WEBP
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Image Preview --}}
                        <div id="imagePreview" class="mb-4" style="display: none;">
                            <label class="form-label fw-bold">
                                <i class="fas fa-eye me-2 text-primary"></i>New Image Preview
                            </label>
                            <div class="card" style="border-radius: 15px;">
                                <div class="card-body p-3">
                                    <img id="previewImg" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="d-flex justify-content-between flex-wrap gap-3">
                            <a href="{{ route('newsfeedsupplier.index') }}" class="btn btn-secondary btn-lg px-4"
                               style="border-radius: 15px;">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            
                            <button type="submit" class="btn btn-primary btn-lg px-4"
                                    style="border-radius: 15px; background: linear-gradient(45deg, #667eea, #088a50); border: none;">
                                <i class="fas fa-save me-2"></i>Update Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom CSS --}}
<style>
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-primary {
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(45deg, #5a6fd8, #6a42a0) !important;
        transform: translateY(-1px);
    }
    
    .btn-secondary:hover {
        transform: translateY(-1px);
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .form-check-input:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
        }
        
        .btn-lg {
            width: 100%;
        }
    }
</style>

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for content field
    const contentField = document.getElementById('content');
    const charCount = document.getElementById('charCount');
    
    function updateCharCount() {
        const count = contentField.value.length;
        charCount.textContent = count;
        charCount.className = count > 900 ? 'text-warning' : count > 950 ? 'text-danger' : '';
    }
    
    contentField.addEventListener('input', updateCharCount);
    
    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageCheckbox = document.getElementById('removeImage');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
            
            // Uncheck remove image if new image is selected
            if (removeImageCheckbox) {
                removeImageCheckbox.checked = false;
            }
        } else {
            imagePreview.style.display = 'none';
        }
    });
    
    // Remove image checkbox handling
    if (removeImageCheckbox) {
        removeImageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Clear file input
                imageInput.value = '';
                imagePreview.style.display = 'none';
            }
        });
    }
    
    // Form submission handling
    const form = document.querySelector('form');
    form.addEventListener('submit', function() {
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        submitBtn.disabled = true;
    });
    
    // Auto-resize textarea
    contentField.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 300) + 'px';
    });
});
</script>
@endsection