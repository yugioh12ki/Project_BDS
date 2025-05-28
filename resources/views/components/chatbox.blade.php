<!-- Chatbox Component -->
<div id="chatbox-container">
    <!-- Chat Toggle Button -->
    <div id="chat-toggle" class="chat-toggle">
        <i class="fas fa-comments" id="chat-icon"></i>
        <i class="fas fa-times" id="close-icon" style="display: none;"></i>
        <div class="chat-notification" id="chat-notification">1</div>
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
                    <h4>Trợ lý BĐS</h4>
                    <span class="chat-status">Đang hoạt động</span>
                </div>
            </div>
            <button id="chat-minimize" class="chat-minimize">
                <i class="fas fa-minus"></i>
            </button>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="chat-messages">
            <div class="message bot-message">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Xin chào! Tôi là trợ lý ảo của website bất động sản. Tôi có thể giúp bạn tìm hiểu về các dự án, giá cả, và thủ tục mua bán. Bạn cần hỗ trợ gì?
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
                <input type="text" id="chat-input" placeholder="Nhập câu hỏi của bạn..." maxlength="500">
                <button id="chat-send" type="button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <div class="chat-suggestions">
                <button class="suggestion-btn" data-text="Tôi muốn mua nhà">Tôi muốn mua nhà</button>
                <button class="suggestion-btn" data-text="Giá nhà hiện tại như thế nào?">Giá nhà hiện tại?</button>
                <button class="suggestion-btn" data-text="Thủ tục mua bán">Thủ tục mua bán</button>
            </div>
        </div>
    </div>
</div>

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
    width: 350px;
    height: 500px;
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
    opacity: 0.8;
}

.chat-minimize {
    background: none;
    border: none;
    color: white;
    font-size: 16px;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    transition: background 0.3s ease;
}

.chat-minimize:hover {
    background: rgba(255, 255, 255, 0.2);
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

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    font-size: 14px;
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
    max-width: 70%;
}

.user-message .message-content {
    text-align: right;
}

.message-bubble {
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.4;
    word-wrap: break-word;
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

#chat-send:hover {
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
}

.suggestion-btn {
    background: #e9ecef;
    border: none;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 12px;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
}

.suggestion-btn:hover {
    background: #667eea;
    color: white;
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

/* Mobile Responsive */
@media (max-width: 768px) {
    #chatbox-container {
        bottom: 15px;
        right: 15px;
    }

    .chat-window {
        width: calc(100vw - 30px);
        height: 70vh;
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
        height: 75vh;
        right: -10px;
    }
}
</style>

<!-- Chat JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chat-toggle');
    const chatWindow = document.getElementById('chat-window');
    const chatMinimize = document.getElementById('chat-minimize');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const chatMessages = document.getElementById('chat-messages');
    const typingIndicator = document.getElementById('typing-indicator');
    const chatNotification = document.getElementById('chat-notification');
    const chatIcon = document.getElementById('chat-icon');
    const closeIcon = document.getElementById('close-icon');
    const suggestionBtns = document.querySelectorAll('.suggestion-btn');

    let isOpen = false;

    // Toggle chat window
    chatToggle.addEventListener('click', function() {
        isOpen = !isOpen;
        if (isOpen) {
            chatWindow.classList.add('active');
            chatIcon.style.display = 'none';
            closeIcon.style.display = 'block';
            chatNotification.style.display = 'none';
            chatInput.focus();
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

    // Send message
    function sendMessage() {
        const message = chatInput.value.trim();
        if (message === '') return;

        // Add user message
        addMessage(message, 'user');
        chatInput.value = '';

        // Show typing indicator
        showTypingIndicator();

        // Send to API
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
            addMessage(data.answer, 'bot');
        })
        .catch(error => {
            hideTypingIndicator();
            addMessage('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.', 'bot');
            console.error('Error:', error);
        });
    }

    // Send button click
    chatSend.addEventListener('click', sendMessage);

    // Enter key press
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Suggestion buttons
    suggestionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.getAttribute('data-text');
            chatInput.value = text;
            sendMessage();
        });
    });

    // Add message function
    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;

        const currentTime = new Date().toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });

        if (sender === 'bot') {
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble">${text}</div>
                    <div class="message-time">${currentTime}</div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="message-content">
                    <div class="message-bubble">${text}</div>
                    <div class="message-time">${currentTime}</div>
                </div>
                <div class="message-avatar">
                    <i class="fas fa-user"></i>
                </div>
            `;
        }

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Show typing indicator
    function showTypingIndicator() {
        typingIndicator.style.display = 'flex';
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Hide typing indicator
    function hideTypingIndicator() {
        typingIndicator.style.display = 'none';
    }

    // Auto-focus input when window opens
    chatInput.addEventListener('focus', function() {
        setTimeout(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 100);
    });
});
</script>
