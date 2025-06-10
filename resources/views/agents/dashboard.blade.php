@extends('_layout._layagent.app')

@section('title', 'Người Môi Giới')

@section('styles')
<style>
.dashboard-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
}

.performance-card {
    border-radius: 15px;
    transition: all 0.3s ease;
}

.performance-card:hover {
    transform: scale(1.02);
}

.appointment-card {
    border-left: 4px solid #0d6efd;
}

.commission-gradient {
    background: linear-gradient(135deg, #28a745, #20c997);
}
</style>
@endsection

@section('dashboard')
<div class="container-fluid py-4">
    <!-- Header with Quick Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">Bảng điều khiển</h1>
            <p class="text-muted mb-0">Chào mừng trở lại, {{ auth()->user()->Name ?? 'Nguyễn Văn A' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('agent.appointments') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-calendar-plus"></i> Tạo lịch hẹn
            </a>
            <a href="{{ route('agent.transactions') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tạo giao dịch
            </a>
        </div>
    </div>

    <!-- Alert for Today's Appointments -->
    @if($todayAppointments->count() > 0)
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Nhắc nhở:</strong> Bạn có {{ $todayAppointments->count() }} lịch hẹn hôm nay.
        <a href="#today-appointments" class="alert-link">Xem chi tiết</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mt-4">
        <!-- Bất động sản quản lý -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded p-2 me-3">
                            <i class="bi bi-buildings text-primary fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">Bất động sản quản lý</h5>
                    </div>
                    <div class="mt-3 text-center">
                        <h2 class="stat-number text-primary mb-2">{{ $brokerStats['total'] }}</h2>
                        <p class="text-muted mb-0">Bất động sản đang quản lý</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('agent.brokers') }}" class="text-primary text-decoration-none">
                            Xem chi tiết <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch hẹn xem nhà -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded p-2 me-3">
                            <i class="bi bi-calendar-event text-primary fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">Lịch hẹn xem nhà</h5>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Chờ xử lý</span>
                            <span class="text-warning fw-bold">{{ $appointmentStats['pending'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Thành công</span>
                            <span class="text-success fw-bold">{{ $appointmentStats['completed'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Đã hủy</span>
                            <span class="text-danger fw-bold">{{ $appointmentStats['cancelled'] }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('agent.appointments') }}" class="text-primary text-decoration-none">
                            Xem chi tiết <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Giao dịch -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded p-2 me-3">
                            <i class="bi bi-currency-exchange text-primary fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">Giao dịch</h5>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Hoàn thành</span>
                            <span class="text-success fw-bold">{{ $transactionStats['completed'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Đang xử lý</span>
                            <span class="text-warning fw-bold">{{ $transactionStats['processing'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng số</span>
                            <span class="text-primary fw-bold fs-5">{{ $transactionStats['total'] }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('agent.transactions') }}" class="text-primary text-decoration-none">
                            Xem chi tiết <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch hẹn hôm nay -->
    <div class="row mt-2" id="today-appointments">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        Lịch hẹn hôm nay
                        <span class="badge bg-primary ms-2">{{ $todayAppointments->count() }}</span>
                    </h5>

                    @if($todayAppointments->count() > 0)
                        <div class="row">
                            @foreach($todayAppointments as $appointment)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-start border-4 border-primary appointment-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">{{ $appointment->TitleAppoint }}</h6>
                                            <span class="badge
                                                @if($appointment->Status == 'Khởi tạo') bg-secondary
                                                @elseif($appointment->Status == 'Đang Thực hiện') bg-info
                                                @elseif($appointment->Status == 'Hoàn Thành') bg-success
                                                @else bg-danger
                                                @endif">
                                                {{ $appointment->Status }}
                                            </span>
                                        </div>

                                        <div class="small text-muted mb-2">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($appointment->AppointmentDateStart)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($appointment->AppointmentDateEnd)->format('H:i') }}
                                        </div>

                                        @if($appointment->property)
                                        <div class="small text-muted mb-2">
                                            <i class="bi bi-house me-1"></i>
                                            {{ Str::limit($appointment->property->Title, 40) }}
                                        </div>
                                        @endif

                                        @if($appointment->cusUser)
                                        <div class="small text-muted mb-2">
                                            <i class="bi bi-person me-1"></i>
                                            {{ $appointment->cusUser->Name }}
                                            @if($appointment->cusUser->Phone)
                                                <br><i class="bi bi-telephone me-1"></i>{{ $appointment->cusUser->Phone }}
                                            @endif
                                        </div>
                                        @endif

                                        @if($appointment->ownerUser)
                                        <div class="small text-muted">
                                            <i class="bi bi-person-badge me-1"></i>
                                            Chủ nhà: {{ $appointment->ownerUser->Name }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 mb-3"></i>
                            <p>Không có lịch hẹn nào hôm nay</p>
                        </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('agent.appointments') }}" class="text-primary text-decoration-none">
                            Xem tất cả lịch hẹn <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê hoa hồng tháng này -->
    @if($monthlyCommission > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Hoa hồng tháng này</h5>
                            <p class="card-text opacity-75">Tổng hoa hồng đã kiếm được trong {{ now()->format('m/Y') }}</p>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0">{{ number_format($monthlyCommission, 0, ',', '.') }} VNĐ</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Hiệu suất tháng này -->
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card border-success performance-card">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-check-circle-fill text-success fs-2 me-2"></i>
                        <h3 class="mb-0 text-success">{{ $monthlyPerformance['transactions_completed'] }}</h3>
                    </div>
                    <p class="card-text text-muted">Giao dịch hoàn thành tháng này</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-info performance-card">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-calendar-check text-info fs-2 me-2"></i>
                        <h3 class="mb-0 text-info">{{ $monthlyPerformance['appointments_completed'] }}</h3>
                    </div>
                    <p class="card-text text-muted">Lịch hẹn hoàn thành tháng này</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-warning performance-card">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-currency-dollar text-warning fs-2 me-2"></i>
                        <h4 class="mb-0 text-warning">{{ number_format($monthlyPerformance['total_sales_value'], 0, ',', '.') }}</h4>
                    </div>
                    <p class="card-text text-muted">Tổng giá trị giao dịch tháng này (VNĐ)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch hẹn sắp tới -->
    @if($nextAppointment)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-event me-2"></i>
                        Lịch hẹn sắp tới
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="fw-bold">{{ $nextAppointment->TitleAppoint }}</h6>
                            @if($nextAppointment->property)
                            <p class="text-muted mb-1">
                                <i class="bi bi-house me-1"></i>
                                {{ $nextAppointment->property->Title }}
                            </p>
                            <p class="text-muted mb-1">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $nextAppointment->property->Address }}, {{ $nextAppointment->property->Ward }}, {{ $nextAppointment->property->District }}
                            </p>
                            @endif
                            @if($nextAppointment->cusUser)
                            <p class="text-muted mb-1">
                                <i class="bi bi-person me-1"></i>
                                Khách hàng: {{ $nextAppointment->cusUser->Name }}
                                @if($nextAppointment->cusUser->Phone)
                                - <i class="bi bi-telephone me-1"></i>{{ $nextAppointment->cusUser->Phone }}
                                @endif
                            </p>
                            @endif
                            @if($nextAppointment->ownerUser)
                            <p class="text-muted mb-0">
                                <i class="bi bi-person-badge me-1"></i>
                                Chủ nhà: {{ $nextAppointment->ownerUser->Name }}
                                @if($nextAppointment->ownerUser->Phone)
                                - <i class="bi bi-telephone me-1"></i>{{ $nextAppointment->ownerUser->Phone }}
                                @endif
                            </p>
                            @endif
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="mb-2">
                                <span class="badge bg-info">{{ $nextAppointment->Status }}</span>
                            </div>
                            <div class="mb-1">
                                <strong>{{ \Carbon\Carbon::parse($nextAppointment->AppointmentDateStart)->format('d/m/Y') }}</strong>
                            </div>
                            <div class="text-muted">
                                {{ \Carbon\Carbon::parse($nextAppointment->AppointmentDateStart)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($nextAppointment->AppointmentDateEnd)->format('H:i') }}
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($nextAppointment->AppointmentDateStart)->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
