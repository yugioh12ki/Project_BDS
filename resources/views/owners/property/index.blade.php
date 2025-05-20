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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                        <div class="position-absolute top-0 end-0 m-3">
                            <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Chỉnh sửa</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>Xem chi tiết</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i>Xóa</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Property Type and Title -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0 fw-bold text-truncate" style="max-width: 80%;">{{ $property->Title }}</h5>
                        </div>
                        
                        <!-- Location -->
                        <p class="card-text text-muted mb-3">
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
                        <div class="d-flex justify-content-between text-center mb-3">
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
                        
                        <!-- Price Information -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
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
                                <a href="{{ route('owner.property.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> Chi tiết
                                </a>
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

<style>
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
</style>

<script>
// Property Search and Filter
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('propertySearch');
    const typeFilter = document.getElementById('propertyTypeFilter');
    const statusFilter = document.getElementById('propertyStatusFilter');
    const propertyItems = document.querySelectorAll('.property-item');
    
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
});
</script>
@endsection
