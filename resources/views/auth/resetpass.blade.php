@extends('_layout.app')

@section('login')
<main class="main-content">
    <div class="login-container">
        <div class="image-section">
            <div class="welcome-content">
                <h2>Khôi phục tài khoản</h2>
                <p>Chọn phương thức khôi phục mật khẩu phù hợp với bạn</p>
                <div class="feature-list">
                    <div class="feature">
                        <i class="fas fa-envelope"></i>
                        <span>Qua Email</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Qua SMS</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>Bảo mật cao</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="login-form">
                <h1 class="login-title">Khôi phục mật khẩu</h1>
                <p class="subtitle">Chọn phương thức nhận mã OTP để đặt lại mật khẩu</p>

                <div class="reset-options">
                    <div class="reset-option" onclick="selectResetMethod('email')">
                        <div class="option-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="option-content">
                            <h3>Qua Email</h3>
                            <p>Nhận mã OTP qua địa chỉ email đã đăng ký</p>
                            <span class="option-time">⏱️ Nhận trong 1-2 phút</span>
                        </div>
                        <div class="option-check">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>

                    <div class="reset-option" onclick="selectResetMethod('phone')">
                        <div class="option-icon">
                            <i class="fas fa-sms"></i>
                        </div>
                        <div class="option-content">
                            <h3>Qua SMS</h3>
                            <p>Nhận mã OTP qua số điện thoại đã đăng ký</p>
                            <span class="option-time">⏱️ Nhận trong 30 giây</span>
                        </div>
                        <div class="option-check">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>

                <div class="selected-method" id="selected-method" style="display: none;">
                    <div class="method-info">
                        <i class="fas fa-info-circle"></i>
                        <span id="method-text">Đã chọn khôi phục qua Email</span>
                    </div>
                </div>

                <div class="button-group">
                    <button type="button" id="continue-btn" onclick="continueReset()" disabled>
                        <i class="fas fa-arrow-right"></i>
                        Tiếp tục
                    </button>
                    <button type="button" class="register" onclick="window.location.href='{{ route('login') }}'">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại đăng nhập
                    </button>
                </div>

                <div class="additional-info">
                    <div class="info-item">
                        <i class="fas fa-lock"></i>
                        <span>Mã OTP có hiệu lực 15 phút</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Bảo mật bằng mã hóa SSL</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-user-check"></i>
                        <span>Xác thực tài khoản tự động</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.reset-options {
    margin: 25px 0;
}

.reset-option {
    display: flex;
    align-items: center;
    padding: 20px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.reset-option:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1);
    transform: translateY(-2px);
}

.reset-option.selected {
    border-color: #007bff;
    background: #f8f9ff;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.option-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 50%;
    margin-right: 15px;
    font-size: 20px;
    color: #6c757d;
    transition: all 0.3s ease;
}

.reset-option.selected .option-icon {
    background: #007bff;
    color: white;
}

.option-content {
    flex: 1;
}

.option-content h3 {
    margin: 0 0 5px 0;
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.option-content p {
    margin: 0 0 8px 0;
    font-size: 14px;
    color: #6c757d;
    line-height: 1.4;
}

.option-time {
    font-size: 12px;
    color: #28a745;
    font-weight: 500;
}

.option-check {
    width: 24px;
    height: 24px;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: transparent;
    transition: all 0.3s ease;
}

.reset-option.selected .option-check {
    border-color: #007bff;
    background: #007bff;
    color: white;
}

.selected-method {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
}

.method-info {
    display: flex;
    align-items: center;
    color: #155724;
    font-weight: 500;
}

.method-info i {
    margin-right: 10px;
    font-size: 16px;
}

.additional-info {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    font-size: 14px;
    color: #6c757d;
}

.info-item i {
    width: 16px;
    margin-right: 12px;
    color: #007bff;
}

.button-group button i {
    margin-right: 8px;
}

button[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
}

button[disabled]:hover {
    transform: none;
}
</style>

<script>
let selectedMethod = null;

function selectResetMethod(method) {
    // Remove previous selection
    document.querySelectorAll('.reset-option').forEach(option => {
        option.classList.remove('selected');
    });

    // Add selection to clicked option
    event.currentTarget.classList.add('selected');

    selectedMethod = method;

    // Show selected method info
    const selectedMethodDiv = document.getElementById('selected-method');
    const methodText = document.getElementById('method-text');
    const continueBtn = document.getElementById('continue-btn');

    if (method === 'email') {
        methodText.textContent = 'Đã chọn khôi phục qua Email';
    } else if (method === 'phone') {
        methodText.textContent = 'Đã chọn khôi phục qua SMS';
    }

    selectedMethodDiv.style.display = 'block';
    continueBtn.disabled = false;

    // Add animation
    selectedMethodDiv.style.opacity = '0';
    selectedMethodDiv.style.transform = 'translateY(-10px)';

    setTimeout(() => {
        selectedMethodDiv.style.transition = 'all 0.3s ease';
        selectedMethodDiv.style.opacity = '1';
        selectedMethodDiv.style.transform = 'translateY(0)';
    }, 10);
}

function continueReset() {
    if (!selectedMethod) return;

    if (selectedMethod === 'email') {
        window.location.href = '{{ route("password.request") }}';
    } else if (selectedMethod === 'phone') {
        // For future implementation
        alert('Chức năng khôi phục qua SMS sẽ sớm được triển khai!');
    }
}

// Add keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && selectedMethod) {
        continueReset();
    }
});
</script>

@endsection
