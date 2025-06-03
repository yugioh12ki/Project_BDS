@extends('_layout._layagent.app')

@section('title', 'Danh Sách Phân Công Môi Giới')

@section('brokers')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-2">Phân công môi giới</h1>
            <p class="text-muted">Xem các phân công môi giới bất động sản</p>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-4">
            <!-- Danh sách phân công -->
            <div class="mb-4">
                @if($propertiesByDistrict->count() > 0)
                    @foreach($propertiesByDistrict as $district => $districtProperties)
                        <div class="card mb-3 district-card" data-district="{{ $district }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title mb-0">{{ $district }}</h5>
                                    <div class="d-flex gap-1">
                                        @php
                                            $saleCount = $districtProperties->where('TypePro', 'Sale')->count();
                                            $rentCount = $districtProperties->where('TypePro', 'Rent')->count();
                                        @endphp
                                        @if($saleCount > 0)
                                            <span class="badge bg-primary px-2 py-1">Bán</span>
                                        @endif
                                        @if($rentCount > 0)
                                            <span class="badge bg-success px-2 py-1">Thuê</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-geo-alt text-muted me-2"></i>
                                    <span>{{ $districtProperties->first()->Province }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar3 text-muted me-2"></i>
                                    <span>{{ \Carbon\Carbon::parse($districtProperties->max('PostedDate'))->format('d/m/Y') }}</span>
                                </div>
                                <div class="mt-2">
                                    <p class="mb-1 text-muted">Môi giới được phân công:</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-light text-dark">{{ $agent->Name }}</span>
                                    </div>
                                </div>
                                
                                <!-- Danh sách BĐS được phân công - tách riêng theo loại -->
                                <div class="mt-3">
                                    @php
                                        $saleProperties = $districtProperties->where('TypePro', 'Sale');
                                        $rentProperties = $districtProperties->where('TypePro', 'Rent');
                                    @endphp
                                    
                                    <!-- Danh sách BĐS Bán -->
                                    @if($saleProperties->count() > 0)
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-primary me-2">Bán</span>
                                                <small class="text-muted">{{ $saleProperties->count() }} bất động sản</small>
                                            </div>
                                            @foreach($saleProperties as $property)
                                                <div class="mb-2 p-2 border rounded-2 bg-light border-primary">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-bold small text-dark">{{ Str::limit($property->Title, 35) }}</div>
                                                            <div class="small text-muted">{{ $property->Address }}, {{ $property->Ward }}</div>
                                                        </div>
                                                        <span class="badge bg-primary ms-2">Bán</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="small text-success fw-bold">
                                                            {{ number_format($property->Price) }} VNĐ
                                                        </div>
                                                        <div class="small text-muted">
                                                            @if($property->danhMuc)
                                                                {{ $property->danhMuc->ten_pro }}
                                                            @else
                                                                Loại BĐS #{{ $property->PropertyType }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <!-- Danh sách BĐS Thuê -->
                                    @if($rentProperties->count() > 0)
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-success me-2">Thuê</span>
                                                <small class="text-muted">{{ $rentProperties->count() }} bất động sản</small>
                                            </div>
                                            @foreach($rentProperties as $property)
                                                <div class="mb-2 p-2 border rounded-2 bg-light border-success">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <div class="flex-grow-1">
                                                            <div class="fw-bold small text-dark">{{ Str::limit($property->Title, 35) }}</div>
                                                            <div class="small text-muted">{{ $property->Address }}, {{ $property->Ward }}</div>
                                                        </div>
                                                        <span class="badge bg-success ms-2">Thuê</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="small text-success fw-bold">
                                                            {{ number_format($property->Price) }} VNĐ
                                                            <span class="text-muted">/tháng</span>
                                                        </div>
                                                        <div class="small text-muted">
                                                            @if($property->danhMuc)
                                                                {{ $property->danhMuc->ten_pro }}
                                                            @else
                                                                Loại BĐS #{{ $property->PropertyType }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="text-muted">
                                <i class="bi bi-house-exclamation fs-1 d-block mb-3"></i>
                                <p>Hiện tại bạn chưa được phân công bất động sản nào.</p>
                                <small>Vui lòng liên hệ quản lý để được phân công.</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Chi tiết phân công -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Chi tiết bất động sản được phân công</h5>
                        <span class="badge bg-success px-2 py-1" id="totalBadge" style="display: none;">{{ $properties->count() }} BĐS</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($properties->count() > 0)
                        <!-- Hướng dẫn hiển thị ban đầu -->
                        <div id="initialInstructions" class="text-center py-4">
                            <i class="bi bi-arrow-left-circle fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted">Chọn một quận/huyện bên trái để xem chi tiết bất động sản.</p>
                        </div>
                        
                        <!-- Nội dung chi tiết được hiển thị theo quận -->
                        <div id="propertyDetailContent" class="d-none">
                            <!-- Header khu vực -->
                            <div id="districtHeader" class="mb-3">
                                <div class="d-flex align-items-center">
                                    <h5 class="mb-0 me-2"><span id="selectedDistrictName">Quận/Huyện</span></h5>
                                    <span id="selectedDistrictProvince" class="text-muted small"></span>
                                </div>
                                <div class="d-flex mt-2">
                                    <span id="propertyTypeCount" class="badge bg-info me-2"></span>
                                </div>
                            </div>
                            
                            <div class="row" id="propertyCards">
                                <!-- Các card bất động sản sẽ được thêm vào đây bởi JavaScript -->
                            </div>
                            
                            <!-- Template cho property card -->
                            <template id="propertyCardTemplate">
                                <div class="col-md-6 mb-4 property-card" data-district="">
                                    <div class="card h-100 border">
                                        <div class="card-body">
                                            <!-- Tiêu đề và loại BĐS -->
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0 property-title"></h6>
                                                <span class="badge property-type-badge"></span>
                                            </div>
                                            
                                            <!-- Địa chỉ -->
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-geo-alt text-muted me-2"></i>
                                                <small class="text-muted property-address"></small>
                                            </div>
                                            
                                            <!-- Giá tiền -->
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-currency-dollar text-muted me-2"></i>
                                                <span class="fw-bold text-success property-price"></span>
                                            </div>
                                            
                                            <!-- Loại bất động sản -->
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-house text-muted me-2"></i>
                                                <small class="text-muted property-category"></small>
                                            </div>
                                            
                                            <!-- Ngày được phân công -->
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="bi bi-calendar3 text-muted me-2"></i>
                                                <small class="text-muted property-date"></small>
                                            </div>
                                            
                                            <!-- Chủ sở hữu -->
                                            <div class="d-flex align-items-center mb-3 property-owner-container">
                                                <i class="bi bi-person text-muted me-2"></i>
                                                <small class="text-muted property-owner"></small>
                                            </div>
                                            
                                            <!-- Mô tả ngắn -->
                                            <p class="card-text small text-muted property-description"></p>
                                            
                                            <!-- Actions -->
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-outline-primary btn-sm flex-fill">
                                                    <i class="bi bi-eye me-1"></i>Xem chi tiết
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Thống kê tổng quan -->
                        <div class="mt-4 p-3 bg-light rounded" id="districtStatistics">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Thống kê khu vực</h6>
                                <span id="statisticsDistrict" class="badge bg-secondary" style="display: none;"></span>
                            </div>
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body py-2 px-3">
                                            <div class="h5 mb-0 text-primary" id="saleCount">0</div>
                                            <small class="text-muted">BĐS Bán</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body py-2 px-3">
                                            <div class="h5 mb-0 text-success" id="rentCount">0</div>
                                            <small class="text-muted">BĐS Thuê</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body py-2 px-3">
                                            <div class="h5 mb-0 text-info" id="totalCount">0</div>
                                            <small class="text-muted">Tổng BĐS</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body py-2 px-3">
                                            <div class="h5 mb-0 text-warning" id="avgPrice">0</div>
                                            <small class="text-muted">Giá TB (VNĐ)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-house-exclamation fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted">Chưa có bất động sản nào được phân công cho bạn.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Dữ liệu BĐS theo quận
    window.propertyData = {
        @foreach($propertiesByDistrict as $district => $districtProperties)
            "{{ $district }}": [
                @foreach($districtProperties as $property)
                    {
                        id: "{{ $property->PropertyID }}",
                        title: "{{ addslashes($property->Title) }}",
                        address: "{{ addslashes($property->Address) }}, {{ addslashes($property->Ward) }}, {{ addslashes($property->District) }}",
                        price: "{{ number_format($property->Price) }} VNĐ{{ $property->TypePro == 'Rent' ? '/tháng' : '' }}",
                        rawPrice: {{ $property->Price }},
                        type: "{{ $property->TypePro }}",
                        typeName: "{{ $property->TypePro == 'Sale' ? 'Bán' : 'Thuê' }}",
                        typeClass: "{{ $property->TypePro == 'Sale' ? 'bg-primary' : 'bg-success' }}",
                        category: "{{ $property->danhMuc ? addslashes($property->danhMuc->ten_pro) : 'Loại BĐS #'.$property->PropertyType }}",
                        date: "{{ \Carbon\Carbon::parse($property->PostedDate)->format('d/m/Y') }}",
                        province: "{{ addslashes($property->Province) }}",
                        @if($property->owner)
                            owner: "{{ addslashes($property->owner->Name) }}",
                        @else
                            owner: null,
                        @endif
                        @if($property->Description)
                            description: "{{ addslashes(Str::limit($property->Description, 80)) }}",
                        @else
                            description: "",
                        @endif
                    },
                @endforeach
            ],
        @endforeach
    };

    // Lấy các elements
    const districtCards = document.querySelectorAll('.district-card');
    const initialInstructions = document.getElementById('initialInstructions');
    const propertyDetailContent = document.getElementById('propertyDetailContent');
    const selectedDistrictName = document.getElementById('selectedDistrictName');
    const selectedDistrictProvince = document.getElementById('selectedDistrictProvince');
    const propertyTypeCount = document.getElementById('propertyTypeCount');
    const propertyCards = document.getElementById('propertyCards');
    const propertyCardTemplate = document.getElementById('propertyCardTemplate');

    // Thêm event listeners cho các district cards
    districtCards.forEach(card => {
        card.addEventListener('click', function() {
            const district = this.dataset.district;
            showDistrictProperties(district);
            
            // Thêm active class cho card được chọn
            districtCards.forEach(c => c.classList.remove('border-primary'));
            this.classList.add('border-primary');
        });
    });

    // Hiển thị properties của district được chọn
    function showDistrictProperties(district) {
        // Ẩn instructions, hiển thị content
        initialInstructions.classList.add('d-none');
        propertyDetailContent.classList.remove('d-none');
        
        // Giữ ẩn badge tổng số BĐS theo yêu cầu
        const totalBadge = document.getElementById('totalBadge');
        if (totalBadge) {
            totalBadge.style.display = 'none';
        }
        
        // Cập nhật header
        selectedDistrictName.textContent = district;
        
        // Lấy properties của quận đã chọn
        const properties = propertyData[district];
        if (!properties || properties.length === 0) return;
        
        // Cập nhật province
        selectedDistrictProvince.textContent = properties[0].province;
        
        // Đếm số lượng BĐS bán/thuê
        const saleCount = properties.filter(p => p.type === 'Sale').length;
        const rentCount = properties.filter(p => p.type === 'Rent').length;
        propertyTypeCount.textContent = `${saleCount} BĐS bán, ${rentCount} BĐS thuê`;
        
        // Xóa tất cả property cards hiện tại
        propertyCards.innerHTML = '';
        
        // Thêm các property cards mới
        properties.forEach(property => {
            const propertyCard = createPropertyCard(property);
            propertyCards.appendChild(propertyCard);
        });
    }

    // Tạo property card từ template
    function createPropertyCard(property) {
        const template = propertyCardTemplate.content.cloneNode(true);
        const card = template.querySelector('.property-card');
        
        // Cập nhật data-district
        card.dataset.district = property.district;
        
        // Cập nhật nội dung
        card.querySelector('.property-title').textContent = property.title;
        
        // Badge loại BĐS
        const typeBadge = card.querySelector('.property-type-badge');
        typeBadge.textContent = property.typeName;
        typeBadge.classList.add(property.typeClass);
        
        card.querySelector('.property-address').textContent = property.address;
        card.querySelector('.property-price').innerHTML = property.price;
        card.querySelector('.property-category').textContent = property.category;
        card.querySelector('.property-date').textContent = 'Ngày đăng: ' + property.date;
        
        // Chủ sở hữu (có thể null)
        if (property.owner) {
            card.querySelector('.property-owner').textContent = 'Chủ sở hữu: ' + property.owner;
        } else {
            card.querySelector('.property-owner-container').classList.add('d-none');
        }
        
        // Mô tả (có thể rỗng)
        if (property.description) {
            card.querySelector('.property-description').textContent = property.description;
        } else {
            card.querySelector('.property-description').classList.add('d-none');
        }
        
        return card;
    }
</script>
@endsection 