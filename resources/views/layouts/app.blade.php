<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Fish Market'))</title>
    
    <!-- In your layouts/app.blade.php, before closing </body> tag -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skins/reverse.css') }}">

    @stack('css')
</head>
<body>
<div id="app">
    <div class="main-wrapper">
        @include('components.header')
        @include('components.sidebar')

        <div class="main-content">
            @yield('content')
        </div>

        @include('components.footer')
    </div>
</div>

<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
<script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('js/stisla.js') }}"></script>
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<!-- Modern Fish AI Chat Widget -->
<div id="fish-ai-chat-container" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
">
    <!-- Chat Head (Circle) -->
    <div id="chat-head" style="
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(144, 8, 178, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    ">
        <!-- Fish Icon -->
        <div style="
            color: white;
            font-size: 24px;
            transition: transform 0.3s ease;
        ">🐟</div>
        
        <!-- Notification Badge -->
        <div id="notification-badge" style="
            position: absolute;
            top: -2px;
            right: -2px;
            width: 20px;
            height: 20px;
            background: #ff4757;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: white;
            font-weight: bold;
            border: 2px solid white;
        ">1</div>
    </div>

    <!-- Chat Window -->
    <div id="chat-window" style="
        position: absolute;
        bottom: 70px;
        right: 0;
        width: 350px;
        height: 450px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        display: none;
        flex-direction: column;
        overflow: hidden;
        transform: scale(0.8) translateY(20px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    ">
        <!-- Chat Header -->
        <div style="
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            color: white;
            position: relative;
        ">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="
                    width: 32px;
                    height: 32px;
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 18px;
                ">🐟</div>
                <div>
                    <div style="font-weight: 600; font-size: 16px;">Fish AI Assistant</div>
                    <div id="status-indicator" style="font-size: 12px; opacity: 0.8;">Online</div>
                </div>
            </div>
            <!-- Close button -->
            <div id="close-chat" style="
                position: absolute;
                top: 15px;
                right: 15px;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.2s ease;
                font-size: 18px;
            ">×</div>
        </div>

        <!-- Messages Container -->
        <div id="chat-messages" style="
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 16px;
        ">
            <!-- Welcome Message -->
            <div class="message bot-message">
                <div class="message-content">
                    Hello! I'm Fish AI, your intelligent fishing and aquaculture assistant. How can I help you today? 🐟
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div id="typing-indicator" style="
            padding: 0 20px;
            display: none;
        ">
            <div style="
                background: white;
                border-radius: 18px;
                padding: 12px 16px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            ">
                <span style="font-size: 14px; color: #764ba2;">Fish AI is typing</span>
                <div class="typing-dots">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div style="
            padding: 20px;
            border-top: 1px solid #e2e8f0;
            background: white;
        ">
            <div style="
                display: flex;
                gap: 12px;
                background: #f1f5f9;
                border-radius: 25px;
                padding: 8px;
                align-items: center;
            ">
                <input type="text" id="chat-input" placeholder="Ask about fish, fishing, or aquaculture..." style="
                    flex: 1;
                    border: none;
                    background: transparent;
                    padding: 8px 12px;
                    font-size: 14px;
                    outline: none;
                    color: #334155;
                ">
                <button id="send-button" style="
                    width: 40px;
                    height: 40px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border: none;
                    border-radius: 50%;
                    color: white;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 16px;
                    transition: transform 0.2s ease;
                ">→</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Message Styles */
.message {
    display: flex;
    margin-bottom: 12px;
}

.bot-message {
    justify-content: flex-start;
}

.user-message {
    justify-content: flex-end;
}

.message-content {
    max-width: 80%;
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.4;
    word-wrap: break-word;
}

.bot-message .message-content {
    background: white;
    color: #334155;
    border-bottom-left-radius: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.user-message .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 6px;
}

/* Typing Animation */
.typing-dots {
    display: flex;
    gap: 3px;
}

.dot {
    width: 4px;
    height: 4px;
    background: #764ba2;
    border-radius: 50%;
    animation: typing 1.4s infinite ease-in-out;
}

.dot:nth-child(2) {
    animation-delay: 0.2s;
}

.dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
        opacity: 0.4;
    }
    30% {
        transform: translateY(-8px);
        opacity: 1;
    }
}

/* Hover Effects */
#chat-head:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(8, 145, 178, 0.5);
}

#send-button:hover {
    transform: scale(1.1);
}

#close-chat:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Scrollbar Styling */
#chat-messages::-webkit-scrollbar {
    width: 6px;
}

#chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

#chat-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

#chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
const chatHead = document.getElementById('chat-head');
const chatWindow = document.getElementById('chat-window');
const chatMessages = document.getElementById('chat-messages');
const chatInput = document.getElementById('chat-input');
const sendButton = document.getElementById('send-button');
const closeChat = document.getElementById('close-chat');
const typingIndicator = document.getElementById('typing-indicator');
const statusIndicator = document.getElementById('status-indicator');
const notificationBadge = document.getElementById('notification-badge');

let isOpen = false;
let unreadCount = 0;

// Toggle chat window
function toggleChat() {
    isOpen = !isOpen;
    
    if (isOpen) {
        chatWindow.style.display = 'flex';
        setTimeout(() => {
            chatWindow.style.transform = 'scale(1) translateY(0)';
            chatWindow.style.opacity = '1';
        }, 10);
        chatInput.focus();
        // Clear unread count
        unreadCount = 0;
        notificationBadge.style.display = 'none';
    } else {
        chatWindow.style.transform = 'scale(0.8) translateY(20px)';
        chatWindow.style.opacity = '0';
        setTimeout(() => {
            chatWindow.style.display = 'none';
        }, 300);
    }
}

// Event listeners
chatHead.addEventListener('click', toggleChat);
closeChat.addEventListener('click', toggleChat);

// Send message function
function sendMessage() {
    const message = chatInput.value.trim();
    if (message === '') return;

    // Add user message
    appendMessage('user', message);
    chatInput.value = '';

    // Show typing indicator
    showTypingIndicator();

    // Send to API
    sendToFishAI(message);
}

// Append message to chat
function appendMessage(sender, text) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${sender === 'user' ? 'user-message' : 'bot-message'}`;

    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';

    // ✅ Render Markdown properly
    contentDiv.innerHTML = marked.parse(text);

    messageDiv.appendChild(contentDiv);
    chatMessages.appendChild(messageDiv);

    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Show notification if chat is closed
    if (!isOpen && sender === 'bot') {
        unreadCount++;
        notificationBadge.textContent = unreadCount;
        notificationBadge.style.display = 'flex';
    }
}

// Show typing indicator
function showTypingIndicator() {
    typingIndicator.style.display = 'block';
    statusIndicator.textContent = 'Typing...';
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Hide typing indicator
function hideTypingIndicator() {
    typingIndicator.style.display = 'none';
    statusIndicator.textContent = 'Online';
}

// Send to Fish AI API
async function sendToFishAI(message) {
    try {
        const response = await fetch('{{ route("gemini.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ prompt: message })
        });

        const data = await response.json();
        
        // Hide typing indicator
        hideTypingIndicator();
        
        let botReply = data.output_text || 'Sorry, I couldn\'t generate a response.';
        
        // Add a slight delay for more natural feel
        setTimeout(() => {
            appendMessage('bot', botReply);
        }, 500);

    } catch (error) {
        hideTypingIndicator();
        setTimeout(() => {
            appendMessage('bot', 'Sorry, there was an error connecting to the service. Please try again.');
        }, 500);
        console.error('Error:', error);
    }
}

// Event listeners for sending messages
sendButton.addEventListener('click', sendMessage);

chatInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

// Auto-resize chat input
chatInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

</script>

@stack('scripts')
</body>
</html>