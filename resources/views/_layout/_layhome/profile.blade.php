@extends('_layout.app')

@section('profilecustomer')
<div class="profile-container">
    <div class="container-fluid py-4">
        <!-- Enhanced Professional Header -->
        <div class="row mb-4">
            <div class="col-12">                <div class="profile-header-card">
                    <div class="header-background"></div>
                    <div class="header-content">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="page-title mb-2">
                                    <i class="fas fa-user-circle me-3"></i>Quản lý hồ sơ cá nhân
                                </h1>
                                <p class="page-subtitle mb-0">
                                    Cập nhật thông tin, sở thích và tùy chỉnh trải nghiệm của bạn
                                </p>
                            </div>
                            <div class="profile-badge">
                                <span class="badge-verified">
                                    <i class="fas fa-shield-check me-2"></i>
                                    Tài khoản đã xác thực
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Enhanced Navigation Sidebar -->
            <div class="col-lg-3 mb-4">
                <!-- Profile Summary Card -->
                <div class="card profile-summary-card mb-4">
                    <div class="card-body text-center p-4">
                        <div class="profile-avatar-section mb-3">
                            <div class="avatar-container" id="avatar-preview-summary">
                                @if(Auth::user()->Avatar)
                                    <img src="{{ asset('storage/avatars/' . Auth::user()->Avatar) }}" 
                                         alt="Avatar" class="avatar-img">
                                @else
                                    <div class="avatar-placeholder">
                                        <div class="initials">
                                            {{ Auth::user()->Name ? strtoupper(substr(Auth::user()->Name, 0, 2)) : 'UN' }}
                                        </div>
                                    </div>
                                @endif
                                <div class="avatar-status">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <h4 class="profile-name mb-1">{{ Auth::user()->Name }}</h4>
                        <p class="profile-email text-muted mb-3">{{ Auth::user()->Email }}</p>
                        <div class="profile-stats">
                            <div class="stat-item">
                                <i class="fas fa-calendar-check text-gold me-2"></i>
                                <small class="text-muted">
                                    Thành viên từ {{ Auth::user()->created_at ? Auth::user()->created_at->format('m/Y') : 'N/A' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Card -->
                <div class="card navigation-card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-bars me-2"></i>Điều hướng
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="nav nav-pills flex-column profile-nav" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="pill" href="#basic-info" role="tab">
                                    <div class="nav-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="nav-content">
                                        <span class="nav-title">Thông tin cơ bản</span>
                                        <small class="nav-desc">Hồ sơ & liên hệ</small>
                                    </div>
                                    <i class="fas fa-chevron-right nav-arrow"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#location-info" role="tab">
                                    <div class="nav-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="nav-content">
                                        <span class="nav-title">Địa chỉ & Liên hệ</span>
                                        <small class="nav-desc">Vị trí & thông tin liên lạc</small>
                                    </div>
                                    <i class="fas fa-chevron-right nav-arrow"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#preferences" role="tab">
                                    <div class="nav-icon">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div class="nav-content">
                                        <span class="nav-title">Sở thích BĐS</span>
                                        <small class="nav-desc">Tùy chọn & thông báo</small>
                                    </div>
                                    <i class="fas fa-chevron-right nav-arrow"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#security" role="tab">
                                    <div class="nav-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="nav-content">
                                        <span class="nav-title">Bảo mật</span>
                                        <small class="nav-desc">Mật khẩu & quyền riêng tư</small>
                                    </div>
                                    <i class="fas fa-chevron-right nav-arrow"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
                        <div class="alert-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="alert-content">
                            <strong>Thành công!</strong>
                            <p class="mb-0">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
                        <div class="alert-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="alert-content">
                            <strong>Lỗi!</strong>
                            <p class="mb-0">{{ session('error') }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="tab-content profile-tab-content">
                        <!-- Tab 1: Thông tin cơ bản -->
                        <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
                            <div class="card section-card">
                                <div class="card-header">
                                    <div class="header-title">
                                        <h5 class="mb-1">
                                            <i class="fas fa-user text-gold me-2"></i>Thông tin cơ bản
                                        </h5>
                                        <p class="text-muted mb-0">Cập nhật thông tin cá nhân và ảnh đại diện</p>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <!-- Enhanced Avatar Upload Section -->
                                    <div class="avatar-upload-section mb-5">
                                        <div class="row align-items-center">
                                            <div class="col-md-3 text-center">
                                                <div class="current-avatar-container">
                                                    <div class="avatar-preview-large" id="avatar-preview">
                                                        @if(Auth::user()->Avatar)
                                                            <img src="{{ asset('storage/avatars/' . Auth::user()->Avatar) }}" 
                                                                 alt="Avatar" class="avatar-img">
                                                        @else
                                                            <div class="avatar-placeholder">
                                                                <div class="initials">
                                                                    {{ Auth::user()->Name ? strtoupper(substr(Auth::user()->Name, 0, 2)) : 'UN' }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="avatar-overlay">
                                                            <i class="fas fa-camera"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="avatar-controls">
                                                    <h6 class="mb-2">
                                                        <i class="fas fa-camera text-gold me-2"></i>Ảnh đại diện
                                                    </h6>
                                                    <p class="text-muted mb-3">
                                                        Chọn ảnh đại diện để thể hiện cá tính của bạn. Ảnh sẽ được hiển thị công khai.
                                                    </p>
                                                    <div class="upload-controls">
                                                        <input type="file" id="avatar" name="avatar" accept="image/*" 
                                                               class="d-none @error('avatar') is-invalid @enderror">
                                                        <button type="button" class="btn btn-outline-gold me-2" onclick="document.getElementById('avatar').click()">
                                                            <i class="fas fa-upload me-2"></i>Chọn ảnh mới
                                                        </button>
                                                        <div class="upload-info">
                                                            <small class="text-muted">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                Định dạng: JPG, PNG, GIF • Kích thước tối đa: 5MB
                                                            </small>
                                                        </div>
                                                        @error('avatar')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Personal Information -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="name" class="form-label">
                                                    <i class="fas fa-user text-gold me-2"></i>Họ và tên <span class="required">*</span>
                                                </label>
                                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                                    name="name" value="{{ old('name', Auth::user()->Name) }}" required
                                                    placeholder="Nhập họ và tên đầy đủ">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="email" class="form-label">
                                                    <i class="fas fa-envelope text-gold me-2"></i>Email <span class="required">*</span>
                                                </label>
                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                                    name="email" value="{{ old('email', Auth::user()->Email) }}" required
                                                    placeholder="your@email.com">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="phone" class="form-label">
                                                    <i class="fas fa-phone text-gold me-2"></i>Số điện thoại <span class="required">*</span>
                                                </label>
                                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                    name="phone" value="{{ old('phone', Auth::user()->Phone) }}" required
                                                    placeholder="0xxxxxxxxx">
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="birth" class="form-label">
                                                    <i class="fas fa-birthday-cake text-gold me-2"></i>Ngày sinh
                                                </label>
                                                <input id="birth" type="date" class="form-control @error('birth') is-invalid @enderror" 
                                                    name="birth" value="{{ old('birth', Auth::user()->Birth) }}">
                                                @error('birth')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="sex" class="form-label">
                                                    <i class="fas fa-venus-mars text-gold me-2"></i>Giới tính
                                                </label>
                                                <select id="sex" class="form-select @error('sex') is-invalid @enderror" name="sex">
                                                    <option value="">Chọn giới tính</option>
                                                    <option value="Nam" {{ old('sex', Auth::user()->Sex) === 'Nam' ? 'selected' : '' }}>Nam</option>
                                                    <option value="Nữ" {{ old('sex', Auth::user()->Sex) === 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                                    <option value="Khác" {{ old('sex', Auth::user()->Sex) === 'Khác' ? 'selected' : '' }}>Khác</option>
                                                </select>
                                                @error('sex')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label for="identity_card" class="form-label">
                                                    <i class="fas fa-id-card text-gold me-2"></i>CMND/CCCD
                                                </label>
                                                <input id="identity_card" type="text" class="form-control @error('identity_card') is-invalid @enderror" 
                                                    name="identity_card" value="{{ old('identity_card', Auth::user()->IdentityCard) }}"
                                                    placeholder="9 hoặc 12 chữ số">
                                                @error('identity_card')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2: Địa chỉ & Liên hệ -->
                        <div class="tab-pane fade" id="location-info" role="tabpanel">
                            <div class="card section-card">
                                <div class="card-header">
                                    <div class="header-title">
                                        <h5 class="mb-1">
                                            <i class="fas fa-map-marker-alt text-gold me-2"></i>Địa chỉ & Liên hệ
                                        </h5>
                                        <p class="text-muted mb-0">Cung cấp thông tin địa chỉ chi tiết để hỗ trợ tốt hơn</p>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-4">
                                                <label for="province" class="form-label">
                                                    <i class="fas fa-map text-gold me-2"></i>Tỉnh/Thành phố
                                                </label>
                                                <select id="province" class="form-select @error('province') is-invalid @enderror" name="province">
                                                    <option value="">Chọn Tỉnh/Thành phố</option>
                                                    <option value="TP.HCM" {{ old('province', Auth::user()->Province) == 'TP.HCM' ? 'selected' : '' }}>TP.HCM</option>
                                                    <option value="Hà Nội" {{ old('province', Auth::user()->Province) == 'Hà Nội' ? 'selected' : '' }}>Hà Nội</option>
                                                    <option value="Đà Nẵng" {{ old('province', Auth::user()->Province) == 'Đà Nẵng' ? 'selected' : '' }}>Đà Nẵng</option>
                                                    <option value="Bình Dương" {{ old('province', Auth::user()->Province) == 'Bình Dương' ? 'selected' : '' }}>Bình Dương</option>
                                                    <option value="Đồng Nai" {{ old('province', Auth::user()->Province) == 'Đồng Nai' ? 'selected' : '' }}>Đồng Nai</option>
                                                    <option value="Khánh Hòa" {{ old('province', Auth::user()->Province) == 'Khánh Hòa' ? 'selected' : '' }}>Khánh Hòa</option>
                                                    <option value="Lâm Đồng" {{ old('province', Auth::user()->Province) == 'Lâm Đồng' ? 'selected' : '' }}>Lâm Đồng</option>
                                                </select>
                                                @error('province')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-4">
                                                <label for="district" class="form-label">
                                                    <i class="fas fa-map-marked text-gold me-2"></i>Quận/Huyện
                                                </label>
                                                <input id="district" type="text" class="form-control @error('district') is-invalid @enderror" 
                                                    name="district" value="{{ old('district', Auth::user()->District) }}"
                                                    placeholder="Ví dụ: Quận 1, Huyện Củ Chi">
                                                @error('district')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-4">
                                                <label for="ward" class="form-label">
                                                    <i class="fas fa-map-pin text-gold me-2"></i>Phường/Xã
                                                </label>
                                                <input id="ward" type="text" class="form-control @error('ward') is-invalid @enderror" 
                                                    name="ward" value="{{ old('ward', Auth::user()->Ward) }}"
                                                    placeholder="Ví dụ: Phường Bến Nghé">
                                                @error('ward')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group mb-4">
                                                <label for="address" class="form-label">
                                                    <i class="fas fa-home text-gold me-2"></i>Địa chỉ chi tiết <span class="required">*</span>
                                                </label>
                                                <textarea id="address" class="form-control @error('address') is-invalid @enderror" 
                                                    name="address" rows="3" required 
                                                    placeholder="Số nhà, tên đường, tòa nhà...">{{ old('address', Auth::user()->Address) }}</textarea>
                                                @error('address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3: Sở thích BĐS -->
                        <div class="tab-pane fade" id="preferences" role="tabpanel">
                            <div class="card section-card">
                                <div class="card-header">
                                    <div class="header-title">
                                        <h5 class="mb-1">
                                            <i class="fas fa-heart text-gold me-2"></i>Sở thích Bất động sản
                                        </h5>
                                        <p class="text-muted mb-0">Thiết lập sở thích để nhận thông báo phù hợp</p>
                                    </div>
                                </div>                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label">
                                                    <i class="fas fa-building text-gold me-2"></i>Loại BĐS quan tâm
                                                </label>
                                                <div class="property-types-checkboxes">
                                                    @if(isset($propertyTypes) && $propertyTypes->count() > 0)
                                                        @foreach($propertyTypes as $propertyType)
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" 
                                                                    name="preferred_property_types[]" value="{{ $propertyType->Protype_ID }}" 
                                                                    id="property_{{ $propertyType->Protype_ID }}"
                                                                    {{ in_array($propertyType->Protype_ID, old('preferred_property_types', $preferences['preferred_property_types'] ?? [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="property_{{ $propertyType->Protype_ID }}">
                                                                    {{ $propertyType->ten_pro }} <small class="text-muted">({{ $propertyType->Type }})</small>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <p class="text-muted">Không có loại bất động sản nào để hiển thị</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label">
                                                    <i class="fas fa-dollar-sign text-gold me-2"></i>Khoảng giá quan tâm
                                                </label>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control" name="price_range_min" 
                                                            placeholder="Giá từ (tỷ)" step="0.1" min="0"
                                                            value="{{ old('price_range_min', $preferences['price_range']['min'] ?? '') }}">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control" name="price_range_max" 
                                                            placeholder="Giá đến (tỷ)" step="0.1" min="0"
                                                            value="{{ old('price_range_max', $preferences['price_range']['max'] ?? '') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label">
                                                    <i class="fas fa-expand-arrows-alt text-gold me-2"></i>Khoảng diện tích quan tâm
                                                </label>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control" name="area_range_min" 
                                                            placeholder="Từ (m²)" min="0"
                                                            value="{{ old('area_range_min', $preferences['area_range']['min'] ?? '') }}">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" class="form-control" name="area_range_max" 
                                                            placeholder="Đến (m²)" min="0"
                                                            value="{{ old('area_range_max', $preferences['area_range']['max'] ?? '') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="form-label">
                                                    <i class="fas fa-bell text-gold me-2"></i>Cài đặt thông báo
                                                </label>
                                                <div class="notification-settings">
                                                    <div class="form-check form-switch mb-2">
                                                        <input class="form-check-input" type="checkbox" name="notification_email" 
                                                            id="notif_email" value="1"
                                                            {{ old('notification_email', $preferences['notifications']['email'] ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="notif_email">Thông báo email</label>
                                                    </div>
                                                    <div class="form-check form-switch mb-2">
                                                        <input class="form-check-input" type="checkbox" name="notification_sms" 
                                                            id="notif_sms" value="1"
                                                            {{ old('notification_sms', $preferences['notifications']['sms'] ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="notif_sms">Thông báo SMS</label>
                                                    </div>
                                                    <div class="form-check form-switch mb-2">
                                                        <input class="form-check-input" type="checkbox" name="notification_new_properties" 
                                                            id="notif_new" value="1"
                                                            {{ old('notification_new_properties', $preferences['notifications']['new_properties'] ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="notif_new">BĐS mới phù hợp</label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="notification_price_changes" 
                                                            id="notif_price" value="1"
                                                            {{ old('notification_price_changes', $preferences['notifications']['price_changes'] ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="notif_price">Thay đổi giá</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 4: Bảo mật -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <div class="card section-card">
                                <div class="card-header">
                                    <div class="header-title">
                                        <h5 class="mb-1">
                                            <i class="fas fa-lock text-gold me-2"></i>Bảo mật tài khoản
                                        </h5>
                                        <p class="text-muted mb-0">Quản lý mật khẩu và các cài đặt bảo mật</p>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="security-item card border-0 shadow-sm mb-3">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <i class="fas fa-key text-gold me-2"></i>Mật khẩu
                                                            </h6>
                                                            <small class="text-muted">Thay đổi mật khẩu đăng nhập</small>
                                                        </div>                                                        
                                                            <a href="{{ route('customer.change-password') }}" class="btn btn-outline-gold btn-sm">
                                                            <i class="fas fa-edit me-1"></i>Đổi mật khẩu
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="security-item card border-0 shadow-sm mb-3">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1">
                                                                <i class="fas fa-shield-alt text-gold me-2"></i>Xác thức 2 lớp
                                                            </h6>
                                                            <small class="text-muted">Tăng cường bảo mật tài khoản</small>
                                                        </div>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="two-factor">
                                                            <label class="form-check-label" for="two-factor"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr class="my-4">
                                    
                                    <div class="privacy-settings">
                                        <h6 class="mb-3">
                                            <i class="fas fa-user-secret text-gold me-2"></i>Cài đặt riêng tư
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="profile-public" checked>
                                                    <label class="form-check-label" for="profile-public">
                                                        Hiển thị thông tin công khai
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="contact-agents" checked>
                                                    <label class="form-check-label" for="contact-agents">
                                                        Cho phép môi giới liên hệ
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </button>
                                <button type="submit" class="btn btn-gold btn-lg">
                                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Gold Color Theme */
:root {
    --gold-primary: #FFD700;
    --gold-secondary: #DAA520;
    --gold-dark: #B8860B;
    --gold-light: #FFF8DC;
    --gold-accent: #F4A460;
    --shadow-gold: rgba(218, 165, 32, 0.15);
}

/* Profile Container */
.profile-container {
    background: linear-gradient(135deg, var(--gold-light) 0%, #ffffff 100%);
    min-height: 100vh;
}

/* Professional Header */
.profile-header-card {
    background: linear-gradient(135deg, var(--gold-secondary) 0%, var(--gold-dark) 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px var(--shadow-gold);
}

.header-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='m36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
    opacity: 0.1;
}

.header-content {
    position: relative;
    z-index: 2;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
}

.badge-verified {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Profile Summary Card */
.profile-summary-card {
    background: white;
    border: none;
    border-radius: 20px;
    box-shadow: 0 15px 35px var(--shadow-gold);
    transition: transform 0.3s ease;
}

.profile-summary-card:hover {
    transform: translateY(-5px);
}

.avatar-container {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid var(--gold-secondary);
    background: var(--gold-light);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 8px 25px var(--shadow-gold);
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-secondary) 100%);
    color: white;
}

.initials {
    font-size: 1.5rem;
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.avatar-status {
    position: absolute;
    bottom: -2px;
    right: -2px;
    background: #28a745;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    border: 2px solid white;
}

.profile-name {
    color: var(--gold-dark);
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.profile-email {
    font-size: 0.9rem;
    color: #6c757d;
}

.text-gold {
    color: var(--gold-secondary) !important;
}

/* Navigation Card */
.navigation-card {
    background: white;
    border: none;
    border-radius: 20px;
    box-shadow: 0 15px 35px var(--shadow-gold);
}

.profile-nav .nav-link {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    color: #495057;
    text-decoration: none;
    border: none;
    border-radius: 0;
    transition: all 0.3s ease;
    position: relative;
    border-bottom: 1px solid #f8f9fa;
}

.profile-nav .nav-link:last-child {
    border-bottom: none;
}

.profile-nav .nav-link:hover {
    background: linear-gradient(90deg, var(--gold-light) 0%, transparent 100%);
    color: var(--gold-dark);
    transform: translateX(5px);
}

.profile-nav .nav-link.active {
    background: linear-gradient(90deg, var(--gold-secondary) 0%, var(--gold-primary) 100%);
    color: white;
    box-shadow: 0 5px 15px var(--shadow-gold);
}

.nav-icon {
    width: 40px;
    height: 40px;
    background: rgba(218, 165, 32, 0.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    transition: all 0.3s ease;
}

.profile-nav .nav-link.active .nav-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.nav-content {
    flex: 1;
}

.nav-title {
    font-weight: 600;
    font-size: 1rem;
    display: block;
}

.nav-desc {
    font-size: 0.8rem;
    opacity: 0.7;
    display: block;
    margin-top: 0.2rem;
}

.nav-arrow {
    opacity: 0.5;
    transition: all 0.3s ease;
}

.profile-nav .nav-link:hover .nav-arrow {
    opacity: 1;
    transform: translateX(3px);
}

/* Section Cards */
.section-card {
    background: white;
    border: none;
    border-radius: 20px;
    box-shadow: 0 15px 35px var(--shadow-gold);
    margin-bottom: 2rem;
}

.section-card .card-header {
    background: linear-gradient(90deg, var(--gold-light) 0%, transparent 100%);
    border-radius: 20px 20px 0 0 !important;
    border-bottom: 3px solid var(--gold-accent);
    padding: 1.5rem 2rem;
}

.header-title h5 {
    color: var(--gold-dark);
    font-weight: 700;
    margin: 0;
}

/* Enhanced Avatar Upload */
.avatar-upload-section {
    background: linear-gradient(135deg, var(--gold-light) 0%, #ffffff 100%);
    border-radius: 15px;
    padding: 2rem;
    border: 2px dashed var(--gold-accent);
}

.avatar-preview-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid var(--gold-secondary);
    background: var(--gold-light);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 10px 30px var(--shadow-gold);
    transition: all 0.3s ease;
    cursor: pointer;
}

.avatar-preview-large:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 40px var(--shadow-gold);
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
    font-size: 1.5rem;
}

.avatar-preview-large:hover .avatar-overlay {
    opacity: 1;
}

/* Form Controls */
.form-label {
    color: var(--gold-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.form-label i {
    color: var(--gold-secondary);
    width: 20px;
}

.required {
    color: #dc3545;
    font-weight: bold;
}

.form-control, .form-select {
    border-radius: 12px;
    border: 2px solid #e9ecef;
    padding: 0.875rem 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.95rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--gold-secondary);
    box-shadow: 0 0 0 0.2rem rgba(218, 165, 32, 0.25);
    background-color: var(--gold-light);
    transform: translateY(-2px);
}

.form-control:hover, .form-select:hover {
    border-color: var(--gold-accent);
}

/* Buttons */
.btn {
    border-radius: 12px;
    padding: 0.875rem 2rem;
    font-weight: 600;
    text-transform: none;
    letter-spacing: 0.3px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-gold {
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary));
    border: 2px solid var(--gold-secondary);
    color: #333;
    font-weight: 700;
}

.btn-gold:hover {
    background: linear-gradient(135deg, var(--gold-secondary), var(--gold-dark));
    border-color: var(--gold-dark);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px var(--shadow-gold);
}

.btn-outline-gold {
    border: 2px solid var(--gold-secondary);
    color: var(--gold-dark);
    font-weight: 600;
    background: transparent;
}

.btn-outline-gold:hover {
    background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary));
    border-color: var(--gold-secondary);
    color: #333;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px var(--shadow-gold);
}

/* Alert Messages */
.alert-modern {
    border: none;
    border-radius: 15px;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.alert-icon {
    font-size: 1.5rem;
    margin-right: 1rem;
}

.alert-content {
    flex: 1;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #ffffff 100%);
    border-left: 4px solid #28a745;
    color: #155724;
}

.alert-success .alert-icon {
    color: #28a745;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #ffffff 100%);
    border-left: 4px solid #dc3545;
    color: #721c24;
}

.alert-danger .alert-icon {
    color: #dc3545;
}

/* Property Types Checkboxes */
.property-types-checkboxes {
    max-height: 200px;
    overflow-y: auto;
    padding: 0.5rem;
    background: var(--gold-light);
    border-radius: 10px;
}

.form-check {
    padding: 0.5rem 0.75rem;
    margin: 0.25rem 0;
    border-radius: 8px;
    transition: background-color 0.2s ease;
}

.form-check:hover {
    background: rgba(218, 165, 32, 0.1);
}

.form-check-input:checked {
    background-color: var(--gold-secondary);
    border-color: var(--gold-secondary);
}

/* Security Items */
.security-item .card {
    transition: all 0.3s ease;
}

.security-item .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px var(--shadow-gold) !important;
}

/* Responsive Design */
@media (max-width: 992px) {
    .profile-nav .nav-link {
        padding: 0.875rem 1.25rem;
    }
    
    .nav-icon {
        width: 35px;
        height: 35px;
        margin-right: 0.75rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    .profile-header-card {
        padding: 1.5rem;
    }
    
    .avatar-container {
        width: 70px;
        height: 70px;
    }
    
    .avatar-preview-large {
        width: 100px;
        height: 100px;
    }
    
    .initials {
        font-size: 1.2rem;
    }
    
    .section-card .card-body {
        padding: 1.5rem;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
    }
    
    .profile-nav .nav-link {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
    }
    
    .nav-icon {
        margin-right: 0;
        margin-bottom: 0.5rem;
    }
}

/* Animation Effects */
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

.section-card {
    animation: fadeInUp 0.6s ease-out;
}

.section-card:nth-child(2) {
    animation-delay: 0.1s;
}

.section-card:nth-child(3) {
    animation-delay: 0.2s;
}

/* Notification Settings */
.notification-settings .form-check {
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid var(--gold-accent);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
}

.notification-settings .form-check:hover {
    background: var(--gold-light);
    border-color: var(--gold-secondary);
    transform: translateX(5px);
}

.form-switch .form-check-input:checked {
    background-color: var(--gold-secondary);
    border-color: var(--gold-secondary);
}

/* Upload Info */
.upload-info {
    margin-top: 0.75rem;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 8px;
    border-left: 3px solid var(--gold-accent);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Avatar upload preview
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');
    const avatarSummary = document.getElementById('avatar-preview-summary');
    
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
                    const imgHtml = `<img src="${e.target.result}" alt="Avatar Preview" class="avatar-img">`;
                    avatarPreview.innerHTML = imgHtml + '<div class="avatar-overlay"><i class="fas fa-camera"></i></div>';
                    if (avatarSummary) {
                        avatarSummary.innerHTML = imgHtml + '<div class="avatar-status"><i class="fas fa-check-circle"></i></div>';
                    }
                };
                reader.onerror = function() {
                    alert('Có lỗi khi đọc file. Vui lòng thử lại.');
                    avatarInput.value = '';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 10) {
                value = value.slice(0, 10);
            }
            e.target.value = value;
        });
    }

    // Identity card formatting
    const identityInput = document.getElementById('identity_card');
    if (identityInput) {
        identityInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 12) {
                value = value.slice(0, 12);
            }
            e.target.value = value;
        });
    }

    // Form validation
    const form = document.querySelector('.needs-validation');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }

    // Tab switching with smooth transition
    const tabLinks = document.querySelectorAll('.profile-nav .nav-link');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links and panes
            tabLinks.forEach(l => l.classList.remove('active'));
            tabPanes.forEach(p => {
                p.classList.remove('show', 'active');
            });
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Show corresponding pane with animation
            const targetId = this.getAttribute('href');
            const targetPane = document.querySelector(targetId);
            if (targetPane) {
                setTimeout(() => {
                    targetPane.classList.add('show', 'active');
                }, 150);
            }
        });
    });

    // Auto-save draft (optional feature)
    let autoSaveTimer;
    const formInputs = form ? form.querySelectorAll('input, select, textarea') : [];
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Could implement auto-save to localStorage here
                console.log('Auto-saving draft...');
            }, 2000);
        });
    });
});
</script>
@endsection
