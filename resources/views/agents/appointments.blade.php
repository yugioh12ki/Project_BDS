@extends('_layout._layagent.app')

@section('title', 'Lịch Hẹn Xem Nhà')

@push('styles')
    @vite(['resources/css/owner-autocomplete.css', 'resources/css/customer-autocomplete.css'])
@endpush

@section('appointments')

<style>
    .content-wrapper {
        padding: 70px 25px 40px;
        min-height: 100vh;
        overflow-x: auto;
        background-color: #f8f9fa;
    }
    
    /* Header section styling - Cải thiện hiển thị phần đầu */
    .page-header {
        margin-bottom: 1.5rem;
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
    }
    
    .page-header h4 {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.8rem;
        font-size: 1.75rem;
        line-height: 1.3;
    }
    
    .page-header .text-muted {
        font-size: 1rem;
        line-height: 1.5;
        color: #6c757d;
        margin-bottom: 0;
    }
    
    .btn-create-appointment {
        white-space: nowrap;
        min-width: 180px;
        padding: 12px 20px;
        font-weight: 600;
        font-size: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,123,255,0.3);
        transition: all 0.3s ease;
    }
    
    .btn-create-appointment:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.4);
    }
    
    /* Đảm bảo header không bị khuất */
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .header-text {
        flex: 1;
        min-width: 300px;
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
        margin-bottom: 15px;
        background-color: white;
        border-radius: 10px;
        padding: 5px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
    }
    
    .agent-tab-nav .nav-link {
        color: #6c757d;
        padding: 12px 20px;
        border-radius: 8px;
        margin: 2px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        background: transparent;
    }
    
    .agent-tab-nav .nav-link:hover {
        background-color: #f8f9fa;
        color: #495057;
    }
    
    .agent-tab-nav .nav-link.active {
        background-color: #007bff;
        color: white;
        box-shadow: 0 2px 8px rgba(0,123,255,0.3);
    }
    
    /* Date Filter Styling */
    .date-filter-container {
        min-width: 150px;
    }
    
    .date-filter-container .form-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .date-filter-container .form-control-sm {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    
    .date-filter-container .form-control-sm:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
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

    /* Style mới cho giao diện appointment cards */
    .appointment-card {
        border-radius: 12px;
        margin-bottom: 12px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        overflow: hidden;
        background-color: white;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .appointment-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 25px rgba(0,0,0,0.12);
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
        margin-bottom: 15px;
        background-color: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
        gap: 20px;
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
        background-color: white;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
    }
    
    /* Style cho tab content */
    .tab-content {
        background-color: transparent;
    }
    
    .tab-pane {
        min-height: 200px;
    }
    
    /* Style cho empty state */
    .empty-state-container {
        background-color: white;
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
    }
    
    .empty-state-container i {
        color: #dee2e6;
        margin-bottom: 1rem;
    }
    
    .empty-state-container p {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 0;
    }
    
    /* Responsive adjustments - Cải thiện responsive */
    @media (max-width: 768px) {
        .content-wrapper {
            padding: 60px 15px 30px;
        }
        
        .page-header {
            margin-bottom: 1rem;
            padding: 15px;
        }
        
        .page-header h4 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .header-content {
            flex-direction: column;
            align-items: stretch !important;
            gap: 15px;
        }
        
        .header-text {
            min-width: auto;
        }
        
        .btn-create-appointment {
            width: 100%;
            min-width: auto;
            justify-content: center;
            padding: 15px 20px;
            font-size: 16px;
        }
        
        .filter-wrapper {
            flex-direction: column;
            gap: 15px;
            align-items: stretch !important;
        }
        
        .search-input-container {
            max-width: none !important;
        }
    }
    
    @media (max-width: 576px) {
        .content-wrapper {
            padding: 55px 10px 25px;
        }
        
        .page-header {
            padding: 12px;
            margin-bottom: 0.75rem;
        }
        
        .page-header h4 {
            font-size: 1.3rem;
            line-height: 1.2;
        }
        
        .page-header .text-muted {
            font-size: 0.9rem;
        }
        
        .btn-create-appointment {
            padding: 12px 16px;
            font-size: 14px;
        }
    }
    
    /* Đảm bảo không có overflow trên màn hình nhỏ */
    @media (max-width: 480px) {
        .content-wrapper {
            padding: 50px 8px 20px;
        }
        
        .page-header {
            padding: 10px;
        }
        
        .page-header h4 {
            font-size: 1.2rem;
            word-wrap: break-word;
        }
        
        .page-header .text-muted {
            font-size: 0.85rem;
        }
    }
    
    /* DEMO STYLES - Test các tính năng mới */
    .demo-test-section {
        transition: all 0.3s ease;
    }
    
    .demo-test-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .fix-badge {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
        margin-left: 10px;
    }
    
    .feature-highlight {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .property-selection-demo {
        border: 2px dashed #dee2e6;
        padding: 15px;
        border-radius: 8px;
        background-color: #f8f9fa;
        margin: 15px 0;
    }
    
    .loading-demo {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        display: none;
    }
    
    .demo-animation {
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .demo-animation.show {
        opacity: 1;
    }
    
    /* Enhancement cho loading spinner trong property selection */
    #propertyLoadingSpinner {
        z-index: 10;
    }
    
    #propertySelectionSection {
        transition: all 0.3s ease;
    }
    
    #propertyDetailSection {
        transition: all 0.3s ease;
        border-left: 4px solid #007bff;
    }
    
    /* Success feedback styling */
    .property-feedback-success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 8px 12px;
        border-radius: 5px;
        font-size: 13px;
    }
    
    .property-feedback-error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 8px 12px;
        border-radius: 5px;
        font-size: 13px;
    }
</style>

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
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-appointments" type="button" role="tab" aria-controls="pending-appointments" aria-selected="true">
                Khởi Tạo <span class="badge rounded-pill bg-warning text-dark">{{ $appointments->where('Status', 'Khởi Tạo')->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="confirmed-tab" data-bs-toggle="tab" data-bs-target="#confirmed-appointments" type="button" role="tab" aria-controls="confirmed-appointments" aria-selected="false">
                Đang Thực Hiện <span class="badge rounded-pill bg-success text-white">{{ $appointments->where('Status', 'Đang Thực Hiện')->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled-appointments" type="button" role="tab" aria-controls="cancelled-appointments" aria-selected="false">
                Hủy Hẹn <span class="badge rounded-pill bg-danger text-white">{{ $appointments->where('Status', 'Hủy Hẹn')->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-appointments" type="button" role="tab" aria-controls="completed-appointments" aria-selected="false">
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
<script src="{{ asset('js/appointment-filters.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize cross-component protection for autocomplete fields
        console.log('Initializing cross-component protection for autocomplete fields');
        
        // Create global storage for component data if not exists
        window.autocompleteStorage = window.autocompleteStorage || {};
        
        // Function to safely initialize and access dropdown components
        function initializeAutocompleteComponent(componentId) {
            // Make sure we only initialize once
            if (window.autocompleteStorage[componentId]) {
                return window.autocompleteStorage[componentId];
            }
            
            const $component = $('#' + componentId);
            if ($component.length === 0) {
                console.warn(`Autocomplete component ${componentId} not found in the DOM`);
                return null;
            }
            
            // Store reference to component
            window.autocompleteStorage[componentId] = {
                element: $component,
                lastValue: $component.val(),
                lastId: $('#' + componentId + 'Id').val()
            };
            
            return window.autocompleteStorage[componentId];
        }
        
        // Initialize both components
        const ownerAutocomplete = initializeAutocompleteComponent('ownerAutocomplete');
        const customerAutocomplete = initializeAutocompleteComponent('customerSearch');
        
        // Set up cross-component event handlers if both components exist
        if (ownerAutocomplete && customerAutocomplete) {
            console.log('Setting up cross-component event handlers for owner and customer autocomplete');
            
            // When clicking outside any autocomplete, ensure data preservation
            $(document).on('click', function(e) {
                // If clicked outside both autocomplete components
                if (!$(e.target).closest('.position-relative').length) {
                    // Make sure both components preserve their values
                    if (window.selectedOwnerData && window.selectedOwnerData.id) {
                        $('#ownerAutocompleteId').val(window.selectedOwnerData.id);
                        $('#ownerSearchId').val(window.selectedOwnerData.id);
                    }
                    
                    if (window.selectedCustomerData && window.selectedCustomerData.id) {
                        $('#customerSearchId').val(window.selectedCustomerData.id);
                    }
                }
            });
            
            // Listen for any dropdown show/hide to make sure state is preserved
            const dropdownEvents = ['show.bs.dropdown', 'hide.bs.dropdown', 'shown.bs.dropdown', 'hidden.bs.dropdown'];
            dropdownEvents.forEach(event => {
                $(document).on(event, function() {
                    // Check for preservation of data when any dropdown opens or closes
                    if (window.selectedOwnerData && window.selectedOwnerData.id) {
                        $('#ownerAutocompleteId').val(window.selectedOwnerData.id);
                        $('#ownerSearchId').val(window.selectedOwnerData.id);
                    }
                    
                    if (window.selectedCustomerData && window.selectedCustomerData.id) {
                        $('#customerSearchId').val(window.selectedCustomerData.id);
                    }
                });
            });
        }
    });
</script>    @push('scripts')
    @vite(['resources/js/owner-autocomplete.js'])
    <script>
        // Initialize owner search functionality to match the component
        $('#ownerAutocomplete').on('owner:selected', function(event, ownerId, ownerName, properties, owner) {
            console.log('Event owner:selected received:', { ownerId, ownerName, properties, owner });
            
            // Cập nhật hidden input OwnerID ngay lập tức
            $('#selectedOwnerId').val(ownerId);
            console.log('Updated hidden input selectedOwnerId to:', ownerId);
            
            // Hiển thị section chọn bất động sản với loading
            showPropertySelection();
            
            // Reset property selection
            resetPropertySelection();
            
            // Xử lý dữ liệu bất động sản đã được truyền từ component
            if (properties && properties.length > 0) {
                populatePropertyCombobox(properties, owner);
            } else {
                // Nếu không có dữ liệu, thử gọi API một lần nữa
                loadOwnerPropertiesForCombobox(ownerId);
            }
        });
        
        // Function to show property selection section
        function showPropertySelection() {
            $('#propertySelectionSection').slideDown(300);
            $('#propertyCombobox').prop('disabled', false);
        }
        
        // Function to hide property selection section
        function hidePropertySelection() {
            $('#propertySelectionSection').slideUp(300);
            $('#propertyCombobox').prop('disabled', true);
            $('#propertyDetailSection').hide();
        }
        
        // Function to reset property selection
        function resetPropertySelection() {
            const $propertyCombobox = $('#propertyCombobox');
            $propertyCombobox.empty().append('<option value="">-- Đang tải dữ liệu bất động sản... --</option>');
            $propertyCombobox.prop('disabled', true);
            $('#propertySelectFeedback').empty();
            $('#propertyDetailSection').hide();
            showLoadingSpinner();
        }
        
        // Function to show loading spinner
        function showLoadingSpinner() {
            $('#propertyLoadingSpinner').show();
        }
        
        // Function to hide loading spinner
        function hideLoadingSpinner() {
            $('#propertyLoadingSpinner').hide();
        }
        
        // Lưu trữ dữ liệu bất động sản và chủ sở hữu (Global scope)
        window.propertiesData = [];
        window.ownerData = null;
        
        // Function to populate property combobox với dữ liệu đã có
        function populatePropertyCombobox(properties, owner) {
            console.log('Populating property combobox with:', properties);
            
            const $propertyCombobox = $('#propertyCombobox');
            
            // Lưu dữ liệu vào global scope
            window.propertiesData = properties || [];
            window.ownerData = owner || null;
            
            // Đảm bảo owner ID được cập nhật
            if (window.ownerData && window.ownerData.id) {
                $('#selectedOwnerId').val(window.ownerData.id);
            }
            
            // Clear combobox và ẩn loading
            $propertyCombobox.empty();
            hideLoadingSpinner();
            
            if (window.propertiesData.length > 0) {
                // Thêm option mặc định
                $propertyCombobox.append('<option value="">-- Chọn bất động sản --</option>');
                
                // Thêm từng bất động sản vào combobox
                window.propertiesData.forEach(function(property, index) {
                    console.log(`Property ${index + 1}:`, property);
                    
                    const title = property.title || 'Không có tiêu đề';
                    let optionLabel = title;
                    
                    // Thêm loại bất động sản nếu có
                    if (property.categoryName) {
                        optionLabel += ` (${property.categoryName})`;
                    }
                    
                    // Thêm địa chỉ nếu có
                    if (property.fullAddress || property.address) {
                        optionLabel += ` - ${property.fullAddress || property.address}`;
                    }
                    
                    // Thêm giá nếu có
                    if (property.formattedPrice) {
                        optionLabel += ` - ${property.formattedPrice}`;
                    }
                    
                    // Ensure property has ownerId if it doesn't already have one
                    if (!property.ownerId && window.ownerData && window.ownerData.id) {
                        property.ownerId = window.ownerData.id;
                    }
                    
                    // Store complete property data in a data attribute for easier access
                    $propertyCombobox.append(
                        $('<option>')
                            .val(property.id)
                            .text(optionLabel)
                            .data('property', property)
                    );
                });
                
                // Enable combobox
                $propertyCombobox.prop('disabled', false);
                
                // Thông báo thành công
                $('#propertySelectFeedback').html(`<span class="text-success">
                    <i class="bi bi-check-circle"></i> Tìm thấy ${window.propertiesData.length} bất động sản
                </span>`);
                
                // Nếu chỉ có một bất động sản, tự động chọn
                if (window.propertiesData.length === 1) {
                    $propertyCombobox.val(window.propertiesData[0].id);
                    console.log('Auto-selected property:', window.propertiesData[0].id);
                    
                    // Trigger change event để cập nhật UI
                    $propertyCombobox.trigger('change');
                }
            } else {
                // Không có bất động sản
                $propertyCombobox.append('<option value="">-- Chủ sở hữu này chưa có bất động sản nào --</option>');
                $propertyCombobox.prop('disabled', true);
                
                $('#propertySelectFeedback').html(`<span class="text-warning">
                    <i class="bi bi-exclamation-triangle"></i> Chủ sở hữu này chưa có bất động sản nào
                </span>`);
            }
        }
        
        // Backup function to load properties via API (nếu cần thiết)
        function loadOwnerPropertiesForCombobox(ownerId) {
            console.log('Gọi API để lấy bất động sản cho ownerId:', ownerId);
            
            const $propertyCombobox = $('#propertyCombobox');
            showLoadingSpinner();
            
            // Đảm bảo giá trị OwnerID luôn được cập nhật ngay lập tức
            $('#selectedOwnerId').val(ownerId);
            
            $.ajax({
                url: '{{ route("agent.owner.properties") }}',
                method: 'GET',
                data: { ownerId: ownerId },
                success: function(response) {
                    console.log('API Response:', response);
                    
                    // Lưu lại dữ liệu chủ sở hữu và bất động sản để sử dụng sau
                    window.propertiesData = response.properties || [];
                    window.ownerData = response.owner || null;
                    
                    console.log('Số lượng bất động sản:', window.propertiesData.length);
                    if (window.propertiesData.length > 0) {
                        console.log('Bất động sản đầu tiên:', window.propertiesData[0]);
                    }
                    
                    // Đảm bảo ID chủ sở hữu luôn được lưu đúng
                    if (window.ownerData && window.ownerData.id) {
                        $('#selectedOwnerId').val(window.ownerData.id);
                    } else {
                        // Sử dụng ownerId từ tham số nếu không có trong phản hồi
                        $('#selectedOwnerId').val(ownerId);
                    }
                    
                    // Populate combobox với dữ liệu nhận được
                    populatePropertyCombobox(window.propertiesData, window.ownerData);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading properties:', error);
                    console.error('Response:', xhr.responseText);
                    
                    window.propertiesData = [];
                    window.ownerData = null;
                    
                    hideLoadingSpinner();
                    $propertyCombobox.empty().append('<option value="">-- Lỗi khi tải dữ liệu --</option>');
                    $propertyCombobox.prop('disabled', true);
                    
                    $('#propertySelectFeedback').html(`<span class="text-danger">
                        <i class="bi bi-x-circle"></i> Lỗi khi tải danh sách bất động sản: ${error}
                    </span>`);
                }
            });
        }
        
        // Xử lý khi người dùng chọn một bất động sản từ combobox
        $('#propertyCombobox').change(function() {
            const propertyId = $(this).val();
            
            if (!propertyId) {
                $('#propertyDetailSection').hide();
                return;
            }
            
            console.log('Chọn bất động sản ID:', propertyId);
            
            // Lấy dữ liệu được nhúng trong option
            const selectedOption = $(this).find('option:selected');
            let selectedProperty;
            
            try {
                // Thử lấy dữ liệu từ data attribute trước
                selectedProperty = selectedOption.data('property');
                if (typeof selectedProperty === 'string') {
                    selectedProperty = JSON.parse(selectedProperty);
                }
            } catch (e) {
                console.error('Lỗi khi parse dữ liệu property từ option:', e);
            }
            
            // Nếu không có dữ liệu từ data attribute, tìm từ window.propertiesData
            if (!selectedProperty) {
                console.log('Tìm dữ liệu bất động sản từ danh sách đã lưu');
                selectedProperty = window.propertiesData.find(p => p.id == propertyId);
            }
            
            console.log('Bất động sản được chọn:', selectedProperty);
            
            // CẬP NHẬT QUAN TRỌNG: Cập nhật OwnerID nếu chúng ta có thông tin chủ sở hữu từ property
            if (propertyId && selectedProperty && selectedProperty.ownerId) {
                $('#selectedOwnerId').val(selectedProperty.ownerId);
                console.log('Đã cập nhật selectedOwnerId hidden input thành:', selectedProperty.ownerId);
            } else {
                console.warn('Không tìm thấy ownerId cho property này, kiểm tra dữ liệu property');
            }
            
            if (selectedProperty) {
                showPropertyDetails(selectedProperty);
                
                // Cập nhật thông tin liên quan cho cuộc hẹn
                updateAppointmentPropertyInfo(selectedProperty);
            } else {
                console.error('Không tìm thấy dữ liệu cho bất động sản ID:', propertyId);
                $('#propertyDetailSection').hide();
                $('#propertySelectFeedback').html(`<span class="text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Không tìm thấy thông tin chi tiết về bất động sản
                </span>`);
            }
        });
        
        // Function to show property details
        function showPropertyDetails(selectedProperty) {
            // Hiển thị thông tin chi tiết bất động sản
            const title = selectedProperty.title || 'Không có tiêu đề';
            $('#propertyDetailTitle').text(title);
            
            // Hiển thị địa chỉ đầy đủ nếu có
            const addressText = selectedProperty.fullAddress || selectedProperty.address || 'Không có địa chỉ';
            $('#propertyDetailAddress').text(addressText);
            
            // Hiển thị thêm thông tin giá và loại nếu có
            let extraInfo = [];
            
            if (selectedProperty.categoryName) {
                extraInfo.push(`<span class="badge bg-info me-1">${selectedProperty.categoryName}</span>`);
            }
            
            if (selectedProperty.formattedPrice) {
                extraInfo.push(`<span class="badge bg-success">${selectedProperty.formattedPrice}</span>`);
            }
            
            if (selectedProperty.area) {
                extraInfo.push(`<span class="badge bg-secondary">${selectedProperty.area} m²</span>`);
            }
            
            if (selectedProperty.bedroom) {
                extraInfo.push(`<span class="badge bg-secondary">${selectedProperty.bedroom} phòng ngủ</span>`);
            }
            
            // Thêm trạng thái bất động sản
            if (selectedProperty.status) {
                let statusClass = 'bg-secondary';
                if (selectedProperty.status === 'active') statusClass = 'bg-success';
                if (selectedProperty.status === 'pending') statusClass = 'bg-warning';
                if (selectedProperty.status === 'inactive') statusClass = 'bg-danger';
                
                const statusText = selectedProperty.status === 'active' ? 'Đang bán/cho thuê' : 
                                  (selectedProperty.status === 'pending' ? 'Đang chờ duyệt' : 
                                  (selectedProperty.status === 'inactive' ? 'Đã khóa' : selectedProperty.status));
                
                extraInfo.push(`<span class="badge ${statusClass}">${statusText}</span>`);
            }
            
            // Thêm loại giao dịch (bán/thuê)
            if (selectedProperty.type) {
                const typeText = selectedProperty.type === 'Rent' ? 'Cho thuê' : 'Bán';
                const typeClass = selectedProperty.type === 'Rent' ? 'bg-primary' : 'bg-danger';
                extraInfo.push(`<span class="badge ${typeClass}">${typeText}</span>`);
            }
            
            // Cập nhật thông tin bổ sung
            if (extraInfo.length > 0) {
                $('#propertyExtraInfo').html(extraInfo.join(' ')).show();
            } else {
                $('#propertyExtraInfo').hide();
            }
            
            $('#propertyDetailSection').slideDown(300);
        }
        
        // Cập nhật thông tin cuộc hẹn dựa trên bất động sản đã chọn
        function updateAppointmentPropertyInfo(property) {
            if (!property) {
                console.error('updateAppointmentPropertyInfo called with null/undefined property');
                return;
            }
            
            console.log('Cập nhật thông tin cuộc hẹn với bất động sản:', property);
            
            // Đảm bảo không có giá trị null/undefined
            const title = property.title || 'Không có tiêu đề';
            const address = property.fullAddress || property.address || 'Không có địa chỉ';
            const propertyType = property.categoryName || property.type || '';
            const price = property.formattedPrice || '';
            
            // Tự động điền tiêu đề cuộc hẹn dựa trên thông tin bất động sản
            const appointmentTitle = $('#appointmentTitle');
            if (!appointmentTitle.val()) {
                let titleText = 'Tham quan bất động sản';
                if (propertyType) {
                    titleText += `: ${propertyType}`;
                }
                titleText += ` - ${title}`;
                appointmentTitle.val(titleText);
            }
            
            // Tự động điền mô tả nếu trống
            const appointmentDesc = $('textarea[name="DescAppoint"]');
            if (!appointmentDesc.val()) {
                let description = `Cuộc hẹn xem bất động sản ${title}`;
                if (propertyType) {
                    description += ` loại ${propertyType}`;
                }
                description += ` tại ${address}`;
                if (price) {
                    description += `. Giá: ${price}`;
                }
                appointmentDesc.val(description);
            }
            
            // Đảm bảo ownerId được lưu đúng từ nhiều nguồn có thể có
            let selectedOwnerId = $('#ownerSearchId').val();
            if (!selectedOwnerId) {
                selectedOwnerId = property.ownerId || (window.ownerData ? window.ownerData.id : null) || 
                                  (window.currentAppointmentOwnerData ? window.currentAppointmentOwnerData.id : null);
                if (selectedOwnerId) {
                    $('#ownerSearchId').val(selectedOwnerId);
                }
            }
            
            // Cập nhật hidden input cho OwnerID
            if (selectedOwnerId) {
                $('#selectedOwnerId').val(selectedOwnerId);
                console.log('Updated hidden input selectedOwnerId to:', selectedOwnerId);
            }
            
            console.log('Owner ID đã chọn:', selectedOwnerId);
            
            if (!selectedOwnerId) {
                console.error('Không tìm thấy Owner ID hợp lệ!');
            }
            
            // Đảm bảo customer ID cũng được lưu đúng - lấy từ component
            const customerData = window.getSelectedCustomerData ? window.getSelectedCustomerData() : null;
            if (customerData && customerData.customerId) {
                console.log('Customer ID đã chọn:', customerData.customerId);
            }
            
            // Lưu thông tin bất động sản và chủ sở hữu vào form để xử lý khi submit
            $('#appointmentPropertyInfo').val(JSON.stringify({
                id: property.id,
                title: title,
                address: address,
                ownerId: selectedOwnerId,
                type: property.type,
                price: property.price,
                formattedPrice: price,
                categoryName: propertyType
            }));
        }
    </script>
@endpush
<script>
    // Date filtering functionality
    $('#applyDateFilter').on('click', function() {
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();
        
        if (dateFrom && dateTo && dateFrom > dateTo) {
            alert('Ngày bắt đầu phải nhỏ hơn ngày kết thúc');
            return;
        }
        
        filterAppointmentsByDateRange(dateFrom, dateTo);
    });
    
    $('#clearDateFilter').on('click', function() {
        $('#dateFrom').val('{{ date('Y-m-01') }}');
        $('#dateTo').val('{{ date('Y-m-t') }}');
        showAllAppointments();
    });
    
    // Function to filter appointments by date range
    function filterAppointmentsByDateRange(dateFrom, dateTo) {
        $('.appointment-card').each(function() {
            const appointmentDate = $(this).data('date');
            
            if (appointmentDate) {
                const formattedDate = new Date(appointmentDate).toISOString().split('T')[0];
                const isInRange = (!dateFrom || formattedDate >= dateFrom) && 
                                 (!dateTo || formattedDate <= dateTo);
                
                if (isInRange) {
                    $(this).fadeIn(300);
                } else {
                    $(this).fadeOut(300);
                }
            }
        });
        
        // Update empty state visibility
        updateEmptyStates();
    }
    
    // Function to show all appointments
    function showAllAppointments() {
        $('.appointment-card').fadeIn(300);
        updateEmptyStates();
    }
    
    // Function to update empty state visibility
    function updateEmptyStates() {
        $('.tab-pane').each(function() {
            const visibleCards = $(this).find('.appointment-card:visible');
            const emptyState = $(this).find('.empty-state-container');
            
            if (visibleCards.length === 0) {
                emptyState.show();
            } else {
                emptyState.hide();
            }
        });
    }

    // NOTE: Form submission is handled in create-appointment-modal.blade.php
    // to avoid duplicate submissions. This comment replaces the previous
    // duplicate event handler that was causing appointments to be created twice.

    // Initialize Bootstrap tabs functionality
    $(document).ready(function() {
        // Initialize Bootstrap Tab functionality
        const triggerTabList = document.querySelectorAll('#appointmentTabs button[data-bs-toggle="tab"]');
        triggerTabList.forEach(triggerEl => {
            const tabTrigger = new bootstrap.Tab(triggerEl);
            
            triggerEl.addEventListener('click', event => {
                event.preventDefault();
                tabTrigger.show();
            });
        });

        // Handle tab switching with proper filtering
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const target = $(e.target).attr('data-bs-target');
            
            // Reset any search/filter states when switching tabs
            $('.empty-filtered').hide();
            $('.property-group-header, .owner-group-header').remove();
            
            // Reset filter dropdown to default
            $('#appointmentFilter').val('all');
            
            // Clear search if active
            if ($('#propertyOwnerSearch').val()) {
                $('#propertyOwnerSearch').val('');
                $('.clear-search-icon').hide();
            }
            
            // Show all appointments in the new active tab
            $(target + ' .appointment-card').show();
            
            // Update empty states for the active tab
            updateEmptyStates();
            
            // Reset date filters to current month
            $('#dateFrom').val('{{ date("Y-m-01") }}');
            $('#dateTo').val('{{ date("Y-m-t") }}');
            
            console.log('Tab switched to:', target);
        });

        // Enhanced search functionality that works with tabs
        $('#propertyOwnerSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase().trim();
            const activeTab = $('.tab-pane.active');
            const clearIcon = $('.clear-search-icon');
            
            // Show/hide clear icon
            if (searchTerm.length > 0) {
                clearIcon.show();
            } else {
                clearIcon.hide();
            }
            
            if (searchTerm.length < 2) {
                $('#searchResults').hide();
                // Show all appointments in active tab
                activeTab.find('.appointment-card').show();
                updateEmptyStates();
                return;
            }
            
            // Filter appointments in active tab only
            let foundAny = false;
            activeTab.find('.appointment-card').each(function() {
                const propertyTitle = $(this).find('.property-name').text().toLowerCase();
                const appointmentMeta = $(this).find('.appointment-meta').text().toLowerCase();
                
                // Extract owner and customer names from meta text
                const metaParts = appointmentMeta.split('|');
                const ownerText = metaParts[0] || '';
                const customerText = metaParts[1] || '';
                
                if (propertyTitle.includes(searchTerm) || 
                    ownerText.includes(searchTerm) || 
                    customerText.includes(searchTerm) ||
                    appointmentMeta.includes(searchTerm)) {
                    $(this).show();
                    foundAny = true;
                } else {
                    $(this).hide();
                }
            });
            
            // Show/hide empty state based on search results
            updateEmptyStates();
            
            // Show search feedback
            if (foundAny) {
                const visibleCount = activeTab.find('.appointment-card:visible').length;
                console.log(`Found ${visibleCount} appointments matching "${searchTerm}"`);
            }
        });

        // Clear search functionality
        $('#resetSearch').on('click', function() {
            $('#propertyOwnerSearch').val('');
            $('.tab-pane.active .appointment-card').show();
            $('#searchResults').hide();
            updateEmptyStates();
        });
    });

    // Enhanced appointment filtering with date support
    $(document).ready(function() {
        // Filter dropdown functionality
        $('#appointmentFilter').on('change', function() {
            const filterValue = $(this).val();
            const activeTab = $('.tab-pane.active');
            const appointments = activeTab.find('.appointment-card');
            
            console.log('Filter changed to:', filterValue);
            
            // Reset all appointments visibility
            appointments.show();
            
            switch(filterValue) {
                case 'newest':
                    sortAppointmentsByDate(appointments, 'desc');
                    break;
                case 'oldest':
                    sortAppointmentsByDate(appointments, 'asc');
                    break;
                case 'property':
                    sortAppointmentsByProperty(appointments);
                    break;
                case 'owner':
                    sortAppointmentsByOwner(appointments);
                    break;
                case 'all':
                default:
                    // Reset to original order
                    appointments.sort(function(a, b) {
                        return $(a).data('original-index') - $(b).data('original-index');
                    }).appendTo(activeTab);
                    break;
            }
            
            updateEmptyStates();
        });
        
        // Date range filter functionality
        $('#applyDateFilter').on('click', function() {
            const dateFrom = $('#dateFrom').val();
            const dateTo = $('#dateTo').val();
            
            if (!dateFrom || !dateTo) {
                alert('Vui lòng chọn đầy đủ khoảng thời gian');
                return;
            }
            
            if (new Date(dateFrom) > new Date(dateTo)) {
                alert('Ngày bắt đầu không thể lớn hơn ngày kết thúc');
                return;
            }
            
            filterAppointmentsByDateRange(dateFrom, dateTo);
        });
        
        // Clear date filter
        $('#clearDateFilter').on('click', function() {
            $('#dateFrom').val('{{ date("Y-m-01") }}');
            $('#dateTo').val('{{ date("Y-m-t") }}');
            $('.tab-pane.active .appointment-card').show();
            updateEmptyStates();
        });
        
        // Store original order for reset functionality
        $('.appointment-card').each(function(index) {
            $(this).data('original-index', index);
        });
    });
    
    // Helper function to sort appointments by date
    function sortAppointmentsByDate(appointments, order) {
        appointments.sort(function(a, b) {
            const dateA = new Date($(a).data('date'));
            const dateB = new Date($(b).data('date'));
            
            if (order === 'desc') {
                return dateB - dateA;
            } else {
                return dateA - dateB;
            }
        }).appendTo($('.tab-pane.active'));
    }
    
    // Helper function to sort appointments by property
    function sortAppointmentsByProperty(appointments) {
        appointments.sort(function(a, b) {
            const propertyA = $(a).find('.property-name').text().toLowerCase();
            const propertyB = $(b).find('.property-name').text().toLowerCase();
            return propertyA.localeCompare(propertyB);
        }).appendTo($('.tab-pane.active'));
    }
    
    // Helper function to sort appointments by owner
    function sortAppointmentsByOwner(appointments) {
        appointments.sort(function(a, b) {
            const metaA = $(a).find('.appointment-meta').text().toLowerCase();
            const metaB = $(b).find('.appointment-meta').text().toLowerCase();
            
            // Extract owner name from meta text (format: "Chủ sở hữu: Name | Khách hàng: Name")
            const ownerA = metaA.split('|')[0].replace('chủ sở hữu:', '').trim();
            const ownerB = metaB.split('|')[0].replace('chủ sở hữu:', '').trim();
            
            return ownerA.localeCompare(ownerB);
        }).appendTo($('.tab-pane.active'));
    }
    
    // Helper function to filter appointments by date range
    function filterAppointmentsByDateRange(dateFrom, dateTo) {
        const activeTab = $('.tab-pane.active');
        const appointments = activeTab.find('.appointment-card');
        let visibleCount = 0;
        
        appointments.each(function() {
            const appointmentDate = new Date($(this).data('date'));
            const fromDate = new Date(dateFrom);
            const toDate = new Date(dateTo);
            
            // Set time to start and end of day for proper comparison
            fromDate.setHours(0, 0, 0, 0);
            toDate.setHours(23, 59, 59, 999);
            
            if (appointmentDate >= fromDate && appointmentDate <= toDate) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        
        updateEmptyStates();
        
        // Show feedback
        const feedbackText = `Hiển thị ${visibleCount} lịch hẹn từ ${formatDate(dateFrom)} đến ${formatDate(dateTo)}`;
        showMessage('info', feedbackText);
    }
    
    // Helper function to format date for display
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }
    
    // Function to show messages to user
    function showMessage(type, message) {
        const messageContainer = $('#messageContainer');
        const messageElement = $(`#${type}Message`);
        const textElement = $(`#${type}Text`);
        
        // Hide all messages first
        messageContainer.find('.alert').hide();
        
        // Set the message text
        textElement.text(message);
        
        // Show the specific message type
        messageElement.show();
        messageContainer.show();
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            messageElement.fadeOut();
            if (messageContainer.find('.alert:visible').length === 0) {
                messageContainer.hide();
            }
        }, 5000);
    }
    
    // Function to update empty states for tabs
    function updateEmptyStates() {
        $('.tab-pane').each(function() {
            const tabPane = $(this);
            const visibleAppointments = tabPane.find('.appointment-card:visible');
            const emptyState = tabPane.find('.empty-state-container');
            
            if (visibleAppointments.length === 0) {
                if (emptyState.length === 0) {
                    // Create empty state if it doesn't exist
                    tabPane.append(`
                        <div class="empty-state-container text-center py-5">
                            <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Không có lịch hẹn nào</h5>
                            <p class="text-muted">Chưa có lịch hẹn nào trong trạng thái này hoặc không có lịch hẹn nào phù hợp với bộ lọc hiện tại.</p>
                        </div>
                    `);
                } else {
                    emptyState.show();
                }
            } else {
                emptyState.hide();
            }
        });
    }
</script>
@endsection