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
        @foreach($recentProperties as $property)
            <div class="property-card">
                <div class="property-image">
                    @if(file_exists(public_path('storage/image_properties/' . $property->PropertyID . '.jpg')))
                        <img src="{{ asset('storage/image_properties/' . $property->PropertyID . '.jpg') }}" alt="{{ $property->Title }}">
                    @else
                        <img src="{{ asset('storage/properties/no-image.jpg') }}" alt="{{ $property->Title }}">
                    @endif
                </div>
                <h3>{{ $property->Title }}</h3>
                <p>{{ $property->District }}, {{ $property->Province }}</p>
                <span>{{ number_format($property->Price, 0, ',', '.') }} VND</span>
                <a href="{{ route('property.detail', $property->PropertyID) }}" class="btn-view">Xem chi tiết</a>
            </div>
        @endforeach
    </div>
</section>

<section class="featured-properties">
    <h2>Bất động sản nổi bật</h2>
    <div class="property-list">
        @foreach($featuredProperties as $property)
            <div class="property-card">
                <div class="property-image">
                    @if(file_exists(public_path('storage/image_properties/' . $property->PropertyID . '.jpg')))
                        <img src="{{ asset('storage/image_properties/' . $property->PropertyID . '.jpg') }}" alt="{{ $property->Title }}">
                    @else
                        <img src="{{ asset('storage/properties/no-image.jpg') }}" alt="{{ $property->Title }}">
                    @endif
                </div>
                <h3>{{ $property->Title }}</h3>
                <p>{{ $property->chiTiet->first()->Bedroom ?? 0 }} phòng ngủ · {{ $property->chiTiet->first()->Area ?? 0 }}m²</p>
                <span>{{ number_format($property->Price, 0, ',', '.') }} VND</span>
                <a href="{{ route('property.detail', $property->PropertyID) }}" class="btn-view">Xem chi tiết</a>
            </div>
        @endforeach
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
