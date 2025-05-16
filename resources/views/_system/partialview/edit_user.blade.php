@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Bỏ form ở đây, form sẽ nằm ở ngoài khi include --}}

{{-- Name --}}
<div class="mb-3">
    <label for="Name" class="form-label">Tên User</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Nhập họ tên" value="{{ $user->Name ?? '' }}" required>
</div>

    {{-- Email --}}
    <div class="mb-3">
        <label for="userEmail" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" value="{{ $user->Email ?? '' }}" required>
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label for="userPassword" class="form-label">Mật khẩu</label>
        <input type="text" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" value="{{ $user->PasswordHash ?? '' }}" required>
    </div>

    {{-- Role --}}
    <div class="mb-3">
        <label for="userRole" class="form-label">Role</label>
        <select class="form-control" id="userRole" name="role" required>
            <option value="Admin" {{ (isset($user) && $user->Role == 'Admin') ? 'selected' : '' }}>Admin</option>
            <option value="Agent" {{ (isset($user) && $user->Role == 'Agent') ? 'selected' : '' }}>Agent</option>
            <option value="Owner" {{ (isset($user) && $user->Role == 'Owner') ? 'selected' : '' }}>Owner</option>
            <option value="Customer" {{ (isset($user) && $user->Role == 'Customer') ? 'selected' : '' }}>Customer</option>
        </select>
    </div>

    {{-- Birth --}}
    <div class="mb-3">
        <label for="userBirth" class="form-label">Ngày sinh</label>
        <input type="date" class="form-control" id="birth" name="birth" value="{{ $user->Birth ?? '' }}" required>
    </div>

    {{-- Sex --}}
    <div class="mb-3">
        <label for="userSex" class="form-label">Giới tính</label>
        <select class="form-control" id="sex" name="sex" required>
            <option value="Nam" {{ (isset($user) && $user->Sex == 'Nam') ? 'selected' : '' }}>Nam</option>
            <option value="Nữ" {{ (isset($user) && $user->Sex == 'Nữ') ? 'selected' : '' }}>Nữ</option>
            <option value="Khác" {{ (isset($user) && $user->Sex == 'Khác') ? 'selected' : '' }}>Khác</option>
        </select>
    </div>

    {{-- IdentityCard --}}
    <div class="mb-3">
        <label for="userIdentityCard" class="form-label">CMND/CCCD</label>
        <input type="text" class="form-control" id="identity_card" name="identity_card" placeholder="Nhập CMND/CCCD" value="{{ $user->IdentityCard ?? '' }}" required pattern="\d{9}|\d{12}">
    </div>

    {{-- Phone --}}
    <div class="mb-3">
        <label for="userPhone" class="form-label">Số điện thoại</label>
        <input type="text" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại" value="{{ $user->Phone ?? '' }}" required pattern="\d{10}">
    </div>

    {{-- Status --}}
    <div class="mb-3">
        <label for="userStatus" class="form-label">Trạng thái</label>
        <select class="form-control" id="status" name="status" required>
            <option value="active" {{ (isset($user) && $user->StatusUser == 'active') ? 'selected' : '' }}>Hoạt động</option>
            <option value="inactive" {{ (isset($user) && $user->StatusUser == 'inactive') ? 'selected' : '' }}>Ngừng hoạt động</option>
        </select>

    {{-- Address --}}
    <div class="mb-3">
        <label for="userAddress" class="form-label">Địa chỉ</label>
        <input type="text" class="form-control" id="address" name="address" placeholder="Nhập địa chỉ" value="{{ $user->Address ?? '' }}" required>
    </div>

    {{-- Ward --}}
    <div class="mb-3">
        <label for="ward" class="form-label">Phường/Xã</label>
        <select class="form-select" id="ward" name="ward" data-selected="{{ $user->Ward ?? '' }}" required>
            <option value="">{{ $user->Ward ?? 'Chọn Phường/Xã' }}</option>

        </select>
    </div>

    {{-- District --}}
    <div class="mb-3">
        <label for="ward" class="form-label">Quận/Huyện</label>
        <select class="form-select" id="district" name="district" data-selected="{{ $user->District ?? '' }}" required>
            <option value="">{{ $user->District ?? 'Chọn Quận/Huyện' }}</option>

        </select>
    </div>

    {{-- Province --}}
    <div class="mb-3">
        <label for="province" class="form-label">Tỉnh</label>
        <select class="form-select" id="province" name="province" data-selected="{{ $user->Province ?? '' }}" required>
            <option value="">{{ $user->Province ?? 'Chọn Tỉnh' }}</option>

        </select>
    </div>


