<!-- Chatbox Component - Dành cho tất cả user (Public Access) -->
<!-- Ẩn chatbox cho Admin users -->
@auth
    @if(Auth::user()->Role === 'Admin')
        <!-- Admin không hiển thị chatbox -->
        <style>
            #chatbox-container {
                display: none !important;
            }
        </style>
        <!-- Debug info for admin -->
        <script>
            console.log('Chatbox hidden for Admin user: {{ Auth::user()->Name }}');
        </script>
    @endif
@endauth

<!-- Chatbox container (hiển thị cho tất cả trừ admin) -->
<div id="chatbox-container">
    <!-- Chat Toggle Button -->
    <div id="chat-toggle" class="chat-toggle">
        <i class="fas fa-comments" id="chat-icon"></i>
        <i class="fas fa-times" id="close-icon" style="display: none;"></i>
        <div class="chat-notification" id="chat-notification">!</div>
    </div>

    <!-- Chat Window -->
    <div id="chat-window" class="chat-window">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="chat-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="chat-title">
                    <h4>
                        @auth
                            Xin chào, {{ Auth::user()->Name ?? 'User' }}!
                        @else
                            Trợ lý BĐS AI
                        @endauth
                    </h4>
                    <span class="chat-status">
                        @auth
                            🟢 Đã đăng nhập
                        @else
                            🟢 Online 24/7
                        @endauth
                    </span>
                </div>
            </div>
            <div class="chat-header-actions">
                <button id="chat-new-message" class="chat-action-btn" title="Bắt đầu cuộc trò chuyện mới">
                    <i class="fas fa-plus"></i>
                </button>
                <button id="chat-minimize" class="chat-minimize" title="Thu nhỏ">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="chat-messages">
            <div class="message bot-message welcome-message">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        👋 Xin chào! Tôi là trợ lý AI chuyên về bất động sản.<br><br>
                        🏠 Tôi có thể giúp bạn:<br>
                        • Tư vấn mua bán nhà đất<br>
                        • Thông tin giá cả thị trường<br>
                        • Hướng dẫn thủ tục pháp lý<br>
                        • Đánh giá dự án đầu tư<br><br>
                        💬 Hãy đặt câu hỏi hoặc chọn gợi ý bên dưới!
                    </div>
                    <div class="message-time">{{ date('H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div id="typing-indicator" class="typing-indicator" style="display: none;">
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="chat-input-container">
            <div class="chat-input-wrapper">
                <input type="text" id="chat-input" placeholder="Nhập câu hỏi về bất động sản..." maxlength="500">
                <button id="chat-send" type="button" title="Gửi tin nhắn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <div class="chat-suggestions" id="chat-suggestions">
                <button class="suggestion-btn" data-text="Tôi muốn mua nhà, cần tư vấn gì?">🏠 Tư vấn mua nhà</button>
                <button class="suggestion-btn" data-text="Giá nhà đất hiện tại như thế nào?">💰 Giá thị trường</button>
                <button class="suggestion-btn" data-text="Thủ tục mua bán nhà cần những gì?">📄 Thủ tục pháp lý</button>
                <button class="suggestion-btn" data-text="Tôi muốn thuê căn hộ chung cư">🏢 Thuê căn hộ</button>
                <button class="suggestion-btn" data-text="Tư vấn đầu tư bất động sản">📈 Đầu tư BĐS</button>
                <button class="suggestion-btn" data-text="Các dự án mới đang mở bán">🏗️ Dự án mới</button>
                <button class="suggestion-btn contact-admin-btn" data-action="contact-admin">👨‍💼 Liên hệ Admin</button>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome Icons (ensure icons display properly) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>

<!-- Chat Styles -->
<style>
#chatbox-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Chat Toggle Button */
.chat-toggle {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    position: relative;
    animation: pulse 2s infinite;
}

.chat-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
}

.chat-toggle i {
    color: white;
    font-size: 24px;
    transition: all 0.3s ease;
}

.chat-notification {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    animation: bounce 1s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4); }
    50% { box-shadow: 0 4px 20px rgba(102, 126, 234, 0.8); }
    100% { box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4); }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-5px); }
    60% { transform: translateY(-3px); }
}

/* Chat Window */
.chat-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    height: 550px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

.chat-window.active {
    display: flex;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Chat Header */
.chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chat-header-info {
    display: flex;
    align-items: center;
}

.chat-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
}

.chat-avatar i {
    font-size: 18px;
}

.chat-title h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.chat-status {
    font-size: 12px;
    opacity: 0.9;
    margin-top: 2px;
}

.chat-header-actions {
    display: flex;
    gap: 5px;
    align-items: center;
}

.chat-action-btn, .chat-minimize {
    background: none;
    border: none;
    color: white;
    font-size: 14px;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: background 0.3s ease;
    opacity: 0.8;
}

.chat-action-btn:hover, .chat-minimize:hover {
    background: rgba(255, 255, 255, 0.2);
    opacity: 1;
}

/* Chat Messages */
.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f8f9fa;
}

.message {
    display: flex;
    margin-bottom: 15px;
    animation: fadeIn 0.3s ease;
}

.message.user-message {
    justify-content: flex-end;
}

.welcome-message .message-bubble {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%) !important;
    color: #333 !important;
    border: 1px solid #e1e5e9;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    font-size: 14px;
    flex-shrink: 0;
}

.bot-message .message-avatar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.user-message .message-avatar {
    background: #e9ecef;
    color: #6c757d;
    margin-right: 0;
    margin-left: 10px;
    order: 2;
}

.message-content {
    max-width: 75%;
}

.user-message .message-content {
    text-align: right;
}

.message-bubble {
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;
    white-space: pre-line;
}

.bot-message .message-bubble {
    background: white;
    color: #333;
    border-bottom-left-radius: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.user-message .message-bubble {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 6px;
}

.admin-message .message-avatar {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
}

.admin-message .message-bubble {
    background: linear-gradient(135deg, #ff9f43 0%, #feca57 100%);
    color: #333;
    border-bottom-left-radius: 6px;
    border: 1px solid #f39c12;
    box-shadow: 0 2px 8px rgba(243, 156, 18, 0.2);
    font-weight: 500;
}

.admin-message .message-bubble::before {
    content: "👨‍💼 Nhân viên hỗ trợ: ";
    font-weight: 600;
    color: #e74c3c;
}

.message-time {
    font-size: 11px;
    color: #999;
    margin-top: 5px;
    padding: 0 16px;
}

.user-message .message-time {
    text-align: right;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Typing Indicator */
.typing-indicator {
    display: flex;
    align-items: center;
    padding: 0 20px 10px;
    background: #f8f9fa;
}

.typing-dots {
    background: white;
    padding: 12px 16px;
    border-radius: 18px;
    border-bottom-left-radius: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-left: 10px;
}

.typing-dots span {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #999;
    margin: 0 2px;
    animation: typing 1.4s infinite;
}

.typing-dots span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dots span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-10px); }
}

/* Chat Input */
.chat-input-container {
    background: white;
    border-top: 1px solid #e9ecef;
    padding: 15px 20px;
}

.chat-input-wrapper {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border-radius: 25px;
    padding: 8px 15px;
    margin-bottom: 10px;
    border: 2px solid transparent;
    transition: border-color 0.3s ease;
}

.chat-input-wrapper:focus-within {
    border-color: #667eea;
}

#chat-input {
    flex: 1;
    border: none;
    background: none;
    outline: none;
    font-size: 14px;
    padding: 8px 0;
    color: #333;
}

#chat-input::placeholder {
    color: #999;
}

#chat-input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

#chat-send {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-left: 10px;
}

#chat-send:hover:not(:disabled) {
    transform: scale(1.1);
}

#chat-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Chat Suggestions */
.chat-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    max-height: 80px;
    overflow-y: auto;
}

.suggestion-btn {
    background: #e9ecef;
    border: none;
    padding: 8px 12px;
    border-radius: 15px;
    font-size: 12px;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.suggestion-btn:hover {
    background: #667eea;
    color: white;
    transform: translateY(-1px);
}

/* Status indicators */
.status-online {
    color: #28a745;
}

.status-typing {
    color: #ffc107;
}

/* Scrollbar */
.chat-messages::-webkit-scrollbar {
    width: 4px;
}

.chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 2px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #999;
}

/* Conversation Separator */
.conversation-separator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 20px 0;
    position: relative;
}

.separator-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, transparent, #ddd, transparent);
}

.separator-text {
    background: #f8f9fa;
    padding: 8px 15px;
    border: 1px solid #e9ecef;
    border-radius: 20px;
    font-size: 12px;
    color: #666;
    margin: 0 10px;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

.separator-text i {
    font-size: 10px;
    color: #999;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    #chatbox-container {
        bottom: 15px;
        right: 15px;
    }

    .chat-window {
        width: calc(100vw - 30px);
        height: 75vh;
        bottom: 80px;
        right: -15px;
    }

    .chat-toggle {
        width: 55px;
        height: 55px;
    }

    .chat-toggle i {
        font-size: 22px;
    }
}

@media (max-width: 480px) {
    .chat-window {
        width: calc(100vw - 20px);
        height: 80vh;
        right: -10px;
    }

    .message-content {
        max-width: 85%;
    }
}

/* Conversation Separator */
.conversation-separator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 20px 0;
    position: relative;
}

.separator-line {
    flex: 1;
    height: 1px;
    background: #e9ecef;
}

.separator-text {
    background: white;
    padding: 0 10px;
    font-size: 12px;
    color: #666;
    white-space: nowrap;
    position: relative;
    z-index: 1;
}

.separator-text i {
    margin-right: 4px;
    color: #667eea;
}

/* Welcome Message Adjustment */
.welcome-message {
    margin-top: 10px;
}

/* Admin Message Highlight */
.admin-reply {
    border-left: 4px solid #28a745;
    padding-left: 16px;
    margin-left: 10px;
    position: relative;
}

.admin-reply::before {
    content: '';
    position: absolute;
    top: 0;
    left: -6px;
    width: 6px;
    height: 100%;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 4px 0 0 4px;
}

/* Enhanced Bot Message */
.bot-message {
    position: relative;
}

.bot-message::after {
    content: '';
    position: absolute;
    top: 0;
    right: -8px;
    width: 8px;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 0 4px 4px 0;
}

/* User Message Enhancement */
.user-message {
    position: relative;
}

.user-message::before {
    content: '';
    position: absolute;
    top: 0;
    left: -8px;
    width: 8px;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px 0 0 4px;
}

/* Mobile Adjustments */
@media (max-width: 480px) {
    .chat-window {
        width: calc(100% - 30px);
        height: 70vh;
        right: 15px;
        bottom: 75px;
    }

    .chat-toggle {
        width: 50px;
        height: 50px;
    }

    .chat-toggle i {
        font-size: 20px;
    }

    .message-bubble {
        padding: 10px 14px;
        border-radius: 16px;
        font-size: 13px;
    }

    .message-time {
        font-size: 10px;
    }

    .chat-header {
        padding: 10px 15px;
    }

    .chat-title h4 {
        font-size: 14px;
    }

    .chat-status {
        font-size: 11px;
    }

    .chat-action-btn, .chat-minimize {
        padding: 6px;
        font-size: 12px;
    }

    .separator-text {
        font-size: 11px;
    }
}
</style>

<!-- Chat JavaScript -->
<script>
// Firebase Configuration
const firebaseConfig = {
    apiKey: "AIzaSyDqqenpK-EGlKcvjgl-kBHyQ9V8BSSTEkg",
    authDomain: "bds-chat-8ea88.firebaseapp.com",
    databaseURL: "https://bds-chat-8ea88-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "bds-chat-8ea88",
    storageBucket: "bds-chat-8ea88.firebasestorage.app",
    messagingSenderId: "468265302327",
    appId: "1:468265302327:web:1c610887df3380953a02b2",
    measurementId: "G-J0MTJ840VS"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const db = firebase.database();

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Chatbox JavaScript loading...');

    const chatToggle = document.getElementById('chat-toggle');
    const chatWindow = document.getElementById('chat-window');
    const chatMinimize = document.getElementById('chat-minimize');
    const chatNewMessage = document.getElementById('chat-new-message');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const chatMessages = document.getElementById('chat-messages');
    const typingIndicator = document.getElementById('typing-indicator');
    const chatNotification = document.getElementById('chat-notification');
    const chatIcon = document.getElementById('chat-icon');
    const closeIcon = document.getElementById('close-icon');
    const suggestionBtns = document.querySelectorAll('.suggestion-btn');

    // Debug: Check if elements are found
    console.log('🔍 Element check:', {
        chatToggle: !!chatToggle,
        chatNewMessage: !!chatNewMessage,
        chatWindow: !!chatWindow,
        chatInput: !!chatInput
    });

    let isOpen = false;
    let sessionId = localStorage.getItem('chatbox_session_id') || null;
    let messageCount = 0;
    let conversationId = sessionId; // Set conversation ID immediately if session exists
    let messageListener = null;
    let messagesRef = null;
    let isEscalated = localStorage.getItem('chatbox_escalated') === 'true';

    // Initialize
    loadChatHistory();
    loadSuggestions();

    // Setup Firebase connection and listeners
    setupFirebaseConnection();

    // Important: Setup message listener immediately if we have a session
    if (sessionId && conversationId) {
        console.log('Existing session found:', sessionId, 'Escalated:', isEscalated);

        // Setup listener immediately for real-time messages
        setupMessageListener();

        // Also ensure we're listening for any existing conversation state
        checkConversationState();
    }

    // Check if already escalated and show notification
    if (isEscalated && sessionId) {
        console.log('Conversation already escalated - ensuring real-time listener is active');
        showEscalationStatus();
    }

    // Toggle chat window
    chatToggle.addEventListener('click', function() {
        isOpen = !isOpen;
        if (isOpen) {
            chatWindow.classList.add('active');
            chatIcon.style.display = 'none';
            closeIcon.style.display = 'block';
            chatNotification.style.display = 'none';
            setTimeout(() => chatInput.focus(), 300);
        } else {
            chatWindow.classList.remove('active');
            chatIcon.style.display = 'block';
            closeIcon.style.display = 'none';
        }
    });

    // Minimize chat
    chatMinimize.addEventListener('click', function() {
        isOpen = false;
        chatWindow.classList.remove('active');
        chatIcon.style.display = 'block';
        closeIcon.style.display = 'none';
    });

    // Start new conversation
    chatNewMessage.addEventListener('click', function() {
        console.log('🆕 New conversation button clicked!');
        if (confirm('Bạn có muốn bắt đầu cuộc trò chuyện mới? Tin nhắn hiện tại sẽ được lưu lại.')) {
            console.log('✅ User confirmed new conversation');
            startNewConversation();
        } else {
            console.log('❌ User cancelled new conversation');
        }
    });

    // Send message function
    function sendMessage() {
        const message = chatInput.value.trim();
        if (message === '' || chatInput.disabled) return;

        // Disable input
        setInputState(false);

        // Add user message
        addMessage(message, 'user');
        chatInput.value = '';

        // Show typing indicator
        showTypingIndicator();

        // Check if escalated - if yes, only save to Firebase and wait for admin
        if (isEscalated && conversationId) {
            hideTypingIndicator();
            saveUserMessageToFirebase(message);

            // Add system message to inform user
            const systemMessageDiv = document.createElement('div');
            systemMessageDiv.className = 'message bot-message admin-reply';
            systemMessageDiv.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-user-tie" style="color: #28a745;"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white;">
                        📨 Tin nhắn của bạn đã được gửi đến admin. Vui lòng chờ admin phản hồi.
                    </div>
                    <div class="message-time">${new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}</div>
                </div>
            `;
            chatMessages.appendChild(systemMessageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Re-enable input
            setInputState(true);
            return;
        }

        // If not escalated, proceed with chatbot API
        callChatbotAPI(message);
    }

    // Call chatbot API with fallback
    function callChatbotAPI(message) {
        const payload = sessionId ?
            { question: message, session_id: sessionId } :
            { question: message };

        const apiUrl = '/api/chatbot/advanced';

        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            hideTypingIndicator();

            if (data.status === 'success') {
                addMessage(data.answer, 'bot');

                // Check if escalated to admin
                if (data.escalated === true) {
                    isEscalated = true;
                    localStorage.setItem('chatbox_escalated', 'true');

                    // Setup or refresh message listener for admin responses
                    if (sessionId) {
                        conversationId = sessionId;
                        setupMessageListener();
                    }

                    console.log('Conversation escalated to admin - listening for real-time messages');
                }

                // Save session ID
                if (data.session_id && !sessionId) {
                    sessionId = data.session_id;
                    conversationId = sessionId;
                    localStorage.setItem('chatbox_session_id', sessionId);

                    // Setup message listener for new conversations
                    setupMessageListener();
                }
            } else {
                throw new Error('API returned error status');
            }
        })
        .catch(error => {
            console.log('Advanced API failed, trying basic method...');
            // Fallback to basic method
            fetch('/api/chatbot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ question: message })
            })
            .then(response => response.json())
            .then(data => {
                hideTypingIndicator();
                addMessage(data.answer || 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.', 'bot');
            })
            .catch(fallbackError => {
                hideTypingIndicator();
                addMessage('⚠️ Hiện tại hệ thống đang bận. Vui lòng thử lại sau ít phút.', 'bot');
                console.error('All API methods failed:', fallbackError);
            });
        })
        .finally(() => {
            setInputState(true);
        });
    }

    // Set input state (enabled/disabled)
    function setInputState(enabled) {
        chatInput.disabled = !enabled;
        chatSend.disabled = !enabled;
        if (enabled) {
            chatInput.focus();
        }
    }

    // Event listeners
    chatSend.addEventListener('click', sendMessage);

    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Suggestion buttons
    suggestionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const text = this.getAttribute('data-text');

            if (action === 'contact-admin') {
                contactAdmin();
            } else if (text) {
                chatInput.value = text;
                sendMessage();
            }
        });
    });

    // Firebase Helper Functions
    function setupFirebaseConnection() {
        // Test Firebase connection
        db.ref('.info/connected').on('value', function(snapshot) {
            if (snapshot.val() === true) {
                console.log('🔥 Firebase connected successfully');

                // If we have a session and Firebase is connected, ensure listener is active
                if (conversationId) {
                    console.log('🔄 Firebase connected - ensuring message listener is active');
                    setupMessageListener();
                }

                // Check conversation state when Firebase connects
                if (isEscalated && conversationId) {
                    setTimeout(() => {
                        checkConversationState();
                    }, 1000);
                }
            } else {
                console.warn('❌ Firebase connection lost');
            }
        });

        // Monitor Firebase auth state changes
        db.ref('.info/serverTimeOffset').on('value', function(snapshot) {
            const offset = snapshot.val() || 0;
            console.log('Firebase server time offset:', offset);
        });
    }

    function setupMessageListener() {
        if (!conversationId) {
            console.log('No conversation ID available for message listener');
            return;
        }

        // Remove any existing listener first
        removeMessageListener();

        console.log('Setting up message listener for conversation:', conversationId);

        try {
            // Listen for new messages in real-time
            messagesRef = db.ref('chats/' + conversationId + '/messages');

            messageListener = function(snapshot) {
                const message = snapshot.val();
                const messageId = snapshot.key;

                console.log('New message detected:', message);

                // Only process messages from admin or system (not user messages)
                if (message.sender_id === 'admin' || message.sender_id === 'system') {
                    // Check if message is not already displayed to avoid duplicates
                    if (!document.querySelector(`[data-msg-id="${messageId}"]`)) {
                        console.log('New admin message received:', message);
                        addRealtimeMessage(message, messageId);
                        showNotificationIfClosed();
                        playNotificationSound();

                        // Update last displayed time
                        localStorage.setItem('chatbox_last_displayed_time', message.timestamp.toString());
                    }
                }
            };

            // Attach the listener to the messages reference
            messagesRef.on('child_added', messageListener);

            // Also listen for conversation status changes
            const conversationRef = db.ref('chats/' + conversationId);
            conversationRef.on('child_changed', function(snapshot) {
                console.log('Conversation property changed:', snapshot.key, snapshot.val());

                if (snapshot.key === 'status' && snapshot.val() === 'admin_replied') {
                    console.log('Admin replied to conversation');
                    isEscalated = true;
                    localStorage.setItem('chatbox_escalated', 'true');
                    showEscalationStatus();
                }

                if (snapshot.key === 'needs_admin' && snapshot.val() === true) {
                    console.log('Conversation needs admin attention');
                    isEscalated = true;
                    localStorage.setItem('chatbox_escalated', 'true');
                    showEscalationStatus();
                }
            });

            console.log('Firebase message listener setup complete');
        } catch (error) {
            console.error('Error setting up message listener:', error);
        }
    }

    function addRealtimeMessage(message, messageId = null) {
        // Double check to prevent duplicates
        if (messageId && document.querySelector(`[data-msg-id="${messageId}"]`)) {
            console.log('Message already displayed, skipping:', messageId);
            return;
        }

        const currentTime = new Date(message.timestamp * 1000).toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });

        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot-message admin-reply';
        if (messageId) {
            messageDiv.setAttribute('data-msg-id', messageId);
        }

        // Special styling for admin messages with enhanced visibility
        messageDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-user-tie" style="color: #28a745;"></i>
            </div>
            <div class="message-content">
                <div class="message-bubble" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: 2px solid #d4edda; animation: fadeIn 0.5s ease, glow 2s ease;">
                    <strong>👨‍💼 Admin:</strong> ${message.content}
                </div>
                <div class="message-time">${currentTime} <span style="color: #28a745;">● Real-time</span></div>
            </div>
        `;

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Save to history with special admin prefix
        saveMessageToHistory(`👨‍💼 Admin: ${message.content}`, 'bot', currentTime);

        // Increment message count
        messageCount++;

        // Show special notification and effects
        playNotificationSound();

        // Add glow animation CSS if not exists
        if (!document.getElementById('glow-animation-css')) {
            const style = document.createElement('style');
            style.id = 'glow-animation-css';
            style.textContent = `
                @keyframes glow {
                    0% { box-shadow: 0 0 5px rgba(40, 167, 69, 0.5); }
                    50% { box-shadow: 0 0 20px rgba(40, 167, 69, 0.8); }
                    100% { box-shadow: 0 0 5px rgba(40, 167, 69, 0.5); }
                }
            `;
            document.head.appendChild(style);
        }

        console.log('✅ Real-time admin message displayed successfully');
    }

    function showNotificationIfClosed() {
        if (!isOpen) {
            chatNotification.style.display = 'flex';
            chatNotification.textContent = '!';
            // Animate the toggle button
            chatToggle.style.animation = 'pulse 0.5s ease-in-out 3';
        }
    }

    function playNotificationSound() {
        // Simple notification sound using Web Audio API
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            console.log('Audio notification not available');
        }
    }

    function removeMessageListener() {
        if (messageListener && messagesRef) {
            messagesRef.off('child_added', messageListener);
            messageListener = null;
            messagesRef = null;
        }
    }

    function saveUserMessageToFirebase(message) {
        if (!conversationId) {
            console.log('No conversation ID available for saving user message to Firebase');
            return;
        }

        try {
            // Get user information for authenticated users
            @auth
                const userData = {
                    sender_id: '{{ Auth::user()->UserID }}',
                    sender_name: '{{ Auth::user()->Name }}',
                    sender_role: '{{ Auth::user()->Role }}',
                    sender_email: '{{ Auth::user()->Email }}'
                };
            @else
                const userData = {
                    sender_id: 'guest_' + Date.now(),
                    sender_name: 'Khách',
                    sender_role: 'Guest',
                    sender_email: null
                };
            @endauth

            const messageData = {
                content: message,
                sender_id: userData.sender_id,
                sender_name: userData.sender_name,
                sender_role: userData.sender_role,
                sender_email: userData.sender_email,
                timestamp: Math.floor(Date.now() / 1000),
                type: 'text',
                is_read: false
            };

            const messagesRef = db.ref('chats/' + conversationId + '/messages');
            messagesRef.push(messageData)
                .then(() => {
                    console.log('User message saved to Firebase successfully with user data:', userData);
                })
                .catch((error) => {
                    console.error('Error saving user message to Firebase:', error);
                });
        } catch (error) {
            console.error('Error in saveUserMessageToFirebase:', error);
        }
    }

    // Message functions
    function addMessage(text, sender) {
        const currentTime = new Date().toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });

        addMessageToUI(text, sender, currentTime);
        saveMessageToHistory(text, sender, currentTime);
        messageCount++;
    }

    function addMessageToUI(text, sender, time) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;

        if (sender === 'bot') {
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble">${text}</div>
                    <div class="message-time">${time}</div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="message-content">
                    <div class="message-bubble">${text}</div>
                    <div class="message-time">${time}</div>
                </div>
                <div class="message-avatar">
                    <i class="fas fa-user"></i>
                </div>
            `;
        }

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Typing indicator
    function showTypingIndicator() {
        typingIndicator.style.display = 'flex';
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function hideTypingIndicator() {
        typingIndicator.style.display = 'none';
    }

    // Chat history management
    function loadChatHistory() {
        try {
            const savedMessages = localStorage.getItem('chatbox_history');
            if (savedMessages) {
                const messages = JSON.parse(savedMessages);
                // Skip welcome message, load saved ones
                const welcomeMsg = chatMessages.querySelector('.welcome-message');
                messages.forEach(msg => {
                    addMessageToUI(msg.text, msg.sender, msg.time);
                });
                messageCount = messages.length;
            }
        } catch (e) {
            console.warn('Failed to load chat history:', e);
        }
    }

    function saveMessageToHistory(text, sender, time) {
        try {
            const savedMessages = localStorage.getItem('chatbox_history');
            let messages = savedMessages ? JSON.parse(savedMessages) : [];

            messages.push({ text, sender, time });

            // Keep only last 100 messages
            if (messages.length > 100) {
                messages = messages.slice(-100);
            }

            localStorage.setItem('chatbox_history', JSON.stringify(messages));
        } catch (e) {
            console.warn('Failed to save message:', e);
        }
    }

    function saveChatHistory() {
        try {
            // Get all current messages except welcome message
            const currentMessages = [];
            const messageElements = chatMessages.querySelectorAll('.message:not(.welcome-message)');

            messageElements.forEach(msgElement => {
                const isUser = msgElement.classList.contains('user-message');
                const isBot = msgElement.classList.contains('bot-message');
                const isAdmin = msgElement.classList.contains('admin-message');

                let sender = 'bot';
                if (isUser) sender = 'user';
                else if (isAdmin) sender = 'admin';

                const messageContent = msgElement.querySelector('.message-bubble');
                const messageTime = msgElement.querySelector('.message-time');

                if (messageContent) {
                    currentMessages.push({
                        text: messageContent.innerHTML,
                        sender: sender,
                        time: messageTime ? messageTime.textContent : new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
                    });
                }
            });

            // Save to a separate conversation history if we want to keep multiple conversations
            const conversationHistory = localStorage.getItem('chatbox_conversation_history');
            let conversations = conversationHistory ? JSON.parse(conversationHistory) : [];

            if (currentMessages.length > 0) {
                conversations.push({
                    id: sessionId,
                    timestamp: new Date().toISOString(),
                    messages: currentMessages,
                    isEscalated: isEscalated
                });

                // Keep only last 10 conversations
                if (conversations.length > 10) {
                    conversations = conversations.slice(-10);
                }

                localStorage.setItem('chatbox_conversation_history', JSON.stringify(conversations));
            }
        } catch (e) {
            console.warn('Failed to save conversation history:', e);
        }
    }

    function startNewConversation() {
        console.log('🔄 Starting new conversation...');

        // Save current conversation to history if it exists
        if (sessionId && messageCount > 0) {
            console.log('💾 Saving current conversation to history');
            saveChatHistory();
        }

        // Remove Firebase listener for current conversation
        removeMessageListener();

        // Generate new session ID
        sessionId = 'chat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        conversationId = sessionId;

        // Reset state but keep user logged experience
        isEscalated = false;
        messageCount = 0;

        // Store new session
        localStorage.setItem('chatbox_session_id', sessionId);
        localStorage.removeItem('chatbox_escalated');

        // Clear messages except welcome message
        const messages = chatMessages.querySelectorAll('.message:not(.welcome-message)');
        messages.forEach(msg => msg.remove());

        // Add conversation separator if there were previous messages
        if (messageCount > 0) {
            addConversationSeparator();
        }

        // Setup new Firebase listener
        setupMessageListener();

        // Show success message with enhanced styling
        addMessage('🆕 <strong>Cuộc trò chuyện mới đã bắt đầu!</strong><br>📝 Lịch sử trò chuyện trước đã được lưu.<br>💬 Bạn có thể đặt câu hỏi mới ngay bây giờ.', 'bot');

        // Focus input
        setTimeout(() => chatInput.focus(), 500);
    }

    function addConversationSeparator() {
        const separator = document.createElement('div');
        separator.className = 'conversation-separator';
        separator.innerHTML = `
            <div class="separator-line"></div>
            <div class="separator-text">
                <i class="fas fa-history"></i>
                <span>Cuộc trò chuyện trước (đã lưu)</span>
            </div>
            <div class="separator-line"></div>
        `;

        // Insert before welcome message
        const welcomeMessage = chatMessages.querySelector('.welcome-message');
        if (welcomeMessage) {
            chatMessages.insertBefore(separator, welcomeMessage);
        } else {
            chatMessages.appendChild(separator);
        }

        // Scroll to show the separator
        setTimeout(() => {
            separator.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }

    // Load dynamic suggestions
    function loadSuggestions() {
        fetch('/api/chatbot/suggestions')
            .then(response => response.json())
            .then(data => {
                if (data.suggestions && data.suggestions.length > 0) {
                    updateSuggestions(data.suggestions.slice(0, 6)); // Show max 6 suggestions
                }
            })
            .catch(error => {
                console.log('Could not load dynamic suggestions:', error);
            });
    }

    // Contact admin function
    function contactAdmin() {
        if (chatInput.disabled) return;

        const adminMessage = "Tôi muốn liên hệ với admin";

        // Disable input
        setInputState(false);

        // Add user message
        addMessage(adminMessage, 'user');

        // Show typing indicator
        showTypingIndicator();

        // Call API with admin contact request
        const payload = sessionId ?
            { question: adminMessage, session_id: sessionId } :
            { question: adminMessage };

        fetch('/api/chatbot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            hideTypingIndicator();
            setInputState(true);

            if (data.status === 'success') {
                sessionId = data.session_id || sessionId;
                conversationId = sessionId;
                const escalated = data.escalated || false;

                // Add special styling for escalation messages
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message bot-message';
                messageDiv.innerHTML = `
                    <div class="message-avatar">
                        <i class="fas fa-user-tie" style="color: #ff6b6b;"></i>
                    </div>
                    <div class="message-content">
                        <div class="message-bubble" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); color: white; border: 2px solid #ff9ff3;">
                            ${data.answer || data.response}
                        </div>
                        <div class="message-time">${data.timestamp}</div>
                    </div>
                `;

                chatMessages.appendChild(messageDiv);
                chatMessages.scrollTop = chatMessages.scrollHeight;

                // Save to history
                saveMessageToHistory(data.answer || data.response, 'bot', data.timestamp);

                if (escalated) {
                    // Mark as escalated and setup Firebase listener
                    isEscalated = true;
                    localStorage.setItem('chatbox_escalated', 'true');
                    localStorage.setItem('chatbox_session_id', sessionId);

                    // Setup real-time message listener
                    setupMessageListener();

                    // Show special notification for escalation
                    setTimeout(() => {
                        const notificationDiv = document.createElement('div');
                        notificationDiv.className = 'message bot-message';
                        notificationDiv.innerHTML = `
                            <div class="message-avatar">
                                <i class="fas fa-bell" style="color: #ffa502;"></i>
                            </div>
                            <div class="message-content">
                                <div class="message-bubble" style="background: #fff3cd; color: #856404; border: 1px solid #ffeaa7;">
                                    ⚡ <strong>Thông báo:</strong> Cuộc trò chuyện này đã được chuyển đến Admin.
                                    Admin sẽ trả lời trực tiếp tại đây trong thời gian thực.
                                    <br><br>
                                    🔔 <strong>Bạn sẽ nhận được thông báo ngay khi Admin phản hồi!</strong>
                                </div>
                                <div class="message-time">${new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}</div>
                            </div>
                        `;
                        chatMessages.appendChild(notificationDiv);
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }, 1000);
                }
            } else {
                addMessage('Xin lỗi, không thể kết nối với admin lúc này. Vui lòng thử lại sau.', 'bot');
            }
        })
        .catch(error => {
            console.error('Admin contact error:', error);
            hideTypingIndicator();
            setInputState(true);
            addMessage('Lỗi kết nối. Vui lòng thử lại sau hoặc liên hệ trực tiếp: 0123456789', 'bot');
        });
    }

    function updateSuggestions(suggestions) {
        const suggestionsContainer = document.getElementById('chat-suggestions');
        const emojis = ['🏠', '💰', '📄', '🏢', '📈', '🏗️', '🔍', '📞'];

        suggestionsContainer.innerHTML = '';
        suggestions.forEach((suggestion, index) => {
            const btn = document.createElement('button');
            btn.className = 'suggestion-btn';
            btn.setAttribute('data-text', suggestion);
            btn.innerHTML = `${emojis[index] || '💬'} ${suggestion}`;
            btn.addEventListener('click', function() {
                chatInput.value = suggestion;
                sendMessage();
            });
            suggestionsContainer.appendChild(btn);
        });
    }

    // Auto-focus when opening
    chatInput.addEventListener('focus', function() {
        setTimeout(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 100);
    });

    function checkConversationState() {
        if (!conversationId) return;

        console.log('Checking conversation state for:', conversationId);

        try {
            // Check conversation status in Firebase
            db.ref('chats/' + conversationId).once('value').then(function(snapshot) {
                const conversationData = snapshot.val();

                if (conversationData) {
                    console.log('Conversation data loaded:', conversationData);

                    // Update escalation status if needed
                    if (conversationData.needs_admin === true || conversationData.status === 'admin_replied') {
                        isEscalated = true;
                        localStorage.setItem('chatbox_escalated', 'true');
                        showEscalationStatus();
                    }

                    // Check for unread admin messages
                    if (conversationData.messages) {
                        checkForUnreadAdminMessages(conversationData.messages);
                    }
                }
            }).catch(function(error) {
                console.error('Error checking conversation state:', error);
            });
        } catch (error) {
            console.error('Error in checkConversationState:', error);
        }
    }

    function showEscalationStatus() {
        // Show notification if chat is closed
        if (!isOpen) {
            chatNotification.style.display = 'flex';
            chatNotification.textContent = '!';
            chatToggle.style.animation = 'pulse 2s infinite';
        }

        // Update chat header to show escalated status
        const chatTitle = document.querySelector('.chat-title h4');
        if (chatTitle && !chatTitle.textContent.includes('Admin')) {
            @auth
                chatTitle.innerHTML = 'Xin chào, {{ Auth::user()->Name ?? "User" }}! <span style="color: #ff6b6b; font-size: 12px;">(Đã chuyển Admin)</span>';
            @else
                chatTitle.innerHTML = '🔥 Trợ lý BĐS AI <span style="color: #ff6b6b; font-size: 12px;">(Đã chuyển Admin)</span>';
            @endauth
        }

        console.log('Escalation status displayed');
    }

    function checkForUnreadAdminMessages(messages) {
        if (!messages) return;

        const lastDisplayedTime = localStorage.getItem('chatbox_last_displayed_time') || '0';
        let hasNewAdminMessages = false;

        Object.values(messages).forEach(message => {
            if ((message.sender_id === 'admin' || message.sender_id === 'system') &&
                message.timestamp > parseInt(lastDisplayedTime)) {
                hasNewAdminMessages = true;
            }
        });

        if (hasNewAdminMessages && !isOpen) {
            chatNotification.style.display = 'flex';
            chatNotification.textContent = '!';
            playNotificationSound();
        }
    }
});
</script>

</div> <!-- End chatbox-container -->