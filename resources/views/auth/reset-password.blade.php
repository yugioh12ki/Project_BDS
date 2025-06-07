@extends('_layout.app')

@section('login')
<main class="main-content">
    <div class="login-container">
        <div class="image-section">
            <div class="welcome-content">
                <h2>Đặt lại mật khẩu</h2>
                <p>Tạo mật khẩu mới mạnh mẽ để bảo vệ tài khoản của bạn</p>
                <div class="feature-list">
                    <div class="feature">
                        <i class="fas fa-key"></i>
                        <span>Mật khẩu mới</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>Bảo mật tối đa</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-check-double"></i>
                        <span>Xác thực 2 lần</span>
                    </div>
                </div>
            </div>
        </div>        <div class="form-section">
            <form class="login-form" method="POST" action="{{ route('password.password-reset') }}">
                @csrf                <h1 class="login-title">Tạo mật khẩu mới</h1>
                <p class="subtitle">Nhập mật khẩu mới cho tài khoản: <strong>{{ $email }}</strong></p>

                @include('_partials.alerts')

                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="password">Mật khẩu mới <span style="color: red;">*</span></label>
                    <div class="password-input-container">
                        <input type="password" id="password" name="password"
                               placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)"
                               required minlength="6">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                        <div class="strength-text" id="strength-text">Nhập mật khẩu để kiểm tra độ mạnh</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu <span style="color: red;">*</span></label>
                    <div class="password-input-container">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="Nhập lại mật khẩu mới"
                               required minlength="6">
                        <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye" id="password_confirmation-eye"></i>
                        </button>
                    </div>
                    <div class="password-match" id="password-match"></div>
                </div>

                <div class="password-requirements">
                    <h4><i class="fas fa-info-circle"></i> Yêu cầu mật khẩu:</h4>
                    <ul id="requirements-list">
                        <li id="req-length"><i class="fas fa-times"></i> Ít nhất 6 ký tự</li>
                        <li id="req-letter"><i class="fas fa-times"></i> Có chữ cái</li>
                        <li id="req-number"><i class="fas fa-times"></i> Có số</li>
                        <li id="req-special"><i class="fas fa-times"></i> Có ký tự đặc biệt</li>
                    </ul>
                </div>

                <div class="button-group">
                    <button type="submit" id="submit-btn" disabled>
                        <i class="fas fa-save"></i>
                        Đặt lại mật khẩu
                    </button>
                    <button type="button" class="register" onclick="window.location.href='{{ route('login') }}'">
                        <i class="fas fa-times"></i>
                        Hủy bỏ
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
.password-input-container {
    position: relative;
}

.password-input-container input {
    padding-right: 50px;
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 5px;
}

.toggle-password:hover {
    color: #007bff;
}

.password-strength {
    margin-top: 8px;
}

.strength-bar {
    width: 100%;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.strength-fill {
    height: 100%;
    transition: all 0.3s ease;
    border-radius: 3px;
}

.strength-text {
    font-size: 0.875rem;
    margin-top: 4px;
    font-weight: 500;
}

.password-match {
    margin-top: 8px;
    font-size: 0.875rem;
    font-weight: 500;
}

.password-requirements {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin: 20px 0;
}

.password-requirements h4 {
    margin: 0 0 10px 0;
    color: #495057;
    font-size: 0.9rem;
    font-weight: 600;
}

.password-requirements ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.password-requirements li {
    padding: 4px 0;
    font-size: 0.875rem;
    color: #6c757d;
}

.password-requirements li.valid {
    color: #28a745;
}

.password-requirements li.valid i {
    color: #28a745;
}

.password-requirements li i {
    width: 16px;
    margin-right: 8px;
}

.button-group button i {
    margin-right: 8px;
}

button[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Strength levels */
.strength-weak {
    background: #dc3545;
    width: 25%;
}

.strength-fair {
    background: #fd7e14;
    width: 50%;
}

.strength-good {
    background: #ffc107;
    width: 75%;
}

.strength-strong {
    background: #28a745;
    width: 100%;
}

.text-weak { color: #dc3545; }
.text-fair { color: #fd7e14; }
.text-good { color: #ffc107; }
.text-strong { color: #28a745; }
.text-match { color: #28a745; }
.text-mismatch { color: #dc3545; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submit-btn');

    passwordInput.addEventListener('input', checkPassword);
    confirmInput.addEventListener('input', checkPasswordMatch);

    function checkPassword() {
        const password = passwordInput.value;
        checkPasswordStrength(password);
        checkPasswordRequirements(password);
        checkPasswordMatch();
        updateSubmitButton();
    }

    function checkPasswordStrength(password) {
        const strengthFill = document.getElementById('strength-fill');
        const strengthText = document.getElementById('strength-text');

        let strength = 0;
        if (password.length >= 6) strength++;
        if (password.match(/[a-zA-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;

        // Remove existing classes
        strengthFill.className = 'strength-fill';
        strengthText.className = 'strength-text';

        if (password.length === 0) {
            strengthText.textContent = 'Nhập mật khẩu để kiểm tra độ mạnh';
        } else if (strength <= 1) {
            strengthFill.classList.add('strength-weak');
            strengthText.classList.add('text-weak');
            strengthText.textContent = 'Mật khẩu yếu';
        } else if (strength === 2) {
            strengthFill.classList.add('strength-fair');
            strengthText.classList.add('text-fair');
            strengthText.textContent = 'Mật khẩu trung bình';
        } else if (strength === 3) {
            strengthFill.classList.add('strength-good');
            strengthText.classList.add('text-good');
            strengthText.textContent = 'Mật khẩu tốt';
        } else {
            strengthFill.classList.add('strength-strong');
            strengthText.classList.add('text-strong');
            strengthText.textContent = 'Mật khẩu mạnh';
        }
    }

    function checkPasswordRequirements(password) {
        const requirements = {
            'req-length': password.length >= 6,
            'req-letter': /[a-zA-Z]/.test(password),
            'req-number': /[0-9]/.test(password),
            'req-special': /[^a-zA-Z0-9]/.test(password)
        };

        Object.keys(requirements).forEach(reqId => {
            const element = document.getElementById(reqId);
            const icon = element.querySelector('i');

            if (requirements[reqId]) {
                element.classList.add('valid');
                icon.className = 'fas fa-check';
            } else {
                element.classList.remove('valid');
                icon.className = 'fas fa-times';
            }
        });
    }

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        const matchDiv = document.getElementById('password-match');

        if (confirm.length === 0) {
            matchDiv.textContent = '';
            return;
        }

        if (password === confirm) {
            matchDiv.textContent = '✓ Mật khẩu khớp';
            matchDiv.className = 'password-match text-match';
        } else {
            matchDiv.textContent = '✗ Mật khẩu không khớp';
            matchDiv.className = 'password-match text-mismatch';
        }
    }

    function updateSubmitButton() {
        const password = passwordInput.value;
        const confirm = confirmInput.value;

        const isValidLength = password.length >= 6;
        const isMatching = password === confirm && confirm.length > 0;

        submitBtn.disabled = !(isValidLength && isMatching);
    }
});

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const eye = document.getElementById(inputId + '-eye');

    if (input.type === 'password') {
        input.type = 'text';
        eye.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        eye.className = 'fas fa-eye';
    }
}
</script>

@endsection
