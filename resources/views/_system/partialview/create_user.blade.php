@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="addUserForm" action="{{ route('admin.users.store') }}" method="POST">
    @csrf
    {{-- Name --}}
    <div class="mb-3">
        <label for="Name" class="form-label">Tên User</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Nhập họ tên" required>
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label for="userEmail" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" required>
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label for="userPassword" class="form-label">Mật khẩu</label>
        <input type="text" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
    </div>

    {{-- Role --}}
    <div class="mb-3">
        <label for="userRole" class="form-label">Role</label>
        <select class="form-control" id="userRole" name="role" required>
            <option value="Admin">Admin</option>
            <option value="Agent">Agent</option>
            <option value="Owner">Owner</option>
            <option value="Customer">Customer</option>
        </select>
    </div>

    {{-- Birth --}}
    <div class="mb-3">
        <label for="userBirth" class="form-label">Ngày sinh</label>
        <input type="date" class="form-control" id="birth" name="birth" required>
    </div>

    {{-- Sex --}}
    <div class="mb-3">
        <label for="userSex" class="form-label">Giới tính</label>
        <select class="form-control" id="sex" name="sex" required>
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
            <option value="Khác">Khác</option>
        </select>
    </div>

    {{-- IdentityCard --}}
    <div class="mb-3">
        <label for="userIdentityCard" class="form-label">CMND/CCCD</label>
        <input type="text" class="form-control" id="identity_card" name="identity_card" placeholder="Nhập CMND/CCCD" required pattern="\d{9}|\d{12}">
    </div>

    {{-- Phone --}}
    <div class="mb-3">
        <label for="userPhone" class="form-label">Số điện thoại</label>
        <input type="text" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại" required pattern="\d{10}">
    </div>

    {{-- Address --}}
    <div class="mb-3">
        <label for="userAddress" class="form-label">Địa chỉ</label>
        <input type="text" class="form-control" id="address" name="address" placeholder="Nhập địa chỉ" required>
    </div>

    {{-- Ward --}}
    <div class="mb-3">
        <label for="userWard" class="form-label">Phường/Xã</label>
        <select class="form-select" id="ward" name="ward" data-selected="" required>
            <option value="">Chọn phường/Xã</option>
            {{-- Các tùy chọn phường sẽ được thêm vào đây --}}
        </select>
    </div>

    {{-- District --}}
    <div class="mb-3">
        <label for="userDistrict" class="form-label">Quận</label>
        <select class="form-select" id="district" name="district" data-selected="" required>
            <option value="">Chọn quận</option>
            {{-- Các tùy chọn quận sẽ được thêm vào đây --}}
        </select>
    </div>

    {{-- Province --}}
    <div class="mb-3">
        <label for="userProvince" class="form-label">Tỉnh</label>
        <select class="form-select" id="province" name="province" data-selected="" required>
            <option value="">Chọn tỉnh</option>
            {{-- Các tùy chọn tỉnh sẽ được thêm vào đây --}}
        </select>
    </div>

    {{-- Submit Button --}}
    <button type="submit" class="btn btn-primary">Lưu</button>
</form>

<script src="{{ asset('js/dscactinh.js') }}"></script>
