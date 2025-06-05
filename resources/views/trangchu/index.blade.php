@extends('_layout._layhome.home')

@section('home')
<!-- Hero Section with Modern Design -->
<section class="modern-hero">
    <div class="hero-slider" id="heroSlider">
        <div class="hero-slide active" style="background-image: url('{{ asset('storage/banner-1.jpg') }}')">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-slide" style="background-image: url('{{ asset('storage/banner-2.jpg') }}')">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-slide" style="background-image: url('{{ asset('storage/banner-3.png') }}')">
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
    </div>
</section>

<!-- Sale Properties Section -->
<section class="properties-section">
    <div class="container">
        <div class="section-header">
            <div class="section-title">
                <h2>
                    <i class="bi bi-house-heart"></i>
                    Bất động sản nổi bật
                </h2>
                <p>Khám phá những bất động sản được quan tâm nhiều nhất</p>
            </div>
        </div>

        <div class="modern-property-grid">
            @foreach($saleProperties->take(3) as $property)
                <div class="modern-property-card">
                    <div class="property-image-container">
                        @if($property->images->count() > 0)
                            @php
                                // Sắp xếp ảnh theo ImageID tăng dần và lấy ảnh đầu tiên
                                $sortedImages = $property->images->sortBy('ImageID');
                                $mainImage = $sortedImages->first();
                                $imageUrl = asset('storage/images/properties/' . $property->PropertyID . '/' . basename($mainImage->ImagePath));

                            @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $property->Title }}" loading="lazy">
                        @else
                            <img src="{{ asset('/storage/images/no-image.jpeg') }}" alt="{{ $property->Title }}" loading="lazy">
                        @endif
                        <div class="property-badge">Mới</div>
                        <div class="property-overlay">
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

<!-- About Section -->
<section class="about-section">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <div class="section-title">
                    <h2>
                        <i class="bi bi-building-fill"></i>
                        Về chúng tôi
                    </h2>
                    <p>Đối tác tin cậy trong lĩnh vực bất động sản</p>
                </div>

                <div class="about-description">
                    <p>
                        Với hơn 10 năm kinh nghiệm trong lĩnh vực bất động sản, chúng tôi tự hào là đơn vị
                        hàng đầu cung cấp dịch vụ tư vấn, mua bán và cho thuê bất động sản trên toàn quốc.
                    </p>

                    <div class="about-stats">
                        <div class="stat-item">
                            <div class="stat-number">5000+</div>
                            <div class="stat-label">Khách hàng tin tưởng</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">1000+</div>
                            <div class="stat-label">Bất động sản</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">Thành phố</div>
                        </div>
                    </div>

                    <div class="about-features">
                        <div class="feature-item">
                            <i class="bi bi-shield-check"></i>
                            <span>Pháp lý minh bạch</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-person-hearts"></i>
                            <span>Tư vấn tận tâm</span>
                        </div>
                        <div class="feature-item">
                            <i class="bi bi-award"></i>
                            <span>Uy tín hàng đầu</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="about-image">
                <div class="image-container">
                    <img src="{{ asset('storage/about-us.jpg') }}" alt="Về chúng tôi" class="main-image">
                    <div class="floating-card">
                        <div class="card-content">
                            <i class="bi bi-trophy-fill"></i>
                            <div class="card-text">
                                <h4>Top 1</h4>
                                <p>Công ty BĐS uy tín</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
