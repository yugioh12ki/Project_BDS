@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="propertyListingForm" method="POST" enctype="multipart/form-data">
  @csrf
  <div class="modal fade" id="createPropertyListingModal" tabindex="-1" aria-labelledby="createPropertyListingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0 bg-white">
          <div>
            <h5 class="modal-title fw-bold fs-4 mb-1" id="createPropertyListingModalLabel">Tạo tin đăng ký gửi bất động sản</h5>
            <p class="text-muted mb-0 small">Hoàn thành các bước để tạo tin đăng ký gửi bất động sản</p>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        <div class="modal-body">

              <div class="bg-light p-3 rounded-3 mb-4">
                <div class="d-flex align-items-center">
                  <div class="process-icon rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                    <i class="bi bi-arrow-repeat fs-6"></i>
                  </div>
                  <div class="ms-3">
                    <h6 class="text-primary fw-bold mb-1">Quy trình ký gửi mới</h6>
                    <p class="text-primary mb-0 small">Sau khi tạo tin đăng, admin sẽ liên hệ để thương lượng hợp đồng. Thanh toán chỉ được thực hiện sau khi hợp đồng đã được ký kết.</p>
                  </div>
                </div>
              </div>

              <div class="d-flex justify-content-between mb-4">
                <div class="step-circle active">
                  <div class="circle-wrapper rounded-circle bg-white border border-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <span class="fw-bold">1</span>
                  </div>
                  <div class="text-center mt-1 small">Chọn BĐS</div>
                </div>
                <div class="step-connector flex-grow-1 position-relative mx-2">
                  <div class="line position-absolute bg-secondary" style="height: 2px; top: 24px; left: 0; right: 0;"></div>
                </div>
                <div class="step-circle">
                  <div class="circle-wrapper rounded-circle bg-white border border-2 border-secondary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <span class="fw-bold text-secondary">2</span>
                  </div>
                  <div class="text-center mt-1 small text-secondary">Thông tin tin đăng</div>
                </div>
                <div class="step-connector flex-grow-1 position-relative mx-2">
                  <div class="line position-absolute bg-secondary" style="height: 2px; top: 24px; left: 0; right: 0;"></div>
                </div>
                <div class="step-circle">
                  <div class="circle-wrapper rounded-circle bg-white border border-2 border-secondary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <span class="fw-bold text-secondary">3</span>
                  </div>
                  <div class="text-center mt-1 small text-secondary">Chọn gói đăng tin</div>
                </div>
              </div>

              <h6 class="fw-bold mb-3">Chọn bất động sản để đăng tin</h6>

              <div class="property-selection-list">
                @forelse($ownerProperties ?? [] as $property)
                  <div class="property-item border rounded mb-3 overflow-hidden">
                    <div class="property-content p-0">
                      <div class="property-image bg-light text-center" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                        @php
                          $thumbnailImage = null;
                          $firstImage = null;
                          $imageUrl = null;
                          $images = $property->images ?? collect();
                          
                          if ($images->isNotEmpty()) {
                              $thumbnailImage = $images->where('IsThumbnail', 1)->first();
                              $firstImage = $images->first();
                              if ($thumbnailImage) {
                                  $imageUrl = $thumbnailImage->ImageURL ?? ($thumbnailImage->ImagePath ? 'data:image/jpeg;base64,' . base64_encode($thumbnailImage->ImagePath) : null);
                              } elseif ($firstImage) {
                                  $imageUrl = $firstImage->ImageURL ?? ($firstImage->ImagePath ? 'data:image/jpeg;base64,' . base64_encode($firstImage->ImagePath) : null);
                              }
                          }
                        @endphp

                        @if($imageUrl)
                          <img src="{{ $imageUrl }}" alt="{{ $property->Title ?? 'Property image' }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                        @else
                          <svg class="text-secondary" width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                            <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                          </svg>
                        @endif
                      </div>
                      <div class="p-3">
                        <h5 class="mb-1 fw-bold">
                          @php
                            $typePro = $property->TypePro ?? '';
                            $danhMuc = $property->danhMuc ?? null;
                            $district = $property->District ?? '';
                            $propertyType = $danhMuc ? $danhMuc->ten_pro : 'BĐS';
                          @endphp
                          
                          @if($typePro == 'Sale' || $typePro == 'Sold')
                            Bán {{ $propertyType }} {{ $district }}
                          @else
                            Cho thuê {{ $propertyType }} {{ $district }}
                          @endif
                        </h5>
                        <p class="text-muted mb-2">
                          <i class="bi bi-geo-alt me-1"></i>
                          {{ implode(', ', array_filter([$property->Address ?? '', $property->Ward ?? '', $property->District ?? '', $property->Province ?? ''])) }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                          <span class="badge rounded-pill me-2" style="background-color: #eef2ff; color: #4f46e5;">
                            {{ $propertyType }}
                          </span>
                          <span class="badge rounded-pill {{ $typePro == 'Sale' || $typePro == 'Sold' ? 'bg-danger' : 'bg-primary' }}">
                            {{ $typePro == 'Sale' || $typePro == 'Sold' ? 'Đang bán' : 'Đang cho thuê' }}
                          </span>
                        </div>
                        <div class="d-flex justify-content-between text-center py-3 border-top border-bottom">
                          @php
                            $chiTiet = $property->chiTiet ?? null;
                            $area = $chiTiet ? ($chiTiet->Area ?? 'N/A') : 'N/A';
                            $bedroom = $chiTiet ? ($chiTiet->Bedroom ?? '0') : '0';
                            $bathroom = $chiTiet ? ($chiTiet->Bath_WC ?? '0') : '0';
                          @endphp
                          
                          <div class="px-2">
                            <i class="bi bi-rulers d-block mb-1"></i>
                            <strong class="d-block">{{ $area }}</strong>
                            <small class="text-muted">m²</small>
                          </div>
                          <div class="px-2">
                            <i class="bi bi-door-open d-block mb-1"></i>
                            <strong class="d-block">{{ $bedroom }}</strong>
                            <small class="text-muted">Phòng ngủ</small>
                          </div>
                          <div class="px-2">
                            <i class="bi bi-droplet d-block mb-1"></i>
                            <strong class="d-block">{{ $bathroom }}</strong>
                            <small class="text-muted">Phòng tắm</small>
                          </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                          <div>
                            <h5 class="fw-bold text-primary mb-0">
                              @php
                                $price = $property->Price ?? 0;
                              @endphp
                              @if($typePro == 'Sale' || $typePro == 'Sold')
                                {{ number_format($price / 1000000000, 1) }} tỷ VND
                              @else
                                {{ number_format($price / 1000000) }} triệu VND<span class="small">/tháng</span>
                              @endif
                            </h5>
                          </div>
                          <div class="form-check">
                            <input type="radio" 
                                   class="form-check-input" 
                                   name="selectedProperty" 
                                   id="property_{{ $property->PropertyID ?? '' }}" 
                                   value="{{ $property->PropertyID ?? '' }}" 
                                   data-type="{{ $typePro }}" 
                                   required>
                            <label class="form-check-label" for="property_{{ $property->PropertyID ?? '' }}">
                              Chọn bất động sản này
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Bạn chưa có bất động sản nào. Hãy thêm bất động sản trước khi tạo tin đăng.
                  </div>
                @endforelse
              </div>

              <!-- Step 2: Listing Details Section -->
              <div class="listing-details-section d-none">
                <h6 class="fw-bold mb-3">Thông tin tin đăng</h6>
                
                <div class="mb-4">
                  <label class="form-label fw-medium">Tiêu đề tin đăng <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="title" required>
                  <div class="form-text">Tiêu đề ngắn gọn, hấp dẫn sẽ thu hút nhiều người xem hơn</div>
                </div>

                <div class="mb-4">
                  <label class="form-label fw-medium">Mô tả chi tiết <span class="text-danger">*</span></label>
                  <textarea class="form-control" name="description" rows="6" required></textarea>
                  <div class="form-text">Cung cấp thông tin chi tiết về bất động sản của bạn</div>
                </div>

                <div class="mb-4">
                  <label class="form-label fw-medium">Hình ảnh <span class="text-danger">*</span></label>
                  <div class="image-upload-container border rounded-3 p-3">
                    <div class="row g-3" id="imagePreviewContainer">
                      <!-- Image preview slots -->
                      @for ($i = 0; $i < 3; $i++)
                      <div class="col-md-4">
                        <div class="image-upload-slot border rounded-3 d-flex align-items-center justify-content-center" style="height: 200px;">
                          <div class="text-center p-3">
                            <i class="bi bi-image fs-2 text-secondary"></i>
                            <p class="mb-0 small text-secondary">Tải lên hình {{ $i + 1 }}</p>
                          </div>
                        </div>
                      </div>
                      @endfor
                    </div>
                    <input type="file" class="d-none" id="imageInput" name="images[]" accept="image/*" multiple required>
                    <div class="text-center mt-3">
                      <button type="button" class="btn btn-outline-primary" id="addImagesBtn">
                        <i class="bi bi-plus-lg me-2"></i>Thêm hình ảnh (Tối thiểu 3 hình)
                      </button>
                    </div>
                  </div>
                </div>

                <div class="mb-4">
                  <label class="form-label fw-medium">Video giới thiệu (Không bắt buộc)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-youtube"></i></span>
                    <input type="text" class="form-control" name="video_url" placeholder="Nhập đường dẫn YouTube">
                  </div>
                  <div class="form-text">Hỗ trợ đường dẫn YouTube</div>
                </div>
              </div>

              <!-- Step 3: Package Selection Section -->
              <div class="package-selection-section d-none">
                <h6 class="fw-bold mb-3">Chọn gói đăng tin</h6>
                
                <div class="row g-3">
                  <div class="col-md-4">
                    <div class="card h-100 package-card border-2 cursor-pointer position-relative">
                      <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">Gói Cơ Bản</h5>
                        <div class="price-tag mb-3">
                          <h3 class="fw-bold mb-0">500.000đ</h3>
                          <span class="text-muted">/30 ngày</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Đăng tin 30 ngày</li>
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Hiển thị cơ bản</li>
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Hỗ trợ 24/7</li>
                        </ul>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="selectedPackage" value="basic" id="packageBasic" required>
                          <label class="form-check-label" for="packageBasic">
                            Chọn gói này
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="card h-100 package-card border-2 border-primary cursor-pointer position-relative">
                      <div class="position-absolute top-0 start-50 translate-middle">
                        <span class="badge bg-primary px-3 py-2 rounded-pill">Phổ biến</span>
                      </div>
                      <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">Gói Nổi Bật</h5>
                        <div class="price-tag mb-3">
                          <h3 class="fw-bold mb-0">1.200.000đ</h3>
                          <span class="text-muted">/30 ngày</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Đăng tin 30 ngày</li>
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Tin được đẩy lên top</li>
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Nhận báo cáo hiệu quả</li>
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Hỗ trợ ưu tiên 24/7</li>
                        </ul>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="selectedPackage" value="premium" id="packagePremium" required>
                          <label class="form-check-label" for="packagePremium">
                            Chọn gói này
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="card h-100 package-card border-2 cursor-pointer position-relative">
                      <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">Gói VIP</h5>
                        <div class="price-tag mb-3">
                          <h3 class="fw-bold mb-0">2.500.000đ</h3>
                          <span class="text-muted">/30 ngày</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Đăng tin 30 ngày</li>
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Vị trí đặc biệt</li>
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Tiếp cận khách hàng VIP</li>
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Hỗ trợ marketing</li>
                          <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Hỗ trợ VIP 24/7</li>
                        </ul>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="selectedPackage" value="vip" id="packageVIP" required>
                          <label class="form-check-label" for="packageVIP">
                            Chọn gói này
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
            <div class="modal-footer justify-content-center border-0">
              <button type="button" class="btn btn-light rounded-pill px-5 me-2 d-none" id="backButton">
                Quay lại
              </button>
              <button type="button" class="btn btn-primary rounded-pill px-5" id="nextButton" disabled>
                Tiếp tục
              </button>
            </div>
          </div>
        </div>
      </div>
  </div>
</form>

<script>
$(document).ready(function() {
    var currentStep = 1;
    
    // Xử lý click vào card bất động sản
    $('.property-item').click(function() {
        // Check radio và bỏ check các radio khác
        $(this).find('input[type="radio"]').prop('checked', true);
        // Enable nút Tiếp tục
        $('#nextButton').prop('disabled', false);
        // Thêm viền highlight cho card được chọn
        $('.property-item').removeClass('border-primary');
        $(this).addClass('border-primary');
    });

    // Xử lý nút Tiếp tục
    $('#nextButton').click(function() {
        switch(currentStep) {
            case 1:
                if($('input[name="selectedProperty"]:checked').length) {
                    // Ẩn step 1, hiện step 2
                    $('.property-selection-list').addClass('d-none');
                    $('.listing-details-section').removeClass('d-none');
                    currentStep = 2;
                    
                    // Hiện nút Quay lại
                    $('#backButton').removeClass('d-none');
                    
                    // Cập nhật UI steps
                    updateStepUI(2);
                }
                break;

            case 2:
                if(validateStep2()) {
                    // Ẩn step 2, hiện step 3
                    $('.listing-details-section').addClass('d-none');
                    $('.package-selection-section').removeClass('d-none');
                    currentStep = 3;
                    
                    // Cập nhật nút Tiếp tục thành "Hoàn tất"
                    $('#nextButton').text('Hoàn tất');
                    
                    // Cập nhật UI steps
                    updateStepUI(3);
                }
                break;

            case 3:
                if($('input[name="selectedPackage"]:checked').length) {
                    $('#propertyListingForm').submit();
                } else {
                    alert('Vui lòng chọn gói đăng tin');
                }
                break;
        }
    });

    // Back button handler  
    $('#backButton').on('click', function() {
        moveToStep(currentStep - 1);
    });

    function moveToStep(step) {
        // Hide all sections
        $('.property-selection-list, .listing-details-section, .package-selection-section').addClass('d-none');
        
        // Show appropriate section
        switch(step) {
            case 1:
                $('.property-selection-list').removeClass('d-none');
                $('#backButton').addClass('d-none');
                $('#nextButton').text('Tiếp tục').prop('disabled', !$('input[name="selectedProperty"]:checked').length);
                break;
            case 2:
                $('.listing-details-section').removeClass('d-none');
                $('#backButton').removeClass('d-none');
                $('#nextButton').text('Tiếp tục').prop('disabled', false);
                break;
            case 3:
                $('.package-selection-section').removeClass('d-none');
                $('#backButton').removeClass('d-none');
                $('#nextButton').text('Hoàn tất').prop('disabled', false);
                break;
        }

        // Update step indicators
        $('.step-circle').each(function(index) {
            if(index + 1 <= step) {
                $(this).addClass('active')
                    .find('.circle-wrapper').removeClass('border-secondary')
                    .find('span').removeClass('text-secondary');
            } else {
                $(this).removeClass('active')
                    .find('.circle-wrapper').addClass('border-secondary')
                    .find('span').addClass('text-secondary');
            }
        });

        currentStep = step;
    }

    function updateStepUI(step) {
        $('.step-circle').each(function(index) {
            if(index < step) {
                $(this).addClass('active')
                    .find('.circle-wrapper').removeClass('border-secondary')
                    .find('span').removeClass('text-secondary');
            } else {
                $(this).removeClass('active')
                    .find('.circle-wrapper').addClass('border-secondary')
                    .find('span').addClass('text-secondary');  
            }
        });
    }

    function validateStep2() {
        if(!$('input[name="title"]').val() || !$('textarea[name="description"]').val()) {
            alert('Vui lòng điền đầy đủ thông tin bắt buộc');
            return false;
        }
        if(!$('#imageInput')[0].files || $('#imageInput')[0].files.length < 3) {
            alert('Vui lòng tải lên ít nhất 3 hình ảnh');
            return false;
        }
        return true;
    }
});
</script>
