@extends('_layout.app')

@section('login')
<main class="main-content">
    <div class="login-container">
        <div class="image-section">
            <div class="welcome-content">
                <h2>Xác thực OTP</h2>
                <p>Vui lòng nhập mã OTP 6 chữ số đã được gửi tới email của bạn</p>
                <div class="feature-list">
                    <div class="feature">
                        <i class="fas fa-envelope"></i>
                        <span>Kiểm tra email</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-stopwatch"></i>
                        <span>Có hiệu lực 15 phút</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-redo"></i>
                        <span>Có thể gửi lại</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <form class="login-form" method="POST" action="{{ route('password.verify-otp') }}">
                @csrf                <h1 class="login-title">Nhập mã OTP</h1>
                <p class="subtitle">
                    Mã OTP đã được gửi
                    @if(isset($method) && $method === 'sms')
                        tới số điện thoại: <strong>{{ substr($user->Phone, 0, 3) }}****{{ substr($user->Phone, -3) }}</strong>
                    @else
                        tới email: <strong>{{ $email }}</strong>
                    @endif
                </p>

                @include('_partials.alerts')

                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label for="otp">Mã OTP <span style="color: red;">*</span></label>
                    <input type="text" id="otp" name="otp" class="otp-input"
                           placeholder="Nhập 6 chữ số"
                           value="{{ old('otp') }}"
                           maxlength="6"
                           pattern="[0-9]{6}"
                           required>
                    <small class="form-text">
                        <i class="fas fa-info-circle"></i>
                        Mã OTP có hiệu lực trong 15 phút
                    </small>
                </div>

                <div class="countdown-timer" id="countdown">
                    <i class="fas fa-hourglass-half"></i>
                    <span>Thời gian còn lại: <span id="timer">15:00</span></span>
                </div>

                <div class="button-group">
                    <button type="submit">
                        <i class="fas fa-check"></i>
                        Xác thực OTP
                    </button>
                    <button type="button" class="register" onclick="resendOtp()">
                        <i class="fas fa-redo"></i>
                        Gửi lại mã
                    </button>
                </div>

                <div class="additional-links">
                    <a href="{{ route('password.request') }}" class="forgot">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại nhập email
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
.otp-input {
    text-align: center;
    font-size: 1.5rem;
    font-weight: bold;
    letter-spacing: 0.5rem;
    font-family: 'Courier New', monospace;
}

.countdown-timer {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px;
    text-align: center;
    margin: 15px 0;
    color: #495057;
}

.countdown-timer.expired {
    background: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
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

.alert {
    margin-bottom: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Countdown timer
    let timeLeft = 15 * 60; // 15 minutes in seconds
    const timerElement = document.getElementById('timer');
    const countdownElement = document.getElementById('countdown');

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

        if (timeLeft <= 0) {
            countdownElement.classList.add('expired');
            timerElement.textContent = 'Đã hết hạn';
            return;
        }

        timeLeft--;
    }

    // Update timer every second
    updateTimer();
    const interval = setInterval(updateTimer, 1000);

    // Stop timer when form is submitted or page is unloaded
    window.addEventListener('beforeunload', () => clearInterval(interval));

    // Auto-format OTP input
    const otpInput = document.getElementById('otp');
    otpInput.addEventListener('input', function(e) {
        // Remove non-digits
        this.value = this.value.replace(/[^0-9]/g, '');

        // Auto-submit when 6 digits entered
        if (this.value.length === 6) {
            // Optional: auto-submit form
            // this.form.submit();
        }
    });

    // Prevent paste of non-numeric content
    otpInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        const numericPaste = paste.replace(/[^0-9]/g, '').substring(0, 6);
        this.value = numericPaste;
    });
});

function resendOtp() {
    const email = '{{ $email }}';
    const method = '{{ $method ?? "email" }}';
    const button = event.target;
    const originalText = button.innerHTML;

    // Disable button
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const token = csrfToken ? csrfToken.getAttribute('content') : '{{ csrf_token() }}';

    fetch('{{ route("password.resend-otp") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email: email, method: method })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', data.success);

            // Reset countdown
            if (typeof timeLeft !== 'undefined') {
                timeLeft = 15 * 60;
                const countdownElement = document.getElementById('countdown');
                if (countdownElement) {
                    countdownElement.classList.remove('expired');
                }
            }
        } else {
            showAlert('error', data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Có lỗi xảy ra khi gửi lại mã OTP. Vui lòng thử lại.');
    })
    .finally(() => {
        // Re-enable button after 30 seconds
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        }, 30000);
    });
}

function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;

    // Insert after form title
    const subtitle = document.querySelector('.subtitle');
    subtitle.parentNode.insertBefore(alert, subtitle.nextSibling);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>

@endsection
