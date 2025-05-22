@extends('_layout._layowner.app')

@section('content')
<div class="property-dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fs-2 fw-bold">Bất động sản của tôi</h1>
        <button type="button" class="btn btn-primary rounded-3 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addPropertyModal">
            <i class="bi bi-plus-lg me-2"></i> Thêm bất động sản
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-0 rounded-3 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-secondary mb-1">Tổng số bất động sản</h6>
                        <h2 class="mb-0 fw-bold">{{ $properties->count() }}</h2>
                        <p class="text-muted mb-0 small">+{{ $properties->where('PostedDate', '>=', now()->startOfMonth())->count() }} bất động sản mới trong tháng này</p>
                    </div>
                    <div class="stats-icon text-secondary">
                        <i class="bi bi-building fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card h-100 border-0 rounded-3 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-secondary mb-1">Đang cho thuê</h6>
                        <h2 class="mb-0 fw-bold">{{ $properties->where('TypePro', 'Rent')->count() }}</h2>
                        <p class="text-muted mb-0 small">Tổng thu: {{ number_format($properties->where('TypePro', 'Rent')->sum('Price')) }} VNĐ/tháng</p>
                    </div>
                    <div class="stats-icon text-primary">
                        <i class="bi bi-house-door fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card h-100 border-0 rounded-3 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-secondary mb-1">Đang bán</h6>
                        <h2 class="mb-0 fw-bold">{{ $properties->where('TypePro', 'Sale')->count() }}</h2>
                        <p class="text-muted mb-0 small">Tổng giá trị: {{ number_format($properties->where('TypePro', 'Sale')->sum('Price')) }} tỷ VNĐ</p>
                    </div>
                    <div class="stats-icon text-success">
                        <i class="bi bi-cash-stack fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card h-100 border-0 rounded-3 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-secondary mb-1">Tỷ lệ lấp đầy</h6>
                        <h2 class="mb-0 fw-bold">
                            @php
                                $total = $properties->count();
                                $occupied = $properties->whereIn('TypePro', ['Rented', 'Sold'])->count();
                                $percentage = $total > 0 ? round(($occupied / $total) * 100) : 0;
                            @endphp
                            {{ $percentage }}%
                        </h2>
                        <p class="text-muted mb-0 small">Tăng 5% so với tháng trước</p>
                    </div>
                    <div class="stats-icon text-warning">
                        <i class="bi bi-percent fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tab Content -->
    <div class="tab-content" id="propertyTabsContent">

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="propertyTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="property-list-tab" data-bs-toggle="tab" data-bs-target="#property-list-content" type="button" role="tab" aria-controls="property-list-content" aria-selected="true">
                Danh sách bất động sản
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="property-posts-tab" data-bs-toggle="tab" data-bs-target="#property-posts-content" type="button" role="tab" aria-controls="property-posts-content" aria-selected="false">
                Tin đăng ký gửi bất động sản
            </button>
        </li>
    </ul>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tab Content -->
    <div class="tab-content" id="propertyTabsContent">
        <!-- Property List Tab -->
        <div class="tab-pane fade show active" id="property-list-content" role="tabpanel" aria-labelledby="property-list-tab">
            <!-- Search and Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control ps-0 border-start-0" placeholder="Tìm kiếm bất động sản..." id="propertySearch">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="propertyTypeFilter">
                        <option value="all">Tất cả loại BĐS</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->Protype_ID }}">{{ $category->ten_pro }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="propertyStatusFilter">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="Sale">Đang bán</option>
                        <option value="Rent">Đang cho thuê</option>
                        <option value="Sold">Đã bán</option>
                        <option value="Rented">Đã cho thuê</option>
                        <option value="inactive">Không hoạt động</option>
                    </select>
                </div>
            </div>

            <!-- Property List -->
            <div class="row property-list" id="propertyContainer">
                @forelse($properties as $property)
                    <div class="col-md-4 mb-4 property-item" 
                         data-type="{{ $property->PropertyType }}" 
                         data-status="{{ $property->TypePro }}"
                         data-title="{{ $property->Title }}"
                         data-address="{{ $property->Address }}">
                        <div class="card h-100 border-0 rounded-4 shadow-sm hover-shadow">
                            <!-- Property Image -->
                            <div class="property-image position-relative">
                                @php
                                    $thumbnailImage = $property->images->where('IsThumbnail', 1)->first();
                                    $firstImage = $property->images->first();
                                    $imageUrl = $thumbnailImage ? $thumbnailImage->ImageURL : ($firstImage ? $firstImage->ImageURL : null);
                                @endphp
                                
                                @if($imageUrl)
                                    <img src="{{ asset($imageUrl) }}" class="card-img-top rounded-top-4" alt="{{ $property->Title }}" style="height: 220px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded-top-4" style="height: 220px;">
                                        <i class="bi bi-image text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                                    </div>
                                @endif
                                
                                <!-- Options Menu Button -->
                                <div class="dropdown position-absolute top-0 end-0 m-3">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item edit-property" href="#" data-bs-toggle="modal" data-bs-target="#editPropertyModal" data-property-id="{{ $property->PropertyID }}">
                                                <i class="bi bi-pencil me-2 text-primary"></i> Chỉnh sửa
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewPropertyModal" data-property-id="{{ $property->PropertyID }}">
                                                <i class="bi bi-eye me-2 text-secondary"></i> Xem chi tiết
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#">
                                                <i class="bi bi-trash me-2"></i> Xóa
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="card-body p-4">
                                <!-- Property Type and Title -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0 fw-bold text-truncate" style="max-width: 80%;">{{ $property->Title }}</h5>
                                </div>
                                
                                <!-- Location -->
                                <p class="card-text text-muted mb-3 text-truncate">
                                    <i class="bi bi-geo-alt me-1"></i> 
                                    {{ $property->Address }}, {{ $property->Ward }}, {{ $property->District }}, {{ $property->Province }}
                                </p>
                                
                                <!-- Property Type Badge -->
                                <div class="mb-3">
                                    <span class="badge bg-light text-dark rounded-pill py-2 px-3 fw-normal">
                                        <i class="bi bi-building me-1"></i>
                                        {{ $property->danhMuc ? $property->danhMuc->ten_pro : 'Bất động sản' }}
                                    </span>
                                    
                                    <span class="badge rounded-pill py-2 px-3 fw-normal ms-2
                                        @if($property->TypePro == 'Sale') bg-danger
                                        @elseif($property->TypePro == 'Rent') bg-primary
                                        @elseif($property->TypePro == 'Sold') bg-success
                                        @elseif($property->TypePro == 'Rented') bg-info
                                        @else bg-secondary @endif">
                                        @if($property->TypePro == 'Rent')
                                            <i class="bi bi-key me-1"></i> Đang cho thuê
                                        @elseif($property->TypePro == 'Sale')
                                            <i class="bi bi-tag me-1"></i> Đang bán
                                        @elseif($property->TypePro == 'Rented')
                                            <i class="bi bi-check-circle me-1"></i> Đã cho thuê
                                        @elseif($property->TypePro == 'Sold')
                                            <i class="bi bi-check-circle me-1"></i> Đã bán
                                        @else
                                            {{ $property->TypePro }}
                                        @endif
                                    </span>
                                </div>
                                
                                <!-- Property Features -->
                                <div class="d-flex justify-content-between text-center mb-3 property-features-container">
                                    <div class="property-feature">
                                        <i class="bi bi-rulers d-block mb-1 fs-5"></i>
                                        <span class="fw-bold d-block">{{ $property->chiTiet->Area ?? 'N/A' }}</span>
                                        <small class="text-muted">m²</small>
                                    </div>
                                    <div class="property-feature">
                                        <i class="bi bi-door-open d-block mb-1 fs-5"></i>
                                        <span class="fw-bold d-block">{{ $property->chiTiet->Bedroom ?? '0' }}</span>
                                        <small class="text-muted">Phòng ngủ</small>
                                    </div>
                                    <div class="property-feature">
                                        <i class="bi bi-droplet d-block mb-1 fs-5"></i>
                                        <span class="fw-bold d-block">{{ $property->chiTiet->Bath_WC ?? '0' }}</span>
                                        <small class="text-muted">Phòng tắm</small>
                                    </div>
                                </div>
                                
                                <!-- Utility Prices -->
                                @if($property->TypePro == 'Rent' || $property->TypePro == 'Rented')
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if($property->chiTiet && $property->chiTiet->WaterPrice)
                                    <span class="badge bg-info text-dark">
                                        <i class="bi bi-droplet-fill me-1"></i> Nước: 
                                        {{ number_format($property->chiTiet->WaterPrice) }} VNĐ
                                    </span>
                                    @endif
                                    
                                    @if($property->chiTiet && $property->chiTiet->PowerPrice)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-lightning-fill me-1"></i> Điện: 
                                        {{ number_format($property->chiTiet->PowerPrice) }} VNĐ
                                    </span>
                                    @endif
                                    
                                    @if($property->chiTiet && $property->chiTiet->Utilities)
                                    <span class="badge bg-secondary text-white">
                                        <i class="bi bi-tools me-1"></i> {{ $property->chiTiet->Utilities }}
                                    </span>
                                    @endif
                                </div>
                                @endif
                                
                                <!-- Price Information -->
                                <div class="d-flex justify-content-between align-items-center price-edit-container">
                                    @if($property->TypePro == 'Rent' || $property->TypePro == 'Rented')
                                        <div>
                                            <h5 class="fw-bold mb-0 text-primary">{{ number_format($property->Price / 1000000) }} triệu VNĐ<span class="fs-6 fw-normal">/tháng</span></h5>
                                        </div>
                                    @else
                                        <div>
                                            <h5 class="fw-bold mb-0 text-primary">{{ number_format($property->Price / 1000000000, 1) }} tỷ VNĐ</h5>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <button class="btn btn-outline-primary edit-property" data-bs-toggle="modal" data-bs-target="#editPropertyModal" data-property-id="{{ $property->PropertyID }}">
                                            <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Bạn chưa có bất động sản nào. Hãy thêm bất động sản đầu tiên của bạn!
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Property Posts Tab -->
        <div class="tab-pane fade" id="property-posts-content" role="tabpanel" aria-labelledby="property-posts-tab">
            <!-- Sub Nav for Property Posts -->
            <ul class="nav nav-pills mb-4">
                <li class="nav-item">
                    <a class="nav-link active" href="#" id="allPostsTab">Tất cả</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="approvedPostsTab">Đã duyệt</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="pendingPostsTab">Chờ xử lý</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="sellingPostsTab">Đang thương lượng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="canceledPostsTab">Từ chối</a>
                </li>
            </ul>

            <!-- Search bar for property listings -->
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control ps-0 border-start-0" placeholder="Tìm kiếm tin đăng..." id="propertyPostSearch">
                    <button type="button" class="btn btn-primary ms-2 rounded" id="addNewPostBtn" data-bs-toggle="modal" data-bs-target="#createPropertyListingModal">
                        <i class="bi bi-plus-lg me-1"></i> Tạo tin đăng mới
                    </button>
                </div>
            </div>

            <!-- Property Listings Table -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Mã tin</th>
                                    <th>Bất động sản</th>
                                    <th>Loại</th>
                                    <th>Giá</th>
                                    <th>Trạng thái</th>
                                    <th>Tiến độ</th>
                                    <th>Thanh toán</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Listing 1 -->
                                <tr>
                                    <td class="ps-4 fw-medium">LST-2025-001</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="https://via.placeholder.com/50x50" class="rounded" width="50" height="50" alt="Property image">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-medium">Căn hộ cao cấp, Trung tâm</h6>
                                                <small class="text-muted">123 Nguyễn Huệ, Quận 1, TP.HCM</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-danger">Bán</span></td>
                                    <td class="fw-bold">5.2 tỷ VNĐ</td>
                                    <td>
                                        <span class="badge bg-success">Đã duyệt</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                        </div>
                                        <small class="text-muted">100%</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2 text-primary"></i> Xem chi tiết</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2 text-secondary"></i> Chỉnh sửa</a></li>
                                                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i> Xoá</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Listing 2 -->
                                <tr>
                                    <td class="ps-4 fw-medium">LST-2025-002</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="https://via.placeholder.com/50x50" class="rounded" width="50" height="50" alt="Property image">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-medium">Nhà phố tiện kế</h6>
                                                <small class="text-muted">456 Lê Văn Lương, Quận 7, TP.HCM</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-primary">Cho thuê</span></td>
                                    <td class="fw-bold">35 triệu VNĐ/tháng</td>
                                    <td>
                                        <span class="badge text-bg-primary">Chờ duyệt</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 80%"></div>
                                        </div>
                                        <small class="text-muted">80%</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2 text-primary"></i> Xem chi tiết</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2 text-secondary"></i> Chỉnh sửa</a></li>
                                                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i> Xoá</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Listing 3 -->
                                <tr>
                                    <td class="ps-4 fw-medium">LST-2025-003</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="https://via.placeholder.com/50x50" class="rounded" width="50" height="50" alt="Property image">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-medium">Biệt thự view sông</h6>
                                                <small class="text-muted">789 Nguyễn Văn Linh, Quận 7, TP.HCM</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-danger">Bán</span></td>
                                    <td class="fw-bold">25.0 tỷ VNĐ</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 60%"></div>
                                        </div>
                                        <small class="text-muted">60%</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2 text-primary"></i> Xem chi tiết</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2 text-secondary"></i> Chỉnh sửa</a></li>
                                                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i> Xoá</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Listing 4 -->
                                <tr>
                                    <td class="ps-4 fw-medium">LST-2025-004</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="https://via.placeholder.com/50x50" class="rounded" width="50" height="50" alt="Property image">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-medium">Căn hộ dịch vụ cao cấp</h6>
                                                <small class="text-muted">101 Lê Lợi, Quận 1, TP.HCM</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-primary">Cho thuê</span></td>
                                    <td class="fw-bold">18 triệu VNĐ/tháng</td>
                                    <td>
                                        <span class="badge bg-info">Đang thương lượng</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 40%"></div>
                                        </div>
                                        <small class="text-muted">40%</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2 text-primary"></i> Xem chi tiết</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2 text-secondary"></i> Chỉnh sửa</a></li>
                                                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i> Xoá</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Listing 5 -->
                                <tr>
                                    <td class="ps-4 fw-medium">LST-2025-005</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="https://via.placeholder.com/50x50" class="rounded" width="50" height="50" alt="Property image">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-medium">Đất nền khu đô thị mới</h6>
                                                <small class="text-muted">202 Võ Văn Kiệt, Quận 5, TP.HCM</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-danger">Bán</span></td>
                                    <td class="fw-bold">12.0 tỷ VNĐ</td>
                                    <td>
                                        <span class="badge bg-secondary">Chờ tiến hành</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar bg-secondary" role="progressbar" style="width: 20%"></div>
                                        </div>
                                        <small class="text-muted">20%</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2 text-primary"></i> Xem chi tiết</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2 text-secondary"></i> Chỉnh sửa</a></li>
                                                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i> Xoá</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Listing 6 -->
                                <tr>
                                    <td class="ps-4 fw-medium">LST-2025-006</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="https://via.placeholder.com/50x50" class="rounded" width="50" height="50" alt="Property image">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-medium">Nhà phố thương mại</h6>
                                                <small class="text-muted">303 Điện Biên Phủ, Quận 3, TP.HCM</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-danger">Bán</span></td>
                                    <td class="fw-bold">15.0 tỷ VNĐ</td>
                                    <td>
                                        <span class="badge bg-danger">Từ chối</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted">0%</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">Đã hoàn tiền</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2 text-primary"></i> Xem chi tiết</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2 text-secondary"></i> Chỉnh sửa</a></li>
                                                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i> Xoá</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Property Modal -->
<div class="modal fade" id="addPropertyModal" tabindex="-1" aria-labelledby="addPropertyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPropertyModalLabel">Thêm Bất Động Sản</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        @include('_system.partialview.create_property')
      </div>
    </div>
  </div>
</div>

<!-- Include Property Listing Modal -->
@include('_system.partialview.create_property_listing')

<!-- Edit Property Modal -->
<div class="modal fade" id="editPropertyModal" tabindex="-1" aria-labelledby="editPropertyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      @include('_system.partialview.edit_property')
    </div>
  </div>
</div>

<!-- View Property Modal -->
<div class="modal fade" id="viewPropertyModal" tabindex="-1" aria-labelledby="viewPropertyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      @include('_system.partialview.view_property_details')
    </div>
  </div>
</div>

{{-- <style>
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-3px);
        transition: all 0.3s ease;
    }
    .stats-icon {
        opacity: 0.8;
    }
    .property-feature {
        flex: 1;
        padding: 0.5rem;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
    }
    .rounded-4 {
        border-radius: 0.75rem !important;
    }
    .rounded-top-4 {
        border-top-left-radius: 0.75rem !important;
        border-top-right-radius: 0.75rem !important;
    }
    .card-body {
        padding: 1.25rem !important;
    }
    .property-item .card {
        display: flex;
        flex-direction: column;
    }
    .property-item .card-body {
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
    }
    .property-item .card-body > .d-flex:last-child {
        margin-top: auto;
    }
    /* Fix for buttons at bottom */
    .property-item .btn-outline-primary {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    /* Fix for dropdown menus */
    .dropdown-menu-end {
        right: 0;
        left: auto;
    }
    .property-image {
        position: relative;
        height: 220px;
    }
    .property-image img, 
    .property-image .bg-light {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
    /* Better responsive styling */
    @media (max-width: 767.98px) {
        .property-item {
            margin-bottom: 1.5rem;
        }
        .property-image {
            height: 180px;
        }
    }
    
    .card-title {
        height: 1.5em;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
    }
    
    .property-features-container {
        min-height: 85px;
    }
    
    .price-edit-container {
        margin-top: auto;
        padding-top: 0.5rem;
    }
    
    /* Styles for Property Listings Tab */
    .nav-pills .nav-link {
        border-radius: 50rem;
        padding: 0.5rem 1rem;
        color: #6c757d;
    }
    .nav-pills .nav-link.active {
        background-color: #0d6efd;
        color: white;
    }
    .progress {
        background-color: #e9ecef;
        border-radius: 0.25rem;
    }
    .table th, .table td {
        padding: 1rem 0.5rem;
        vertical-align: middle;
    }
    .table thead th {
        font-weight: 600;
        color: #495057;
        border-bottom-width: 1px;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    /* Responsive styles for property listings */
    @media (max-width: 767.98px) {
        .table-responsive {
            border-radius: 0.5rem;
        }
        .table th, .table td {
            white-space: nowrap;
        }
    }
    
    /* Styles for Create Property Listing Modal */
    .listing-steps .step-item {
        position: relative;
        cursor: pointer;
    }
    .listing-steps .step-item.active {
        font-weight: 600;
    }
    .property-type-option {
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .property-type-option:hover {
        border-color: #0d6efd !important;
    }
    .property-type-option input[type="radio"] {
        position: absolute;
        top: 15px;
        right: 15px;
    }
    .form-check-input:checked ~ .form-check-label .icon-wrapper {
        background-color: #e6f0ff !important;
    }
    .property-type-option:has(input:checked) {
        border-color: #0d6efd !important;
        background-color: #f8f9ff;
    }
</style>

<script>
// Property Search and Filter
document.addEventListener('DOMContentLoaded', function() {
    // Property List Tab
    const searchInput = document.getElementById('propertySearch');
    const typeFilter = document.getElementById('propertyTypeFilter');
    const statusFilter = document.getElementById('propertyStatusFilter');
    const propertyItems = document.querySelectorAll('.property-item');
    
    // Property Posts Tab
    const propertyPostSearch = document.getElementById('propertyPostSearch');
    const allPostsTab = document.getElementById('allPostsTab');
    const approvedPostsTab = document.getElementById('approvedPostsTab');
    const pendingPostsTab = document.getElementById('pendingPostsTab');
    const sellingPostsTab = document.getElementById('sellingPostsTab');
    const canceledPostsTab = document.getElementById('canceledPostsTab');
    const addNewPostBtn = document.getElementById('addNewPostBtn');
    
    const filterProperties = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const typeValue = typeFilter.value;
        const statusValue = statusFilter.value;
        
        propertyItems.forEach(item => {
            const title = item.dataset.title.toLowerCase();
            const address = item.dataset.address.toLowerCase();
            const type = item.dataset.type;
            const status = item.dataset.status;
            
            const matchesSearch = title.includes(searchTerm) || address.includes(searchTerm);
            const matchesType = typeValue === 'all' || type === typeValue;
            const matchesStatus = statusValue === 'all' || status === statusValue;
            
            if (matchesSearch && matchesType && matchesStatus) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    };
    
    if (searchInput) searchInput.addEventListener('input', filterProperties);
    if (typeFilter) typeFilter.addEventListener('change', filterProperties);
    if (statusFilter) statusFilter.addEventListener('change', filterProperties);
    
    // Xử lý chỉnh sửa bất động sản
    const editButtons = document.querySelectorAll('.edit-property');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // No need to prevent default since we're using data-bs-toggle and data-bs-target
            const propertyId = this.dataset.propertyId;
            
            // Gọi API để lấy thông tin bất động sản
            fetch(`/api/properties/${propertyId}`)
                .then(response => response.json())
                .then(data => {
                    // Mock data cho demo - trong thực tế, sẽ lấy từ response API
                    const mockData = {
                        PropertyID: propertyId,
                        Title: 'Căn hộ cao cấp, Trung tâm',
                        Address: '123 Nguyễn Huệ, Quận 1, TP.HCM',
                        Ward: 'Quận 1',
                        District: 'Quận 1',
                        Province: 'TP.HCM',
                        TypePro: 'Sale',
                        PropertyType: 1,
                        Price: 5200000000,
                        Description: 'Căn hộ cao cấp tại trung tâm thành phố với đầy đủ tiện nghi, view đẹp, an ninh 24/7.',
                        PostedDate: '2023-05-15',
                        chiTiet: {
                            Area: 85,
                            Bedroom: 2,
                            Bath_WC: 2,
                            Floor: 2,
                            legal: 'Sổ đỏ / sổ hồng',
                            Interior: 'đầy đủ',
                            WaterPrice: 30000,
                            PowerPrice: 3500,
                            Utilities: 'Có phí dịch vụ',
                            view: 'Sông Sài Gòn',
                            near: 'Công viên, trường học',
                            Road: 'Đường 12m'
                        },
                        danhMuc: {
                            ten_pro: 'Căn hộ'
                        },
                        images: [
                            {ImageURL: 'https://via.placeholder.com/600x400', IsThumbnail: 1}
                        ]
                    };
                    
                    // Điền thông tin vào form
                    document.getElementById('edit_property_id').value = mockData.PropertyID;
                    document.getElementById('edit_title').value = mockData.Title;
                    document.getElementById('edit_address').value = mockData.Address;
                    document.getElementById('edit_district').value = mockData.District;
                    document.getElementById('edit_ward').value = mockData.Ward;
                    document.getElementById('edit_province').value = mockData.Province;
                    document.getElementById('edit_type_pro').value = mockData.TypePro;
                    document.getElementById('edit_property_type').value = mockData.PropertyType;
                    document.getElementById('edit_price').value = mockData.Price;
                    document.getElementById('edit_description').value = mockData.Description;
                    
                    // Thông tin chi tiết
                    if (mockData.chiTiet) {
                        document.getElementById('edit_area').value = mockData.chiTiet.Area;
                        document.getElementById('edit_bedroom').value = mockData.chiTiet.Bedroom;
                        document.getElementById('edit_bathroom').value = mockData.chiTiet.Bath_WC;
                        document.getElementById('edit_floor').value = mockData.chiTiet.Floor;
                        document.getElementById('edit_legal').value = mockData.chiTiet.legal;
                        document.getElementById('edit_interior').value = mockData.chiTiet.Interior;
                        
                        // Load utility prices and additional fields
                        if (document.getElementById('edit_water_price'))
                            document.getElementById('edit_water_price').value = mockData.chiTiet.WaterPrice || '';
                        if (document.getElementById('edit_power_price'))
                            document.getElementById('edit_power_price').value = mockData.chiTiet.PowerPrice || '';
                        if (document.getElementById('edit_utilities'))
                            document.getElementById('edit_utilities').value = mockData.chiTiet.Utilities || '';
                        if (document.getElementById('edit_view'))
                            document.getElementById('edit_view').value = mockData.chiTiet.view || '';
                        if (document.getElementById('edit_near'))
                            document.getElementById('edit_near').value = mockData.chiTiet.near || '';
                        if (document.getElementById('edit_road'))
                            document.getElementById('edit_road').value = mockData.chiTiet.Road || '';
                    }
                    
                    // Handle legal documents
                    if (mockData.chiTiet && mockData.chiTiet.legal) {
                        const legalDocs = mockData.chiTiet.legal.split('/').map(doc => doc.trim());
                        
                        // Check the corresponding checkboxes
                        if (legalDocs.includes('Sổ hồng') && document.getElementById('edit_so_hong'))
                            document.getElementById('edit_so_hong').checked = true;
                        if (legalDocs.includes('Sổ đỏ') && document.getElementById('edit_so_do'))
                            document.getElementById('edit_so_do').checked = true;
                        if (legalDocs.includes('Hợp đồng mua bán') && document.getElementById('edit_hop_dong_mua_ban'))
                            document.getElementById('edit_hop_dong_mua_ban').checked = true;
                        // Add other document types as needed
                    }
                    
                    // Hiển thị hình ảnh
                    const imagesContainer = document.getElementById('property_images_container');
                    imagesContainer.innerHTML = '';
                    
                    if (mockData.images && mockData.images.length > 0) {
                        mockData.images.forEach(image => {
                            const imgEl = document.createElement('div');
                            imgEl.className = 'position-relative';
                            imgEl.innerHTML = `
                                <img src="${image.ImageURL}" alt="Property image" style="width: 120px; height: 90px; object-fit: cover; border-radius: 4px;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle" style="width: 24px; height: 24px; padding: 0; line-height: 24px;">
                                    <i class="bi bi-x"></i>
                                </button>
                                ${image.IsThumbnail ? '<div class="position-absolute bottom-0 start-0 bg-primary text-white px-2 py-1 small rounded-end">Ảnh bìa</div>' : ''}
                            `;
                            imagesContainer.appendChild(imgEl);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });
    
    // Xử lý xem chi tiết bất động sản
    const viewDetailButtons = document.querySelectorAll('[data-bs-target="#viewPropertyModal"]');
    const viewPropertyModal = new bootstrap.Modal(document.getElementById('viewPropertyModal'));
    
    viewDetailButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const propertyId = this.dataset.propertyId;
            
            // Gọi API để lấy thông tin bất động sản
            fetch(`/api/properties/${propertyId}`)
                .then(response => response.json())
                .then(data => {
                    // Mock data cho demo - trong thực tế, sẽ lấy từ response API
                    const mockData = {
                        PropertyID: propertyId,
                        Title: 'Căn hộ cao cấp, Trung tâm',
                        Address: '123 Nguyễn Huệ, Quận 1, TP.HCM',
                        Ward: 'Quận 1',
                        District: 'Quận 1',
                        Province: 'TP.HCM',
                        TypePro: 'Sale',
                        PropertyType: 1,
                        Price: 5200000000,
                        Description: 'Căn hộ cao cấp tại trung tâm thành phố với đầy đủ tiện nghi, view đẹp, an ninh 24/7.',
                        PostedDate: '2023-05-15',
                        chiTiet: {
                            Area: 85,
                            Bedroom: 2,
                            Bath_WC: 2,
                            Floor: 2,
                            legal: 'Sổ đỏ / sổ hồng',
                            Interior: 'đầy đủ',
                            WaterPrice: 30000,
                            PowerPrice: 3500,
                            Utilities: 'Có phí dịch vụ',
                            view: 'Sông Sài Gòn',
                            near: 'Công viên, trường học',
                            Road: 'Đường 12m'
                        },
                        danhMuc: {
                            ten_pro: 'Căn hộ'
                        },
                        images: [
                            {ImageURL: 'https://via.placeholder.com/600x400', IsThumbnail: 1}
                        ]
                    };
                    
                    // Điền thông tin vào modal xem chi tiết
                    document.getElementById('view_property_id').value = mockData.PropertyID;
                    document.getElementById('view_property_title').textContent = mockData.Title;
                    document.getElementById('view_property_address').textContent = `${mockData.Address}, ${mockData.Ward}, ${mockData.District}, ${mockData.Province}`;
                    
                    // Thiết lập hình ảnh
                    if (mockData.images && mockData.images.length > 0) {
                        const thumbnail = mockData.images.find(img => img.IsThumbnail) || mockData.images[0];
                        document.getElementById('view_property_image').src = thumbnail.ImageURL;
                    } else {
                        document.getElementById('view_property_image').src = 'https://via.placeholder.com/600x400?text=No+Image';
                    }
                    
                    // Thiết lập badge trạng thái
                    const statusBadge = document.getElementById('view_property_status');
                    if (mockData.TypePro === 'Sale') {
                        statusBadge.className = 'badge rounded-pill py-2 px-3 fw-normal bg-danger';
                        statusBadge.innerHTML = '<i class="bi bi-tag me-1"></i> Đang bán';
                    } else if (mockData.TypePro === 'Rent') {
                        statusBadge.className = 'badge rounded-pill py-2 px-3 fw-normal bg-primary';
                        statusBadge.innerHTML = '<i class="bi bi-key me-1"></i> Đang cho thuê';
                    } else if (mockData.TypePro === 'Sold') {
                        statusBadge.className = 'badge rounded-pill py-2 px-3 fw-normal bg-success';
                        statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i> Đã bán';
                    } else if (mockData.TypePro === 'Rented') {
                        statusBadge.className = 'badge rounded-pill py-2 px-3 fw-normal bg-info';
                        statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i> Đã cho thuê';
                    }
                    
                    // Thông tin cơ bản
                    document.getElementById('view_property_type').textContent = mockData.danhMuc ? mockData.danhMuc.ten_pro : 'Bất động sản';
                    document.getElementById('view_property_area').textContent = mockData.chiTiet.Area ? `${mockData.chiTiet.Area} m²` : 'N/A';
                    document.getElementById('view_property_bedroom').textContent = mockData.chiTiet.Bedroom || '0';
                    document.getElementById('view_property_bathroom').textContent = mockData.chiTiet.Bath_WC || '0';
                    
                    // Giá bán và giá thuê
                    if (mockData.TypePro === 'Sale' || mockData.TypePro === 'Sold') {
                        document.getElementById('view_property_price_sale').textContent = `${(mockData.Price / 1000000000).toFixed(1)} tỷ VNĐ`;
                        document.getElementById('view_property_price_rent').textContent = 'N/A';
                    } else if (mockData.TypePro === 'Rent' || mockData.TypePro === 'Rented') {
                        document.getElementById('view_property_price_sale').textContent = 'N/A';
                        document.getElementById('view_property_price_rent').textContent = `${(mockData.Price / 1000000)} triệu VNĐ/tháng`;
                    } else {
                        document.getElementById('view_property_price_sale').textContent = `${(mockData.Price / 1000000000).toFixed(1)} tỷ VNĐ`;
                        document.getElementById('view_property_price_rent').textContent = 'Liên hệ';
                    }
                    
                    // Thông tin chi tiết
                    document.getElementById('view_property_description').textContent = mockData.Description;
                    document.getElementById('view_property_floor').textContent = mockData.chiTiet.Floor || 'N/A';
                    document.getElementById('view_property_interior').textContent = mockData.chiTiet.Interior || 'N/A';
                    document.getElementById('view_property_view').textContent = mockData.chiTiet.view || 'N/A';
                    document.getElementById('view_property_road').textContent = mockData.chiTiet.Road || 'N/A';
                    document.getElementById('view_property_posted_date').textContent = new Date(mockData.PostedDate).toLocaleDateString('vi-VN');
                    document.getElementById('view_property_status_text').textContent = getStatusText(mockData.TypePro);
                    
                    // Phí dịch vụ (chỉ hiển thị cho thuê)
                    const utilityPricesSection = document.getElementById('utility_prices_section');
                    if (mockData.TypePro === 'Rent' || mockData.TypePro === 'Rented') {
                        utilityPricesSection.style.display = 'block';
                        document.getElementById('view_property_water_price').textContent = mockData.chiTiet.WaterPrice ? 
                            isNaN(mockData.chiTiet.WaterPrice) ? mockData.chiTiet.WaterPrice : `${Number(mockData.chiTiet.WaterPrice).toLocaleString('vi-VN')} VNĐ` : 'N/A';
                        document.getElementById('view_property_power_price').textContent = mockData.chiTiet.PowerPrice ? 
                            isNaN(mockData.chiTiet.PowerPrice) ? mockData.chiTiet.PowerPrice : `${Number(mockData.chiTiet.PowerPrice).toLocaleString('vi-VN')} VNĐ` : 'N/A';
                        document.getElementById('view_property_utilities').textContent = mockData.chiTiet.Utilities || 'N/A';
                    } else {
                        utilityPricesSection.style.display = 'none';
                    }
                    
                    // Địa điểm lân cận
                    document.getElementById('view_property_near').textContent = mockData.chiTiet.near || 'Không có thông tin';
                    
                    // Giấy tờ pháp lý
                    const legalDocsContainer = document.getElementById('legal_documents');
                    legalDocsContainer.innerHTML = '';
                    
                    if (mockData.chiTiet.legal) {
                        const legalDocs = mockData.chiTiet.legal.split('/').map(doc => doc.trim());
                        legalDocs.forEach(doc => {
                            const docElement = document.createElement('div');
                            docElement.className = 'mb-2';
                            docElement.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                    <span>${doc}</span>
                                </div>
                            `;
                            legalDocsContainer.appendChild(docElement);
                        });
                    } else {
                        legalDocsContainer.innerHTML = '<p class="text-muted">Không có thông tin giấy tờ pháp lý</p>';
                    }
                    
                    // Thiết lập nút chỉnh sửa
                    const editFromViewBtn = document.querySelector('.edit-from-view');
                    editFromViewBtn.dataset.propertyId = mockData.PropertyID;
                    
                    // Thêm sự kiện cho nút chỉnh sửa từ xem chi tiết
                    editFromViewBtn.addEventListener('click', function() {
                        // Đóng modal xem chi tiết
                        viewPropertyModal.hide();
                        
                        // Tìm nút chỉnh sửa tương ứng và kích hoạt
                        const editBtn = document.querySelector(`.edit-property[data-property-id="${this.dataset.propertyId}"]`);
                        if (editBtn) {
                            // Chờ một chút để modal view đóng hoàn toàn trước khi mở modal edit
                            setTimeout(() => {
                                editBtn.click();
                            }, 500);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });
    
    // Hàm helper để hiển thị trạng thái
    function getStatusText(typePro) {
        switch(typePro) {
            case 'Sale': return 'Đang bán';
            case 'Rent': return 'Đang cho thuê';
            case 'Sold': return 'Đã bán';
            case 'Rented': return 'Đã cho thuê';
            default: return typePro;
        }
    }
    
    // Xử lý form submit
    const editPropertyForm = document.getElementById('editPropertyForm');
    if (editPropertyForm) {
        editPropertyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Trong thực tế, gửi dữ liệu qua AJAX
            // const formData = new FormData(this);
            
            // Đóng modal
            const editPropertyModal = bootstrap.Modal.getInstance(document.getElementById('editPropertyModal'));
            editPropertyModal.hide();
            
            // Hiển thị thông báo thành công
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show">
                    Cập nhật thông tin bất động sản thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.querySelector('.property-dashboard').insertAdjacentHTML('afterbegin', alertHtml);
        });
    }
    
    // Xử lý Tin đăng ký gửi bất động sản
    // Lọc đăng tin theo trạng thái
    const filterPostListings = (status = 'all') => {
        const postRows = document.querySelectorAll('#property-posts-content tbody tr');
        const searchTerm = propertyPostSearch ? propertyPostSearch.value.toLowerCase() : '';
        
        postRows.forEach(row => {
            const postTitle = row.querySelector('h6').textContent.toLowerCase();
            const postAddress = row.querySelector('small').textContent.toLowerCase();
            const postStatus = row.querySelector('td:nth-child(5) .badge').textContent.toLowerCase();
            
            // Tìm kiếm phải match với title hoặc địa chỉ
            const matchesSearch = postTitle.includes(searchTerm) || postAddress.includes(searchTerm) || searchTerm === '';
            
            // Lọc theo trạng thái
            let matchesStatus = true;
            if (status !== 'all') {
                switch(status) {
                    case 'approved':
                        matchesStatus = postStatus.includes('đã duyệt');
                        break;
                    case 'pending':
                        matchesStatus = postStatus.includes('chờ duyệt') || postStatus.includes('chờ tiến hành') || postStatus.includes('chờ thanh toán');
                        break;
                    case 'selling':
                        matchesStatus = postStatus.includes('thương lượng');
                        break;
                    case 'canceled':
                        matchesStatus = postStatus.includes('từ chối');
                        break;
                }
            }
            
            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
        });
    };
    
    // Lắng nghe events cho tab filter và search
    if (propertyPostSearch) {
        propertyPostSearch.addEventListener('input', () => filterPostListings('all'));
    }
    
    if (allPostsTab) {
        allPostsTab.addEventListener('click', function(e) {
            e.preventDefault();
            setActivePostTab(this);
            filterPostListings('all');
        });
    }
    
    if (approvedPostsTab) {
        approvedPostsTab.addEventListener('click', function(e) {
            e.preventDefault();
            setActivePostTab(this);
            filterPostListings('approved');
        });
    }
    
    if (pendingPostsTab) {
        pendingPostsTab.addEventListener('click', function(e) {
            e.preventDefault();
            setActivePostTab(this);
            filterPostListings('pending');
        });
    }
    
    if (sellingPostsTab) {
        sellingPostsTab.addEventListener('click', function(e) {
            e.preventDefault();
            setActivePostTab(this);
            filterPostListings('selling');
        });
    }
    
    if (canceledPostsTab) {
        canceledPostsTab.addEventListener('click', function(e) {
            e.preventDefault();
            setActivePostTab(this);
            filterPostListings('canceled');
        });
    }
    
    // Helper function để set active tab
    function setActivePostTab(tab) {
        const postTabs = document.querySelectorAll('#property-posts-content .nav-link');
        postTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
    }
    
    // Xử lý nút thêm tin đăng mới
    if (addNewPostBtn) {
        addNewPostBtn.addEventListener('click', function(e) {
            // Kiểm tra xem modal đã được initialized chưa
            if (!bootstrap.Modal.getInstance(document.getElementById('createPropertyListingModal'))) {
                // Mở modal đã được include trong trang
                const createListingModal = new bootstrap.Modal(document.getElementById('createPropertyListingModal'));
                createListingModal.show();
            }
            
            // Tải dữ liệu cho modal
            fetch("{{ route('owner.property.get-for-listing') }}")
                .then(response => response.json())
                .then(data => {
                    console.log('Loaded properties for modal:', data);
                })
                .catch(error => {
                    console.error('Error loading properties:', error);
                });
        });
    }
    
    // Xử lý form tạo tin đăng
    const propertyTypeOptions = document.querySelectorAll('.property-type-option');
    if (propertyTypeOptions.length > 0) {
        propertyTypeOptions.forEach(option => {
            option.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
            });
        });
    }
    
    // Nút tiếp tục trong form tạo tin đăng
    const nextStepBtn = document.getElementById('nextStepBtn');
    if (nextStepBtn) {
        nextStepBtn.addEventListener('click', function() {
            // Trong thực tế, sẽ chuyển đến bước tiếp theo
            alert('Chuyển đến bước tiếp theo của quá trình tạo tin đăng');
        });
    }
    
    // Nút xem trước tin đăng
    const previewListingBtn = document.getElementById('previewListingBtn');
    if (previewListingBtn) {
        previewListingBtn.addEventListener('click', function() {
            alert('Xem trước tin đăng');
        });
    }
    
    // Xử lý dropdown provinces/cities/districts
    const propertyCity = document.getElementById('propertyCity');
    const propertyDistrict = document.getElementById('propertyDistrict');
    const propertyWard = document.getElementById('propertyWard');
    
    if (propertyCity) {
        propertyCity.addEventListener('change', function() {
            // Fake data for districts based on selected city
            let districtOptions = '<option selected>Chọn Quận/Huyện</option>';
            
            if (this.value === 'HCM') {
                districtOptions += `
                    <option value="Q1">Quận 1</option>
                    <option value="Q2">Quận 2</option>
                    <option value="Q3">Quận 3</option>
                    <option value="Q7">Quận 7</option>
                    <option value="QBT">Quận Bình Thạnh</option>
                `;
            } else if (this.value === 'HN') {
                districtOptions += `
                    <option value="HK">Hoàn Kiếm</option>
                    <option value="BD">Ba Đình</option>
                    <option value="TX">Thanh Xuân</option>
                `;
            }
            
            if (propertyDistrict) {
                propertyDistrict.innerHTML = districtOptions;
                // Reset ward selection
                if (propertyWard) {
                    propertyWard.innerHTML = '<option selected>Chọn Phường/Xã</option>';
                }
            }
        });
    }
    
    if (propertyDistrict) {
        propertyDistrict.addEventListener('change', function() {
            // Fake data for wards based on selected district
            let wardOptions = '<option selected>Chọn Phường/Xã</option>';
            
            if (this.value === 'Q1') {
                wardOptions += `
                    <option value="P1">Phường Bến Nghé</option>
                    <option value="P2">Phường Bến Thành</option>
                    <option value="P3">Phường Đa Kao</option>
                `;
            } else if (this.value === 'QBT') {
                wardOptions += `
                    <option value="P1">Phường 1</option>
                    <option value="P2">Phường 2</option>
                    <option value="P3">Phường 3</option>
                `;
            }
            
            if (propertyWard) {
                propertyWard.innerHTML = wardOptions;
            }
        });
    }
});
</script> --}}
@endsection
