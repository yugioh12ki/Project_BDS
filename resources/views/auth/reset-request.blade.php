@extends('_layout.app')

@section('login')
<main class="main-content">
    <div class="login-container">
        <div class="image-section">
            <div class="welcome-content">
                <h2>Quên mật khẩu?</h2>
                <p>Đừng lo lắng! Chúng tôi sẽ giúp bạn khôi phục tài khoản</p>
                <div class="feature-list">
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>Bảo mật cao</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-clock"></i>
                        <span>Nhanh chóng</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-lock"></i>
                        <span>An toàn tuyệt đối</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <form class="login-form" method="POST" action="{{ route('password.send-otp-email') }}" id="reset-form">
                @csrf
                <h1 class="login-title">Đặt lại mật khẩu</h1>
                <p class="subtitle">Chọn phương thức nhận mã OTP khôi phục mật khẩu</p>

                @include('_partials.alerts')

                <!-- Tab chọn phương thức -->
                <div class="method-tabs">
                    <button type="button" class="tab-btn active" data-tab="email">
                        <i class="fas fa-envelope"></i>
                        Qua Email
                    </button>
                    <button type="button" class="tab-btn disabled" data-tab="sms" disabled>
                        <i class="fas fa-sms"></i>
                        Qua SMS
                        <small>(Sắp có)</small>
                    </button>
                </div>

                <!-- Input Fields -->
                <div class="tab-content active" id="email-tab">
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email"
                               placeholder="Nhập địa chỉ email của bạn"
                               value="{{ old('email') }}" required>
                        <small class="form-text">Chúng tôi sẽ gửi mã OTP 6 chữ số tới email này</small>
                    </div>
                </div>

                <div class="tab-content" id="sms-tab" style="display: none;">
                    <div class="form-group">
                        <label for="phone">Số điện thoại <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone"
                               placeholder="Tính năng này sẽ sớm được triển khai"
                               value="{{ old('phone') }}" pattern="[0-9]{10,11}" disabled>
                        <small class="form-text">Chức năng gửi OTP qua SMS đang được phát triển</small>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" id="submit-btn">
                        <i class="fas fa-paper-plane"></i>
                        <span id="submit-text">Gửi mã OTP</span>
                    </button>
                    <button type="button" class="register" onclick="window.location.href='{{ route('login') }}'">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại đăng nhập
                    </button>
                </div>

                <div class="additional-links">
                    <a href="{{ route('register') }}" class="forgot">
                        <i class="fas fa-user-plus"></i>
                        Chưa có tài khoản? Đăng ký ngay
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
.required { color: #dc3545; }

.method-tabs {
    display: flex;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
}

.tab-btn {
    flex: 1;
    padding: 12px 20px;
    border: none;
    background: #f8f9fa;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-weight: 500;
}

.tab-btn:hover {
    background: #e9ecef;
    color: #495057;
}

.tab-btn.active {
    background: #007bff;
    color: white;
}

.tab-btn.disabled {
    background: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
    opacity: 0.6;
    position: relative;
}

.tab-btn.disabled:hover {
    background: #f8f9fa;
    color: #6c757d;
}

.tab-btn.disabled small {
    font-size: 0.75rem;
    margin-left: 5px;
    color: #dc3545;
    font-weight: normal;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #495057;
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.form-text {
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}

.additional-links {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.additional-links a {
    color: #007bff;
    text-decoration: none;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.additional-links a:hover {
    text-decoration: underline;
}

.button-group button i {
    margin-right: 8px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reset-form');
    const submitText = document.getElementById('submit-text');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Ignore clicks on disabled tabs
            if (this.disabled || this.classList.contains('disabled')) {
                return;
            }

            const method = this.dataset.tab;
            switchTab(method);
        });
    });

    // Phone number formatting
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        e.target.value = value;
    });

    function switchTab(method) {
        // Switch tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-tab="${method}"]`).classList.add('active');

        // Switch tab content
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.getElementById(method + '-tab').classList.add('active');

        // Update form action and validation
        if (method === 'email') {
            form.action = '{{ route("password.send-otp-email") }}';
            submitText.textContent = 'Gửi mã OTP qua Email';
            emailInput.required = true;
            phoneInput.required = false;
        } else {
            form.action = '{{ route("password.send-otp-sms") }}';
            submitText.textContent = 'Gửi mã OTP qua SMS';
            emailInput.required = false;
            phoneInput.required = true;
        }
    }
});
</script>

@endsection
