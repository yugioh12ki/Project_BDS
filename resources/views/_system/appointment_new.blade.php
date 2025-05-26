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
                                <div class="agent-properties-preview d-none">
                                    <!-- Sẽ được load bằng AJAX -->
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
                                    <!-- Sẽ được load bằng AJAX -->
                                </div>
                            </div>

                            <!-- Tab cuộc hẹn với khách hàng -->
                            <div class="tab-pane fade" id="customer-appointments" role="tabpanel">
                                <div id="customer-appointments-content" class="p-3">
                                    <!-- Sẽ được load bằng AJAX -->
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
            <div class="modal-body" id="appointmentDetail">
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
/* Modern appointment management styling */
.appointment-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.1);
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
}

.stat-label {
    font-size: 0.875rem;
    opacity: 0.9;
}

/* Search filters */
.search-filters-section .card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

.search-filters-section .card-header {
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #dee2e6;
}

/* Agent list styling */
.agent-item {
    padding: 1rem;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.agent-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.agent-item.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.agent-item.active .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
}

.agent-name {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.agent-stats {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    font-size: 0.875rem;
}

.agent-stats span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.agent-stats i {
    width: 16px;
    font-size: 0.875rem;
}

/* Appointments panel */
.appointments-list-view {
    max-height: 600px;
    overflow-y: auto;
}

.appointment-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.appointment-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
    border-color: #007bff;
}

.appointment-card.selected {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    background-color: #f8f9ff;
}

.appointments-grid-view {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
    padding: 1rem;
}

/* Details panel */
.details-panel .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.no-selection, .no-data {
    color: #6c757d;
}

/* Property preview cards */
.property-preview-card {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.property-preview-card:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}

/* Responsive design */
@media (max-width: 992px) {
    .appointment-main-layout .col-lg-3,
    .appointment-main-layout .col-lg-6 {
        margin-bottom: 2rem;
    }

    .stat-card {
        margin-bottom: 1rem;
    }
}

@media (max-width: 768px) {
    .appointment-header {
        padding: 1rem;
    }

    .page-title {
        font-size: 1.5rem;
    }

    .search-filters-section .row > div {
        margin-bottom: 1rem;
    }

    .appointments-grid-view {
        grid-template-columns: 1fr;
    }
}

/* Status badges */
.status-pending { background-color: #ffc107; }
.status-confirmed { background-color: #28a745; }
.status-completed { background-color: #007bff; }
.status-cancelled { background-color: #dc3545; }

/* Animation classes */
.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-in-right {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables for managing state
    let currentAgentId = null;
    let currentAppointmentId = null;
    let allAppointments = [];
    let filteredAppointments = [];
    let currentView = 'list'; // 'list' hoặc 'grid'

    // DOM elements
    const agentItems = document.querySelectorAll('.agent-item');
    const searchForm = document.getElementById('appointmentSearchForm');
    const appointmentCount = document.getElementById('appointmentCount');
    const appointmentsList = document.getElementById('appointments-list');
    const appointmentsGrid = document.getElementById('appointments-grid');
    const appointmentDetailSidebar = document.getElementById('appointment-detail-sidebar');
    const relatedProperties = document.getElementById('related-properties');

    // Initialize event listeners
    initializeEventListeners();

    function initializeEventListeners() {
        // Agent selection
        agentItems.forEach(item => {
            item.addEventListener('click', handleAgentSelection);
        });

        // Search button
        document.getElementById('searchBtn').addEventListener('click', handleSearch);

        // View type toggle
        document.getElementById('listView').addEventListener('change', () => {
            if (document.getElementById('listView').checked) {
                toggleView('list');
            }
        });

        document.getElementById('gridView').addEventListener('change', () => {
            if (document.getElementById('gridView').checked) {
                toggleView('grid');
            }
        });

        // Tab switches
        document.getElementById('all-appointments-tab').addEventListener('click', () => filterAppointmentsByType('all'));
        document.getElementById('owner-appointments-tab').addEventListener('click', () => filterAppointmentsByType('owner'));
        document.getElementById('customer-appointments-tab').addEventListener('click', () => filterAppointmentsByType('customer'));

        // Modal delete button
        document.querySelector('.delete-appointment-btn').addEventListener('click', handleDeleteButtonClick);
    }

    function handleAgentSelection(e) {
        e.preventDefault();

        // Remove active class from all agents
        agentItems.forEach(item => item.classList.remove('active'));

        // Add active class to selected agent
        this.classList.add('active');

        const agentId = this.getAttribute('data-agent-id');
        currentAgentId = agentId;

        // Load appointments for this agent
        loadAgentAppointments(agentId);

        // Load agent properties
        loadAgentProperties(agentId);
    }

    function handleSearch() {
        const searchDate = document.getElementById('searchDate').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const agentId = document.getElementById('filterAgent').value;
        const status = document.getElementById('filterStatus').value;

        const searchParams = new URLSearchParams();

        if (searchDate) {
            searchParams.append('date', searchDate);
        }

        if (startDate && endDate) {
            searchParams.append('start_date', startDate);
            searchParams.append('end_date', endDate);
        }

        if (agentId && agentId !== 'all') {
            searchParams.append('agent_id', agentId);
        }

        if (status && status !== 'all') {
            searchParams.append('status', status);
        }

        // Call appropriate search endpoint
        const endpoint = searchDate ?
            `/admin/appointment/search-by-date?${searchParams}` :
            `/admin/appointment/search-by-range?${searchParams}`;

        showLoadingState();

        fetch(endpoint)
            .then(response => response.json())
            .then(data => {
                allAppointments = data.appointments;
                filteredAppointments = [...allAppointments];
                updateAppointmentCount(data.count);
                renderAppointments();
                hideInitialMessage();
            })
            .catch(error => {
                console.error('Search error:', error);
                showErrorMessage('Lỗi khi tìm kiếm cuộc hẹn');
            });
    }

    function loadAgentAppointments(agentId) {
        showLoadingState();

        fetch(`/admin/appointment/agent/${agentId}`)
            .then(response => response.json())
            .then(data => {
                allAppointments = data.appointments || [];
                filteredAppointments = [...allAppointments];
                updateAppointmentCount(allAppointments.length);
                renderAppointments();
                hideInitialMessage();
            })
            .catch(error => {
                console.error('Error loading agent appointments:', error);
                showErrorMessage('Lỗi khi tải danh sách cuộc hẹn');
            });
    }

    function loadAgentProperties(agentId) {
        fetch(`/admin/agent/${agentId}/properties`)
            .then(response => response.json())
            .then(data => {
                renderAgentProperties(data.properties || []);
            })
            .catch(error => {
                console.error('Error loading agent properties:', error);
            });
    }

    function renderAppointments() {
        if (currentView === 'list') {
            renderAppointmentsList();
        } else {
            renderAppointmentsGrid();
        }
    }

    function renderAppointmentsList() {
        if (filteredAppointments.length === 0) {
            appointmentsList.innerHTML = '<div class="text-center py-4 text-muted">Không có cuộc hẹn nào</div>';
            return;
        }

        const html = filteredAppointments.map(appointment => `
            <div class="appointment-card" data-appointment-id="${appointment.AppointmentID}">
                <div class="row">
                    <div class="col-8">
                        <h6 class="mb-2">${appointment.TitleAppoint || 'Không có tiêu đề'}</h6>
                        <p class="text-muted mb-2 small">${truncateText(appointment.DescAppoint || '', 100)}</p>
                        <div class="d-flex align-items-center gap-3 small">
                            <span><i class="fas fa-calendar"></i> ${formatDate(appointment.AppointmentDateStart)}</span>
                            <span><i class="fas fa-user"></i> ${getAppointmentPerson(appointment)}</span>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <span class="badge ${getStatusClass(appointment.AppointmentStatus)} mb-2">${appointment.AppointmentStatus || 'Pending'}</span>
                        ${appointment.property ? `<div class="small text-muted"><i class="fas fa-home"></i> ${appointment.property.Title}</div>` : ''}
                    </div>
                </div>
            </div>
        `).join('');

        appointmentsList.innerHTML = html;

        // Add click handlers
        document.querySelectorAll('.appointment-card').forEach(card => {
            card.addEventListener('click', function() {
                const appointmentId = this.getAttribute('data-appointment-id');
                selectAppointment(appointmentId);
            });
        });
    }

    function renderAppointmentsGrid() {
        if (filteredAppointments.length === 0) {
            appointmentsGrid.innerHTML = '<div class="text-center py-4 text-muted">Không có cuộc hẹn nào</div>';
            return;
        }

        const html = filteredAppointments.map(appointment => `
            <div class="appointment-card" data-appointment-id="${appointment.AppointmentID}">
                <h6 class="mb-2">${appointment.TitleAppoint || 'Không có tiêu đề'}</h6>
                <p class="text-muted mb-2 small">${truncateText(appointment.DescAppoint || '', 80)}</p>
                <div class="mb-2">
                    <span class="badge ${getStatusClass(appointment.AppointmentStatus)}">${appointment.AppointmentStatus || 'Pending'}</span>
                </div>
                <div class="small text-muted">
                    <div><i class="fas fa-calendar"></i> ${formatDate(appointment.AppointmentDateStart)}</div>
                    <div><i class="fas fa-user"></i> ${getAppointmentPerson(appointment)}</div>
                    ${appointment.property ? `<div><i class="fas fa-home"></i> ${appointment.property.Title}</div>` : ''}
                </div>
            </div>
        `).join('');

        appointmentsGrid.innerHTML = html;

        // Add click handlers
        document.querySelectorAll('.appointment-card').forEach(card => {
            card.addEventListener('click', function() {
                const appointmentId = this.getAttribute('data-appointment-id');
                selectAppointment(appointmentId);
            });
        });
    }

    function selectAppointment(appointmentId) {
        currentAppointmentId = appointmentId;

        // Remove selected class from all cards
        document.querySelectorAll('.appointment-card').forEach(card => {
            card.classList.remove('selected');
        });

        // Add selected class to clicked card
        document.querySelector(`[data-appointment-id="${appointmentId}"]`).classList.add('selected');

        // Load appointment details
        loadAppointmentDetails(appointmentId);
    }

    function loadAppointmentDetails(appointmentId) {
        appointmentDetailSidebar.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

        fetch(`/admin/appointment/detail/${appointmentId}`)
            .then(response => response.json())
            .then(data => {
                renderAppointmentDetails(data.appointment);
                if (data.appointment.property) {
                    renderRelatedProperty(data.appointment.property);
                }
            })
            .catch(error => {
                console.error('Error loading appointment details:', error);
                appointmentDetailSidebar.innerHTML = '<div class="text-center py-4 text-danger">Lỗi khi tải chi tiết</div>';
            });
    }

    function renderAppointmentDetails(appointment) {
        const html = `
            <div class="appointment-detail-content">
                <div class="mb-3">
                    <h6 class="fw-bold">${appointment.TitleAppoint || 'Không có tiêu đề'}</h6>
                    <span class="badge ${getStatusClass(appointment.AppointmentStatus)}">${appointment.AppointmentStatus || 'Pending'}</span>
                </div>

                <div class="mb-3">
                    <div class="small text-muted mb-1">Thời gian bắt đầu</div>
                    <div>${formatDate(appointment.AppointmentDateStart)}</div>
                </div>

                <div class="mb-3">
                    <div class="small text-muted mb-1">Thời gian kết thúc</div>
                    <div>${formatDate(appointment.AppointmentDateEnd)}</div>
                </div>

                <div class="mb-3">
                    <div class="small text-muted mb-1">Môi giới</div>
                    <div>${appointment.user_agent ? appointment.user_agent.Name : 'Chưa xác định'}</div>
                </div>

                <div class="mb-3">
                    <div class="small text-muted mb-1">Người hẹn</div>
                    <div>${getAppointmentPerson(appointment)}</div>
                </div>

                <div class="mb-3">
                    <div class="small text-muted mb-1">Mô tả</div>
                    <div class="bg-light p-2 rounded">${appointment.DescAppoint || 'Không có mô tả'}</div>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-sm" onclick="showDetailModal('${appointment.AppointmentID}')">
                        <i class="fas fa-expand"></i> Xem chi tiết đầy đủ
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete('${appointment.AppointmentID}')">
                        <i class="fas fa-trash"></i> Xóa cuộc hẹn
                    </button>
                </div>
            </div>
        `;

        appointmentDetailSidebar.innerHTML = html;
    }

    function renderRelatedProperty(property) {
        const html = `
            <div class="property-info">
                <h6 class="fw-bold">${property.Title}</h6>
                <div class="small text-muted mb-2">${property.Address}</div>
                <div class="small">
                    <div>Giá: ${formatPrice(property.Price)}</div>
                    <div>Loại: ${property.danhMuc ? property.danhMuc.ten_pro : 'Chưa xác định'}</div>
                </div>
            </div>
        `;

        relatedProperties.innerHTML = html;
    }

    function renderAgentProperties(properties) {
        // Implementation for showing agent's properties in the agent item
        // This could expand the agent item to show their property portfolio
    }

    function toggleView(viewType) {
        currentView = viewType;

        if (viewType === 'list') {
            appointmentsList.classList.remove('d-none');
            appointmentsGrid.classList.add('d-none');
        } else {
            appointmentsList.classList.add('d-none');
            appointmentsGrid.classList.remove('d-none');
        }

        renderAppointments();
    }

    function filterAppointmentsByType(type) {
        switch(type) {
            case 'all':
                filteredAppointments = [...allAppointments];
                break;
            case 'owner':
                filteredAppointments = allAppointments.filter(apt => apt.OwnerID);
                break;
            case 'customer':
                filteredAppointments = allAppointments.filter(apt => apt.CusID);
                break;
        }

        updateAppointmentCount(filteredAppointments.length);
        renderAppointments();
    }

    function showLoadingState() {
        const loadingHtml = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</div>';
        appointmentsList.innerHTML = loadingHtml;
        appointmentsGrid.innerHTML = loadingHtml;
    }

    function showErrorMessage(message) {
        const errorHtml = `<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-circle"></i> ${message}</div>`;
        appointmentsList.innerHTML = errorHtml;
        appointmentsGrid.innerHTML = errorHtml;
    }

    function hideInitialMessage() {
        document.querySelector('.initial-message').classList.add('d-none');
        document.querySelector('.appointments-content').classList.remove('d-none');
    }

    function updateAppointmentCount(count) {
        appointmentCount.textContent = count;
    }

    function handleDeleteButtonClick() {
        // Handle delete from modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('appointmentDetailModal'));
        modal.hide();

        setTimeout(() => {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        }, 300);
    }

    // Utility functions
    function formatDate(dateString) {
        if (!dateString) return 'Chưa xác định';
        try {
            return new Date(dateString).toLocaleString('vi-VN');
        } catch (e) {
            return 'Định dạng không hợp lệ';
        }
    }

    function formatPrice(price) {
        if (!price) return 'Chưa xác định';
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
    }

    function truncateText(text, maxLength) {
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }

    function getStatusClass(status) {
        switch(status) {
            case 'Completed': return 'status-completed';
            case 'Confirmed': return 'status-confirmed';
            case 'Cancelled': return 'status-cancelled';
            default: return 'status-pending';
        }
    }

    function getAppointmentPerson(appointment) {
        if (appointment.user_owner) {
            return appointment.user_owner.Name;
        } else if (appointment.user_customer) {
            return appointment.user_customer.Name;
        }
        return 'Chưa xác định';
    }

    // Global functions for button callbacks
    window.showDetailModal = function(appointmentId) {
        // Load appointment details into modal and show
        loadAppointmentDetailsModal(appointmentId);
    };

    window.confirmDelete = function(appointmentId) {
        document.getElementById('deleteAppointmentForm').action = `/admin/appointment/${appointmentId}`;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();
    };

    function loadAppointmentDetailsModal(appointmentId) {
        const modal = new bootstrap.Modal(document.getElementById('appointmentDetailModal'));
        modal.show();

        fetch(`/admin/appointment/detail/${appointmentId}`)
            .then(response => response.json())
            .then(data => {
                // Render full details in modal
                document.getElementById('appointmentDetail').innerHTML = renderFullAppointmentDetails(data.appointment);
                document.getElementById('deleteAppointmentForm').action = `/admin/appointment/${appointmentId}`;
            })
            .catch(error => {
                console.error('Error loading modal details:', error);
                document.getElementById('appointmentDetail').innerHTML = '<div class="text-center py-4 text-danger">Lỗi khi tải chi tiết</div>';
            });
    }

    function renderFullAppointmentDetails(appointment) {
        return `
            <div class="appointment-detail-full">
                <div class="mb-3">
                    <span class="badge bg-secondary">ID: ${appointment.AppointmentID}</span>
                </div>

                <div class="mb-3">
                    <h6 class="text-primary fw-bold mb-0">${appointment.TitleAppoint || 'Không có tiêu đề'}</h6>
                    <small class="text-muted">
                        Trạng thái: <span class="badge ${getStatusClass(appointment.AppointmentStatus)}">${appointment.AppointmentStatus || 'ĐANG CHỜ'}</span>
                    </small>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            <div>
                                <div class="small fw-bold">Bắt đầu</div>
                                <div>${formatDate(appointment.AppointmentDateStart)}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-check text-primary me-2"></i>
                            <div>
                                <div class="small fw-bold">Kết thúc</div>
                                <div>${formatDate(appointment.AppointmentDateEnd)}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-tie text-primary me-2"></i>
                            <div>
                                <div class="small fw-bold">Môi giới</div>
                                <div>${appointment.user_agent ? appointment.user_agent.Name : 'Chưa xác định'}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user text-primary me-2"></i>
                            <div>
                                <div class="small fw-bold">Người hẹn</div>
                                <div>${getAppointmentPerson(appointment)}</div>
                            </div>
                        </div>
                    </div>
                </div>

                ${appointment.property ? `
                <div class="mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-home text-primary me-2"></i>
                        <div>
                            <div class="small fw-bold">Bất động sản</div>
                            <div>${appointment.property.Title || 'Không có thông tin'}</div>
                        </div>
                    </div>
                </div>
                ` : ''}

                <div class="mb-0">
                    <div class="small fw-bold text-muted mb-2">Mô tả chi tiết:</div>
                    <div class="bg-light p-3 rounded appointment-notes">
                        ${appointment.DescAppoint || 'Không có mô tả chi tiết'}
                    </div>
                </div>
            </div>
        `;
    }
});
</script>
