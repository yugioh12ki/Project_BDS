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
                                <button class="btn btn-sm btn-outline-primary" id="fitAllMarkersBtn">
                                    <i class="fas fa-compress-arrows-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
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

<!-- Google Maps JavaScript using @googlemaps/google-maps-services-js -->
<script>
// Make map and markers globally accessible
var map;
var markers = [];
var infoWindows = [];

// Import the Client from the npm package in the app.js file
// We'll focus on frontend functionality here
function initMap() {
    // Default center of Vietnam
    var vietnam = {lat: 16.0, lng: 106.0};

    // Create map
    map = new google.maps.Map(document.getElementById('googleMap'), {
        zoom: 5,
        center: vietnam,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true
    });

    // Add markers for properties if coordinates exist
    markers = [];
    infoWindows = [];
    var bounds = new google.maps.LatLngBounds();

    @if(isset($propertyCoordinates))
        @foreach($propertyCoordinates as $property)
            var position = {lat: {{ $property['lat'] }}, lng: {{ $property['lng'] }}};
            var marker = new google.maps.Marker({
                position: position,
                map: map,
                title: '{{ $property['title'] }}',
                propertyId: '{{ $property['id'] }}',
                animation: google.maps.Animation.DROP
            });

            // Extend bounds to include this marker
            bounds.extend(position);

            var infoContent = '<div class="map-info-window">' +
                '<strong>{{ $property['title'] }}</strong>' +
                '<p>{{ $property['address'] }}</p>' +
                '</div>';

            var infoWindow = new google.maps.InfoWindow({
                content: infoContent,
                maxWidth: 300
            });

            marker.addListener('click', (function(marker, infoWindow, id) {
                return function() {
                    // Close all open info windows
                    infoWindows.forEach(function(iw) {
                        iw.close();
                    });

                    // Open this info window
                    infoWindow.open(map, marker);

                    // Highlight the property in the list
                    highlightProperty(id);
                };
            })(marker, infoWindow, '{{ $property['id'] }}'));

            markers.push(marker);
            infoWindows.push(infoWindow);
        @endforeach

        // If we have markers, fit the map to show all of them
        if (markers.length > 0) {
            map.fitBounds(bounds);
            // Don't zoom in too far on only one marker
            if (markers.length === 1) {
                map.setZoom(15);
            }
        }
    @endif

    // Function to select property on map when clicked in list
    window.selectPropertyOnMap = function(id, lat, lng) {
        // Find the marker with the matching property ID
        var targetMarker = markers.find(marker => marker.propertyId === id);

        if (targetMarker) {
            // Close all info windows
            infoWindows.forEach(function(iw) {
                iw.close();
            });

            // Find the info window for this marker
            var index = markers.indexOf(targetMarker);
            if (index !== -1) {
                // Open this property's info window
                infoWindows[index].open(map, targetMarker);

                // Center map on this property
                map.setCenter(targetMarker.getPosition());
                map.setZoom(15);
            }
        } else if (lat && lng) {
            // If no markers but we have coordinates, center on those
            var latLng = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));
            map.setCenter(latLng);
            map.setZoom(15);
        }
    };

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

<!-- Google Maps API and Services -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY', 'YOUR_API_KEY') }}&libraries=places&callback=initMap" async defer></script>
<script>
    // This is where we would initialize the @googlemaps/google-maps-services-js client
    // In a real implementation, this would be done in a separate JS file
    // Example:
    /*
    import { Client } from "@googlemaps/google-maps-services-js";
    const client = new Client({});
    */

    // Additional geocoding functionality could be implemented here using the library
</script>

<style>
    .property-page-wrapper {
        background-color: transparent;
    }

    .nav-tabs {
        display: flex;
        justify-content: flex-start;
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
