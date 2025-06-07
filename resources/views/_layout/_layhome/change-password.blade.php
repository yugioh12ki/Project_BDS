@extends('_layout.app')

@section('title', 'Đổi mật khẩu')

@section('profilecustomer')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <!-- Header -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-gold-light rounded-circle p-3 mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-key text-gold" style="font-size: 2rem;"></i>
                </div>
                <h2 class="text-gold mb-2">Đổi mật khẩu</h2>
                <p class="text-muted">Bảo mật tài khoản của bạn với mật khẩu mạnh</p>
            </div>

            <!-- Change Password Form -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient text-white text-center py-4" style="background: linear-gradient(135deg, #b8860b, #daa520);">
                    <h5 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>Cập nhật mật khẩu
                    </h5>
                </div>
                <div class="card-body p-5">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('customer.updatePassword') }}" id="changePasswordForm">
                        @csrf
                        
                        <!-- Current Password -->
                        <div class="form-group mb-4">
                            <label for="current_password" class="form-label fw-bold">
                                <i class="fas fa-lock text-gold me-2"></i>Mật khẩu hiện tại <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-gold">
                                    <i class="fas fa-key text-gold"></i>
                                </span>
                                <input id="current_password" type="password" 
                                    class="form-control border-gold @error('current_password') is-invalid @enderror" 
                                    name="current_password" required autocomplete="current-password"
                                    placeholder="Nhập mật khẩu hiện tại">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye" id="current_password_eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="form-group mb-4">
                            <label for="password" class="form-label fw-bold">
                                <i class="fas fa-lock text-gold me-2"></i>Mật khẩu mới <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-gold">
                                    <i class="fas fa-key text-gold"></i>
                                </span>
                                <input id="password" type="password" 
                                    class="form-control border-gold @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="new-password"
                                    placeholder="Nhập mật khẩu mới (ít nhất 8 ký tự)"
                                    onkeyup="checkPasswordStrength()">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password_eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Password Strength Indicator -->
                            <div class="password-strength mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Độ mạnh mật khẩu:</small>
                                    <span id="strength-text" class="badge bg-secondary">Chưa nhập</span>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div id="strength-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                                <div class="password-requirements mt-2">
                                    <small class="text-muted">
                                        <div id="req-length" class="requirement">
                                            <i class="fas fa-times text-danger me-1"></i>Ít nhất 8 ký tự
                                        </div>
                                        <div id="req-letter" class="requirement">
                                            <i class="fas fa-times text-danger me-1"></i>Có chữ cái
                                        </div>
                                        <div id="req-case" class="requirement">
                                            <i class="fas fa-times text-danger me-1"></i>Có chữ hoa và chữ thường
                                        </div>
                                        <div id="req-number" class="requirement">
                                            <i class="fas fa-times text-danger me-1"></i>Có số
                                        </div>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">
                                <i class="fas fa-lock text-gold me-2"></i>Xác nhận mật khẩu <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-gold">
                                    <i class="fas fa-key text-gold"></i>
                                </span>
                                <input id="password_confirmation" type="password" 
                                    class="form-control border-gold" 
                                    name="password_confirmation" required autocomplete="new-password"
                                    placeholder="Nhập lại mật khẩu mới"
                                    onkeyup="checkPasswordMatch()">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="password_confirmation_eye"></i>
                                </button>
                            </div>
                            <div id="password-match" class="mt-2"></div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <a href="{{ route('customer.profile') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại
                            </a>
                            <button type="submit" class="btn btn-gold btn-lg px-4" id="submitBtn" disabled>
                                <i class="fas fa-save me-2"></i>Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="text-gold mb-3">
                        <i class="fas fa-lightbulb me-2"></i>Mẹo bảo mật
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Sử dụng mật khẩu dài ít nhất 8 ký tự</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Kết hợp chữ hoa, chữ thường, số và ký tự đặc biệt</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Không sử dụng thông tin cá nhân dễ đoán</small>
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <small>Thay đổi mật khẩu định kỳ</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-gold {
    border-color: #daa520 !important;
}

.text-gold {
    color: #b8860b !important;
}

.btn-gold {
    background: linear-gradient(135deg, #b8860b, #daa520);
    border: none;
    color: white;
    font-weight: 600;
}

.btn-gold:hover {
    background: linear-gradient(135deg, #9d7109, #b8940e);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(184, 134, 11, 0.3);
}

.bg-gold-light {
    background-color: rgba(218, 165, 32, 0.1);
}

.requirement {
    display: flex;
    align-items: center;
    margin-bottom: 2px;
}

.requirement.valid i {
    color: #28a745 !important;
}

.requirement.valid i:before {
    content: "\f00c";
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border: none;
}

.form-control:focus {
    border-color: #daa520;
    box-shadow: 0 0 0 0.2rem rgba(218, 165, 32, 0.25);
}

.input-group-text {
    background-color: rgba(218, 165, 32, 0.1);
}

.progress {
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar {
    transition: all 0.3s ease;
    border-radius: 10px;
}

.alert {
    border-radius: 10px;
    border: none;
}
</style>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '_eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    
    // Requirements
    const reqLength = document.getElementById('req-length');
    const reqLetter = document.getElementById('req-letter');
    const reqCase = document.getElementById('req-case');
    const reqNumber = document.getElementById('req-number');
    
    let score = 0;
    let requirements = 0;
    
    // Check length
    if (password.length >= 8) {
        reqLength.classList.add('valid');
        reqLength.querySelector('i').classList.remove('fa-times', 'text-danger');
        reqLength.querySelector('i').classList.add('fa-check', 'text-success');
        score += 25;
        requirements++;
    } else {
        reqLength.classList.remove('valid');
        reqLength.querySelector('i').classList.remove('fa-check', 'text-success');
        reqLength.querySelector('i').classList.add('fa-times', 'text-danger');
    }
    
    // Check letters
    if (/[a-zA-Z]/.test(password)) {
        reqLetter.classList.add('valid');
        reqLetter.querySelector('i').classList.remove('fa-times', 'text-danger');
        reqLetter.querySelector('i').classList.add('fa-check', 'text-success');
        score += 25;
        requirements++;
    } else {
        reqLetter.classList.remove('valid');
        reqLetter.querySelector('i').classList.remove('fa-check', 'text-success');
        reqLetter.querySelector('i').classList.add('fa-times', 'text-danger');
    }
    
    // Check mixed case
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
        reqCase.classList.add('valid');
        reqCase.querySelector('i').classList.remove('fa-times', 'text-danger');
        reqCase.querySelector('i').classList.add('fa-check', 'text-success');
        score += 25;
        requirements++;
    } else {
        reqCase.classList.remove('valid');
        reqCase.querySelector('i').classList.remove('fa-check', 'text-success');
        reqCase.querySelector('i').classList.add('fa-times', 'text-danger');
    }
    
    // Check numbers
    if (/[0-9]/.test(password)) {
        reqNumber.classList.add('valid');
        reqNumber.querySelector('i').classList.remove('fa-times', 'text-danger');
        reqNumber.querySelector('i').classList.add('fa-check', 'text-success');
        score += 25;
        requirements++;
    } else {
        reqNumber.classList.remove('valid');
        reqNumber.querySelector('i').classList.remove('fa-check', 'text-success');
        reqNumber.querySelector('i').classList.add('fa-times', 'text-danger');
    }
    
    // Update progress bar
    strengthBar.style.width = score + '%';
    
    if (score === 0) {
        strengthBar.className = 'progress-bar';
        strengthText.textContent = 'Chưa nhập';
        strengthText.className = 'badge bg-secondary';
    } else if (score <= 25) {
        strengthBar.className = 'progress-bar bg-danger';
        strengthText.textContent = 'Yếu';
        strengthText.className = 'badge bg-danger';
    } else if (score <= 50) {
        strengthBar.className = 'progress-bar bg-warning';
        strengthText.textContent = 'Trung bình';
        strengthText.className = 'badge bg-warning';
    } else if (score <= 75) {
        strengthBar.className = 'progress-bar bg-info';
        strengthText.textContent = 'Khá';
        strengthText.className = 'badge bg-info';
    } else {
        strengthBar.className = 'progress-bar bg-success';
        strengthText.textContent = 'Mạnh';
        strengthText.className = 'badge bg-success';
    }
    
    checkFormValid();
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const matchDiv = document.getElementById('password-match');
    
    if (confirmPassword === '') {
        matchDiv.innerHTML = '';
        return;
    }
    
    if (password === confirmPassword) {
        matchDiv.innerHTML = '<small class="text-success"><i class="fas fa-check me-1"></i>Mật khẩu khớp</small>';
    } else {
        matchDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times me-1"></i>Mật khẩu không khớp</small>';
    }
    
    checkFormValid();
}

function checkFormValid() {
    const currentPassword = document.getElementById('current_password').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const submitBtn = document.getElementById('submitBtn');
    
    // Check all requirements
    const hasLength = password.length >= 8;
    const hasLetter = /[a-zA-Z]/.test(password);
    const hasMixedCase = /[a-z]/.test(password) && /[A-Z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const passwordMatch = password === confirmPassword && confirmPassword !== '';
    
    const isValid = currentPassword !== '' && 
                   hasLength && hasLetter && hasMixedCase && hasNumber && 
                   passwordMatch;
    
    submitBtn.disabled = !isValid;
    
    if (isValid) {
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-gold');
    } else {
        submitBtn.classList.remove('btn-gold');
        submitBtn.classList.add('btn-secondary');
    }
}

// Event listeners
document.getElementById('current_password').addEventListener('keyup', checkFormValid);
document.getElementById('password').addEventListener('keyup', function() {
    checkPasswordStrength();
    checkPasswordMatch();
});
document.getElementById('password_confirmation').addEventListener('keyup', checkPasswordMatch);
</script>
@endsection