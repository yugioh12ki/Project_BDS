<ul class="nav nav-tabs" id="propertyTab{{ $modalId }}" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="basic-tab{{ $modalId }}" data-bs-toggle="tab" data-bs-target="#basic{{ $modalId }}" type="button" role="tab">Thông tin cơ bản BĐS</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="detail-tab{{ $modalId }}" data-bs-toggle="tab" data-bs-target="#detail{{ $modalId }}" type="button" role="tab">Thông tin chi tiết BĐS</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="media-tab{{ $modalId }}" data-bs-toggle="tab" data-bs-target="#media{{ $modalId }}" type="button" role="tab">Hình ảnh/Video</button>
  </li>
</ul>
<div class="tab-content mt-3" id="propertyTabContent{{ $modalId }}">
  <!-- Tab 1: Thông tin cơ bản BĐS -->
  <div class="tab-pane fade show active" id="basic{{ $modalId }}" role="tabpanel">
    <div class="mb-2">
      <label class="form-label">Người đăng</label>
      <input type="text" class="form-control" value="{{ $property->chusohuu->Name ?? ' ' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Ngày đăng</label>
      <input type="text" class="form-control" value="{{ $property->PostDate  ?? 'NaN' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Địa chỉ</label>
      <input type="text" class="form-control" value="{{ $property->Address ?? '' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Phường</label>
      <input type="text" class="form-control" value="{{ $property->Ward ?? '' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Quận</label>
      <input type="text" class="form-control" value="{{ $property->District ?? '' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Tỉnh</label>
      <input type="text" class="form-control" value="{{ $property->Province ?? '' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Loại BĐS</label>
      <input type="text" class="form-control" value="{{ $property->TypePro == 'Rent' ? 'Cho Thuê' : ($property->TypePro == 'Sale' ? 'Mua Bán' : '') }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Giá tiền (VND)</label>
      <div class="input-group">
        <input type="text" class="form-control" value="{{ number_format($property->Price ?? 0, 0, ',', '.') }}" readonly>
        <span class="input-group-text">VND</span>
      </div>
    </div>

  </div>
  <!-- Tab 2: Thông tin chi tiết BĐS -->
  <div class="tab-pane fade" id="detail{{ $modalId }}" role="tabpanel">
    @if(!empty($property->Title))
    <div class="row mb-2">
      <div class="col-md-12">
        <label class="form-label">Tiêu đề</label>
        <input type="text" class="form-control" value="{{ $property->Title ?? '' }}" readonly>
      </div>
    </div>
    @endif

    @if(!empty($property->Description))
    <div class="row mb-2">
      <div class="col-md-12">
        <label class="form-label">Mô tả</label>
        <textarea class="form-control" rows="2" readonly>{{ $property->Description ?? '' }}</textarea>
      </div>
    </div>
    @endif

    <div class="row mb-2">
      @if(!empty($property->DanhMuc->ten_pro))
      <div class="col-md-6">
        <label class="form-label">Loại hình</label>
        <input type="text" class="form-control" value="{{ $property->DanhMuc->ten_pro ?? '' }}" readonly>
      </div>
      @endif

      @if(!empty($property->chiTiet->Levelhouse))
      <div class="col-md-{{ !empty($property->DanhMuc->ten_pro) ? '6' : '12' }}">
        <label class="form-label">Nhà Cấp</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Levelhouse ?? '' }}" readonly>
      </div>
      @endif
    </div>

    <div class="row mb-2">
      @if(!empty($property->chiTiet->Floor))
      <div class="col-md-6">
        <label class="form-label">Số Tầng</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Floor ?? '' }}" readonly>
      </div>
      @endif

      @if(isset($property->chiTiet->Balcony))
      <div class="col-md-{{ !empty($property->chiTiet->Floor) ? '6' : '12' }}">
        <label class="form-label">Ban công</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Balcony == 1 ? 'Có' : 'Không' ?? '' }}" readonly>
      </div>
      @endif
    </div>

    <div class="row mb-2">
      @if(!empty($property->chiTiet->HouseLength))
      <div class="col-md-6">
        <label class="form-label">Chiều dài nhà</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->HouseLength ?? '' }}" readonly>
      </div>
      @endif

      @if(!empty($property->chiTiet->HouseWidth))
      <div class="col-md-{{ !empty($property->chiTiet->HouseLength) ? '6' : '12' }}">
        <label class="form-label">Chiều rộng nhà</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->HouseWidth ?? '' }}" readonly>
      </div>
      @endif
    </div>

    <div class="row mb-2">
      @if(!empty($property->chiTiet->TotalLength))
      <div class="col-md-6">
        <label class="form-label">Chiều dài tổng thể</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->TotalLength ?? '' }}" readonly>
      </div>
      @endif

      @if(!empty($property->chiTiet->TotalWidth))
      <div class="col-md-{{ !empty($property->chiTiet->TotalLength) ? '6' : '12' }}">
        <label class="form-label">Chiều rộng tổng thể</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->TotalWidth ?? '' }}" readonly>
      </div>
      @endif
    </div>

    <div class="row mb-2">
      @if(!empty($property->chiTiet->Bedroom))
      <div class="col-md-6">
        <label class="form-label">Phòng ngủ</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Bedroom ?? '' }}" readonly>
      </div>
      @endif

      @if(!empty($property->chiTiet->Bath_WC))
      <div class="col-md-{{ !empty($property->chiTiet->Bedroom) ? '6' : '12' }}">
        <label class="form-label">Phòng WC/tắm</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Bath_WC ?? '' }}" readonly>
      </div>
      @endif
    </div>

    <div class="row mb-2">
      @if(!empty($property->chiTiet->Road))
      <div class="col-md-6">
        <label class="form-label">Con đường vào</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Road ?? '' }}" readonly>
      </div>
      @endif

      @if(!empty($property->chiTiet->legal))
      <div class="col-md-{{ !empty($property->chiTiet->Road) ? '6' : '12' }}">
        <label class="form-label">Sở hữu pháp lý</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->legal ?? '' }}" readonly>
      </div>
      @endif
    </div>

    <div class="row mb-2">
      @if(!empty($property->chiTiet->view))
      <div class="col-md-6">
        <label class="form-label">Hướng</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->view ?? '' }}" readonly>
      </div>
      @endif

      @if(!empty($property->chiTiet->near))
      <div class="col-md-{{ !empty($property->chiTiet->view) ? '6' : '12' }}">
        <label class="form-label">Gần</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->near ?? '' }}" readonly>
      </div>
      @endif
    </div>

    <div class="row mb-2">
      @if(!empty($property->chiTiet->Interior))
      <div class="col-md-12">
        <label class="form-label">Nội thất</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Interior ?? '' }}" readonly>
      </div>
      @endif
    </div>

    @if(isset($property->TypePro) && strtolower($property->TypePro) == 'rent')
    <div class="row mb-2">
      @if(!empty($property->chiTiet->PowerPrice))
      <div class="col-md-4">
        <label class="form-label">Giá điện</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->PowerPrice ?? '' }}" readonly>
      </div>
      @endif

      @if(!empty($property->chiTiet->WaterPrice))
      <div class="col-md-4">
        <label class="form-label">Giá nước</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->WaterPrice ?? '' }}" readonly>
      </div>
      @endif

      @if(!empty($property->chiTiet->Utilities))
      <div class="col-md-4">
        <label class="form-label">Tiện ích</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Utilities ?? '' }}" readonly>
      </div>
      @endif
    </div>
    @endif
  </div>
  <!-- Tab 3: Hình ảnh/Video -->
  <div class="tab-pane fade" id="media{{ $modalId }}" role="tabpanel">
    <link rel="stylesheet" href="{{ asset('css/property-media.css') }}">

    <div class="mb-3">
      <h6 class="form-label">
        <i class="fas fa-image"></i> Hình ảnh
        <span class="badge bg-primary">{{ count($property->images) }}</span>
      </h6>

      @if(isset($property->images) && count($property->images) > 0)
        <div class="media-gallery">
          @foreach($property->images as $img)
            @php
              // Xử lý đường dẫn hình ảnh
              $imagePath = $img->ImagePath;
              // Loại bỏ 'public/' hoặc '\public\' nếu có
              $imagePath = preg_replace('/^(\\\\)?public(\\\\|\/)/', '', $imagePath);
              // Thay thế dấu gạch chéo ngược bằng dấu gạch chéo
              $imagePath = str_replace('\\', '/', $imagePath);
            @endphp
            <div class="media-item image-item" data-image="{{ asset($imagePath) }}">
              <img src="{{ asset($imagePath) }}" alt="Hình ảnh BĐS" class="img-fluid">
              <div class="overlay">
                <button type="button" class="btn-view view-image" data-src="{{ asset($imagePath) }}">
                  <i class="fas fa-search-plus"></i>
                </button>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="empty-media-message">
          <i class="fas fa-info-circle"></i> Không có hình ảnh
        </div>
      @endif
    </div>

    <div class="mb-3">
      <h6 class="form-label">
        <i class="fas fa-video"></i> Video
        <span class="badge bg-info">{{ count($property->videos) }}</span>
      </h6>

      @if(isset($property->videos) && count($property->videos) > 0)
        <div class="media-gallery">
          @foreach($property->videos as $video)
            <div class="video-container video-item">
              @php
                // Xử lý đường dẫn video
                $videoPath = $video->VideoPath;
                // Loại bỏ 'public/' hoặc '\public\' nếu có
                $videoPath = preg_replace('/^(\\\\)?public(\\\\|\/)/', '', $videoPath);
                // Thay thế dấu gạch chéo ngược bằng dấu gạch chéo
                $videoPath = str_replace('\\', '/', $videoPath);
              @endphp

              @if(strpos($videoPath, 'youtube.com') !== false || strpos($videoPath, 'youtu.be') !== false)
                <!-- YouTube embed -->
                @php
                  $videoId = '';
                  if (strpos($videoPath, 'youtube.com/watch?v=') !== false) {
                    $videoId = substr($videoPath, strpos($videoPath, 'v=') + 2);
                    $videoId = explode('&', $videoId)[0];
                  } elseif (strpos($videoPath, 'youtu.be/') !== false) {
                    $videoId = substr($videoPath, strpos($videoPath, 'youtu.be/') + 9);
                  }
                @endphp
                <iframe width="100%" height="200" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
              @else
                <!-- Direct video file -->
                <video src="{{ asset($videoPath) }}" controls style="width: 100%; max-height: 200px;"></video>
              @endif
            </div>
          @endforeach
        </div>
      @else
        <div class="empty-media-message">
          <i class="fas fa-info-circle"></i> Không có video
        </div>
      @endif
    </div>
  </div>

  <!-- Image Viewer Modal -->
  <div class="modal fade image-viewer-modal" id="imageViewerModal{{ $modalId }}" tabindex="-1" aria-labelledby="imageViewerModalLabel{{ $modalId }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="imageViewerModalLabel{{ $modalId }}">Xem hình ảnh</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <img id="fullImage{{ $modalId }}" src="" class="img-fluid" alt="Hình ảnh lớn">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Đảm bảo Bootstrap đã được tải
      if (typeof bootstrap !== 'undefined') {
        // Lấy tất cả các nút xem ảnh trong tab này
        const viewButtons = document.querySelectorAll('#media{{ $modalId }} .view-image');
        
        // Lấy tham chiếu đến modal
        const modalElement = document.getElementById('imageViewerModal{{ $modalId }}');
        
        // Nếu tìm thấy modal, khởi tạo đối tượng Modal của Bootstrap
        if (modalElement) {
          const modal = new bootstrap.Modal(modalElement);
          
          // Thêm sự kiện click cho mỗi nút xem ảnh
          viewButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
              e.preventDefault();
              e.stopPropagation();
              
              // Lấy đường dẫn ảnh từ thuộc tính data-src
              const imageSrc = this.getAttribute('data-src');
              
              // Cập nhật src của ảnh trong modal
              const modalImage = document.getElementById('fullImage{{ $modalId }}');
              if (modalImage) {
                modalImage.src = imageSrc;
              }
              
              // Hiển thị modal
              modal.show();
            });
          });
          
          // Thêm sự kiện đóng modal khi nhấn nút đóng
          const closeButtons = modalElement.querySelectorAll('[data-bs-dismiss="modal"]');
          closeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
              modal.hide();
            });
          });
        }
      } else {
        console.error('Bootstrap không được tìm thấy. Vui lòng đảm bảo thư viện Bootstrap đã được tải.');
      }
    });
  </script>
</div>
