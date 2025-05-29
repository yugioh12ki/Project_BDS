<!-- Chatbot Widget -->
<div id="chatbotWidget" class="chatbot-widget">
    <div id="chatbotToggle" class="chatbot-toggle">
        <i class="fa fa-comment"></i>
        <span class="chat-notification" id="chatNotification" style="display: none;">1</span>
    </div>

    <div id="chatbotWindow" class="chatbot-window" style="display: none;">
        <div class="chatbot-header">
            <h4>Hỗ trợ khách hàng</h4>
            <button id="closeChatbot" class="close-chatbot">&times;</button>
        </div>

        <div id="chatbotMessages" class="chatbot-messages">
            <div class="message bot-message">
                <div class="message-content">
                    <strong>Bot:</strong> Xin chào! Tôi có thể giúp gì cho bạn?
                </div>
            </div>
        </div>

        <div class="chatbot-input">
            <form id="chatbotForm">
                <input type="text" id="chatbotInput" placeholder="Nhập câu hỏi..." required>
                <button type="submit"><i class="fa fa-paper-plane"></i></button>
            </form>
        </div>

        <div class="chatbot-suggestions">
            <button class="suggestion-btn" onclick="sendQuickMessage('Khi nào công ty mở cửa?')">Giờ làm việc</button>
            <button class="suggestion-btn" onclick="sendQuickMessage('Làm sao đổi mật khẩu?')">Đổi mật khẩu</button>
            <button class="suggestion-btn" onclick="sendQuickMessage('Làm sao liên hệ hỗ trợ?')">Liên hệ hỗ trợ</button>
        </div>
    </div>
</div>

<style>
.chatbot-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.chatbot-toggle {
    width: 60px;
    height: 60px;
    background: #007bff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    transition: all 0.3s ease;
    position: relative;
}

.chatbot-toggle:hover {
    background: #0056b3;
    transform: scale(1.1);
}

.chatbot-toggle i {
    color: white;
    font-size: 24px;
}

.chat-notification {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.chatbot-header {
    background: #007bff;
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header h4 {
    margin: 0;
    font-size: 16px;
}

.close-chatbot {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    padding: 0;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f8f9fa;
}

.message {
    margin-bottom: 15px;
}

.message-content {
    padding: 10px 15px;
    border-radius: 18px;
    max-width: 85%;
    word-wrap: break-word;
    font-size: 14px;
    line-height: 1.4;
}

.bot-message .message-content {
    background: #e9ecef;
    color: #333;
}

.user-message {
    display: flex;
    justify-content: flex-end;
}

.user-message .message-content {
    background: #007bff;
    color: white;
}

.admin-message .message-content {
    background: #28a745;
    color: white;
}

.pending-message .message-content {
    background: #ffc107;
    color: #000;
}

.chatbot-input {
    padding: 15px;
    border-top: 1px solid #dee2e6;
}

.chatbot-input form {
    display: flex;
    gap: 10px;
}

.chatbot-input input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #ced4da;
    border-radius: 20px;
    outline: none;
    font-size: 14px;
}

.chatbot-input input:focus {
    border-color: #007bff;
}

.chatbot-input button {
    width: 40px;
    height: 40px;
    border: none;
    background: #007bff;
    color: white;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chatbot-input button:hover {
    background: #0056b3;
}

.chatbot-suggestions {
    padding: 10px 15px;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.suggestion-btn {
    display: inline-block;
    margin: 3px;
    padding: 5px 10px;
    border: 1px solid #007bff;
    background: white;
    color: #007bff;
    border-radius: 15px;
    cursor: pointer;
    font-size: 11px;
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
    max-width: 85%;
    color: #6c757d;
    font-style: italic;
    font-size: 14px;
}

@media (max-width: 768px) {
    .chatbot-widget {
        bottom: 10px;
        right: 10px;
    }

    .chatbot-window {
        width: 280px;
        height: 400px;
        bottom: 70px;
    }
}

/* Animation for widget */
.chatbot-window.show {
    animation: slideUp 0.3s ease-out;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatbotToggle = document.getElementById('chatbotToggle');
    const chatbotWindow = document.getElementById('chatbotWindow');
    const closeChatbot = document.getElementById('closeChatbot');
    const chatbotForm = document.getElementById('chatbotForm');
    const chatbotInput = document.getElementById('chatbotInput');
    const chatbotMessages = document.getElementById('chatbotMessages');

    // Tạo conversation ID duy nhất
    const conversationId = 'widget_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

    // Kiểm tra user type
    const userType = @auth 'user' @else 'guest' @endauth;

    let isOpen = false;

    // Toggle chatbot
    chatbotToggle.addEventListener('click', function() {
        if (isOpen) {
            closeChatbotWindow();
        } else {
            openChatbotWindow();
        }
    });

    // Close chatbot
    closeChatbot.addEventListener('click', function() {
        closeChatbotWindow();
    });

    // Send message
    chatbotForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = chatbotInput.value.trim();
        if (!message) return;

        sendMessage(message);
    });

    function openChatbotWindow() {
        chatbotWindow.style.display = 'flex';
        chatbotWindow.classList.add('show');
        isOpen = true;

        // Hide notification
        document.getElementById('chatNotification').style.display = 'none';
    }

    function closeChatbotWindow() {
        chatbotWindow.style.display = 'none';
        chatbotWindow.classList.remove('show');
        isOpen = false;
    }

    function sendMessage(message) {
        // Hiển thị tin nhắn user
        addMessage(message, 'user');
        chatbotInput.value = '';

        // Hiển thị typing indicator
        showTypingIndicator();

        // Gửi tin nhắn đến API
        fetch('/api/chatbot/answer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                    // Nếu cần admin, có thể kiểm tra định kỳ cho reply
                    setTimeout(() => {
                        checkForAdminReply();
                    }, 10000); // Check sau 10 giây
                }
            } else {
                addMessage('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại.', 'bot');
            }
        })
        .catch(error => {
            hideTypingIndicator();
            addMessage('Xin lỗi, không thể kết nối đến server.', 'bot');
            console.error('Error:', error);
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
        `;

        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
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
        chatbotMessages.appendChild(typingDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
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
                    msg.type === 'admin' && msg.timestamp > (Date.now()/1000 - 60)
                );

                adminMessages.forEach(msg => {
                    addMessage(msg.content, 'admin');

                    // Show notification if chatbot is closed
                    if (!isOpen) {
                        document.getElementById('chatNotification').style.display = 'flex';
                    }
                });
            })
            .catch(error => console.error('Error checking admin reply:', error));
    }

    // Global function cho suggestion buttons
    window.sendQuickMessage = function(message) {
        sendMessage(message);
    };

    // Kiểm tra tin nhắn admin mỗi 30 giây nếu user đã đăng nhập
    if (userType === 'user') {
        setInterval(checkForAdminReply, 30000);
    }
});
</script>
