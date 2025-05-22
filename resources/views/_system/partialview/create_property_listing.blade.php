<!-- Create Property Listing Popup Modal -->
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
        <!-- Quy trình section -->
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

        <!-- Steps Navigation -->
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
        </div>

        <!-- Property Selection -->
        <h6 class="fw-bold mb-3">Chọn bất động sản để đăng tin</h6>

        <!-- Debug info -->
        @if(empty($ownerProperties))
        <div class="alert alert-warning">
          <i class="bi bi-exclamation-triangle me-2"></i>
          Biến ownerProperties không tồn tại hoặc rỗng. Vui lòng kiểm tra lại controller.
        </div>
        @endif

        <!-- Property List -->
        <div class="property-selection-list">
          @forelse($ownerProperties ?? [] as $property)
            <div class="property-item border rounded mb-3 overflow-hidden">
              <div class="property-content p-0">
                <!-- Property Image -->
                <div class="property-image bg-light text-center" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                  @php
                    $thumbnailImage = $property->images->where('IsThumbnail', 1)->first();
                    $firstImage = $property->images->first();
                    $imageUrl = $thumbnailImage ? $thumbnailImage->ImageURL : ($firstImage ? $firstImage->ImageURL : null);
                  @endphp
                  
                  @if($imageUrl)
                    <img src="{{ asset($imageUrl) }}" alt="{{ $property->Title }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                  @else
                    <svg class="text-secondary" width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                      <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                    </svg>
                  @endif
                </div>
                
                <!-- Property Details -->
                <div class="p-3">
                  <h5 class="mb-1 fw-bold">
                    @if($property->TypePro == 'Sale' || $property->TypePro == 'Sold')
                      Bán {{ $property->danhMuc ? $property->danhMuc->ten_pro : 'BĐS' }} {{ $property->District }}
                    @else
                      Cho thuê {{ $property->danhMuc ? $property->danhMuc->ten_pro : 'BĐS' }} {{ $property->District }}
                    @endif
                  </h5>
                  
                  <p class="text-muted mb-2">
                    <i class="bi bi-geo-alt me-1"></i> {{ $property->Address }}, {{ $property->Ward }}, {{ $property->District }}, {{ $property->Province }}
                  </p>
                  
                  <div class="d-flex align-items-center mb-2">
                    <span class="badge rounded-pill me-2" style="background-color: #eef2ff; color: #4f46e5;">
                      {{ $property->danhMuc ? $property->danhMuc->ten_pro : 'Bất động sản' }}
                    </span>
                    
                    <span class="badge rounded-pill {{ $property->TypePro == 'Sale' || $property->TypePro == 'Sold' ? 'bg-danger' : 'bg-primary' }}">
                      {{ $property->TypePro == 'Sale' || $property->TypePro == 'Sold' ? 'Đang bán' : 'Đang cho thuê' }}
                    </span>
                  </div>
                  
                  <!-- Property Features -->
                  <div class="d-flex justify-content-between text-center py-3 border-top border-bottom">
                    <div class="px-2">
                      <i class="bi bi-rulers d-block mb-1"></i>
                      <strong class="d-block">{{ $property->chiTiet->Area ?? 'N/A' }}</strong>
                      <small class="text-muted">m²</small>
                    </div>
                    <div class="px-2">
                      <i class="bi bi-door-open d-block mb-1"></i>
                      <strong class="d-block">{{ $property->chiTiet->Bedroom ?? '0' }}</strong>
                      <small class="text-muted">Phòng ngủ</small>
                    </div>
                    <div class="px-2">
                      <i class="bi bi-droplet d-block mb-1"></i>
                      <strong class="d-block">{{ $property->chiTiet->Bath_WC ?? '0' }}</strong>
                      <small class="text-muted">Phòng tắm</small>
                    </div>
                  </div>
                  
                  <!-- Price and Select Button -->
                  <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                      <h5 class="fw-bold text-primary mb-0">
                        @if($property->TypePro == 'Sale' || $property->TypePro == 'Sold')
                          {{ number_format($property->Price / 1000000000, 1) }} tỷ VND
                        @else
                          {{ number_format($property->Price / 1000000) }} triệu VND<span class="small">/tháng</span>
                        @endif
                      </h5>
                    </div>
                    
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="selectedProperty" id="property{{ $property->PropertyID }}" value="{{ $property->PropertyID }}" data-type="{{ $property->TypePro }}">
                      <label class="form-check-label" for="property{{ $property->PropertyID }}">
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
      </div>
      <div class="modal-footer justify-content-center border-0">
        <button type="button" class="btn btn-primary btn-lg rounded-pill px-5" id="continueToListingDetailsBtn">
          Tiếp tục
        </button>
      </div>
    </div>
  </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('sass/scss_owner/create_property_listing.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('resourcesjs/create_property_listing.js') }}"></script>
@endpush 