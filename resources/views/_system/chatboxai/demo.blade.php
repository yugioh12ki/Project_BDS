<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Demo Chatbot System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .status-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .status-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .status-card h3 {
            margin-top: 0;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .status-badge.guest {
            background: #ffc107;
            color: #000;
        }

        .status-badge.user {
            background: #28a745;
            color: white;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .feature-list li:last-child {
            border-bottom: none;
        }

        .feature-list li i {
            color: #28a745;
            margin-right: 10px;
        }

        .demo-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .demo-link {
            display: block;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            text-align: center;
        }

        .demo-link:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            text-decoration: none;
            color: #007bff;
        }

        .demo-link i {
            font-size: 48px;
            color: #007bff;
            margin-bottom: 15px;
        }

        .demo-link h3 {
            margin: 0 0 10px 0;
        }

        .demo-link p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .instructions {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .instructions h2 {
            color: #007bff;
            margin-top: 0;
        }

        .step {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .step h4 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .widget-demo {
            position: relative;
            min-height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            padding: 30px;
            color: white;
            text-align: center;
            margin-top: 20px;
        }

        .widget-demo h3 {
            margin: 0 0 15px 0;
        }

        .widget-demo p {
            margin: 0;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-robot"></i> Demo Hệ thống Chatbot BDS</h1>
            <p>Hệ thống chatbot hỗ trợ khách hàng với AI và admin</p>
        </div>

        <div class="status-info">
            <div class="status-card">
                <h3>Trạng thái hiện tại</h3>
                @auth
                    <span class="status-badge user">Đã đăng nhập</span>
                    <p><strong>Tên:</strong> {{ Auth::user()->Name ?? 'User' }}</p>
                    <p><strong>Quyền:</strong> {{ Auth::user()->Role ?? 'User' }}</p>
                @else
                    <span class="status-badge guest">Khách</span>
                    <p>Bạn đang truy cập với tư cách khách</p>
                @endauth
            </div>

            <div class="status-card">
                <h3>Tính năng chatbot</h3>
                <ul class="feature-list">
                    <li><i class="fas fa-check"></i> AI trả lời tự động từ FAQ</li>
                    <li><i class="fas fa-check"></i> Chuyển sang admin khi cần</li>
                    <li><i class="fas fa-check"></i> Real-time chat với Firebase</li>
                    <li><i class="fas fa-check"></i> Responsive design</li>
                    <li><i class="fas fa-check"></i> Widget có thể embed</li>
                </ul>
            </div>

            <div class="status-card">
                <h3>Logic hệ thống</h3>
                <ul class="feature-list">
                    <li><i class="fas fa-user"></i> <strong>Guest:</strong> Chỉ dùng AI bot</li>
                    <li><i class="fas fa-user-check"></i> <strong>User:</strong> AI bot + admin support</li>
                    <li><i class="fas fa-user-cog"></i> <strong>Admin:</strong> Quản lý conversations</li>
                </ul>
            </div>
        </div>

        <div class="demo-links">
            <a href="/chat" class="demo-link">
                <i class="fas fa-comments"></i>
                <h3>Chat Interface</h3>
                <p>Giao diện chat đầy đủ cho user</p>
            </a>

            @auth
                @if(Auth::user()->Role === 'Admin')
                <a href="/admin/chatbot/admin" class="demo-link">
                    <i class="fas fa-cogs"></i>
                    <h3>Admin Panel</h3>
                    <p>Quản lý conversations và trả lời user</p>
                </a>
                @endif
            @endauth

            <a href="/test/gemini" class="demo-link">
                <i class="fas fa-flask"></i>
                <h3>AI Test Suite</h3>
                <p>Test Gemini AI integration và performance</p>
            </a>

            <a href="#widget-demo" class="demo-link">
                <i class="fas fa-puzzle-piece"></i>
                <h3>Widget Demo</h3>
                <p>Xem widget chatbot ở góc dưới phải</p>
            </a>

            <a href="/api/chat/pending-conversations" class="demo-link" target="_blank">
                <i class="fas fa-database"></i>
                <h3>API Test</h3>
                <p>Test API lấy conversations cần hỗ trợ</p>
            </a>
        </div>

        <div class="instructions">
            <h2><i class="fas fa-list-ol"></i> Hướng dẫn test hệ thống</h2>

            <div class="step">
                <h4>1. Test AI Bot</h4>
                <p>Thử hỏi các câu hỏi như: "Khi nào công ty mở cửa?", "Làm sao đổi mật khẩu?", "Làm sao liên hệ hỗ trợ?"</p>
            </div>

            <div class="step">
                <h4>2. Test Guest Mode</h4>
                <p>Đăng xuất và thử hỏi câu hỏi không có trong FAQ. Hệ thống sẽ không chuyển sang admin.</p>
            </div>

            <div class="step">
                <h4>3. Test User Mode</h4>
                <p>Đăng nhập và hỏi câu hỏi không có trong FAQ. Hệ thống sẽ chuyển sang admin hỗ trợ.</p>
            </div>

            <div class="step">
                <h4>4. Test Admin Panel</h4>
                <p>Nếu bạn là admin, vào Admin Panel để xem và trả lời các conversations cần hỗ trợ.</p>
            </div>

            <div class="widget-demo" id="widget-demo">
                <h3><i class="fas fa-arrow-down"></i> Widget Demo</h3>
                <p>Chatbot widget đang hoạt động ở góc dưới bên phải. Click vào icon để mở chat!</p>
            </div>
        </div>
    </div>

    <!-- Include chatbot widget -->
    @include('_system.chatboxai.widget')

    <script>
        // Add some interactive features
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html>
