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