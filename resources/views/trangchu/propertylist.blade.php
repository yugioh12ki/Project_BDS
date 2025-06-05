@extends('_layout._layhome.home')

@section('home')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-title">
            <h1>
                <i class="bi bi-{{ $pageType == 'sale' ? 'house-door' : 'key' }}"></i>
                {{ $pageTitle }}
            </h1>
            <p>Tìm kiếm {{ $pageType == 'sale' ? 'mua bán' : 'cho thuê' }} bất động sản phù hợp với nhu cầu của bạn</p>
        </div>
    </div>
</section>

<!-- Search Filter Section -->
<section class="search-filter-section">
    <div class="container">
        <form action="{{ $pageType == 'sale' ? route('properties.sale') : route('properties.rent') }}" method="GET" class="property-search-form">
            <div class="search-box-container">
                <div class="filter-options">
                    <!-- Main Search Input -->
                    <div class="main-search-input">
                        <i class="bi bi-geo-alt"></i>
                        <input type="text" name="keyword" placeholder="Nhập địa điểm tìm kiếm..."
                               value="{{ request('keyword') }}" class="form-control">
                    </div>

                    <div class="filter-group">
                        <label><i class="bi bi-house"></i> Loại hình</label>
                        <select name="type" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($danhmucs as $dm)
                                <option value="{{ $dm->Protype_ID }}"
                                        {{ request('type') == $dm->Protype_ID ? 'selected' : '' }}>
                                    {{ $dm->ten_pro }}
                                </option>
                            @endforeach
                        </select>
                    </div>                    <div class="filter-group">
                        <label><i class="bi bi-bed"></i> Phòng ngủ</label>
                        <select name="bedrooms" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="1" {{ request('bedrooms') == 1 ? 'selected' : '' }}>1 phòng</option>
                            <option value="2" {{ request('bedrooms') == 2 ? 'selected' : '' }}>2 phòng</option>
                            <option value="3" {{ request('bedrooms') == 3 ? 'selected' : '' }}>3 phòng</option>
                            <option value="4" {{ request('bedrooms') == 4 ? 'selected' : '' }}>4+ phòng</option>
                        </select>
                    </div>                    <div class="filter-group price-range-filter">                        <label><i class="bi bi-currency-dollar"></i> Giá (triệu VNĐ)</label>                        <div class="range-slider-container">                            <input type="range" class="range-slider" id="priceMin" name="price_min" min="0" max="100000000" step="1000000" value="{{ request('price_min', 0) }}">
                            <input type="range" class="range-slider" id="priceMax" name="price_max" min="0" max="100000000" step="1000000" value="{{ request('price_max', 100000000) }}">
                        </div>
                        <div class="price-inputs">
                            <div class="price-value">
                                <span>Từ: </span>                                <input type="text" id="priceMinValue" name="price_min_input" value="{{ number_format(request('price_min', 0), 0, '', '.') }}" min="0" max="100000000" class="formatted-price">
                            </div>
                            <div class="price-value">
                                <span>Đến: </span>
                                <input type="text" id="priceMaxValue" name="price_max_input" value="{{ number_format(request('price_max', 100000000), 0, '', '.') }}" min="0" max="100000000"class="formatted-price">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-search">
                        <i class="bi bi-search"></i>
                        Tìm kiếm
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Results Section -->
<section class="results-section">
    <div class="container">
        <!-- Results Header -->
        <div class="results-header">
            <div class="results-info">
                <h3>Kết quả tìm kiếm</h3>
                <p>Tìm thấy <strong>{{ $properties->total() }}</strong> bất động sản {{ $pageType == 'sale' ? 'mua bán' : 'cho thuê' }}</p>
            </div>

            <!-- Sort Options -->
            <div class="sort-options">
                <label>Sắp xếp theo:</label>
                <select class="form-select" onchange="sortProperties(this.value)">
                    <option value="newest">Mới nhất</option>
                    <option value="price_low">Giá thấp đến cao</option>
                    <option value="price_high">Giá cao đến thấp</option>
                    <option value="area_small">Diện tích nhỏ đến lớn</option>
                    <option value="area_large">Diện tích lớn đến nhỏ</option>
                </select>
            </div>
        </div>

        <!-- Properties Grid -->
        <div class="properties-grid">
            @forelse($properties as $property)
                <div class="property-card">
                    <div class="property-image">
                        @if($property->images->count() > 0)
                            @php
                                // Sắp xếp ảnh theo ImageID tăng dần và lấy ảnh đầu tiên
                                $sortedImages = $property->images->sortBy('ImageID');
                                $mainImage = $sortedImages->first();
                                // $imageUrl = 'public/storage/' . $mainImage->ImagePath;
                                $imageUrl = asset('/storage') . '/' . $mainImage->ImagePath;
                            @endphp

                            <img src="{{ $imageUrl }}" alt="{{ $property->Title }}" loading="lazy">
                        @else
                            <img src="{{ asset('/storage/images/no-image.jpeg') }}" alt="{{ $property->Title }}" loading="lazy">
                        @endif

                        <div class="property-badges">
                            <span class="badge badge-{{ $pageType }}">
                                {{ $pageType == 'sale' ? 'Bán' : 'Cho thuê' }}
                            </span>
                            @if($property->danhMuc)
                                <span class="badge badge-type">{{ $property->danhMuc->ten_pro }}</span>
                            @endif
                        </div>                        <div class="property-overlay">
                            @if($property->TypePro == 'Cho bán')
                                <a href="{{ route('properties.sale.detail', $property->PropertyID) }}" class="btn-view-detail">
                                    <i class="bi bi-eye"></i>
                                    Xem chi tiết
                                </a>
                            @else
                                <a href="{{ route('properties.rent.detail', $property->PropertyID) }}" class="btn-view-detail">
                                    <i class="bi bi-eye"></i>
                                    Xem chi tiết
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="property-content">
                        <div class="property-price">
                            <span class="price-value">{{ number_format($property->Price, 0, ',', '.') }}</span>
                            <span class="price-unit">VND</span>
                        </div>                        <h4 class="property-title">
                            @if($property->TypePro == 'Cho bán')
                                <a href="{{ route('properties.sale.detail', $property->PropertyID) }}">
                                    {{ $property->Title }}
                                </a>
                            @else
                                <a href="{{ route('properties.rent.detail', $property->PropertyID) }}">
                                    {{ $property->Title }}
                                </a>
                            @endif
                        </h4>

                        <div class="property-location">
                            <i class="bi bi-geo-alt"></i>
                            <span>{{ $property->District }}, {{ $property->Province }}</span>
                        </div>                        @if($property->chiTiet)
                        <div class="property-details">
                            <div class="detail-item">
                                <i class="bi bi-bed"></i>
                                <span>{{ $property->chiTiet->Bedroom ?? 0 }} phòng ngủ</span>
                            </div>
                            <div class="detail-item">
                                <i class="bi bi-droplet"></i>
                                <span>{{ $property->chiTiet->Bath_WC ?? 0 }} WC</span>
                            </div>
                            <div class="detail-item">
                                <i class="bi bi-arrows-angle-expand"></i>
                                <span>{{ $property->chiTiet->Area ?? 0 }} m²</span>
                            </div>
                            @if($property->chiTiet->view)
                            <div class="detail-item">
                                <i class="bi bi-compass"></i>
                                <span>{{ $property->chiTiet->view }}</span>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="property-footer">                            <div class="property-info">
                                <div class="posted-date">
                                    <i class="bi bi-calendar"></i>
                                    <span>{{ $property->PostedDate ? \Carbon\Carbon::parse($property->PostedDate)->format('d/m/Y') : 'N/A' }}</span>
                                </div>                                @if($property->TypePro == 'Cho bán')
                                    <a href="{{ route('properties.sale.detail', $property->PropertyID) }}" class="btn-detail">
                                        Chi tiết <i class="bi bi-arrow-right"></i>
                                    </a>
                                @else
                                    <a href="{{ route('properties.rent.detail', $property->PropertyID) }}" class="btn-detail">
                                        Chi tiết <i class="bi bi-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                            <div class="property-source">
                                <i class="bi bi-info-circle"></i>
                                <span>Tin gửi của BĐS</span>
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="bi bi-house-x"></i>
                    </div>
                    <h3>Không tìm thấy bất động sản phù hợp</h3>
                    <p>Vui lòng thử lại với các tiêu chí tìm kiếm khác</p>
                    <a href="{{ $pageType == 'sale' ? route('properties.sale') : route('properties.rent') }}" class="btn btn-primary">
                        Xem tất cả
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($properties->hasPages())
        <div class="pagination-wrapper">
            {{ $properties->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</section>

<script>
function sortProperties(sortBy) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortBy);
    window.location.href = url.toString();
}

// Xử lý thanh trượt giá
document.addEventListener('DOMContentLoaded', function() {
    const priceMin = document.getElementById('priceMin');
    const priceMax = document.getElementById('priceMax');
    const priceMinValue = document.getElementById('priceMinValue');
    const priceMaxValue = document.getElementById('priceMaxValue');

    // Hàm định dạng số
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Hàm loại bỏ định dạng số
    function unformatNumber(str) {
        return parseInt(str.replace(/\./g, ''));
    }

    // Đồng bộ giá trị khi di chuyển thanh trượt
    priceMin.addEventListener('input', function() {
        priceMinValue.value = formatNumber(this.value);
        // Đảm bảo giá min không vượt quá giá max
        if (parseInt(priceMin.value) > parseInt(priceMax.value)) {
            priceMax.value = priceMin.value;
            priceMaxValue.value = formatNumber(priceMin.value);
        }
    });

    priceMax.addEventListener('input', function() {
        priceMaxValue.value = formatNumber(this.value);
        // Đảm bảo giá max không nhỏ hơn giá min
        if (parseInt(priceMax.value) < parseInt(priceMin.value)) {
            priceMin.value = priceMax.value;
            priceMinValue.value = formatNumber(priceMax.value);
        }
    });

    // Đồng bộ giá trị khi nhập trực tiếp
    priceMinValue.addEventListener('change', function() {
        const value = unformatNumber(this.value);
        priceMin.value = value;
        this.value = formatNumber(value);
        // Đảm bảo giá min không vượt quá giá max
        if (value > parseInt(priceMax.value)) {
            priceMax.value = value;
            priceMaxValue.value = formatNumber(value);
        }
    });

    priceMaxValue.addEventListener('change', function() {
        const value = unformatNumber(this.value);
        priceMax.value = value;
        this.value = formatNumber(value);
        // Đảm bảo giá max không nhỏ hơn giá min
        if (value < parseInt(priceMin.value)) {
            priceMin.value = value;
            priceMinValue.value = formatNumber(value);
        }
    });
});
</script>

@endsection
