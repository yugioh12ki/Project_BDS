@extends('_layout._layowner.app')

@section('property')
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
                                        {{ $property->chiTiet->WaterPrice }}
                                    </span>
                                    @endif
                                    
                                    @if($property->chiTiet && $property->chiTiet->PowerPrice)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-lightning-fill me-1"></i> Điện: 
                                        {{ $property->chiTiet->PowerPrice }}
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
                                    <th>Bất động sản</th>
                                    <th>Loại</th>
                                    <th>Giá</th>
                                    <th>Trạng thái</th>
                                    <th>Thanh toán</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Listing 1 -->
                                <tr>
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
@include('_system.partialview.create_property_listing', ['ownerProperties' => $ownerProperties])

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

@endsection
