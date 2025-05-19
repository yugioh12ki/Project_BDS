@extends('_layout._layowner.app')
@section('title', 'Chủ sở hữu')

@section('content')
    <div class="dashboard-container">
        <!-- Properties and Appointments Row -->
        <div class="row mb-4">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-white border-0 pt-3 pb-1">
                        <h4 class="text-center text-success fw-normal">BẤT ĐỘNG SẢN ĐANG RAO BÁN</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Property Card 1 -->
                            <div class="col-md-6">
                                <div class="property-card border rounded p-2">
                                    <div class="row">
                                        <div class="col-5">
                                            <img src="{{ asset('images/property-sample.jpg') }}" class="img-fluid rounded" alt="THE PRIVÉ AN PHÚ">
                                        </div>
                                        <div class="col-7 ps-1">
                                            <h6 class="mb-0 fw-bold">THE PRIVÉ AN PHÚ</h6>
                                            <small>Quận 2, Hồ Chí Minh</small>
                                            <div class="property-info mt-1">
                                                <small class="d-block">Giá: 5.2 tỷ</small>
                                                <small class="d-block">Diện tích: 67m2</small>
                                                <small class="d-block">Trạng Thái: <span class="text-danger">Đang Bán</span></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end mt-2">
                                        <button class="btn btn-sm btn-outline-warning">Chi Tiết</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Property Card 2 -->
                            <div class="col-md-6">
                                <div class="property-card border rounded p-2">
                                    <div class="row">
                                        <div class="col-5">
                                            <img src="{{ asset('images/property-sample.jpg') }}" class="img-fluid rounded" alt="CYTI GATE TOWERS">
                                        </div>
                                        <div class="col-7 ps-1">
                                            <h6 class="mb-0 fw-bold">CYTI GATE TOWERS</h6>
                                            <small>Quận 8, Hồ Chí Minh</small>
                                            <div class="property-info mt-1">
                                                <small class="d-block">Giá: 3.9 tỷ</small>
                                                <small class="d-block">Diện tích: 105m2</small>
                                                <small class="d-block">Trạng Thái: <span class="text-danger">Đang Bán</span></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end mt-2">
                                        <button class="btn btn-sm btn-outline-warning">Chi Tiết</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Property Card 3 -->
                            <div class="col-md-6">
                                <div class="property-card border rounded p-2">
                                    <div class="row">
                                        <div class="col-5">
                                            <img src="{{ asset('images/property-sample.jpg') }}" class="img-fluid rounded" alt="THE FELIX">
                                        </div>
                                        <div class="col-7 ps-1">
                                            <h6 class="mb-0 fw-bold">THE FELIX</h6>
                                            <small>Trung An, Bình Dương</small>
                                            <div class="property-info mt-1">
                                                <small class="d-block">Giá: 4.8 tỷ</small>
                                                <small class="d-block">Diện tích: 75m2</small>
                                                <small class="d-block">Trạng Thái: <span class="text-danger">Đang Bán</span></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end mt-2">
                                        <button class="btn btn-sm btn-outline-warning">Chi Tiết</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Property Card 4 -->
                            <div class="col-md-6">
                                <div class="property-card border rounded p-2">
                                    <div class="row">
                                        <div class="col-5">
                                            <img src="{{ asset('images/property-sample.jpg') }}" class="img-fluid rounded" alt="HAPPY ONE SORA">
                                        </div>
                                        <div class="col-7 ps-1">
                                            <h6 class="mb-0 fw-bold">HAPPY ONE SORA</h6>
                                            <small>Thủ Đức, Hồ Chí Minh</small>
                                            <div class="property-info mt-1">
                                                <small class="d-block">Giá: 5.7 tỷ</small>
                                                <small class="d-block">Diện tích: 85m2</small>
                                                <small class="d-block">Trạng Thái: <span class="text-danger">Đang Bán</span></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end mt-2">
                                        <button class="btn btn-sm btn-outline-warning">Chi Tiết</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header bg-white border-0 pt-3 pb-1">
                        <h4 class="text-center text-success fw-normal">LỊCH HẸN SẮP TỚI</h4>
                    </div>
                    <div class="card-body">
                        <!-- Appointment Card 1 -->
                        <div class="appointment-card border rounded p-3 mb-3">
                            <div class="row">
                                <div class="col-9">
                                    <div class="mb-2">
                                        <strong>Ngày & giờ hẹn:</strong> 22/05/2025 - 14:00
                                    </div>
                                    <div class="mb-2">
                                        <strong>Tên khách hàng:</strong> Lê Thị Mai
                                    </div>
                                    <div class="mb-2">
                                        <strong>SĐT:</strong> 0909 123 456
                                    </div>
                                    <div class="mb-2">
                                        <strong>BĐS cần xem:</strong> Căn hộ Sunrise Riverside, Q.7
                                    </div>
                                    <div class="mb-2">
                                        <strong>Địa điểm gặp mặt:</strong> Tại bất động sản
                                    </div>
                                    <div>
                                        <strong>Trạng thái:</strong> <span class="text-warning">Chờ xác nhận</span>
                                    </div>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="avatar rounded-circle bg-warning mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="bi bi-person-fill text-white" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Appointment Card 2 -->
                        <div class="appointment-card border rounded p-3">
                            <div class="row">
                                <div class="col-9">
                                    <div class="mb-2">
                                        <strong>Ngày & giờ:</strong> 23/05/2025 - 10:30
                                    </div>
                                    <div class="mb-2">
                                        <strong>Khách hàng:</strong> Nguyễn Văn Bảo
                                    </div>
                                    <div class="mb-2">
                                        <strong>SĐT:</strong> 0988 765 432
                                    </div>
                                    <div class="mb-2">
                                        <strong>BĐS:</strong> Nhà phố Phạm Văn Đồng, Thủ Đức
                                    </div>
                                    <div class="mb-2">
                                        <strong>Địa điểm:</strong> Cafe Highland gần nhà
                                    </div>
                                    <div>
                                        <strong>Trạng thái:</strong> <span class="text-success">Đã xác nhận</span>
                                    </div>
                                </div>
                                <div class="col-3 text-center">
                                    <div class="avatar rounded-circle bg-warning mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="bi bi-person-fill text-white" style="font-size: 2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 rounded-3 shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="display-4 fw-bold mb-2">02</h1>
                        <p class="text-muted text-uppercase mb-0">BẤT ĐỘNG SẢN ĐANG RAO BÁN</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 rounded-3 shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="display-4 fw-bold mb-2">01</h1>
                        <p class="text-muted text-uppercase mb-0">GIAO DỊCH ĐÃ HOÀN TẤT</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 rounded-3 shadow-sm">
                    <div class="card-body text-center">
                        <h1 class="display-4 fw-bold mb-2">03</h1>
                        <p class="text-muted text-uppercase mb-0">LỊCH HẸN ĐANG CHỜ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .property-card, .appointment-card {
            transition: all 0.3s ease;
        }
        
        .property-card:hover, .appointment-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
    </style>
@endsection

