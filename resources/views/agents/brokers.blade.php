@extends('_layout._layagent.app')

@section('title', 'Danh Sách Phân Công Môi Giới')

@section('brokers')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-2">Phân công môi giới</h1>
            <p class="text-muted">Xem các phân công môi giới bất động sản</p>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-4">
            <!-- Danh sách phân công -->
            <div class="mb-4">
                <!-- Quận 2 -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Quận 2</h5>
                            <span class="badge bg-success px-2 py-1">Đang hoạt động</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt text-muted me-2"></i>
                            <span>Khu vực Thảo Điền, An Phú</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-calendar3 text-muted me-2"></i>
                            <span>2025-06-01</span>
                        </div>
                        <div class="mt-2">
                            <p class="mb-1 text-muted">Môi giới được phân công:</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark">Nguyễn Văn A</span>
                                <span class="badge bg-light text-dark">Trần Thị B</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quận Bình Thạnh -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Quận Bình Thạnh</h5>
                            <span class="badge bg-warning px-2 py-1">Chờ xử lý</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt text-muted me-2"></i>
                            <span>Khu vực Vinhomes Central Park</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-calendar3 text-muted me-2"></i>
                            <span>2025-06-02</span>
                        </div>
                        <div class="mt-2">
                            <p class="mb-1 text-muted">Môi giới được phân công:</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark">Trần Thị B</span>
                                <span class="badge bg-light text-dark">Lê Văn C</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quận 7 -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Quận 7</h5>
                            <span class="badge bg-info px-2 py-1">Hoàn thành</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt text-muted me-2"></i>
                            <span>Khu đô thị Phú Mỹ Hưng</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-calendar3 text-muted me-2"></i>
                            <span>2025-06-03</span>
                        </div>
                        <div class="mt-2">
                            <p class="mb-1 text-muted">Môi giới được phân công:</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark">Nguyễn Văn A</span>
                                <span class="badge bg-light text-dark">Lê Văn C</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Chi tiết phân công -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Chi tiết phân công - Quận 2</h5>
                        <span class="badge bg-success px-2 py-1">Đang hoạt động</span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="px-3 py-3 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt text-muted me-2"></i>
                            <span>Khu vực Thảo Điền, An Phú</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar3 text-muted me-2"></i>
                            <span>Ngày: 2025-06-01</span>
                        </div>
                    </div>

                    <!-- Map display -->
                    <div class="map-container mb-3">
                        <div id="propertyMap" style="height: 350px; width: 100%;"></div>
                    </div>

                    <!-- Môi giới được phân công -->
                    <h6 class="fw-bold mt-4 mb-3">Môi giới được phân công</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-2 border rounded mb-2">
                                <div class="me-3">
                                    <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="Nguyễn Văn A" class="rounded-circle" width="48">
                                </div>
                                <div>
                                    <h6 class="mb-0">Nguyễn Văn A</h6>
                                    <small class="text-muted">0901234567</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-2 border rounded mb-2">
                                <div class="me-3">
                                    <img src="{{ asset('assets/images/users/avatar-2.jpg') }}" alt="Trần Thị B" class="rounded-circle" width="48">
                                </div>
                                <div>
                                    <h6 class="mb-0">Trần Thị B</h6>
                                    <small class="text-muted">0907654321</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Google Maps API script -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" defer></script>

<script>
    // Initialize the map
    function initMap() {
        // Coordinates for Thảo Điền, An Phú (example coordinates)
        const location = { lat: 10.802744, lng: 106.740697 };
        
        const map = new google.maps.Map(document.getElementById("propertyMap"), {
            zoom: 15,
            center: location,
        });
        
        // Add marker
        const marker = new google.maps.Marker({
            position: location,
            map: map,
            title: "Khu vực Thảo Điền, An Phú",
        });
    }
</script>
@endsection 