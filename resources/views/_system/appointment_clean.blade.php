@extends('_layout._layadmin.app')
@section('appointment')
@if(session('success'))
    <div class="alert alert-success">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif
@if(isset($error))
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {{ $error }}
    </div>
@else

<!-- Header với thống kê -->
<div class="appointment-header mb-4">
    <div class="row">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-calendar-alt me-2"></i>
                Quản lý Lịch hẹn
            </h1>
            <p class="text-muted">Theo dõi và quản lý lịch hẹn của tất cả môi giới</p>
        </div>
        <div class="col-md-4">
            <div class="appointment-stats-summary">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="stat-card stat-today">
                            <div class="stat-number">{{ $appointmentStats['today'] }}</div>
                            <div class="stat-label">Hôm nay</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card stat-week">
                            <div class="stat-number">{{ $appointmentStats['this_week'] }}</div>
                            <div class="stat-label">Tuần này</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bộ lọc và tìm kiếm nâng cao -->
<div class="search-filters-section mb-4">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-search me-2"></i>
                Tìm kiếm và Lọc
            </h5>
        </div>
        <div class="card-body">
            <form id="appointmentSearchForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Ngày cụ thể</label>
                        <input type="date" id="searchDate" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" id="startDate" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Môi giới</label>
                        <select id="filterAgent" class="form-select">
                            <option value="all">Tất cả</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->UserID }}">{{ $agent->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Trạng thái</label>
                        <select id="filterStatus" class="form-select">
                            <option value="all">Tất cả</option>
                            <option value="Pending">Chờ xử lý</option>
                            <option value="Confirmed">Đã xác nhận</option>
                            <option value="Completed">Hoàn thành</option>
                            <option value="Cancelled">Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" id="searchBtn" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Layout chính 3 cột -->
<div class="appointment-main-layout">
    <div class="row g-4">

        <!-- Cột 1: Danh sách môi giới và thống kê -->
        <div class="col-lg-3">
            <div class="agents-panel">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Môi giới
                        </h5>
                        <span class="badge bg-primary">{{ $agents->total() }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="agent-list-container">
                            @foreach($agents as $agent)
                            <div class="agent-item" data-agent-id="{{ $agent->UserID }}">
                                <div class="agent-info">
                                    <div class="agent-name">{{ $agent->Name }}</div>
                                    <div class="agent-stats">
                                        <span class="property-count">
                                            <i class="fas fa-home"></i>
                                            {{ $agent->active_property_count }} BĐS
                                        </span>
                                        <span class="appointment-count">
                                            <i class="fas fa-calendar"></i>
                                            {{ $agent->total_appointments }} lịch hẹn
                                        </span>
                                    </div>
                                    @if($agent->profile_agent)
                                        <div class="agent-contact">
                                            <small class="text-muted">{{ $agent->profile_agent->AreaAgent }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- Phân trang -->
                        <div class="agent-pagination p-3">
                            {{ $agents->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột 2: Danh sách cuộc hẹn -->
        <div class="col-lg-6">
            <div class="appointments-panel">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-check me-2"></i>
                                Danh sách cuộc hẹn
                                <span id="appointmentCount" class="badge bg-secondary ms-2">0</span>
                            </h5>
                            <div class="appointment-view-controls">
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" class="btn-check" name="viewType" id="listView" checked>
                                    <label class="btn btn-outline-primary" for="listView">
                                        <i class="fas fa-list"></i>
                                    </label>

                                    <input type="radio" class="btn-check" name="viewType" id="gridView">
                                    <label class="btn btn-outline-primary" for="gridView">
                                        <i class="fas fa-th"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Tabs cho loại cuộc hẹn -->
                        <ul class="nav nav-tabs" id="appointmentTypeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-appointments-tab" data-bs-toggle="tab"
                                        data-bs-target="#all-appointments" type="button" role="tab">
                                    <i class="fas fa-calendar-alt me-1"></i>Tất cả
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="owner-appointments-tab" data-bs-toggle="tab"
                                        data-bs-target="#owner-appointments" type="button" role="tab">
                                    <i class="fas fa-user-tie me-1"></i>Với chủ sở hữu
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="customer-appointments-tab" data-bs-toggle="tab"
                                        data-bs-target="#customer-appointments" type="button" role="tab">
                                    <i class="fas fa-user me-1"></i>Với khách hàng
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="appointmentTabContent">
                            <!-- Tab tất cả cuộc hẹn -->
                            <div class="tab-pane fade show active" id="all-appointments" role="tabpanel">
                                <div class="appointments-container">
                                    <div class="initial-message text-center py-5">
                                        <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Chọn môi giới hoặc tìm kiếm để xem cuộc hẹn</h5>
                                        <p class="text-muted">Sử dụng bộ lọc ở trên để tìm kiếm cuộc hẹn theo ngày tháng</p>
                                    </div>
                                    <div class="appointments-content d-none">
                                        <div id="appointments-list" class="appointments-list-view">
                                            <!-- Danh sách cuộc hẹn sẽ được load ở đây -->
                                        </div>
                                        <div id="appointments-grid" class="appointments-grid-view d-none">
                                            <!-- Grid view cuộc hẹn sẽ được load ở đây -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab cuộc hẹn với chủ sở hữu -->
                            <div class="tab-pane fade" id="owner-appointments" role="tabpanel">
                                <div id="owner-appointments-content" class="p-3">
                                    <div class="initial-message text-center py-4">
                                        <i class="fas fa-user-tie fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">Chọn môi giới để xem cuộc hẹn với chủ sở hữu</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab cuộc hẹn với khách hàng -->
                            <div class="tab-pane fade" id="customer-appointments" role="tabpanel">
                                <div id="customer-appointments-content" class="p-3">
                                    <div class="initial-message text-center py-4">
                                        <i class="fas fa-user fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">Chọn môi giới để xem cuộc hẹn với khách hàng</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột 3: Chi tiết và Bất động sản -->
        <div class="col-lg-3">
            <div class="details-panel">
                <!-- Chi tiết cuộc hẹn được chọn -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Chi tiết cuộc hẹn
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="appointment-detail-sidebar">
                            <div class="no-selection text-center py-4">
                                <i class="fas fa-mouse-pointer fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Chọn một cuộc hẹn để xem chi tiết</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bất động sản liên quan -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-building me-2"></i>
                            Bất động sản liên quan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="related-properties">
                            <div class="no-data text-center py-4">
                                <i class="fas fa-home fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Chưa có thông tin BĐS</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Template cho Chi tiết cuộc hẹn -->
<div class="modal fade" id="appointmentDetailModal" tabindex="-1" aria-labelledby="appointmentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentDetailModalLabel">Chi tiết cuộc hẹn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="appointmentDetailModal-body">
                <!-- Nội dung chi tiết sẽ được thêm vào bằng JavaScript -->
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger btn-sm delete-appointment-btn">
                    <i class="fa fa-trash"></i> Xóa
                </button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xác nhận xóa -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                Bạn có chắc chắn muốn xóa cuộc hẹn này?
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteAppointmentForm" action="" method="POST" class="delete-appointment-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endif
@endsection

<style>
/* ===== APPOINTMENT PAGE STYLES ===== */

/* Header styles */
.appointment-header .page-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

/* Statistics cards */
.appointment-stats-summary {
    margin-top: 1rem;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem;
    border-radius: 10px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.stat-card.stat-today {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-card.stat-week {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.stat-number {
    font-size: 1.8rem;
    font-weight: bold;
    line-height: 1;
}

.stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
    margin-top: 0.25rem;
}

/* Search filters */
.search-filters-section .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 15px;
}

.search-filters-section .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0;
}

/* Main layout */
.appointment-main-layout {
    min-height: 600px;
}

/* Agent panel styles */
.agents-panel .card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 15px;
}

.agent-item {
    padding: 1rem;
    margin: 0.5rem;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    background: #f8f9fa;
}

.agent-item:hover {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    transform: translateX(5px);
    border-left-color: #2196f3;
    box-shadow: 0 4px 15px rgba(33, 150, 243, 0.2);
}

.agent-item.active {
    background: linear-gradient(135deg, #e8f5e8 0%, #f0f8e8 100%);
    border-left-color: #4caf50;
    box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
    transform: translateX(8px);
}

.agent-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.agent-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.agent-stats span {
    font-size: 0.85rem;
    color: #666;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.agent-stats i {
    color: #3498db;
}

.agent-contact {
    font-size: 0.8rem;
}

/* Appointments panel */
.appointments-panel .card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 15px;
}

.appointment-view-controls .btn-group {
    border-radius: 20px;
    overflow: hidden;
}

/* Tabs styling */
.nav-tabs {
    border-bottom: 2px solid #e9ecef;
}

.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    padding: 1rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border: none;
    color: #495057;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.nav-tabs .nav-link.active {
    color: #495057;
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border: none;
    border-bottom: 3px solid #007bff;
}

/* Appointments list/grid */
.appointments-container {
    min-height: 400px;
}

.initial-message {
    color: #6c757d;
}

.appointments-list-view {
    max-height: 500px;
    overflow-y: auto;
}

.appointments-grid-view {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    padding: 1rem;
}

.appointment-item {
    padding: 1rem;
    margin: 0.5rem;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    cursor: pointer;
}

.appointment-item:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.appointment-item.selected {
    border-color: #007bff;
    background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
}

/* Details panel */
.details-panel .card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 15px;
}

.details-panel .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0;
}

/* Status badges */
.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-confirmed {
    background: #d4edda;
    color: #155724;
}

.status-completed {
    background: #d1ecf1;
    color: #0c5460;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

/* Loading states */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive design */
@media (max-width: 992px) {
    .appointment-main-layout .col-lg-3,
    .appointment-main-layout .col-lg-6 {
        margin-bottom: 2rem;
    }

    .agent-stats {
        flex-direction: column;
        gap: 0.5rem;
    }

    .stat-card {
        margin-bottom: 1rem;
    }
}

@media (max-width: 768px) {
    .appointment-header {
        text-align: center;
    }

    .appointment-stats-summary {
        margin-top: 1.5rem;
    }

    .search-filters-section .row > div {
        margin-bottom: 1rem;
    }

    .appointment-view-controls {
        margin-top: 1rem;
    }
}

/* Animation effects */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-in-right {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Custom scrollbar */
.agent-list-container::-webkit-scrollbar,
.appointments-list-view::-webkit-scrollbar {
    width: 6px;
}

.agent-list-container::-webkit-scrollbar-track,
.appointments-list-view::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.agent-list-container::-webkit-scrollbar-thumb,
.appointments-list-view::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.agent-list-container::-webkit-scrollbar-thumb:hover,
.appointments-list-view::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global variables
    let currentAgentId = null;
    let currentAppointmentId = null;
    let allAppointments = [];
    let filteredAppointments = [];

    // DOM elements
    const agentItems = document.querySelectorAll('.agent-item');
    const searchBtn = document.getElementById('searchBtn');
    const appointmentCountBadge = document.getElementById('appointmentCount');
    const listViewBtn = document.getElementById('listView');
    const gridViewBtn = document.getElementById('gridView');

    // Initialize event listeners
    initializeEventListeners();

    function initializeEventListeners() {
        // Agent selection
        agentItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                selectAgent(this);
            });
        });

        // Search functionality
        searchBtn.addEventListener('click', performSearch);

        // Enter key in search forms
        document.getElementById('appointmentSearchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });

        // View toggle
        listViewBtn.addEventListener('change', function() {
            if (this.checked) toggleView('list');
        });

        gridViewBtn.addEventListener('change', function() {
            if (this.checked) toggleView('grid');
        });

        // Tab switching
        document.querySelectorAll('#appointmentTypeTabs button[data-bs-toggle="tab"]').forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', function(e) {
                const target = e.target.getAttribute('data-bs-target');
                loadTabContent(target);
            });
        });
    }

    function selectAgent(agentElement) {
        // Remove active class from all agents
        agentItems.forEach(item => item.classList.remove('active'));

        // Add active class to selected agent
        agentElement.classList.add('active');

        // Get agent ID
        currentAgentId = agentElement.getAttribute('data-agent-id');

        // Load agent's appointments
        loadAgentAppointments(currentAgentId);
    }

    function loadAgentAppointments(agentId) {
        showLoadingState();

        fetch(`/admin/appointment/agent/${agentId}`)
            .then(response => response.json())
            .then(data => {
                allAppointments = data.appointments || [];
                filteredAppointments = [...allAppointments];
                updateAppointmentsDisplay();
                hideInitialMessage();
            })
            .catch(error => {
                console.error('Error loading appointments:', error);
                showErrorMessage('Không thể tải danh sách cuộc hẹn');
            });
    }

    function performSearch() {
        const searchDate = document.getElementById('searchDate').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const filterAgent = document.getElementById('filterAgent').value;
        const filterStatus = document.getElementById('filterStatus').value;

        showLoadingState();

        let searchUrl = '/admin/appointment/search';
        const params = new URLSearchParams();

        if (searchDate) {
            searchUrl = '/admin/appointment/search-by-date';
            params.append('date', searchDate);
        } else if (startDate && endDate) {
            searchUrl = '/admin/appointment/search-by-range';
            params.append('start_date', startDate);
            params.append('end_date', endDate);
        }

        if (filterAgent && filterAgent !== 'all') {
            params.append('agent_id', filterAgent);
        }

        if (filterStatus && filterStatus !== 'all') {
            params.append('status', filterStatus);
        }

        const fullUrl = params.toString() ? `${searchUrl}?${params.toString()}` : searchUrl;

        fetch(fullUrl)
            .then(response => response.json())
            .then(data => {
                allAppointments = data.appointments || [];
                filteredAppointments = [...allAppointments];
                updateAppointmentsDisplay();
                hideInitialMessage();

                // Clear agent selection if search is performed
                agentItems.forEach(item => item.classList.remove('active'));
                currentAgentId = null;
            })
            .catch(error => {
                console.error('Error searching appointments:', error);
                showErrorMessage('Không thể tìm kiếm cuộc hẹn');
            });
    }

    function updateAppointmentsDisplay() {
        appointmentCountBadge.textContent = filteredAppointments.length;

        const currentView = document.querySelector('input[name="viewType"]:checked').id;

        if (currentView === 'listView') {
            renderListView();
        } else {
            renderGridView();
        }
    }

    function renderListView() {
        const container = document.getElementById('appointments-list');
        container.innerHTML = '';

        if (filteredAppointments.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                    <p class="text-muted">Không có cuộc hẹn nào</p>
                </div>
            `;
            return;
        }

        const listHTML = filteredAppointments.map(appointment => `
            <div class="appointment-item" data-appointment-id="${appointment.AppointmentID}" onclick="selectAppointment(${appointment.AppointmentID})">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="appointment-info flex-grow-1">
                        <h6 class="appointment-title mb-2">${appointment.AppointmentTitle || 'Cuộc hẹn'}</h6>
                        <p class="appointment-description text-muted small mb-2">${appointment.AppointmentDescription || 'Không có mô tả'}</p>
                        <div class="appointment-meta">
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-calendar me-1"></i>
                                ${formatDate(appointment.AppointmentDateStart)}
                            </span>
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-clock me-1"></i>
                                ${formatTime(appointment.AppointmentDateStart)}
                            </span>
                            ${getStatusBadge(appointment.AppointmentStatus)}
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewAppointmentDetail(${appointment.AppointmentID}); event.stopPropagation();">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = listHTML;
    }

    function renderGridView() {
        const container = document.getElementById('appointments-grid');
        container.innerHTML = '';

        if (filteredAppointments.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                    <p class="text-muted">Không có cuộc hẹn nào</p>
                </div>
            `;
            return;
        }

        const gridHTML = filteredAppointments.map(appointment => `
            <div class="card appointment-card" data-appointment-id="${appointment.AppointmentID}" onclick="selectAppointment(${appointment.AppointmentID})">
                <div class="card-body">
                    <h6 class="card-title">${appointment.AppointmentTitle || 'Cuộc hẹn'}</h6>
                    <p class="card-text text-muted small">${appointment.AppointmentDescription || 'Không có mô tả'}</p>
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            ${formatDate(appointment.AppointmentDateStart)}
                        </small><br>
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            ${formatTime(appointment.AppointmentDateStart)} - ${formatTime(appointment.AppointmentDateEnd)}
                        </small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        ${getStatusBadge(appointment.AppointmentStatus)}
                        <button class="btn btn-sm btn-outline-primary" onclick="viewAppointmentDetail(${appointment.AppointmentID}); event.stopPropagation();">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = gridHTML;
    }

    function toggleView(viewType) {
        const listContainer = document.getElementById('appointments-list');
        const gridContainer = document.getElementById('appointments-grid');

        if (viewType === 'list') {
            listContainer.classList.remove('d-none');
            gridContainer.classList.add('d-none');
            renderListView();
        } else {
            listContainer.classList.add('d-none');
            gridContainer.classList.remove('d-none');
            renderGridView();
        }
    }

    function selectAppointment(appointmentId) {
        currentAppointmentId = appointmentId;

        // Update visual selection
        document.querySelectorAll('.appointment-item, .appointment-card').forEach(item => {
            item.classList.remove('selected');
        });

        document.querySelector(`[data-appointment-id="${appointmentId}"]`)?.classList.add('selected');

        // Load appointment details in sidebar
        loadAppointmentDetail(appointmentId);
    }

    function loadAppointmentDetail(appointmentId) {
        const sidebar = document.getElementById('appointment-detail-sidebar');
        sidebar.innerHTML = '<div class="text-center"><div class="loading-spinner"></div></div>';

        fetch(`/admin/appointment/detail/${appointmentId}`)
            .then(response => response.json())
            .then(data => {
                renderAppointmentDetail(data.appointment);
                if (data.properties) {
                    renderRelatedProperties(data.properties);
                }
            })
            .catch(error => {
                console.error('Error loading appointment detail:', error);
                sidebar.innerHTML = '<div class="text-center text-danger">Không thể tải chi tiết</div>';
            });
    }

    function renderAppointmentDetail(appointment) {
        const sidebar = document.getElementById('appointment-detail-sidebar');

        const detailHTML = `
            <div class="appointment-detail-content">
                <h6 class="fw-bold mb-3">${appointment.AppointmentTitle || 'Cuộc hẹn'}</h6>

                <div class="detail-item mb-3">
                    <label class="text-muted small">Mô tả:</label>
                    <p class="mb-0">${appointment.AppointmentDescription || 'Không có mô tả'}</p>
                </div>

                <div class="detail-item mb-3">
                    <label class="text-muted small">Thời gian:</label>
                    <p class="mb-0">
                        <i class="fas fa-calendar me-1"></i>
                        ${formatDate(appointment.AppointmentDateStart)}<br>
                        <i class="fas fa-clock me-1"></i>
                        ${formatTime(appointment.AppointmentDateStart)} - ${formatTime(appointment.AppointmentDateEnd)}
                    </p>
                </div>

                <div class="detail-item mb-3">
                    <label class="text-muted small">Trạng thái:</label>
                    <div>${getStatusBadge(appointment.AppointmentStatus)}</div>
                </div>

                <div class="detail-actions mt-4">
                    <button class="btn btn-primary btn-sm w-100 mb-2" onclick="viewAppointmentDetail(${appointment.AppointmentID})">
                        <i class="fas fa-expand-alt me-1"></i>
                        Xem chi tiết đầy đủ
                    </button>
                </div>
            </div>
        `;

        sidebar.innerHTML = detailHTML;
    }

    function renderRelatedProperties(properties) {
        const container = document.getElementById('related-properties');

        if (!properties || properties.length === 0) {
            container.innerHTML = `
                <div class="no-data text-center py-4">
                    <i class="fas fa-home fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">Chưa có thông tin BĐS</p>
                </div>
            `;
            return;
        }

        const propertiesHTML = properties.map(property => `
            <div class="property-item mb-3 p-3 border rounded">
                <h6 class="property-title">${property.PropertyTitle}</h6>
                <p class="text-muted small mb-2">${property.PropertyAddress}</p>
                <div class="property-meta">
                    <span class="badge bg-light text-dark">
                        ${property.PropertyPrice ? formatPrice(property.PropertyPrice) : 'Giá thỏa thuận'}
                    </span>
                </div>
            </div>
        `).join('');

        container.innerHTML = propertiesHTML;
    }

    function loadTabContent(target) {
        // Implementation for loading different tab contents
        console.log('Loading tab content for:', target);
    }

    // Global functions (accessible from onclick handlers)
    window.selectAppointment = selectAppointment;
    window.viewAppointmentDetail = function(appointmentId) {
        loadAppointmentDetail(appointmentId);
        // Open modal if needed
        const modal = new bootstrap.Modal(document.getElementById('appointmentDetailModal'));
        modal.show();
    };

    // Utility functions
    function formatDate(dateString) {
        if (!dateString) return 'Chưa xác định';
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }

    function formatTime(dateString) {
        if (!dateString) return 'Chưa xác định';
        const date = new Date(dateString);
        return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }

    function getStatusBadge(status) {
        const statusMap = {
            'Pending': { class: 'status-pending', text: 'Chờ xử lý' },
            'Confirmed': { class: 'status-confirmed', text: 'Đã xác nhận' },
            'Completed': { class: 'status-completed', text: 'Hoàn thành' },
            'Cancelled': { class: 'status-cancelled', text: 'Đã hủy' }
        };

        const statusInfo = statusMap[status] || { class: 'status-pending', text: status || 'Chưa xác định' };
        return `<span class="status-badge ${statusInfo.class}">${statusInfo.text}</span>`;
    }

    function showLoadingState() {
        const container = document.querySelector('.appointments-content');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="loading-spinner mb-3"></div>
                    <p class="text-muted">Đang tải dữ liệu...</p>
                </div>
            `;
            container.classList.remove('d-none');
        }
    }

    function hideInitialMessage() {
        document.querySelectorAll('.initial-message').forEach(el => {
            el.classList.add('d-none');
        });
        document.querySelector('.appointments-content')?.classList.remove('d-none');
    }

    function showErrorMessage(message) {
        const container = document.querySelector('.appointments-content');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                    <p class="text-danger">${message}</p>
                    <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                        <i class="fas fa-refresh me-1"></i>
                        Thử lại
                    </button>
                </div>
            `;
            container.classList.remove('d-none');
        }
    }
});
</script>
