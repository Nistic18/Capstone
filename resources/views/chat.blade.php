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
                    <div class="user-list" id="user-list" style="max-height: 500px; overflow-y: auto;">
                        <!-- JS will populate users here -->
                    </div>
                </div>
            </div>
        </div>

        {{-- Chat Area --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-lg h-100" style="border-radius: 20px;">
                <div id="chat-area">
                    <!-- JS will render chat header & messages here -->
                </div>
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

{{-- JS Data Injection --}}
<script>
    window.chatData = {
        users: @json($usersJson ?? []),
        receiverId: @json($receiverId ?? null),
        currentUserId: {{ auth()->id() }},
    };
</script>

{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const { users, receiverId, currentUserId } = window.chatData;
    const userList = document.getElementById('user-list');
    const chatArea = document.getElementById('chat-area');
    const messagesContainerId = 'messages'; // ID for messages div

    /** FETCH CHAT MESSAGES */
    function fetchChatList() {
        $.ajax({
            url: '/chat/list',
            method: 'GET',
            dataType: 'json',
        })
        .then(function(response) {
            console.log('Chat list:', response.data);
            renderChatMessages(response.data);
        })
        .catch(function(error) {
            console.error('Error fetching chat list:', error);
        });
    }

    /** RENDER CHAT MESSAGES */
    function renderChatMessages(messages) {
        const messagesContainer = document.getElementById(messagesContainerId);
        if (!messagesContainer) return;

        messagesContainer.innerHTML = ""; // Clear existing messages

        // Filter messages for current conversation
        const conversation = messages.filter(msg =>
            (msg.user_id == currentUserId && msg.receiver_id == receiverId) ||
            (msg.user_id == receiverId && msg.receiver_id == currentUserId)
        );

        if (conversation.length === 0) {
            messagesContainer.innerHTML = `
                <div class="text-center text-muted mt-5">
                    No messages yet. Start the conversation!
                </div>`;
            return;
        }

        conversation.forEach(msg => {
            const isSentByCurrentUser = msg.user_id == currentUserId;
            const messageItem = `
                <div class="d-flex ${isSentByCurrentUser ? 'justify-content-end' : 'justify-content-start'} mb-3">
                    <div class="p-3 rounded ${isSentByCurrentUser ? 'bg-primary text-white' : 'bg-light text-dark'}" 
                         style="max-width: 60%;">
                        <strong>${msg.user.name}</strong><br>
                        ${msg.content}
                        <div class="text-end" style="font-size: 0.7rem; opacity: 0.6;">
                            ${new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                        </div>
                    </div>
                </div>`;
            messagesContainer.insertAdjacentHTML("beforeend", messageItem);
        });

        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    /** RENDER USERS LIST */
    function renderUsers() {
        userList.innerHTML = "";
        if (users.length > 0) {
            users.forEach(user => {
                const isActive = receiverId == user.id ? 'active' : '';
                const userItem = `
                    <a href="/chat?user=${user.id}"
                       class="user-item d-flex align-items-center p-3 text-decoration-none border-bottom position-relative ${isActive}"
                       style="transition: all 0.3s ease; color: inherit;">
                        <div class="position-relative me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 45px; height: 45px; background: linear-gradient(45deg, #667eea, #764ba2); color: white; font-weight: bold;">
                                 ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="position-absolute bottom-0 end-0 rounded-circle border border-2 border-white"
                                 style="width: 12px; height: 12px; background: #28a745;"></div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-semibold">${user.name}</h6>
                            <small class="text-muted">
                                <i class="fas fa-circle me-1" style="color: #28a745; font-size: 0.5rem;"></i>
                                ${user.role ?? "User"}
                            </small>
                        </div>
                        ${receiverId == user.id ? '<i class="fas fa-comment-dots text-primary"></i>' : ''}
                    </a>`;
                userList.insertAdjacentHTML("beforeend", userItem);
            });
        } else {
            userList.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-users text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-3">No users available</p>
                </div>`;
        }
    }

    /** RENDER CHAT AREA */
    function renderChat() {
        if (!receiverId) {
            chatArea.innerHTML = `
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
                                <span class="fw-semibold">${users.length} Users Online</span>
                            </div>
                        </div>
                    </div>
                </div>`;
            return;
        }

        const receiver = users.find(u => u.id == receiverId);
        chatArea.innerHTML = `
            <div class="card-header border-0 py-3" style="background: linear-gradient(45deg, #f8f9fa, #e9ecef); border-radius: 20px 20px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 40px; height: 40px; background: linear-gradient(45deg, #667eea, #764ba2); color: white; font-weight: bold;">
                        ${receiver ? receiver.name.charAt(0).toUpperCase() : "?"}
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: #2c3e50;">${receiver ? receiver.name : ""}</h5>
                        <small class="text-muted">
                            <i class="fas fa-circle me-1" style="color: #28a745; font-size: 0.5rem;"></i>
                            Online • ${receiver?.role ?? "User"}
                        </small>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 d-flex flex-column" style="height: 500px;">
                <div id="${messagesContainerId}" class="flex-grow-1 p-4"
                     style="overflow-y: auto; background: linear-gradient(to bottom, #f8f9fa, #ffffff);">
                    <!-- JS will insert messages -->
                </div>
                <div class="border-top p-3" style="background: #f8f9fa;">
                    <form id="chat-form" class="d-flex gap-2">
                        <input type="hidden" name="receiver_id" value="${receiverId}">
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
            </div>`;
    }

    /** INIT RENDER */
    renderUsers();
    renderChat();
    fetchChatList(); // Fetch messages immediately
});

</script>
@endsection
