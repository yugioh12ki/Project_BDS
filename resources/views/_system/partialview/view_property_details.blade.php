<div class="modal-header">
  <h5 class="modal-title" id="viewPropertyModalLabel">
    <i class="bi bi-eye me-2"></i> Chi tiết bất động sản
  </h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
</div>
<div class="modal-body">
  <input type="hidden" id="view_property_id">
  
  <!-- Property Title & Address -->
  <div class="mb-4">
    <h4 id="view_property_title" class="fw-bold mb-2"></h4>
    <p class="text-muted mb-0">
      <i class="bi bi-geo-alt me-1"></i> 
      <span id="view_property_address"></span>
    </p>
  </div>
  
  <!-- Property Image -->
  <div class="mb-4">
    <div class="position-relative">
      <img id="view_property_image" src="" alt="Property image" class="img-fluid rounded" style="width: 100%; height: 250px; object-fit: cover;">
      <div class="position-absolute top-0 end-0 m-2">
        <span id="view_property_status" class="badge rounded-pill py-2 px-3 fw-normal"></span>
      </div>
    </div>
  </div>
  
  <!-- Property Basic Info -->
  <div class="d-flex justify-content-between mb-4">
    <div class="text-center">
      <i class="bi bi-building fs-4 mb-2"></i>
      <p class="fw-bold mb-0" id="view_property_type"></p>
      <small class="text-muted">Loại BĐS</small>
    </div>
    <div class="text-center">
      <i class="bi bi-rulers fs-4 mb-2"></i>
      <p class="fw-bold mb-0" id="view_property_area"></p>
      <small class="text-muted">Diện tích</small>
    </div>
    <div class="text-center">
      <i class="bi bi-door-open fs-4 mb-2"></i>
      <p class="fw-bold mb-0" id="view_property_bedroom"></p>
      <small class="text-muted">Phòng ngủ</small>
    </div>
    <div class="text-center">
      <i class="bi bi-droplet fs-4 mb-2"></i>
      <p class="fw-bold mb-0" id="view_property_bathroom"></p>
      <small class="text-muted">Phòng tắm</small>
    </div>
  </div>
  
  <!-- Price Information -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h6 class="text-muted mb-1">Giá bán</h6>
      <h4 class="fw-bold text-primary" id="view_property_price_sale"></h4>
    </div>
    <div>
      <h6 class="text-muted mb-1">Giá thuê</h6>
      <h4 class="fw-bold text-primary" id="view_property_price_rent"></h4>
    </div>
  </div>
  
  <!-- Tabs -->
  <ul class="nav nav-tabs mb-3" id="propertyDetailTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-info" type="button" role="tab">Chi tiết</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features-info" type="button" role="tab">Tiện ích</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents-info" type="button" role="tab">Giấy tờ</button>
    </li>
  </ul>
  
  <div class="tab-content" id="propertyDetailTabsContent">
    <!-- Tab 1: Chi tiết -->
    <div class="tab-pane fade show active" id="details-info" role="tabpanel">
      <div class="mb-4">
        <h6 class="fw-bold mb-3">Mô tả</h6>
        <p id="view_property_description" class="text-muted"></p>
      </div>
      
      <div class="row">
        <div class="col-md-6 mb-3">
          <h6 class="fw-bold mb-3">Thông tin chi tiết</h6>
          <table class="table table-striped">
            <tbody>
              <tr>
                <td>Số tầng</td>
                <td id="view_property_floor"></td>
              </tr>
              <tr>
                <td>Nội thất</td>
                <td id="view_property_interior"></td>
              </tr>
              <tr>
                <td>View</td>
                <td id="view_property_view"></td>
              </tr>
              <tr>
                <td>Đường</td>
                <td id="view_property_road"></td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="col-md-6 mb-3">
          <h6 class="fw-bold mb-3">Thông tin thêm</h6>
          <table class="table table-striped">
            <tbody>
              <tr>
                <td>Năm xây dựng</td>
                <td>2019</td>
              </tr>
              <tr>
                <td>Năm cải tạo gần nhất</td>
                <td>2022</td>
              </tr>
              <tr>
                <td>Ngày đăng</td>
                <td id="view_property_posted_date"></td>
              </tr>
              <tr>
                <td>Trạng thái</td>
                <td id="view_property_status_text"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Utility Prices (only for rentals) -->
      <div id="utility_prices_section" class="mb-4">
        <h6 class="fw-bold mb-3">Phí dịch vụ</h6>
        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="card h-100 bg-light">
              <div class="card-body text-center">
                <i class="bi bi-droplet-fill fs-3 text-info mb-2"></i>
                <h6>Giá nước</h6>
                <p class="mb-0 fw-bold" id="view_property_water_price"></p>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="card h-100 bg-light">
              <div class="card-body text-center">
                <i class="bi bi-lightning-fill fs-3 text-warning mb-2"></i>
                <h6>Giá điện</h6>
                <p class="mb-0 fw-bold" id="view_property_power_price"></p>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="card h-100 bg-light">
              <div class="card-body text-center">
                <i class="bi bi-tools fs-3 text-secondary mb-2"></i>
                <h6>Tiện ích</h6>
                <p class="mb-0 fw-bold" id="view_property_utilities"></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Tab 2: Tiện ích -->
    <div class="tab-pane fade" id="features-info" role="tabpanel">
      <div class="mb-4">
        <h6 class="fw-bold mb-3">Tiện ích và đặc điểm</h6>
        <div class="row" id="amenities_container">
          <div class="col-md-6 mb-2">
            <div class="d-flex align-items-center">
              <i class="bi bi-check-circle-fill text-success me-2"></i>
              <span>Sân vườn</span>
            </div>
          </div>
          <div class="col-md-6 mb-2">
            <div class="d-flex align-items-center">
              <i class="bi bi-check-circle-fill text-success me-2"></i>
              <span>Gara ô tô</span>
            </div>
          </div>
          <div class="col-md-6 mb-2">
            <div class="d-flex align-items-center">
              <i class="bi bi-check-circle-fill text-success me-2"></i>
              <span>An ninh 24/7</span>
            </div>
          </div>
          <div class="col-md-6 mb-2">
            <div class="d-flex align-items-center">
              <i class="bi bi-check-circle-fill text-success me-2"></i>
              <span>Nội thất cao cấp</span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="mb-4">
        <h6 class="fw-bold mb-3">Địa điểm lân cận</h6>
        <div id="nearby_locations">
          <div class="mb-2">
            <i class="bi bi-geo me-2 text-primary"></i>
            <span id="view_property_near"></span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Tab 3: Giấy tờ -->
    <div class="tab-pane fade" id="documents-info" role="tabpanel">
      <div class="mb-4">
        <h6 class="fw-bold mb-3">Giấy tờ pháp lý</h6>
        <div id="legal_documents">
          <!-- Legal documents will be populated here -->
        </div>
      </div>
      
      <div class="mb-4">
        <h6 class="fw-bold mb-3">Hình ảnh giấy tờ</h6>
        <div class="row" id="document_images">
          <!-- Document images will be populated here -->
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
  <button type="button" class="btn btn-primary edit-from-view" data-bs-toggle="modal" data-bs-target="#editPropertyModal">
    <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa
  </button>
</div> 