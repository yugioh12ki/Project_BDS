<div class="tab-pane fade show active" id="info{{ $modalID ?? '' }}" role="tabpanel">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Chủ sở hữu</label>
          <select class="form-select" name="owner">
            <option value="">Chọn</option>
            @foreach ($owners as $user)
            <option value="{{ $user->UserID }}">{{ $user->Name }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Địa Chỉ</label>
          <input type="text" class="form-control" name="address">
        </div>
        <div class="mb-3">
          <label class="form-label">Phường</label>
          <select class="form-select"  name="address">
            <option value="">Chọn</option>
        </div>
        <div class="mb-3">
          <label class="form-label">Quận/Huyện</label>
          <select class="form-select"  name="address">
            <option value="">Chọn</option>
        </div>
        <div class="mb-3">
          <label class="form-label">Tỉnh/Thành Phố</label>
          <select class="form-select"  name="address">
            <option value="">Chọn</option>
        </div>
        <div class="mb-3">
          <label class="form-label">Loại Bất Động Sản</label>
          <select class="form-select" name="property_type">
            <option value="">Chọn</option>
            @foreach ($categories as $cat)
            <option value="{{ $cat->Protype_ID }}">{{ $cat->ten_pro }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Giá Tiền</label>
          <input type="number" class="form-control" name="price">
        </div>
      </div>
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Tiêu Đề</label>
          <input type="text" class="form-control" name="title">
        </div>
        <div class="mb-3">
          <label class="form-label">Mô Tả</label>
          <textarea class="form-control" name="description" rows="7"></textarea>
        </div>
        <div class="mb-3 text-end">
          <label class="form-label me-2">Loại Thuê/Bán</label>
          <select class="form-select d-inline-block w-auto" name="sale_type" id="sale_type">
            <option value="">Chọn</option>
            <option value="rent">Thuê</option>
            <option value="sale">Bán</option>
            <option value="Sale/Rent">Sale/Rent</option>
          </select>
        </div>
      </div>
    </div>
</div>
<!-- Chi tiết bất động sản -->
<div class="tab-pane fade" id="detail {{ $modalID ?? '' }}" role="tabpanel">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Số tầng (Floor)</label>
          <input type="number" class="form-control" name="floor">
        </div>
        <div class="mb-3">
          <label class="form-label">Diện tích (Area)</label>
          <input type="number" class="form-control" name="area">
        </div>
        <div class="mb-3">
          <label class="form-label">Phòng ngủ (Bedroom)</label>
          <input type="number" class="form-control" name="bedroom">
        </div>
        <div class="mb-3">
          <label class="form-label">Phòng tắm/WC (Bath_WC)</label>
          <input type="number" class="form-control" name="bath_wc">
        </div>
        <div class="mb-3">
          <label class="form-label">Đường (Road)</label>
          <input type="text" class="form-control" name="road">
        </div>
      </div>
      <div class="col-md-6">
        <div class="mb-3">
          <label class="form-label">Pháp lý (Legal)</label>
          <input type="text" class="form-control" name="legal">
        </div>
        <div class="mb-3">
          <label class="form-label">Nội thất (Interior)</label>
          <select class="form-select" name="interior">
            <option value="">Chọn</option>
            <option value="cơ bản">Cơ bản</option>
            <option value="đầy đủ">Đầy đủ</option>
          </select>
        </div>
        <div class="mb-3" id="water_price_group">
          <label class="form-label">Giá nước (WaterPrice)</label>
          <select class="form-select" name="water_price">
            <option value="">Chọn</option>
            <option value="Thoả thuận">Thoả thuận</option>
            <option value="Do Chủ Nhà Quyết định">Do Chủ Nhà Quyết định</option>
            <option value="Theo Nhà Cung Cấp">Theo Nhà Cung Cấp</option>
          </select>
        </div>
        <div class="mb-3" id="power_price_group">
          <label class="form-label">Giá điện (PowerPrice)</label>
          <select class="form-select" name="power_price">
            <option value="">Chọn</option>
            <option value="Thoả thuận">Thoả thuận</option>
            <option value="Do Chủ Nhà Quyết định">Do Chủ Nhà Quyết định</option>
            <option value="Theo Nhà Cung Cấp">Theo Nhà Cung Cấp</option>
          </select>
        </div>
        <div class="mb-3" id="utilities_group">
          <label class="form-label">Tiện ích (Utilities)</label>
          <select class="form-select" name="utilities">
            <option value="">Chọn</option>
            <option value="Thoả thuận">Thoả thuận</option>
            <option value="Do Chủ Nhà Quyết định">Do Chủ Nhà Quyết định</option>

          </select>
        </div>
      </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const saleType = document.getElementById('sale_type');
    const waterPriceGroup = document.getElementById('water_price_group');
    const powerPriceGroup = document.getElementById('power_price_group');
    const utilitiesGroup = document.getElementById('utilities_group');

    function toggleFields() {
        if (saleType.value === 'sale') {
            waterPriceGroup.style.display = 'none';
            powerPriceGroup.style.display = 'none';
            utilitiesGroup.style.display = 'none';
        } else {
            waterPriceGroup.style.display = '';
            powerPriceGroup.style.display = '';
            utilitiesGroup.style.display = '';
        }
    }

    saleType.addEventListener('change', toggleFields);
    toggleFields();
});
</script>
