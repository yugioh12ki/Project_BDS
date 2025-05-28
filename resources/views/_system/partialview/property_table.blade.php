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
    <table class="table table-hover table-striped table-enhanced">
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
                    class="property-row property-row-enhanced {{ isset($status) && $status == 'pending' ? 'pending-property' : '' }}"
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

                            <!-- Additional action buttons -->
                            @if(isset($status) && $status !== 'pending')
                            <button type="button"
                                    class="btn btn-warning btn-action edit-btn"
                                    onclick="editProperty('{{ $property->PropertyID }}')"
                                    title="Chỉnh sửa bất động sản">
                                <i class="fas fa-edit"></i>
                                <span class="btn-text">Sửa</span>
                            </button>
                            @endif
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

/* Enhanced Property Info Tab Styles */
/* Property Info Card Styles */
.property-info-card {
    max-width: 600px;
    margin: 0 auto;
    border-radius: 12px !important;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.property-info-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white;
    padding: 1.25rem;
    position: relative;
}

.property-title-section h5 {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

.property-badges {
    margin-top: 0.75rem;
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.property-badges .badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.property-id-badge {
    background: rgba(255,255,255,0.2) !important;
    color: white !important;
    border: 1px solid rgba(255,255,255,0.3);
}

.badge-rent {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
    color: white !important;
    border: none;
}

.badge-sale {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
    color: white !important;
    border: none;
}

.badge-unknown {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%) !important;
    color: #8b4513 !important;
    border: none;
}

.badge-approved {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%) !important;
    color: #22c55e !important;
    border: none;
}

.badge-pending {
    background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%) !important;
    color: #f59e0b !important;
    border: none;
}

.badge-rejected {
    background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%) !important;
    color: #ef4444 !important;
    border: none;
}

/* Tab Navigation Styles */
.property-tabs-container {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.property-nav-tabs {
    border-bottom: none;
    padding: 0 1rem;
}

.property-nav-tabs .nav-item {
    margin-bottom: 0;
}

.property-nav-tabs .nav-link {
    border: none;
    background: transparent;
    color: #6c757d;
    font-weight: 600;
    font-size: 0.875rem;
    padding: 1rem 1.5rem;
    position: relative;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.property-nav-tabs .nav-link:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    color: #667eea;
    transform: translateY(-2px);
}

.property-nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
    border-radius: 25px 25px 0 0;
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.property-nav-tabs .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* User Avatar Styles */
.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.5rem;
    text-transform: uppercase;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    border: 3px solid white;
    position: relative;
    overflow: hidden;
}

.user-avatar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 50%, rgba(0,0,0,0.1) 100%);
    border-radius: 50%;
}

.user-avatar.avatar-default {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.user-avatar i {
    font-size: 1.5rem;
    z-index: 1;
    position: relative;
}

/* Info Card Styles */
.info-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.25rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

.info-card-title {
    color: #495057;
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
    display: flex;
    align-items: center;
}

.info-card-title i {
    color: #667eea;
    margin-right: 0.5rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
    padding: 0.5rem 0;
}

.info-item:not(:last-child) {
    border-bottom: 1px solid #f8f9fa;
}

.info-label {
    font-weight: 600;
    color: #495057;
    flex: 0 0 auto;
    margin-right: 1rem;
    display: flex;
    align-items: center;
}

.info-value {
    color: #6c757d;
    text-align: right;
    flex: 1;
    word-break: break-word;
}

/* Owner Details Styles */
.owner-details .info-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border: none;
    transition: all 0.3s ease;
}

.owner-details .info-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.owner-details .info-label {
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.owner-details .info-value {
    font-size: 1rem;
    color: #495057;
    font-weight: 500;
    text-align: left;
}

/* Media Gallery Enhanced Styles */
.media-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.media-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.media-item img:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

.video-container video,
.video-container iframe {
    width: 100%;
    height: 120px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.image-caption,
.video-caption {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.5rem;
    text-align: center;
    font-weight: 500;
}

.upload-date {
    font-size: 0.7rem;
    color: #adb5bd;
    text-align: center;
    margin-top: 0.25rem;
}

/* Tab Content Animation */
.tab-content .tab-pane {
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Card Footer Styles */
.card-footer {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border-top: 1px solid #dee2e6;
    padding: 1rem 1.25rem;
}

.card-footer h6 {
    color: #495057;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.card-footer .btn {
    border-radius: 25px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.card-footer .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Responsive Design */
@media (max-width: 768px) {
    .property-nav-tabs .nav-link {
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
    }

    .property-nav-tabs .nav-link i {
        display: none;
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }

    .info-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .info-label {
        margin-bottom: 0.25rem;
        margin-right: 0;
    }

    .info-value {
        text-align: left;
    }

    .media-gallery {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
    }
}

@media (max-width: 576px) {
    .property-info-card {
        margin: 0.5rem;
        max-width: none;
    }

    .property-nav-tabs {
        padding: 0 0.5rem;
    }

    .property-nav-tabs .nav-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
    }
}

/* Beautiful scrollbar for tab content */
.tab-content {
    scrollbar-width: thin;
    scrollbar-color: #667eea #f1f1f1;
}

.tab-content::-webkit-scrollbar {
    width: 6px;
}

.tab-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.tab-content::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 3px;
}

.tab-content::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
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
    OwnerAddress: {!! json_encode(optional($property->chusohuu)->Address ?? "Không có") !!},
    OwnerWard: {!! json_encode(optional($property->chusohuu)->Ward ?? "Không có") !!},
    OwnerDistrict: {!! json_encode(optional($property->chusohuu)->District ?? "Không có") !!},
    OwnerProvince: {!! json_encode(optional($property->chusohuu)->Province ?? "Không có") !!},
    OwnerCMND: {!! json_encode(optional($property->chusohuu)->IdentityCard ?? "Không có") !!},
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

// Function to create user avatar from initials
function createUserAvatar(name) {
    if (!name || name === 'Không có' || name === 'N/A') {
        return '<div class="user-avatar avatar-default"><i class="fas fa-user"></i></div>';
    }

    const words = name.trim().split(' ');
    const initials = words.length >= 2
        ? (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase()
        : words[0].charAt(0).toUpperCase();

    // Generate color based on name
    const colors = [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
        '#F7DC6F', '#BB8FCE', '#85C1E9', '#F8C471', '#82E0AA'
    ];
    const colorIndex = name.length % colors.length;
    const backgroundColor = colors[colorIndex];

    return `<div class="user-avatar" style="background-color: ${backgroundColor}">${initials}</div>`;
}

// Function to show property notification with enhanced tabs in modal
function showPropertyNotification(propertyId) {
    console.log('showPropertyNotification called with ID:', propertyId);

    const property = window.propertyData[propertyId];
    if (!property) {
        console.error('Property not found for ID:', propertyId);
        showNotification('error', 'Không tìm thấy thông tin bất động sản với ID: ' + propertyId);
        return;
    }

    // Generate unique tab ID
    const tabId = 'prop-' + propertyId + '-' + Date.now();

    // Update modal title
    const modalTitle = document.getElementById('propertyDetailsModalLabel');
    if (modalTitle) {
        modalTitle.innerHTML = `
            <i class="fas fa-home me-2"></i>
            ${property.Title}
            <span class="badge property-id-badge ms-2">#${property.PropertyID}</span>
        `;
    }

    // Create modal content with beautiful tabs
    const modalContent = `
        <div class="property-info-modal-content">
            <!-- Property Badges with Price -->
            <div class="property-badges mb-3 d-flex justify-content-between align-items-center">
                <div>
                    ${property.TypePro === 'Rent' || property.TypePro === 'Cho thuê' ?
                        '<span class="badge badge-rent"><i class="fas fa-key me-1"></i>Cho thuê</span>' :
                        property.TypePro === 'Sale' || property.TypePro === 'Cho bán' ?
                        '<span class="badge badge-sale"><i class="fas fa-home me-1"></i>Cho bán</span>' :
                        ''
                    }
                    <span class="badge ${property.Status === 'approved' ? 'badge-approved' : property.Status === 'pending' ? 'badge-pending' : 'badge-rejected'}">
                        ${property.Status}
                    </span>
                </div>
                <div class="property-price">
                    <span class="price-label text-muted">Giá:</span>
                    <span class="price-value text-success fw-bold fs-5">${property.Price || 'Liên hệ'}</span>
                </div>
            </div>

            <!-- Beautiful Tab Navigation -->
            <div class="property-tabs-container">
                <ul class="nav nav-tabs property-nav-tabs" id="propertyTabs-${tabId}" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="owner-tab-${tabId}" data-bs-toggle="tab" data-bs-target="#owner-${tabId}" type="button" role="tab">
                            <i class="fas fa-user-circle me-2"></i>Thông tin chủ sở hữu
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="media-tab-${tabId}" data-bs-toggle="tab" data-bs-target="#media-${tabId}" type="button" role="tab">
                            <i class="fas fa-images me-2"></i>Hình ảnh/Video
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="basic-tab-${tabId}" data-bs-toggle="tab" data-bs-target="#basic-${tabId}" type="button" role="tab">
                            <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="details-tab-${tabId}" data-bs-toggle="tab" data-bs-target="#details-${tabId}" type="button" role="tab">
                            <i class="fas fa-home me-2"></i>Chi tiết BĐS
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="propertyTabContent-${tabId}">
                <!-- Owner Information Tab (First and Active) -->
                <div class="tab-pane fade show active" id="owner-${tabId}" role="tabpanel">
                    <div class="p-3">
                        <div class="row align-items-center mb-3">
                            <div class="col-auto">
                                ${createUserAvatar(property.OwnerName)}
                            </div>
                            <div class="col">
                                <h5 class="mb-1 text-primary">${property.OwnerName || 'Chưa có thông tin'}</h5>
                                <p class="text-muted mb-0">Chủ sở hữu bất động sản</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-phone text-success me-2"></i>
                                    <strong>Số điện thoại:</strong> ${property.OwnerPhone || 'Chưa có thông tin'}
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-envelope text-info me-2"></i>
                                    <strong>Email:</strong> ${property.OwnerEmail || 'Chưa có thông tin'}
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-credit-card text-warning me-2"></i>
                                    <strong>CMND/CCCD:</strong> ${property.OwnerCMND || 'Chưa có thông tin'}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <i class="fas fa-home text-primary me-2"></i>
                                    <strong>Địa chỉ:</strong> ${property.OwnerAddress || 'Chưa có thông tin'}
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-building text-secondary me-2"></i>
                                    <strong>Phường/Xã:</strong> ${property.OwnerWard || 'Chưa có thông tin'}
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-city text-secondary me-2"></i>
                                    <strong>Quận/Huyện:</strong> ${property.OwnerDistrict || 'Chưa có thông tin'}
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-flag text-secondary me-2"></i>
                                    <strong>Tỉnh/TP:</strong> ${property.OwnerProvince || 'Chưa có thông tin'}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Media Tab -->
                <div class="tab-pane fade" id="media-${tabId}" role="tabpanel">
                    <div class="media-info-section p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-image me-2"></i>Hình ảnh <span class="badge bg-primary">${property.ImageCount || 0} ảnh</span></h6>
                                ${property.Images && property.Images.length > 0 ?
                                    '<div class="media-gallery">' +
                                    property.Images.map(img => {
                                        // Update image path to use public/storage/property/{propertyId}
                                        const imagePath = img.path.includes('public/storage/property') ?
                                            img.path :
                                            `public/storage/property/${property.PropertyID}/${img.path.split('/').pop()}`;
                                        return `<div class="media-item image-item">
                                            <img src="${imagePath}" alt="Hình ảnh BĐS" onclick="viewFullImage('${imagePath}')">
                                            ${img.caption ? `<div class="image-caption">${img.caption}</div>` : ''}
                                            ${img.uploadDate ? `<div class="upload-date">${img.uploadDate}</div>` : ''}
                                        </div>`;
                                    }).join('') +
                                    '</div>'
                                    : '<div class="empty-media-message text-muted"><i class="fas fa-info-circle"></i> Không có hình ảnh</div>'
                                }
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-info mb-3"><i class="fas fa-video me-2"></i>Video <span class="badge bg-info">${property.VideoCount || 0} video</span></h6>
                                ${property.Videos && property.Videos.length > 0 ?
                                    '<div class="media-gallery">' +
                                    property.Videos.map(video => {
                                        // Check for YouTube/TikTok video links
                                        const isYouTube = video.path && (video.path.includes('youtube.com') || video.path.includes('youtu.be'));
                                        const isTikTok = video.path && video.path.includes('tiktok.com');

                                        let videoContent = '';
                                        if (isYouTube) {
                                            const youtubeId = video.path.includes('youtu.be') ?
                                                video.path.split('youtu.be/')[1]?.split('?')[0] :
                                                video.path.split('v=')[1]?.split('&')[0];
                                            videoContent = `<iframe width="200" height="120" src="https://www.youtube.com/embed/${youtubeId}" frameborder="0" allowfullscreen></iframe>`;
                                        } else if (isTikTok) {
                                            videoContent = `<div class="tiktok-video"><a href="${video.path}" target="_blank" class="btn btn-outline-primary"><i class="fab fa-tiktok me-2"></i>Xem video TikTok</a></div>`;
                                        } else {
                                            const videoPath = video.path.includes('public/storage/property') ?
                                                video.path :
                                                `public/storage/property/${property.PropertyID}/${video.path.split('/').pop()}`;
                                            videoContent = `<video src="${videoPath}" controls style="width: 200px; max-height: 120px;"></video>`;
                                        }

                                        return `<div class="video-container video-item">
                                            ${videoContent}
                                            ${video.caption ? `<div class="video-caption">${video.caption}</div>` : ''}
                                            ${video.uploadDate ? `<div class="upload-date">${video.uploadDate}</div>` : ''}
                                        </div>`;
                                    }).join('') +
                                    '</div>'
                                    : '<div class="empty-media-message text-muted"><i class="fas fa-info-circle"></i> Không có video</div>'
                                }
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Information Tab -->
                <div class="tab-pane fade" id="basic-${tabId}" role="tabpanel">
                    <div class="basic-info-section p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card mb-3">
                                    <h6 class="info-card-title"><i class="fas fa-tag me-2"></i>Thông tin cơ bản</h6>
                                    <div class="info-item">
                                        <span class="info-label">Loại BĐS:</span>
                                        <span class="info-value">${property.Category || 'Chưa phân loại'}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Hình thức:</span>
                                        <span class="info-value">
                                            ${property.TypePro === 'Rent' || property.TypePro === 'Cho thuê' ? 'Cho thuê' :
                                              property.TypePro === 'Sale' || property.TypePro === 'Cho bán' ? 'Cho bán' :
                                              property.TypePro || 'Không xác định'}
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Trạng thái:</span>
                                        <span class="badge ${property.Status === 'approved' ? 'bg-success' : property.Status === 'pending' ? 'bg-warning' : 'bg-danger'}">${property.Status}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card mb-3">
                                    <h6 class="info-card-title"><i class="fas fa-map-marker-alt me-2"></i>Thông tin vị trí</h6>
                                    <div class="info-item">
                                        <span class="info-label">Địa chỉ:</span>
                                        <span class="info-value">${property.Address || 'Chưa có địa chỉ'}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Phường/Xã:</span>
                                        <span class="info-value">${property.Ward || 'Chưa xác định'}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Quận/Huyện:</span>
                                        <span class="info-value">${property.District || 'Chưa xác định'}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Tỉnh/TP:</span>
                                        <span class="info-value">${property.Province || 'Chưa xác định'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Basic Info Cards -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card mb-3">
                                    <h6 class="info-card-title"><i class="fas fa-expand-arrows-alt me-2"></i>Diện tích</h6>
                                    ${property.Area ? `
                                    <div class="info-item">
                                        <span class="info-label">Diện tích:</span>
                                        <span class="info-value">${property.Area} m²</span>
                                    </div>` : ''}
                                    ${property.HouseLength && property.HouseWidth ? `
                                    <div class="info-item">
                                        <span class="info-label">Diện tích nhà:</span>
                                        <span class="info-value">${property.HouseLength * property.HouseWidth} m² (${property.HouseLength}m × ${property.HouseWidth}m)</span>
                                    </div>` : ''}
                                    ${property.TotalLength && property.TotalWidth ? `
                                    <div class="info-item">
                                        <span class="info-label">Diện tích tổng:</span>
                                        <span class="info-value">${property.TotalLength * property.TotalWidth} m² (${property.TotalLength}m × ${property.TotalWidth}m)</span>
                                    </div>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Property Details Tab -->
                    <div class="tab-pane fade" id="details-${tabId}" role="tabpanel">
                        <div class="details-info-section p-4">
                            <div class="row">
                                ${property.Levelhouse || property.Floor || property.Bedroom || property.Bath_WC ? `
                                <div class="col-md-6">
                                    <div class="info-card mb-3">
                                        <h6 class="info-card-title"><i class="fas fa-building me-2"></i>Cấu trúc nhà</h6>
                                        ${property.Levelhouse ? `
                                        <div class="info-item">
                                            <span class="info-label">Cấp nhà:</span>
                                            <span class="info-value">${property.Levelhouse}</span>
                                        </div>` : ''}
                                        ${property.Floor ? `
                                        <div class="info-item">
                                            <span class="info-label">Số tầng:</span>
                                            <span class="info-value">${property.Floor}</span>
                                        </div>` : ''}
                                        ${property.Bedroom ? `
                                        <div class="info-item">
                                            <span class="info-label">Phòng ngủ:</span>
                                            <span class="info-value">${property.Bedroom}</span>
                                        </div>` : ''}
                                        ${property.Bath_WC ? `
                                        <div class="info-item">
                                            <span class="info-label">Phòng tắm/WC:</span>
                                            <span class="info-value">${property.Bath_WC}</span>
                                        </div>` : ''}
                                        ${property.Balcony !== null ? `
                                        <div class="info-item">
                                            <span class="info-label">Ban công:</span>
                                            <span class="info-value">${property.Balcony == 1 ? 'Có' : 'Không'}</span>
                                        </div>` : ''}
                                    </div>
                                </div>` : ''}

                                ${property.Road ? `
                                <div class="col-md-6">
                                    <div class="info-card mb-3">
                                        <h6 class="info-card-title"><i class="fas fa-ruler-combined me-2"></i>Thông tin đường</h6>
                                        <div class="info-item">
                                            <span class="info-label">Chiều rộng đường:</span>
                                            <span class="info-value">${property.Road}m</span>
                                        </div>
                                    </div>
                                </div>` : ''}
                            </div>

                            <div class="row">
                                ${property.Legal || property.Direction || property.Near || property.Interior ? `
                                <div class="col-md-6">
                                    <div class="info-card mb-3">
                                        <h6 class="info-card-title"><i class="fas fa-clipboard-list me-2"></i>Thông tin khác</h6>
                                        ${property.Legal ? `
                                        <div class="info-item">
                                            <span class="info-label">Pháp lý:</span>
                                            <span class="info-value">${property.Legal}</span>
                                        </div>` : ''}
                                        ${property.Direction ? `
                                        <div class="info-item">
                                            <span class="info-label">Hướng:</span>
                                            <span class="info-value">${property.Direction}</span>
                                        </div>` : ''}
                                        ${property.Near ? `
                                        <div class="info-item">
                                            <span class="info-label">Gần:</span>
                                            <span class="info-value">${property.Near}</span>
                                        </div>` : ''}
                                        ${property.Interior ? `
                                        <div class="info-item">
                                            <span class="info-label">Nội thất:</span>
                                            <span class="info-value">${property.Interior}</span>
                                        </div>` : ''}
                                    </div>
                                </div>` : ''}

                                ${property.WaterPrice || property.PowerPrice || property.Utilities ? `
                                <div class="col-md-6">
                                    <div class="info-card mb-3">
                                        <h6 class="info-card-title"><i class="fas fa-tools me-2"></i>Tiện ích & Chi phí</h6>
                                        ${property.WaterPrice ? `
                                        <div class="info-item">
                                            <span class="info-label">Giá nước:</span>
                                            <span class="info-value">${property.WaterPrice}</span>
                                        </div>` : ''}
                                        ${property.PowerPrice ? `
                                        <div class="info-item">
                                            <span class="info-label">Giá điện:</span>
                                            <span class="info-value">${property.PowerPrice}</span>
                                        </div>` : ''}
                                        ${property.Utilities ? `
                                        <div class="info-item">
                                            <span class="info-label">Tiện ích:</span>
                                            <span class="info-value">${property.Utilities}</span>
                                        </div>` : ''}
                                    </div>
                                </div>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            ${property.Description && property.Description !== '' ? `
            <div class="card-footer bg-light">
                <h6 class="text-secondary mb-2"><i class="fas fa-align-left me-2"></i>Mô tả</h6>
                <p class="mb-0 text-muted">${property.Description}</p>
            </div>` : ''}

            <div class="card-footer bg-white border-top">
                <div class="d-flex gap-2 justify-content-end">
                    <small class="text-muted">Mã BĐS: ${property.PropertyID}</small>
                </div>
            </div>
        </div>
    `;

    // Update modal content and show modal
    const modalBody = document.getElementById('propertyDetailsContent');
    if (modalBody) {
        modalBody.innerHTML = modalContent;
    }

    // Show the modal
    const modal = document.getElementById('propertyDetailsModal');
    if (modal) {
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }
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
