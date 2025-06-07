@extends('_layout.app')
@section('register')
<main class="main-content">
    <div class="register-container">
        <div class="image-section">
            <div class="welcome-content">
                <h2>Chào mừng đến với BDS</h2>
                <p>Tham gia cộng đồng bất động sản hàng đầu Việt Nam</p>
                <div class="feature-list">
                    <div class="feature">
                        <i class="fas fa-home"></i>
                        <span>Hàng nghìn tin đăng chất lượng</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-users"></i>
                        <span>Kết nối với chuyên gia uy tín</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>Giao dịch an toàn, minh bạch</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <form class="register-form" method="POST" action="{{ route('register.submit') }}" enctype="multipart/form-data">
                @csrf
                <h1 class="register-title">Đăng Ký Tài Khoản</h1>
                <p class="subtitle">Tạo tài khoản để bắt đầu hành trình tìm kiếm bất động sản</p>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Row 1: Tên và Email -->
                <div class="form-row two-col">
                    <div class="form-group">
                        <label for="name">Họ Tên <span style="color: red;">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               placeholder="Nhập họ và tên đầy đủ" required
                               class="{{ $errors->has('name') ? 'error' : '' }}">
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span style="color: red;">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               placeholder="Nhập địa chỉ email" required
                               class="{{ $errors->has('email') ? 'error' : '' }}">
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row 2: Mật khẩu -->
                <div class="form-row two-col">
                    <div class="form-group">
                        <label for="password">Mật Khẩu <span style="color: red;">*</span></label>
                        <div class="password-input-wrapper">
                            <input type="password" id="password" name="password"
                                   placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)" required
                                   class="{{ $errors->has('password') ? 'error' : '' }}">
                            <button type="button" class="password-toggle" id="password-toggle">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        <div class="password-strength">
                            <div class="strength-indicator">
                                <div class="strength-bar" id="strength-bar"></div>
                            </div>
                            <div class="strength-text" id="strength-text">Độ mạnh mật khẩu: Yếu</div>
                            <div class="strength-requirements">
                                <div class="strength-item" id="length-check">
                                    <i class="fas fa-times"></i>
                                    <span>Ít nhất 8 ký tự</span>
                                </div>
                                <div class="strength-item" id="uppercase-check">
                                    <i class="fas fa-times"></i>
                                    <span>Có chữ hoa (A-Z)</span>
                                </div>
                                <div class="strength-item" id="lowercase-check">
                                    <i class="fas fa-times"></i>
                                    <span>Có chữ thường (a-z)</span>
                                </div>
                                <div class="strength-item" id="number-check">
                                    <i class="fas fa-times"></i>
                                    <span>Có số (0-9)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Xác Nhận Mật Khẩu <span style="color: red;">*</span></label>
                        <div class="password-input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   placeholder="Nhập lại mật khẩu" required>
                            <button type="button" class="password-toggle" id="password-confirmation-toggle">
                                <i class="fas fa-eye" id="password-confirmation-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Số điện thoại -->
                <div class="form-group">
                    <label for="phone">Số Điện Thoại <span style="color: red;">*</span></label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                           placeholder="Nhập số điện thoại (VD: 0981234567)" required 
                           pattern="^(0[3|5|7|8|9])+([0-9]{8})$" maxlength="10"
                           class="{{ $errors->has('phone') ? 'error' : '' }}">
                    @error('phone')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Row 4: CCCD và Ngày sinh -->
                <div class="form-row two-col">
                    <div class="form-group">
                        <label for="identity_card">CCCD/CMND <span style="color: red;">*</span></label>
                        <input type="text" id="identity_card" name="identity_card" value="{{ old('identity_card') }}"
                               placeholder="Nhập số CCCD (12 số) hoặc CMND (9 số)" required 
                               pattern="^[0-9]{9}$|^[0-9]{12}$" maxlength="12"
                               class="{{ $errors->has('identity_card') ? 'error' : '' }}">
                        @error('identity_card')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="birth">Ngày Sinh <span style="color: red;">*</span></label>
                        <input type="date" id="birth" name="birth" value="{{ old('birth') }}" required
                               class="{{ $errors->has('birth') ? 'error' : '' }}">
                        @error('birth')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row 5: Địa chỉ -->
                <div class="form-group">
                    <label for="address">Địa Chỉ <span style="color: red;">*</span></label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}"
                           placeholder="Nhập địa chỉ cụ thể" required
                           class="{{ $errors->has('address') ? 'error' : '' }}">
                    @error('address')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Row 6: Tỉnh, Quận, Phường -->
                <div class="form-row three-col">
                    <div class="form-group">
                        <label for="province">Tỉnh/Thành phố <span style="color: red;">*</span></label>
                        <select id="province" name="province" required
                                class="{{ $errors->has('province') ? 'error' : '' }}">
                            <option value="">Chọn Tỉnh/Thành phố</option>
                        </select>
                        @error('province')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="district">Quận/Huyện <span style="color: red;">*</span></label>
                        <select id="district" name="district" required
                                class="{{ $errors->has('district') ? 'error' : '' }}">
                            <option value="">Chọn Quận/Huyện</option>
                        </select>
                        @error('district')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="ward">Phường/Xã <span style="color: red;">*</span></label>
                        <select id="ward" name="ward" required
                                class="{{ $errors->has('ward') ? 'error' : '' }}">
                            <option value="">Chọn Phường/Xã</option>
                        </select>
                        @error('ward')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Avatar Upload (cuối form) -->
                <div class="form-group avatar-section">
                    <label for="avatar">Ảnh Đại Diện (Tùy chọn)</label>
                    <div class="avatar-upload">
                        <div class="avatar-preview" id="avatar-preview">
                            <div class="avatar-placeholder">
                                <i class="fas fa-user"></i>
                                <span>Chọn ảnh</span>
                            </div>
                        </div>
                        <input type="file" id="avatar" name="avatar" accept="image/*" 
                               class="avatar-input {{ $errors->has('avatar') ? 'error' : '' }}">
                        <label for="avatar" class="avatar-upload-btn">
                            <i class="fas fa-camera"></i>
                            Chọn ảnh đại diện
                        </label>
                    </div>
                    @error('avatar')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="button-group">
                    <button type="button" class="login-btn" onclick="window.location.href='{{ route('login') }}'">
                        Về Trang Đăng Nhập
                    </button>
                    <button type="submit" class="register-btn">
                        Đăng Ký Ngay
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load provinces, districts, wards
    loadProvinces();
    
    // Initialize avatar upload
    initializeAvatarUpload();
    
    // Initialize form validation
    initializeFormValidation();
    
    // Initialize password toggle functionality
    initializePasswordToggle();
    initializeAvatarUpload();
});

function initializeFormValidation() {
    const form = document.querySelector('.register-form');
    
    // Validation rules and messages
    const validationRules = {
        name: {
            required: true,
            minLength: 3,
            pattern: /^[a-zA-ZÀ-ỹ\s]+$/,
            messages: {
                required: 'Vui lòng nhập họ tên',
                minLength: 'Họ tên phải có ít nhất 3 ký tự',
                pattern: 'Họ tên chỉ được chứa chữ cái và khoảng trắng'
            }
        },
        email: {
            required: true,
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            messages: {
                required: 'Vui lòng nhập email',
                pattern: 'Email không đúng định dạng'
            }
        },
        password: {
            required: true,
            minLength: 8,
            pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/,
            messages: {
                required: 'Vui lòng nhập mật khẩu',
                minLength: 'Mật khẩu phải có ít nhất 8 ký tự',
                pattern: 'Mật khẩu phải chứa ít nhất 1 chữ hoa, 1 chữ thường và 1 số'
            }
        },
        password_confirmation: {
            required: true,
            messages: {
                required: 'Vui lòng xác nhận mật khẩu',
                match: 'Xác nhận mật khẩu không khớp'
            }
        },
        phone: {
            required: true,
            pattern: /^(0[3|5|7|8|9])+([0-9]{8})$/,
            messages: {
                required: 'Vui lòng nhập số điện thoại',
                pattern: 'Số điện thoại không đúng định dạng (VD: 0981234567)'
            }
        },
        identity_card: {
            required: true,
            pattern: /^[0-9]{9}$|^[0-9]{12}$/,
            messages: {
                required: 'Vui lòng nhập CCCD/CMND',
                pattern: 'CCCD/CMND phải có 9 hoặc 12 chữ số'
            }
        },
        birth: {
            required: true,
            messages: {
                required: 'Vui lòng nhập ngày sinh',
                pattern: 'Ngày sinh không hợp lệ'
            }
        },
        address: {
            required: true,
            minLength: 10,
            messages: {
                required: 'Vui lòng nhập địa chỉ',
                minLength: 'Địa chỉ phải có ít nhất 10 ký tự'
            }
        },
        province: {
            required: true,
            messages: {
                required: 'Vui lòng chọn tỉnh/thành phố'
            }
        },
        district: {
            required: true,
            messages: {
                required: 'Vui lòng chọn quận/huyện'
            }
        },
        ward: {
            required: true,
            messages: {
                required: 'Vui lòng chọn phường/xã'
            }
        }
    };
    
    // Add real-time validation to all form fields
    Object.keys(validationRules).forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            // Add input event listener for real-time validation
            field.addEventListener('input', function() {
                // Format input for specific fields
                if (fieldName === 'phone') {
                    formatPhoneInput(field);
                } else if (fieldName === 'identity_card') {
                    formatIdentityCardInput(field);
                } else if (fieldName === 'name') {
                    formatNameInput(field);
                }
                
                validateField(fieldName, field.value, validationRules[fieldName]);
                
                // Special handling for password strength indicator
                if (fieldName === 'password') {
                    updatePasswordStrength(field.value);
                }
            });
            
            // Add blur event listener for validation when leaving field
            field.addEventListener('blur', function() {
                validateField(fieldName, field.value, validationRules[fieldName]);
            });
        }
    });
    
    // Add form submit validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        Object.keys(validationRules).forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field) {
                const valid = validateField(fieldName, field.value, validationRules[fieldName]);
                if (!valid) isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showErrorMessage('Vui lòng kiểm tra lại thông tin nhập vào!');
        }
    });
}

function validateField(fieldName, value, rules) {
    const field = document.querySelector(`[name="${fieldName}"]`);
    const errorDiv = field.parentNode.querySelector('.error-message');
    
    // Remove existing error styling
    field.classList.remove('error', 'success');
    if (errorDiv) {
        errorDiv.remove();
    }
    
    let isValid = true;
    let errorMessage = '';
    
    // Check required
    if (rules.required && (!value || value.trim() === '')) {
        isValid = false;
        errorMessage = rules.messages.required;
    }
    
    // Check minimum length
    else if (rules.minLength && value.length < rules.minLength) {
        isValid = false;
        errorMessage = rules.messages.minLength;
    }
    
    // Check pattern
    else if (rules.pattern && !rules.pattern.test(value)) {
        isValid = false;
        errorMessage = rules.messages.pattern;
    }
    
    // Special validation for password confirmation
    else if (fieldName === 'password_confirmation') {
        const password = document.querySelector('[name="password"]').value;
        if (value !== password) {
            isValid = false;
            errorMessage = rules.messages.match;
        }
    }
    
    // Special validation for birth date
    else if (fieldName === 'birth' && value) {
        const birthDate = new Date(value);
        const today = new Date();
        if (birthDate >= today) {
            isValid = false;
            errorMessage = 'Ngày sinh phải trước ngày hiện tại';
        } else {
            // Check if user is at least 18 years old
            const age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            if (age < 18) {
                isValid = false;
                errorMessage = 'Bạn phải đủ 18 tuổi để đăng ký';
            }
        }
    }
    
    // Apply styling and show error message
    if (!isValid) {
        field.classList.add('error');
        showFieldError(field, errorMessage);
    } else if (value.trim() !== '') {
        field.classList.add('success');
    }
    
    return isValid;
}

function showFieldError(field, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function showErrorMessage(message) {
    // Create or update general error message
    let errorAlert = document.querySelector('.alert-error');
    if (!errorAlert) {
        errorAlert = document.createElement('div');
        errorAlert.className = 'alert alert-error';
        const form = document.querySelector('.register-form');
        form.insertBefore(errorAlert, form.firstChild);
    }
    errorAlert.textContent = message;
    errorAlert.style.display = 'block';
    
    // Scroll to top to show error
    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        errorAlert.style.display = 'none';
    }, 5000);
}

function updatePasswordStrength(password) {
    const checks = {
        'length-check': password.length >= 8,
        'uppercase-check': /[A-Z]/.test(password),
        'lowercase-check': /[a-z]/.test(password),
        'number-check': /[0-9]/.test(password)
    };
    
    Object.keys(checks).forEach(checkId => {
        const element = document.getElementById(checkId);
        if (element) {
            if (checks[checkId]) {
                element.classList.add('valid');
            } else {
                element.classList.remove('valid');
            }
        }
    });
}

function loadProvinces() {
    // You can implement Vietnam provinces API here
    // For now, I'll add some sample data
    const provinces = [
        { name: 'Hà Nội', code: 'HN' },
        { name: 'Hồ Chí Minh', code: 'HCM' },
        { name: 'Đà Nẵng', code: 'DN' },
        { name: 'Hải Phòng', code: 'HP' },
        { name: 'Cần Thơ', code: 'CT' }
    ];

    const provinceSelect = document.getElementById('province');
    provinces.forEach(province => {
        const option = document.createElement('option');
        option.value = province.name;
        option.textContent = province.name;
        provinceSelect.appendChild(option);
    });

    provinceSelect.addEventListener('change', function() {
        loadDistricts(this.value);
        // Trigger validation when province changes
        validateField('province', this.value, {
            required: true,
            messages: { required: 'Vui lòng chọn tỉnh/thành phố' }
        });
    });
}

function loadDistricts(province) {
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');

    // Clear previous options
    districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';

    // Sample districts based on province
    const districts = {
        'Hà Nội': ['Ba Đình', 'Hoàn Kiếm', 'Tây Hồ', 'Long Biên', 'Cầu Giấy'],
        'Hồ Chí Minh': ['Quận 1', 'Quận 2', 'Quận 3', 'Quận 4', 'Quận 5'],
        'Đà Nẵng': ['Hải Châu', 'Thanh Khê', 'Sơn Trà', 'Ngũ Hành Sơn'],
        'Hải Phòng': ['Hồng Bàng', 'Ngô Quyền', 'Lê Chân', 'Hải An'],
        'Cần Thơ': ['Ninh Kiều', 'Bình Thủy', 'Cái Răng', 'Ô Môn']
    };

    if (districts[province]) {
        districts[province].forEach(district => {
            const option = document.createElement('option');
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        });
    }

    districtSelect.addEventListener('change', function() {
        loadWards(this.value);
        // Trigger validation when district changes
        validateField('district', this.value, {
            required: true,
            messages: { required: 'Vui lòng chọn quận/huyện' }
        });
    });
}

function loadWards(district) {
    const wardSelect = document.getElementById('ward');
    wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';

    // Sample wards
    const wards = ['Phường 1', 'Phường 2', 'Phường 3', 'Phường 4', 'Phường 5'];

    wards.forEach(ward => {
        const option = document.createElement('option');
        option.value = ward;
        option.textContent = ward;
        wardSelect.appendChild(option);
    });
    
    wardSelect.addEventListener('change', function() {
        // Trigger validation when ward changes
        validateField('ward', this.value, {
            required: true,
            messages: { required: 'Vui lòng chọn phường/xã' }
        });
    });
}

// Avatar upload handling
function initializeAvatarUpload() {
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');
    
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Vui lòng chọn file hình ảnh (PNG, JPG, JPEG, GIF)');
                    avatarInput.value = '';
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Kích thước file không được vượt quá 5MB');
                    avatarInput.value = '';
                    return;
                }
                
                // Create preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.innerHTML = `<img src="${e.target.result}" alt="Avatar Preview" class="avatar-img">`;
                };
                reader.onerror = function() {
                    alert('Có lỗi khi đọc file. Vui lòng thử lại.');
                    avatarInput.value = '';
                    resetAvatarPreview();
                };
                reader.readAsDataURL(file);
            } else {
                resetAvatarPreview();
            }
        });
    }
}

function resetAvatarPreview() {
    const avatarPreview = document.getElementById('avatar-preview');
    if (avatarPreview) {
        avatarPreview.innerHTML = `
            <div class="avatar-placeholder">
                <i class="fas fa-user"></i>
                <span>Chọn ảnh</span>
            </div>
        `;
    }
}

// Function to generate initials from name for avatar fallback
function generateInitials(name) {
    if (!name) return 'U';
    const words = name.trim().split(' ');
    if (words.length === 1) {
        return words[0].charAt(0).toUpperCase();
    }
    return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
}

// Input formatting functions
function formatPhoneInput(field) {
    let value = field.value.replace(/[^0-9]/g, '');
    if (value.length > 10) {
        value = value.slice(0, 10);
    }
    field.value = value;
}

function formatIdentityCardInput(field) {
    let value = field.value.replace(/[^0-9]/g, '');
    if (value.length > 12) {
        value = value.slice(0, 12);
    }
    field.value = value;
}

function formatNameInput(field) {
    // Remove extra spaces and format name properly
    let value = field.value.replace(/[^a-zA-ZÀ-ỹ\s]/g, '');
    value = value.replace(/\s+/g, ' ');
    field.value = value;
}

function initializeAvatarUpload() {
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');
    const avatarUploadBtn = document.querySelector('.avatar-upload-btn');
    
    console.log('Initializing avatar upload...', { avatarInput, avatarPreview });
    
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            console.log('File selected:', file);
            
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    showValidationMessage('avatar', 'Vui lòng chọn file ảnh hợp lệ', 'error');
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showValidationMessage('avatar', 'Kích thước ảnh không được vượt quá 5MB', 'error');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log('Image loaded, updating preview');
                    avatarPreview.innerHTML = `<img src="${e.target.result}" alt="Avatar preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                    
                    // Update button text to show filename
                    if (avatarUploadBtn) {
                        avatarUploadBtn.innerHTML = `<i class="fas fa-check"></i> ${file.name}`;
                    }
                };
                reader.readAsDataURL(file);
                
                // Clear any previous error messages
                clearValidationMessage('avatar');
            } else {
                // Reset to placeholder when no file selected
                avatarPreview.innerHTML = `
                    <div class="avatar-placeholder">
                        <i class="fas fa-user"></i>
                        <span>Chọn ảnh</span>
                    </div>
                `;
                if (avatarUploadBtn) {
                    avatarUploadBtn.innerHTML = `<i class="fas fa-camera"></i> Chọn ảnh đại diện`;
                }
            }
        });
    } else {
        console.error('Avatar elements not found!');
    }
}

function initializePasswordToggle() {
    const passwordToggle = document.getElementById('password-toggle');
    const passwordInput = document.getElementById('password');
    const passwordEye = document.getElementById('password-eye');
    
    if (passwordToggle && passwordInput && passwordEye) {
        passwordToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordEye.classList.remove('fa-eye');
                passwordEye.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordEye.classList.remove('fa-eye-slash');
                passwordEye.classList.add('fa-eye');
            }
        });
    }
    
    // Password confirmation toggle (if exists)
    const confirmToggle = document.getElementById('password-confirmation-toggle');
    const confirmInput = document.getElementById('password_confirmation');
    const confirmEye = document.getElementById('password-confirmation-eye');
    
    if (confirmToggle && confirmInput && confirmEye) {
        confirmToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirmInput.type === 'password') {
                confirmInput.type = 'text';
                confirmEye.classList.remove('fa-eye');
                confirmEye.classList.add('fa-eye-slash');
            } else {
                confirmInput.type = 'password';
                confirmEye.classList.remove('fa-eye-slash');
                confirmEye.classList.add('fa-eye');
            }
        });
    }
}
</script>

@endsection
