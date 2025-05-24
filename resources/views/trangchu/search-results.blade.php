@extends('_layout._layhome.home')

@section('home')
<div class="banner-section">
    <div class="banner-slider" id="bannerSlider">
        <div class="slide active" style="background-image: url('/storage/banner1.jpg')"></div>
        <div class="slide" style="background-image: url('/storage/banner2.jpg')"></div>
        <div class="slide" style="background-image: url('/storage/banner3.jpg')"></div>
        
        <button class="banner-nav prev">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button class="banner-nav next">
            <i class="bi bi-chevron-right"></i>
        </button>

        <div class="slider-dots">
            <button class="dot active" data-slide="0"></button>
            <button class="dot" data-slide="1"></button>
            <button class="dot" data-slide="2"></button>
        </div>
    </div>

    <form action="{{ route('customer.search') }}" method="GET" class="search-box">
        <div class="search-input">
            <input type="text" name="keyword" placeholder="Nhập tìm kiếm tỉnh thành" value="{{ request('keyword') }}">
        </div>
        <div class="search-filters">
            <select name="area">
                <option value="">Diện tích</option>
                <option value="1" {{ request('area') == 1 ? 'selected' : '' }}>Dưới 30m²</option>
                <option value="2" {{ request('area') == 2 ? 'selected' : '' }}>30-50m²</option>
                <option value="3" {{ request('area') == 3 ? 'selected' : '' }}>50-80m²</option>
                <option value="4" {{ request('area') == 4 ? 'selected' : '' }}>Trên 80m²</option>
            </select>
            <select name="type">
                <option value="">Loại hình</option>
                @foreach($danhmucs as $dm)
                    <option value="{{ $dm->Protype_ID }}" {{ request('type') == $dm->Protype_ID ? 'selected' : '' }}>{{ $dm->ten_pro }}</option>
                @endforeach
            </select>
            <select name="price">
                <option value="">Giá tiền</option>
                <option value="1" {{ request('price') == 1 ? 'selected' : '' }}>Dưới 1 tỷ</option>
                <option value="2" {{ request('price') == 2 ? 'selected' : '' }}>1-3 tỷ</option>
                <option value="3" {{ request('price') == 3 ? 'selected' : '' }}>3-5 tỷ</option>
                <option value="4" {{ request('price') == 4 ? 'selected' : '' }}>Trên 5 tỷ</option>
            </select>
            <button type="submit" class="btn-search">Tìm kiếm</button>
        </div>
    </form>
</div>

<div class="container mt-4">
    <div class="search-results-header">
        <h2>Kết quả tìm kiếm ({{ $properties->total() }})</h2>
    </div>

    @if($properties->count() > 0)
        <div class="row">
            @foreach($properties as $property)
                <div class="col-md-4 mb-4">
                    <div class="property-card">
                        <div class="property-image">
                            @if(file_exists(public_path('storage/image_properties/' . $property->PropertyID . '.jpg')))
                                <img src="{{ asset('storage/image_properties/' . $property->PropertyID . '.jpg') }}" alt="{{ $property->Title }}">
                            @elseif(file_exists(public_path('storage/properties/' . $property->PropertyID . '.jpg')))
                                <img src="{{ asset('storage/properties/' . $property->PropertyID . '.jpg') }}" alt="{{ $property->Title }}">
                            @else
                                <img src="{{ asset('storage/properties/no-image.jpg') }}" alt="{{ $property->Title }}">
                            @endif
                            <div class="property-badge">Bán</div>
                        </div>
                        <div class="property-info">
                            <h3 class="property-title text-center">{{ $property->Title }}</h3>
                            
                            <div class="property-address">
                                <i class="bi bi-geo-alt-fill"></i>
                                {{ $property->Address }}, {{ $property->Ward }}, {{ $property->District }}, {{ $property->Province }}
                            </div>
                            
                            <div class="property-price text-center">
                                {{ number_format($property->Price, 0, ',', '.') }} VND
                            </div>
                            
                            <div class="property-metadata">
                                <div class="property-type">
                                    {{ $property->danhMuc->ten_pro ?? 'N/A' }}
                            </div>
                            
                            <div class="property-date">
                                <i class="bi bi-calendar"></i>
                                {{ \Carbon\Carbon::parse($property->created_at ?? $property->PostedDate)->format('d/m/Y') }}
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('property.detail', $property->PropertyID) }}" class="property-detail-btn">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Phân trang -->
        <div class="pagination-container">
            {{ $properties->appends(request()->query())->links() }}
        </div>
    @else
        <div class="no-results text-center">
            <i class="bi bi-search" style="font-size: 3rem;"></i>
            <p class="mt-3">Không tìm thấy bất động sản nào phù hợp với yêu cầu của bạn.</p>
            <p>Vui lòng thử lại với các tiêu chí khác.</p>
        </div>
    @endif
</div>
@endsection