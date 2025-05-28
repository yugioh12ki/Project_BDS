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
                    <input type="text" class="form-control" id="name" name="name" placeholder="Nhập họ và tên đầy đủ" required value="{{ old('name') }}">
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="example@email.com" required value="{{ old('email') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="password" class="form-label fw-bold">Mật khẩu <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-bold">Số điện thoại</label>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="0123456789" value="{{ old('phone') }}">
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
                    <input type="text" class="form-control" id="identity_card" name="identity_card" placeholder="Số CCCD/CMND" value="{{ old('identity_card') }}" required>
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
                    <input type="text" class="form-control" id="agent_contact" name="ContactAgent" placeholder="Số điện thoại công việc" value="{{ old('ContactAgent') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="agent_id_card" class="form-label fw-bold">Số thẻ vật lý/Thẻ ngân hàng</label>
                    <input type="text" class="form-control" id="agent_id_card" name="NumberCardAgent" placeholder="Số thẻ vật lý hoặc thẻ ngân hàng" value="{{ old('NumberCardAgent') }}">
                </div>
                <div class="col-md-6">
                    <label for="agent_province" class="form-label fw-bold">Tỉnh/Thành phố hoạt động</label>
                    <select class="form-control" id="agent_province" name="ProvinceAgent" onchange="loadDistricts(this.value, 'agent_district')">
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
                    <input type="text" class="form-control" id="owner_contact" name="ContactOwner" placeholder="Số điện thoại liên hệ" value="{{ old('ContactOwner') }}">
                </div>
                <div class="col-md-6">
                    <label for="owner_id_card" class="form-label fw-bold">Số thẻ vật lý/Thẻ ngân hàng</label>
                    <input type="text" class="form-control" id="owner_id_card" name="NumberCardOwner" placeholder="Số thẻ vật lý hoặc thẻ ngân hàng" value="{{ old('NumberCardOwner') }}">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <label for="owner_documents" class="form-label fw-bold">Giấy tờ sở hữu</label>
                    <textarea class="form-control" id="owner_documents" name="GiayTo" rows="3" placeholder="Sổ đỏ, sổ hồng, giấy chứng nhận quyền sở hữu...">{{ old('GiayTo') }}</textarea>
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
    console.log('toggleProfileForm called'); // Debug log

    // Ẩn tất cả form profile
    document.querySelectorAll('.profile-form').forEach(form => {
        form.style.display = 'none';
    });

    // Hiển thị form tương ứng với role
    const role = document.getElementById('userRole').value;
    console.log('Selected role:', role); // Debug log

    if (role) {
        const targetForm = document.getElementById(role.toLowerCase() + 'ProfileForm');
        console.log('Target form:', targetForm); // Debug log

        if (targetForm) {
            targetForm.style.display = 'block';
            console.log('Form displayed successfully'); // Debug log
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

// Functions for loading provinces/districts/wards
async function loadProvinces() {
    try {
        const response = await fetch('https://provinces.open-api.vn/api/?depth=1');
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
    }
}

async function loadDistricts(provinceName, targetDistrictId) {
    try {
        // Determine which province select to use based on target district
        let provinceSelect, provinceCode;

        if (targetDistrictId === 'agent_district') {
            provinceSelect = document.getElementById('agent_province');
        } else {
            provinceSelect = document.getElementById('province');
        }

        const selectedOption = Array.from(provinceSelect.options).find(
            option => option.value === provinceName
        );
        provinceCode = selectedOption ? selectedOption.getAttribute('data-code') : null;

        if (!provinceCode) return;

        const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
        const data = await response.json();

        const districtSelect = document.getElementById(targetDistrictId);

        if (districtSelect) {
            districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';
            data.districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district.name;
                option.textContent = district.name;
                option.setAttribute('data-code', district.code);
                districtSelect.appendChild(option);
            });
        }

        // Clear ward select only for general address district (not agent district)
        if (targetDistrictId === 'district') {
            const wardSelect = document.getElementById('ward');
            if (wardSelect) {
                wardSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
            }
        }
    } catch (error) {
        console.error('Error loading districts:', error);
    }
}

async function loadWards(districtName, targetWardId) {
    try {
        // Get district code
        const districtSelect = document.getElementById('district');
        const selectedOption = Array.from(districtSelect.options).find(
            option => option.value === districtName
        );
        const districtCode = selectedOption ? selectedOption.getAttribute('data-code') : null;

        if (!districtCode) return;

        const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
        const data = await response.json();

        const wardSelect = document.getElementById(targetWardId);
        if (wardSelect) {
            wardSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
            data.wards.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.name;
                option.textContent = ward.name;
                wardSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading wards:', error);
    }
}

// Load provinces từ API
async function loadProvinces() {
    try {
        const response = await fetch('https://provinces.open-api.vn/api/p/');
        const provinces = await response.json();

        // Load vào select chính (address)
        const provinceSelect = document.getElementById('province');
        provinceSelect.innerHTML = '<option value="">-- Chọn tỉnh/thành phố --</option>';

        provinces.forEach(province => {
            const option = document.createElement('option');
            option.value = province.name;
            option.setAttribute('data-code', province.code);
            option.textContent = province.name;
            provinceSelect.appendChild(option);
        });

        // Load vào select agent province
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
        }
    } catch (error) {
        console.error('Lỗi khi load provinces:', error);
    }
}

// Load districts từ API
async function loadDistricts(provinceName, targetSelectId) {
    try {
        // Tìm province code từ tên
        const provinceSelect = targetSelectId === 'agent_district' ?
            document.getElementById('agent_province') :
            document.getElementById('province');

        const selectedOption = Array.from(provinceSelect.options).find(option => option.value === provinceName);
        if (!selectedOption) return;

        const provinceCode = selectedOption.getAttribute('data-code');

        const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
        const provinceData = await response.json();

        const districtSelect = document.getElementById(targetSelectId);
        districtSelect.innerHTML = '<option value="">-- Chọn quận/huyện --</option>';

        if (provinceData.districts) {
            provinceData.districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district.name;
                option.setAttribute('data-code', district.code);
                option.textContent = district.name;
                districtSelect.appendChild(option);
            });
        }

        // Reset ward select nếu có
        const wardSelect = document.getElementById('ward');
        if (wardSelect && targetSelectId === 'district') {
            wardSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';
        }
    } catch (error) {
        console.error('Lỗi khi load districts:', error);
    }
}

// Load wards từ API
async function loadWards(districtName, targetSelectId) {
    try {
        // Tìm district code từ tên
        const districtSelect = document.getElementById('district');
        const selectedOption = Array.from(districtSelect.options).find(option => option.value === districtName);
        if (!selectedOption) return;

        const districtCode = selectedOption.getAttribute('data-code');

        const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
        const districtData = await response.json();

        const wardSelect = document.getElementById(targetSelectId);
        wardSelect.innerHTML = '<option value="">-- Chọn phường/xã --</option>';

        if (districtData.wards) {
            districtData.wards.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.name;
                option.textContent = ward.name;
                wardSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Lỗi khi load wards:', error);
    }
}

// Auto-generate password và load provinces
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded'); // Debug log

    // Tạo mật khẩu ngẫu nhiên nếu trống
    const passwordField = document.getElementById('password');
    if (!passwordField.value) {
        passwordField.value = generateRandomPassword();
    }

    // Load provinces
    loadProvinces();
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
</style>
