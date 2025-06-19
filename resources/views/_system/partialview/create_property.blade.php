@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Thành công!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

<style>
    /* Enhanced Mint Green Theme Styling */

    /* Card and Container Styling */
    .card {
        border: 1px solid #19f5c5;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(25, 245, 197, 0.15);
        background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
    }

    .card-header {
        background: linear-gradient(135deg, #19f5c5 0%, #3498db 100%);
        border-bottom: 1px solid #19f5c5;
        border-radius: 11px 11px 0 0 !important;
        color: white;
        padding: 15px 20px;
    }

    .card-header h4 {
        margin: 0;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
        border-radius: 0 0 11px 11px;
    }

    /* Autocomplete Styles */
    .autocomplete-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
        border: 1px solid #19f5c5;
        border-top: none;
        border-radius: 0 0 8px 8px;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(25, 245, 197, 0.2);
        display: none;
    }

    .autocomplete-item {
        padding: 12px 15px;
        cursor: pointer;
        border-bottom: 1px solid #e0f2fe;
        transition: all 0.3s ease;
    }

    .autocomplete-item:hover {
        background: linear-gradient(135deg, #e6fffe 0%, #ccfff8 100%);
        transform: translateX(4px);
        border-left: 3px solid #19f5c5;
    }

    .autocomplete-item:last-child {
        border-bottom: none;
    }

    .autocomplete-item.no-results {
        color: #6c757d;
        font-style: italic;
        cursor: default;
        text-align: center;
    }

    .autocomplete-item.error {
        color: #dc3545;
        cursor: default;
        text-align: center;
    }

    .owner-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 4px;
    }

    .owner-details {
        font-size: 0.9em;
        color: #3498db;
    }

    /* Enhanced form styling */
    .position-relative {
        position: relative;
    }

    .form-control:focus + .autocomplete-results {
        border-color: #19f5c5;
    }

    /* Form Controls Enhancement */
    .form-control, .form-select {
        border: 1px solid #e0f2fe;
        border-radius: 8px;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
    }

    .form-control:focus, .form-select:focus {
        border-color: #19f5c5;
        box-shadow: 0 0 0 0.2rem rgba(25, 245, 197, 0.25);
        background: linear-gradient(135deg, #ffffff 0%, #e6fffe 100%);
    }

    .form-control:hover, .form-select:hover {
        border-color: #3498db;
    }

    /* Form Labels */
    .col-form-label {
        font-weight: 600;
        color: #2c3e50;
    }

    /* Input Groups */
    .input-group-text {
        background: linear-gradient(135deg, #19f5c5 0%, #3498db 100%);
        border: 1px solid #19f5c5;
        color: white;
        font-weight: 500;
    }

    /* Tab content styling improvements */
    .tab-content {
        min-height: 500px;
    }

    .tab-pane {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Alert Styling */
    .alert {
        border-radius: 8px;
        border: none;
    }

    .alert-success {
        background: linear-gradient(135deg, #e6fffe 0%, #ccfff8 100%);
        border-left: 4px solid #19f5c5;
        color: #2c3e50;
    }

    .alert-info {
        background: linear-gradient(135deg, #e6f3ff 0%, #cce7ff 100%);
        border-left: 4px solid #3498db;
        color: #2c3e50;
    }

    .alert-danger {
        background: linear-gradient(135deg, #ffe6e6 0%, #ffcccc 100%);
        border-left: 4px solid #dc3545;
        color: #721c24;
    }

    /* Owner info display styling */
    #selectedOwnerInfo .alert {
        border-left: 4px solid #19f5c5;
    }

    #ownerInfoDisplay i {
        width: 20px;
        color: #19f5c5;
    }

    /* Form Check Styling */
    .form-check-input:checked {
        background-color: #19f5c5;
        border-color: #19f5c5;
    }

    .form-check-input:focus {
        border-color: #19f5c5;
        box-shadow: 0 0 0 0.2rem rgba(25, 245, 197, 0.25);
    }

    .form-check-label {
        color: #2c3e50;
        font-weight: 500;
    }

    .form-check-label i {
        margin-right: 8px;
        width: 16px;
    }

    /* Enhanced Radio Button Styling */
    .form-check-inline {
        margin-right: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .form-check-inline .form-check-input {
        margin-top: 0.25rem;
    }

    .form-check-inline .form-check-label {
        padding-left: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-check-inline .form-check-label:hover {
        color: #19f5c5;
        transform: translateX(2px);
    }

    /* Form Text Enhancement */
    .form-text.text-muted {
        color: #3498db !important;
        font-weight: 500;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .form-text.text-muted i {
        color: #19f5c5;
        margin-right: 0.25rem;
    }

    /* Loading state */
    .autocomplete-loading {
        padding: 12px 15px;
        text-align: center;
        color: #3498db;
    }

    .autocomplete-loading::after {
        content: '';
        display: inline-block;
        width: 16px;
        height: 16px;
        margin-left: 8px;
        border: 2px solid #19f5c5;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Enhanced Button Styling */
    .btn {
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 500;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn:hover::before {
        left: 100%;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }

    .btn-primary {
        background: linear-gradient(135deg, #3498db 0%, #19f5c5 100%);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2980b9 0%, #00e5b3 100%);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
    }

    .btn-success {
        background: linear-gradient(135deg, #19f5c5 0%, #00d4aa 100%);
        color: white;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #00e5b3 0%, #00c299 100%);
        box-shadow: 0 6px 20px rgba(25, 245, 197, 0.5);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        color: white;
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #5a6268 0%, #495057 100%);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
    }

    .btn-outline-secondary {
        border: 2px solid #6c757d;
        color: #6c757d;
        background: transparent;
    }

    .btn-outline-secondary:hover {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        color: white;
        border-color: #5a6268;
    }

    /* Text Colors Enhancement */
    .text-primary {
        color: #3498db !important;
    }

    .text-success {
        color: #19f5c5 !important;
    }

    .text-info {
        color: #17a2b8 !important;
    }

    .text-warning {
        color: #ffc107 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    /* Border Enhancements */
    .border-primary {
        border-color: #19f5c5 !important;
    }

    .border-success {
        border-color: #19f5c5 !important;
    }

    /* File Input Styling */
    .form-control[type="file"] {
        padding: 8px 12px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
        border: 2px dashed #19f5c5;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .form-control[type="file"]:hover {
        border-color: #3498db;
        background: linear-gradient(135deg, #e6fffe 0%, #ccfff8 100%);
    }

    /* Small Text Enhancements */
    .form-text.text-muted {
        color: #3498db !important;
        font-weight: 500;
    }

    .text-muted {
        color: #6c757d !important;
    }

    /* Map Styling */
    .map-container {
        border: 2px solid #19f5c5;
        border-radius: 12px;
        overflow: hidden;
        background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
        box-shadow: 0 4px 12px rgba(25, 245, 197, 0.2);
    }

    .property-map {
        height: 300px;
        width: 100%;
        border-radius: 10px;
        background: #f0f8ff;
        position: relative;
    }

    .map-info {
        padding: 8px 12px;
        background: linear-gradient(135deg, #e6fffe 0%, #ccfff8 100%);
        border-top: 1px solid #19f5c5;
        text-align: center;
    }

    .map-info small {
        color: #2c3e50;
        font-weight: 500;
    }

    .map-info i {
        color: #19f5c5;
        margin-right: 5px;
    }

    /* Leaflet Map Custom Styling */
    .leaflet-container {
        font-family: inherit;
        border-radius: 10px;
    }

    .leaflet-popup-content-wrapper {
        background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
        border: 2px solid #19f5c5;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(25, 245, 197, 0.3);
    }

    .leaflet-popup-content {
        color: #2c3e50;
        font-weight: 500;
    }

    .leaflet-popup-tip {
        background: #19f5c5;
    }

    /* Custom marker styling */
    .custom-marker {
        background: linear-gradient(135deg, #19f5c5 0%, #3498db 100%);
        border: 2px solid white;
        border-radius: 50%;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    /* Address display styling */
    #addressInfo {
        transition: all 0.3s ease;
        border-left: 4px solid #19f5c5 !important;
    }

    #addressInfo.show {
        animation: slideInRight 0.3s ease;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Transaction type display */
    #transactionTypeDisplay {
        border-left: 4px solid #3498db;
        background: linear-gradient(135deg, #e6f3ff 0%, #cce7ff 100%);
        animation: fadeInDown 0.3s ease;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Enhancements */
    @media (max-width: 768px) {
        .nav-tabs .nav-link {
            font-size: 0.9rem;
            padding: 8px 12px;
        }

        .card-body {
            padding: 15px;
        }

        .btn {
            font-size: 0.9rem;
        }
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tạo mới bất động sản</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.property.store') }}" method="POST" enctype="multipart/form-data" class="form-horizontal">
                        @csrf

                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs" id="propertyTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="owner-tab" data-bs-toggle="tab" data-bs-target="#owner" type="button" role="tab" aria-controls="owner" aria-selected="true">
                                    <i class="fas fa-user-tie me-2"></i>Thông tin chủ sở hữu
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab" aria-controls="basic" aria-selected="false">
                                    <i class="fas fa-home me-2"></i>Thông tin cơ bản
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="false">
                                    <i class="fas fa-info-circle me-2"></i>Chi tiết BĐS
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media" type="button" role="tab" aria-controls="media" aria-selected="false">
                                    <i class="fas fa-photo-video me-2"></i>Video và hình ảnh
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="propertyTabsContent">
                            <!-- Tab 1: Thông tin chủ sở hữu -->
                            <div class="tab-pane fade show active" id="owner" role="tabpanel" aria-labelledby="owner-tab">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Chủ sở hữu <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <div class="position-relative">
                                                    <input type="text" id="ownerSearch" class="form-control" placeholder="Tìm kiếm chủ sở hữu theo tên hoặc số điện thoại...">
                                                    <input type="hidden" name="selectedOwnerId" id="selectedOwnerId" required>
                                                    <div id="ownerSearchResults" class="autocomplete-results"></div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <small class="text-muted">Nhập ít nhất 2 ký tự để tìm kiếm</small>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="createNewOwner()">
                                                        <i class="fas fa-plus"></i> Tạo mới chủ sở hữu
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="selectedOwnerInfo" class="form-group row mb-3" style="display: none;">
                                            <label class="col-sm-3 col-form-label">Thông tin đã chọn</label>
                                            <div class="col-sm-9">
                                                <div class="alert alert-success mb-0">
                                                    <div id="ownerInfoDisplay"></div>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="clearOwnerSelection()">
                                                        <i class="fas fa-times"></i> Chọn lại
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Loại giao dịch <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="TypePro" id="typeSale" value="Sale" {{ old('TypePro') == 'Sale' ? 'checked' : '' }} required>
                                                            <label class="form-check-label" for="typeSale">
                                                                <i class="fas fa-handshake text-primary"></i> Bán
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="TypePro" id="typeRent" value="Rent" {{ old('TypePro') == 'Rent' ? 'checked' : '' }} required>
                                                            <label class="form-check-label" for="typeRent">
                                                                <i class="fas fa-key text-success"></i> Cho thuê
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Danh mục <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select name="PropertyType" class="form-control" required>
                                                    <option value="">-- Chọn danh mục --</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->Protype_ID }}" {{ old('PropertyType') == $category->Protype_ID ? 'selected' : '' }}>
                                                            {{ $category->ten_pro }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-info">
                                            <h6><i class="fas fa-info-circle"></i> Hướng dẫn</h6>
                                            <p class="mb-1">1. Chọn chủ sở hữu từ danh sách</p>
                                            <p class="mb-1">2. Xác định loại giao dịch</p>
                                            <p class="mb-0">3. Chọn danh mục bất động sản</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-3">
                                    <button type="button" class="btn btn-primary" onclick="nextTab('#basic-tab')">
                                        Tiếp theo <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Tab 2: Thông tin cơ bản -->
                            <div class="tab-pane fade" id="basic" role="tabpanel" aria-labelledby="basic-tab">
                                <!-- Hiển thị loại giao dịch đã chọn -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="alert alert-info" id="transactionTypeDisplay" style="display: none;">
                                            <h6 class="mb-0">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Loại giao dịch: <span id="transactionTypeText" class="fw-bold text-primary"></span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Tiêu đề <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="Title" class="form-control" value="{{ old('Title') }}" required placeholder="Nhập tiêu đề bất động sản">
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Giá <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                    <input type="number" name="Price" class="form-control" value="{{ old('Price') }}" required placeholder="0">
                                                    <span class="input-group-text">VNĐ</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Địa chỉ <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="Address" class="form-control" value="{{ old('Address') }}" required placeholder="Số nhà, tên đường">
                                            </div>
                                        </div>

                                        <!-- Sắp xếp lại: Tỉnh -> Quận/Huyện -> Phường/Xã -->
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <select name="Province" id="province" class="form-control" required>
                                                    <option value="">Chọn tỉnh/thành phố</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-6 col-form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                                    <div class="col-sm-6">
                                                        <select name="District" id="district" class="form-control" required>
                                                            <option value="">Chọn quận/huyện</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-6 col-form-label">Phường/Xã <span class="text-danger">*</span></label>
                                                    <div class="col-sm-6">
                                                        <select name="Ward" id="ward" class="form-control" required>
                                                            <option value="">Chọn phường/xã</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Mô tả <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <textarea name="Description" class="form-control" rows="5" required placeholder="Mô tả chi tiết về bất động sản">{{ old('Description') }}</textarea>
                                            </div>
                                        </div>

                                        <!-- Bản đồ hiển thị vị trí -->
                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Vị trí trên bản đồ</label>
                                            <div class="col-sm-9">
                                                <div id="map-container" class="map-container">
                                                    <div id="property-map" class="property-map"></div>
                                                    <div class="map-info">
                                                        <small class="text-muted">
                                                            <i class="fas fa-info-circle"></i>
                                                            Bản đồ sẽ tự động cập nhật khi bạn chọn địa chỉ
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-warning">
                                            <h6><i class="fas fa-exclamation-triangle"></i> Lưu ý</h6>
                                            <p class="mb-1">• Tiêu đề phải rõ ràng, hấp dẫn với loại bán, thuê </p>
                                            <p class="mb-1">• Giá cả phải chính xác</p>
                                            <p class="mb-1">• Địa chỉ phải đầy đủ và chính xác tránh ghi sai</p>
                                            <p class="mb-0">• Mô tả chi tiết sẽ thu hút khách hàng</p>
                                        </div>

                                        <!-- Thông tin địa chỉ đã chọn -->
                                        <div class="alert alert-success" id="addressInfo" style="display: none;">
                                            <h6><i class="fas fa-map-marker-alt"></i> Địa chỉ đã chọn</h6>
                                            <div id="fullAddressDisplay"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-secondary" onclick="prevTab('#owner-tab')">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="nextTab('#details-tab')">
                                        Tiếp theo <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Tab 3: Chi tiết BĐS -->
                            <div class="tab-pane fade" id="details" role="tabpanel" aria-labelledby="details-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3"><i class="fas fa-building me-2"></i>Thông số kỹ thuật</h6>

                                        <div class="row">
                                            <div class="col-6">                                        <div class="form-group mb-3">
                                            <label class="form-label">Số tầng / Nhà Cấp</label>
                                            <input type="number" name="LevelHouse" class="form-control" value="{{ old('LevelHouse') }}"  min="0" max="5">
                                            <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Chỉ có nhà, văn phòng, shophouse, kho mới điền, nếu khác bỏ trống</small>
                                        </div>
                                            </div>
                                            <div class="col-6">                                        <div class="form-group mb-3">
                                            <label class="form-label">Lầu</label>
                                            <input type="number" name="Floor" class="form-control" value="{{ old('Floor') }}" min="0">
                                            <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Chỉ có chung cư mới điền, nếu khác bỏ trống</small>
                                        </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Chiều dài nhà (m)</label>
                                                    <input type="number" name="HouseLength" class="form-control" value="{{ old('HouseLength') }}" min="0">
                                                    <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Chiều dài của ngôi nhà</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Chiều rộng nhà (m)</label>
                                                    <input type="number" name="HouseWidth" class="form-control" value="{{ old('HouseWidth') }}" min="0">
                                                    <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Chiều rộng của ngôi nhà</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Chiều dài tổng thể (m)</label>
                                                    <input type="number" name="TotalLength" class="form-control" value="{{ old('TotalLength') }}" min="0">
                                                    <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Chiều dài của toàn bộ lô đất</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Chiều rộng tổng thể (m)</label>
                                                    <input type="number" name="TotalWidth" class="form-control" value="{{ old('TotalWidth') }}" min="0">
                                                    <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Chiều rộng của toàn bộ lô đất</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Số phòng ngủ</label>
                                                    <input type="number" name="Bedroom" class="form-control" value="{{ old('Bedroom') }}" min="0">
                                                    <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Tổng số phòng ngủ</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Số phòng WC</label>
                                                    <input type="number" name="Bath_WC" class="form-control" value="{{ old('Bath_WC') }}" min="0">
                                                    <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Tổng số phòng tắm/WC</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Ban công</label>
                                                    <select name="Balcony" class="form-control">
                                                        <option value="1" {{ old('Balcony') == '1' ? 'selected' : '' }}>Có</option>
                                                        <option value="0" {{ old('Balcony') == '0' ? 'selected' : '' }}>Không</option>
                                                    </select>
                                                    <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Có ban công hay không</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Đường vào (m)</label>
                                                    <input type="number" name="Road" class="form-control" value="{{ old('Road') }}" min="0">
                                                    <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Độ rộng của đường vào</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-success mb-3"><i class="fas fa-compass me-2"></i>Thông tin bổ sung</h6>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Pháp lý</label>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="legal" id="legal_sodo" value="Sổ đỏ" {{ old('legal') == 'Sổ đỏ' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="legal_sodo">
                                                            <i class="fas fa-book text-danger me-1"></i>Sổ đỏ
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="legal" id="legal_sohong" value="Sổ hồng" {{ old('legal') == 'Sổ hồng' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="legal_sohong">
                                                            <i class="fas fa-book text-success me-1"></i>Sổ hồng
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="legal" id="legal_khac" value="Khác" {{ old('legal') != 'Sổ đỏ' && old('legal') != 'Sổ hồng' && old('legal') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="legal_khac">
                                                            <i class="fas fa-edit text-warning me-1"></i>Khác
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-2" id="legalOtherInput" style="display: {{ old('legal') && old('legal') != 'Sổ đỏ' && old('legal') != 'Sổ hồng' ? 'block' : 'none' }};">
                                                <input type="text" name="legal_other" class="form-control" placeholder="Nhập loại pháp lý khác..." value="{{ old('legal') && old('legal') != 'Sổ đỏ' && old('legal') != 'Sổ hồng' ? old('legal') : '' }}">
                                            </div>
                                            <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Trạng thái pháp lý của bất động sản</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Hướng nhìn</label>
                                            <select name="view" class="form-control">
                                                <option value="">-- Chọn hướng --</option>
                                                <option value="Bắc" {{ old('view') == 'Bắc' ? 'selected' : '' }}>Bắc</option>
                                                <option value="Tây Bắc" {{ old('view') == 'Tây Bắc' ? 'selected' : '' }}>Tây Bắc</option>
                                                <option value="Tây" {{ old('view') == 'Tây' ? 'selected' : '' }}>Tây</option>
                                                <option value="Tây Nam" {{ old('view') == 'Tây Nam' ? 'selected' : '' }}>Tây Nam</option>
                                                <option value="Nam" {{ old('view') == 'Nam' ? 'selected' : '' }}>Nam</option>
                                                <option value="Đông Nam" {{ old('view') == 'Đông Nam' ? 'selected' : '' }}>Đông Nam</option>
                                                <option value="Đông" {{ old('view') == 'Đông' ? 'selected' : '' }}>Đông</option>
                                                <option value="Đông Bắc" {{ old('view') == 'Đông Bắc' ? 'selected' : '' }}>Đông Bắc</option>
                                            </select>
                                            <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Hướng nhìn chính của bất động sản</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Lân cận</label>
                                            <input type="text" name="near" class="form-control" value="{{ old('near') }}" placeholder="Trường học, bệnh viện, siêu thị...">
                                            <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Các địa điểm lân cận quan trọng như trường học, bệnh viện, siêu thị...</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Nội thất</label>
                                            <select name="Interior" class="form-control">
                                                <option value="">Không</option>
                                                <option value="Cơ bản" {{ old('Interior') == 'Cơ bản' ? 'selected' : '' }}>Cơ bản</option>
                                                <option value="Đầy đủ" {{ old('Interior') == 'Đầy đủ' ? 'selected' : '' }}>Đầy đủ</option>
                                            </select>
                                            <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Mức độ trang bị nội thất hiện có</small>
                                        </div>

                                        <h6 class="text-warning mb-3 mt-4"><i class="fas fa-bolt me-2"></i>Tiện ích</h6>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Giá nước</label>
                                            <select name="WaterPrice" class="form-control">
                                                <option value="">-- Chọn --</option>
                                                <option value="Thỏa thuận" {{ old('WaterPrice') == 'Thỏa thuận' ? 'selected' : '' }}>Thỏa thuận</option>
                                                <option value="Do chủ nhà quy định" {{ old('WaterPrice') == 'Do chủ nhà quy định' ? 'selected' : '' }}>Do chủ nhà quy định</option>
                                                <option value="Theo nhà nước" {{ old('WaterPrice') == 'Theo nhà nước' ? 'selected' : '' }}>Theo nhà nước</option>
                                            </select>
                                            <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Cách tính giá nước cho thuê nhà</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Giá điện</label>
                                            <select name="PowerPrice" class="form-control">
                                                <option value="">-- Chọn --</option>
                                                <option value="Thỏa thuận" {{ old('PowerPrice') == 'Thỏa thuận' ? 'selected' : '' }}>Thỏa thuận</option>
                                                <option value="Do chủ nhà quy định" {{ old('PowerPrice') == 'Do chủ nhà quy định' ? 'selected' : '' }}>Do chủ nhà quy định</option>
                                                <option value="Theo nhà nước" {{ old('PowerPrice') == 'Theo nhà nước' ? 'selected' : '' }}>Theo nhà nước</option>
                                            </select>
                                            <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Cách tính giá điện cho thuê nhà</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Tiện ích khác</label>
                                            <select name="Utilities" class="form-control">
                                                <option value="">-- Chọn --</option>
                                                <option value="Thỏa thuận" {{ old('Utilities') == 'Thỏa thuận' ? 'selected' : '' }}>Thỏa thuận</option>
                                                <option value="Do chủ nhà quy định" {{ old('Utilities') == 'Do chủ nhà quy định' ? 'selected' : '' }}>Do chủ nhà quy định</option>
                                            </select>
                                            <small class="form-text text-muted"><i class="fas fa-info-circle me-1"></i>Các tiện ích khác như internet, truyền hình, vệ sinh...</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <button type="button" class="btn btn-secondary" onclick="prevTab('#basic-tab')">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="nextTab('#media-tab')">
                                        Tiếp theo <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Tab 4: Video và hình ảnh -->
                            <div class="tab-pane fade" id="media" role="tabpanel" aria-labelledby="media-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0"><i class="fas fa-images me-2"></i>Hình ảnh bất động sản</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Chọn hình ảnh</label>
                                                    <input type="file" name="property_images[]" class="form-control" multiple accept="image/*" id="imageInput">
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle text-info"></i>
                                                        Có thể chọn nhiều hình ảnh (định dạng: jpg, png, jpeg)
                                                    </small>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label class="form-label">Mô tả hình ảnh</label>
                                                    <input type="text" name="image_caption" class="form-control" value="{{ old('image_caption') }}" placeholder="Mô tả chung cho tất cả hình ảnh">
                                                </div>

                                                <!-- Preview container for images -->
                                                <div id="imagePreview" class="mt-3"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card border-success">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0"><i class="fas fa-video me-2"></i>Video bất động sản</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">URL Video (YouTube, TikTok, v.v.)</label>
                                                    <div id="videoUrlContainer">
                                                        <div class="video-url-group mb-2">
                                                            <div class="input-group">
                                                                <input type="url" name="video_urls[]" class="form-control video-url-input" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('video_urls.0') }}">
                                                                <button type="button" class="btn btn-outline-danger remove-video-url" style="display: none;">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addVideoUrl">
                                                        <i class="fas fa-plus me-1"></i>Thêm URL video
                                                    </button>
                                                    <small class="form-text text-muted d-block mt-2">
                                                        <i class="fas fa-info-circle text-info"></i>
                                                        Hỗ trợ YouTube, TikTok, Vimeo và các URL video khác
                                                    </small>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label class="form-label">Mô tả video</label>
                                                    <input type="text" name="video_caption" class="form-control" value="{{ old('video_caption') }}" placeholder="Mô tả chung cho tất cả video">
                                                </div>

                                                <!-- Preview container for videos -->
                                                <div id="videoPreview" class="mt-3"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="alert alert-light border">
                                            <h6 class="text-dark"><i class="fas fa-lightbulb text-warning me-2"></i>Mẹo tải file hiệu quả</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="text-primary">Hình ảnh:</h6>
                                                    <ul class="text-muted mb-0">
                                                        <li>Kích thước tối đa: 5MB/file</li>
                                                        <li>Định dạng: JPG, PNG, JPEG</li>
                                                        <li>Độ phân giải khuyến nghị: 1200x800px</li>
                                                        <li>Chụp nhiều góc độ khác nhau</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-success">Video:</h6>
                                                    <ul class="text-muted mb-0">
                                                        <li>Hỗ trợ YouTube, TikTok, Vimeo</li>
                                                        <li>Copy và paste URL video vào ô input</li>
                                                        <li>Có thể thêm nhiều video khác nhau</li>
                                                        <li>URL phải hoạt động và công khai</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-secondary" onclick="prevTab('#details-tab')">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </button>
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-save me-2"></i>Tạo mới bất động sản
                                    </button>
                                </div>
                            </div>

                        </div> <!-- End tab-content -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs {
    border-bottom: 2px solid #19f5c5;
    margin-bottom: 0;
    background: linear-gradient(135deg, #f0f8ff 0%, #e6fffe 100%);
    border-radius: 8px 8px 0 0;
    padding: 8px 12px 0 12px;
}

.nav-tabs .nav-link {
    border-radius: 8px 8px 0 0;
    border: 2px solid #e0f2fe;
    transition: all 0.3s ease;
    color: #2c3e50;
    background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
    margin-right: 4px;
    font-weight: 500;
}

.nav-tabs .nav-link:hover {
    border-color: #19f5c5;
    background: linear-gradient(135deg, #e6fffe 0%, #ccfff8 100%);
    color: #2c3e50;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(25, 245, 197, 0.2);
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #19f5c5 0%, #3498db 100%);
    border-color: #19f5c5;
    color: white !important;
    box-shadow: 0 4px 12px rgba(25, 245, 197, 0.3);
}

.tab-pane {
    border: 1px solid #e0f2fe;
    border-top: none;
    padding: 20px;
    border-radius: 0 0 8px 8px;
    background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
    box-shadow: 0 2px 8px rgba(25, 245, 197, 0.1);
}

.form-check-input:checked {
    background: linear-gradient(135deg, #3498db 0%, #19f5c5 100%);
    border-color: #3498db;
}

.form-control:focus {
    border-color: #19f5c5;
    box-shadow: 0 0 8px rgba(25, 245, 197, 0.3);
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,.1);
    border-radius: 8px;
    border: 1px solid #e0f2fe;
}

.card-header.bg-primary {
    background: linear-gradient(135deg, #3498db 0%, #19f5c5 100%) !important;
}

.card-header.bg-success {
    background: linear-gradient(135deg, #19f5c5 0%, #00d4aa 100%) !important;
}

.alert {
    border-radius: 8px;
}

.alert-info {
    background: linear-gradient(135deg, #e6fffe 0%, #ccfff8 100%);
    border-color: #19f5c5;
    color: #2c3e50;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-color: #ffc107;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #b3e5d1 100%);
    border-color: #19f5c5;
}

.btn {
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,.2);
}

.btn-primary {
    background: linear-gradient(135deg, #3498db 0%, #19f5c5 100%);
    border-color: #3498db;
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2980b9 0%, #00e5b3 100%);
    border-color: #2980b9;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.btn-success {
    background: linear-gradient(135deg, #19f5c5 0%, #00d4aa 100%);
    border-color: #19f5c5;
}

.btn-success:hover {
    background: linear-gradient(135deg, #00e5b3 0%, #00c299 100%);
    border-color: #00d4aa;
    box-shadow: 0 4px 12px rgba(25, 245, 197, 0.4);
}

#imagePreview, #videoPreview {
    max-height: 300px;
    overflow-y: auto;
}

.preview-item {
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f9f9f9;
}

.preview-item img {
    max-width: 100px;
    max-height: 100px;
    object-fit: cover;
    border-radius: 4px;
}

.preview-item video {
    max-width: 150px;
    max-height: 100px;
    border-radius: 4px;
}
</style>

<script>
function nextTab(tabId) {
    document.querySelector(tabId).click();
}

function prevTab(tabId) {
    document.querySelector(tabId).click();
}

// Image preview functionality
document.getElementById('imageInput').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';

    for (let i = 0; i < e.target.files.length; i++) {
        const file = e.target.files[i];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'preview-item d-flex align-items-center';
                div.innerHTML = `
                    <img src="${e.target.result}" class="me-3">
                    <div>
                        <strong>${file.name}</strong><br>
                        <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                    </div>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    }
});

// Video URL functionality
document.getElementById('addVideoUrl').addEventListener('click', function() {
    const container = document.getElementById('videoUrlContainer');
    const newGroup = document.createElement('div');
    newGroup.className = 'video-url-group mb-2';
    newGroup.innerHTML = `
        <div class="input-group">
            <input type="url" name="video_urls[]" class="form-control video-url-input" placeholder="https://www.youtube.com/watch?v=...">
            <button type="button" class="btn btn-outline-danger remove-video-url">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(newGroup);

    // Update remove button visibility
    updateRemoveButtonVisibility();

    // Add event listener for the new input
    const newInput = newGroup.querySelector('.video-url-input');
    newInput.addEventListener('input', updateVideoPreview);

    // Add event listener for remove button
    newGroup.querySelector('.remove-video-url').addEventListener('click', function() {
        newGroup.remove();
        updateRemoveButtonVisibility();
        updateVideoPreview();
    });
});

// Function to update remove button visibility
function updateRemoveButtonVisibility() {
    const groups = document.querySelectorAll('.video-url-group');
    groups.forEach((group, index) => {
        const removeBtn = group.querySelector('.remove-video-url');
        if (groups.length > 1) {
            removeBtn.style.display = 'block';
        } else {
            removeBtn.style.display = 'none';
        }
    });
}

// Function to get video embed URL
function getVideoEmbedUrl(url) {
    // YouTube
    const youtubeRegex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
    const youtubeMatch = url.match(youtubeRegex);
    if (youtubeMatch) {
        return `https://www.youtube.com/embed/${youtubeMatch[1]}`;
    }

    // TikTok
    const tiktokRegex = /(?:tiktok\.com\/)(?:.*\/video\/|@[^\/]+\/video\/)(\d+)/;
    const tiktokMatch = url.match(tiktokRegex);
    if (tiktokMatch) {
        return `https://www.tiktok.com/embed/v2/${tiktokMatch[1]}`;
    }

    // Vimeo
    const vimeoRegex = /(?:vimeo\.com\/)(\d+)/;
    const vimeoMatch = url.match(vimeoRegex);
    if (vimeoMatch) {
        return `https://player.vimeo.com/video/${vimeoMatch[1]}`;
    }

    return null;
}

// Function to update video preview
function updateVideoPreview() {
    const preview = document.getElementById('videoPreview');
    const inputs = document.querySelectorAll('.video-url-input');
    preview.innerHTML = '';

    inputs.forEach((input, index) => {
        const url = input.value.trim();
        if (url) {
            const embedUrl = getVideoEmbedUrl(url);
            const div = document.createElement('div');
            div.className = 'preview-item d-flex align-items-center mb-3 p-3 border rounded';

            if (embedUrl) {
                div.innerHTML = `
                    <iframe width="200" height="120" src="${embedUrl}" frameborder="0" allowfullscreen class="me-3"></iframe>
                    <div>
                        <strong>Video ${index + 1}</strong><br>
                        <small class="text-muted">${url}</small>
                    </div>
                `;
            } else {
                div.innerHTML = `
                    <div class="me-3 d-flex align-items-center justify-content-center" style="width: 200px; height: 120px; background-color: #f8f9fa; border: 2px dashed #dee2e6;">
                        <i class="fas fa-video fa-2x text-muted"></i>
                    </div>
                    <div>
                        <strong>Video ${index + 1}</strong><br>
                        <small class="text-muted">${url}</small><br>
                        <small class="text-warning">URL không được hỗ trợ preview</small>
                    </div>
                `;
            }
            preview.appendChild(div);
        }
    });
}

// Add event listeners to existing inputs
document.querySelectorAll('.video-url-input').forEach(input => {
    input.addEventListener('input', updateVideoPreview);
});

// Initialize remove button visibility
updateRemoveButtonVisibility();

// Form validation when switching tabs
document.addEventListener('DOMContentLoaded', function() {
    // Add validation for required fields when switching tabs
    const requiredFieldsByTab = {
        '#owner-tab': ['selectedOwnerId', 'TypePro', 'PropertyType'],
        '#basic-tab': ['Title', 'Price', 'Address', 'Ward', 'District', 'Province', 'Description'],
        '#details-tab': [], // No required fields in details tab
        '#media-tab': [] // No required fields in media tab
    };

    function validateTabFields(tabId) {
        const fields = requiredFieldsByTab[tabId] || [];
        let isValid = true;

        fields.forEach(fieldName => {
            let field;
            if (fieldName === 'selectedOwnerId') {
                field = document.getElementById('selectedOwnerId');
                const ownerSearchInput = document.getElementById('ownerSearch');
                if (!field.value.trim()) {
                    ownerSearchInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    ownerSearchInput.classList.remove('is-invalid');
                }
            } else {
                field = document.querySelector(`[name="${fieldName}"]`);
                if (field && !field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else if (field) {
                    field.classList.remove('is-invalid');
                }
            }
        });

        return isValid;
    }

    // Override nextTab function to include validation
    window.nextTab = function(tabId) {
        const currentTab = document.querySelector('.nav-link.active').getAttribute('data-bs-target');
        if (validateTabFields(currentTab)) {
            document.querySelector(tabId).click();
        } else {
            alert('Vui lòng điền đầy đủ thông tin bắt buộc trước khi chuyển tab!');
        }
    };
});

// Autocomplete functionality for owner search
let ownerSearchTimeout;

document.addEventListener('DOMContentLoaded', function() {
    const ownerSearchInput = document.getElementById('ownerSearch');
    const ownerSearchResults = document.getElementById('ownerSearchResults');
    const selectedOwnerIdInput = document.getElementById('selectedOwnerId');
    const selectedOwnerInfo = document.getElementById('selectedOwnerInfo');
    const ownerInfoDisplay = document.getElementById('ownerInfoDisplay');

    // Initialize form restoration and owner selection
    restoreFormData();
    handleOwnerCreationReturn();

    // Search owners
    ownerSearchInput.addEventListener('input', function() {
        const query = this.value.trim();

        clearTimeout(ownerSearchTimeout);

        if (query.length < 2) {
            ownerSearchResults.innerHTML = '';
            ownerSearchResults.style.display = 'none';
            return;
        }

        ownerSearchTimeout = setTimeout(() => {
            // Show loading state
            ownerSearchResults.innerHTML = '<div class="autocomplete-loading">Đang tìm kiếm...</div>';
            ownerSearchResults.style.display = 'block';

            fetch(`{{ route('admin.owners.search') }}?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data);
                })
                .catch(error => {
                    console.error('Error searching owners:', error);
                    ownerSearchResults.innerHTML = '<div class="autocomplete-item error"><i class="fas fa-exclamation-triangle"></i> Lỗi khi tìm kiếm</div>';
                    ownerSearchResults.style.display = 'block';
                });
        }, 300);
    });

    // Handle TypePro radio button change to show/hide utilities section
    const typeProRadios = document.querySelectorAll('input[name="TypePro"]');
    const utilitiesSection = document.querySelector('h6.text-warning');

    // Find the utilities section and its parent container
    let utilitiesContainer = null;
    if (utilitiesSection && utilitiesSection.textContent.includes('Tiện ích')) {
        utilitiesContainer = utilitiesSection.parentElement;
        // Find all utility form groups after the utilities heading
        const utilityElements = [];
        let nextElement = utilitiesSection.nextElementSibling;
        while (nextElement && nextElement.classList.contains('form-group')) {
            utilityElements.push(nextElement);
            nextElement = nextElement.nextElementSibling;
        }
        utilitiesContainer = { heading: utilitiesSection, elements: utilityElements };
    }    function toggleUtilitiesSection() {
        if (!utilitiesContainer) return;

        const selectedType = document.querySelector('input[name="TypePro"]:checked');
        if (selectedType && selectedType.value === 'Sale') {
            // Hide utilities section for Sale
            utilitiesContainer.heading.style.display = 'none';
            utilitiesContainer.elements.forEach(el => {
                el.style.display = 'none';
                // Clear values for Sale
                const selects = el.querySelectorAll('select');
                selects.forEach(select => {
                    if (select.name === 'WaterPrice' || select.name === 'PowerPrice' || select.name === 'Utilities') {
                        select.value = ''; // Set empty value for Sale
                    }
                });
            });
        } else if (selectedType && selectedType.value === 'Rent') {
            // Show utilities section for Rent
            utilitiesContainer.heading.style.display = 'block';
            utilitiesContainer.elements.forEach(el => {
                el.style.display = 'block';
                // Set default values for Rent
                const selects = el.querySelectorAll('select');
                selects.forEach(select => {
                    if (select.name === 'WaterPrice' || select.name === 'PowerPrice' || select.name === 'Utilities') {
                        if (!select.value || select.value === '') {
                            select.value = 'Thỏa thuận'; // Set default value for rent
                        }
                    }
                });
            });
        } else {
            // No selection - hide utilities
            utilitiesContainer.heading.style.display = 'none';
            utilitiesContainer.elements.forEach(el => {
                el.style.display = 'none';
            });
        }
    }

    // Add event listeners to TypePro radio buttons
    typeProRadios.forEach(radio => {
        radio.addEventListener('change', toggleUtilitiesSection);
    });

    // Initial check on page load
    toggleUtilitiesSection();

    // Function to display search results
    function displaySearchResults(owners) {
        if (owners.length === 0) {
            ownerSearchResults.innerHTML = '<div class="autocomplete-item no-results"><i class="fas fa-search"></i> Không tìm thấy chủ sở hữu nào</div>';
        } else {
            ownerSearchResults.innerHTML = owners.map(owner => {
                const name = escapeHtml(owner.Name || '');
                const phone = escapeHtml(owner.Phone || '');
                const email = escapeHtml(owner.Email || '');
                const address = escapeHtml(owner.Address || '');
                const ward = escapeHtml(owner.Ward || '');
                const district = escapeHtml(owner.District || '');
                const province = escapeHtml(owner.Province || '');
                const identityCard = escapeHtml(owner.IdentityCard || '');

                // Build complete address
                const fullAddress = [address, ward, district, province].filter(Boolean).join(', ');

                return `
                    <div class="autocomplete-item" data-owner-id="${owner.UserID}" onclick="selectOwner('${owner.UserID}', '${name}', '${phone}', '${email}', '${address}', '${ward}', '${district}', '${province}', '${identityCard}')">
                        <div class="owner-name"><i class="fas fa-user me-2"></i>${name}</div>
                        <div class="owner-details">
                            <div><i class="fas fa-phone me-1"></i>${phone} <i class="fas fa-envelope ms-2 me-1"></i>${email}</div>
                            ${fullAddress ? `<div class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${fullAddress}</div>` : ''}
                            ${identityCard ? `<div class="text-muted"><i class="fas fa-id-card me-1"></i>CCCD: ${identityCard}</div>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }
        ownerSearchResults.style.display = 'block';
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!ownerSearchInput.contains(e.target) && !ownerSearchResults.contains(e.target)) {
            ownerSearchResults.style.display = 'none';
        }
    });
});

function selectOwner(ownerId, name, phone, email, address, ward, district, province, identityCard) {
    const ownerSearchInput = document.getElementById('ownerSearch');
    const ownerSearchResults = document.getElementById('ownerSearchResults');
    const selectedOwnerIdInput = document.getElementById('selectedOwnerId');
    const selectedOwnerInfo = document.getElementById('selectedOwnerInfo');
    const ownerInfoDisplay = document.getElementById('ownerInfoDisplay');

    // Set the selected owner
    selectedOwnerIdInput.value = ownerId;
    ownerSearchInput.value = name;

    // Build complete address for display
    const fullAddress = [address, ward, district, province].filter(Boolean).join(', ');

    // Display selected owner info with complete details
    ownerInfoDisplay.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <strong><i class="fas fa-user"></i> ${name}</strong><br>
                <i class="fas fa-phone"></i> ${phone}<br>
                <i class="fas fa-envelope"></i> ${email}
            </div>
            <div class="col-md-6">
                ${fullAddress ? `<i class="fas fa-map-marker-alt"></i> ${fullAddress}<br>` : ''}
                ${identityCard ? `<i class="fas fa-id-card"></i> CCCD: ${identityCard}` : ''}
            </div>
        </div>
    `;

    selectedOwnerInfo.style.display = 'block';
    ownerSearchResults.style.display = 'none';

    // Remove validation error if any
    ownerSearchInput.classList.remove('is-invalid');
}

function clearOwnerSelection() {
    const ownerSearchInput = document.getElementById('ownerSearch');
    const selectedOwnerIdInput = document.getElementById('selectedOwnerId');
    const selectedOwnerInfo = document.getElementById('selectedOwnerInfo');

    ownerSearchInput.value = '';
    selectedOwnerIdInput.value = '';
    selectedOwnerInfo.style.display = 'none';
}

// Function to redirect to create new owner
function createNewOwner() {
    // Save current form data to localStorage for restoration later
    const formData = new FormData(document.querySelector('form'));
    const formDataObj = {};
    for (let [key, value] of formData.entries()) {
        formDataObj[key] = value;
    }
    localStorage.setItem('propertyFormDraft', JSON.stringify(formDataObj));

    // Redirect to owners page with modal parameter
    window.location.href = '{{ route("admin.users.byRole", "Owner") }}?showCreateModal=true';
}

// Function to restore form data from localStorage
function restoreFormData() {
    const savedData = localStorage.getItem('propertyFormDraft');
    if (savedData) {
        try {
            const formData = JSON.parse(savedData);
            const form = document.querySelector('form');

            // Restore form fields
            Object.keys(formData).forEach(key => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field) {
                    if (field.type === 'checkbox' || field.type === 'radio') {
                        field.checked = formData[key] === field.value;
                    } else if (field.tagName === 'SELECT') {
                        field.value = formData[key];
                    } else {
                        field.value = formData[key];
                    }
                }
            });

            // Clear the saved data after restoration
            localStorage.removeItem('propertyFormDraft');

            // Show notification that data was restored
            if (Object.keys(formData).length > 0) {
                showNotification('Dữ liệu form đã được khôi phục thành công!', 'success');
            }
        } catch (error) {
            console.error('Error restoring form data:', error);
            localStorage.removeItem('propertyFormDraft');
        }
    }
}

// Function to show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Function to handle owner selection after creation
function handleOwnerCreationReturn() {
    const urlParams = new URLSearchParams(window.location.search);
    const newOwnerId = urlParams.get('newOwnerId');
    const newOwnerName = urlParams.get('newOwnerName');
    const newOwnerPhone = urlParams.get('newOwnerPhone');
    const newOwnerEmail = urlParams.get('newOwnerEmail');
    const newOwnerAddress = urlParams.get('newOwnerAddress');
    const newOwnerWard = urlParams.get('newOwnerWard');
    const newOwnerDistrict = urlParams.get('newOwnerDistrict');
    const newOwnerProvince = urlParams.get('newOwnerProvince');
    const newOwnerIdentityCard = urlParams.get('newOwnerIdentityCard');

    if (newOwnerId && newOwnerName) {
        // Select the newly created owner with all details
        selectOwner(
            newOwnerId,
            newOwnerName,
            newOwnerPhone || '',
            newOwnerEmail || '',
            newOwnerAddress || '',
            newOwnerWard || '',
            newOwnerDistrict || '',
            newOwnerProvince || '',
            newOwnerIdentityCard || ''
        );

        // Clean up URL parameters
        const url = new URL(window.location);
        url.searchParams.delete('newOwnerId');
        url.searchParams.delete('newOwnerName');
        url.searchParams.delete('newOwnerPhone');
        url.searchParams.delete('newOwnerEmail');
        url.searchParams.delete('newOwnerAddress');
        url.searchParams.delete('newOwnerWard');
        url.searchParams.delete('newOwnerDistrict');
        url.searchParams.delete('newOwnerProvince');
        url.searchParams.delete('newOwnerIdentityCard');
        window.history.replaceState({}, document.title, url.toString());

        showNotification(`Đã chọn chủ sở hữu: ${newOwnerName}`, 'success');
    }
}


</script>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<script>
// Transaction type display functionality
document.addEventListener('DOMContentLoaded', function() {
    const typeProRadios = document.querySelectorAll('input[name="TypePro"]');
    const transactionTypeDisplay = document.getElementById('transactionTypeDisplay');
    const transactionTypeText = document.getElementById('transactionTypeText');

    function updateTransactionTypeDisplay() {
        const selectedType = document.querySelector('input[name="TypePro"]:checked');
        if (selectedType) {
            const typeText = selectedType.value === 'Sale' ? 'Bán' : 'Cho thuê';
            transactionTypeText.textContent = typeText;
            transactionTypeDisplay.style.display = 'block';
        } else {
            transactionTypeDisplay.style.display = 'none';
        }
    }

    // Add event listeners to TypePro radio buttons
    typeProRadios.forEach(radio => {
        radio.addEventListener('change', updateTransactionTypeDisplay);
    });

    // Initial check on page load
    updateTransactionTypeDisplay();
});

// Leaflet Map functionality
let propertyMap = null;
let propertyMarker = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    initializeMap();

    // Add address change listeners
    addAddressChangeListeners();
});

function initializeMap() {
    // Initialize Leaflet map centered on Vietnam
    propertyMap = L.map('property-map').setView([16.0583, 108.2772], 6);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(propertyMap);

    // Add custom marker styles
    const customIcon = L.divIcon({
        className: 'custom-marker',
        html: '<i class="fas fa-map-marker-alt" style="color: white; font-size: 20px; margin-top: 2px;"></i>',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });

    // Store the custom icon for later use
    window.customMapIcon = customIcon;
}

function addAddressChangeListeners() {
    const addressInput = document.querySelector('input[name="Address"]');
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const addressInfo = document.getElementById('addressInfo');
    const fullAddressDisplay = document.getElementById('fullAddressDisplay');

    // Debounce function to prevent too many API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function updateMapAndDisplay() {
        const address = addressInput ? addressInput.value.trim() : '';
        const province = provinceSelect ? provinceSelect.value : '';
        const district = districtSelect ? districtSelect.value : '';
        const ward = wardSelect ? wardSelect.value : '';

        // Build full address
        const addressParts = [address, ward, district, province].filter(Boolean);
        const fullAddress = addressParts.join(', ');

        if (fullAddress) {
            // Update address display
            fullAddressDisplay.innerHTML = `
                <div class="text-dark">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                    <strong>${fullAddress}</strong>
                </div>
            `;
            addressInfo.style.display = 'block';
            addressInfo.classList.add('show');

            // Update map with debounce to prevent too many calls
            debouncedUpdateMap(fullAddress);
        } else {
            addressInfo.style.display = 'none';
            addressInfo.classList.remove('show');

            // Reset map to Vietnam view
            if (propertyMap) {
                propertyMap.setView([16.0583, 108.2772], 6);
                if (propertyMarker) {
                    propertyMap.removeLayer(propertyMarker);
                    propertyMarker = null;
                }
                const mapInfo = document.querySelector('.map-info small');
                if (mapInfo) {
                    mapInfo.innerHTML = `
                        <i class="fas fa-info-circle"></i>
                        Bản đồ sẽ tự động cập nhật khi bạn chọn địa chỉ
                    `;
                }
            }
        }
    }

    // Create debounced version of updateMapLocation
    const debouncedUpdateMap = debounce(updateMapLocation, 1000);

    // Add event listeners
    if (addressInput) addressInput.addEventListener('input', updateMapAndDisplay);
    if (provinceSelect) provinceSelect.addEventListener('change', updateMapAndDisplay);
    if (districtSelect) districtSelect.addEventListener('change', updateMapAndDisplay);
    if (wardSelect) wardSelect.addEventListener('change', updateMapAndDisplay);
}

async function updateMapLocation(address) {
    try {
        // Update map info to show loading
        const mapInfo = document.querySelector('.map-info small');
        if (mapInfo) {
            mapInfo.innerHTML = `
                <i class="fas fa-spinner fa-spin text-info"></i>
                Đang định vị địa chỉ trên bản đồ...
            `;
        }

        // Multiple geocoding services with failover
        const geocodingAPIs = [
            {
                name: 'Nominatim',
                url: `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address + ', Vietnam')}&limit=1`,
                parser: (data) => data && data.length > 0 ? { lat: parseFloat(data[0].lat), lon: parseFloat(data[0].lon) } : null
            },
            {
                name: 'Photon',
                url: `https://photon.komoot.io/api/?q=${encodeURIComponent(address + ', Vietnam')}&limit=1`,
                parser: (data) => data && data.features && data.features.length > 0 ? {
                    lat: data.features[0].geometry.coordinates[1],
                    lon: data.features[0].geometry.coordinates[0]
                } : null
            },
            {
                name: 'MapBox (free tier)',
                url: `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(address + ', Vietnam')}.json?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw&limit=1`,
                parser: (data) => data && data.features && data.features.length > 0 ? {
                    lat: data.features[0].center[1],
                    lon: data.features[0].center[0]
                } : null
            }
        ];

        let lat, lon, found = false, apiUsed = '';

        // Try each geocoding API
        for (let i = 0; i < geocodingAPIs.length; i++) {
            const api = geocodingAPIs[i];
            try {
                console.log(`Trying geocoding API: ${api.name}`);

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 8000); // 8 second timeout

                const response = await fetch(api.url, {
                    signal: controller.signal,
                    headers: {
                        'User-Agent': 'PropertyApp/1.0',
                        'Accept': 'application/json'
                    }
                });

                clearTimeout(timeoutId);

                if (response.ok) {
                    const data = await response.json();
                    const coords = api.parser(data);

                    if (coords && coords.lat && coords.lon) {
                        lat = coords.lat;
                        lon = coords.lon;
                        found = true;
                        apiUsed = api.name;
                        console.log(`Successfully geocoded with ${api.name}:`, lat, lon);
                        break;
                    }
                }
            } catch (error) {
                console.warn(`${api.name} geocoding failed:`, error.message);
                continue;
            }
        }

        // Fallback to approximate coordinates based on province/city
        if (!found) {
            console.log('All geocoding APIs failed, using approximate coordinates');
            const province = document.getElementById('province')?.value || '';
            const coordinates = getApproximateCoordinates(province, address);
            if (coordinates) {
                lat = coordinates.lat;
                lon = coordinates.lon;
                found = true;
                apiUsed = 'Approximate';
            }
        }

        if (found && lat && lon) {
            // Remove existing marker
            if (propertyMarker) {
                propertyMap.removeLayer(propertyMarker);
            }

            // Choose marker style based on accuracy
            const isExact = apiUsed !== 'Approximate';
            const markerIcon = isExact ? window.customMapIcon : L.divIcon({
                className: 'custom-marker',
                html: '<i class="fas fa-map-marker-alt" style="color: orange; font-size: 20px; margin-top: 2px;"></i>',
                iconSize: [30, 30],
                iconAnchor: [15, 30],
                popupAnchor: [0, -30]
            });

            // Add new marker
            propertyMarker = L.marker([lat, lon], {
                icon: markerIcon
            }).addTo(propertyMap);

            // Add popup with address info
            propertyMarker.bindPopup(`
                <div class="text-center">
                    <strong>${isExact ? 'Vị trí chính xác' : 'Vị trí ước tính'}</strong><br>
                    <small>${address}</small><br>
                    ${!isExact ? '<em class="text-warning">Vị trí có thể không chính xác</em><br>' : ''}
                    <small class="text-muted">Nguồn: ${apiUsed}</small>
                </div>
            `);

            // Center map on the location with appropriate zoom
            const zoomLevel = isExact ? 16 : 12;
            propertyMap.setView([lat, lon], zoomLevel);

            // Update map info
            if (mapInfo) {
                mapInfo.innerHTML = `
                    <i class="fas fa-${isExact ? 'check-circle text-success' : 'map-marked-alt text-warning'}"></i>
                    ${isExact ? 'Đã định vị chính xác địa chỉ' : 'Hiển thị vị trí ước tính'} (${apiUsed})
                `;
            }
        } else {
            // Could not find location at all
            console.warn('Could not geocode address:', address);
            if (mapInfo) {
                mapInfo.innerHTML = `
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    Không thể định vị địa chỉ. Vui lòng kiểm tra lại thông tin.
                `;
            }

            // Reset to Vietnam center view
            if (propertyMarker) {
                propertyMap.removeLayer(propertyMarker);
                propertyMarker = null;
            }
            propertyMap.setView([16.0583, 108.2772], 6);
        }
    } catch (error) {
        console.error('Error geocoding address:', error);
        const mapInfo = document.querySelector('.map-info small');
        if (mapInfo) {
            mapInfo.innerHTML = `
                <i class="fas fa-map-marked-alt text-info"></i>
                Hiển thị vị trí ước tính cho khu vực này
            `;
        }

        // Show approximate location as fallback
        showApproximateLocation(address);
    }
}

// Function to get approximate coordinates for major Vietnamese cities/provinces
function getApproximateCoordinates(province, fullAddress) {
    const coordinates = {
        'Thành phố Hồ Chí Minh': { lat: 10.8231, lon: 106.6297 },
        'Thành phố Hà Nội': { lat: 21.0285, lon: 105.8542 },
        'Thành phố Hải Phòng': { lat: 20.8449, lon: 106.6881 },
        'Thành phố Đà Nẵng': { lat: 16.0471, lon: 108.2068 },
        'Thành phố Cần Thơ': { lat: 10.0452, lon: 105.7469 },
        'Tỉnh An Giang': { lat: 10.3883, lon: 105.4358 },
        'Tỉnh Bà Rịa - Vũng Tàu': { lat: 10.5417, lon: 107.2431 },
        'Tỉnh Bắc Giang': { lat: 21.2819, lon: 106.1946 },
        'Tỉnh Bắc Kạn': { lat: 22.1477, lon: 105.8348 },
        'Tỉnh Bạc Liêu': { lat: 9.2940, lon: 105.7215 },
        'Tỉnh Bắc Ninh': { lat: 21.1861, lon: 106.0763 },
        'Tỉnh Bến Tre': { lat: 10.2433, lon: 106.3756 },
        'Tỉnh Bình Định': { lat: 14.1665, lon: 109.0450 },
        'Tỉnh Bình Dương': { lat: 11.3254, lon: 106.4772 },
        'Tỉnh Bình Phước': { lat: 11.7512, lon: 106.7234 },
        'Tỉnh Bình Thuận': { lat: 11.0904, lon: 108.0721 },
        'Tỉnh Cà Mau': { lat: 9.1768, lon: 105.1524 },
        'Tỉnh Cao Bằng': { lat: 22.6356, lon: 106.2573 },
        'Tỉnh Đắk Lắk': { lat: 12.7100, lon: 108.2378 },
        'Tỉnh Đắk Nông': { lat: 12.2646, lon: 107.6098 },
        'Tỉnh Điện Biên': { lat: 21.8042, lon: 103.2287 },
        'Tỉnh Đồng Nai': { lat: 11.0686, lon: 107.1676 },
        'Tỉnh Đồng Tháp': { lat: 10.4493, lon: 105.6881 },
        'Tỉnh Gia Lai': { lat: 13.8078, lon: 108.1099 },
        'Tỉnh Hà Giang': { lat: 22.8025, lon: 104.9784 },
        'Tỉnh Hà Nam': { lat: 20.5835, lon: 105.9230 },
        'Tỉnh Hà Tĩnh': { lat: 18.2943, lon: 105.8906 },
        'Tỉnh Hải Dương': { lat: 20.9373, lon: 106.3148 },
        'Tỉnh Hậu Giang': { lat: 9.7579, lon: 105.6412 },
        'Tỉnh Hòa Bình': { lat: 20.6861, lon: 105.3131 },
        'Tỉnh Hưng Yên': { lat: 20.8525, lon: 106.0511 },
        'Tỉnh Khánh Hòa': { lat: 12.2585, lon: 109.0526 },
        'Tỉnh Kiên Giang': { lat: 10.0125, lon: 105.0808 },
        'Tỉnh Kon Tum': { lat: 14.3497, lon: 108.0005 },
        'Tỉnh Lai Châu': { lat: 22.3964, lon: 103.4704 },
        'Tỉnh Lâm Đồng': { lat: 11.5753, lon: 108.1429 },
        'Tỉnh Lạng Sơn': { lat: 21.8537, lon: 106.7614 },
        'Tỉnh Lào Cai': { lat: 22.4856, lon: 103.9707 },
        'Tỉnh Long An': { lat: 10.6959, lon: 106.2431 },
        'Tỉnh Nam Định': { lat: 20.4388, lon: 106.1621 },
        'Tỉnh Nghệ An': { lat: 19.2342, lon: 104.9200 },
        'Tỉnh Ninh Bình': { lat: 20.2506, lon: 105.9744 },
        'Tỉnh Ninh Thuận': { lat: 11.6739, lon: 108.8629 },
        'Tỉnh Phú Thọ': { lat: 21.2680, lon: 105.2045 },
        'Tỉnh Phú Yên': { lat: 13.1611, lon: 109.3529 },
        'Tỉnh Quảng Bình': { lat: 17.6102, lon: 106.3487 },
        'Tỉnh Quảng Nam': { lat: 15.5394, lon: 108.0191 },
        'Tỉnh Quảng Ngãi': { lat: 15.1214, lon: 108.8044 },
        'Tỉnh Quảng Ninh': { lat: 21.0062, lon: 107.2925 },
        'Tỉnh Quảng Trị': { lat: 16.7943, lon: 107.1851 },
        'Tỉnh Sóc Trăng': { lat: 9.6003, lon: 105.9800 },
        'Tỉnh Sơn La': { lat: 21.1022, lon: 103.7289 },
        'Tỉnh Tây Ninh': { lat: 11.3350, lon: 106.1273 },
        'Tỉnh Thái Bình': { lat: 20.4464, lon: 106.3364 },
        'Tỉnh Thái Nguyên': { lat: 21.5928, lon: 105.8253 },
        'Tỉnh Thanh Hóa': { lat: 19.8066, lon: 105.7851 },
        'Tỉnh Thừa Thiên Huế': { lat: 16.4674, lon: 107.5905 },
        'Tỉnh Tiền Giang': { lat: 10.4493, lon: 106.3420 },
        'Tỉnh Trà Vinh': { lat: 9.9477, lon: 106.3254 },
        'Tỉnh Tuyên Quang': { lat: 21.8256, lon: 105.2280 },
        'Tỉnh Vĩnh Long': { lat: 10.2397, lon: 105.9571 },
        'Tỉnh Vĩnh Phúc': { lat: 21.3609, lon: 105.6049 },
        'Tỉnh Yên Bái': { lat: 21.6837, lon: 104.4551 }
    };

    return coordinates[province] || null;
}

// Function to show approximate location when exact geocoding fails
function showApproximateLocation(address) {
    const province = document.getElementById('province')?.value || '';
    const coordinates = getApproximateCoordinates(province, address);

    if (coordinates) {
        // Remove existing marker
        if (propertyMarker) {
            propertyMap.removeLayer(propertyMarker);
        }

        // Add approximate marker with different style
        const approximateIcon = L.divIcon({
            className: 'custom-marker',
            html: '<i class="fas fa-map-marker-alt" style="color: orange; font-size: 20px; margin-top: 2px;"></i>',
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -30]
        });

        propertyMarker = L.marker([coordinates.lat, coordinates.lon], {
            icon: approximateIcon
        }).addTo(propertyMap);

        // Add popup with approximate location info
        propertyMarker.bindPopup(`
            <div class="text-center">
                <strong>Vị trí ước tính</strong><br>
                <small>${address}</small><br>
                <em class="text-warning">Vị trí có thể không chính xác</em>
            </div>
        `);

        // Center map on the approximate location
        propertyMap.setView([coordinates.lat, coordinates.lon], 12);
    } else {
        // Default to Vietnam center if no province match
        propertyMap.setView([16.0583, 108.2772], 6);
    }
}

// Enhanced address selection with map update
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');

    if (provinceSelect && districtSelect && wardSelect) {
        // Enhanced load provinces function with multiple API fallbacks
        async function loadProvinces() {
            const apiEndpoints = [
                'https://provinces.open-api.vn/api/?depth=1',
                'https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json',
                'https://api.mysupership.vn/v1/partner/areas/province'
            ];

            let lastError = null;

            // Try each API endpoint
            for (let i = 0; i < apiEndpoints.length; i++) {
                try {
                    console.log(`Attempting to load provinces from API ${i + 1}:`, apiEndpoints[i]);

                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 second timeout

                    const response = await fetch(apiEndpoints[i], {
                        signal: controller.signal,
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'User-Agent': 'PropertyApp/1.0'
                        },
                        mode: 'cors' // Enable CORS
                    });

                    clearTimeout(timeoutId);

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();
                    let provinces = [];

                    // Handle different API response formats
                    if (i === 0) {
                        // provinces.open-api.vn format
                        provinces = data;
                    } else if (i === 1) {
                        // GitHub data format
                        provinces = data.map(item => ({
                            name: item.Name,
                            code: item.Id
                        }));
                    } else if (i === 2) {
                        // MySuperShip format
                        provinces = data.results ? data.results.map(item => ({
                            name: item.name,
                            code: item.code
                        })) : [];
                    }

                    if (provinces && provinces.length > 0) {
                        console.log(`Successfully loaded ${provinces.length} provinces from API ${i + 1}`);

                        provinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
                        provinces.forEach(province => {
                            const option = document.createElement('option');
                            option.value = province.name || province.Name;
                            option.textContent = province.name || province.Name;
                            option.setAttribute('data-code', province.code || province.Id || province.Code);
                            provinceSelect.appendChild(option);
                        });

                        // Success! Exit the loop
                        return;
                    } else {
                        throw new Error('No provinces data found in response');
                    }

                } catch (error) {
                    lastError = error;
                    console.warn(`API ${i + 1} failed:`, error.message);

                    // If this is a network error, show user-friendly message
                    if (error.name === 'AbortError') {
                        console.warn(`API ${i + 1} timed out after 5 seconds`);
                    } else if (error.message.includes('fetch')) {
                        console.warn(`API ${i + 1} network error:`, error.message);
                    }

                    // Continue to next API
                    continue;
                }
            }

            // All APIs failed, use offline fallback
            console.error('All APIs failed, using offline data. Last error:', lastError?.message);
            showApiErrorNotification();
            loadProvincesOffline();
        }

        // Show notification when APIs fail
        function showApiErrorNotification() {
            const notification = document.createElement('div');
            notification.className = 'alert alert-warning alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 80px; right: 20px; z-index: 9999; max-width: 400px;';
            notification.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Thông báo:</strong> Không thể kết nối đến server địa chỉ. Đang sử dụng dữ liệu offline.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);

            // Auto remove after 8 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 8000);
        }

        // Offline fallback for provinces
        function loadProvincesOffline() {
            const provinces = [
                { name: 'Thành phố Hồ Chí Minh', code: '79' },
                { name: 'Thành phố Hà Nội', code: '01' },
                { name: 'Thành phố Hải Phòng', code: '31' },
                { name: 'Thành phố Đà Nẵng', code: '48' },
                { name: 'Thành phố Cần Thơ', code: '92' },
                { name: 'Tỉnh An Giang', code: '89' },
                { name: 'Tỉnh Bà Rịa - Vũng Tàu', code: '77' },
                { name: 'Tỉnh Bắc Giang', code: '54' },
                { name: 'Tỉnh Bắc Kạn', code: '06' },
                { name: 'Tỉnh Bạc Liêu', code: '95' },
                { name: 'Tỉnh Bắc Ninh', code: '27' },
                { name: 'Tỉnh Bến Tre', code: '83' },
                { name: 'Tỉnh Bình Định', code: '52' },
                { name: 'Tỉnh Bình Dương', code: '74' },
                { name: 'Tỉnh Bình Phước', code: '70' },
                { name: 'Tỉnh Bình Thuận', code: '60' },
                { name: 'Tỉnh Cà Mau', code: '96' },
                { name: 'Tỉnh Cao Bằng', code: '04' },
                { name: 'Tỉnh Đắk Lắk', code: '66' },
                { name: 'Tỉnh Đắk Nông', code: '67' },
                { name: 'Tỉnh Điện Biên', code: '11' },
                { name: 'Tỉnh Đồng Nai', code: '75' },
                { name: 'Tỉnh Đồng Tháp', code: '87' },
                { name: 'Tỉnh Gia Lai', code: '64' },
                { name: 'Tỉnh Hà Giang', code: '02' },
                { name: 'Tỉnh Hà Nam', code: '18' },
                { name: 'Tỉnh Hà Tĩnh', code: '42' },
                { name: 'Tỉnh Hải Dương', code: '30' },
                { name: 'Tỉnh Hậu Giang', code: '93' },
                { name: 'Tỉnh Hòa Bình', code: '17' },
                { name: 'Tỉnh Hưng Yên', code: '33' },
                { name: 'Tỉnh Khánh Hòa', code: '58' },
                { name: 'Tỉnh Kiên Giang', code: '91' },
                { name: 'Tỉnh Kon Tum', code: '62' },
                { name: 'Tỉnh Lai Châu', code: '12' },
                { name: 'Tỉnh Lâm Đồng', code: '68' },
                { name: 'Tỉnh Lạng Sơn', code: '09' },
                { name: 'Tỉnh Lào Cai', code: '10' },
                { name: 'Tỉnh Long An', code: '80' },
                { name: 'Tỉnh Nam Định', code: '36' },
                { name: 'Tỉnh Nghệ An', code: '40' },
                { name: 'Tỉnh Ninh Bình', code: '37' },
                { name: 'Tỉnh Ninh Thuận', code: '59' },
                { name: 'Tỉnh Phú Thọ', code: '25' },
                { name: 'Tỉnh Phú Yên', code: '54' },
                { name: 'Tỉnh Quảng Bình', code: '44' },
                { name: 'Tỉnh Quảng Nam', code: '49' },
                { name: 'Tỉnh Quảng Ngãi', code: '51' },
                { name: 'Tỉnh Quảng Ninh', code: '22' },
                { name: 'Tỉnh Quảng Trị', code: '45' },
                { name: 'Tỉnh Sóc Trăng', code: '94' },
                { name: 'Tỉnh Sơn La', code: '14' },
                { name: 'Tỉnh Tây Ninh', code: '72' },
                { name: 'Tỉnh Thái Bình', code: '34' },
                { name: 'Tỉnh Thái Nguyên', code: '19' },
                { name: 'Tỉnh Thanh Hóa', code: '38' },
                { name: 'Tỉnh Thừa Thiên Huế', code: '46' },
                { name: 'Tỉnh Tiền Giang', code: '82' },
                { name: 'Tỉnh Trà Vinh', code: '84' },
                { name: 'Tỉnh Tuyên Quang', code: '08' },
                { name: 'Tỉnh Vĩnh Long', code: '86' },
                { name: 'Tỉnh Vĩnh Phúc', code: '26' },
                { name: 'Tỉnh Yên Bái', code: '15' }
            ];

            provinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.name;
                option.textContent = province.name;
                option.setAttribute('data-code', province.code);
                provinceSelect.appendChild(option);
            });
        }

        // Enhanced load districts function with multiple API fallbacks
        async function loadDistricts(provinceName) {
            const selectedOption = Array.from(provinceSelect.options).find(
                option => option.value === provinceName
            );
            const provinceCode = selectedOption ? selectedOption.getAttribute('data-code') : null;

            if (!provinceCode) {
                districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
                wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                return;
            }

            // Reset selects
            districtSelect.innerHTML = '<option value="">Đang tải...</option>';
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';

            const apiEndpoints = [
                `https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`,
                `https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json`
            ];

            let lastError = null;

            // Try each API endpoint
            for (let i = 0; i < apiEndpoints.length; i++) {
                try {
                    console.log(`Loading districts from API ${i + 1}:`, apiEndpoints[i]);

                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 5000);

                    const response = await fetch(apiEndpoints[i], {
                        signal: controller.signal,
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'User-Agent': 'PropertyApp/1.0'
                        }
                    });

                    clearTimeout(timeoutId);

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();
                    let districts = [];

                    if (i === 0) {
                        // provinces.open-api.vn format
                        districts = data.districts || [];
                    } else if (i === 1) {
                        // GitHub data format - find province and get districts
                        const provinceData = data.find(p => p.Id === provinceCode || p.Name === provinceName);
                        districts = provinceData ? provinceData.Districts || [] : [];
                        // Convert format
                        districts = districts.map(d => ({
                            name: d.Name,
                            code: d.Id
                        }));
                    }

                    if (districts && districts.length > 0) {
                        console.log(`Successfully loaded ${districts.length} districts from API ${i + 1}`);

                        districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
                        districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.name || district.Name;
                            option.textContent = district.name || district.Name;
                            option.setAttribute('data-code', district.code || district.Id);
                            districtSelect.appendChild(option);
                        });

                        // Success! Exit the loop
                        return;

                    } else {
                        throw new Error('No districts data found');
                    }

                } catch (error) {
                    lastError = error;
                    console.warn(`API ${i + 1} failed for districts:`, error.message);
                    continue;
                }
            }

            // All APIs failed, show fallback message
            console.error('All APIs failed for districts. Last error:', lastError?.message);
            districtSelect.innerHTML = '<option value="">Lỗi tải dữ liệu - vui lòng thử lại</option>';

            // Show approximate location for the province on map
            if (provinceName) {
                const coordinates = getApproximateCoordinates(provinceName, '');
                if (coordinates && propertyMap) {
                    propertyMap.setView([coordinates.lat, coordinates.lon], 10);

                    // Show notification
                    const mapInfo = document.querySelector('.map-info small');
                    if (mapInfo) {
                        mapInfo.innerHTML = `
                            <i class="fas fa-map-marked-alt text-warning"></i>
                            Hiển thị vị trí tỉnh ${provinceName}
                        `;
                    }
        }

        // Enhanced load wards function with multiple API fallbacks
        async function loadWards(districtName) {
            const selectedOption = Array.from(districtSelect.options).find(
                option => option.value === districtName
            );
            const districtCode = selectedOption ? selectedOption.getAttribute('data-code') : null;

            if (!districtCode) {
                wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                return;
            }

            // Reset ward select
            wardSelect.innerHTML = '<option value="">Đang tải...</option>';

            const apiEndpoints = [
                `https://provinces.open-api.vn/api/d/${districtCode}?depth=2`,
                `https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json`
            ];

            let lastError = null;

            // Try each API endpoint
            for (let i = 0; i < apiEndpoints.length; i++) {
                try {
                    console.log(`Loading wards from API ${i + 1}:`, apiEndpoints[i]);

                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 5000);

                    const response = await fetch(apiEndpoints[i], {
                        signal: controller.signal,
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'User-Agent': 'PropertyApp/1.0'
                        }
                    });

                    clearTimeout(timeoutId);

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }

                    const data = await response.json();
                    let wards = [];

                    if (i === 0) {
                        // provinces.open-api.vn format
                        wards = data.wards || [];
                    } else if (i === 1) {
                        // GitHub data format - find district and get wards
                        for (const province of data) {
                            if (province.Districts) {
                                const districtData = province.Districts.find(d =>
                                    d.Id === districtCode || d.Name === districtName
                                );
                                if (districtData && districtData.Wards) {
                                    wards = districtData.Wards.map(w => ({
                                        name: w.Name,
                                        code: w.Id
                                    }));
                                    break;
                                }
                            }
                        }
                    }

                    if (wards && wards.length > 0) {
                        console.log(`Successfully loaded ${wards.length} wards from API ${i + 1}`);

                        wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                        wards.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.name || ward.Name;
                            option.textContent = ward.name || ward.Name;
                            wardSelect.appendChild(option);
                        });

                        // Success! Exit the loop
                        return;

                    } else {
                        throw new Error('No wards data found');
                    }

                } catch (error) {
                    lastError = error;
                    console.warn(`API ${i + 1} failed for wards:`, error.message);
                    continue;
                }
            }

            // All APIs failed, show fallback message
            console.error('All APIs failed for wards. Last error:', lastError?.message);
            wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu - vui lòng thử lại</option>';
        }
                wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu phường/xã</option>';
            }
        }

        // Event listeners with map update
        provinceSelect.addEventListener('change', function() {
            const provinceName = this.value;
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';

            if (provinceName) {
                loadDistricts(provinceName);
            }
        });

        districtSelect.addEventListener('change', function() {
            const districtName = this.value;
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';

            if (districtName) {
                loadWards(districtName);
            }
        });

        // Load provinces on page load
        loadProvinces();
    }

    // Handle legal radio buttons
    const legalRadios = document.querySelectorAll('input[name="legal"]');
    const legalOtherInput = document.getElementById('legalOtherInput');
    const legalOtherInputField = document.querySelector('input[name="legal_other"]');

    legalRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'Khác') {
                legalOtherInput.style.display = 'block';
                legalOtherInputField.required = true;
                legalOtherInputField.focus();
            } else {
                legalOtherInput.style.display = 'none';
                legalOtherInputField.required = false;
                legalOtherInputField.value = '';
            }
        });
    });

    // Handle form submission for legal field
    const propertyForm = document.querySelector('form');
    if (propertyForm) {
        propertyForm.addEventListener('submit', function(e) {
            const selectedLegal = document.querySelector('input[name="legal"]:checked');
            if (selectedLegal && selectedLegal.value === 'Khác') {
                const otherValue = legalOtherInputField.value.trim();
                if (otherValue) {
                    // Create a hidden input to send the custom legal value
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'legal';
                    hiddenInput.value = otherValue;
                    this.appendChild(hiddenInput);

                    // Disable the radio button to avoid conflict
                    selectedLegal.disabled = true;
                }
            }
        });
    }
});
</script>



