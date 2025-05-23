<form action="{{ route('owner.property.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="container-fluid">
        <ul class="nav nav-tabs mb-4" id="propertyAddTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="basic-info-tab" data-bs-toggle="tab" data-bs-target="#basic-info-add" type="button" role="tab">Thông tin cơ bản</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="details-info-tab" data-bs-toggle="tab" data-bs-target="#details-info-add" type="button" role="tab">Chi tiết</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="media-tab" data-bs-toggle="tab" data-bs-target="#media-add" type="button" role="tab">Hình ảnh & Video</button>
            </li>
        </ul>

        <div class="tab-content" id="propertyAddTabsContent">
            <!-- Thông tin cơ bản -->
            <div class="tab-pane fade show active" id="basic-info-add" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Địa Chỉ</label>
                            <input type="text" class="form-control" name="Address" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tỉnh/Thành phố</label>
                            <select class="form-select" id="province" name="Province" required>
                                <option value="">Chọn Tỉnh/Thành phố</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quận/Huyện</label>
                            <select class="form-select" id="district" name="District" required>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phường/Xã</label>
                            <select class="form-select" id="ward" name="Ward" required>
                                <option value="">Chọn Phường/Xã</option>
                            </select>
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
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Giá Tiền</label>
                            <input type="number" class="form-control" name="Price" required>
                        </div>
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
                            <select class="form-select" name="TypePro" required>
                                <option value="">Chọn</option>
                                <option value="Rent">Thuê</option>
                                <option value="Sale">Bán</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chi tiết -->
            <div class="tab-pane fade" id="details-info-add" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Số tầng (Levelhouse)</label>
                            <input type="number" class="form-control" name="Levelhouse">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tầng (Floor)</label>
                            <input type="number" class="form-control" name="Floor">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chiều dài nhà (HouseLength)</label>
                            <input type="number" class="form-control" name="HouseLength">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chiều rộng nhà (HouseWidth)</label>
                            <input type="number" class="form-control" name="HouseWidth">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chiều dài tổng (TotalLength)</label>
                            <input type="number" class="form-control" name="TotalLength">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chiều rộng tổng (TotalWidth)</label>
                            <input type="number" class="form-control" name="TotalWidth">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phòng ngủ (Bedroom)</label>
                            <input type="number" class="form-control" name="Bedroom">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ban công (Balcony)</label>
                            <select class="form-select" name="Balcony">
                                <option value="">Chọn</option>
                                <option value="1">Có</option>
                                <option value="0">Không</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phòng tắm/WC (Bath_WC)</label>
                            <input type="number" class="form-control" name="Bath_WC">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Đường (Road)</label>
                            <input type="number" class="form-control" name="Road">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Pháp lý (legal)</label>
                            <input type="text" class="form-control" name="legal">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hướng nhà (view)</label>
                            <select class="form-select" name="view">
                                <option value="">Chọn</option>
                                <option value="Bắc">Bắc</option>
                                <option value="Tây Bắc">Tây Bắc</option>
                                <option value="Tây">Tây</option>
                                <option value="Tây Nam">Tây Nam</option>
                                <option value="Nam">Nam</option>
                                <option value="Đông Nam">Đông Nam</option>
                                <option value="Đông">Đông</option>
                                <option value="Đông Bắc">Đông Bắc</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gần (near)</label>
                            <input type="text" class="form-control" name="near">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nội thất (Interior)</label>
                            <select class="form-select" name="Interior">
                                <option value="">Chọn</option>
                                <option value="Cơ Bản">Cơ Bản</option>
                                <option value="Đầy đủ">Đầy đủ</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá nước (WaterPrice)</label>
                            <select class="form-select" name="WaterPrice">
                                <option value="">Chọn</option>
                                <option value="Thỏa thuận">Thỏa thuận</option>
                                <option value="Do chủ nhà quy định">Do chủ nhà quy định</option>
                                <option value="Theo nhà cung cấp">Theo nhà cung cấp</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá điện (PowerPrice)</label>
                            <select class="form-select" name="PowerPrice">
                                <option value="">Chọn</option>
                                <option value="Thỏa thuận">Thỏa thuận</option>
                                <option value="Do chủ nhà quy định">Do chủ nhà quy định</option>
                                <option value="Theo nhà cung cấp">Theo nhà cung cấp</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tiện ích (Utilities)</label>
                            <select class="form-select" name="Utilities">
                                <option value="">Chọn</option>
                                <option value="Thỏa thuận">Thỏa thuận</option>
                                <option value="Do chủ nhà quy định">Do chủ nhà quy định</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hình ảnh & Video -->
            <div class="tab-pane fade" id="media-add" role="tabpanel">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Vui lòng tải lên hình ảnh và video để admin có thể xem xét và phê duyệt bất động sản của bạn.
                </div>
                <div class="mb-4">
                    <h6 class="mb-3">Hình ảnh bất động sản</h6>
                    <div class="mb-3">
                        <label for="property_images" class="form-label">Tải lên hình ảnh (tối đa 10 hình)</label>
                        <input class="form-control" type="file" id="property_images" name="property_images[]" multiple accept="image/*">
                        <small class="text-muted">PNG, JPG, WEBP tối đa 10MB mỗi hình</small>
                    </div>
                </div>
                <div class="mb-4">
                    <h6 class="mb-3">Video giới thiệu</h6>
                    <div class="mb-3">
                        <label for="property_video" class="form-label">Tải lên video</label>
                        <input class="form-control" type="file" id="property_video" name="property_video" accept="video/*">
                        <small class="text-muted">MP4, AVI, MOV tối đa 50MB</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-primary">Lưu</button>
    </div>
</form>
