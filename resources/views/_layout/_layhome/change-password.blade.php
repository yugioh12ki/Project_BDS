@extends('_layout.app')

@section('changepassword')
<div class="change-password-container">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5>
                    <i class="bi bi-shield-lock"></i>
                    Đổi mật khẩu
                </h5>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('customer.password.change') }}" id="changePasswordForm">
                    @csrf

                    <div class="form-group">
                        <label for="current_password">
                            <i class="bi bi-key me-2"></i>
                            Mật khẩu hiện tại
                        </label>
                        <div class="password-input-group">
                            <input id="current_password" 
                                type="password" 
                                class="form-control @error('current_password') is-invalid @enderror" 
                                name="current_password" 
                                required>
                            <i class="bi bi-eye-slash toggle-password" data-target="current_password"></i>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="bi bi-lock me-2"></i>
                            Mật khẩu mới
                        </label>
                        <div class="password-input-group">
                            <input id="password" 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                name="password" 
                                required>
                            <i class="bi bi-eye-slash toggle-password" data-target="password"></i>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <div class="password-strength">
                            <div class="requirements">
                                <ul>
                                    <li id="length"><i class="bi bi-check2"></i>Tối thiểu 8 ký tự</li>
                                    <li id="uppercase"><i class="bi bi-check2"></i>Ít nhất 1 chữ in hoa</li>
                                    <li id="lowercase"><i class="bi bi-check2"></i>Ít nhất 1 chữ thường</li>
                                    <li id="number"><i class="bi bi-check2"></i>Ít nhất 1 số</li>
                                    <li id="special"><i class="bi bi-check2"></i>Ít nhất 1 ký tự đặc biệt</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">
                            <i class="bi bi-shield-check me-2"></i>
                            Xác nhận mật khẩu mới
                        </label>
                        <div class="password-input-group">
                            <input id="password_confirmation" 
                                type="password" 
                                class="form-control"
                                name="password_confirmation" 
                                required>
                            <i class="bi bi-eye-slash toggle-password" data-target="password_confirmation"></i>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary" form="changePasswordForm">
                    <i class="bi bi-check2-circle me-2"></i>
                    Đổi mật khẩu
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password strength checker
    const password = document.getElementById('password');
    const requirements = {
        length: document.getElementById('length'),
        uppercase: document.getElementById('uppercase'),
        lowercase: document.getElementById('lowercase'),
        number: document.getElementById('number'),
        special: document.getElementById('special')
    };

    password.addEventListener('input', function() {
        const value = this.value;
        
        // Check length
        if(value.length >= 8) {
            requirements.length.classList.add('valid');
        } else {
            requirements.length.classList.remove('valid');
        }

        // Check uppercase
        if(/[A-Z]/.test(value)) {
            requirements.uppercase.classList.add('valid');
        } else {
            requirements.uppercase.classList.remove('valid');
        }

        // Check lowercase
        if(/[a-z]/.test(value)) {
            requirements.lowercase.classList.add('valid');
        } else {
            requirements.lowercase.classList.remove('valid');
        }

        // Check number
        if(/[0-9]/.test(value)) {
            requirements.number.classList.add('valid');
        } else {
            requirements.number.classList.remove('valid');
        }

        // Check special character
        if(/[!@#$%^&*(),.?":{}|<>]/.test(value)) {
            requirements.special.classList.add('valid');
        } else {
            requirements.special.classList.remove('valid');
        }
    });

    // Password visibility toggle
    document.querySelectorAll('.toggle-password').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.remove('bi-eye-slash');
                this.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('bi-eye');
                this.classList.add('bi-eye-slash');
            }
        });
    });
});
</script>
@endsection 