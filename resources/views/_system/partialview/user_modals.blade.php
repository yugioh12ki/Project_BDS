@if(isset($user))
<!-- Modal Xóa -->
<div class="modal fade" id="deleteModal{{ $user->UserID }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $user->UserID }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $user->UserID }}">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa người dùng <strong>{{ $user->Name }}</strong>? Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ route('admin.users.delete', $user->UserID) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa -->
<div class="modal fade" id="editModal{{ $user->UserID }}" tabindex="-1" aria-labelledby="editModalLabel{{ $user->UserID }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.update', $user->UserID) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel{{ $user->UserID }}">Chỉnh sửa người dùng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên User</label>
                        <input type="text" class="form-control" name="name" value="{{ $user->Name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ $user->Email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" name="password" placeholder="Để trống nếu không đổi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="Admin" {{ $user->Role == 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="Agent" {{ $user->Role == 'Agent' ? 'selected' : '' }}>Agent</option>
                            <option value="Owner" {{ $user->Role == 'Owner' ? 'selected' : '' }}>Owner</option>
                            <option value="Customer" {{ $user->Role == 'Customer' ? 'selected' : '' }}>Customer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status" required>
                            <option value="active" {{ strtolower($user->StatusUser) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ strtolower($user->StatusUser) == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" name="phone" value="{{ $user->Phone }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CMND/CCCD</label>
                        <input type="text" class="form-control" name="identity_card" value="{{ $user->IdentityCard }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control" name="address" value="{{ $user->Address }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phường/Xã</label>
                        <select class="form-select ward-select" id="ward" name="ward" data-selected="{{ $user->Ward }}" required>
                            <option value="">Chọn phường/xã</option>
                            @if(!empty($user->Ward))
                                <option value="{{ $user->Ward }}" selected>{{ $user->Ward }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quận/Huyện</label>
                        <select class="form-select district-select" id="district" name="district" data-selected="{{ $user->District }}" required>
                            <option value="">Chọn quận/huyện</option>
                            @if(!empty($user->District))
                                <option value="{{ $user->District }}" selected>{{ $user->District }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tỉnh/Thành phố</label>
                        <select class="form-select province-select" id="province" name="province" data-selected="{{ $user->Province }}" required>
                            <option value="">Chọn tỉnh/thành phố</option>
                            @if(!empty($user->Province))
                                <option value="{{ $user->Province }}" selected>{{ $user->Province }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('js/dscactinh_edit.js') }}"></script>
<script type="module">
import provinces from 'vietnam-provinces';

document.addEventListener('shown.bs.modal', function (event) {
    if (event.target.id && event.target.id.startsWith('editModal')) {
        const modal = event.target;
        const provinceSelect = modal.querySelector('.province-select');
        const districtSelect = modal.querySelector('.district-select');
        const wardSelect = modal.querySelector('.ward-select');

        // Đổ tỉnh
        provinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
        provinces.forEach(p => {
            provinceSelect.innerHTML += `<option value="${p.name}">${p.name}</option>`;
        });
        // Set selected
        if (provinceSelect.dataset.selected) {
            provinceSelect.value = provinceSelect.dataset.selected;
        }

        // Đổ quận/huyện khi chọn tỉnh
        provinceSelect.addEventListener('change', function () {
            const selectedProvince = provinces.find(p => p.name === this.value);
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            if (selectedProvince) {
                selectedProvince.districts.forEach(d => {
                    districtSelect.innerHTML += `<option value="${d.name}">${d.name}</option>`;
                });
            }
        });
        // Set quận/huyện nếu có sẵn
        if (provinceSelect.value && provinceSelect.value !== '') {
            const selectedProvince = provinces.find(p => p.name === provinceSelect.value);
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            if (selectedProvince) {
                selectedProvince.districts.forEach(d => {
                    districtSelect.innerHTML += `<option value="${d.name}" ${districtSelect.dataset.selected === d.name ? 'selected' : ''}>${d.name}</option>`;
                });
            }
        }
        // Đổ phường/xã khi chọn quận/huyện
        districtSelect.addEventListener('change', function () {
            const selectedProvince = provinces.find(p => p.name === provinceSelect.value);
            const selectedDistrict = selectedProvince ? selectedProvince.districts.find(d => d.name === this.value) : null;
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            if (selectedDistrict) {
                selectedDistrict.wards.forEach(w => {
                    wardSelect.innerHTML += `<option value="${w.name}">${w.name}</option>`;
                });
            }
        });
        // Set phường/xã nếu có sẵn
        if (provinceSelect.value && districtSelect.value) {
            const selectedProvince = provinces.find(p => p.name === provinceSelect.value);
            const selectedDistrict = selectedProvince ? selectedProvince.districts.find(d => d.name === districtSelect.value) : null;
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            if (selectedDistrict) {
                selectedDistrict.wards.forEach(w => {
                    wardSelect.innerHTML += `<option value="${w.name}" ${wardSelect.dataset.selected === w.name ? 'selected' : ''}>${w.name}</option>`;
                });
            }
        }
    }
});
</script>
@endif
