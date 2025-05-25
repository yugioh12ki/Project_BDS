@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(empty($properties))
    <div class="alert alert-danger">
        <strong>Không có dữ liệu</strong>
    </div>
@else
<div class="property-table-header d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 class="mb-0">
            Danh sách BĐS
            <span class="badge bg-info" id="propertyCount">{{ count($properties) }}</span>
        </h6>
    </div>
    @if(isset($status) && $status == 'pending')
    <div class="batch-actions">
        <button class="btn btn-sm btn-outline-primary" id="selectAllBtn">
            <i class="fas fa-check-square"></i> Chọn tất cả
        </button>
        <button class="btn btn-sm btn-success batch-approve-btn" disabled>
            <i class="fas fa-check"></i> Duyệt <span class="selected-count">(0)</span>
        </button>
        <button class="btn btn-sm btn-warning batch-reject-btn" disabled>
            <i class="fas fa-times"></i> Từ chối <span class="selected-count">(0)</span>
        </button>
    </div>
    @endif
</div>

<div id="noPropertiesMessage" style="display: none;" class="alert alert-info">
    <i class="fas fa-info-circle"></i> Không tìm thấy bất động sản nào phù hợp với điều kiện lọc.
</div>

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                @if(isset($status) && $status == 'pending')
                <th class="checkbox-column">
                    <div class="form-check">
                        <input class="form-check-input select-all-checkbox" type="checkbox" id="selectAll">
                    </div>
                </th>
                @endif
                <th>ID</th>
                <th>Tiêu đề</th>
                <th class="d-none d-md-table-cell">Loại hình</th>
                <th class="d-none d-md-table-cell">Địa chỉ</th>
                <th class="d-none d-lg-table-cell">Ngày đăng</th>
                <th class="d-none d-lg-table-cell">Chủ sở hữu</th>
                <th class="d-none d-lg-table-cell">Media</th>
                <th class="actions-column">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($properties as $property)
                <tr id="property-row-{{ $property->PropertyID }}"
                    class="property-row {{ isset($status) && $status == 'pending' ? 'pending-property' : '' }}"
                    data-property-id="{{ $property->PropertyID }}"
                    {{-- data-lat="{{ $property->Latitude ?? '' }}"
                    data-lng="{{ $property->Longitude ?? '' }}" --}}
                    data-address="{{ $property->Address ?? '' }}"
                    data-ward="{{ $property->Ward ?? '' }}"
                    data-district="{{ $property->District ?? '' }}"
                    data-province="{{ $property->Province ?? '' }}"
                    data-full-address="{{ trim(implode(', ', array_filter([$property->Address, $property->Ward, $property->District, $property->Province]))) }}"
                    data-category="{{ optional($property->danhMuc)->CategoryID ?? '' }}"
                    data-price="{{ $property->Price ?? 0 }}"
                    data-date="{{ $property->PostedDate !== '0000-00-00' ? $property->PostedDate : '' }}">

                    @if(isset($status) && $status == 'pending')
                    <td class="checkbox-column" onclick="event.stopPropagation();">
                        <div class="form-check">
                            <input class="form-check-input property-checkbox" type="checkbox" data-property-id="{{ $property->PropertyID }}" onchange="handlePropertySelection(event, '{{ $property->PropertyID }}')">
                        </div>
                    </td>
                    @endif

                    <td class="clickable-cell">{{ $property->PropertyID }}</td>
                    <td class="clickable-cell">
                        <div class="property-title">{{ $property->Title }}</div>
                        <div class="mb-2">
                            @if($property->TypePro == 'Cho thuê')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-key"></i> Cho thuê
                                </span>
                            @elseif($property->TypePro == 'Cho bán')
                                <span class="badge bg-success">
                                    <i class="fas fa-home"></i> Cho bán
                                </span>
                            @endif
                        </div>
                        <div class="d-block d-md-none">
                            <small class="text-muted">{{ optional($property->danhMuc)->ten_pro ?? 'N/A' }} - {{ $property->Address }}</small>
                        </div>
                    </td>
                    <td class="d-none d-md-table-cell clickable-cell">{{ optional($property->danhMuc)->ten_pro ?? 'N/A' }}</td>
                    <td class="d-none d-md-table-cell clickable-cell"
                        style="cursor:pointer;color:#0d6efd;text-decoration:underline;"
                        onclick="selectPropertyOnMap('{{ $property->PropertyID }}', '{{ $property->Latitude ?? '' }}', '{{ $property->Longitude ?? '' }}', '{{ trim(implode(', ', array_filter([$property->Address, $property->Ward, $property->District, $property->Province]))) }}')">
                        {{ trim(implode(', ', array_filter([$property->Address, $property->Ward, $property->District, $property->Province]))) }}
                    </td>
                    <td class="d-none d-lg-table-cell clickable-cell">{{ $property->PostedDate === '0000-00-00' ? 'N/A' : date('d/m/Y', strtotime($property->PostedDate)) }}</td>
                    <td class="d-none d-lg-table-cell clickable-cell">{{ optional($property->chusohuu)->Name ?? 'N/A' }}</td>
                    <td class="d-none d-lg-table-cell clickable-cell">
                        <span class="badge bg-primary" title="Số lượng hình ảnh">
                            <i class="fas fa-image"></i> {{ count($property->images) }}
                        </span>
                        <span class="badge bg-info" title="Số lượng video">
                            <i class="fas fa-video"></i> {{ count($property->videos) }}
                        </span>
                    </td>
                    <td onclick="event.stopPropagation();">
                        <div class="action-buttons-container">
                            <!-- View Button (Always visible and prominent) -->
                            <button type="button"
                                    class="btn btn-primary btn-action view-btn"
                                    onclick="showPropertyNotification('{{ $property->PropertyID }}')"
                                    title="Xem thông tin chi tiết">
                                <i class="fas fa-eye"></i>
                                <span class="btn-text">Xem</span>
                            </button>

                            @if(isset($status) && $status == 'pending')
                            <!-- Approval Actions for pending properties -->
                            <div class="approval-actions">
                                <button type="button"
                                        class="btn btn-success btn-action approve-btn"
                                        data-property-id="{{ $property->PropertyID }}"
                                        onclick="approveProperty('{{ $property->PropertyID }}')"
                                        title="Duyệt bất động sản">
                                    <i class="fas fa-check"></i>
                                    <span class="btn-text">Duyệt</span>
                                </button>
                                <button type="button"
                                        class="btn btn-danger btn-action reject-btn"
                                        data-property-id="{{ $property->PropertyID }}"
                                        onclick="rejectProperty('{{ $property->PropertyID }}')"
                                        title="Từ chối bất động sản">
                                    <i class="fas fa-times"></i>
                                    <span class="btn-text">Từ chối</span>
                                </button>
                            </div>
                            @endif

                            <!-- Additional actions dropdown for more options -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-action dropdown-toggle"
                                        type="button"
                                        id="dropdownMenuButton{{ $property->PropertyID }}"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        title="Thêm tùy chọn">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $property->PropertyID }}">
                                    <li>
                                        <a class="dropdown-item" href="/admin/property/{{ $property->PropertyID }}" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> Mở trang chi tiết
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#"
                                           onclick="selectPropertyOnMap('{{ $property->PropertyID }}', '{{ $property->Latitude ?? '' }}', '{{ $property->Longitude ?? '' }}', '{{ trim(implode(', ', array_filter([$property->Address, $property->Ward, $property->District, $property->Province]))) }}')">
                                            <i class="fas fa-map-marker-alt"></i> Hiển thị trên bản đồ
                                        </a>
                                    </li>
                                    @if(isset($status) && $status !== 'pending')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-warning" href="#" onclick="editProperty('{{ $property->PropertyID }}')">
                                            <i class="fas fa-edit"></i> Chỉnh sửa
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- TẤT CẢ CÁC MODALS ĐẶT Ở ĐÂY ĐỂ TRÁNH HIỂN THỊ TRONG BẢNG --}}

{{-- Include Property Notification Styles --}}
<link rel="stylesheet" href="{{ asset('css/property-notification.css') }}">

{{-- Enhanced Action Buttons Styles --}}
<style>
/* Media gallery in notification */
.media-gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 10px;
}

.media-item {
    position: relative;
    overflow: hidden;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.media-item img:hover {
    transform: scale(1.02);
    transition: transform 0.2s ease;
}

.video-container {
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.image-caption, .video-caption {
    max-width: 120px;
    word-wrap: break-word;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.upload-date {
    max-width: 120px;
    word-wrap: break-word;
}

.empty-media-message {
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
    text-align: center;
    color: #6c757d;
    font-size: 0.8rem;
}

.action-buttons-container {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: center;
    min-width: 180px;
}

.btn-action {
    font-size: 0.8rem;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 500;
    transition: all 0.2s ease;
    position: relative;
    white-space: nowrap;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-action .btn-text {
    margin-left: 4px;
}

/* View button - most prominent */
.view-btn {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    color: white;
    min-width: 70px;
}

.view-btn:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    color: white;
}

/* Approval actions */
.approval-actions {
    display: flex;
    gap: 3px;
}

.approve-btn {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    border: none;
    color: white;
    min-width: 65px;
}

.approve-btn:hover {
    background: linear-gradient(135deg, #1e7e34, #155724);
    color: white;
}

.reject-btn {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
    color: white;
    min-width: 75px;
}

.reject-btn:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    color: white;
}

/* Dropdown button */
.dropdown .btn-action {
    min-width: 35px;
    padding: 4px 6px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .action-buttons-container {
        flex-direction: column;
        gap: 2px;
        min-width: 100px;
    }

    .approval-actions {
        width: 100%;
        justify-content: space-between;
    }

    .btn-action {
        width: 100%;
        justify-content: center;
    }

    .btn-text {
        display: inline !important;
    }
}

@media (max-width: 576px) {
    .btn-text {
        display: none;
    }

    .btn-action {
        min-width: 32px;
        padding: 4px;
    }
}

/* Table improvements */
.table th:last-child,
.table td:last-child {
    min-width: 200px;
    width: 200px;
}

.actions-column {
    text-align: center;
    font-weight: 600;
    color: #495057;
}

.property-table-card .table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

.property-table-card .table {
    margin-bottom: 0;
}

.property-table-card .table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}

.property-row:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.property-row:hover .action-buttons-container {
    opacity: 1;
}

.action-buttons-container {
    opacity: 0.8;
    transition: opacity 0.2s ease;
}

/* Badge enhancements */
.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

/* Enhanced card styling */
.property-details-card,
.map-card,
.property-table-card {
    border: 1px solid #e3e6ea;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}

.property-details-card .card-header,
.map-card .card-header,
.property-table-card .card-header {
    background: linear-gradient(135deg, #0d6efd, #0056b3);
    border-bottom: 1px solid #dee2e6;
    color: white;
}

.property-details-card .card-header h6,
.map-card .card-header h6,
.property-table-card .card-header h6 {
    color: white;
    margin: 0;
    font-weight: 600;
}

/* Map container improvements */
.map-card .card-body {
    padding: 1rem;
}

#googleMap {
    height: 400px;
    min-height: 300px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

/* Property details card improvements */
.property-details-card .card-body {
    /* Remove max-height and overflow to show full content */
    padding: 1rem;
    /* Remove any height restrictions */
    max-height: none !important;
    overflow: visible !important;
}

#property-notifications-area {
    min-height: 200px;
    /* Allow content to expand naturally */
    height: auto !important;
    max-height: none !important;
    overflow: visible !important;
}
</style>

@foreach ($properties as $property)
<!-- Property data for notifications -->
<script>
window.propertyData = window.propertyData || {};
window.propertyData['{{ $property->PropertyID }}'] = {
    PropertyID: '{{ $property->PropertyID }}',
    Title: {!! json_encode($property->Title ?? "") !!},
    OwnerName: {!! json_encode(optional($property->chusohuu)->Name ?? "Không có") !!},
    OwnerPhone: {!! json_encode(optional($property->chusohuu)->Phone ?? "Không có") !!},
    OwnerEmail: {!! json_encode(optional($property->chusohuu)->Email ?? "Không có") !!},
    Address: {!! json_encode($property->Address ?? "") !!},
    District: {!! json_encode($property->District ?? "") !!},
    Province: {!! json_encode($property->Province ?? "") !!},
    Ward: {!! json_encode($property->Ward ?? "") !!},
    Price: {!! json_encode(number_format($property->Price ?? 0, 0, ",", ".") . " VND") !!},
    PostDate: {!! json_encode($property->PostDate ?? "N/A") !!},
    Status: {!! json_encode($property->Status ?? "") !!},
    Description: {!! json_encode($property->Description ?? "") !!},
    ImageCount: '{{ count($property->images) }}',
    VideoCount: '{{ count($property->videos) }}',
    Category: {!! json_encode(optional($property->danhmuc)->CategoryName ?? "Không có") !!},
    TypePro: {!! json_encode($property->TypePro ?? "") !!},
    Area: {!! json_encode($property->Area ?? "N/A") !!},
    Bedrooms: {!! json_encode(optional($property->chiTiet)->Bedrooms ?? "N/A") !!},
    Bathrooms: {!! json_encode(optional($property->chiTiet)->Bathrooms ?? "N/A") !!},
    Floors: {!! json_encode(optional($property->chiTiet)->Floors ?? "N/A") !!},
    Legal: {!! json_encode(optional($property->chiTiet)->legal ?? "N/A") !!},
    Direction: {!! json_encode(optional($property->chiTiet)->view ?? "N/A") !!},
    AgentName: {!! json_encode(optional($property->moigioi)->Name ?? "Chưa phân công") !!},
    // DetailProperty fields
    Levelhouse: {!! json_encode(optional($property->chiTiet)->Levelhouse ?? null) !!},
    Floor: {!! json_encode(optional($property->chiTiet)->Floor ?? null) !!},
    HouseLength: {!! json_encode(optional($property->chiTiet)->HouseLength ?? null) !!},
    HouseWidth: {!! json_encode(optional($property->chiTiet)->HouseWidth ?? null) !!},
    TotalLength: {!! json_encode(optional($property->chiTiet)->TotalLength ?? null) !!},
    TotalWidth: {!! json_encode(optional($property->chiTiet)->TotalWidth ?? null) !!},
    Bedroom: {!! json_encode(optional($property->chiTiet)->Bedroom ?? null) !!},
    Balcony: {!! json_encode(optional($property->chiTiet)->Balcony ?? null) !!},
    Bath_WC: {!! json_encode(optional($property->chiTiet)->Bath_WC ?? null) !!},
    Road: {!! json_encode(optional($property->chiTiet)->Road ?? null) !!},
    Near: {!! json_encode(optional($property->chiTiet)->near ?? null) !!},
    Interior: {!! json_encode(optional($property->chiTiet)->Interior ?? null) !!},
    WaterPrice: {!! json_encode(optional($property->chiTiet)->WaterPrice ?? null) !!},
    PowerPrice: {!! json_encode(optional($property->chiTiet)->PowerPrice ?? null) !!},
    Utilities: {!! json_encode(optional($property->chiTiet)->Utilities ?? null) !!},
    Images: [
        @if(isset($property->images) && count($property->images) > 0)
            @foreach($property->images as $img)
                @php
                    $imagePath = $img->ImagePath;
                    $imagePath = preg_replace('/^(\\\\)?public(\\\\|\/)/', '', $imagePath);
                    $imagePath = str_replace('\\', '/', $imagePath);
                @endphp
                {
                    id: '{{ $img->ImageID ?? '' }}',
                    path: '{{ asset('storage/' . $imagePath) }}',
                    caption: {!! json_encode($img->Caption ?? '') !!},
                    uploadDate: '{{ $img->created_at ? $img->created_at->format("d/m/Y") : "" }}'
                },
            @endforeach
        @endif
    ],
    Videos: [
        @if(isset($property->videos) && count($property->videos) > 0)
            @foreach($property->videos as $video)
                @php
                    $videoPath = $video->VideoPath;
                    $videoPath = preg_replace('/^(\\\\)?public(\\\\|\/)/', '', $videoPath);
                    $videoPath = str_replace('\\', '/', $videoPath);
                    $isYoutube = strpos($videoPath, 'youtube.com') !== false || strpos($videoPath, 'youtu.be') !== false;
                    $videoId = '';
                    if ($isYoutube) {
                        if (strpos($videoPath, 'youtube.com/watch?v=') !== false) {
                            $videoId = substr($videoPath, strpos($videoPath, 'v=') + 2);
                            $videoId = explode('&', $videoId)[0];
                        } elseif (strpos($videoPath, 'youtu.be/') !== false) {
                            $videoId = substr($videoPath, strpos($videoPath, 'youtu.be/') + 9);
                        }
                    }
                @endphp
                {
                    id: '{{ $video->VideoID ?? '' }}',
                    path: '{{ $isYoutube ? $videoPath : asset('storage/' . $videoPath) }}',
                    isYoutube: {{ $isYoutube ? 'true' : 'false' }},
                    youtubeId: '{{ $videoId }}',
                    caption: {!! json_encode($video->Caption ?? '') !!},
                    uploadDate: '{{ $video->created_at ? $video->created_at->format("d/m/Y") : "" }}'
                },
            @endforeach
        @endif
    ]
};
</script>
@endforeach

<script>
// Global functions for property approval and rejection
function approveProperty(propertyId) {
    if (confirm('Bạn có chắc chắn muốn duyệt bất động sản này không?')) {
        PropertyManagement.updatePropertyStatus(propertyId, 'approved');
    }
}

function rejectProperty(propertyId) {
    if (confirm('Bạn có chắc chắn muốn từ chối bất động sản này không?')) {
        PropertyManagement.updatePropertyStatus(propertyId, 'rejected');
    }
}

// Function to view image in modal
function viewImage(imageSrc) {
    // Remove existing image modal if any
    const existingModal = document.getElementById('imageViewModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Create image modal
    const modal = document.createElement('div');
    modal.id = 'imageViewModal';
    modal.className = 'modal fade show';
    modal.style.cssText = 'display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.8); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1055;';

    modal.innerHTML = `
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background: transparent; border: none;">
                <div class="modal-header" style="border-bottom: none; background: rgba(0,0,0,0.5); color: white;">
                    <h5 class="modal-title">Xem hình ảnh</h5>
                    <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('imageViewModal').remove()"></button>
                </div>
                <div class="modal-body text-center" style="padding: 0;">
                    <img src="${imageSrc}" alt="Hình ảnh BĐS" style="max-width: 100%; max-height: 80vh; object-fit: contain;">
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Function to edit property
function editProperty(propertyId) {
    // Redirect to edit page or open edit modal
    window.open('/admin/property/' + propertyId + '/edit', '_blank');
}

// Function to show property notification instead of modal
function showPropertyNotification(propertyId) {
    console.log('showPropertyNotification called with ID:', propertyId);
    console.log('Available property data:', window.propertyData);

    const property = window.propertyData[propertyId];
    if (!property) {
        console.error('Property not found for ID:', propertyId);
        showNotification('error', 'Không tìm thấy thông tin bất động sản với ID: ' + propertyId);
        return;
    }

    console.log('Property data found:', property);

    // Create notification content với giao diện đơn giản
    const notificationContent = `
        <div class="card border-secondary">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-home"></i>
                    ${property.Title}
                    <span class="badge bg-secondary ms-2">#${property.PropertyID}</span>
                </h6>
                <div class="mt-1">
                    ${property.TypePro && property.TypePro.includes('thuê') ?
                        '<span class="badge bg-warning text-dark"><i class="fas fa-key"></i> Cho thuê</span>' :
                        property.TypePro && property.TypePro.includes('bán') ?
                        '<span class="badge bg-success"><i class="fas fa-home"></i> Cho bán</span>' :
                        '<span class="badge bg-secondary">Không xác định</span>'
                    }
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle"></i> Thông tin cơ bản</h6>
                        <p><strong>Loại:</strong> ${property.Category || 'Chưa phân loại'}</p>
                        <p><strong>Giá:</strong> <span class="text-success fw-bold">${property.Price || 'Liên hệ'}</span></p>
                        <p><strong>Trạng thái:</strong>
                            <span class="badge ${property.Status === 'approved' ? 'bg-success' : property.Status === 'pending' ? 'bg-warning' : 'bg-danger'}">
                                ${property.Status}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-map-marker-alt"></i> Vị trí</h6>
                        <p><strong>Địa chỉ:</strong> ${property.Address || 'Chưa có địa chỉ'}</p>
                        <p><strong>Quận/Huyện:</strong> ${property.District || 'Chưa xác định'}</p>
                        <p><strong>Tỉnh/TP:</strong> ${property.Province || 'Chưa xác định'}</p>
                    </div>
                </div>

                ${property.OwnerName && property.OwnerName !== 'Không có' ? `
                <div class="border-top pt-3 mt-3">
                    <h6><i class="fas fa-user"></i> Chủ sở hữu</h6>
                    <p><strong>Tên:</strong> ${property.OwnerName}</p>
                    ${property.OwnerEmail && property.OwnerEmail !== 'Không có' ? `<p><strong>Email:</strong> ${property.OwnerEmail}</p>` : ''}
                    ${property.OwnerPhone && property.OwnerPhone !== 'Không có' ? `<p><strong>SĐT:</strong> ${property.OwnerPhone}</p>` : ''}
                </div>
                ` : ''}

                <div class="border-top pt-3 mt-3">
                    <h6><i class="fas fa-home"></i> Chi tiết bất động sản</h6>
                    <div class="row">
                        ${property.Levelhouse ? `<div class="col-md-6 mb-2"><strong>Cấp nhà:</strong> ${property.Levelhouse}</div>` : ''}
                        ${property.Floor ? `<div class="col-md-6 mb-2"><strong>Số tầng:</strong> ${property.Floor}</div>` : ''}
                        ${property.HouseLength ? `<div class="col-md-6 mb-2"><strong>Chiều dài nhà:</strong> ${property.HouseLength}m</div>` : ''}
                        ${property.HouseWidth ? `<div class="col-md-6 mb-2"><strong>Chiều rộng nhà:</strong> ${property.HouseWidth}m</div>` : ''}
                        ${property.TotalLength ? `<div class="col-md-6 mb-2"><strong>Chiều dài tổng:</strong> ${property.TotalLength}m</div>` : ''}
                        ${property.TotalWidth ? `<div class="col-md-6 mb-2"><strong>Chiều rộng tổng:</strong> ${property.TotalWidth}m</div>` : ''}
                        ${property.Bedroom ? `<div class="col-md-6 mb-2"><strong>Phòng ngủ:</strong> ${property.Bedroom}</div>` : ''}
                        ${property.Balcony !== null ? `<div class="col-md-6 mb-2"><strong>Ban công:</strong> ${property.Balcony == 1 ? 'Có' : 'Không'}</div>` : ''}
                        ${property.Bath_WC ? `<div class="col-md-6 mb-2"><strong>Phòng tắm/WC:</strong> ${property.Bath_WC}</div>` : ''}
                        ${property.Road ? `<div class="col-md-6 mb-2"><strong>Đường vào:</strong> ${property.Road}m</div>` : ''}
                        ${property.Legal ? `<div class="col-md-6 mb-2"><strong>Pháp lý:</strong> ${property.Legal}</div>` : ''}
                        ${property.Direction ? `<div class="col-md-6 mb-2"><strong>Hướng:</strong> ${property.Direction}</div>` : ''}
                        ${property.Near ? `<div class="col-md-6 mb-2"><strong>Gần:</strong> ${property.Near}</div>` : ''}
                        ${property.Interior ? `<div class="col-md-6 mb-2"><strong>Nội thất:</strong> ${property.Interior}</div>` : ''}
                        ${property.WaterPrice ? `<div class="col-md-6 mb-2"><strong>Giá nước:</strong> ${property.WaterPrice}</div>` : ''}
                        ${property.PowerPrice ? `<div class="col-md-6 mb-2"><strong>Giá điện:</strong> ${property.PowerPrice}</div>` : ''}
                        ${property.Utilities ? `<div class="col-md-6 mb-2"><strong>Tiện ích:</strong> ${property.Utilities}</div>` : ''}
                    </div>
                </div>

                <!-- Media Gallery Section -->
                <div class="border-top pt-3 mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-image"></i> Hình ảnh <span class="badge bg-primary">${property.ImageCount} ảnh</span></h6>
                            ${property.Images && property.Images.length > 0 ?
                                '<div class="media-gallery">' +
                                property.Images.map(img =>
                                    `<div class="media-item image-item">
                                        <img src="${img.path}" alt="Hình ảnh BĐS" style="width: 120px; height: 90px; object-fit: cover; border-radius: 4px; margin: 2px; cursor: pointer;" onclick="viewImage('${img.path}')">
                                        ${img.caption ? `<div class="image-caption" style="font-size: 0.7rem; color: #6c757d; margin-top: 2px;">${img.caption}</div>` : ''}
                                        ${img.uploadDate ? `<div class="upload-date" style="font-size: 0.6rem; color: #adb5bd;">${img.uploadDate}</div>` : ''}
                                    </div>`
                                ).join('') +
                                '</div>'
                                : '<div class="empty-media-message text-muted"><i class="fas fa-info-circle"></i> Không có hình ảnh</div>'
                            }
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-video"></i> Video <span class="badge bg-info">${property.VideoCount} video</span></h6>
                            ${property.Videos && property.Videos.length > 0 ?
                                '<div class="media-gallery">' +
                                property.Videos.map(video =>
                                    `<div class="video-container video-item" style="margin: 5px 0;">
                                        ${video.isYoutube ?
                                            `<iframe width="200" height="120" src="https://www.youtube.com/embed/${video.youtubeId}" frameborder="0" allowfullscreen style="border-radius: 4px;"></iframe>` :
                                            `<video src="${video.path}" controls style="width: 200px; max-height: 120px; border-radius: 4px;"></video>`
                                        }
                                        ${video.caption ? `<div class="video-caption" style="font-size: 0.7rem; color: #6c757d; margin-top: 2px;">${video.caption}</div>` : ''}
                                        ${video.uploadDate ? `<div class="upload-date" style="font-size: 0.6rem; color: #adb5bd;">${video.uploadDate}</div>` : ''}
                                    </div>`
                                ).join('') +
                                '</div>'
                                : '<div class="empty-media-message text-muted"><i class="fas fa-info-circle"></i> Không có video</div>'
                            }
                        </div>
                    </div>
                </div>

                ${property.Description && property.Description !== '' ? `
                <div class="border-top pt-3 mt-3">
                    <h6><i class="fas fa-align-left"></i> Mô tả</h6>
                    <div class="text-muted">
                        <p class="mb-0">${property.Description}</p>
                    </div>
                </div>` : ''}

                <div class="border-top pt-3 mt-3 d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="window.open('/admin/property/${property.PropertyID}', '_blank')">
                        <i class="fas fa-eye"></i> Xem chi tiết
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="clearPropertyNotifications()">
                        <i class="fas fa-times"></i> Đóng
                    </button>
                </div>
            </div>
        </div>
    `;

    // Create notification element
    createPropertyNotification(notificationContent);
}

// Function to create and display property notification
function createPropertyNotification(content) {
    const notificationsArea = document.getElementById('property-notifications-area');

    if (notificationsArea) {
        // Clear existing notifications
        const existingNotifications = notificationsArea.querySelectorAll('.card');
        existingNotifications.forEach(notification => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 200);
        });

        // Add new notification
        setTimeout(() => {
            notificationsArea.innerHTML = content;

            const newNotification = notificationsArea.querySelector('.card');
            if (newNotification) {
                newNotification.style.opacity = '0';
                setTimeout(() => {
                    newNotification.style.transition = 'opacity 0.3s ease-in';
                    newNotification.style.opacity = '1';
                }, 50);
            }
        }, 250);
    } else {
        // Fallback to floating notification if area not found
        createFloatingNotification(content);
    }
}

// Function to clear all property notifications
function clearPropertyNotifications() {
    const notificationsArea = document.getElementById('property-notifications-area');
    if (notificationsArea) {
        const notifications = notificationsArea.querySelectorAll('.card');
        notifications.forEach(notification => {
            notification.style.transition = 'all 0.3s ease-in';
            notification.style.opacity = '0';
        });

        setTimeout(() => {
            notificationsArea.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Nhấn vào nút "Xem" để hiển thị thông tin chi tiết bất động sản
                </div>
            `;
        }, 300);
    }
}

// Fallback function for floating notification
function createFloatingNotification(content) {
    // Remove existing notification if any
    const existingNotification = document.querySelector('.property-notification-toast');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'property-notification-toast alert alert-info alert-dismissible fade show';
    notification.innerHTML = `
        ${content}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()" aria-label="Close"></button>
    `;

    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        width: 450px;
        max-width: 90vw;
        z-index: 1055;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    `;

    // Add to body
    document.body.appendChild(notification);

    // Auto remove after 10 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }
    }, 10000);
}

// Helper function to get badge color
function getBadgeColor(status) {
    switch(status?.toLowerCase()) {
        case 'active': return 'success';
        case 'pending': return 'warning';
        case 'inactive': return 'secondary';
        case 'rejected': return 'danger';
        case 'approved': return 'success';
        default: return 'info';
    }
}

// Function để hiển thị thông báo (kết nối với hàm trong property.blade.php)
function showNotification(type, message) {
    // Nếu hàm đã tồn tại trong window, sử dụng nó
    if (window.showNotification) {
        window.showNotification(type, message);
    } else {
        // Tạo thông báo tạm thời nếu hàm chính chưa sẵn sàng
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : type === 'info' ? 'info' : 'danger'} alert-dismissible fade show notification-toast`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        document.body.appendChild(notification);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle select all checkbox
    const selectAllCheckbox = document.querySelector('.select-all-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('.property-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateSelectedCount();
        });
    }

    // Handle select all button
    const selectAllBtn = document.getElementById('selectAllBtn');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.property-checkbox');
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = !allChecked;
            }

            updateSelectedCount();
        });
    }

    // Handle individual checkboxes
    const propertyCheckboxes = document.querySelectorAll('.property-checkbox');
    propertyCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Update selected count
    function updateSelectedCount() {
        const checkedCount = document.querySelectorAll('.property-checkbox:checked').length;
        const countElements = document.querySelectorAll('.selected-count');

        countElements.forEach(element => {
            element.textContent = `(${checkedCount})`;
        });

        // Enable/disable batch action buttons
        const batchButtons = document.querySelectorAll('.batch-approve-btn, .batch-reject-btn');
        batchButtons.forEach(button => {
            button.disabled = checkedCount === 0;
        });
    }

    // Handle batch approve button
    const batchApproveBtn = document.querySelector('.batch-approve-btn');
    if (batchApproveBtn) {
        batchApproveBtn.addEventListener('click', function() {
            const selectedIds = getSelectedPropertyIds();
            if (selectedIds.length > 0 && confirm(`Bạn có chắc chắn muốn duyệt ${selectedIds.length} bất động sản này không?`)) {
                PropertyManagement.updateBatchPropertyStatus(selectedIds, 'approved');
            }
        });
    }

    // Handle batch reject button
    const batchRejectBtn = document.querySelector('.batch-reject-btn');
    if (batchRejectBtn) {
        batchRejectBtn.addEventListener('click', function() {
            const selectedIds = getSelectedPropertyIds();
            if (selectedIds.length > 0 && confirm(`Bạn có chắc chắn muốn từ chối ${selectedIds.length} bất động sản này không?`)) {
                PropertyManagement.updateBatchPropertyStatus(selectedIds, 'rejected');
            }
        });
    }

    // Get selected property IDs
    function getSelectedPropertyIds() {
        const checkboxes = document.querySelectorAll('.property-checkbox:checked');
        return Array.from(checkboxes).map(checkbox => checkbox.getAttribute('data-property-id'));
    }

    /* Moved the individual approval and rejection functions to global scope */

    // Gán sự kiện click cho các cell có class clickable-cell (trừ cell thao tác/checkbox)
    const clickableCells = document.querySelectorAll('.clickable-cell');
    clickableCells.forEach(cell => {
        cell.addEventListener('click', function(e) {
            // Lấy dòng chứa cell này
            const row = cell.closest('.property-row');
            if (!row) return;
            const propertyId = row.getAttribute('data-property-id');
            const lat = row.getAttribute('data-lat');
            const lng = row.getAttribute('data-lng');
            const fullAddress = row.getAttribute('data-full-address');
            // Highlight dòng
            document.querySelectorAll('.property-row').forEach(r => r.classList.remove('highlighted-row'));
            row.classList.add('highlighted-row');
            // Gọi hàm bản đồ
            if (typeof selectPropertyOnMap === 'function') {
                selectPropertyOnMap(propertyId, lat, lng, fullAddress);
            }
        });
    });

    // Xử lý khi click vào hàng để hiển thị bất động sản trên bản đồ
    const propertyRows = document.querySelectorAll('.property-row');
    propertyRows.forEach(row => {
        row.addEventListener('click', function() {
            const propertyId = this.getAttribute('data-property-id');
            const lat = this.getAttribute('data-lat');
            const lng = this.getAttribute('data-lng');
            const fullAddress = this.getAttribute('data-full-address');

            // Thêm highlight cho hàng được chọn
            document.querySelectorAll('.property-row').forEach(r => r.classList.remove('highlighted-row'));
            this.classList.add('highlighted-row');

            // Gọi hàm selectPropertyOnMap để hiển thị trên bản đồ
            if (typeof selectPropertyOnMap === 'function') {
                selectPropertyOnMap(propertyId, lat, lng, fullAddress);
            } else {
                console.error('selectPropertyOnMap function is not defined');
            }
        });
    });

    // Xử lý khi checkbox được chọn
    function handlePropertySelection(event, propertyId) {
        // Ngăn sự kiện click không lan ra hàng
        event.stopPropagation();

        // Nếu checkbox được chọn, hiển thị bất động sản trên bản đồ
        if (event.target.checked) {
            const row = document.getElementById('property-row-' + propertyId);
            if (row) {
                const lat = row.getAttribute('data-lat');
                const lng = row.getAttribute('data-lng');
                const fullAddress = row.getAttribute('data-full-address');

                // Thêm highlight cho hàng được chọn
                document.querySelectorAll('.property-row').forEach(r => r.classList.remove('highlighted-row'));
                row.classList.add('highlighted-row');

                if (typeof selectPropertyOnMap === 'function') {
                    // Hiển thị thông báo đang tìm kiếm nếu không có tọa độ
                    if ((!lat || lat === '') && (!lng || lng === '') && fullAddress) {
                        showNotification('info', 'Đang tìm kiếm vị trí của bất động sản...');
                    }

                    selectPropertyOnMap(propertyId, lat, lng, fullAddress);
                }
            }
        }
    }

    // Update batch property status
    function updateBatchPropertyStatus(propertyIds, status, reason = null) {
        // Add loading state to selected rows
        propertyIds.forEach(id => {
            const row = document.getElementById(`property-row-${id}`);
            if (row) {
                row.classList.add('updating');
            }
        });

        // Create form data
        const formData = new FormData();
        formData.append('property_ids', JSON.stringify(propertyIds));
        formData.append('status', status);
        if (reason) {
            formData.append('reason', reason);
        }
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Send AJAX request
        fetch('/admin/property/update-batch-status', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove rows with animation
                propertyIds.forEach(id => {
                    const row = document.getElementById(`property-row-${id}`);
                    if (row) {
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(100px)';
                        setTimeout(() => {
                            row.remove();
                        }, 500);
                    }
                });

                // Update property count
                const countElement = document.getElementById('propertyCount');
                if (countElement) {
                    const currentCount = parseInt(countElement.textContent);
                    countElement.textContent = currentCount - propertyIds.length;
                }

                // Update menu badge counts
                PropertyManagement.updatePendingCountInMenu();

                // Show success notification
                PropertyManagement.showNotification('success', data.message || `Đã cập nhật trạng thái cho ${propertyIds.length} bất động sản`);
            } else {
                PropertyManagement.showNotification('error', data.message || 'Đã xảy ra lỗi khi cập nhật trạng thái');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            PropertyManagement.showNotification('error', 'Đã xảy ra lỗi khi cập nhật trạng thái');
        });
    }
});
</script>

<style>
.property-notification-container {
    font-size: 0.9rem;
}

.property-notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dee2e6;
}

.property-notification-header h5 {
    margin: 0;
    color: #0d6efd;
    font-size: 1.1rem;
    flex: 1;
}

.property-notification-body p {
    margin-bottom: 8px;
    line-height: 1.4;
    font-size: 0.85rem;
}

.property-notification-body p strong {
    color: #495057;
    font-weight: 600;
}

.property-notification-body h6 {
    font-size: 0.9rem;
    font-weight: bold;
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #e9ecef;
}

.property-notification-card .card-body {
    max-height: 600px;
    overflow-y: auto;
    padding: 1rem;
}

#property-notifications-area {
    min-height: 200px;
}

.alert-light {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    font-size: 0.85rem;
}

/* Icon spacing */
.property-notification-body i {
    width: 16px;
    margin-right: 5px;
}

.property-notification-header i {
    margin-right: 8px;
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .additional-content-column {
        margin-top: 20px;
    }

    .property-notification-body .col-md-6 {
        margin-bottom: 15px;
    }
}

@media (max-width: 576px) {
    .property-notification-body p {
        font-size: 0.8rem;
    }

    .property-notification-body h6 {
        font-size: 0.85rem;
    }
}
</style>
@endif
