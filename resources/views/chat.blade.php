@extends('layouts.app')

@section('title', 'Chat')

@section('content')
<div class="mt-5">
    {{-- Hero Section --}}
    <div class="card border-0 shadow-lg mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="fas fa-comments text-white" style="font-size: 3rem;"></i>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">💬 Chat Center</h1>
            <p class="lead text-white-50 mb-0">Connect with buyers, sellers, and suppliers in real-time</p>
        </div>
    </div>

    {{-- Chat Interface --}}
    <div class="row g-4">
        {{-- Sidebar with users --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-lg h-100" style="border-radius: 20px;">
                <div class="card-header border-0 py-3" style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 20px 20px 0 0;">
                    <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #2c3e50;">
                        <i class="fas fa-users me-2" style="color: #667eea;"></i>
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
                                             style="width: 45px; height: 45px; background: linear-gradient(45deg, #667eea, #764ba2); color: white; font-weight: bold;">
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
                                $receiver = $users->find($receiver_id);
                            @endphp
                            
                            @if($receiver)
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 40px; height: 40px; background: linear-gradient(45deg, #667eea, #764ba2); color: white; font-weight: bold;">
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
                                                 style="background: linear-gradient(45deg, #667eea, #764ba2); 
                                                        color: white; 
                                                        max-width: 70%; 
                                                        box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
                                                        word-wrap: break-word;
                                                        overflow-wrap: break-word;">
                                                <div style="line-height: 1.4;">{{ $message->content }}</div>
                                                <div class="text-end mt-1">
                                                    <small style="opacity: 0.8; font-size: 0.75rem;">
                                                        {{ $message->created_at->format('H:i') }}
                                                    </small>
                                                </div>
                                                <div class="position-absolute top-50 translate-middle-y"
                                                     style="right: -8px; width: 0; height: 0; 
                                                            border-left: 8px solid #667eea; 
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
                                                            max-width: 70%; 
                                                            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                                                            word-wrap: break-word;
                                                            overflow-wrap: break-word;">
                                                    <div class="fw-semibold mb-1" style="font-size: 0.8rem; color: #667eea;">
                                                        {{ $message->user->name }}
                                                    </div>
                                                    <div style="line-height: 1.4;">{{ $message->content }}</div>
                                                    <div class="mt-1">
                                                        <small class="text-muted" style="font-size: 0.75rem;">
                                                            {{ $message->created_at->format('H:i') }}
                                                        </small>
                                                    </div>
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
                            <form method="POST" action="{{ route('chat.send') }}" class="d-flex gap-2">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $receiver_id }}">
                                <div class="flex-grow-1">
                                    <input type="text" name="message" class="form-control" 
                                           style="border-radius: 25px; border: 2px solid #e9ecef; padding: 12px 20px;"
                                           placeholder="Type your message..." required>
                                </div>
                                <button type="submit" class="btn btn-primary px-4" 
                                        style="border-radius: 25px; background: linear-gradient(45deg, #667eea, #764ba2); border: none;">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card-body d-flex align-items-center justify-content-center" style="height: 500px;">
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="fas fa-comments text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                            </div>
                            <h4 class="text-muted mb-3">Start a Conversation</h4>
                            <p class="text-muted mb-4">Select a user from the sidebar to begin chatting</p>
                            <div class="d-flex justify-content-center gap-3">
                                <div class="d-flex align-items-center px-3 py-2 rounded-pill" 
                                     style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
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

{{-- Custom CSS --}}
<style>
    .user-item { border-bottom: 1px solid #e9ecef !important; }
    .user-item:last-child { border-bottom: none !important; }
    .user-item.active {
        background: rgba(102, 126, 234, 0.1) !important;
        border-left: 4px solid #667eea !important;
    }
    .user-item:hover { background: #f8f9fa !important; }
    .user-item.active:hover { background: rgba(102, 126, 234, 0.15) !important; }
    #messages { scroll-behavior: smooth; }
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .btn-primary:hover {
        background: linear-gradient(45deg, #5a6fd8, #6a42a0) !important;
        transform: translateY(-1px);
    }
    .user-list::-webkit-scrollbar, #messages::-webkit-scrollbar { width: 6px; }
    .user-list::-webkit-scrollbar-thumb, #messages::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    .user-list::-webkit-scrollbar-thumb:hover, #messages::-webkit-scrollbar-thumb:hover { background: #667eea; }
</style>

{{-- Scripts --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const messagesDiv = document.getElementById('messages');
        const messageInput = document.querySelector('input[name="message"]');
        const chatForm = document.querySelector('form[action="{{ route('chat.send') }}"]');

        function scrollToBottom() {
            if (messagesDiv) messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
        scrollToBottom();

        if (messageInput) {
            messageInput.focus();
            messageInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    chatForm.requestSubmit();
                }
            });
        }

        if (chatForm) {
            chatForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                const input = this.querySelector('input[name="message"]');
                const message = input.value.trim();
                if (message === '') return;

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                submitBtn.disabled = true;
                
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message, receiver_id: {{ $receiver_id ?? 'null' }} })
                })
                .then(res => res.json())
                .then(() => { input.value = ''; 
                    input.focus(); 
                })
                .catch(err => console.error(err))
                .finally(() => {
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    submitBtn.disabled = false;
                });
            });
        }

        // Laravel Echo real-time listener
        const userId = document.head.querySelector('meta[name="user-id"]').content;
        if (window.Echo) {
            window.Echo.private(`chat.${userId}`)
                .listen('MessageSent', (e) => {
                    const isMe = parseInt(userId) === e.message.user_id;
                    let bubbleHtml = "";
                    if (isMe) {
                        bubbleHtml = `
                            <div class="d-flex justify-content-end mb-3">
                                <div class="p-3 rounded-4 position-relative" 
                                     style="background: linear-gradient(45deg, #667eea, #764ba2); color: white; max-width: 70%; box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3); word-wrap: break-word;">
                                    <div>${e.message.content}</div>
                                    <div class="text-end mt-1">
                                        <small style="opacity: 0.8; font-size: 0.75rem;">${e.message.created_at ?? ""}</small>
                                    </div>
                                    <div class="position-absolute top-50 translate-middle-y"
                                         style="right: -8px; border-left: 8px solid #667eea; border-top: 8px solid transparent; border-bottom: 8px solid transparent;"></div>
                                </div>
                            </div>`;
                    } else {
                        bubbleHtml = `
                            <div class="d-flex justify-content-start mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2 flex-shrink-0"
                                         style="width: 32px; height: 32px; background: linear-gradient(45deg, #28a745, #20c997); color: white; font-size: 0.8rem; font-weight: bold;">
                                        ${e.message.user.name.charAt(0).toUpperCase()}
                                    </div>
                                    <div class="p-3 rounded-4 position-relative" 
                                         style="background: #ffffff; border: 2px solid #e9ecef; color: #2c3e50; max-width: 70%; box-shadow: 0 2px 10px rgba(0,0,0,0.1); word-wrap: break-word;">
                                        <div class="fw-semibold mb-1" style="font-size: 0.8rem; color: #667eea;">${e.message.user.name}</div>
                                        <div>${e.message.content}</div>
                                        <div class="mt-1">
                                            <small class="text-muted" style="font-size: 0.75rem;">${e.message.created_at ?? ""}</small>
                                        </div>
                                        <div class="position-absolute top-50 translate-middle-y"
                                             style="left: -10px; border-right: 8px solid #ffffff; border-top: 8px solid transparent; border-bottom: 8px solid transparent;"></div>
                                    </div>
                                </div>
                            </div>`;
                    }
                    messagesDiv.insertAdjacentHTML("beforeend", bubbleHtml);
                    scrollToBottom();
                });
        }
    });
</script>
@endsection
