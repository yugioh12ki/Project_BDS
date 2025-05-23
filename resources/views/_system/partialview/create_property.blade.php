

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
                                                <small class="text-muted">Nhập ít nhất 2 ký tự để tìm kiếm</small>
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

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-6 col-form-label">Phường/Xã <span class="text-danger">*</span></label>
                                                    <div class="col-sm-6">
                                                        <input type="text" name="Ward" class="form-control" value="{{ old('Ward') }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-6 col-form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                                    <div class="col-sm-6">
                                                        <input type="text" name="District" class="form-control" value="{{ old('District') }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" name="Province" class="form-control" value="{{ old('Province') }}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-3">
                                            <label class="col-sm-3 col-form-label">Mô tả <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <textarea name="Description" class="form-control" rows="5" required placeholder="Mô tả chi tiết về bất động sản">{{ old('Description') }}</textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-6 col-form-label">Số điện thoại liên hệ</label>
                                                    <div class="col-sm-6">
                                                        <input type="tel" name="ContactPhone" class="form-control" value="{{ old('ContactPhone') }}" placeholder="Số điện thoại">
                                                        <small class="text-muted">Được tự động điền từ thông tin chủ sở hữu</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row mb-3">
                                                    <label class="col-sm-6 col-form-label">Email liên hệ</label>
                                                    <div class="col-sm-6">
                                                        <input type="email" name="ContactEmail" class="form-control" value="{{ old('ContactEmail') }}" placeholder="Email liên hệ">
                                                        <small class="text-muted">Được tự động điền từ thông tin chủ sở hữu</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-warning">
                                            <h6><i class="fas fa-exclamation-triangle"></i> Lưu ý</h6>
                                            <p class="mb-1">• Tiêu đề phải rõ ràng, hấp dẫn</p>
                                            <p class="mb-1">• Giá cả phải chính xác</p>
                                            <p class="mb-1">• Địa chỉ phải đầy đủ và chính xác</p>
                                            <p class="mb-0">• Mô tả chi tiết sẽ thu hút khách hàng</p>
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
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Số tầng</label>
                                                    <input type="number" name="LevelHouse" class="form-control" value="{{ old('LevelHouse') }}" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Tầng số</label>
                                                    <input type="number" name="Floor" class="form-control" value="{{ old('Floor') }}" placeholder="0">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Chiều dài nhà (m)</label>
                                                    <input type="number" name="HouseLength" class="form-control" value="{{ old('HouseLength') }}" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Chiều rộng nhà (m)</label>
                                                    <input type="number" name="HouseWidth" class="form-control" value="{{ old('HouseWidth') }}" placeholder="0">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Chiều dài tổng thể (m)</label>
                                                    <input type="number" name="TotalLength" class="form-control" value="{{ old('TotalLength') }}" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Chiều rộng tổng thể (m)</label>
                                                    <input type="number" name="TotalWidth" class="form-control" value="{{ old('TotalWidth') }}" placeholder="0">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Số phòng ngủ</label>
                                                    <input type="number" name="Bedroom" class="form-control" value="{{ old('Bedroom') }}" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Số phòng WC</label>
                                                    <input type="number" name="Bath_WC" class="form-control" value="{{ old('Bath_WC') }}" placeholder="0">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Ban công</label>
                                                    <select name="Balcony" class="form-control">
                                                        <option value="">-- Chọn --</option>
                                                        <option value="1" {{ old('Balcony') == '1' ? 'selected' : '' }}>Có</option>
                                                        <option value="0" {{ old('Balcony') == '0' ? 'selected' : '' }}>Không</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Đường vào (m)</label>
                                                    <input type="number" name="Road" class="form-control" value="{{ old('Road') }}" placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-success mb-3"><i class="fas fa-compass me-2"></i>Thông tin bổ sung</h6>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Pháp lý</label>
                                            <input type="text" name="legal" class="form-control" value="{{ old('legal') }}" placeholder="Sổ đỏ, sổ hồng...">
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
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Lân cận</label>
                                            <input type="text" name="near" class="form-control" value="{{ old('near') }}" placeholder="Trường học, bệnh viện, siêu thị...">
                                            <small class="form-text text-muted">Các địa điểm lân cận quan trọng</small>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Nội thất</label>
                                            <select name="Interior" class="form-control">
                                                <option value="">-- Chọn --</option>
                                                <option value="Cơ bản" {{ old('Interior') == 'Cơ bản' ? 'selected' : '' }}>Cơ bản</option>
                                                <option value="Đầy đủ" {{ old('Interior') == 'Đầy đủ' ? 'selected' : '' }}>Đầy đủ</option>
                                            </select>
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
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Giá điện</label>
                                            <select name="PowerPrice" class="form-control">
                                                <option value="">-- Chọn --</option>
                                                <option value="Thỏa thuận" {{ old('PowerPrice') == 'Thỏa thuận' ? 'selected' : '' }}>Thỏa thuận</option>
                                                <option value="Do chủ nhà quy định" {{ old('PowerPrice') == 'Do chủ nhà quy định' ? 'selected' : '' }}>Do chủ nhà quy định</option>
                                                <option value="Theo nhà nước" {{ old('PowerPrice') == 'Theo nhà nước' ? 'selected' : '' }}>Theo nhà nước</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="form-label">Tiện ích khác</label>
                                            <select name="Utilities" class="form-control">
                                                <option value="">-- Chọn --</option>
                                                <option value="Thỏa thuận" {{ old('Utilities') == 'Thỏa thuận' ? 'selected' : '' }}>Thỏa thuận</option>
                                                <option value="Do chủ nhà quy định" {{ old('Utilities') == 'Do chủ nhà quy định' ? 'selected' : '' }}>Do chủ nhà quy định</option>
                                            </select>
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
                                                    <label class="form-label">Chọn video</label>
                                                    <input type="file" name="property_videos[]" class="form-control" multiple accept="video/*" id="videoInput">
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-info-circle text-info"></i>
                                                        Có thể chọn nhiều video (định dạng: mp4, avi, mov)
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
                                                        <li>Kích thước tối đa: 50MB/file</li>
                                                        <li>Định dạng: MP4, AVI, MOV</li>
                                                        <li>Thời lượng khuyến nghị: 1-3 phút</li>
                                                        <li>Quay video ổn định, rõ nét</li>
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

// Video preview functionality
document.getElementById('videoInput').addEventListener('change', function(e) {
    const preview = document.getElementById('videoPreview');
    preview.innerHTML = '';

    for (let i = 0; i < e.target.files.length; i++) {
        const file = e.target.files[i];
        if (file.type.startsWith('video/')) {
            const div = document.createElement('div');
            div.className = 'preview-item d-flex align-items-center';
            div.innerHTML = `
                <video controls class="me-3">
                    <source src="${URL.createObjectURL(file)}" type="${file.type}">
                </video>
                <div>
                    <strong>${file.name}</strong><br>
                    <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                </div>
            `;
            preview.appendChild(div);
        }
    }
});

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

    function displaySearchResults(owners) {
        if (owners.length === 0) {
            ownerSearchResults.innerHTML = '<div class="autocomplete-item no-results"><i class="fas fa-search"></i> Không tìm thấy chủ sở hữu nào</div>';
        } else {
            ownerSearchResults.innerHTML = owners.map(owner => {
                const name = escapeHtml(owner.Name || '');
                const phone = escapeHtml(owner.Phone || '');
                const email = escapeHtml(owner.Email || '');
                const address = escapeHtml(owner.Address || '');

                return `
                    <div class="autocomplete-item" data-owner-id="${owner.UserID}" onclick="selectOwner(${owner.UserID}, '${name}', '${phone}', '${email}', '${address}')">
                        <div class="owner-name"><i class="fas fa-user me-2"></i>${name}</div>
                        <div class="owner-details"><i class="fas fa-phone me-1"></i>${phone} <i class="fas fa-envelope ms-2 me-1"></i>${email}</div>
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

function selectOwner(ownerId, name, phone, email, address) {
    const ownerSearchInput = document.getElementById('ownerSearch');
    const ownerSearchResults = document.getElementById('ownerSearchResults');
    const selectedOwnerIdInput = document.getElementById('selectedOwnerId');
    const selectedOwnerInfo = document.getElementById('selectedOwnerInfo');
    const ownerInfoDisplay = document.getElementById('ownerInfoDisplay');

    // Set the selected owner
    selectedOwnerIdInput.value = ownerId;
    ownerSearchInput.value = name;

    // Display selected owner info
    ownerInfoDisplay.innerHTML = `
        <strong><i class="fas fa-user"></i> ${name}</strong><br>
        <i class="fas fa-phone"></i> ${phone}<br>
        <i class="fas fa-envelope"></i> ${email}
        ${address ? `<br><i class="fas fa-map-marker-alt"></i> ${address}` : ''}
    `;

    selectedOwnerInfo.style.display = 'block';
    ownerSearchResults.style.display = 'none';

    // Auto-fill contact information in basic tab
    autoFillContactInfo(phone, email, address);

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

    // Clear auto-filled contact info
    clearContactInfo();
}

function autoFillContactInfo(phone, email, address) {
    // Try to find contact info fields in basic tab and auto-fill if empty
    const phoneField = document.querySelector('input[name="ContactPhone"]');
    const emailField = document.querySelector('input[name="ContactEmail"]');

    if (phoneField && !phoneField.value) {
        phoneField.value = phone;
    }

    if (emailField && !emailField.value) {
        emailField.value = email;
    }
}

function clearContactInfo() {
    // Clear auto-filled contact info
    const phoneField = document.querySelector('input[name="ContactPhone"]');
    const emailField = document.querySelector('input[name="ContactEmail"]');

    if (phoneField) phoneField.value = '';
    if (emailField) emailField.value = '';
}
</script>



