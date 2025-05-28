@extends('_layout._layagent.app')

@section('title', 'Lịch Hẹn Xem Nhà')

@push('styles')
    @vite(['resources/css/owner-autocomplete.css'])
@endpush

@section('appointments')
<style>
    .content-wrapper {
        padding: 20px;
    }
    
    .search-container {
        margin-bottom: 20px;
    }
    
    .property-list {
        margin-bottom: 20px;
    }
    
    .property-item {
        padding: 10px;
        border: 1px solid #eee;
        margin-bottom: 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .property-item:hover {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    
    .property-item.active {
        border-color: #4a6cf7;
        background-color: rgba(74, 108, 247, 0.05);
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        padding: 10px 15px;
        border: none;
        border-bottom: 2px solid transparent;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #4a6cf7;
        border-bottom-color: #4a6cf7;
        background: transparent;
    }
    
    .appointment-list {
        background: #fff;
        border-radius: 8px;
    }
    
    .appointment-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        transition: all 0.2s;
    }
    
    .appointment-item:last-child {
        border-bottom: none;
    }
    
    .appointment-item:hover {
        background: #f8f9fa;
    }
    
    .date-badge {
        width: 45px;
        height: 45px;
        text-align: center;
        margin-right: 15px;
        background-color: rgba(74, 108, 247, 0.1);
        color: #4a6cf7;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .date-badge .day {
        font-size: 18px;
        font-weight: bold;
        line-height: 1;
    }
    
    .date-badge .month {
        font-size: 10px;
        text-transform: uppercase;
    }
    
    .appointment-content {
        flex: 1;
    }
    
    .status-badge {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-badge.chờ-xử-lý {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-badge.thành-công {
        background: #d4edda;
        color: #155724;
    }
    
    .status-badge.đã-hủy {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status-badge.hoàn-thành {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    
    .action-btn {
        padding: 5px 10px;
        font-size: 14px;
    }
    
    .time-badge {
        background-color: rgba(74, 108, 247, 0.1);
        color: #4a6cf7;
        border-radius: 20px;
        font-size: 12px;
        padding: 3px 10px;
    }
    
    /* Chi tiết lịch hẹn */
    .appointment-detail-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    /* Card màu cho loại trạng thái */
    .appointment-card.pending {
        border-left-color: #ff9800;
    }
    
    .appointment-card.success {
        border-left-color: #4caf50;
    }
    
    .appointment-card.cancelled {
        border-left-color: #f44336;
    }
    
    /* Form tạo lịch hẹn */
    .property-dropdown, .customer-dropdown {
        position: absolute;
        width: 100%;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 9999 !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        top: 100%;
        left: 0;
        margin-top: 2px;
        transition: all 0.2s ease-in-out;
    }
    
    #ownerDropdown, #customerDropdown {
        z-index: 9999 !important;
        display: none;
    }
    
    .property-option, .customer-option, .owner-option {
        padding: 10px 15px;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .property-option:hover, .customer-option:hover, .owner-option:hover {
        background-color: #f0f7ff;
    }
    
    .property-option:last-child, .customer-option:last-child, .owner-option:last-child {
        border-bottom: none;
    }
    
    .owner-option .owner-name {
        font-weight: 500;
        color: #333;
    }
    
    .owner-option .owner-details {
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }
    
    .search-highlight {
        background-color: #ffe8c2;
        padding: 0 2px;
        font-weight: 500;
        border-radius: 2px;
    }
    
    .agent-tab-nav {
        margin-bottom: 20px;
    }
    
    .agent-tab-nav .nav-link {
        color: #555;
        padding: 10px 16px;
    }
    
    /* Thêm style cho phần tìm kiếm */
    #searchResults {
        position: absolute;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 0 0 6px 6px;
        z-index: 1050;
        margin-top: 2px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .search-result-item {
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .search-result-item:hover {
        background-color: #f5f9ff;
    }
    
    .search-result-item:last-child {
        border-bottom: none;
    }
    
    .result-property-title {
        font-weight: 500;
        color: #333;
        display: block;
        font-size: 14px;
        margin-bottom: 3px;
    }
    
    .result-owner-name {
        font-size: 12px;
        color: #6c757d;
    }

    /* Style mới cho giao diện giống hình */
    .appointment-card {
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .appointment-header {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .appointment-date-time {
        display: flex;
        align-items: center;
    }
    
    .appointment-date {
        font-weight: 500;
        margin-right: 10px;
    }
    
    .badge-status {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-status.pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .badge-status.success {
        background-color: #d4edda;
        color: #155724;
    }
    
    .badge-status.cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .badge-status.completed {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .appointment-detail {
        padding: 15px;
        display: flex;
        align-items: center;
    }
    
    .property-info {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    
    .property-name {
        font-weight: 500;
        margin-bottom: 5px;
    }
    
    .customer-name {
        font-size: 14px;
        color: #666;
    }

    .appointment-actions {
        display: flex;
        gap: 8px;
    }

    .filter-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    
    .search-box {
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 8px 15px;
        width: 100%;
        max-width: 300px;
    }
    
    .filter-dropdown {
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 8px 15px;
        background-color: white;
    }
    
    .btn-action {
        border-radius: 6px;
        font-weight: 500;
        padding: 8px 16px;
        font-size: 14px;
    }
    
    .btn-confirm {
        border: 1px solid #28a745;
        color: #28a745;
        background-color: transparent;
    }
    
    .btn-confirm:hover {
        background-color: #28a745;
        color: white;
    }
    
    .btn-cancel {
        border: 1px solid #dc3545;
        color: #dc3545;
        background-color: transparent;
    }
    
    .btn-cancel:hover {
        background-color: #dc3545;
        color: white;
    }
    
    .btn-view {
        border: 1px solid #007bff;
        color: #007bff;
        background-color: transparent;
    }
    
    .btn-view:hover {
        background-color: #007bff;
        color: white;
    }
    
    .property-name {
        font-weight: 500;
    }
    
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #6c757d;
    }

    /* Styles cho input tìm kiếm */
    .search-input-container {
        position: relative;
        max-width: 350px;
    }
    
    .search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 1;
    }
    
    .clear-search-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        cursor: pointer;
        opacity: 0.7;
        z-index: 1;
        display: none;
    }
    
    .clear-search-icon:hover {
        opacity: 1;
    }
    
    .search-box {
        padding-left: 35px;
        padding-right: 35px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        width: 100%;
    }

    /* Styles cho nhóm lịch hẹn */
    .property-group-header,
    .owner-group-header {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #4a6cf7;
    }
    
    .owner-group-header {
        border-left-color: #28a745;
    }
    
    .empty-filtered {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
    }
</style>

<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Quản lý lịch hẹn</h4>
            <p class="text-muted mb-0">Quản lý lịch hẹn giữa chủ sở hữu và khách hàng</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
            <i class="bi bi-plus"></i> Tạo lịch hẹn mới
        </button>
    </div>

    <!-- Tabs điều hướng -->
    <ul class="nav nav-tabs agent-tab-nav mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="#all-appointments" data-bs-toggle="tab">
                Tất cả lịch hẹn
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#pending-appointments" data-bs-toggle="tab">
                Chờ xác nhận <span class="badge rounded-pill bg-warning text-dark">{{ $appointments->where('Status', 'Chờ xử lý')->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#confirmed-appointments" data-bs-toggle="tab">
                Đã xác nhận <span class="badge rounded-pill bg-success text-white">{{ $appointments->where('Status', 'Thành công')->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#cancelled-appointments" data-bs-toggle="tab">
                Đã hủy <span class="badge rounded-pill bg-danger text-white">{{ $appointments->where('Status', 'Đã hủy')->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#completed-appointments" data-bs-toggle="tab">
                Hoàn thành <span class="badge rounded-pill bg-info text-white">{{ $appointments->where('Status', 'Hoàn Thành')->count() }}</span>
            </a>
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
    <div class="tab-content">
        <div class="tab-pane fade show active" id="all-appointments">
            @forelse($appointments as $appointment)
            <div class="appointment-card" 
                 data-property-id="{{ $appointment->PropertyID }}" 
                 data-owner-id="{{ $appointment->OwnerID }}"
                 data-date="{{ $appointment->AppointmentDateStart }}">
                <div class="appointment-header">
                    <div class="appointment-date-time me-auto">
                        <span class="appointment-date">{{ date('d-m-Y', strtotime($appointment->AppointmentDateStart)) }}</span>
                        <span class="time-badge">{{ date('H:i', strtotime($appointment->AppointmentDateStart)) }}</span>
                    </div>
                    @switch($appointment->Status)
                        @case('Chờ xử lý')
                            <span class="badge-status pending">Chờ xử lý</span>
                            @break
                        @case('Thành công')
                            <span class="badge-status success">Thành công</span>
                            @break
                        @case('Đã hủy')
                            <span class="badge-status cancelled">Đã hủy</span>
                            @break
                        @case('Hoàn Thành')
                            <span class="badge-status completed">Hoàn thành</span>
                            @break
                        @default
                            <span class="badge-status">{{ $appointment->Status }}</span>
                    @endswitch
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
                        @if($appointment->Status == 'Chờ xử lý')
                            <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="Thành công">
                                <button type="submit" class="btn btn-action btn-confirm">
                                    <i class="bi bi-check-lg me-1"></i> Xác nhận
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="Đã hủy">
                                <button type="submit" class="btn btn-action btn-cancel">
                                    <i class="bi bi-x-lg me-1"></i> Hủy bỏ
                                </button>
                            </form>
                        @endif
                        <button class="btn btn-action btn-view" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal{{ $appointment->AppointmentID }}">
                            <i class="bi bi-eye me-1"></i> Chi tiết
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-calendar-x mb-2" style="font-size: 2rem;"></i>
                <p>Không có lịch hẹn nào</p>
            </div>
            @endforelse
        </div>

        <div class="tab-pane fade" id="pending-appointments">
            @forelse($appointments->where('Status', 'Chờ xử lý') as $appointment)
            <div class="appointment-card"
                 data-property-id="{{ $appointment->PropertyID }}" 
                 data-owner-id="{{ $appointment->OwnerID }}"
                 data-date="{{ $appointment->AppointmentDateStart }}">
                <div class="appointment-header">
                    <div class="appointment-date-time me-auto">
                        <span class="appointment-date">{{ date('d-m-Y', strtotime($appointment->AppointmentDateStart)) }}</span>
                        <span class="time-badge">{{ date('H:i', strtotime($appointment->AppointmentDateStart)) }}</span>
                    </div>
                    <span class="badge-status pending">Chờ xử lý</span>
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
                        <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="Thành công">
                            <button type="submit" class="btn btn-action btn-confirm">
                                <i class="bi bi-check-lg me-1"></i> Xác nhận
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="Đã hủy">
                            <button type="submit" class="btn btn-action btn-cancel">
                                <i class="bi bi-x-lg me-1"></i> Hủy bỏ
                            </button>
                        </form>
                        
                        <button class="btn btn-action btn-view" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal{{ $appointment->AppointmentID }}">
                            <i class="bi bi-eye me-1"></i> Chi tiết
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-calendar-x mb-2" style="font-size: 2rem;"></i>
                <p>Không có lịch hẹn chờ xử lý</p>
            </div>
            @endforelse
        </div>

        <div class="tab-pane fade" id="confirmed-appointments">
            @forelse($appointments->where('Status', 'Thành công') as $appointment)
            <div class="appointment-card"
                 data-property-id="{{ $appointment->PropertyID }}" 
                 data-owner-id="{{ $appointment->OwnerID }}"
                 data-date="{{ $appointment->AppointmentDateStart }}">
                <div class="appointment-header">
                    <div class="appointment-date-time me-auto">
                        <span class="appointment-date">{{ date('d-m-Y', strtotime($appointment->AppointmentDateStart)) }}</span>
                        <span class="time-badge">{{ date('H:i', strtotime($appointment->AppointmentDateStart)) }}</span>
                    </div>
                    <span class="badge-status success">Thành công</span>
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
            <div class="p-4 text-center text-muted">
                <i class="bi bi-calendar-x mb-2" style="font-size: 2rem;"></i>
                <p>Không có lịch hẹn đã xác nhận</p>
            </div>
            @endforelse
        </div>

        <div class="tab-pane fade" id="cancelled-appointments">
            @forelse($appointments->where('Status', 'Đã hủy') as $appointment)
            <div class="appointment-card"
                 data-property-id="{{ $appointment->PropertyID }}" 
                 data-owner-id="{{ $appointment->OwnerID }}"
                 data-date="{{ $appointment->AppointmentDateStart }}">
                <div class="appointment-header">
                    <div class="appointment-date-time me-auto">
                        <span class="appointment-date">{{ date('d-m-Y', strtotime($appointment->AppointmentDateStart)) }}</span>
                        <span class="time-badge">{{ date('H:i', strtotime($appointment->AppointmentDateStart)) }}</span>
                    </div>
                    <span class="badge-status cancelled">Đã hủy</span>
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
            <div class="p-4 text-center text-muted">
                <i class="bi bi-calendar-x mb-2" style="font-size: 2rem;"></i>
                <p>Không có lịch hẹn đã hủy</p>
            </div>
            @endforelse
        </div>
        
        <div class="tab-pane fade" id="completed-appointments">
            @forelse($appointments->where('Status', 'Hoàn Thành') as $appointment)
            <div class="appointment-card"
                 data-property-id="{{ $appointment->PropertyID }}" 
                 data-owner-id="{{ $appointment->OwnerID }}"
                 data-date="{{ $appointment->AppointmentDateStart }}">
                <div class="appointment-header">
                    <div class="appointment-date-time me-auto">
                        <span class="appointment-date">{{ date('d-m-Y', strtotime($appointment->AppointmentDateStart)) }}</span>
                        <span class="time-badge">{{ date('H:i', strtotime($appointment->AppointmentDateStart)) }}</span>
                    </div>
                    <span class="badge-status completed">Hoàn thành</span>
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
            <div class="p-4 text-center text-muted">
                <i class="bi bi-calendar-x mb-2" style="font-size: 2rem;"></i>
                <p>Không có lịch hẹn hoàn thành</p>
            </div>
            @endforelse
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
                <form id="appointmentForm" method="POST" action="{{ route('agent.appointments.create') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Chủ sở hữu <span class="text-danger">*</span></label>
                        <x-owner-autocomplete 
                            id="ownerAutocomplete"
                            name="OwnerID"
                            placeholder="Nhập tên chủ sở hữu..."
                            api-url="{{ route('agent.search.owners') }}"
                            required="true"
                        />
                    </div>

                    <div class="mb-3" id="propertySelectionSection" style="display: none;">
                        <label class="form-label">Bất động sản <span class="text-danger">*</span></label>
                        <select class="form-select" id="propertySelect" name="PropertyID">
                            <option value="">-- Chọn bất động sản --</option>
                        </select>
                        <div class="form-text">Chọn bất động sản của chủ sở hữu để tạo lịch hẹn</div>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customerSearch" placeholder="Nhập tên khách hàng..." autocomplete="off">
                        <input type="hidden" id="customerId" name="CusID">
                        
                        <!-- Dropdown gợi ý khách hàng -->
                        <div id="customerDropdown" class="property-dropdown" style="display: none;">
                            <div id="customerList"></div>
                        </div>
                        
                        <!-- Thông tin khách hàng đã chọn -->
                        <div id="selectedCustomerInfo" class="alert alert-info mt-2" style="display: none;">
                            <i class="bi bi-person-check me-2"></i>
                            Đã chọn: <strong><span id="selectedCustomerName"></span></strong>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="appointmentDateStart" name="AppointmentDateStart" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="appointmentDateEnd" name="AppointmentDateEnd" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="appointmentTitle" name="TitleAppoint" placeholder="Nhập tiêu đề lịch hẹn..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="DescAppoint" rows="3" placeholder="Nhập nội dung cuộc hẹn..." required></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div>
                                <strong>Lưu ý:</strong> Thời gian hẹn sẽ được tự động thông báo cho chủ sở hữu và khách hàng.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="appointmentForm" class="btn btn-primary">Tạo lịch hẹn</button>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Detail Modals -->
@foreach($appointments as $appointment)
    @include('agents.appointment-detail-modal', ['appointment' => $appointment])
@endforeach

@push('scripts')
<script>
    window.propertyList = {!! json_encode($propertyList) !!};
</script>
<script src="{{ asset('js/appointments.js') }}"></script>
<script src="{{ asset('js/appointment-filters.js') }}"></script>
<script>
    $(document).ready(function() {
        // Setup AJAX với CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Customer search functionality
        const $customerSearch = $('#customerSearch');
        const $customerId = $('#customerId');
        const $customerDropdown = $('#customerDropdown');
        const $customerList = $('#customerList');
        const $selectedCustomerInfo = $('#selectedCustomerInfo');
        const $selectedCustomerName = $('#selectedCustomerName');
        
        $customerSearch.on('input', function() {
            const searchTerm = $(this).val().toLowerCase().trim();
            
            if (searchTerm.length < 2) {
                $customerDropdown.hide();
                return;
            }
            
            // Clear previous selection
            $customerId.val('');
            $selectedCustomerInfo.hide();
            
            // Search customers via AJAX
            $.ajax({
                url: '{{ route("agent.search.customers") }}',
                method: 'GET',
                data: { term: searchTerm },
                success: function(response) {
                    $customerList.empty();
                    
                    if (response.customers && response.customers.length > 0) {
                        response.customers.forEach(function(customer) {
                            const customerHtml = `
                                <div class="owner-option" data-customer-id="${customer.id}" data-customer-name="${customer.name}">
                                    <div class="owner-name">${customer.name}</div>
                                    <div class="owner-details">
                                        ${customer.phone ? 'SĐT: ' + customer.phone : ''} 
                                        ${customer.email ? '• Email: ' + customer.email : ''}
                                    </div>
                                </div>
                            `;
                            $customerList.append(customerHtml);
                        });
                        $customerDropdown.show();
                    } else {
                        $customerDropdown.hide();
                    }
                },
                error: function() {
                    console.error('Error searching customers');
                    $customerDropdown.hide();
                }
            });
        });
        
        // Handle customer selection
        $(document).on('click', '.owner-option[data-customer-id]', function() {
            const customerId = $(this).data('customer-id');
            const customerName = $(this).data('customer-name');
            
            $customerSearch.val(customerName);
            $customerId.val(customerId);
            $selectedCustomerName.text(customerName);
            $selectedCustomerInfo.show();
            $customerDropdown.hide();
        });
        
        // Hide customer dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#customerSearch, #customerDropdown').length) {
                $customerDropdown.hide();
            }
        });

        // Tìm kiếm bất động sản hoặc chủ sở hữu
        const $propertyOwnerSearch = $('#propertyOwnerSearch');
        const $searchResults = $('#searchResults');
        const $searchResultsList = $('#searchResultsList');
        const $appointmentCards = $('.appointment-card');
        const $clearSearchIcon = $('.clear-search-icon');
        
        // Ẩn icon xóa ban đầu
        $clearSearchIcon.hide();
        
        // ...existing code...
        
        // Xử lý sự kiện khi nhập vào ô tìm kiếm
        $propertyOwnerSearch.on('input', function() {
            const searchTerm = $(this).val().toLowerCase().trim();
            
            // Hiển thị hoặc ẩn nút xóa
            if(searchTerm.length > 0) {
                $('.clear-search-icon').show();
            } else {
                $('.clear-search-icon').hide();
            }
            
            if (searchTerm.length < 2) {
                $searchResults.hide();
                return;
            }
            
            // Lọc danh sách bất động sản và chủ sở hữu
            const filteredProperties = window.propertyList.filter(property => 
                (property.title && property.title.toLowerCase().includes(searchTerm)) ||
                (property.ownerName && property.ownerName.toLowerCase().includes(searchTerm)) ||
                (property.address && property.address.toLowerCase().includes(searchTerm)) ||
                (property.district && property.district.toLowerCase().includes(searchTerm)) ||
                (property.ward && property.ward.toLowerCase().includes(searchTerm))
            );
            
            // Hiển thị kết quả tìm kiếm
            if (filteredProperties.length > 0) {
                let html = '';
                filteredProperties.forEach(property => {
                    const ownerName = property.ownerName || 'Không xác định';
                    const address = property.address ? `${property.address}, ${property.ward}, ${property.district}` : 'Không có địa chỉ';
                    
                    html += `
                        <li class="search-result-item" data-property-id="${property.id}" data-owner-id="${property.ownerId}">
                            <div class="result-property-title">${property.title}</div>
                            <div class="result-owner-name">
                                <i class="bi bi-person"></i> Chủ sở hữu: ${ownerName}
                            </div>
                            <div class="small text-muted">
                                <i class="bi bi-geo-alt"></i> ${address}
                            </div>
                        </li>
                    `;
                });
                
                $searchResultsList.html(html);
                $searchResults.show();
            } else {
                $searchResultsList.html(`
                    <li class="search-result-item">
                        <div class="text-center py-3">
                            <i class="bi bi-search text-muted mb-2" style="font-size: 1.5rem;"></i>
                            <p class="mb-0">Không tìm thấy kết quả cho "${searchTerm}"</p>
                        </div>
                    </li>
                `);
                $searchResults.show();
            }
        });
        
        // Xử lý sự kiện khi click vào kết quả tìm kiếm
        $(document).on('click', '.search-result-item', function() {
            const propertyId = $(this).data('property-id');
            const ownerId = $(this).data('owner-id');
            
            if (propertyId) {
                filterAppointmentsByProperty(propertyId);
            }
            
            if (ownerId) {
                // Nếu cần lọc theo owner id
                filterAppointmentsByOwner(ownerId);
            }
            
            $searchResults.hide();
            
            // Hiển thị text đã chọn
            const selectedText = $(this).find('.result-property-title').text();
            $propertyOwnerSearch.val(selectedText);
        });
        
        // Đóng kết quả tìm kiếm khi click bên ngoài
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#propertyOwnerSearch, #searchResults').length) {
                $searchResults.hide();
            }
        });
        
        // Xử lý nút Reset tìm kiếm
        $clearSearchIcon.on('click', function() {
            $propertyOwnerSearch.val('');
            $searchResults.hide();
            $appointmentCards.show();
            $(this).hide();
            $('.empty-filtered').hide();
        });
        
        // Hàm lọc lịch hẹn theo bất động sản
        function filterAppointmentsByProperty(propertyId) {
            let foundAny = false;
            
            $appointmentCards.each(function() {
                const cardPropertyId = $(this).data('property-id');
                if (propertyId && cardPropertyId == propertyId) {
                    $(this).show();
                    foundAny = true;
                } else {
                    $(this).hide();
                }
            });
            
            // Hiển thị thông báo nếu không tìm thấy lịch hẹn nào
            const $tabPane = $appointmentCards.first().closest('.tab-pane');
            const $emptyMessage = $tabPane.find('.empty-filtered');
            
            if (!foundAny) {
                if ($emptyMessage.length === 0) {
                    $tabPane.append(`
                        <div class="empty-filtered p-4 text-center text-muted">
                            <i class="bi bi-calendar-x mb-2" style="font-size: 2rem;"></i>
                            <p>Không có lịch hẹn nào cho bất động sản này</p>
                        </div>
                    `);
                } else {
                    $emptyMessage.show();
                }
            } else {
                $('.empty-filtered').hide();
            }
        }
        
        // Hàm lọc lịch hẹn theo chủ sở hữu
        function filterAppointmentsByOwner(ownerId) {
            $appointmentCards.each(function() {
                const cardOwnerId = $(this).data('owner-id');
                if (ownerId && cardOwnerId == ownerId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
        

        

        
        // Xử lý bộ lọc
        $('#appointmentFilter').on('change', function() {
            const filterValue = $(this).val();
            
            // Reset tìm kiếm
            $appointmentCards.show();
            $propertyRows.removeClass('active');
            $propertyOwnerSearch.val('');
            
            if (filterValue === 'newest') {
                // Sắp xếp theo ngày mới nhất
                const sortedCards = $appointmentCards.toArray().sort(function(a, b) {
                    const dateA = new Date($(a).data('date'));
                    const dateB = new Date($(b).data('date'));
                    return dateB - dateA; // Giảm dần
                });
                
                $('#all-appointments').empty().append(sortedCards);
                
            } else if (filterValue === 'oldest') {
                // Sắp xếp theo ngày cũ nhất
                const sortedCards = $appointmentCards.toArray().sort(function(a, b) {
                    const dateA = new Date($(a).data('date'));
                    const dateB = new Date($(b).data('date'));
                    return dateA - dateB; // Tăng dần
                });
                
                $('#all-appointments').empty().append(sortedCards);
            }
        });
        
        // Form validation before submit
        $('#appointmentForm').on('submit', function(e) {
            e.preventDefault();
            
            // Check required fields - use owner autocomplete
            const ownerAutocompleteEl = document.getElementById('ownerAutocomplete');
            const ownerId = ownerAutocompleteEl ? ownerAutocompleteEl.value : '';
            const propertyId = $('#propertySelect').val();
            const customerId = $('#customerId').val();
            const startDate = $('#appointmentDateStart').val();
            const endDate = $('#appointmentDateEnd').val();
            const title = $('#appointmentTitle').val();
            const description = $('textarea[name="DescAppoint"]').val();
            
            // Validation messages
            let errors = [];
            
            if (!ownerId) {
                errors.push('Vui lòng chọn chủ sở hữu');
            }
            
            if (!propertyId) {
                errors.push('Vui lòng chọn bất động sản');
            }
            
            if (!customerId) {
                errors.push('Vui lòng chọn khách hàng');
            }
            
            if (!startDate) {
                errors.push('Vui lòng chọn thời gian bắt đầu');
            }
            
            if (!endDate) {
                errors.push('Vui lòng chọn thời gian kết thúc');
            }
            
            if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
                errors.push('Thời gian kết thúc phải sau thời gian bắt đầu');
            }
            
            if (!title.trim()) {
                errors.push('Vui lòng nhập tiêu đề lịch hẹn');
            }
            
            if (!description.trim()) {
                errors.push('Vui lòng nhập nội dung lịch hẹn');
            }
            
            // Display errors or submit form
            if (errors.length > 0) {
                alert('Lỗi:\n' + errors.join('\n'));
                return false;
            }
            
            // If all validation passes, submit the form
            this.submit();
        });
        
        // Handle form submission success
        @if(session('success'))
            $(document).ready(function() {
                alert('{{ session('success') }}');
                $('#createAppointmentModal').modal('hide');
            });
        @endif
        
        @if(session('error'))
            $(document).ready(function() {
                alert('{{ session('error') }}');
            });
        @endif
    });
</script>

@push('scripts')
    @vite(['resources/js/owner-autocomplete.js'])
    <script>
        // Initialize owner search functionality to match the component
        $('#ownerAutocomplete').on('owner:selected', function(event, ownerId, ownerName) {
            console.log('Owner selected:', ownerId, ownerName);
            
            // Load properties for selected owner
            loadOwnerProperties(ownerId);
        });
        
        // Function to load properties for selected owner
        function loadOwnerProperties(ownerId) {
            $.ajax({
                url: '{{ route("agent.owner.properties") }}',
                method: 'GET',
                data: { ownerId: ownerId },
                success: function(response) {
                    const $propertySelect = $('#propertySelect');
                    $propertySelect.empty().append('<option value="">-- Chọn bất động sản --</option>');
                    
                    if (response.properties && response.properties.length > 0) {
                        response.properties.forEach(function(property) {
                            $propertySelect.append(`
                                <option value="${property.id}">${property.title} - ${property.address || 'Không có địa chỉ'}</option>
                            `);
                        });
                        $('#propertySelectionSection').show();
                    } else {
                        $('#propertySelectionSection').hide();
                        alert('Chủ sở hữu này chưa có bất động sản nào.');
                    }
                },
                error: function() {
                    console.error('Error loading properties');
                    alert('Lỗi khi tải danh sách bất động sản');
                }
            });
        }
    </script>
@endpush
@endsection