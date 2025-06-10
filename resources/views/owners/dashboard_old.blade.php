@extends('_layout._layowner.app')
@section('title', 'Dashboard - Chủ Sở Hữu')

@section('dashboard')
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 bg-gradient-primary text-white">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-2">Chào mừng, {{ $owner->Name }}!</h3>
                                <p class="mb-0 opacity-75">Quản lý bất động sản và theo dõi các giao dịch của bạn</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="avatar-lg bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-person-circle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="avatar-md bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-house-check text-success fs-4"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-1">{{ $stats['active_properties'] }}</h3>
                        <p class="text-muted mb-0 small">BĐS đang rao bán</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="avatar-md bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                        </div>
                        <h3 class="fw-bold text-warning mb-1">{{ $stats['pending_properties'] }}</h3>
                        <p class="text-muted mb-0 small">BĐS chờ duyệt</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="avatar-md bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-check-circle text-primary fs-4"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-1">{{ $stats['completed_transactions'] }}</h3>
                        <p class="text-muted mb-0 small">Giao dịch hoàn thành</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="avatar-md bg-info bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-event text-info fs-4"></i>
                        </div>
                        <h3 class="fw-bold text-info mb-1">{{ $stats['pending_appointments'] }}</h3>
                        <p class="text-muted mb-0 small">Lịch hẹn sắp tới</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-1">Tổng doanh thu</h5>
                                <h2 class="text-success fw-bold mb-0">
                                    {{ number_format($stats['total_revenue'], 0, ',', '.') }} VNĐ
                                </h2>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="avatar-lg bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-currency-dollar text-success fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Properties and Appointments Row -->
        <div class="row mb-4">
            <!-- Properties Section -->
            <div class="col-lg-7 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary fw-semibold">
                                <i class="bi bi-house-door me-2"></i>Bất động sản của tôi
                            </h5>
                            <a href="{{ route('owner.property.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>Xem tất cả
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($recentProperties->count() > 0)
                            <div class="row g-3">
                                @foreach($recentProperties as $property)
                                    <div class="col-md-6">
                                        <div class="property-card border rounded-3 p-3 h-100">
                                            <div class="row">
                                                <div class="col-4">
                                                    @php
                                                        $image = $property->images->first();
                                                        $imageUrl = $image ? 
                                                            ($image->ImageURL ?: 'data:image/jpeg;base64,' . base64_encode($image->ImagePath)) 
                                                            : asset('images/default-property.jpg');
                                                    @endphp
                                                    <img src="{{ $imageUrl }}" 
                                                         class="img-fluid rounded-2 property-thumbnail" 
                                                         alt="{{ $property->Title }}"
                                                         style="width: 100%; height: 80px; object-fit: cover;">
                                                </div>
                                                <div class="col-8">
                                                    <h6 class="mb-1 fw-semibold text-truncate" title="{{ $property->Title }}">
                                                        {{ Str::limit($property->Title, 25) }}
                                                    </h6>
                                                    <p class="text-muted small mb-1">
                                                        <i class="bi bi-geo-alt me-1"></i>
                                                        {{ $property->District }}
                                                    </p>
                                                    <div class="property-info">
                                                        <small class="d-block text-primary fw-semibold">
                                                            {{ number_format($property->Price, 0, ',', '.') }} VNĐ
                                                        </small>
                                                        @if($property->chiTiet && $property->chiTiet->Area)
                                                            <small class="text-muted">
                                                                {{ $property->chiTiet->Area }}m²
                                                            </small>
                                                        @endif
                                                        <small class="d-block">
                                                            <span class="badge {{ $property->Status == 'active' ? 'bg-success' : 'bg-warning' }} badge-sm">
                                                                {{ $property->Status == 'active' ? 'Đang bán' : 'Chờ duyệt' }}
                                                            </span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="avatar-lg bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-house-add text-muted fs-2"></i>
                                </div>
                                <h6 class="text-muted">Chưa có bất động sản nào</h6>
                                <a href="{{ route('owner.property.index') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="bi bi-plus me-1"></i>Đăng tin bất động sản
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Appointments Section -->
            <div class="col-lg-5 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary fw-semibold">
                                <i class="bi bi-calendar-event me-2"></i>Lịch hẹn sắp tới
                            </h5>
                            <a href="{{ route('owner.appointments.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>Xem tất cả
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($upcomingAppointments->count() > 0)
                            @foreach($upcomingAppointments as $appointment)
                                <div class="appointment-card border rounded-3 p-3 mb-3">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-md bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-calendar-check text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0 fw-semibold">{{ $appointment->TitleAppoint }}</h6>
                                                <span class="badge {{ $appointment->Status == 'Khởi tạo' ? 'bg-warning' : 'bg-info' }} badge-sm">
                                                    {{ $appointment->Status }}
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ \Carbon\Carbon::parse($appointment->AppointmentDateStart)->format('d/m/Y H:i') }}
                                            </p>
                                            <p class="text-muted small mb-2">
                                                <i class="bi bi-person me-1"></i>
                                                {{ $appointment->cusUser ? $appointment->cusUser->Name : 'Khách hàng' }}
                                            </p>
                                            @if($appointment->property)
                                                <p class="text-muted small mb-0">
                                                    <i class="bi bi-house me-1"></i>
                                                    {{ Str::limit($appointment->property->Title, 30) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <div class="avatar-lg bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-calendar-x text-muted fs-2"></i>
                                </div>
                                <h6 class="text-muted">Không có lịch hẹn sắp tới</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        @if($recentTransactions->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-primary fw-semibold">
                                    <i class="bi bi-receipt me-2"></i>Giao dịch gần đây
                                </h5>
                                <a href="{{ route('owner.transactions.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>Xem tất cả
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mã giao dịch</th>
                                            <th>Bất động sản</th>
                                            <th>Khách hàng</th>
                                            <th>Giá trị</th>
                                            <th>Ngày</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTransactions as $transaction)
                                            <tr>
                                                <td>
                                                    <span class="fw-semibold">{{ $transaction->TransactionID }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h6 class="mb-0">{{ Str::limit($transaction->property->Title ?? 'N/A', 30) }}</h6>
                                                            <small class="text-muted">{{ $transaction->TransactionType == 'Sale' ? 'Bán' : 'Thuê' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $transaction->trans_cus->Name ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-success">
                                                        {{ number_format($transaction->TotalPrice, 0, ',', '.') }} VNĐ
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($transaction->TransactionDate)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    <span class="badge {{ $transaction->TranStatus == 'Paid' ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $transaction->TranStatus == 'Paid' ? 'Đã thanh toán' : 'Chờ thanh toán' }}
                                                    </span>
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
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .property-card, .appointment-card {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef !important;
        }
        
        .property-card:hover, .appointment-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-2px);
        }
        
        .avatar-md {
            width: 50px;
            height: 50px;
        }
        
        .avatar-lg {
            width: 80px;
            height: 80px;
        }
        
        .property-thumbnail {
            transition: transform 0.3s ease;
        }
        
        .property-card:hover .property-thumbnail {
            transform: scale(1.05);
        }
        
        .table th {
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        
        .badge-sm {
            font-size: 0.75rem;
        }
    </style>
@endsection

