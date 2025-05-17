<form action="{{ route('owner.property.store') }}" method="POST">
    @csrf
    <div class="container-fluid">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Chủ sở hữu</label>
                    <select class="form-select" name="owner" required>
                        <option value="">Chọn</option>
                        @foreach ($owners as $user)
                            <option value="{{ $user->UserID }}" {{ Auth::user()->UserID == $user->UserID ? 'selected' : '' }}>
                                {{ $user->Name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Địa Chỉ</label>
                    <input type="text" class="form-control" name="Address" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phường</label>
                    <input type="text" class="form-control" name="Ward" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quận</label>
                    <input type="text" class="form-control" name="District" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tỉnh</label>
                    <input type="text" class="form-control" name="Province" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Loại Bất Động Sản</label>
                    <select class="form-select" name="PropertyType" required>
                        <option value="">Chọn</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->Protype_ID }}">{{ $cat->ten_pro }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giá Tiền</label>
                    <input type="number" class="form-control" name="Price" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số tầng (Floor)</label>
                    <input type="number" class="form-control" name="Floor">
                </div>
                <div class="mb-3">
                    <label class="form-label">Diện tích (Area)</label>
                    <input type="number" class="form-control" name="Area">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phòng ngủ (Bedroom)</label>
                    <input type="number" class="form-control" name="Bedroom">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phòng tắm/WC (Bath_WC)</label>
                    <input type="number" class="form-control" name="Bath_WC">
                </div>
                <div class="mb-3">
                    <label class="form-label">Đường (Road)</label>
                    <input type="text" class="form-control" name="Road">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Tiêu Đề</label>
                    <input type="text" class="form-control" name="Title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô Tả</label>
                    <textarea class="form-control" name="Description" rows="7"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Loại Thuê/Bán</label>
                    <select class="form-select" name="TypePro" id="sale_type" required>
                        <option value="">Chọn</option>
                        <option value="Rent">Thuê</option>
                        <option value="Sale">Bán</option>
                        <option value="Sale/Rent">Sale/Rent</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pháp lý (Legal)</label>
                    <input type="text" class="form-control" name="legal">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nội thất (Interior)</label>
                    <select class="form-select" name="Interior">
                        <option value="">Chọn</option>
                        <option value="cơ bản">Cơ bản</option>
                        <option value="đầy đủ">Đầy đủ</option>
                    </select>
                </div>
                <div class="mb-3" id="water_price_group">
                    <label class="form-label">Giá nước (WaterPrice)</label>
                    <select class="form-select" name="WaterPrice">
                        <option value="">Chọn</option>
                        <option value="Thoả thuận">Thoả thuận</option>
                        <option value="Do Chủ Nhà Quyết định">Do Chủ Nhà Quyết định</option>
                        <option value="Theo Nhà Cung Cấp">Theo Nhà Cung Cấp</option>
                    </select>
                </div>
                <div class="mb-3" id="power_price_group">
                    <label class="form-label">Giá điện (PowerPrice)</label>
                    <select class="form-select" name="PowerPrice">
                        <option value="">Chọn</option>
                        <option value="Thoả thuận">Thoả thuận</option>
                        <option value="Do Chủ Nhà Quyết định">Do Chủ Nhà Quyết định</option>
                        <option value="Theo Nhà Cung Cấp">Theo Nhà Cung Cấp</option>
                    </select>
                </div>
                <div class="mb-3" id="utilities_group">
                    <label class="form-label">Tiện ích (Utilities)</label>
                    <select class="form-select" name="Utilities">
                        <option value="">Chọn</option>
                        <option value="Thoả thuận">Thoả thuận</option>
                        <option value="Do Chủ Nhà Quyết định">Do Chủ Nhà Quyết định</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col text-end">
                <button type="submit" class="btn btn-success px-4">Lưu</button>
            </div>
        </div>
    </div>
</form>



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
