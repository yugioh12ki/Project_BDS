@extends('_layout._layhome.home')

@section('home')
<!-- Hero Section with Modern Design -->
<section class="modern-hero">
    <div class="hero-slider" id="heroSlider">
        <div class="hero-slide active" style="background-image: url('/storage/banner1.jpg')">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-slide" style="background-image: url('/storage/banner2.jpg')">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-slide" style="background-image: url('/storage/banner3.jpg')">
            <div class="hero-overlay"></div>
        </div>

        <!-- Navigation -->
        <button class="hero-nav prev">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button class="hero-nav next">
            <i class="bi bi-chevron-right"></i>
        </button>

        <!-- Dots -->
        <div class="hero-dots">
            <button class="dot active" data-slide="0"></button>
            <button class="dot" data-slide="1"></button>
            <button class="dot" data-slide="2"></button>
        </div>
    </div>

    <!-- Hero Content -->
    <div class="hero-content">
        <div class="hero-text">
            <h1 class="hero-title">
                <span class="title-main">Tìm Ngôi Nhà</span>
                <span class="title-highlight">Hoàn Hảo</span>
                <span class="title-sub">Cho Gia Đình Bạn</span>
            </h1>
            <p class="hero-subtitle">
                <i class="bi bi-geo-alt-fill"></i>
                Khám phá hàng ngàn bất động sản cao cấp trên toàn quốc
            </p>
        </div>

        <!-- Modern Search Box -->
        <form action="{{ route('customer.search') }}" method="GET" class="modern-search-box">
            <div class="search-header">
                <i class="bi bi-search"></i>
                <h3>Tìm kiếm bất động sản</h3>
            </div>

            <div class="search-main">
                <div class="search-input-group">
                    <i class="bi bi-geo-alt"></i>
                    <input type="text" name="keyword" placeholder="Nhập địa điểm tìm kiếm..." value="{{ request('keyword') }}">
                </div>
            </div>

            <div class="search-filters-grid">
                <div class="filter-group">
                    <label><i class="bi bi-rulers"></i> Diện tích</label>
                    <select name="area">
                        <option value="">Chọn diện tích</option>
                        <option value="1" {{ request('area') == 1 ? 'selected' : '' }}>Dưới 30m²</option>
                        <option value="2" {{ request('area') == 2 ? 'selected' : '' }}>30-50m²</option>
                        <option value="3" {{ request('area') == 3 ? 'selected' : '' }}>50-80m²</option>
                        <option value="4" {{ request('area') == 4 ? 'selected' : '' }}>Trên 80m²</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label><i class="bi bi-house"></i> Loại hình</label>
                    <select name="type">
                        <option value="">Chọn loại hình</option>
                        @if(isset($danhmucs))
                            @foreach($danhmucs as $dm)
                                <option value="{{ $dm->Protype_ID }}" {{ request('type') == $dm->Protype_ID ? 'selected' : '' }}>{{ $dm->ten_pro }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="filter-group">
                    <label><i class="bi bi-currency-dollar"></i> Mức giá</label>
                    <select name="price">
                        <option value="">Chọn mức giá</option>
                        <option value="1" {{ request('price') == 1 ? 'selected' : '' }}>Dưới 1 tỷ</option>
                        <option value="2" {{ request('price') == 2 ? 'selected' : '' }}>1-3 tỷ</option>
                        <option value="3" {{ request('price') == 3 ? 'selected' : '' }}>3-5 tỷ</option>
                        <option value="4" {{ request('price') == 4 ? 'selected' : '' }}>Trên 5 tỷ</option>
                    </select>
                </div>

                <button type="submit" class="btn-search-modern">
                    <i class="bi bi-search"></i>
                    <span>Tìm kiếm ngay</span>
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Sale Properties Section -->
<section class="properties-section">
    <div class="container">
        <div class="section-header">
            <div class="section-title">
                <h2>
                    <i class="bi bi-house-heart"></i>
                    Bất động sản Bán nổi bật
                </h2>
                <p>Khám phá những bất động sản bán được quan tâm nhiều nhất</p>
            </div>
        </div>

        <div class="modern-property-grid">
            @foreach($saleProperties as $property)
                <div class="modern-property-card">
                    <div class="property-image-container">
                        @if(file_exists(public_path('storage/image_properties/' . $property->PropertyID . '.jpg')))
                            <img src="{{ asset('storage/image_properties/' . $property->PropertyID . '.jpg') }}" alt="{{ $property->Title }}">
                        @else
                            <img src="{{ asset('storage/properties/no-image.jpg') }}" alt="{{ $property->Title }}">
                        @endif
                        <div class="property-badge">Mới</div>
                        <div class="property-overlay">
                            <a href="{{ route('property.detail', $property->PropertyID) }}" class="btn-view-detail">
                                <i class="bi bi-eye"></i>
                                Xem chi tiết
                            </a>
                        </div>
                    </div>

                    <div class="property-content">
                        <div class="property-price">
                            <span class="price-value">{{ number_format($property->Price, 0, ',', '.') }}</span>
                            <span class="price-unit">VND</span>
                        </div>
                        <h3 class="property-title">{{ $property->Title }}</h3>
                        <div class="property-location">
                            <i class="bi bi-geo-alt"></i>
                            <span>{{ $property->District }}, {{ $property->Province }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Rental Properties Section -->
<section class="featured-section">
    <div class="container">
        <div class="section-header">
            <div class="section-title">
                <h2>
                    <i class="bi bi-star-fill"></i>
                    Bất động sản Thuê nổi bật
                </h2>
                <p>Những lựa chọn cho thuê được đánh giá cao và được quan tâm nhiều nhất</p>
            </div>
        </div>

        <div class="featured-property-grid">
            @foreach($rentProperties as $property)
                <div class="featured-property-card">
                    <div class="property-image-container">
                        @if(file_exists(public_path('storage/image_properties/' . $property->PropertyID . '.jpg')))
                            <img src="{{ asset('storage/image_properties/' . $property->PropertyID . '.jpg') }}" alt="{{ $property->Title }}">
                        @else
                            <img src="{{ asset('storage/properties/no-image.jpg') }}" alt="{{ $property->Title }}">
                        @endif
                        <div class="featured-badge">
                            <i class="bi bi-star-fill"></i>
                            Nổi bật
                        </div>
                        <div class="property-overlay">
                            <a href="{{ route('property.detail', $property->PropertyID) }}" class="btn-view-featured">
                                <i class="bi bi-arrow-right"></i>
                                Khám phá ngay
                            </a>
                        </div>
                    </div>

                    <div class="property-content">
                        <div class="property-price-featured">
                            {{ number_format($property->Price, 0, ',', '.') }} VND
                        </div>
                        <h3 class="property-title">{{ $property->Title }}</h3>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Utilities Section -->
<section class="utilities-section">
    <div class="container">
        <div class="section-header">
            <div class="section-title">
                <h2>
                    <i class="bi bi-tools"></i>
                    Hỗ trợ tiện ích
                </h2>
                <p>Các công cụ hữu ích giúp bạn trong quá trình mua bán bất động sản</p>
            </div>
        </div>

        <div class="utilities-grid">
            <div class="utility-card-modern">
                <div class="utility-icon">
                    <i class="bi bi-calculator"></i>
                </div>
                <div class="utility-content">
                    <h4>Tính toán khoản vay</h4>
                    <p>Công cụ tính khoản vay và lãi suất cho bất động sản một cách chính xác</p>
                    <button class="utility-btn">
                        <i class="bi bi-arrow-right"></i>
                        Sử dụng ngay
                    </button>
                </div>
            </div>

            <div class="utility-card-modern">
                <div class="utility-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="utility-content">
                    <h4>Tư vấn pháp lý</h4>
                    <p>Hỗ trợ tư vấn các vấn đề pháp lý liên quan đến bất động sản</p>
                    <button class="utility-btn">
                        <i class="bi bi-arrow-right"></i>
                        Tư vấn ngay
                    </button>
                </div>
            </div>

            <div class="utility-card-modern">
                <div class="utility-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="utility-content">
                    <h4>Định giá BĐS</h4>
                    <p>Công cụ định giá bất động sản theo thị trường hiện tại</p>
                    <button class="utility-btn">
                        <i class="bi bi-arrow-right"></i>
                        Định giá ngay
                    </button>
                </div>
            </div>

            <div class="utility-card-modern">
                <div class="utility-icon">
                    <i class="bi bi-map"></i>
                </div>
                <div class="utility-content">
                    <h4>Bản đồ khu vực</h4>
                    <p>Xem bản đồ và thông tin quy hoạch khu vực chi tiết</p>
                    <button class="utility-btn">
                        <i class="bi bi-arrow-right"></i>
                        Xem bản đồ
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Companies Section -->
<section class="companies-section">
    <div class="container">
        <div class="section-header">
            <div class="section-title">
                <h2>
                    <i class="bi bi-building"></i>
                    Đối tác uy tín
                </h2>
                <p>Những doanh nghiệp hàng đầu trong lĩnh vực bất động sản</p>
            </div>
        </div>

        <div class="companies-grid">
            <div class="company-card-modern">
                <div class="company-logo">
                    <i class="bi bi-buildings"></i>
                </div>
                <h4>Vinhomes</h4>
                <p>Tập đoàn BĐS hàng đầu Việt Nam</p>
            </div>

            <div class="company-card-modern">
                <div class="company-logo">
                    <i class="bi bi-house-heart"></i>
                </div>
                <h4>Novaland</h4>
                <p>Chuyên phát triển dự án cao cấp</p>
            </div>

            <div class="company-card-modern">
                <div class="company-logo">
                    <i class="bi bi-brightness-high"></i>
                </div>
                <h4>Sun Group</h4>
                <p>Tập đoàn đa ngành uy tín</p>
            </div>

            <div class="company-card-modern">
                <div class="company-logo">
                    <i class="bi bi-gem"></i>
                </div>
                <h4>Masterise Homes</h4>
                <p>Phát triển BĐS cao cấp</p>
            </div>
        </div>
    </div>
</section>

<!-- Real Estate News Section -->
<section class="news-section">
    <div class="container">
        <div class="section-header">
            <div class="section-title">
                <h2>
                    <i class="bi bi-newspaper"></i>
                    Tin tức bất động sản
                </h2>
                <p>Cập nhật thông tin mới nhất về thị trường bất động sản</p>
            </div>
            <a href="#" class="view-all-btn">
                Xem tất cả
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="news-grid">
            <div class="news-card-modern">
                <div class="news-date">
                    <span class="day">12</span>
                    <span class="month">THG 3</span>
                </div>
                <div class="news-content">
                    <h4>Thị trường BĐS phía Nam khởi sắc</h4>
                    <p>Thị trường bất động sản khu vực phía Nam đang có những tín hiệu tích cực với nhiều dự án mới...</p>
                    <div class="news-meta">
                        <span class="news-category">
                            <i class="bi bi-tag"></i>
                            Thị trường
                        </span>
                        <span class="read-time">
                            <i class="bi bi-clock"></i>
                            5 phút đọc
                        </span>
                    </div>
                </div>
            </div>

            <div class="news-card-modern">
                <div class="news-date">
                    <span class="day">10</span>
                    <span class="month">THG 3</span>
                </div>
                <div class="news-content">
                    <h4>Xu hướng thiết kế căn hộ 2024</h4>
                    <p>Những xu hướng thiết kế nội thất căn hộ hiện đại được ưa chuộng nhất trong năm 2024...</p>
                    <div class="news-meta">
                        <span class="news-category">
                            <i class="bi bi-tag"></i>
                            Thiết kế
                        </span>
                        <span class="read-time">
                            <i class="bi bi-clock"></i>
                            3 phút đọc
                        </span>
                    </div>
                </div>
            </div>

            <div class="news-card-modern">
                <div class="news-date">
                    <span class="day">08</span>
                    <span class="month">THG 3</span>
                </div>
                <div class="news-content">
                    <h4>Cơ hội đầu tư BĐS 2024</h4>
                    <p>Phân tích những cơ hội đầu tư bất động sản tiềm năng và xu hướng thị trường năm 2024...</p>
                    <div class="news-meta">
                        <span class="news-category">
                            <i class="bi bi-tag"></i>
                            Đầu tư
                        </span>
                        <span class="read-time">
                            <i class="bi bi-clock"></i>
                            7 phút đọc
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
