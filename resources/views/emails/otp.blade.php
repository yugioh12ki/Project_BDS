<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã OTP - Đặt lại mật khẩu</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        .otp-section {
            text-align: center;
            margin: 30px 0;
            padding: 25px;
            background: #f8f9fa;
            border: 2px dashed #007bff;
            border-radius: 10px;
        }

        .otp-label {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .otp-validity {
            font-size: 14px;
            color: #dc3545;
            font-weight: 500;
        }

        .instructions {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }

        .instructions h3 {
            margin: 0 0 10px 0;
            color: #1976d2;
            font-size: 16px;
        }

        .instructions ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .instructions li {
            margin: 5px 0;
            color: #555;
        }

        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }

        .warning h4 {
            margin: 0 0 8px 0;
            color: #856404;
            font-size: 16px;
        }

        .warning p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }

        .footer {
            background: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }

        .footer p {
            margin: 5px 0;
            color: #6c757d;
            font-size: 14px;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .support-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }

            .header {
                padding: 20px 15px;
            }

            .header h1 {
                font-size: 20px;
            }

            .content {
                padding: 25px 20px;
            }

            .otp-code {
                font-size: 28px;
                letter-spacing: 4px;
            }

            .footer {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔐 Mã OTP Đặt Lại Mật Khẩu</h1>
            <p>BDS Platform - Nền tảng Bất Động Sản</p>
        </div>

        <div class="content">
            <div class="greeting">
                Xin chào <strong>{{ $user->Name }}</strong>,
            </div>

            <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Để tiếp tục, vui lòng sử dụng mã OTP bên dưới:</p>

            <div class="otp-section">
                <div class="otp-label">Mã OTP của bạn là:</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-validity">⏰ Có hiệu lực trong 15 phút</div>
            </div>

            <div class="instructions">
                <h3>📋 Hướng dẫn sử dụng:</h3>
                <ul>
                    <li>Nhập mã OTP này vào trang xác thực</li>
                    <li>Mã chỉ có hiệu lực trong <strong>15 phút</strong></li>
                    <li>Chỉ sử dụng được <strong>một lần</strong></li>
                    <li>Không chia sẻ mã này với bất kỳ ai</li>
                </ul>
            </div>

            <div class="warning">
                <h4>⚠️ Lưu ý bảo mật:</h4>
                <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này và liên hệ với chúng tôi ngay lập tức. Tài khoản của bạn vẫn an toàn và không có thay đổi nào được thực hiện.</p>
            </div>

            <p>Nếu bạn gặp khó khăn trong việc đặt lại mật khẩu, đừng ngần ngại liên hệ với đội ngũ hỗ trợ của chúng tôi.</p>

            <p style="margin-top: 30px;">
                Trân trọng,<br>
                <strong>Đội ngũ BDS Platform</strong>
            </p>
        </div>

        <div class="footer">
            <p><strong>BDS Platform - Nền tảng Bất Động Sản Hàng Đầu</strong></p>
            <p>📧 Email: support@bdsplatform.com | 📞 Hotline: 1900-xxxx</p>
            <p>🌐 Website: <a href="https://bdsplatform.com">bdsplatform.com</a></p>

            <div class="support-info">
                <p><strong>Cần hỗ trợ?</strong></p>
                <p>Liên hệ với chúng tôi qua email hoặc hotline bất kỳ lúc nào!</p>
            </div>
        </div>
    </div>
</body>
</html>
