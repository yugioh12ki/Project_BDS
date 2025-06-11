@extends('_layout._layowner.app')
@section('title', 'Trang Chủ - Chủ Sở Hữu')

@section('dashboard')
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card bg-gradient-primary text-white rounded-3 p-4">
                <h2 class="mb-2">Chào mừng, {{ $owner->Name }}!</h2>
                <p class="mb-0 opacity-75">Quản lý bất động sản và theo dõi hoạt động kinh doanh của bạn</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card bg-white rounded-3 shadow-sm p-4 border-start border-5 border-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="stat-number text-primary">{{ $stats['active_properties'] }}</h3>
                        <p class="stat-label text-muted mb-0">BĐS Đang Hoạt Động</p>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-house-fill text-primary fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card bg-white rounded-3 shadow-sm p-4 border-start border-5 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="stat-number text-warning">{{ $stats['pending_properties'] }}</h3>
                        <p class="stat-label text-muted mb-0">BĐS Chờ Duyệt</p>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-clock-fill text-warning fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card bg-white rounded-3 shadow-sm p-4 border-start border-5 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="stat-number text-success">{{ $stats['completed_transactions'] }}</h3>
                        <p class="stat-label text-muted mb-0">Giao Dịch Hoàn Tất</p>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-check-circle-fill text-success fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card bg-white rounded-3 shadow-sm p-4 border-start border-5 border-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="stat-number text-info">{{ $stats['pending_appointments'] }}</h3>
                        <p class="stat-label text-muted mb-0">Lịch Hẹn Chờ</p>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-calendar-check-fill text-info fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="revenue-card bg-white rounded-3 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="text-success mb-2">Tổng Doanh Thu</h4>
                        <h2 class="mb-0 text-success">{{ number_format($stats['total_revenue'], 0, ',', '.') }} VNĐ</h2>
                    </div>
                    <div class="revenue-icon">
                        <i class="bi bi-cash-stack text-success" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Properties and Appointments Row -->
    <div class="row mb-4">
        <!-- Properties Section -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-success">
                        <i class="bi bi-buildings me-2"></i>Bất Động Sản Gần Đây
                    </h5>
                    <a href="{{ route('owner.property.index') }}" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-eye me-1"></i>Xem Tất Cả
                    </a>
                </div>
                <div class="card-body">
                    @if($recentProperties->count() > 0)
                        <div class="row g-3">
                            @foreach($recentProperties as $property)
                            <div class="col-md-6">
                                <div class="property-card border rounded-3 p-3 h-100">
                                    <div class="row g-2">
                                        <div class="col-4">
                                            @php
                                                $image = $property->images->first();
                                                $imageSrc = $image ? asset($image->ImagePath) : asset('images/default-property.jpg');
                                            @endphp
                                            <img src="{{ $imageSrc }}"
                                                 class="img-fluid rounded property-thumbnail"
                                                 alt="{{ $property->Title }}"
                                                 onerror="this.src='{{ asset('images/default-property.jpg') }}'">
                                        </div>
                                        <div class="col-8">
                                            <h6 class="mb-1 fw-bold text-truncate" title="{{ $property->Title }}">
                                                {{ $property->Title }}
                                            </h6>
                                            <small class="text-muted d-block mb-2">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                {{ $property->District }}, {{ $property->Province }}
                                            </small>
                                            <div class="property-info">
                                                <small class="d-block mb-1">
                                                    <strong>Giá:</strong>
                                                    <span class="text-success">{{ number_format($property->Price, 0, ',', '.') }} VNĐ</span>
                                                </small>
                                                @if($property->chiTiet)
                                                <small class="d-block mb-1">
                                                    <strong>Diện tích:</strong> {{ $property->chiTiet->Area ?? 'N/A' }}m²
                                                </small>
                                                @endif
                                                <small class="d-block">
                                                    <strong>Trạng thái:</strong>
                                                    @if($property->Status === 'active')
                                                        <span class="badge bg-success">Đang Hoạt Động</span>
                                                    @elseif($property->Status === 'pending')
                                                        <span class="badge bg-warning">Chờ Duyệt</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $property->Status }}</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>Chi Tiết
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-house text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Chưa có bất động sản nào</p>
                            <button class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Thêm Bất Động Sản
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Appointments Section -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-info">
                        <i class="bi bi-calendar-check me-2"></i>Lịch Hẹn Sắp Tới
                    </h5>
                    <a href="{{ route('owner.appointments.index') }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-calendar me-1"></i>Xem Tất Cả
                    </a>
                </div>
                <div class="card-body">
                    @if($upcomingAppointments->count() > 0)
                        @foreach($upcomingAppointments as $appointment)
                        <div class="appointment-card border rounded-3 p-3 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="appointment-avatar rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3"
                                     style="width: 45px; height: 45px; flex-shrink: 0;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $appointment->cusUser->Name ?? 'Khách hàng' }}</h6>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($appointment->AppointmentDateStart)->format('d/m/Y H:i') }}
                                    </small>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-house me-1"></i>
                                        {{ $appointment->property->Title ?? $appointment->TitleAppoint }}
                                    </small>
                                    <small class="d-block">
                                        @if($appointment->Status === 'Khởi tạo')
                                            <span class="badge bg-warning">Chờ Xác Nhận</span>
                                        @elseif($appointment->Status === 'Đang Thực hiện')
                                            <span class="badge bg-success">Đã Xác Nhận</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $appointment->Status }}</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-2 mb-0">Không có lịch hẹn sắp tới</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    @if($recentTransactions->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-success">
                        <i class="bi bi-graph-up me-2"></i>Giao Dịch Gần Đây
                    </h5>
                    <a href="{{ route('owner.transactions.index') }}" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-list me-1"></i>Xem Tất Cả
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã GD</th>
                                    <th>Bất Động Sản</th>
                                    <th>Khách Hàng</th>
                                    <th>Loại</th>
                                    <th>Giá Trị</th>
                                    <th>Ngày</th>
                                    <th>Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>
                                        <code>{{ $transaction->TransactionID }}</code>
                                    </td>
                                    <td>
                                        <span class="text-truncate" style="max-width: 150px; display: inline-block;"
                                              title="{{ $transaction->property->Title ?? 'N/A' }}">
                                            {{ $transaction->property->Title ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->trans_cus->Name ?? 'N/A' }}</td>
                                    <td>
                                        @if($transaction->TransactionType === 'Sale')
                                            <span class="badge bg-primary">Bán</span>
                                        @else
                                            <span class="badge bg-info">Thuê</span>
                                        @endif
                                    </td>
                                    <td class="text-success fw-bold">
                                        {{ number_format($transaction->TotalPrice, 0, ',', '.') }} VNĐ
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->TransactionDate)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($transaction->TranStatus === 'Paid')
                                            <span class="badge bg-success">Đã Thanh Toán</span>
                                        @else
                                            <span class="badge bg-warning">{{ $transaction->TranStatus }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0;
}

.stat-label {
    font-size: 0.9rem;
    font-weight: 500;
}

.property-card, .appointment-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef !important;
}

.property-card:hover, .appointment-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.property-thumbnail {
    width: 100%;
    height: 80px;
    object-fit: cover;
}

.appointment-avatar {
    font-size: 1.2rem;
}

.revenue-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #28a745;
}

.revenue-icon {
    opacity: 0.8;
}

@media (max-width: 768px) {
    .stat-number {
        font-size: 2rem;
    }

    .property-card .row {
        --bs-gutter-x: 0.5rem;
    }
}
</style>
@endsection
