@extends('layouts.app')

@section('title', 'Chat')
<link rel="icon" type="image/png" href="{{ asset('img/avatar/dried-fish-logo.png') }}">
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
    {{-- Chat Interface --}}
    <div class="row g-4">
        {{-- Sidebar with users --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-lg h-100" style="border-radius: 20px;">
                <div class="card-header border-0 py-3" style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 20px 20px 0 0;">
                    <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #2c3e50;">
                        <i class="fas fa-users me-2" style="color: #0bb364;"></i>
                        Active Users
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="user-list" style="max-height: 500px; overflow-y: auto;">
                        @if($users->count() > 0)
                            @foreach($users as $user)
                                <a href="{{ route('chat.index', ['user' => $user->id]) }}"
                                   class="user-item d-flex align-items-center p-3 text-decoration-none border-bottom position-relative {{ $receiver_id == $user->id ? 'active' : '' }}"
                                   style="transition: all 0.3s ease; color: inherit;"
                                   onmouseover="this.style.backgroundColor='#f8f9fa'"
                                   onmouseout="this.style.backgroundColor='{{ $receiver_id == $user->id ? 'rgba(102, 126, 234, 0.1)' : 'transparent' }}'">
                                    
                                    {{-- User Avatar --}}
                                    <div class="position-relative me-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 45px; height: 45px; background: linear-gradient(45deg, #0bb364, #0bb364); color: white; font-weight: bold;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        {{-- Online status indicator --}}
                                        <div class="position-absolute bottom-0 end-0 rounded-circle border border-2 border-white"
                                             style="width: 12px; height: 12px; background: #28a745;"></div>
                                    </div>
                                    
                                    {{-- User Info --}}
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ $user->name }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-circle me-1" style="color: #28a745; font-size: 0.5rem;"></i>
                                            {{ ucfirst($user->role ?? 'User') }}
                                        </small>
                                    </div>
                                    
                                    {{-- Active indicator --}}
                                    @if($receiver_id == $user->id)
                                        <i class="fas fa-comment-dots text-primary"></i>
                                    @endif
                                </a>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted mt-3">No users available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Chat Area --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-lg h-100" style="border-radius: 20px;">
                @if($receiver_id)
                    {{-- Chat Header --}}
                    <div class="card-header border-0 py-3" style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 20px 20px 0 0;">
                        <div class="d-flex align-items-center">
                            @php
                                $receiver = \App\Models\User::find($receiver_id);
                            @endphp

                            @if($receiver)
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 40px; height: 40px; background: linear-gradient(45deg, #0bb364, #0bb364); color: white; font-weight: bold;">
                                    {{ strtoupper(substr($receiver->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold" style="color: #2c3e50;">{{ $receiver->name }}</h5>
                                    <small class="text-muted">
                                        <i class="fas fa-circle me-1" style="color: #28a745; font-size: 0.5rem;"></i>
                                        Online • {{ ucfirst($receiver->role ?? 'User') }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Chat Messages --}}
                    <div class="card-body p-0 d-flex flex-column" style="height: 500px;">
                        <div id="messages" class="flex-grow-1 p-4" 
                             style="overflow-y: auto; background: linear-gradient(to bottom, #f8f9fa, #ffffff);">
                            @if($messages->count() > 0)
                                @foreach($messages as $message)
                                    @if($message->user_id == auth()->id())
                                        {{-- Sender (me) --}}
                                        <div class="d-flex justify-content-end mb-3">
                                            <div class="p-3 rounded-4 position-relative" 
                                                 style="background: linear-gradient(45deg, #0bb364, #0bb364); 
                                                        color: white; 
                                                        max-width: 70%; 
                                                        box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
                                                        word-wrap: break-word;
                                                        overflow-wrap: break-word;
                                                        hyphens: none;">
                                                <div style="line-height: 1.4;">
                                                    @if($message->content)
                                                        <div>{{ $message->content }}</div>
                                                    @endif

                                                    @if($message->image)
                                                        <div class="mt-2">
                                                            <img src="{{ asset($message->image) }}" 
                                                                class="chat-image" 
                                                                style="max-width: 200px; border-radius: 10px; cursor: pointer; transition: transform 0.2s;" 
                                                                alt="Image"
                                                                onclick="openImageModal('{{ asset($message->image) }}')"
                                                                onmouseover="this.style.transform='scale(1.02)'"
                                                                onmouseout="this.style.transform='scale(1)'">
                                                        </div>
                                                    @endif
                                                </div>
                                                {{-- Message tail --}}
                                                <div class="position-absolute top-50 translate-middle-y"
                                                     style="right: -8px; width: 0; height: 0; 
                                                            border-left: 8px solid #0bb364; 
                                                            border-top: 8px solid transparent; 
                                                            border-bottom: 8px solid transparent;"></div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Receiver --}}
                                        <div class="d-flex justify-content-start mb-3">
                                            <div class="d-flex align-items-start">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center me-2 flex-shrink-0"
                                                     style="width: 32px; height: 32px; background: linear-gradient(45deg, #28a745, #20c997); color: white; font-size: 0.8rem; font-weight: bold;">
                                                    {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                                </div>
                                                <div class="p-3 rounded-4 position-relative" 
                                                     style="background: #ffffff; 
                                                            border: 2px solid #e9ecef; 
                                                            color: #2c3e50; 
                                                            max-width: 90%; 
                                                            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                                                            word-wrap: break-word;
                                                            overflow-wrap: break-word;
                                                            hyphens: none;">
                                                    <div class="fw-semibold mb-1" style="font-size: 0.8rem; color: #0bb364;">
                                                        {{ $message->user->name }}
                                                    </div>
                                                    <div style="line-height: 1.4;">
                                                        @if($message->content)
                                                            <div>{{ $message->content }}</div>
                                                        @endif

                                                        @if($message->image)
                                                            <div class="mt-1">
                                                                <img src="{{ asset($message->image) }}" 
                                                                    class="chat-image" 
                                                                    style="max-width: 200px; border-radius: 10px; cursor: pointer; transition: transform 0.2s;" 
                                                                    alt="Image"
                                                                    onclick="openImageModal('{{ asset($message->image) }}')"
                                                                    onmouseover="this.style.transform='scale(1.02)'"
                                                                    onmouseout="this.style.transform='scale(1)'">
                                                            </div>
                                                        @endif
                                                    </div>
                                                    {{-- Message tail --}}
                                                    <div class="position-absolute top-50 translate-middle-y"
                                                         style="left: -10px; width: 0; height: 0; 
                                                                border-right: 8px solid #ffffff; 
                                                                border-top: 8px solid transparent; 
                                                                border-bottom: 8px solid transparent;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="text-center h-100 d-flex align-items-center justify-content-center">
                                    <div>
                                        <i class="fas fa-comment-slash text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <p class="text-muted">No messages yet. Start the conversation!</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Message Input --}}
                        <div class="border-top p-3" style="background: #f8f9fa;">
                            <form method="POST" action="{{ route('chat.send') }}" class="d-flex gap-2" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $receiver_id }}">
                                
                                <div class="flex-grow-1 d-flex gap-2">
                                    <input type="text" name="message" class="form-control" 
                                           placeholder="Type your message...">
                                    <input type="file" name="image" accept="image/*" class="form-control">
                                </div>
                                
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- No Chat Selected State --}}
                    <div class="card-body d-flex align-items-center justify-content-center" style="height: 500px;">
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="fas fa-comments text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                            </div>
                            <h4 class="text-muted mb-3">Start a Conversation</h4>
                            <p class="text-muted mb-4">Select a user from the sidebar to begin chatting</p>
                            <div class="d-flex justify-content-center gap-3">
                                <div class="d-flex align-items-center px-3 py-2 rounded-pill" 
                                     style="background: rgba(102, 126, 234, 0.1); color: #0bb364;">
                                    <i class="fas fa-users me-2"></i>
                                    <span class="fw-semibold">{{ $users->count() }} Users Online</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Image Modal (Messenger-style) --}}
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
    <span class="close-modal">&times;</span>
    <img class="modal-content-image" id="modalImage">
    <div id="imageCaption"></div>
</div>

{{-- Custom CSS --}}
<style>
    /* User list styling */
    .user-item {
        border-bottom: 1px solid #e9ecef !important;
    }
    
    .user-item:last-child {
        border-bottom: none !important;
    }
    
    .user-item.active {
        background: rgba(102, 126, 234, 0.1) !important;
        border-left: 4px solid #0bb364 !important;
    }
    
    .user-item:hover {
        background: #f8f9fa !important;
    }
    
    .user-item.active:hover {
        background: rgba(102, 126, 234, 0.15) !important;
    }

    /* Messages styling */
    #messages {
        scroll-behavior: smooth;
    }

/* Image Modal Styling (Fixed and Centered) */
.image-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.95);
    animation: fadeIn 0.3s;
    pointer-events: none;
}

.image-modal.show {
    display: flex !important;
    pointer-events: auto;
    align-items: center;
    justify-content: center;
}

.modal-content-image {
    margin: auto;
    display: block;
    width: 100%;
    height: auto;
    max-width: 95vw;
    max-height: 95vh;
    border-radius: 10px;
    object-fit: contain;
    animation: zoomIn 0.3s ease-out;
}


.close-modal {
    position: fixed;
    top: 20px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
    cursor: pointer;
    z-index: 10001;
    background: rgba(0, 0, 0, 0.5);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.close-modal:hover,
.close-modal:focus {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
    text-decoration: none;
    cursor: pointer;
    transform: rotate(90deg);
}

#imageCaption {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    max-width: 700px;
    text-align: center;
    color: #ccc;
    padding: 10px 20px;
    background: rgba(0, 0, 0, 0.7);
    border-radius: 8px;
    z-index: 10000;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes zoomIn {
    from {
        transform: scale(0.8);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-content-image {
        max-width: 95vw;
        max-height: 85vh;
    }

    .close-modal {
        top: 10px;
        right: 10px;
        font-size: 30px;
        width: 40px;
        height: 40px;
    }

    #imageCaption {
        width: 90%;
        font-size: 14px;
    }
}

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Chat image hover effect */
    .chat-image {
        position: relative;
    }

    .chat-image:hover::after {
        content: '\f002'; /* Font Awesome search icon */
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 2rem;
        opacity: 0.8;
        pointer-events: none;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Form styling */
    .form-control:focus {
        border-color: #0bb364;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #5a6fd8, #6a42a0) !important;
        transform: translateY(-1px);
    }

    /* Scrollbar styling */
    .user-list::-webkit-scrollbar,
    #messages::-webkit-scrollbar {
        width: 6px;
    }

    .user-list::-webkit-scrollbar-track,
    #messages::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .user-list::-webkit-scrollbar-thumb,
    #messages::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }

    .user-list::-webkit-scrollbar-thumb:hover,
    #messages::-webkit-scrollbar-thumb:hover {
        background: #0bb364;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .display-4 {
            font-size: 2rem;
        }
        
        .message-bubble {
            max-width: 90% !important;
        }
        
        .col-xl-4 {
            margin-bottom: 1rem;
        }
        
        .card-body {
            height: 400px !important;
        }

        .modal-content-image {
            max-width: 95%;
        }

        .close-modal {
            top: 10px;
            right: 20px;
            font-size: 35px;
        }
    }

    /* Animation for new messages */
    .message-bubble:last-child {
        animation: slideIn 0.4s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
</style>

{{-- JavaScript for Image Modal and Chat Functions --}}
<script>
    // Image Modal Functions
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    
    modal.classList.add('show');
    modal.style.display = 'flex';
    modalImg.src = imageSrc;
    
    // Prevent body scroll when modal is open
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
    
    // Restore body scroll
    document.body.style.overflow = 'auto';
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});

// Prevent closing when clicking on the image itself
document.addEventListener('DOMContentLoaded', function() {
    const modalImage = document.getElementById('modalImage');
    if (modalImage) {
        modalImage.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
});

    // Prevent closing when clicking on the image itself
    document.getElementById('modalImage')?.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const messagesDiv = document.getElementById('messages');
        
        // Auto-scroll to bottom
        function scrollToBottom() {
            if(messagesDiv) {
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }
        }
        
        // Initial scroll
        scrollToBottom();
        
        // Focus on message input when page loads
        const messageInput = document.querySelector('input[name="message"]');
        if(messageInput) {
            messageInput.focus();
        }
        
        // Auto-submit on Enter key (without Shift)
        if(messageInput) {
            messageInput.addEventListener('keydown', function(e) {
                if(e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.closest('form').submit();
                }
            });
        }
        
        // Enhanced form submission with loading state
        const chatForm = document.querySelector('form[action="{{ route('chat.send') }}"]');
        if(chatForm) {
            chatForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const input = this.querySelector('input[name="message"]');
                const imageInput = this.querySelector('input[name="image"]');
                
                // Check if both message and image are empty
                if(input.value.trim() === '' && !imageInput.files.length) {
                    e.preventDefault();
                    return;
                }
                
                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                submitBtn.disabled = true;
                
                // Reset after a short delay (form will reload page anyway)
                setTimeout(() => {
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    submitBtn.disabled = false;
                }, 1000);
            });
        }
        
        // Smooth hover effects for user items
        document.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
                this.style.transition = 'all 0.3s ease';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
    });

    // Optional: Auto-refresh messages every 30 seconds
    setInterval(function() {
        // Only refresh if we're in an active chat
        if({{ $receiver_id ?? 'null' }}) {
            const messagesDiv = document.getElementById('messages');
            if(messagesDiv) {
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }
        }
    }, 30000);
</script>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection