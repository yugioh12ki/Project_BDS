{{-- Create User với Profile Management --}}
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Notification Container --}}
<div id="notificationContainer" class="notification-container"></div>

<form id="addUserForm" action="{{ route('admin.users.createWithProfile') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Chọn Role trước để hiển thị form phù hợp --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-user-tag me-2"></i>
                Chọn loại tài khoản
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <label for="userRole" class="form-label fw-bold">Loại tài khoản <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg" id="userRole" name="role" required onchange="toggleProfileForm()">
                        <option value="">-- Chọn loại tài khoản --</option>
                        <option value="Admin">👑 Admin - Quản trị viên</option>
                        <option value="Agent">🏢 Agent - Nhân viên bán hàng</option>
                        <option value="Owner">🏠 Owner - Chủ sở hữu BDS</option>
                        <option value="Customer">👤 Customer - Khách hàng</option>
                    </select>
                    <div class="form-text">Chọn loại tài khoản để hiển thị form thông tin phù hợp</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Thông tin cơ bản --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0">
                <i class="fas fa-user me-2"></i>
                Thông tin cơ bản
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Nhập họ và tên đầy đủ" required value="{{ old('name') }}" pattern="[a-zA-ZÀ-ỹ\s]{2,50}" title="Họ tên chỉ được chứa chữ cái và khoảng trắng (2-50 ký tự)">
                    <small class="form-text text-muted">Họ tên chỉ được chứa chữ cái và khoảng trắng (2-50 ký tự)</small>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                    <div class="email-suggestion position-relative">
                    <input type="email" class="form-control" id="email" name="email" placeholder="VD: user@company.com hoặc user@gmail.com" required value="{{ old('email') }}" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" title="Nhập email hợp lệ (cá nhân hoặc công ty)" autocomplete="email">
                        <div id="emailSuggestions" class="email-suggestion-dropdown"></div>
                    </div>
                    <small class="form-text text-muted email-help-text">
                        Chấp nhận mọi email hợp lệ: <span class="domain-list">công ty, tổ chức, cá nhân (@gmail.com, @company.com.vn, @edu.vn...)</span>
                    </small>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="password" class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required minlength="6" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{6,}$" title="Mật khẩu phải có ít nhất 6 ký tự, bao gồm chữ hoa, chữ thường và số">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted">Mật khẩu phải có ít nhất 6 ký tự, bao gồm chữ hoa, chữ thường và số</small>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-bold">Số điện thoại</label>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="0123456789" value="{{ old('phone') }}" pattern="[0-9]{10}" maxlength="10" title="Chỉ được nhập số điện thoại (10 ký tự)">
                    <small class="form-text text-muted">Chỉ được nhập số điện thoại (10 ký tự)</small>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="birth" class="form-label fw-bold">Ngày sinh</label>
                    <input type="date" class="form-control" id="birth" name="birth" value="{{ old('birth') }}">
                </div>
                <div class="col-md-6">
                    <label for="sex" class="form-label fw-bold">Giới tính <span class="text-danger">*</span></label>
                    <select class="form-control" id="sex" name="sex" required>
                        <option value="">-- Chọn giới tính --</option>
                        <option value="Nam" {{ old('sex') == 'Nam' ? 'selected' : '' }}>Nam</option>
                        <option value="Nữ" {{ old('sex') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                        <option value="Khác" {{ old('sex') == 'Khác' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="identity_card" class="form-label fw-bold">CCCD/CMND <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="identity_card" name="identity_card" placeholder="Số CCCD/CMND" value="{{ old('identity_card') }}" required pattern="[0-9]{9,12}" title="CCCD phải có 12 số, CMND phải có 9 số">
                    <small class="form-text text-muted">CCCD phải có 12 số, CMND phải có 9 số</small>
                </div>
                <div class="col-md-6">
                    <label for="avatar" class="form-label fw-bold">Ảnh đại diện</label>
                    <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                    <small class="form-text text-muted">Chấp nhận file: JPG, PNG, GIF (tối đa 2MB)</small>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <label for="address" class="form-label fw-bold">Địa chỉ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="Số nhà, tên đường" value="{{ old('address') }}" required>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <label for="province" class="form-label fw-bold">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                    <select class="form-control" id="province" name="province" onchange="loadDistricts(this.value, 'district')" required>
                        <option value="">-- Chọn tỉnh/thành phố --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="district" class="form-label fw-bold">Quận/Huyện <span class="text-danger">*</span></label>
                    <select class="form-control" id="district" name="district" onchange="loadWards(this.value, 'ward')" required>
                        <option value="">-- Chọn quận/huyện --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="ward" class="form-label fw-bold">Phường/Xã <span class="text-danger">*</span></label>
                    <select class="form-control" id="ward" name="ward" required>
                        <option value="">-- Chọn phường/xã --</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile forms cho từng role --}}
    {{-- Profile Admin --}}
    <div id="adminProfileForm" class="profile-form card border-0 shadow-sm mb-4" style="display: none;">
        <div class="card-header bg-danger text-white">
            <h6 class="mb-0">
                <i class="fas fa-user-shield me-2"></i>
                Thông tin Admin
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <label for="admin_position" class="form-label fw-bold">Chức vụ</label>
                    <select class="form-control" id="admin_position" name="TenChucVu">
                        <option value="">-- Chọn chức vụ --</option>
                        <option value="Nhân viên" {{ old('TenChucVu') == 'Nhân viên' ? 'selected' : '' }}>Nhân viên</option>
                        <option value="Quản trị viên" {{ old('TenChucVu') == 'Quản trị viên' ? 'selected' : '' }}>Quản trị viên</option>
                        <option value="Giám đốc" {{ old('TenChucVu') == 'Giám đốc' ? 'selected' : '' }}>Giám đốc</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Agent --}}
    <div id="agentProfileForm" class="profile-form card border-0 shadow-sm mb-4" style="display: none;">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-user-tie me-2"></i>
                Thông tin Agent
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="agent_certificate" class="form-label fw-bold">Chứng chỉ</label>
                    <textarea class="form-control" id="agent_certificate" name="Certificate" rows="3" placeholder="Các chứng chỉ bất động sản, môi giới...">{{ old('Certificate') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="agent_contact" class="form-label fw-bold">Liên hệ công việc</label>
                    <input type="text" class="form-control" id="agent_contact" name="ContactAgent" placeholder="Số điện thoại công việc" value="{{ old('ContactAgent') }}" pattern="[0-9]{10}" maxlength="10" title="Chỉ được nhập số điện thoại (10 ký tự)">
                    <small class="form-text text-muted">Chỉ được nhập số điện thoại (10 ký tự)</small>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="agent_id_card" class="form-label fw-bold">Số thẻ vật lý/Thẻ ngân hàng</label>
                    <input type="text" class="form-control" id="agent_id_card" name="NumberCardAgent" placeholder="Số thẻ vật lý hoặc thẻ ngân hàng" value="{{ old('NumberCardAgent') }}" pattern="[0-9]{8,20}" title="Chỉ được nhập số (8-20 ký tự)">
                    <small class="form-text text-muted">Chỉ được nhập số (8-20 ký tự)</small>
                </div>
                <div class="col-md-6">
                    <label for="agent_province" class="form-label fw-bold">Tỉnh/Thành phố hoạt động</label>
                    <select class="form-control" id="agent_province" name="ProvinceAgent" onchange="loadAgentDistricts()">
                        <option value="">-- Chọn tỉnh/thành phố --</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="agent_district" class="form-label fw-bold">Quận/Huyện hoạt động</label>
                    <select class="form-control" id="agent_district" name="DistrictAgent">
                        <option value="">-- Chọn quận/huyện --</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Owner --}}
    <div id="ownerProfileForm" class="profile-form card border-0 shadow-sm mb-4" style="display: none;">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0">
                <i class="fas fa-home me-2"></i>
                Thông tin Owner
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="owner_contact" class="form-label fw-bold">Liên hệ Owner</label>
                    <input type="text" class="form-control" id="owner_contact" name="ContactOwner" placeholder="Số điện thoại liên hệ" value="{{ old('ContactOwner') }}" pattern="[0-9]{10}" maxlength="10" title="Chỉ được nhập số điện thoại (10 ký tự)">
                    <small class="form-text text-muted">Chỉ được nhập số điện thoại (10 ký tự)</small>
                </div>
                <div class="col-md-6">
                    <label for="owner_id_card" class="form-label fw-bold">Số thẻ vật lý/Thẻ ngân hàng</label>
                    <input type="text" class="form-control" id="owner_id_card" name="NumberCardOwner" placeholder="Số thẻ vật lý hoặc thẻ ngân hàng" value="{{ old('NumberCardOwner') }}" pattern="[0-9]{8,20}" title="Chỉ được nhập số (8-20 ký tự)">
                    <small class="form-text text-muted">Chỉ được nhập số (8-20 ký tự)</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Customer --}}
    <div id="customerProfileForm" class="profile-form card border-0 shadow-sm mb-4" style="display: none;">
        <div class="card-header bg-secondary text-white">
            <h6 class="mb-0">
                <i class="fas fa-user-tag me-2"></i>
                Thông tin Customer
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="customer_preferences" class="form-label fw-bold">Danh sách yêu thích</label>
                    <textarea class="form-control" id="customer_preferences" name="Whitelist" rows="3" placeholder="Loại BDS yêu thích, khu vực mong muốn...">{{ old('Whitelist') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="customer_property_type" class="form-label fw-bold">Loại BDS ưa thích</label>
                    <select class="form-control" id="customer_property_type" name="PreferredPropertyType">
                        <option value="">-- Chọn loại BDS --</option>
                        <option value="Nhà ở" {{ old('PreferredPropertyType') == 'Nhà ở' ? 'selected' : '' }}>Nhà ở</option>
                        <option value="Chung cư" {{ old('PreferredPropertyType') == 'Chung cư' ? 'selected' : '' }}>Chung cư</option>
                        <option value="Đất nền" {{ old('PreferredPropertyType') == 'Đất nền' ? 'selected' : '' }}>Đất nền</option>
                        <option value="Văn phòng" {{ old('PreferredPropertyType') == 'Văn phòng' ? 'selected' : '' }}>Văn phòng</option>
                        <option value="Mặt bằng kinh doanh" {{ old('PreferredPropertyType') == 'Mặt bằng kinh doanh' ? 'selected' : '' }}>Mặt bằng kinh doanh</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Buttons --}}
    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Hủy bỏ
        </button>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-user-plus me-2"></i>Tạo tài khoản
        </button>
    </div>
</form>

<script>
function toggleProfileForm() {
    console.log('toggleProfileForm called');

    // Ẩn tất cả form profile
    document.querySelectorAll('.profile-form').forEach(form => {
        form.style.display = 'none';
    });

    // Hiển thị form tương ứng với role
    const role = document.getElementById('userRole').value;
    console.log('Selected role:', role);

    if (role) {
        const targetForm = document.getElementById(role.toLowerCase() + 'ProfileForm');
        console.log('Target form:', targetForm);

        if (targetForm) {
            targetForm.style.display = 'block';
            console.log('Form displayed successfully');

            // Nếu là Agent, load provinces cho Agent dropdown
            if (role === 'Agent') {
                loadAgentProvinces();
            }
        }
    }
}

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggleIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

function generateRandomPassword() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let password = '';
    for (let i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
}

// Load provinces riêng cho Agent khi chọn role Agent
async function loadAgentProvinces() {
    try {
        console.log('Loading provinces for Agent...');
        showNotification('info', '🔄 Đang tải danh sách tỉnh/thành phố cho Agent...');

        const response = await fetch('https://provinces.open-api.vn/api/p/');
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const provinces = await response.json();

        const agentProvinceSelect = document.getElementById('agent_province');
        if (agentProvinceSelect) {
            agentProvinceSelect.innerHTML = '<option value="">-- Chọn tỉnh/thành phố --</option>';
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.name;
                option.setAttribute('data-code', province.code);
                option.textContent = province.name;
                agentProvinceSelect.appendChild(option);
            });
            console.log('Agent provinces loaded successfully');
            showNotification('success', '✅ Đã tải thành công danh sách tỉnh/thành phố cho Agent!');
        } else {
            console.error('Agent province select not found');
            showNotification('error', '❌ Không tìm thấy dropdown tỉnh/thành phố Agent!');
        }
    } catch (error) {
        console.error('Lỗi khi load provinces cho Agent:', error);
        showNotification('error', `❌ Lỗi khi tải danh sách tỉnh/thành phố: ${error.message}`);
    }
}

// Load districts riêng cho Agent
async function loadAgentDistricts() {
    try {
        const agentProvinceSelect = document.getElementById('agent_province');
        const provinceName = agentProvinceSelect.value;

        if (!provinceName) {
            console.log('No province selected for Agent');
            return;
        }

        console.log(`Loading Agent districts for province: ${provinceName}`);
        showNotification('info', '🔄 Đang tải quận/huyện cho Agent...');

        const selectedOption = Array.from(agentProvinceSelect.options).find(
            option => option.value === provinceName
        );
        const provinceCode = selectedOption ? selectedOption.getAttribute('data-code') : null;

        if (!provinceCode) {
            console.error('Agent province code not found');
            showNotification('error', '❌ Không tìm thấy mã tỉnh/thành phố Agent!');
            return;
        }

        const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        console.log(`Agent districts loaded: ${data.districts ? data.districts.length : 0}`);

        const agentDistrictSelect = document.getElementById('agent_district');
        if (agentDistrictSelect) {
            agentDistrictSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
            data.districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district.name;
                option.textContent = district.name;
                option.setAttribute('data-code', district.code);
                agentDistrictSelect.appendChild(option);
            });
            showNotification('success', '✅ Đã tải thành công quận/huyện cho Agent!');
        } else {
            console.error('Agent district select not found');
            showNotification('error', '❌ Không tìm thấy dropdown quận/huyện Agent!');
        }

        // Clear agent ward - no longer needed since ward field was removed

    } catch (error) {
        console.error('Error loading Agent districts:', error);
        showNotification('error', `❌ Lỗi khi tải quận/huyện Agent: ${error.message}`);
    }
}

// Notification system
function showNotification(type, message, duration = 5000) {
    const container = document.getElementById('notificationContainer');
    if (!container) return;

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;

    const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'info' ? '🔄' : '⚠️';
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">${icon}</span>
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;

    container.appendChild(notification);

    // Auto remove after duration
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, duration);
}

// Functions for loading provinces/districts/wards
async function loadProvinces() {
    try {
        console.log('Loading provinces for general use...');
        const response = await fetch('https://provinces.open-api.vn/api/p/');
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const provinces = await response.json();

        // Load for general address
        const provinceSelect = document.getElementById('province');
        if (provinceSelect) {
            provinceSelect.innerHTML = '<option value="">-- Chọn tỉnh/thành phố --</option>';
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.name;
                option.textContent = province.name;
                option.setAttribute('data-code', province.code);
                provinceSelect.appendChild(option);
            });
        }

        // Load for agent province
        const agentProvinceSelect = document.getElementById('agent_province');
        if (agentProvinceSelect) {
            agentProvinceSelect.innerHTML = '<option value="">-- Chọn tỉnh/thành phố --</option>';
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.name;
                option.textContent = province.name;
                option.setAttribute('data-code', province.code);
                agentProvinceSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading provinces:', error);
        showNotification('error', `❌ Lỗi khi tải danh sách tỉnh/thành phố: ${error.message}`);
    }
}

async function loadDistricts(provinceName, targetDistrictId) {
    try {
        console.log(`Loading districts for province: ${provinceName}, target: ${targetDistrictId}`);

        // Show loading notification for Agent district
        if (targetDistrictId === 'agent_district') {
            showNotification('info', '🔄 Đang tải quận/huyện cho Agent...');
        }

        // Determine which province select to use based on target district
        let provinceSelect, provinceCode;

        if (targetDistrictId === 'agent_district') {
            provinceSelect = document.getElementById('agent_province');
            console.log('Using agent_province select for agent_district');
        } else {
            provinceSelect = document.getElementById('province');
            console.log('Using province select for district');
        }

        if (!provinceSelect) {
            console.error(`Province select not found for target: ${targetDistrictId}`);
            showNotification('error', '❌ Không tìm thấy dropdown tỉnh/thành phố!');
            return;
        }

        const selectedOption = Array.from(provinceSelect.options).find(
            option => option.value === provinceName
        );
        provinceCode = selectedOption ? selectedOption.getAttribute('data-code') : null;

        console.log(`Province code found: ${provinceCode}`);

        if (!provinceCode) {
            console.error('Province code not found');
            showNotification('error', '❌ Không tìm thấy mã tỉnh/thành phố!');
            return;
        }

        console.log(`Fetching districts from API for province code: ${provinceCode}`);

        // Add timeout and better error handling
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout

        const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`, {
            signal: controller.signal,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        clearTimeout(timeoutId);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        console.log(`Districts loaded successfully: ${data.districts ? data.districts.length : 0}`);

        const districtSelect = document.getElementById(targetDistrictId);
        if (!districtSelect) {
            console.error(`District select not found: ${targetDistrictId}`);
            showNotification('error', `❌ Không tìm thấy dropdown ${targetDistrictId}!`);
            return;
        }

        if (districtSelect) {
            districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
            data.districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district.name;
                option.textContent = district.name;
                option.setAttribute('data-code', district.code);
                districtSelect.appendChild(option);
            });
            console.log(`Districts added to select: ${data.districts.length}`);
        }

        // Clear ward select only for general address district
        if (targetDistrictId === 'district') {
            const wardSelect = document.getElementById('ward');
            if (wardSelect) {
                wardSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
            }
        }

        // Show success notification for Agent district loading
        if (targetDistrictId === 'agent_district') {
            showNotification('success', '✅ Đã tải thành công quận/huyện cho Agent!');
        }
    } catch (error) {
        console.error('Error loading districts:', error);

        // Provide more specific error messages
        let errorMessage = '❌ Lỗi khi tải quận/huyện: ';
        if (error.name === 'AbortError') {
            errorMessage += 'Yêu cầu quá thời gian (timeout)';
        } else if (error.message.includes('Failed to fetch')) {
            errorMessage += 'Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng.';
        } else if (error.message.includes('TypeError')) {
            errorMessage += 'Lỗi dữ liệu. Vui lòng thử lại.';
        } else {
            errorMessage += error.message;
        }

        showNotification('error', errorMessage);
    }
}

async function loadWards(districtName, targetWardId) {
    console.log(`Loading wards for district: ${districtName}, target: ${targetWardId}`);

    try {
        // Find the district select based on context
        let districtSelect;
        if (targetWardId === 'ward') {
            districtSelect = document.getElementById('district');
        } else {
            districtSelect = document.getElementById('district'); // fallback
        }

        if (!districtSelect) {
            console.error(`District select not found for target: ${targetWardId}`);
            showNotification('error', '❌ Không tìm thấy dropdown quận/huyện!');
            return;
        }

        const selectedOption = Array.from(districtSelect.options).find(option => option.value === districtName);
        if (!selectedOption) {
            console.error(`Selected district option not found: ${districtName}`);
            showNotification('error', '❌ Không tìm thấy quận/huyện đã chọn!');
            return;
        }

        const districtCode = selectedOption.getAttribute('data-code');
        if (!districtCode) {
            console.error('District code not found');
            showNotification('error', '❌ Không tìm thấy mã quận/huyện!');
            return;
        }

        console.log(`Fetching wards for district code: ${districtCode}`);
        const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const districtData = await response.json();
        console.log(`Wards loaded: ${districtData.wards ? districtData.wards.length : 0}`);

        const wardSelect = document.getElementById(targetWardId);
        if (!wardSelect) {
            console.error(`Ward select not found: ${targetWardId}`);
            showNotification('error', `❌ Không tìm thấy dropdown ${targetWardId}!`);
            return;
        }

        wardSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';

        if (districtData.wards && districtData.wards.length > 0) {
            districtData.wards.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.name;
                option.textContent = ward.name;
                wardSelect.appendChild(option);
            });
            console.log(`Wards added to select: ${districtData.wards.length}`);
        } else {
            console.warn('No wards found for district');
            showNotification('info', 'ℹ️ Không có phường/xã nào cho quận/huyện này');
        }
    } catch (error) {
        console.error('Error loading wards:', error);
        showNotification('error', `❌ Lỗi khi tải phường/xã: ${error.message}`);
    }
}

// Validate email linh hoạt
function validateEmailFlexible(emailInput) {
    const email = emailInput.value.trim();

    if (!email) {
        emailInput.setCustomValidity('');
        return;
    }

    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(email)) {
        emailInput.setCustomValidity('❌ Email không đúng định dạng');
        return;
    }

    const [username, domain] = email.split('@');

    if (username.length < 1 || username.length > 64) {
        emailInput.setCustomValidity('❌ Tên người dùng email phải từ 1-64 ký tự');
        return;
    }

    if (username.startsWith('.') || username.endsWith('.')) {
        emailInput.setCustomValidity('❌ Tên người dùng không được bắt đầu hoặc kết thúc bằng dấu chấm');
        return;
    }

    if (username.includes('..')) {
        emailInput.setCustomValidity('❌ Tên người dùng không được có hai dấu chấm liên tiếp');
        return;
    }

    const domainParts = domain.split('.');
    if (domainParts.length < 2) {
        emailInput.setCustomValidity('❌ Domain email phải có ít nhất 1 dấu chấm');
        return;
    }

    for (let part of domainParts) {
        if (part.length < 1) {
            emailInput.setCustomValidity('❌ Domain email không hợp lệ');
            return;
        }

        if (!/^[a-zA-Z0-9-]+$/.test(part)) {
            emailInput.setCustomValidity('❌ Domain chỉ được chứa chữ cái, số và dấu gạch ngang');
            return;
        }

        if (part.startsWith('-') || part.endsWith('-')) {
            emailInput.setCustomValidity('❌ Phần domain không được bắt đầu hoặc kết thúc bằng dấu gạch ngang');
            return;
        }
    }

    const tld = domainParts[domainParts.length - 1];
    if (tld.length < 2 || tld.length > 6) {
        emailInput.setCustomValidity('❌ Phần mở rộng domain phải từ 2-6 ký tự');
        return;
    }

    if (!/^[a-zA-Z]+$/.test(tld)) {
        emailInput.setCustomValidity('❌ Phần mở rộng domain chỉ được chứa chữ cái');
        return;
    }

    emailInput.setCustomValidity('');
}

// Email suggestions functions
function showEmailSuggestions(emailValue, dropdown, domains) {
    const [username, currentDomain] = emailValue.split('@');

    if (!username || username.length < 1 || currentDomain.length < 2) {
        hideSuggestions(dropdown);
        return;
    }

    const matchingDomains = domains.filter(domain =>
        domain.toLowerCase().startsWith(currentDomain.toLowerCase())
    ).slice(0, 5);

    if (matchingDomains.length === 0) {
        hideSuggestions(dropdown);
        return;
    }

    dropdown.innerHTML = matchingDomains.map(domain =>
        `<div class="email-suggestion-item" data-email="${username}@${domain}">
            <strong>${username}</strong>@<span class="popular-domain">${domain}</span>
        </div>`
    ).join('');

    dropdown.querySelectorAll('.email-suggestion-item').forEach(item => {
        item.addEventListener('click', function() {
            const email = this.getAttribute('data-email');
            selectSuggestion(email, document.getElementById('email'), dropdown);
        });
    });

    dropdown.classList.add('show');
    dropdown.style.display = 'block';
}

function hideSuggestions(dropdown) {
    dropdown.classList.remove('show');
    dropdown.style.display = 'none';
    dropdown.innerHTML = '';
}

function updateSuggestionSelection(suggestions, index) {
    suggestions.forEach((item, i) => {
        if (i === index) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

function selectSuggestion(email, input, dropdown) {
    input.value = email;
    hideSuggestions(dropdown);
    input.focus();

    setTimeout(() => {
        validateEmailFlexible(input);
    }, 100);
}

// Input validation
function addInputValidation() {
    // Phone validation
    const phoneInputs = document.querySelectorAll('input[type="tel"], input[name="ContactAgent"], input[name="ContactOwner"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            // Chỉ cho phép nhập số và giới hạn 10 ký tự
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            e.target.value = value;

            // Validate phone number
            if (value.length === 10 && /^[0-9]{10}$/.test(value)) {
                e.target.setCustomValidity('');
            } else if (value.length > 0) {
                e.target.setCustomValidity('Số điện thoại phải có đúng 10 chữ số');
            } else {
                e.target.setCustomValidity('');
            }
        });

        input.addEventListener('blur', function(e) {
            const value = e.target.value.trim();
            if (value && (value.length !== 10 || !/^[0-9]{10}$/.test(value))) {
                e.target.setCustomValidity('Số điện thoại phải có đúng 10 chữ số');
            } else {
                e.target.setCustomValidity('');
            }
        });
    });

    // Identity card validation
    const identityCard = document.getElementById('identity_card');
    if (identityCard) {
        identityCard.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 12) {
                this.value = this.value.slice(0, 12);
            }
        });

        identityCard.addEventListener('blur', function(e) {
            if (this.value && (this.value.length !== 9 && this.value.length !== 12)) {
                this.setCustomValidity('CCCD phải có 12 số, CMND phải có 9 số');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Name validation
    const nameInput = document.getElementById('name');
    if (nameInput) {
        nameInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-ZÀ-ỹ\s]/g, '');
            if (this.value.length > 50) {
                this.value = this.value.slice(0, 50);
            }
        });

        nameInput.addEventListener('blur', function(e) {
            if (this.value && (this.value.length < 2 || this.value.length > 50)) {
                this.setCustomValidity('Họ tên phải có từ 2-50 ký tự');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Card number validation
    const cardInputs = document.querySelectorAll('input[name="NumberCardAgent"], input[name="NumberCardOwner"]');
    cardInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 20) {
                this.value = this.value.slice(0, 20);
            }
        });

        input.addEventListener('blur', function(e) {
            if (this.value && (this.value.length < 8 || this.value.length > 20)) {
                this.setCustomValidity('Số thẻ phải có từ 8-20 ký tự');
            } else {
                this.setCustomValidity('');
            }
        });
    });

    // Email validation với auto-complete
    const emailInput = document.getElementById('email');
    if (emailInput) {
        const popularDomains = [
            'gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'live.com',
            'icloud.com', 'protonmail.com', 'zoho.com', 'aol.com', 'mail.com',
            'fpt.com.vn', 'vnn.vn', 'vnpt.vn', 'yahoo.com.vn',
            'edu.vn', 'ac.vn', 'edu', 'ac.uk', 'edu.au',
            'company.com', 'corp.com', 'group.com', 'co.jp', 'co.uk',
            'google.com', 'microsoft.com', 'apple.com'
        ];

        const suggestionDropdown = document.getElementById('emailSuggestions');
        let currentSuggestionIndex = -1;

        emailInput.addEventListener('input', function(e) {
            const value = this.value.toLowerCase().replace(/\s/g, '');
            this.value = value;

            if (value.includes('@') && !value.endsWith('@')) {
                const [username, currentDomain] = value.split('@');
                if (username && currentDomain && currentDomain.length >= 1) {
                    showEmailSuggestions(value, suggestionDropdown, popularDomains);
                }
            } else {
                hideSuggestions(suggestionDropdown);
            }
        });

        emailInput.addEventListener('keydown', function(e) {
            const suggestions = suggestionDropdown.querySelectorAll('.email-suggestion-item');

            if (suggestions.length > 0) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentSuggestionIndex = Math.min(currentSuggestionIndex + 1, suggestions.length - 1);
                    updateSuggestionSelection(suggestions, currentSuggestionIndex);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentSuggestionIndex = Math.max(currentSuggestionIndex - 1, -1);
                    updateSuggestionSelection(suggestions, currentSuggestionIndex);
                } else if (e.key === 'Enter' && currentSuggestionIndex >= 0) {
                    e.preventDefault();
                    selectSuggestion(suggestions[currentSuggestionIndex].getAttribute('data-email'), emailInput, suggestionDropdown);
                } else if (e.key === 'Escape') {
                    hideSuggestions(suggestionDropdown);
                }
            }
        });

        emailInput.addEventListener('blur', function(e) {
            setTimeout(() => {
                hideSuggestions(suggestionDropdown);
                validateEmailFlexible(this);
            }, 150);
        });

        emailInput.addEventListener('focus', function(e) {
            const value = this.value.toLowerCase();
            if (value.includes('@') && !value.endsWith('@')) {
                showEmailSuggestions(value, suggestionDropdown, popularDomains);
            }
        });

        document.addEventListener('click', function(e) {
            if (!emailInput.contains(e.target) && !suggestionDropdown.contains(e.target)) {
                hideSuggestions(suggestionDropdown);
            }
        });
    }

    // Password validation
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function(e) {
            const password = this.value;
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const isLongEnough = password.length >= 6;

            if (!isLongEnough || !hasUpperCase || !hasLowerCase || !hasNumbers) {
                this.setCustomValidity('Mật khẩu phải có ít nhất 6 ký tự, bao gồm chữ hoa, chữ thường và số');
            } else {
                this.setCustomValidity('');
            }
        });
    }
}

// Notification functions
function showNotification(type, message) {
    const container = document.getElementById('notificationContainer');
    if (!container) return;

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.role = 'alert';
    notification.innerHTML = `
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        ${message}
    `;

    // Append to container
    container.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            container.removeChild(notification);
        }, 150);
    }, 5000);
}

// Initialize when DOM loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');

    // Tạo mật khẩu ngẫu nhiên nếu trống
    const passwordField = document.getElementById('password');
    if (!passwordField.value) {
        passwordField.value = generateRandomPassword();
    }

    // Load provinces
    loadProvinces();

    // Add input validation
    addInputValidation();
});
</script>

<style>
.profile-form {
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card-header {
    font-weight: 600;
}

.form-label.fw-bold {
    color: #2c3e50;
}

.text-danger {
    color: #e74c3c !important;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
}

/* Validation styles */
.form-control:invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control:valid {
    border-color: #28a745;
}

.form-control:invalid + .form-text,
.form-control:invalid ~ .form-text {
    color: #dc3545;
}

.form-control:valid + .form-text,
.form-control:valid ~ .form-text {
    color: #6c757d;
}

/* Custom validation message */
.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875em;
    margin-top: 0.25rem;
}

.valid-feedback {
    display: block;
    color: #28a745;
    font-size: 0.875em;
    margin-top: 0.25rem;
}

/* Highlight required fields */
.form-label .text-danger {
    font-weight: bold;
}

/* Style for input focus */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Small text styling */
.form-text.text-muted {
    font-size: 0.875em;
    color: #6c757d;
    margin-top: 0.25rem;
}

/* Input group button styling */
.input-group .btn-outline-secondary {
    border-color: #ced4da;
}

.input-group .btn-outline-secondary:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
}

/* Email suggestion styling */
.email-suggestion {
    position: relative;
}

.email-suggestion-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 1000;
    max-height: 200px;
    overflow-y: auto;
    display: none;
}

.email-suggestion-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.2s;
}

.email-suggestion-item:hover {
    background-color: #f8f9fa;
}

.email-suggestion-item:last-child {
    border-bottom: none;
}

.email-suggestion-item.active {
    background-color: #007bff;
    color: white;
}

/* Highlight common domains */
.popular-domain {
    font-weight: 500;
    color: #28a745;
}

/* Email validation message styling */
.email-help-text {
    font-size: 0.75em;
    color: #6c757d;
    margin-top: 0.25rem;
    line-height: 1.3;
}

.email-help-text .domain-list {
    display: inline-block;
    font-style: italic;
}

/* Animation for email suggestions */
.email-suggestion-dropdown.show {
    display: block;
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Notification system styles */
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
}

.notification {
    margin-bottom: 10px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    opacity: 0;
    transform: translateX(100%);
    animation: slideInRight 0.3s ease forwards;
}

.notification-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.notification-error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.notification-info {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

.notification-content {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    position: relative;
}

.notification-icon {
    font-size: 18px;
    margin-right: 10px;
    flex-shrink: 0;
}

.notification-message {
    flex-grow: 1;
    font-size: 14px;
    line-height: 1.4;
}

.notification-close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: inherit;
    opacity: 0.6;
    margin-left: 10px;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-close:hover {
    opacity: 1;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideOutRight {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(100%);
    }
}
</style>
