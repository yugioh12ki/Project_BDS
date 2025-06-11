@extends('_layout._layagent.app')

@section('title', 'Lịch Hẹn Xem Nhà')

@section('appointments')

<div class="content-wrapper">
    <div class="page-header">
        <div class="header-content">
            <div class="header-text">
                <h4 class="mb-0">Quản lý lịch hẹn</h4>
                <p class="text-muted mb-0">Quản lý lịch hẹn giữa chủ sở hữu và khách hàng</p>
            </div>
            <button class="btn btn-primary btn-create-appointment" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                <i class="bi bi-plus me-2"></i>Tạo lịch hẹn mới
            </button>
        </div>
    </div>

    <!-- Message Display Area -->
    <div id="messageContainer" class="mb-3" style="display: none;">
        <div id="successMessage" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
            <i class="bi bi-check-circle-fill me-2"></i>
            <span id="successText"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div id="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <span id="errorText"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div id="warningMessage" class="alert alert-warning alert-dismissible fade show" role="alert" style="display: none;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <span id="warningText"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div id="infoMessage" class="alert alert-info alert-dismissible fade show" role="alert" style="display: none;">
            <i class="bi bi-info-circle-fill me-2"></i>
            <span id="infoText"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <!-- Laravel Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Có lỗi xảy ra:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Tabs điều hướng -->
    <ul class="nav nav-tabs agent-tab-nav mb-3" id="appointmentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-appointments" data-filter="khoitao" type="button" role="tab" aria-controls="pending-appointments" aria-selected="true">
                Khởi Tạo <span class="badge rounded-pill bg-warning text-dark">{{ $appointments->where('Status', 'Khởi Tạo')->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="confirmed-tab" data-bs-toggle="tab" data-bs-target="#confirmed-appointments" data-filter="dangthuchien" type="button" role="tab" aria-controls="confirmed-appointments" aria-selected="false">
                Đang Thực Hiện <span class="badge rounded-pill bg-success text-white">{{ $appointments->where('Status', 'Đang Thực Hiện')->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled-appointments" data-filter="huyhen" type="button" role="tab" aria-controls="cancelled-appointments" aria-selected="false">
                Hủy Hẹn <span class="badge rounded-pill bg-danger text-white">{{ $appointments->where('Status', 'Hủy Hẹn')->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-appointments" data-filter="hoanthanh" type="button" role="tab" aria-controls="completed-appointments" aria-selected="false">
                Hoàn Thành <span class="badge rounded-pill bg-info text-white">{{ $appointments->where('Status', 'Hoàn Thành')->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="filter-wrapper">
        <div class="d-flex align-items-center">
            <div class="position-relative search-input-container w-100">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="propertyOwnerSearch" class="search-box" placeholder="Tìm kiếm bất động sản hoặc chủ sở hữu...">
                <i class="bi bi-x-circle clear-search-icon" id="resetSearch"></i>
                <div id="searchResults" class="property-dropdown" style="display: none;">
                    <ul class="list-unstyled mb-0" id="searchResultsList">
                        <!-- Kết quả tìm kiếm sẽ hiển thị ở đây -->
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Date Range Filter -->
        <div class="d-flex align-items-center gap-3 mt-3">
            <div class="date-filter-container">
                <label class="form-label mb-1">Từ ngày:</label>
                <input type="date" id="dateFrom" class="form-control form-control-sm" value="{{ date('Y-m-01') }}">
            </div>
            <div class="date-filter-container">
                <label class="form-label mb-1">Đến ngày:</label>
                <input type="date" id="dateTo" class="form-control form-control-sm" value="{{ date('Y-m-t') }}">
            </div>
            <div class="align-self-end">
                <button class="btn btn-sm btn-primary" id="applyDateFilter">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="clearDateFilter">
                    <i class="bi bi-x"></i> Xóa
                </button>
            </div>
        </div>
        
        <div>
            <span class="me-2">Lọc theo:</span>
            <select class="filter-dropdown" id="appointmentFilter">
                <option value="all">Tất cả</option>
                <option value="newest">Mới nhất</option>
                <option value="oldest">Cũ nhất</option>
                <option value="property">Theo bất động sản</option>
                <option value="owner">Theo chủ sở hữu</option>
            </select>
        </div>
    </div>
    
    <!-- Bảng phân công bất động sản -->
    <div class="tab-content" id="appointmentTabContent">
        <div class="tab-pane fade show active" id="pending-appointments" role="tabpanel" aria-labelledby="pending-tab" tabindex="0">
            @forelse($appointments->where('Status', 'Khởi Tạo') as $appointment)
            <div class="appointment-card"
                 data-property-id="{{ $appointment->PropertyID }}" 
                 data-owner-id="{{ $appointment->OwnerID }}"
                 data-date="{{ $appointment->AppointmentDateStart }}"
                 data-status="Khởi Tạo">
                <div class="appointment-header">
                    <div class="appointment-date-time me-auto">
                        <span class="appointment-date">{{ date('d-m-Y', strtotime($appointment->AppointmentDateStart)) }}</span>
                        <span class="time-badge">{{ date('H:i', strtotime($appointment->AppointmentDateStart)) }}</span>
                    </div>
                    <span class="badge-status pending">Khởi tạo</span>
                </div>
                <div class="appointment-detail">
                    <div class="property-info">
                        <div class="property-name">
                            <i class="bi bi-buildings me-1"></i>
                            {{ $appointment->property->Title ?? 'Bất động sản không xác định' }}
                        </div>
                        <div class="appointment-meta">
                            Chủ sở hữu: {{ $appointment->ownerUser->Name ?? 'Không xác định' }} |
                            Khách hàng: {{ $appointment->cusUser->Name ?? 'Không xác định' }}
                        </div>
                    </div>
                    <div class="appointment-actions">
                        
                        <button class="btn btn-action btn-view" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal{{ $appointment->AppointmentID }}">
                            <i class="bi bi-eye me-1"></i> Chi tiết
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state-container">
                <i class="bi bi-calendar-x mb-3" style="font-size: 3rem;"></i>
                <p>Không có lịch hẹn khởi tạo</p>
            </div>
            @endforelse
        </div>

        <div class="tab-pane fade" id="confirmed-appointments" role="tabpanel" aria-labelledby="confirmed-tab" tabindex="0">
            @forelse($appointments->where('Status', 'Đang Thực Hiện') as $appointment)
            <div class="appointment-card"
                 data-property-id="{{ $appointment->PropertyID }}" 
                 data-owner-id="{{ $appointment->OwnerID }}"
                 data-date="{{ $appointment->AppointmentDateStart }}"
                 data-status="Đang Thực Hiện">
                <div class="appointment-header">
                    <div class="appointment-date-time me-auto">
                        <span class="appointment-date">{{ date('d-m-Y', strtotime($appointment->AppointmentDateStart)) }}</span>
                        <span class="time-badge">{{ date('H:i', strtotime($appointment->AppointmentDateStart)) }}</span>
                    </div>
                    <span class="badge-status success">Đang Thực Hiện</span>
                </div>
                <div class="appointment-detail">
                    <div class="property-info">
                        <div class="property-name">
                            <i class="bi bi-buildings me-1"></i>
                            {{ $appointment->property->Title ?? 'Bất động sản không xác định' }}
                        </div>
                        <div class="appointment-meta">
                            Chủ sở hữu: {{ $appointment->ownerUser->Name ?? 'Không xác định' }} |
                            Khách hàng: {{ $appointment->cusUser->Name ?? 'Không xác định' }}
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="Hoàn Thành">
                            <button type="submit" class="btn btn-action btn-confirm">
                                <i class="bi bi-check-circle me-1"></i> Hoàn thành
                            </button>
                        </form>
                        
                        <button class="btn btn-action btn-view" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal{{ $appointment->AppointmentID }}">
                            <i class="bi bi-eye me-1"></i> Chi tiết
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state-container">
                <i class="bi bi-calendar-x mb-3" style="font-size: 3rem;"></i>
                <p>Không có lịch hẹn đang thực hiện</p>
            </div>
            @endforelse
        </div>

        <div class="tab-pane fade" id="cancelled-appointments" role="tabpanel" aria-labelledby="cancelled-tab" tabindex="0">
            @forelse($appointments->where('Status', 'Hủy Hẹn') as $appointment)
            <div class="appointment-card"
                 data-property-id="{{ $appointment->PropertyID }}" 
                 data-owner-id="{{ $appointment->OwnerID }}"
                 data-date="{{ $appointment->AppointmentDateStart }}"
                 data-status="Hủy Hẹn">
                <div class="appointment-header">
                    <div class="appointment-date-time me-auto">
                        <span class="appointment-date">{{ date('d-m-Y', strtotime($appointment->AppointmentDateStart)) }}</span>
                        <span class="time-badge">{{ date('H:i', strtotime($appointment->AppointmentDateStart)) }}</span>
                    </div>
                    <span class="badge-status cancelled">Hủy Hẹn</span>
                </div>
                <div class="appointment-detail">
                    <div class="property-info">
                        <div class="property-name">
                            <i class="bi bi-buildings me-1"></i>
                            {{ $appointment->property->Title ?? 'Bất động sản không xác định' }}
                        </div>
                        <div class="customer-name">
                            <i class="bi bi-person me-1"></i>
                            Khách hàng: {{ $appointment->cusUser->Name ?? 'Không xác định' }}
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn btn-action btn-view" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal{{ $appointment->AppointmentID }}">
                            <i class="bi bi-eye me-1"></i> Chi tiết
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state-container">
                <i class="bi bi-calendar-x mb-3" style="font-size: 3rem;"></i>
                <p>Không có lịch hẹn đã hủy</p>
            </div>
            @endforelse
        </div>
        
        <div class="tab-pane fade" id="completed-appointments" role="tabpanel" aria-labelledby="completed-tab" tabindex="0">
            @forelse($appointments->where('Status', 'Hoàn Thành') as $appointment)
            <div class="appointment-card"
                 data-property-id="{{ $appointment->PropertyID }}" 
                 data-owner-id="{{ $appointment->OwnerID }}"
                 data-date="{{ $appointment->AppointmentDateStart }}"
                 data-status="Hoàn Thành">
                <div class="appointment-header">
                    <div class="appointment-date-time me-auto">
                        <span class="appointment-date">{{ date('d-m-Y', strtotime($appointment->AppointmentDateStart)) }}</span>
                        <span class="time-badge">{{ date('H:i', strtotime($appointment->AppointmentDateStart)) }}</span>
                    </div>
                    <span class="badge-status completed">Hoàn Thành</span>
                </div>
                <div class="appointment-detail">
                    <div class="property-info">
                        <div class="property-name">
                            <i class="bi bi-buildings me-1"></i>
                            {{ $appointment->property->Title ?? 'Bất động sản không xác định' }}
                        </div>
                        <div class="customer-name">
                            <i class="bi bi-person me-1"></i>
                            Khách hàng: {{ $appointment->cusUser->Name ?? 'Không xác định' }}
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn btn-action btn-view" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal{{ $appointment->AppointmentID }}">
                            <i class="bi bi-eye me-1"></i> Chi tiết
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state-container">
                <i class="bi bi-calendar-x mb-3" style="font-size: 3rem;"></i>
                <p>Không có lịch hẹn hoàn thành</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Include Create Appointment Modal -->
@include('agents.create-appointment-modal')

<!-- Appointment Detail Modals -->
@foreach($appointments as $appointment)
    @include('agents.appointment-detail-modal', ['appointment' => $appointment])
@endforeach

@push('scripts')
<script>
    window.propertyList = {!! json_encode($propertyList) !!};
</script>

@endsection