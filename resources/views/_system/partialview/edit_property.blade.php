<div class="modal-header">
  <h5 class="modal-title" id="editPropertyModalLabel">
      <i class="bi bi-pencil-square me-2"></i> Chỉnh sửa bất động sản
  </h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
</div>
<div class="modal-body">
  <p class="text-muted">Cập nhật thông tin chi tiết về bất động sản của bạn</p>
  
  <ul class="nav nav-tabs mb-4" id="propertyEditTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-info" type="button" role="tab">Thông tin cơ bản</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-info" type="button" role="tab">Chi tiết</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images-info" type="button" role="tab">Hình ảnh & Giấy tờ</button>
    </li>
  </ul>
  
  <form id="editPropertyForm" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" id="edit_property_id" name="property_id">
      
      <div class="tab-content" id="propertyEditTabsContent">
          <!-- Tab 1: Thông tin cơ bản -->
          <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
              <div class="row g-3">
                  <div class="col-md-12 mb-3">
                      <label for="edit_title" class="form-label">Tên bất động sản</label>
                      <input type="text" class="form-control" id="edit_title" name="Title" required>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                      <label for="edit_property_type" class="form-label">Loại bất động sản</label>
                      <select class="form-select" id="edit_property_type" name="PropertyType" required>
                          @foreach($categories as $category)
                              <option value="{{ $category->Protype_ID }}">{{ $category->ten_pro }}</option>
                          @endforeach
                      </select>
                  </div>
                  
                  <div class="col-md-6 mb-3">
                      <label for="edit_type_pro" class="form-label">Trạng thái</label>
                      <select class="form-select" id="edit_type_pro" name="TypePro" required>
                          <option value="Sale">Đang bán</option>
                          <option value="Rent">Đang cho thuê</option>
                          <option value="Sold">Đã bán</option>
                          <option value="Rented">Đã cho thuê</option>
                          <option value="inactive">Không hoạt động</option>
                      </select>
                  </div>
                  
                  <div class="col-md-12 mb-3">
                      <label for="edit_address" class="form-label">Địa chỉ</label>
                      <input type="text" class="form-control" id="edit_address" name="Address" required>
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_district" class="form-label">Quận/Huyện</label>
                      <input type="text" class="form-control" id="edit_district" name="District" required>
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_ward" class="form-label">Phường/Xã</label>
                      <input type="text" class="form-control" id="edit_ward" name="Ward" required>
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_province" class="form-label">Tỉnh/Thành phố</label>
                      <input type="text" class="form-control" id="edit_province" name="Province" required>
                  </div>
                  
                  <div class="col-md-12 mb-3">
                      <label for="edit_description" class="form-label">Mô tả</label>
                      <textarea class="form-control" id="edit_description" name="Description" rows="4"></textarea>
                  </div>
              </div>
          </div>
          
          <!-- Tab 2: Chi tiết -->
          <div class="tab-pane fade" id="details-info" role="tabpanel">
              <div class="row g-3">
                  <div class="col-md-6 mb-3">
                      <label for="edit_area" class="form-label">Diện tích (m²)</label>
                      <input type="number" class="form-control" id="edit_area" name="Area">
                  </div>
                  
                  <div class="col-md-6 mb-3">
                      <label for="edit_price" class="form-label">Giá</label>
                      <input type="number" class="form-control" id="edit_price" name="Price" required>
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_bedroom" class="form-label">Phòng ngủ</label>
                      <input type="number" class="form-control" id="edit_bedroom" name="Bedroom" min="0">
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_bathroom" class="form-label">Phòng tắm</label>
                      <input type="number" class="form-control" id="edit_bathroom" name="Bath_WC" min="0">
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_floor" class="form-label">Số tầng</label>
                      <input type="number" class="form-control" id="edit_floor" name="Floor" min="0">
                  </div>
                  
                  <div class="col-md-12 mb-3">
                      <label for="edit_legal" class="form-label">Pháp lý</label>
                      <input type="text" class="form-control" id="edit_legal" name="legal">
                  </div>
                  
                  <div class="col-md-12 mb-3">
                      <label for="edit_interior" class="form-label">Nội thất</label>
                      <select class="form-select" id="edit_interior" name="Interior">
                          <option value="">Chọn</option>
                          <option value="cơ bản">Cơ bản</option>
                          <option value="đầy đủ">Đầy đủ</option>
                      </select>
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_view" class="form-label">View</label>
                      <input type="text" class="form-control" id="edit_view" name="view">
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_near" class="form-label">Gần</label>
                      <input type="text" class="form-control" id="edit_near" name="near">
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_road" class="form-label">Đường</label>
                      <input type="text" class="form-control" id="edit_road" name="Road">
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_water_price" class="form-label">Giá nước</label>
                      <input type="number" class="form-control" id="edit_water_price" name="WaterPrice">
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_power_price" class="form-label">Giá điện</label>
                      <input type="number" class="form-control" id="edit_power_price" name="PowerPrice">
                  </div>
                  
                  <div class="col-md-4 mb-3">
                      <label for="edit_utilities" class="form-label">Tiện ích</label>
                      <input type="text" class="form-control" id="edit_utilities" name="Utilities">
                  </div>
              </div>
          </div>
          
          <!-- Tab 3: Hình ảnh & Giấy tờ -->
          <div class="tab-pane fade" id="images-info" role="tabpanel">
              <div class="mb-4">
                  <h6 class="mb-3">Hình ảnh hiện tại</h6>
                  <div class="d-flex flex-wrap gap-2 mb-3" id="property_images_container">
                      <!-- Hình ảnh sẽ được nạp bằng JavaScript -->
                  </div>
                  <div class="mb-3">
                      <label for="new_images" class="form-label">Thêm hình ảnh mới</label>
                      <input class="form-control" type="file" id="new_images" name="new_images[]" multiple accept="image/*">
                      <small class="text-muted">PNG, JPG, WEBP tối đa 10MB</small>
                  </div>
              </div>
              
              <div class="mb-4">
                  <h6 class="mb-3">Giấy tờ pháp lý</h6>
                  
                  <div class="mb-3">
                      <div class="form-check mb-2">
                          <input class="form-check-input" type="checkbox" id="edit_so_hong" name="legal_docs[]" value="Sổ hồng">
                          <label class="form-check-label" for="edit_so_hong">Sổ hồng</label>
                      </div>
                      <div class="form-check mb-2">
                          <input class="form-check-input" type="checkbox" id="edit_so_do" name="legal_docs[]" value="Sổ đỏ">
                          <label class="form-check-label" for="edit_so_do">Sổ đỏ</label>
                      </div>
                      <div class="form-check mb-2">
                          <input class="form-check-input" type="checkbox" id="edit_hop_dong_mua_ban" name="legal_docs[]" value="Hợp đồng mua bán">
                          <label class="form-check-label" for="edit_hop_dong_mua_ban">Hợp đồng mua bán</label>
                      </div>
                      <div class="form-check mb-2">
                          <input class="form-check-input" type="checkbox" id="edit_giay_phep_xay_dung" name="legal_docs[]" value="Giấy phép xây dựng">
                          <label class="form-check-label" for="edit_giay_phep_xay_dung">Giấy phép xây dựng</label>
                      </div>
                      <div class="form-check mb-2">
                          <input class="form-check-input" type="checkbox" id="edit_giay_phep_kinh_doanh" name="legal_docs[]" value="Giấy phép kinh doanh">
                          <label class="form-check-label" for="edit_giay_phep_kinh_doanh">Giấy phép kinh doanh</label>
                      </div>
                      <div class="form-check mb-2">
                          <input class="form-check-input" type="checkbox" id="edit_giay_phep_quy_hoach" name="legal_docs[]" value="Giấy phép quy hoạch">
                          <label class="form-check-label" for="edit_giay_phep_quy_hoach">Giấy phép quy hoạch</label>
                      </div>
                      <div class="form-check mb-2">
                          <input class="form-check-input" type="checkbox" id="edit_bien_ban_ban_giao" name="legal_docs[]" value="Biên bản bàn giao">
                          <label class="form-check-label" for="edit_bien_ban_ban_giao">Biên bản bàn giao</label>
                      </div>
                  </div>
                  
                  <div class="mb-3">
                      <label for="edit_other_docs" class="form-label">Giấy tờ khác</label>
                      <input type="text" class="form-control" id="edit_other_docs" name="other_docs" placeholder="Nhập tên giấy tờ khác (nếu có)">
                  </div>
                  
                  <div class="mb-3">
                      <label for="new_documents" class="form-label">Tải lên giấy tờ mới</label>
                      <input class="form-control" type="file" id="new_documents" name="new_documents[]" multiple accept=".pdf,.doc,.docx">
                  </div>
              </div>
          </div>
      </div>
      
      <div class="d-flex justify-content-between mt-4">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i> Lưu thay đổi
          </button>
      </div>
  </form>
</div> 