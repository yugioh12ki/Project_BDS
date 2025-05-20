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
                    </div>
                </div>
            </div>
            
            <!-- Chi tiết -->
            <div class="tab-pane fade" id="details-info-add" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-6">
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
                    
                    <div class="mb-3">
                        <label class="form-label">Chọn ảnh làm ảnh đại diện (thumbnail)</label>
                        <select class="form-select" name="thumbnail_index" id="thumbnail_index">
                            <option value="0">Hình đầu tiên</option>
                            <!-- Options will be added via JavaScript after images are selected -->
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="row" id="image_previews">
                            <!-- Image previews will be shown here -->
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6 class="mb-3">Video giới thiệu</h6>
                    
                    <div class="mb-3">
                        <label for="property_video" class="form-label">Tải lên video giới thiệu bất động sản</label>
                        <input class="form-control" type="file" id="property_video" name="property_video" accept="video/*">
                        <small class="text-muted">MP4, AVI, MOV tối đa 50MB</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="video_url" class="form-label">Hoặc nhập URL video (YouTube, Vimeo)</label>
                        <input type="text" class="form-control" id="video_url" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                    
                    <div id="video_preview" class="mt-3" style="display: none;">
                        <!-- Video preview will be shown here -->
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6 class="mb-3">Giấy tờ pháp lý</h6>
                    
                    <div class="mb-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="so_hong" name="legal_docs[]" value="Sổ hồng">
                            <label class="form-check-label" for="so_hong">Sổ hồng</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="so_do" name="legal_docs[]" value="Sổ đỏ">
                            <label class="form-check-label" for="so_do">Sổ đỏ</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="hop_dong_mua_ban" name="legal_docs[]" value="Hợp đồng mua bán">
                            <label class="form-check-label" for="hop_dong_mua_ban">Hợp đồng mua bán</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="giay_phep_xay_dung" name="legal_docs[]" value="Giấy phép xây dựng">
                            <label class="form-check-label" for="giay_phep_xay_dung">Giấy phép xây dựng</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="giay_phep_kinh_doanh" name="legal_docs[]" value="Giấy phép kinh doanh">
                            <label class="form-check-label" for="giay_phep_kinh_doanh">Giấy phép kinh doanh</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="legal_document_files" class="form-label">Tải lên giấy tờ pháp lý (PDF)</label>
                        <input class="form-control" type="file" id="legal_document_files" name="legal_document_files[]" multiple accept=".pdf,.doc,.docx">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col text-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success px-4">Lưu và gửi phê duyệt</button>
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

    // Quản lý hiển thị các trường giá điện/nước
    function toggleFields() {
        if (saleType.value === 'Sale') {
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
    
    // Xử lý xem trước hình ảnh
    const imageInput = document.getElementById('property_images');
    const imagePreviews = document.getElementById('image_previews');
    const thumbnailSelect = document.getElementById('thumbnail_index');
    
    imageInput.addEventListener('change', function() {
        imagePreviews.innerHTML = '';
        thumbnailSelect.innerHTML = '<option value="0">Hình đầu tiên</option>';
        
        if (this.files) {
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                const reader = new FileReader();
                
                // Tạo option cho dropdown chọn thumbnail
                const option = document.createElement('option');
                option.value = i;
                option.text = `Hình ${i + 1}`;
                thumbnailSelect.appendChild(option);
                
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 mb-3';
                    
                    const imageContainer = document.createElement('div');
                    imageContainer.className = 'position-relative';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.height = '150px';
                    img.style.objectFit = 'cover';
                    img.style.width = '100%';
                    
                    imageContainer.appendChild(img);
                    col.appendChild(imageContainer);
                    imagePreviews.appendChild(col);
                }
                
                reader.readAsDataURL(file);
            }
        }
    });
    
    // Xử lý xem trước video
    const videoInput = document.getElementById('property_video');
    const videoPreview = document.getElementById('video_preview');
    
    videoInput.addEventListener('change', function() {
        videoPreview.innerHTML = '';
        videoPreview.style.display = 'none';
        
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const videoURL = URL.createObjectURL(file);
            
            const video = document.createElement('video');
            video.controls = true;
            video.width = 320;
            video.height = 240;
            
            const source = document.createElement('source');
            source.src = videoURL;
            source.type = file.type;
            
            video.appendChild(source);
            videoPreview.appendChild(video);
            videoPreview.style.display = 'block';
        }
    });
    
    // Xử lý URL video
    const videoUrlInput = document.getElementById('video_url');
    
    videoUrlInput.addEventListener('change', function() {
        if (this.value) {
            // Xóa file video nếu đã chọn
            videoInput.value = '';
            videoPreview.innerHTML = '';
            
            // Hiển thị thông báo
            videoPreview.innerHTML = '<div class="alert alert-success">URL video đã được lưu và sẽ được xử lý khi gửi form.</div>';
            videoPreview.style.display = 'block';
        }
    });
});
</script>
