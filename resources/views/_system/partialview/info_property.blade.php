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
      <label class="form-label">Người môi giới</label>
      <input type="text" class="form-control" value="{{ $property->moigioi->Name ?? ' ' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Người duyệt</label>
      <input type="text" class="form-control" value="{{ $property->quantri->Name ?? '' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Ngày duyệt</label>
      <input type="text" class="form-control" value="{{ $property->ApprovedDate  ?? '' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Địa chỉ</label>
      <input type="text" class="form-control" value="{{ $property->Address ?? '' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Phường</label>
      <input type="text" class="form-control" value="{{ $property->Province ?? '' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Quận</label>
      <input type="text" class="form-control" value="{{ $property->Ward ?? '' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Tỉnh</label>
      <input type="text" class="form-control" value="{{ $property->Province ?? '' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Loại BĐS</label>
      <input type="text" class="form-control" value="{{ $property->TypePro ?? '' }}" readonly>
    </div>
    <div class="mb-2">
      <label class="form-label">Giá</label>
      <input type="text" class="form-control" value="{{ $property->Price ?? '' }}" readonly>
    </div>
  </div>
  <!-- Tab 2: Thông tin chi tiết BĐS -->
  <div class="tab-pane fade" id="detail{{ $modalId }}" role="tabpanel">
    <div class="row mb-2">
      <div class="col-md-12">
        <label class="form-label">Tiêu đề</label>
        <input type="text" class="form-control" value="{{ $property->Title ?? '' }}" readonly>
      </div>
    </div>
    <div class="row mb-2">
      <div class="col-md-12">
        <label class="form-label">Mô tả</label>
        <textarea class="form-control" rows="2" readonly>{{ $property->Description ?? '' }}</textarea>
      </div>
    </div>
    <div class="row mb-2">
      <div class="col-md-6">
        <label class="form-label">Dạng</label>
        <input type="text" class="form-control" value="{{ $property->TypePro ?? '' }}" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Tầng</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Floor ?? '' }}" readonly>
      </div>
    </div>
    <div class="row mb-2">
      <div class="col-md-6">
        <label class="form-label">Diện tích</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Area ?? '' }}" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Phòng ngủ</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Bedroom ?? '' }}" readonly>
      </div>
    </div>
    <div class="row mb-2">
      <div class="col-md-6">
        <label class="form-label">Phòng WC/tắm</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Bath_WC ?? '' }}" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Con đường vào</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Road ?? '' }}" readonly>
      </div>
    </div>
    <div class="row mb-2">
      <div class="col-md-6">
        <label class="form-label">Sở hữu pháp lý</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Legal ?? '' }}" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Hướng</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->View ?? '' }}" readonly>
      </div>
    </div>
    <div class="row mb-2">
      <div class="col-md-6">
        <label class="form-label">Gần</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Near ?? '' }}" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Nội thất</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->Interior ?? '' }}" readonly>
      </div>
    </div>
    @if(isset($property->TypePro) && strtolower($property->TypePro) == 'rent')
    <div class="row mb-2">
      <div class="col-md-4">
        <label class="form-label">Giá điện</label>
        <input type="text" class="form-control" value="{{ $property->chiTiet->PowerPrice ?? '' }}" readonly>
      </div>
      <div class="col-md-4">
        <label class="form-label">Giá nước</label>
        <input type="text" class="form-control" value="{{ $property->GiaNuoc->WaterPrice ?? '' }}" readonly>
      </div>
      <div class="col-md-4">
        <label class="form-label">Tiện ích</label>
        <input type="text" class="form-control" value="{{ $property->TienIch->Utilities ?? '' }}" readonly>
      </div>
    </div>
    @endif
  </div>
  <!-- Tab 3: Hình ảnh/Video -->
  <div class="tab-pane fade" id="media{{ $modalId }}" role="tabpanel">
    <div class="mb-2">
      <label class="form-label">Hình ảnh</label>
      <div class="d-flex flex-wrap gap-2">
        @if(isset($property->images) && count($property->images))
          @foreach($property->images as $img)
            <img src="{{ $img->url }}" alt="Hình ảnh BĐS" class="img-thumbnail" style="max-width: 150px;">
          @endforeach
        @else
          <span>Không có hình ảnh</span>
        @endif
      </div>
    </div>
    <div class="mb-2">
      <label class="form-label">Video</label>
      <div>
        @if(isset($property->videos) && count($property->videos))
          @foreach($property->videos as $video)
            <video src="{{ $video->url }}" controls style="max-width: 300px;"></video>
          @endforeach
        @else
          <span>Không có video</span>
        @endif
      </div>
    </div>
  </div>
</div>
