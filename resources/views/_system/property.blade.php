@extends('_layout._layadmin.app')

@section('property')
<div class="property-page-wrapper" style="margin:0;padding:0;">
    <ul class="nav nav-tabs" id="mainPropertyTabs" style="margin-top: 0;">
        <li class="nav-item">
            <a class="nav-link active" id="approve-tab" data-bs-toggle="tab" href="#approve-content">Kiểm Duyệt</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="assign-tab" data-bs-toggle="tab" href="#assign-content">Phân Công Môi Giới</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="approve-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>
                    Kiểm Duyệt Bất Động Sản
                    {{ isset($typePro) ? '- ' . $typePro : '' }}
                    @if(isset($status) && $status == 'pending')
                        <span class="badge bg-warning">Đang chờ duyệt</span>
                    @endif
                </h5>

                <div class="view-controls">
                    <div class="btn-group" role="group" aria-label="View mode">
                        <button type="button" class="btn btn-outline-secondary active" id="listViewBtn">
                            <i class="fas fa-list"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="mapViewBtn">
                            <i class="fas fa-map-marked-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left column - Property list -->
                <div class="col-lg-7 property-list-column">
                    <div class="card">
                        <div class="card-body">
                            @if(isset($properties))
                                @include('_system.partialview.property_table', ['properties' => $properties, 'columns' => $columns])
                            @else
                                <div class="alert alert-info">Không có dữ liệu BĐS</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right column - Google Map -->
                <div class="col-lg-5 map-column">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Vị trí trên bản đồ</h6>
                            <div class="map-controls">
                                <button class="btn btn-sm btn-outline-primary" id="fitAllMarkersBtn" title="Hiển thị tất cả vị trí">
                                    <i class="fas fa-compress-arrows-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-2 py-2">
                                <small><i class="fas fa-info-circle"></i> Nhấn vào bất kỳ bất động sản nào trong danh sách để xem vị trí trên bản đồ. Nếu không có tọa độ, hệ thống sẽ tự động tìm kiếm theo địa chỉ.</small>
                            </div>
                            <div id="googleMap" style="width:100%;height:500px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="assign-content">
            <h5>Phân Công Môi Giới {{ isset($typePro) ? '- ' . $typePro : '' }}</h5>
            {{-- Nội dung phân công môi giới --}}
        </div>
    </div>
</div>

<!-- Load app.js with PropertyManagement module first -->
<script src="{{ mix('js/app.js') }}"></script>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
   integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
   crossorigin=""/>

<!-- Leaflet Fullscreen Plugin CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/2.0.0/Control.FullScreen.css" />

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
   integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
   crossorigin=""></script>

<!-- Leaflet Fullscreen Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/2.0.0/Control.FullScreen.min.js"></script>

<!-- Map JavaScript -->
<script>
// Make map and markers globally accessible
var map;
var markers = [];

// Function to show notification
function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : type === 'info' ? 'info' : 'danger'} alert-dismissible fade show notification-toast`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Add to document
    document.body.appendChild(notification);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

// Initialize map with Leaflet
function initMap() {
    // Default center of Vietnam
    var vietnamLat = 16.0;
    var vietnamLng = 106.0;

    // Create map
    map = L.map('googleMap').setView([vietnamLat, vietnamLng], 5);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add fullscreen control
    map.addControl(new L.Control.Fullscreen());

    // Add markers for properties if coordinates exist
    markers = [];
    var bounds = [];

    @if(isset($propertyCoordinates))
        @foreach($propertyCoordinates as $property)
            var lat = {{ $property['lat'] }};
            var lng = {{ $property['lng'] }};

            // Create marker
            var marker = L.marker([lat, lng], {
                title: '{{ $property['title'] }}',
                propertyId: '{{ $property['id'] }}'
            }).addTo(map);

            // Add popup with property info
            marker.bindPopup('<div class="map-info-window">' +
                '<strong>{{ $property['title'] }}</strong>' +
                '<p>{{ $property['address'] }}</p>' +
                '</div>');

            // Add marker click event
            marker.on('click', function() {
                highlightProperty('{{ $property['id'] }}');
            });

            // Add to markers array and bounds
            markers.push(marker);
            bounds.push([lat, lng]);
        @endforeach

        // If we have markers, fit the map to show all of them
        if (markers.length > 0) {
            // Create a bounds object
            var boundsObj = L.latLngBounds(bounds);
            map.fitBounds(boundsObj);
            // Don't zoom in too far on only one marker
            if (markers.length === 1) {
                map.setZoom(15);
            }
        }
    @endif

    // Function to select property on map when clicked in list
    window.selectPropertyOnMap = function(id, lat, lng, address) {
        // Find the marker with the matching property ID
        var targetMarker = markers.find(marker => marker.options.propertyId === id);

        if (targetMarker) {
            // Open popup for this marker
            targetMarker.openPopup();

            // Center map on this property
            map.setView(targetMarker.getLatLng(), 15);

            // Highlight the property in the list
            highlightProperty(id);
        } else if (lat && lng && lat !== '' && lng !== '') {
            // If no markers but we have valid coordinates, center on those
            map.setView([parseFloat(lat), parseFloat(lng)], 15);

            // Create a new marker
            var row = document.getElementById('property-row-' + id);
            var title = row ? row.querySelector('.property-title').textContent : 'Bất động sản #' + id;

            var newMarker = L.marker([parseFloat(lat), parseFloat(lng)], {
                propertyId: id,
                title: title
            }).addTo(map);

            // Get additional property information
            var propertyType = '';
            var propertyPrice = '';
            var propertyDate = '';

            if (row) {
                propertyType = row.getAttribute('data-category') ?
                    (document.querySelector(`.property-row[data-category="${row.getAttribute('data-category')}"] td:nth-child(3)`) ?
                    document.querySelector(`.property-row[data-category="${row.getAttribute('data-category')}"] td:nth-child(3)`).textContent.trim() : '') : '';
                propertyPrice = row.getAttribute('data-price') ? new Intl.NumberFormat('vi-VN').format(row.getAttribute('data-price')) + ' VND' : '';
                propertyDate = row.getAttribute('data-date') || '';
            }

            // Add detailed popup with formatted address and property info
            var popupContent = '<div class="map-info-window">' +
                '<strong>' + title + '</strong>';

            if (address && address !== '') {
                popupContent += '<p><i class="fas fa-map-marker-alt"></i> ' + address + '</p>';
            }

            if (propertyType) {
                popupContent += '<p><i class="fas fa-home"></i> ' + propertyType + '</p>';
            }

            if (propertyPrice) {
                popupContent += '<p><i class="fas fa-tags"></i> ' + propertyPrice + '</p>';
            }

            if (propertyDate) {
                popupContent += '<p><i class="far fa-calendar-alt"></i> ' + propertyDate + '</p>';
            }

            popupContent += '</div>';

            newMarker.bindPopup(popupContent).openPopup();

            // Add click event
            newMarker.on('click', function() {
                highlightProperty(id);
            });

            markers.push(newMarker);

            // Highlight the property in the list
            highlightProperty(id);
        } else if (address && address !== '') {
            // If no markers and no coordinates, but we have an address, try to geocode it
            geocodeAddress(address, id);
        } else {
            // If no address found in parameters, try to get it from row attribute
            var row = document.getElementById('property-row-' + id);
            if (row && row.getAttribute('data-full-address')) {
                // Use full address (combines Address, Ward, District, Province)
                geocodeAddress(row.getAttribute('data-full-address'), id);
            } else if (row && row.getAttribute('data-address')) {
                // Fallback to just Address field if full address isn't available
                geocodeAddress(row.getAttribute('data-address'), id);
            } else {
                showNotification('error', 'Không tìm thấy địa chỉ cho bất động sản này');
            }
        }
    };

    // Function to convert address to coordinates using Nominatim (OpenStreetMap's geocoder)
    function geocodeAddress(address, propertyId) {
        if (!address) return;

        // Hiển thị thông báo đang tìm kiếm
        showNotification('info', 'Đang tìm kiếm vị trí của bất động sản...');

        // Thêm "Việt Nam" vào địa chỉ để tăng độ chính xác
        if (address.toLowerCase().indexOf('việt nam') === -1) {
            address += ', Việt Nam';
        }

        // Sử dụng Nominatim API (miễn phí) của OpenStreetMap
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address))
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                const result = data[0];
                const lat = parseFloat(result.lat);
                const lng = parseFloat(result.lon);

                // Tạo marker mới nếu chưa tồn tại
                let markerExists = false;
                let existingMarker;

                for (let i = 0; i < markers.length; i++) {
                    if (markers[i].options.propertyId === propertyId) {
                        markerExists = true;
                        existingMarker = markers[i];
                        break;
                    }
                }

                if (!markerExists) {
                    // Lấy thông tin bất động sản từ data attribute của row
                    var row = document.getElementById('property-row-' + propertyId);
                    var title = row ? row.querySelector('.property-title').textContent : 'Bất động sản #' + propertyId;

                    // Lấy thêm thông tin chi tiết từ data attribute (nếu có)
                    var propertyType = '';
                    var propertyPrice = '';
                    var propertyDate = '';

                    if (row) {
                        propertyType = row.getAttribute('data-category') ? document.querySelector(`.property-row[data-category="${row.getAttribute('data-category')}"] td:nth-child(3)`).textContent.trim() : '';
                        propertyPrice = row.getAttribute('data-price') ? new Intl.NumberFormat('vi-VN').format(row.getAttribute('data-price')) + ' VND' : '';
                        propertyDate = row.getAttribute('data-date') || '';
                    }

                    // Tạo marker mới
                    var marker = L.marker([lat, lng], {
                        propertyId: propertyId,
                        title: title
                    }).addTo(map);

                    // Popup chi tiết cho marker với nhiều thông tin hơn
                    var popupContent = '<div class="map-info-window">' +
                        '<strong>' + title + '</strong>';

                    if (address) {
                        popupContent += '<p><i class="fas fa-map-marker-alt"></i> ' + address + '</p>';
                    }

                    if (propertyType) {
                        popupContent += '<p><i class="fas fa-home"></i> ' + propertyType + '</p>';
                    }

                    if (propertyPrice) {
                        popupContent += '<p><i class="fas fa-tags"></i> ' + propertyPrice + '</p>';
                    }

                    if (propertyDate) {
                        popupContent += '<p><i class="far fa-calendar-alt"></i> ' + propertyDate + '</p>';
                    }

                    popupContent += '</div>';

                    marker.bindPopup(popupContent).openPopup();

                    // Sự kiện click cho marker
                    marker.on('click', function() {
                        highlightProperty(propertyId);
                    });

                    markers.push(marker);

                    // Di chuyển bản đồ tới marker mới tạo
                    map.setView([lat, lng], 15);

                    // Cập nhật data attribute của dòng bất động sản
                    if (row) {
                        row.setAttribute('data-lat', lat);
                        row.setAttribute('data-lng', lng);
                    }

                    showNotification('success', 'Đã tìm thấy vị trí bất động sản');
                } else {
                    // Dùng marker đã có sẵn
                    map.setView(existingMarker.getLatLng(), 15);
                    existingMarker.openPopup();
                }
            } else {
                showNotification('error', 'Không tìm thấy vị trí cho địa chỉ này');
            }
        })
        .catch(error => {
            console.error('Geocoding error:', error);
            showNotification('error', 'Lỗi khi tìm kiếm vị trí: ' + error.message);
        });
    }

    // Function to highlight property in list
    function highlightProperty(id) {
        // Remove highlight from all rows
        document.querySelectorAll('.property-row').forEach(function(row) {
            row.classList.remove('highlighted-row');
        });

        // Add highlight to selected row
        var row = document.getElementById('property-row-' + id);
        if (row) {
            row.classList.add('highlighted-row');
            row.scrollIntoView({behavior: 'smooth', block: 'center'});
        }
    }
}

// Handle property approval
document.addEventListener('DOMContentLoaded', function() {
    // Delegate event handler for approve buttons (works for dynamically added elements)
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('approve-btn') ||
           (e.target.parentElement && e.target.parentElement.classList.contains('approve-btn'))) {

            const button = e.target.classList.contains('approve-btn') ? e.target : e.target.parentElement;
            const propertyId = button.getAttribute('data-property-id');

            if (confirm('Bạn có chắc chắn muốn duyệt bất động sản này không?')) {
                updatePropertyStatus(propertyId, 'approved');
            }
        }
    });

    // Delegate event handler for reject buttons
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('reject-btn') ||
           (e.target.parentElement && e.target.parentElement.classList.contains('reject-btn'))) {

            const button = e.target.classList.contains('reject-btn') ? e.target : e.target.parentElement;
            const propertyId = button.getAttribute('data-property-id');

            if (confirm('Bạn có chắc chắn muốn từ chối bất động sản này không?')) {
                updatePropertyStatus(propertyId, 'rejected');
            }
        }
    });

    // Use the new PropertyManagement module for status updates
    function updatePropertyStatus(propertyId, status, reason = null) {
        // Call the function from our PropertyManagement module
        PropertyManagement.updatePropertyStatus(propertyId, status, reason);
    }
});
</script>
<!-- Load app.js with PropertyManagement module -->
<script src="{{ mix('js/app.js') }}" defer></script>

<!-- OpenStreetMap attrbution -->
<script>
    // Initialize the map when document is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
    });
</script>

<style>
    .property-page-wrapper {
        background-color: transparent;
    }

    .nav-tabs {
        display: flex;
        justify-content: flex-start;
    }

    /* Map Info Window Styling */
    .map-info-window {
        min-width: 200px;
        max-width: 300px;
        padding: 5px;
    }

    .map-info-window strong {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        color: #2c3e50;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }

    .map-info-window p {
        margin: 4px 0;
        font-size: 12px;
        color: #555;
    }

    .map-info-window i {
        width: 16px;
        margin-right: 5px;
        color: #3498db;
    }

    /* Leaflet popup styling */
    .leaflet-popup-content {
        margin: 8px 12px;
    }
        border-top: 1px solid #ddd;
        margin-top: 0;
        background-color: #f8f9fa00;
    }

    .nav-tabs .nav-link {
        border: 1px solid #ddd;
        border-radius: 4px 4px 0 0;
        margin-right: 5px;
        padding: 10px 15px;
        transition: background-color 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        background-color: #0056b3;
        color: #ffffff;
    }

    /* Property table styles */
    .property-row {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .property-row:hover {
        background-color: #f1f1f1;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .highlighted-row {
        background-color: #e9f5ff !important;
        border-left: 3px solid #0d6efd;
    }

    .pending-property {
        border-left: 3px solid #ffc107;
    }

    /* Table styles */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
    }

    .table thead th {
        background-color: #0056b3;
        color: #ffffff;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
        padding: 12px;
        border-bottom: none;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .btn-group {
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        border-radius: 4px;
        overflow: hidden;
    }

    /* Property table header */
    .property-table-header {
        background-color: #f8f9fa;
        padding: 12px;
        border-radius: 8px 8px 0 0;
        border: 1px solid #dee2e6;
        border-bottom: none;
    }

    /* Batch action buttons */
    .batch-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* Checkbox column */
    .checkbox-column {
        width: 40px;
        text-align: center;
    }

    /* Property title formatting */
    .property-title {
        font-weight: 500;
        color: #0056b3;
    }

    /* Table responsive scroll */
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    /* Scrollbar styling */
    .table-responsive::-webkit-scrollbar {
        width: 6px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #0056b3;
        border-radius: 10px;
    }

    /* Google map container */
    #googleMap {
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .col-md-7, .col-md-5 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        #googleMap {
            height: 300px !important;
            margin-top: 20px;
        }
    }

    /* Responsive layout for view toggle */
    @media (max-width: 991.98px) {
        .map-column.d-none {
            display: none !important;
        }

        .property-list-column.d-none {
            display: none !important;
        }

        .view-controls {
            display: flex;
        }
    }

    @media (min-width: 992px) {
        .view-controls {
            display: none;
        }
    }

    /* Popover styles for status confirmation */
    .status-action-buttons {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .popover {
        max-width: 200px;
    }

    .map-info-window {
        padding: 8px;
        max-width: 250px;
    }

    .nav-tabs .nav-link.active {
        background-color: #0056b3;
        border-color: #ddd #ddd transparent;
        color: #ffffff;
    }

    .tab-content {
        border-radius: 0 0 8px 8px;
        padding: 20px;
        min-height: 400px;
    }

    /* Toast notification styles */
    .notification-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        z-index: 9999;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        border-radius: 6px;
        animation: slide-in 0.3s ease-out forwards;
    }

    @keyframes slide-in {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    /* Property updating animation */
    .property-row.updating {
        opacity: 0.6;
        position: relative;
    }

    .property-row.updating:after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.5) url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzgiIGhlaWdodD0iMzgiIHZpZXdCb3g9IjAgMCAzOCAzOCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBzdHJva2U9IiMzNDk4ZGIiPiAgICA8ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPiAgICAgICAgPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMSAxKSIgc3Ryb2tlLXdpZHRoPSIyIj4gICAgICAgICAgICA8Y2lyY2xlIHN0cm9rZS1vcGFjaXR5PSIuMyIgY3g9IjE4IiBjeT0iMTgiIHI9IjE4Ii8+ICAgICAgICAgICAgPHBhdGggZD0iTTM2IDE4YzAtOS45NC04LjA2LTE4LTE4LTE4Ij4gICAgICAgICAgICAgICAgPGFuaW1hdGVUcmFuc2Zvcm0gICAgICAgICAgICAgICAgICAgIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgICAgICAgICAgICAgICAgICAgIHR5cGU9InJvdGF0ZSIgICAgICAgICAgICAgICAgICAgIGZyb209IjAgMTggMTgiICAgICAgICAgICAgICAgICAgICB0bz0iMzYwIDE4IDE4IiAgICAgICAgICAgICAgICAgICAgZHVyPSIxcyIgICAgICAgICAgICAgICAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIi8+ICAgICAgICAgICAgPC9wYXRoPiAgICAgICAgPC9nPiAgICA8L2c+PC9zdmc+') center no-repeat;
        z-index: 2;
    }

    /* Keyboard shortcut hints */
    .shortcut-hint {
        display: inline-block;
        min-width: 20px;
        height: 20px;
        padding: 0 4px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-size: 12px;
        line-height: 18px;
        text-align: center;
        color: #495057;
        margin-left: 5px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    /* CSV export button */
    .csv-export-btn {
        transition: all 0.3s ease;
    }

    .csv-export-btn:hover {
        background-color: #28a745;
        color: #fff;
    }

    /* Selected row highlight */
    .property-row.selected-for-batch {
        background-color: #fffde7;
        box-shadow: 0 0 0 1px #ffc107;
    }

    /* Property status badges */
    .status-badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .status-badge.approved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-badge.rejected {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tab navigation
        const mainTabs = document.querySelectorAll('#mainPropertyTabs .nav-link');

        mainTabs.forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                // Gỡ bỏ active từ tất cả các tab
                mainTabs.forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content > .tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                // Thêm active cho tab được chọn
                this.classList.add('active');

                // Hiển thị nội dung tương ứng
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.classList.add('show', 'active');
                }
            });
        });

        // View mode toggle (list/map)
        const listViewBtn = document.getElementById('listViewBtn');
        const mapViewBtn = document.getElementById('mapViewBtn');

        if (listViewBtn && mapViewBtn) {
            // Set initial state based on screen size
            if (window.innerWidth < 992) {
                // On mobile, start with list view
                PropertyManagement.toggleViewMode('list');
            }

            // List view button click
            listViewBtn.addEventListener('click', function() {
                PropertyManagement.toggleViewMode('list');
            });

            // Map view button click
            mapViewBtn.addEventListener('click', function() {
                PropertyManagement.toggleViewMode('map');
            });
        }

        // Fit all markers button
        const fitAllMarkersBtn = document.getElementById('fitAllMarkersBtn');
        if (fitAllMarkersBtn) {
            fitAllMarkersBtn.addEventListener('click', function() {
                if (typeof map !== 'undefined' && markers && markers.length > 0) {
                    PropertyManagement.fitAllMarkersToMap(map, markers);
                }
            });
        }
    });
</script>

@endsection
