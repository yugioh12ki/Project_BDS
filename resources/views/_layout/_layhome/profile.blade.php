@extends('_layout.app')

@section('profilecustomer')
<div class="profile-container">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="profile-image">
                            <img src="{{ Auth::user()->avatar ?? 'https://via.placeholder.com/150' }}" 
                                alt="Profile Image">
                            <button class="change-avatar-btn" id="changeAvatarBtn">
                                <i class="bi bi-camera"></i> Đổi ảnh đại diện
                            </button>
                        </div>
                        <h4 class="mb-1">{{ Auth::user()->Name }}</h4>
                        <p class="text-muted mb-3">{{ Auth::user()->Role }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Thông tin cá nhân</h5>
                        <button class="btn btn-link" id="editToggleBtn">
                            <i class="bi bi-pencil"></i> Chỉnh sửa
                        </button>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('customer.profile.update') }}" id="profileForm">
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Họ và tên</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" 
                                        class="form-control-plaintext editable @error('name') is-invalid @enderror" 
                                        name="name" 
                                        value="{{ old('name', Auth::user()->Name) }}" 
                                        readonly
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Email</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="email" 
                                        class="form-control-plaintext editable @error('email') is-invalid @enderror" 
                                        name="email" 
                                        value="{{ old('email', Auth::user()->email) }}" 
                                        readonly
                                        required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Số điện thoại</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" 
                                        class="form-control-plaintext editable @error('phone') is-invalid @enderror" 
                                        name="phone" 
                                        value="{{ old('phone', Auth::user()->phone) }}" 
                                        readonly
                                        required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Địa chỉ</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea 
                                        class="form-control-plaintext editable @error('address') is-invalid @enderror" 
                                        name="address" 
                                        rows="3" 
                                        readonly
                                        required>{{ old('address', Auth::user()->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="edit-buttons" id="editButtons" style="display: none;">
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary me-2" id="cancelBtn">Hủy</button>
                                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editToggleBtn = document.getElementById('editToggleBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const editButtons = document.getElementById('editButtons');
    const editableFields = document.querySelectorAll('.editable');
    const form = document.getElementById('profileForm');

    let originalValues = {};

    editToggleBtn.addEventListener('click', function() {
        const isEditing = editButtons.style.display === 'none';
        
        editableFields.forEach(field => {
            if (isEditing) {
                // Store original value
                originalValues[field.name] = field.value;
                field.removeAttribute('readonly');
                field.classList.remove('form-control-plaintext');
                field.classList.add('form-control');
            } else {
                field.setAttribute('readonly', true);
                field.classList.add('form-control-plaintext');
                field.classList.remove('form-control');
            }
        });

        editButtons.style.display = isEditing ? 'block' : 'none';
        editToggleBtn.innerHTML = isEditing ? 
            '<i class="bi bi-x"></i> Đang chỉnh sửa' : 
            '<i class="bi bi-pencil"></i> Chỉnh sửa';
    });

    cancelBtn.addEventListener('click', function() {
        // Restore original values
        editableFields.forEach(field => {
            field.value = originalValues[field.name];
            field.setAttribute('readonly', true);
            field.classList.add('form-control-plaintext');
            field.classList.remove('form-control');
        });

        editButtons.style.display = 'none';
        editToggleBtn.innerHTML = '<i class="bi bi-pencil"></i> Chỉnh sửa';
    });
});
</script>
@endsection 