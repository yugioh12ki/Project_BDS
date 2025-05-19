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
                <div class="card h-100 border rounded shadow-sm hover-shadow">
                    <div class="placeholder-image bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-building text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $property->Title }}</h5>
                        <p class="card-text text-muted">
                            <i class="bi bi-geo-alt"></i> 
                            {{ $property->Ward }}, {{ $property->District }}, {{ $property->Province }}
                        </p>
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <span class="d-block"><strong>Giá: {{ number_format($property->Price) }} ₫</strong></span>
                                <span class="d-block text-muted small">
                                    <i class="bi bi-rulers"></i> 
                                    {{ $property->chiTiet->Area ?? 'N/A' }} m²
                                </span>
                            </div>
                            <div class="text-end">
                                <span class="badge rounded-pill 
                                    @if($property->TypePro == 'Sale') bg-danger
                                    @elseif($property->TypePro == 'Rent') bg-primary
                                    @elseif($property->TypePro == 'Sold') bg-success
                                    @elseif($property->TypePro == 'Rented') bg-info
                                    @else bg-secondary @endif">
                                    @if($property->TypePro == 'Rent')
                                        Đang cho thuê
                                    @elseif($property->TypePro == 'Sale')
                                        Đang bán
                                    @elseif($property->TypePro == 'Rented')
                                        Đã cho thuê
                                    @elseif($property->TypePro == 'Sold')
                                        Đã bán
                                    @else
                                        {{ $property->TypePro }}
                                    @endif
                                </span>
                                <span class="d-block text-muted small mt-1">ID: {{ $property->PropertyID }}</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-light text-dark me-1">
                                    <i class="bi bi-door-open"></i> {{ $property->chiTiet->Bedroom ?? '0' }} phòng
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-droplet"></i> {{ $property->chiTiet->Bath_WC ?? '0' }} WC
                                </span>
                            </div>
                            <a href="{{ route('owner.property.index') }}" class="btn btn-sm btn-outline-primary">Chi Tiết</a>
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
