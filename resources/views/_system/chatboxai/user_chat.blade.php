@extends('_layout._layadmin.app')

@section('title', 'Chat Hỗ trợ')

@section('chatbotai')

<div class="chat-container">
    <div class="chat-header">
        <h3>Hỗ trợ khách hàng</h3>
        <div class="user-status">
            @auth
                <span class="status-badge user">Đã đăng nhập: {{ Auth::user()->Name ?? 'User' }}</span>
            @else
                <span class="status-badge guest">Khách</span>
            @endauth
        </div>
    </div>

    <div id="chatMessages" class="chat-messages">
        <div class="message bot-message">
            <div class="message-content">
                <strong>Bot:</strong> Xin chào! Tôi có thể giúp gì cho bạn?
            </div>
            <div class="message-time">{{ now()->format('H:i') }}</div>
        </div>
    </div>

    <div class="chat-input-container">
        <form id="chatForm" class="chat-form">
            <input type="text" id="messageInput" placeholder="Nhập câu hỏi của bạn..." required>
            <button type="submit" id="sendBtn">
                <i class="fa fa-paper-plane"></i>
            </button>
        </form>
    </div>

    <div class="chat-suggestions">
        <h4>Gợi ý câu hỏi:</h4>
        <div class="suggestion-buttons">
            <button class="suggestion-btn" onclick="askQuestion('Khi nào công ty mở cửa?')">Giờ làm việc</button>
            <button class="suggestion-btn" onclick="askQuestion('Làm sao đổi mật khẩu?')">Đổi mật khẩu</button>
            <button class="suggestion-btn" onclick="askQuestion('Làm sao liên hệ hỗ trợ?')">Liên hệ hỗ trợ</button>
            <button class="suggestion-btn" onclick="askQuestion('Làm sao liên hệ môi giới?')">Liên hệ môi giới</button>
        </div>
    </div>
</div>

<style>
.chat-container {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.chat-header {
    background: #007bff;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-header h3 {
    margin: 0;
    font-size: 18px;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
}

.status-badge.user {
    background: #28a745;
}

.status-badge.guest {
    background: #ffc107;
    color: #000;
}

.chat-messages {
    height: 400px;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
}

.message {
    margin-bottom: 15px;
    display: flex;
    flex-direction: column;
}

.user-message {
    align-items: flex-end;
}

.bot-message, .admin-message {
    align-items: flex-start;
}

.message-content {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 18px;
    word-wrap: break-word;
}

.user-message .message-content {
    background: #007bff;
    color: white;
}

.bot-message .message-content {
    background: #e9ecef;
    color: #333;
}

.admin-message .message-content {
    background: #28a745;
    color: white;
}

.pending-message .message-content {
    background: #ffc107;
    color: #000;
}

.message-time {
    font-size: 11px;
    color: #6c757d;
    margin-top: 5px;
    padding: 0 15px;
}

.chat-input-container {
    padding: 20px;
    background: white;
    border-top: 1px solid #dee2e6;
}

.chat-form {
    display: flex;
    gap: 10px;
}

.chat-form input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #ced4da;
    border-radius: 25px;
    outline: none;
    font-size: 14px;
}

.chat-form input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.chat-form button {
    width: 45px;
    height: 45px;
    border: none;
    background: #007bff;
    color: white;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
}

.chat-form button:hover {
    background: #0056b3;
}

.chat-form button:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

.chat-suggestions {
    padding: 20px;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.chat-suggestions h4 {
    margin: 0 0 15px 0;
    font-size: 14px;
    color: #6c757d;
}

.suggestion-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.suggestion-btn {
    padding: 8px 15px;
    border: 1px solid #007bff;
    background: white;
    color: #007bff;
    border-radius: 20px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
}

.suggestion-btn:hover {
    background: #007bff;
    color: white;
}

.typing-indicator {
    display: none;
    padding: 10px 15px;
    background: #e9ecef;
    border-radius: 18px;
    max-width: 70%;
    color: #6c757d;
    font-style: italic;
}

@media (max-width: 768px) {
    .chat-container {
        margin: 10px;
        border-radius: 0;
    }

    .suggestion-buttons {
        flex-direction: column;
    }

    .suggestion-btn {
        text-align: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const chatMessages = document.getElementById('chatMessages');
    const sendBtn = document.getElementById('sendBtn');

    // Tạo conversation ID duy nhất
    const conversationId = 'conv_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

    // Kiểm tra user type
    const userType = @auth 'user' @else 'guest' @endauth;

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (!message) return;

        sendMessage(message);
    });

    function sendMessage(message) {
        // Hiển thị tin nhắn user
        addMessage(message, 'user');
        messageInput.value = '';

        // Disable send button
        sendBtn.disabled = true;

        // Hiển thị typing indicator
        showTypingIndicator();

        // Gửi tin nhắn đến API
        fetch('/api/chatbot/answer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: message,
                conversation_id: conversationId,
                user_type: userType
            })
        })
        .then(response => response.json())
        .then(data => {
            hideTypingIndicator();

            if (data.success) {
                const messageType = data.type === 'pending_admin' ? 'pending' : 'bot';
                addMessage(data.response, messageType);

                if (data.needs_admin) {
                    // Hiển thị thông báo đang chờ admin
                    setTimeout(() => {
                        checkForAdminReply();
                    }, 5000); // Check sau 5 giây
                }
            } else {
                addMessage('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại.', 'bot');
            }
        })
        .catch(error => {
            hideTypingIndicator();
            addMessage('Xin lỗi, không thể kết nối đến server. Vui lòng thử lại.', 'bot');
            console.error('Error:', error);
        })
        .finally(() => {
            sendBtn.disabled = false;
        });
    }

    function addMessage(content, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}-message`;

        let senderName = '';
        switch(type) {
            case 'user':
                senderName = 'Bạn';
                break;
            case 'bot':
                senderName = 'Bot';
                break;
            case 'admin':
                senderName = 'Admin';
                break;
            case 'pending':
                senderName = 'Hệ thống';
                break;
        }

        messageDiv.innerHTML = `
            <div class="message-content">
                <strong>${senderName}:</strong> ${content}
            </div>
            <div class="message-time">${new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'})}</div>
        `;

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingIndicator';
        typingDiv.className = 'message bot-message';
        typingDiv.innerHTML = `
            <div class="typing-indicator" style="display: block;">
                Bot đang soạn tin...
            </div>
        `;
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function hideTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    function checkForAdminReply() {
        if (userType === 'guest') return;

        // Kiểm tra xem có tin nhắn mới từ admin không
        fetch(`/api/chat/conversation/${conversationId}/messages`)
            .then(response => response.json())
            .then(messages => {
                const adminMessages = messages.filter(msg =>
                    msg.type === 'admin' && msg.timestamp > (Date.now()/1000 - 30)
                );

                adminMessages.forEach(msg => {
                    addMessage(msg.content, 'admin');
                });
            })
            .catch(error => console.error('Error checking admin reply:', error));
    }

    // Global function cho suggestion buttons
    window.askQuestion = function(question) {
        messageInput.value = question;
        sendMessage(question);
    };
});
</script>

@endsection
