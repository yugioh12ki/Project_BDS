@extends('_layout._layagent.app')

@section('title', 'Lịch Hẹn Xem Nhà')

@section('appointments')
<style>
    /* Custom styles for appointment cards */
    .appointment-card {
        border-radius: 12px;
        border: 1px solid #e9ecef;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background-color: #ffffff;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .appointment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .appointment-card .card-header {
        background-color: #ffffff;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .appointment-card .card-body {
        padding: 0.5rem 1.25rem 1.25rem;
    }

    .appointment-card .card-footer {
        background-color: #ffffff;
        border-top: 1px solid rgba(0,0,0,.05);
        padding: 0.75rem 1.25rem;
    }

    .icon-wrapper {
        width: 38px;
        height: 38px;
        background-color: rgba(74, 108, 247, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #4a6cf7;
        font-size: 1rem;
        margin-right: 1rem;
    }

    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
        border-radius: 6px;
        font-size: 0.75rem;
    }

    .badge.bg-warning {
        background-color: #fff8e1 !important;
        color: #ff9800 !important;
    }

    .badge.bg-success {
        background-color: #e8f5e9 !important;
        color: #4caf50 !important;
    }

    .badge.bg-danger {
        background-color: #fdecea !important;
        color: #f44336 !important;
    }

    .badge.bg-info {
        background-color: #e3f2fd !important;
        color: #2196f3 !important;
    }

    .person-card {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #f0f0f0;
    }
    
    .appointment-info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }
    
    .appointment-info-content {
        flex: 1;
    }
    
    .appointment-info-label {
        font-weight: 600;
        margin-bottom: 0.15rem;
        color: #495057;
    }
    
    .appointment-info-value {
        color: #6c757d;
    }
    
    /* Property search dropdown styling */
    #propertyList .dropdown-item,
    #customerList .dropdown-item {
        padding: 0.5rem 1rem;
        border-bottom: 1px solid #f5f5f5;
    }
    
    #propertyList .dropdown-item:last-child,
    #customerList .dropdown-item:last-child {
        border-bottom: none;
    }
    
    #propertyList .property-title,
    #customerList .customer-name {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    #propertyList .owner-name,
    #customerList .text-muted {
        font-size: 0.8rem;
        color: #6c757d !important;
    }
    
    #propertyList .dropdown-item:hover,
    #customerList .dropdown-item:hover,
    #propertyList .dropdown-item.active {
        background-color: #f8f9fa;
        color: #212529;
    }
    
    #ownerInfo {
        background-color: #e3f2fd;
        border-color: #90caf9;
        color: #0d47a1;
    }
    
    /* Responsive improvements */
    @media (max-width: 576px) {
        .appointment-card .card-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .appointment-card .card-header div:last-child {
            margin-top: 0.5rem;
        }
        
        .card-footer {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .card-footer .btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Lịch hẹn xem nhà</h1>
                    <p class="text-muted mb-0">Quản lý lịch hẹn xem bất động sản</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                    <i class="bi bi-plus-lg"></i> Tạo lịch hẹn mới
                </button>
            </div>
        </div>
    </div>

    <!-- Filter tabs -->
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#all-appointments" data-bs-toggle="tab">Tất cả lịch hẹn</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#pending-appointments" data-bs-toggle="tab">Chờ xác nhận ({{ $appointments->where('Status', 'Chờ xử lý')->count() }})</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#confirmed-appointments" data-bs-toggle="tab">Đã xác nhận ({{ $appointments->where('Status', 'Thành công')->count() }})</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#cancelled-appointments" data-bs-toggle="tab">Đã hủy ({{ $appointments->where('Status', 'Đã hủy')->count() }})</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content">
        <!-- All appointments tab -->
        <div class="tab-pane fade show active" id="all-appointments">
            <div class="row">
                @forelse($appointments as $appointment)
                <div class="col-md-6 col-xl-4 mb-3">
                    <div class="card appointment-card h-100">
                        <div class="card-header border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar2-week text-primary me-2"></i>
                                <h5 class="card-title mb-0">Lịch hẹn - {{ Str::limit($appointment->property->Title ?? 'Bất động sản không xác định', 30) }}</h5>
                            </div>
                            <div>
                                @switch($appointment->Status)
                                    @case('Chờ xử lý')
                                        <span class="badge bg-warning">Chờ xác nhận</span>
                                        @break
                                    @case('Thành công')
                                        <span class="badge bg-success">Đã xác nhận</span>
                                        @break
                                    @case('Đã hủy')
                                        <span class="badge bg-danger">Đã hủy</span>
                                        @break
                                    @case('Hoàn thành')
                                        <span class="badge bg-info">Hoàn thành</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $appointment->Status }}</span>
                                @endswitch
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <!-- Ngày hẹn -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Ngày hẹn</div>
                                    <div class="appointment-info-value">{{ date('d/m/Y', strtotime($appointment->AppointmentDateStart)) }}</div>
                                </div>
                            </div>
                            
                            <!-- Thời gian -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Thời gian</div>
                                    <div class="appointment-info-value">
                                        {{ date('H:i', strtotime($appointment->AppointmentDateStart)) }} - 
                                        {{ date('H:i', strtotime($appointment->AppointmentDateEnd)) }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Người mời gặp -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Người mời gặp</div>
                                    @if($appointment->ownerUser)
                                        <div class="appointment-info-value">{{ $appointment->ownerUser->Name }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-telephone-fill"></i> {{ $appointment->ownerUser->Phone ?? 'Không có SĐT' }}
                                        </div>
                                    @else
                                        <div class="text-muted">Không xác định</div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Khách hàng -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Khách hàng</div>
                                    @if($appointment->cusUser)
                                        <div class="appointment-info-value">{{ $appointment->cusUser->Name }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-telephone-fill"></i> {{ $appointment->cusUser->Phone ?? 'Không có SĐT' }}
                                        </div>
                                    @else
                                        <div class="text-muted">Không xác định</div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Nội dung -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Nội dung</div>
                                    <div class="mb-1">{{ $appointment->TitleAppoint }}</div>
                                    <div class="description-text text-muted">
                                        {{ Str::limit($appointment->DescAppoint, 100) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between py-3">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal{{ $appointment->AppointmentID }}">
                                <i class="bi bi-eye"></i> Chi tiết
                            </button>
                            <div>
                                @if($appointment->Status == 'Chờ xử lý')
                                    <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Thành công">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-lg"></i> Xác nhận
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Đã hủy">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-x-lg"></i> Từ chối
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Appointment Detail Modal -->
                <div class="modal fade" id="appointmentDetailModal{{ $appointment->AppointmentID }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title d-flex align-items-center">
                                    <i class="bi bi-calendar2-week text-primary me-2"></i>
                                    Chi tiết lịch hẹn
                                    
                                    <span class="ms-2">
                                        @switch($appointment->Status)
                                            @case('Chờ xử lý')
                                                <span class="badge bg-warning">Chờ xác nhận</span>
                                                @break
                                            @case('Thành công')
                                                <span class="badge bg-success">Đã xác nhận</span>
                                                @break
                                            @case('Đã hủy')
                                                <span class="badge bg-danger">Đã hủy</span>
                                                @break
                                            @case('Hoàn thành')
                                                <span class="badge bg-info">Hoàn thành</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $appointment->Status }}</span>
                                        @endswitch
                                    </span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h5 class="mb-3">{{ $appointment->property->Title ?? 'Bất động sản không xác định' }}</h5>
                                <p class="text-muted mb-4">
                                    <i class="bi bi-geo-alt"></i> 
                                    {{ $appointment->property->Address ?? '' }}{{ isset($appointment->property->Ward) ? ', '.$appointment->property->Ward : '' }}{{ isset($appointment->property->District) ? ', '.$appointment->property->District : '' }}
                                </p>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3">
                                        <div class="appointment-info-item">
                                            <div class="icon-wrapper">
                                                <i class="bi bi-calendar3"></i>
                                            </div>
                                            <div class="appointment-info-content">
                                                <div class="appointment-info-label">Ngày hẹn</div>
                                                <div class="appointment-info-value">{{ date('d/m/Y', strtotime($appointment->AppointmentDateStart)) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="appointment-info-item">
                                            <div class="icon-wrapper">
                                                <i class="bi bi-clock"></i>
                                            </div>
                                            <div class="appointment-info-content">
                                                <div class="appointment-info-label">Thời gian</div>
                                                <div class="appointment-info-value">
                                                    {{ date('H:i', strtotime($appointment->AppointmentDateStart)) }} - 
                                                    {{ date('H:i', strtotime($appointment->AppointmentDateEnd)) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="appointment-info-item">
                                            <div class="icon-wrapper">
                                                <i class="bi bi-geo-alt"></i>
                                            </div>
                                            <div class="appointment-info-content">
                                                <div class="appointment-info-label">Địa điểm</div>
                                                <div class="appointment-info-value">Tại bất động sản</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card person-card mb-3">
                                            <div class="card-header">
                                                <h6 class="mb-0 d-flex align-items-center">
                                                    <i class="bi bi-person me-2"></i> Người mời gặp
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                @if($appointment->ownerUser)
                                                    <div class="person-name mb-1">{{ $appointment->ownerUser->Name }}</div>
                                                    <div class="person-phone">
                                                        <i class="bi bi-telephone-fill"></i> {{ $appointment->ownerUser->Phone ?? 'Không có SĐT' }}
                                                    </div>
                                                @else
                                                    <div class="text-muted">Không xác định</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card person-card">
                                            <div class="card-header">
                                                <h6 class="mb-0 d-flex align-items-center">
                                                    <i class="bi bi-people me-2"></i> Khách hàng tiềm năng
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                @if($appointment->cusUser)
                                                    <div class="person-name mb-1">{{ $appointment->cusUser->Name }}</div>
                                                    <div class="person-phone">
                                                        <i class="bi bi-telephone-fill"></i> {{ $appointment->cusUser->Phone ?? 'Không có SĐT' }}
                                                    </div>
                                                @else
                                                    <div class="text-muted">Không xác định</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card mb-0">
                                    <div class="card-header">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <i class="bi bi-journal-text me-2"></i> Ghi chú
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="mb-3">{{ $appointment->TitleAppoint }}</h5>
                                        <p class="mb-0">{{ $appointment->DescAppoint }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                @if($appointment->Status == 'Chờ xử lý')
                                    <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Thành công">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-lg"></i> Xác nhận lịch hẹn
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="Đã hủy">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-x-lg"></i> Từ chối
                                        </button>
                                    </form>
                                @elseif($appointment->Status == 'Thành công')
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                @else
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div>
                            Không có lịch hẹn nào. Bạn có thể tạo lịch hẹn mới bằng cách nhấn vào nút "Tạo lịch hẹn mới".
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Pending appointments tab -->
        <div class="tab-pane fade" id="pending-appointments">
            <div class="row">
                @forelse($appointments->where('Status', 'Chờ xử lý') as $appointment)
                <div class="col-md-6 col-xl-4 mb-3">
                    <div class="card appointment-card h-100">
                        <div class="card-header border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar2-week text-primary me-2"></i>
                                <h5 class="card-title mb-0">Lịch hẹn - {{ Str::limit($appointment->property->Title ?? 'Bất động sản không xác định', 30) }}</h5>
                            </div>
                            <div>
                                <span class="badge bg-warning">Chờ xác nhận</span>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <!-- Ngày hẹn -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Ngày hẹn</div>
                                    <div class="appointment-info-value">{{ date('d/m/Y', strtotime($appointment->AppointmentDateStart)) }}</div>
                                </div>
                            </div>
                            
                            <!-- Thời gian -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Thời gian</div>
                                    <div class="appointment-info-value">
                                        {{ date('H:i', strtotime($appointment->AppointmentDateStart)) }} - 
                                        {{ date('H:i', strtotime($appointment->AppointmentDateEnd)) }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Người mời gặp -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Người mời gặp</div>
                                    @if($appointment->ownerUser)
                                        <div class="appointment-info-value">{{ $appointment->ownerUser->Name }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-telephone-fill"></i> {{ $appointment->ownerUser->Phone ?? 'Không có SĐT' }}
                                        </div>
                                    @else
                                        <div class="text-muted">Không xác định</div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Khách hàng -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Khách hàng</div>
                                    @if($appointment->cusUser)
                                        <div class="appointment-info-value">{{ $appointment->cusUser->Name }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-telephone-fill"></i> {{ $appointment->cusUser->Phone ?? 'Không có SĐT' }}
                                        </div>
                                    @else
                                        <div class="text-muted">Không xác định</div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Nội dung -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Nội dung</div>
                                    <div class="mb-1">{{ $appointment->TitleAppoint }}</div>
                                    <div class="description-text text-muted">
                                        {{ Str::limit($appointment->DescAppoint, 100) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between py-3">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal{{ $appointment->AppointmentID }}">
                                <i class="bi bi-eye"></i> Chi tiết
                            </button>
                            <div>
                                <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="Thành công">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-lg"></i> Xác nhận
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="Đã hủy">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-x-lg"></i> Từ chối
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div>
                            Không có lịch hẹn nào đang chờ xác nhận.
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Confirmed appointments tab -->
        <div class="tab-pane fade" id="confirmed-appointments">
            <div class="row">
                @forelse($appointments->where('Status', 'Thành công') as $appointment)
                <div class="col-md-6 col-xl-4 mb-3">
                    <div class="card appointment-card h-100">
                        <div class="card-header border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar2-week text-primary me-2"></i>
                                <h5 class="card-title mb-0">Lịch hẹn - {{ Str::limit($appointment->property->Title ?? 'Bất động sản không xác định', 30) }}</h5>
                            </div>
                            <div>
                                <span class="badge bg-success">Đã xác nhận</span>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <!-- Ngày hẹn -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Ngày hẹn</div>
                                    <div class="appointment-info-value">{{ date('d/m/Y', strtotime($appointment->AppointmentDateStart)) }}</div>
                                </div>
                            </div>
                            
                            <!-- Thời gian -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Thời gian</div>
                                    <div class="appointment-info-value">
                                        {{ date('H:i', strtotime($appointment->AppointmentDateStart)) }} - 
                                        {{ date('H:i', strtotime($appointment->AppointmentDateEnd)) }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Người mời gặp -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Người mời gặp</div>
                                    @if($appointment->ownerUser)
                                        <div class="appointment-info-value">{{ $appointment->ownerUser->Name }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-telephone-fill"></i> {{ $appointment->ownerUser->Phone ?? 'Không có SĐT' }}
                                        </div>
                                    @else
                                        <div class="text-muted">Không xác định</div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Khách hàng -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Khách hàng</div>
                                    @if($appointment->cusUser)
                                        <div class="appointment-info-value">{{ $appointment->cusUser->Name }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-telephone-fill"></i> {{ $appointment->cusUser->Phone ?? 'Không có SĐT' }}
                                        </div>
                                    @else
                                        <div class="text-muted">Không xác định</div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Nội dung -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Nội dung</div>
                                    <div class="mb-1">{{ $appointment->TitleAppoint }}</div>
                                    <div class="description-text text-muted">
                                        {{ Str::limit($appointment->DescAppoint, 100) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between py-3">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal{{ $appointment->AppointmentID }}">
                                <i class="bi bi-eye"></i> Chi tiết
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div>
                            Không có lịch hẹn nào đã được xác nhận.
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Cancelled appointments tab -->
        <div class="tab-pane fade" id="cancelled-appointments">
            <div class="row">
                @forelse($appointments->where('Status', 'Đã hủy') as $appointment)
                <div class="col-md-6 col-xl-4 mb-3">
                    <div class="card appointment-card h-100 border-danger-subtle">
                        <div class="card-header border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar2-week text-primary me-2"></i>
                                <h5 class="card-title mb-0">Lịch hẹn - {{ Str::limit($appointment->property->Title ?? 'Bất động sản không xác định', 30) }}</h5>
                            </div>
                            <div>
                                <span class="badge bg-danger">Đã hủy</span>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <!-- Ngày hẹn -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Ngày hẹn</div>
                                    <div class="appointment-info-value">{{ date('d/m/Y', strtotime($appointment->AppointmentDateStart)) }}</div>
                                </div>
                            </div>
                            
                            <!-- Thời gian -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Thời gian</div>
                                    <div class="appointment-info-value">
                                        {{ date('H:i', strtotime($appointment->AppointmentDateStart)) }} - 
                                        {{ date('H:i', strtotime($appointment->AppointmentDateEnd)) }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Người mời gặp -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Người mời gặp</div>
                                    @if($appointment->ownerUser)
                                        <div class="appointment-info-value">{{ $appointment->ownerUser->Name }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-telephone-fill"></i> {{ $appointment->ownerUser->Phone ?? 'Không có SĐT' }}
                                        </div>
                                    @else
                                        <div class="text-muted">Không xác định</div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Khách hàng -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Khách hàng</div>
                                    @if($appointment->cusUser)
                                        <div class="appointment-info-value">{{ $appointment->cusUser->Name }}</div>
                                        <div class="text-muted small">
                                            <i class="bi bi-telephone-fill"></i> {{ $appointment->cusUser->Phone ?? 'Không có SĐT' }}
                                        </div>
                                    @else
                                        <div class="text-muted">Không xác định</div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Nội dung -->
                            <div class="appointment-info-item">
                                <div class="icon-wrapper">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div class="appointment-info-content">
                                    <div class="appointment-info-label">Nội dung</div>
                                    <div class="mb-1">{{ $appointment->TitleAppoint }}</div>
                                    <div class="description-text text-muted">
                                        {{ Str::limit($appointment->DescAppoint, 100) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between py-3">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal{{ $appointment->AppointmentID }}">
                                <i class="bi bi-eye"></i> Chi tiết
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div>
                            Không có lịch hẹn nào đã hủy.
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Create Appointment Modal -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo lịch hẹn mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="appointmentForm" action="{{ route('agent.appointments.create') }}" method="POST">
                    @csrf
                    <div class="mb-3 position-relative">
                        <label class="form-label">Tên bất động sản <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="propertySearch" 
                                   placeholder="Nhập tên bất động sản..." required>
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                        </div>
                        <input type="hidden" name="PropertyID" id="propertyID">
                        <ul id="propertyList" class="dropdown-menu w-100 show" style="display:none; position:absolute; inset: auto 0px 0px; transform: translate(0px, 40px); max-height: 250px; overflow-y: auto; z-index: 1050;">
                        </ul>
                        <div id="ownerInfo" class="mt-2 alert alert-info d-flex align-items-center" style="display: none; border-left: 4px solid #0d6efd; background-color: #e3f2fd;">
                            <i class="bi bi-person-circle me-2 fs-5" style="color: #0d6efd;"></i>
                            <div>
                                <div class="fw-semibold" style="color: #0d6efd;">Chủ sở hữu bất động sản:</div>
                                <strong id="ownerName" class="fs-5"></strong>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customerSearch" 
                               placeholder="Nhập tên khách hàng để tìm..." required>
                        <input type="hidden" name="CusID" id="customerID">
                        <ul id="customerList" class="dropdown-menu w-100 show" style="display:none; position:absolute; inset: auto 0px 0px; transform: translate(0px, 40px);">
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại khách hàng</label>
                        <input type="tel" class="form-control" name="CustomerPhone" readonly
                               placeholder="Số điện thoại sẽ tự động điền khi chọn khách hàng">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tiêu đề cuộc hẹn <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="TitleAppoint" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="DescAppoint" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hẹn <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="AppointmentDateStart" required 
                                       min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="AppointmentTimeStart" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Giờ kết thúc <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="AppointmentTimeEnd" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" form="appointmentForm" class="btn btn-primary">Tạo lịch hẹn</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
// Lấy dữ liệu bất động sản trực tiếp từ controller
window.propertyList = {!! json_encode($propertyList) !!};

// Debug
console.log('Property List loaded:', window.propertyList);
</script>
<script src="{{ asset('js/appointments.js') }}"></script>
@endpush
@endsection