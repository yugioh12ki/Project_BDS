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
                        <div class="image-upload-slot border rounded-3 d-flex align-items-center justify-content-center" style="height: 200px; overflow: hidden;" id="previewSlot{{ $i }}">
                          <div class="text-center p-3 preview-placeholder">
                            <i class="bi bi-image fs-2 text-secondary"></i>
                            <p class="mb-0 small text-secondary">Tải lên hình {{ $i + 1 }}</p>
                          </div>
                          <img src="#" alt="Preview" class="img-preview" style="display: none; max-width: 100%; max-height: 100%; object-fit: contain;">
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
                
<script>
// Xử lý hiển thị hình ảnh khi chọn file
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('imageInput');
    const addImagesBtn = document.getElementById('addImagesBtn');
    
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const files = this.files;
            
            // Hiển thị số lượng hình ảnh đã chọn
            if (files.length > 0 && addImagesBtn) {
                if (files.length >= 3) {
                    addImagesBtn.innerHTML = `<i class="bi bi-check-lg me-2"></i>Đã chọn ${files.length} hình ảnh`;
                    addImagesBtn.classList.remove('btn-outline-primary');
                    addImagesBtn.classList.add('btn-success');
                } else {
                    addImagesBtn.innerHTML = `<i class="bi bi-exclamation-triangle me-2"></i>Cần chọn thêm ${3 - files.length} hình ảnh`;
                    addImagesBtn.classList.remove('btn-outline-primary', 'btn-success');
                    addImagesBtn.classList.add('btn-warning');
                }
            }
            
            // Hiển thị preview cho tối đa 3 hình ảnh
            const maxPreview = Math.min(files.length, 3);
            
            // Ẩn tất cả placeholder và reset tất cả preview
            const placeholders = document.querySelectorAll('.preview-placeholder');
            const previews = document.querySelectorAll('.img-preview');
            
            placeholders.forEach(placeholder => {
                placeholder.style.display = 'block';
            });
            
            previews.forEach(preview => {
                preview.style.display = 'none';
                preview.src = '#';
            });
            
            // Hiển thị preview cho các hình ảnh đã chọn
            for (let i = 0; i < maxPreview; i++) {
                const file = files[i];
                const previewSlot = document.getElementById(`previewSlot${i}`);
                
                if (previewSlot) {
                    const placeholder = previewSlot.querySelector('.preview-placeholder');
                    const preview = previewSlot.querySelector('.img-preview');
                    
                    if (placeholder && preview) {
                        // Ẩn placeholder
                        placeholder.style.display = 'none';
                        
                        // Hiển thị preview
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        });
    }
    
    if (addImagesBtn) {
        addImagesBtn.addEventListener('click', function() {
            if (imageInput) {
                imageInput.click();
            }
        });
    }
});
</script>

                <div class="mb-4">
                  <label class="form-label fw-medium">Video giới thiệu (Không bắt buộc)</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <select class="form-select border-0 bg-transparent" name="video_platform" style="width: auto; min-width: 100px;">
                        <option value="youtube">YouTube</option>
                        <option value="tiktok">TikTok</option>
                      </select>
                    </span>
                    <input type="text" class="form-control" name="video_url" placeholder="Nhập đường dẫn video">
                  </div>
                  <div class="form-text">Hỗ trợ đường dẫn YouTube hoặc TikTok</div>
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
              <button type="button" class="btn btn-light rounded-pill px-5 me-2 d-none" id="backButton" onclick="moveToPreviousStep()">
                Quay lại
              </button>
              
<script>
function moveToPreviousStep() {
    console.log('moveToPreviousStep called');
    
    // Get current step by checking which section is visible
    var currentStep = 1;
    var listingDetailsSection = document.querySelector('.listing-details-section');
    var packageSelectionSection = document.querySelector('.package-selection-section');
    
    if (listingDetailsSection && window.getComputedStyle(listingDetailsSection).display !== 'none') {
        currentStep = 2;
    } else if (packageSelectionSection && window.getComputedStyle(packageSelectionSection).display !== 'none') {
        currentStep = 3;
    }
    
    console.log('Current step:', currentStep);
    
    // Hide all sections using vanilla JavaScript
    var sections = document.querySelectorAll('.property-selection-list, .listing-details-section, .package-selection-section');
    sections.forEach(function(section) {
        if (section) {
            section.classList.add('d-none');
            section.style.display = 'none';
        }
    });
    
    switch(currentStep) {
        case 2:
            // Move back to step 1
            var propertySelectionList = document.querySelector('.property-selection-list');
            if (propertySelectionList) {
                propertySelectionList.classList.remove('d-none');
                propertySelectionList.style.display = 'block';
            }
            
            // Hide back button
            var backButton = document.getElementById('backButton');
            if (backButton) {
                backButton.classList.add('d-none');
            }
            
            // Update step UI
            updateStepUI(1);
            
            console.log('Moved back to step 1');
            break;
            
        case 3:
            // Move back to step 2
            if (listingDetailsSection) {
                listingDetailsSection.classList.remove('d-none');
                listingDetailsSection.style.display = 'block';
            }
            
            // Change button text back to "Tiếp tục"
            var nextButton = document.getElementById('nextButton');
            if (nextButton) {
                nextButton.textContent = 'Tiếp tục';
            }
            
            // Update step UI
            updateStepUI(2);
            
            console.log('Moved back to step 2');
            break;
    }
}
</script>
              <button type="button" class="btn btn-primary rounded-pill px-5" id="nextButton" onclick="moveToNextStep()">
                Tiếp tục
              </button>
              
<script>
function moveToNextStep() {
    console.log('moveToNextStep called');
    
    // Get current step by checking which section is visible
    var currentStep = 1;
    var listingDetailsSection = document.querySelector('.listing-details-section');
    var packageSelectionSection = document.querySelector('.package-selection-section');
    
    if (listingDetailsSection && window.getComputedStyle(listingDetailsSection).display !== 'none') {
        currentStep = 2;
    } else if (packageSelectionSection && window.getComputedStyle(packageSelectionSection).display !== 'none') {
        currentStep = 3;
    }
    
    console.log('Current step:', currentStep);
    
    switch(currentStep) {
        case 1:
            // Check if property is selected
            var selectedProperty = document.querySelector('input[name="selectedProperty"]:checked');
            if (!selectedProperty) {
                alert('Vui lòng chọn một bất động sản để tiếp tục.');
                return;
            }
            
            // Move to step 2 using vanilla JavaScript
            var propertySelectionList = document.querySelector('.property-selection-list');
            if (propertySelectionList) {
                propertySelectionList.classList.add('d-none');
                propertySelectionList.style.display = 'none';
            }
            
            if (listingDetailsSection) {
                listingDetailsSection.classList.remove('d-none');
                listingDetailsSection.style.display = 'block';
            }
            
            // Show back button
            var backButton = document.getElementById('backButton');
            if (backButton) {
                backButton.classList.remove('d-none');
            }
            
            // Update step UI
            updateStepUI(2);
            
            console.log('Moved to step 2');
            break;
            
        case 2:
            // Validate step 2
            if (!validateStep2()) {
                return;
            }
            
            // Move to step 3 using vanilla JavaScript
            if (listingDetailsSection) {
                listingDetailsSection.classList.add('d-none');
                listingDetailsSection.style.display = 'none';
            }
            
            if (packageSelectionSection) {
                packageSelectionSection.classList.remove('d-none');
                packageSelectionSection.style.display = 'block';
            }
            
            // Change button text
            var nextButton = document.getElementById('nextButton');
            if (nextButton) {
                nextButton.textContent = 'Hoàn tất';
            }
            
            // Update step UI
            updateStepUI(3);
            
            console.log('Moved to step 3');
            break;
            
        case 3:
            // Check if package is selected using vanilla JavaScript
            var selectedPackage = document.querySelector('input[name="selectedPackage"]:checked');
            if (!selectedPackage) {
                alert('Vui lòng chọn gói đăng tin');
                return;
            }
            
            // Submit form
            submitForm();
            break;
    }
}

function updateStepUI(step) {
    // Use vanilla JavaScript
    var stepCircles = document.querySelectorAll('.step-circle');
    
    stepCircles.forEach(function(circle, index) {
        var stepNumber = index + 1;
        var wrapper = circle.querySelector('.circle-wrapper');
        var span = wrapper ? wrapper.querySelector('span') : null;
        var text = circle.querySelector('div:last-child');
        
        if (stepNumber <= step) {
            circle.classList.add('active');
            if (wrapper) {
                wrapper.classList.remove('border-secondary');
                wrapper.classList.add('border-primary');
            }
            if (span) {
                span.classList.remove('text-secondary');
                span.classList.add('text-primary');
            }
            if (text) {
                text.classList.remove('text-secondary');
                text.classList.add('text-primary');
            }
        } else {
            circle.classList.remove('active');
            if (wrapper) {
                wrapper.classList.remove('border-primary');
                wrapper.classList.add('border-secondary');
            }
            if (span) {
                span.classList.remove('text-primary');
                span.classList.add('text-secondary');
            }
            if (text) {
                text.classList.remove('text-primary');
                text.classList.add('text-secondary');
            }
        }
    });
}

function validateStep2() {
    const titleInput = document.querySelector('input[name="title"]');
    const descriptionInput = document.querySelector('textarea[name="description"]');
    const imageInput = document.getElementById('imageInput');
    
    const title = titleInput ? titleInput.value.trim() : '';
    const description = descriptionInput ? descriptionInput.value.trim() : '';
    const images = imageInput ? imageInput.files : null;
    
    if (!title) {
        alert('Vui lòng nhập tiêu đề tin đăng');
        if (titleInput) titleInput.focus();
        return false;
    }
    
    if (!description) {
        alert('Vui lòng nhập mô tả chi tiết');
        if (descriptionInput) descriptionInput.focus();
        return false;
    }
    
    if (!images || images.length < 3) {
        alert('Vui lòng tải lên ít nhất 3 hình ảnh');
        return false;
    }
    
    return true;
}

function submitForm() {
    const form = document.getElementById('propertyListingForm');
    if (!form) return;
    
    // Add selected property data to form
    const selectedProperty = document.querySelector('input[name="selectedProperty"]:checked');
    const selectedPropertyId = selectedProperty ? selectedProperty.value : null;
    const selectedPropertyType = selectedProperty ? selectedProperty.getAttribute('data-type') : null;
    
    if (selectedPropertyId) {
        // Remove existing hidden inputs
        const existingPropertyIdInputs = form.querySelectorAll('input[name="property_id"]');
        existingPropertyIdInputs.forEach(input => input.remove());
        
        const existingPropertyTypeInputs = form.querySelectorAll('input[name="property_type"]');
        existingPropertyTypeInputs.forEach(input => input.remove());
        
        // Add new hidden inputs
        const propertyIdInput = document.createElement('input');
        propertyIdInput.type = 'hidden';
        propertyIdInput.name = 'property_id';
        propertyIdInput.value = selectedPropertyId;
        form.appendChild(propertyIdInput);
        
        if (selectedPropertyType) {
            const propertyTypeInput = document.createElement('input');
            propertyTypeInput.type = 'hidden';
            propertyTypeInput.name = 'property_type';
            propertyTypeInput.value = selectedPropertyType;
            form.appendChild(propertyTypeInput);
        }
    }
    
    // Submit the form
    form.submit();
}

// Additional event handlers
document.addEventListener('DOMContentLoaded', function() {
    console.log('Document ready - Setting up additional event handlers');
    
    // Ensure proper display of sections on page load
    const propertySelectionList = document.querySelector('.property-selection-list');
    const listingDetailsSection = document.querySelector('.listing-details-section');
    const packageSelectionSection = document.querySelector('.package-selection-section');
    
    // Remove d-none class and set display style
    if (propertySelectionList) {
        propertySelectionList.classList.remove('d-none');
        propertySelectionList.style.display = 'block';
    }
    
    if (listingDetailsSection) {
        listingDetailsSection.classList.add('d-none');
        listingDetailsSection.style.display = 'none';
    }
    
    if (packageSelectionSection) {
        packageSelectionSection.classList.add('d-none');
        packageSelectionSection.style.display = 'none';
    }
    
    // Ensure next button is disabled initially
    const nextButton = document.getElementById('nextButton');
    if (nextButton) {
        nextButton.disabled = false; // Set to false to allow clicking
    }
    
    // Ensure back button is hidden initially
    const backButton = document.getElementById('backButton');
    if (backButton) {
        backButton.classList.add('d-none');
    }
    
    // Handle property item clicks
    const propertyItems = document.querySelectorAll('.property-item');
    propertyItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            // Don't select if clicking on the radio button itself
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'LABEL') {
                return;
            }
            
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                
                // Update UI
                propertyItems.forEach(function(pi) {
                    pi.classList.remove('border-primary');
                });
                this.classList.add('border-primary');
                
                console.log('Selected property:', radio.value);
                
                // Enable next button
                const nextButton = document.getElementById('nextButton');
                if (nextButton) {
                    nextButton.disabled = false;
                }
            }
        });
    });
    
    // Handle radio button changes
    const propertyRadios = document.querySelectorAll('input[name="selectedProperty"]');
    propertyRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            // Update UI
            propertyItems.forEach(function(pi) {
                pi.classList.remove('border-primary');
            });
            
            let parentItem = this.closest('.property-item');
            if (parentItem) {
                parentItem.classList.add('border-primary');
            }
            
            console.log('Selected property (radio change):', this.value);
            
            // Enable next button
            const nextButton = document.getElementById('nextButton');
            if (nextButton) {
                nextButton.disabled = false;
            }
        });
    });
    
    // Xử lý tải lên hình ảnh đã được chuyển sang script riêng ở phần trên
    
    // Handle package selection
    const packageCards = document.querySelectorAll('.package-card');
    packageCards.forEach(function(card) {
        card.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
            
            packageCards.forEach(function(pc) {
                pc.classList.remove('border-primary');
                pc.classList.add('border-2');
            });
            
            this.classList.remove('border-2');
            this.classList.add('border-primary');
        });
    });
    
    // Reset modal when closed
    const modal = document.getElementById('createPropertyListingModal');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            // Reset state
            const sections = document.querySelectorAll('.property-selection-list, .listing-details-section, .package-selection-section');
            sections.forEach(function(section) {
                section.classList.add('d-none');
                section.style.display = 'none';
            });
            
            const propertySelectionList = document.querySelector('.property-selection-list');
            if (propertySelectionList) {
                propertySelectionList.classList.remove('d-none');
                propertySelectionList.style.display = 'block';
            }
            
            const nextButton = document.getElementById('nextButton');
            if (nextButton) {
                nextButton.textContent = 'Tiếp tục';
            }
            
            const backButton = document.getElementById('backButton');
            if (backButton) {
                backButton.classList.add('d-none');
            }
            
            updateStepUI(1);
        });
    }
});
</script>
            </div>
          </div>
        </div>
      </div>
  </div>
</form>

