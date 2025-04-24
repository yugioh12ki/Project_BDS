@extends('_layout._layhome.home')

@section('home')
<header class="hero">
    <div class="search-box">
        <div class="search-top">
            <input type="text" placeholder="Nhập tìm kiếm tỉnh thành">
          </div>
          <div class="search-filters">
            <select>
              <option>Diện tích</option>
              <option>25m²</option>
              <option>50m²</option>
              <option>75m²</option>
              <option>100m²</option>
            </select>
            <select id="property-type" onchange="updatePriceOptions()">
              <option value="">Loại hình</option>
              <option value="ban">Cho Bán</option>
              <option value="thue">Cho Thuê</option>
            </select>
            <select id="price-options">
              <option>Giá tiền</option>
            </select>
            <button class="btn-search">Tìm kiếm</button>
          </div>
        </div>
    </div>
  </header>
  <script>
    function updatePriceOptions() {
    const type = document.getElementById('property-type').value;
    const priceSelect = document.getElementById('price-options');
    priceSelect.innerHTML = '<option>Giá tiền</option>'; // Reset

    let options = [];
    if (type === 'ban') {
      options = ['Dưới 1 tỷ', 'Từ 1 - 3 tỷ', 'Từ 3 - 5 tỷ', 'Trên 5 tỷ'];
    } else if (type === 'thue') {
      options = ['Dưới 2 triệu', 'Từ 2 - 3 triệu', 'Từ 3 - 5 triệu', 'Trên 5 triệu'];
    }

    options.forEach(opt => {
      const option = document.createElement('option');
      option.textContent = opt;
      priceSelect.appendChild(option);
    });
  }
  </script>


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
