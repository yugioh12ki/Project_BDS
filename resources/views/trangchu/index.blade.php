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
                @if(isset($danhmucs))
                    @foreach($danhmucs as $dm)
                        <option value="{{ $dm->Protype_ID }}" {{ request('type') == $dm->Protype_ID ? 'selected' : '' }}>{{ $dm->ten_pro }}</option>
                    @endforeach
                @endif
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

  <section class="properties">
    <h2>Bất động sản dành cho bạn</h2>
    <div class="property-list">
      <div class="property-card">
        <h3>Căn hộ cao cấp Sunrise</h3>
        <p>Quận 7, TP.HCM</p>
        <span>2.5 tỷ</span>
      </div>
      <div class="property-card">
        <h3>Biệt thự vườn The Manor</h3>
        <p>Quận 2, TP.HCM</p>
        <span>5 tỷ</span>
      </div>
      <div class="property-card">
        <h3>Nhà phố Palm Residence</h3>
        <p>Quận 9, TP.HCM</p>
        <span>8.5 tỷ</span>
      </div>
      <div class="property-card">
        <h3>Penthouse Vinhomes Central</h3>
        <p>Quận 1, TP.HCM</p>
        <span>20 tỷ</span>
      </div>
    </div>
  </section>

  <section class="featured-properties">
    <h2>Bất động sản nổi bật</h2>
    <div class="property-list">
      <div class="property-card">
        <h3>Empire City Luxury</h3>
        <p>3 phòng ngủ · 150m²</p>
        <span>12.5 tỷ</span>
      </div>
      <div class="property-card">
        <h3>Palm Paradise Villa</h3>
        <p>4 phòng ngủ · 300m²</p>
        <span>25 tỷ</span>
      </div>
      <div class="property-card">
        <h3>Sky Garden Penthouse</h3>
        <p>5 phòng ngủ · 400m²</p>
        <span>35 tỷ</span>
      </div>
    </div>
  </section>

  <section class="utilities">
    <h2>Hỗ trợ tiện ích</h2>
    <div class="utility-list">
      <div class="utility-card">
        <h4>Tính toán khoản vay</h4>
        <p>Công cụ tính khoản vay và lãi suất cho bất động sản</p>
      </div>
      <div class="utility-card">
        <h4>Tư vấn pháp lý</h4>
        <p>Hỗ trợ tư vấn các vấn đề pháp lý liên quan đến bất động sản</p>
      </div>
      <div class="utility-card">
        <h4>Định giá BĐS</h4>
        <p>Công cụ định giá bất động sản theo thị trường</p>
      </div>
      <div class="utility-card">
        <h4>Bản đồ khu vực</h4>
        <p>Xem bản đồ và thông tin quy hoạch khu vực</p>
      </div>
    </div>
  </section>

  <section class="featured-companies">
    <h2>Doanh nghiệp tiêu biểu</h2>
    <div class="company-list">
      <div class="company-card">Vinhomes</div>
      <div class="company-card">Novaland</div>
      <div class="company-card">Sun Group</div>
      <div class="company-card">Masterise Homes</div>
    </div>
  </section>

  <section class="real-estate-news">
    <h2>Tin tức bất động sản</h2>
    <div class="news-list">
      <div class="news-card">
        <h4>Thị trường BĐS phía Nam khởi sắc</h4>
        <p><small>12/03/2024</small></p>
      </div>
      <div class="news-card">
        <h4>Xu hướng thiết kế căn hộ 2024</h4>
        <p><small>10/03/2024</small></p>
      </div>
      <div class="news-card">
        <h4>Cơ hội đầu tư BĐS 2024</h4>
        <p><small>08/03/2024</small></p>
      </div>
    </div>
  </section>

@endsection
