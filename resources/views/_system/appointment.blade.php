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

<!-- Layout chính 2 cột -->
<div class="appointment-main-layout">
    <div class="row g-4">

        <!-- Cột 1: Bộ lọc và tìm kiếm -->
        <div class="col-lg-3">
            <div class="search-filters-panel">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-search me-2"></i>
                            Tìm kiếm cuộc hẹn
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="appointmentSearchForm">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tìm kiếm môi giới</label>
                                <div class="position-relative">
                                    <input type="text" id="agentSearch" class="form-control"
                                           placeholder="Nhập tên môi giới..." autocomplete="off">
                                    <button type="button" id="clearAgentSearch" class="btn btn-sm btn-outline-secondary position-absolute"
                                            style="right: 5px; top: 50%; transform: translateY(-50%); display: none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <input type="hidden" id="selectedAgentId" value="">
                                    <div id="agentSuggestions" class="dropdown-menu w-100" style="max-height: 200px; overflow-y: auto; display: none;">
                                        @foreach($agents as $agent)
                                            <a class="dropdown-item agent-suggestion" href="#"
                                               data-agent-id="{{ $agent->UserID }}"
                                               data-agent-name="{{ $agent->Name }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <i class="fas fa-user-tie"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $agent->Name }}</div>
                                                        @if($agent->profile_agent)
                                                            <small class="text-muted">{{ $agent->profile_agent->AreaAgent }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Từ ngày</label>
                                <input type="date" id="startDate" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Đến ngày</label>
                                <input type="date" id="endDate" class="form-control">
                            </div>

                            <div class="d-grid">
                                <button type="button" id="searchBtn" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Tìm kiếm
                                </button>
                            </div>
                        </form>
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
                        <div class="appointments-container">
                            <div class="initial-message text-center py-5">
                                <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tìm kiếm môi giới để xem cuộc hẹn</h5>
                                <p class="text-muted">Sử dụng bộ tìm kiếm ở trên để tìm kiếm cuộc hẹn theo môi giới và ngày tháng</p>
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

                <!-- Thông tin bất động sản -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-building me-2"></i>
                            Thông tin bất động sản
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="property-info">
                            <div class="no-data text-center py-4">
                                <i class="fas fa-home fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Chọn cuộc hẹn để xem thông tin BĐS</p>
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
            <div class="modal-footer">
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

/* Search filters panel */
.search-filters-panel .card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
    border-radius: 12px;
}

.search-filters-panel .form-label {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.search-filters-panel .form-control {
    border-radius: 8px;
    border: 1px solid #e3e6f0;
    transition: all 0.2s ease;
}

.search-filters-panel .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Agent search with suggestions */
.position-relative .dropdown-menu {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    margin-top: 2px;
}

/* Clear button styling */
#clearAgentSearch {
    width: 30px;
    height: 30px;
    padding: 0;
    border: 1px solid #dee2e6;
    background: white;
    border-radius: 4px;
    z-index: 5;
}

#clearAgentSearch:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
}

#clearAgentSearch i {
    font-size: 12px;
    color: #6c757d;
}

.agent-suggestion {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f1f1;
    transition: all 0.2s ease;
}

.agent-suggestion:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
}

.avatar-sm {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
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
    border-radius: 12px;
}

.no-selection, .no-data {
    color: #6c757d;
}

/* Property info card */
.property-info-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.property-info-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.property-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

/* Khi không có hình ảnh, property-details sẽ chiếm toàn bộ không gian */
.property-info-card .property-details {
    padding: 1rem;
}

.property-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2c3e50;
    line-height: 1.4;
}

.property-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #e74c3c;
    margin-bottom: 0.5rem;
}

.property-location {
    color: #7f8c8d;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.property-type {
    background: #3498db;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
}

.property-details {
    padding: 1rem;
}

.property-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.property-price {
    color: #28a745;
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.property-location {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.property-type {
    display: inline-block;
    background: #e3f2fd;
    color: #1976d2;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Responsive design */
@media (max-width: 992px) {
    .appointment-main-layout .col-lg-3,
    .appointment-main-layout .col-lg-6 {
        margin-bottom: 2rem;
    }

    .search-filters-panel .mb-3:last-child {
        margin-bottom: 0 !important;
    }
}

@media (max-width: 768px) {
    .appointments-grid-view {
        grid-template-columns: 1fr;
    }

    .property-image {
        height: 120px;
    }

    .search-filters-panel .card-body {
        padding: 1rem;
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
    const searchForm = document.getElementById('appointmentSearchForm');
    const appointmentCount = document.getElementById('appointmentCount');
    const appointmentsList = document.getElementById('appointments-list');
    const appointmentsGrid = document.getElementById('appointments-grid');
    const appointmentDetailSidebar = document.getElementById('appointment-detail-sidebar');
    const propertyInfo = document.getElementById('property-info');

    // Initialize event listeners
    initializeEventListeners();

    function initializeEventListeners() {
        // Search button
        const searchBtn = document.getElementById('searchBtn');
        if (searchBtn) {
            searchBtn.addEventListener('click', handleSearch);
        }

        // View type toggle
        const listView = document.getElementById('listView');
        if (listView) {
            listView.addEventListener('change', () => {
                if (document.getElementById('listView').checked) {
                    toggleView('list');
                }
            });
        }

        const gridView = document.getElementById('gridView');
        if (gridView) {
            gridView.addEventListener('change', () => {
                if (document.getElementById('gridView').checked) {
                    toggleView('grid');
                }
            });
        }

        // Agent search functionality
        const agentSearchInput = document.getElementById('agentSearch');
        const agentSuggestions = document.getElementById('agentSuggestions');
        const clearAgentBtn = document.getElementById('clearAgentSearch');

        if (agentSearchInput && agentSuggestions) {
            // Initially hide suggestions
            agentSuggestions.style.display = 'none';

            agentSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                if (searchTerm.length > 0) {
                    filterAgentSuggestions(searchTerm);
                    showClearButton();
                } else {
                    filterAgentSuggestions(''); // Hiển thị tất cả khi không có text
                    hideClearButton();
                    document.getElementById('selectedAgentId').value = '';
                }
            });

            // Hiển thị dropdown ngay khi focus vào input
            agentSearchInput.addEventListener('focus', function() {
                agentSuggestions.style.display = 'block';
                const searchTerm = this.value.toLowerCase().trim();
                filterAgentSuggestions(searchTerm);
            });

            // Ẩn dropdown khi blur (click ra ngoài)
            agentSearchInput.addEventListener('blur', function() {
                // Delay để cho phép click vào suggestion
                setTimeout(() => {
                    agentSuggestions.style.display = 'none';
                }, 200);
            });

            // Clear button functionality
            if (clearAgentBtn) {
                clearAgentBtn.addEventListener('click', function() {
                    agentSearchInput.value = '';
                    document.getElementById('selectedAgentId').value = '';
                    agentSuggestions.style.display = 'none';
                    hideClearButton();

                    // Clear appointments list
                    allAppointments = [];
                    filteredAppointments = [];
                    updateAppointmentCount(0);
                    appointmentsList.innerHTML = '<div class="text-center py-4 text-muted">Chọn môi giới để xem cuộc hẹn</div>';
                    appointmentsGrid.innerHTML = '<div class="text-center py-4 text-muted">Chọn môi giới để xem cuộc hẹn</div>';
                    appointmentDetailSidebar.innerHTML = '<div class="no-selection text-center py-4"><i class="fas fa-hand-point-left fa-2x text-muted mb-2"></i><p class="text-muted mb-0">Chọn cuộc hẹn để xem chi tiết</p></div>';
                });
            }
        }

        // Agent suggestion click
        document.querySelectorAll('.agent-suggestion').forEach(suggestion => {
            suggestion.addEventListener('click', function(e) {
                e.preventDefault();
                const agentId = this.getAttribute('data-agent-id');
                const agentName = this.getAttribute('data-agent-name');

                if (!agentId || agentId === 'null' || agentId === 'undefined') {
                    console.error('Invalid agentId:', agentId);
                    return;
                }

                agentSearchInput.value = agentName || '';
                document.getElementById('selectedAgentId').value = agentId;
                agentSuggestions.style.display = 'none';
                showClearButton();

                // Load appointments for selected agent
                loadAgentAppointments(agentId);
            });
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (agentSearchInput && agentSuggestions &&
                !agentSearchInput.contains(e.target) &&
                !agentSuggestions.contains(e.target)) {
                agentSuggestions.style.display = 'none';
            }
        });
    }

    function handleSearch() {
        const selectedAgentId = document.getElementById('selectedAgentId').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        if (!selectedAgentId && !startDate && !endDate) {
            showErrorMessage('Vui lòng chọn môi giới hoặc khoảng thời gian để tìm kiếm');
            return;
        }

        showLoadingState();

        // Nếu có agentId, dùng endpoint agent-specific
        if (selectedAgentId) {
            loadAgentAppointments(selectedAgentId);
            return;
        }

        // Nếu có date range, dùng search-by-range endpoint
        if (startDate && endDate) {
            const searchParams = new URLSearchParams();
            searchParams.append('start_date', startDate);
            searchParams.append('end_date', endDate);

            const endpoint = `/admin/appointment/search-by-range?${searchParams}`;

            fetch(endpoint)
                .then(response => response.json())
                .then(data => {
                    allAppointments = data.appointments || [];
                    filteredAppointments = [...allAppointments];
                    updateAppointmentCount(allAppointments.length);
                    renderAppointments();
                    hideInitialMessage();
                })
                .catch(error => {
                    console.error('Search by range error:', error);
                    showErrorMessage('Lỗi khi tìm kiếm cuộc hẹn theo khoảng thời gian');
                });
        }
    }

    function filterAgentSuggestions(searchTerm) {
        const suggestions = document.querySelectorAll('.agent-suggestion');
        suggestions.forEach(suggestion => {
            const agentName = suggestion.getAttribute('data-agent-name').toLowerCase();
            if (agentName.includes(searchTerm)) {
                suggestion.style.display = 'block';
            } else {
                suggestion.style.display = 'none';
            }
        });
    }

    function loadAgentAppointments(agentId) {
        console.log('Loading appointments for agent:', agentId);
        showLoadingState();

        fetch(`/admin/appointment/agent/${agentId}`)
            .then(response => {
                console.log('Agent appointments response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Agent appointments data:', data);
                allAppointments = data.appointments || [];
                filteredAppointments = [...allAppointments];
                console.log('Loaded appointments count:', allAppointments.length);
                updateAppointmentCount(allAppointments.length);
                renderAppointments();
                hideInitialMessage();
            })
            .catch(error => {
                console.error('Error loading agent appointments:', error);
                showErrorMessage(`Lỗi khi tải danh sách cuộc hẹn: ${error.message}`);
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

        const html = filteredAppointments.map(appointment => {
            const appointmentId = appointment.AppointmentID || 'unknown';
            return `
            <div class="appointment-card" data-appointment-id="${appointmentId}">
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
                        <span class="badge ${getStatusClass(appointment.Status)} mb-2">${appointment.Status || 'Pending'}</span>
                        ${appointment.property ? `<div class="small text-muted"><i class="fas fa-home"></i> ${appointment.property.Title}</div>` : ''}
                    </div>
                </div>
            </div>
            `;
        }).join('');

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

        const html = filteredAppointments.map(appointment => {
            const appointmentId = appointment.AppointmentID || 'unknown';
            return `
            <div class="appointment-card" data-appointment-id="${appointmentId}">
                <h6 class="mb-2">${appointment.TitleAppoint || 'Không có tiêu đề'}</h6>
                <p class="text-muted mb-2 small">${truncateText(appointment.DescAppoint || '', 80)}</p>
                <div class="mb-2">
                    <span class="badge ${getStatusClass(appointment.Status)}">${appointment.Status || 'Pending'}</span>
                </div>
                <div class="small text-muted">
                    <div><i class="fas fa-calendar"></i> ${formatDate(appointment.AppointmentDateStart)}</div>
                    <div><i class="fas fa-user"></i> ${getAppointmentPerson(appointment)}</div>
                    ${appointment.property ? `<div><i class="fas fa-home"></i> ${appointment.property.Title}</div>` : ''}
                </div>
            </div>
            `;
        }).join('');

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
        // Kiểm tra appointmentId hợp lệ
        if (!appointmentId || appointmentId === 'undefined' || appointmentId === 'null') {
            console.error('Invalid appointmentId:', appointmentId);
            return;
        }

        currentAppointmentId = appointmentId;

        // Remove selected class from all cards
        document.querySelectorAll('.appointment-card').forEach(card => {
            card.classList.remove('selected');
        });

        // Add selected class to clicked card
        const targetCard = document.querySelector(`[data-appointment-id="${appointmentId}"]`);
        if (targetCard) {
            targetCard.classList.add('selected');
        } else {
            console.error('Cannot find appointment card with ID:', appointmentId);
        }

        // Load appointment details
        loadAppointmentDetails(appointmentId);
    }

    function loadAppointmentDetails(appointmentId) {
        appointmentDetailSidebar.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

        fetch(`/admin/appointment/detail/${appointmentId}`)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('=== RAW API RESPONSE ===');
                console.log('Full data:', data);
                console.log('Appointment exists:', !!data.appointment);
                console.log('Property exists:', !!(data.appointment && data.appointment.property));

                // Render appointment details first
                renderAppointmentDetails(data.appointment);

                // Handle property info separately
                if (data.appointment && data.appointment.property) {
                    console.log('=== PROPERTY FOUND ===');
                    console.log('Property object:', data.appointment.property);
                    console.log('Property PropertyID:', data.appointment.property.PropertyID);
                    console.log('Property Title:', data.appointment.property.Title);

                    // Test if property-info element exists
                    const propertyInfoElement = document.getElementById('property-info');
                    console.log('Property info element exists:', !!propertyInfoElement);

                    // Render property info
                    renderPropertyInfo(data.appointment.property);
                } else {
                    console.log('=== NO PROPERTY DATA ===');
                    console.log('Appointment object:', data.appointment);
                    console.log('Property value:', data.appointment ? data.appointment.property : 'No appointment');

                    // Clear property info
                    const propertyInfoElement = document.getElementById('property-info');
                    if (propertyInfoElement) {
                        propertyInfoElement.innerHTML = `
                            <div class="no-data text-center py-4">
                                <i class="fas fa-home fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Không có thông tin BĐS</p>
                            </div>
                        `;
                    }
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
                    <span class="badge ${getStatusClass(appointment.Status)}">${appointment.Status || 'Pending'}</span>
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

                <div class="d-grid">
                    <button class="btn btn-primary btn-sm" onclick="showDetailModal('${appointment.AppointmentID}')">
                        <i class="fas fa-expand"></i> Xem chi tiết đầy đủ
                    </button>
                </div>
            </div>
        `;

        appointmentDetailSidebar.innerHTML = html;
    }

    function renderPropertyInfo(property) {
        console.log('Starting renderPropertyInfo with property:', property);

        if (!property) {
            console.log('No property data provided');
            document.getElementById('property-info').innerHTML = `
                <div class="no-data text-center py-4">
                    <i class="fas fa-home fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">Không có thông tin BĐS</p>
                </div>
            `;
            return;
        }

        // Kiểm tra các thuộc tính của property
        console.log('Property keys:', Object.keys(property));
        console.log('Property Title:', property.Title);
        console.log('Property Price:', property.Price);
        console.log('Property Address:', property.Address);
        console.log('Property images:', property.images);
        console.log('Property danhMuc:', property.danhMuc);
        console.log('Property danh_muc:', property.danh_muc);
        console.log('Property chiTiet:', property.chiTiet);
        console.log('Property chi_tiet:', property.chi_tiet);

        // Xây dựng src cho hình ảnh - Trả về hình ảnh rỗng nếu không có
        let propertyImage = '';
        if (property.images && property.images.length > 0) {
            propertyImage = `/storage/${property.images[0].image_path}`;
            console.log('Property image path:', propertyImage);
        }

        // Xác định thông tin danh mục
        let categoryName = 'Chưa phân loại';
        if (property.danhMuc && property.danhMuc.ten_pro) {
            categoryName = property.danhMuc.ten_pro;
        } else if (property.danh_muc && property.danh_muc.ten_pro) {
            categoryName = property.danh_muc.ten_pro;
        }

        const html = `
            <div class="property-info-card">
                ${propertyImage ? `<img src="${propertyImage}" alt="${property.Title || 'Property'}" class="property-image" onerror="this.style.display='none'">` : ''}
                <div class="property-details">
                    <h6 class="property-title">${property.Title || 'Chưa có tiêu đề'}</h6>
                    <div class="property-price">${formatPrice(property.Price)}</div>
                    <div class="property-location">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        ${property.Address || 'Chưa có địa chỉ'}
                    </div>
                    <div class="mt-2">
                        <span class="property-type">
                            ${categoryName}
                        </span>
                    </div>
                </div>
            </div>
        `;

        console.log('Updating property-info with HTML:', html);
        document.getElementById('property-info').innerHTML = html;
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

    // DEBUG FUNCTION - Temporary for testing
    window.testAPI = function(appointmentId) {
        console.log('=== TESTING API DIRECTLY ===');
        fetch(`/admin/appointment/detail/${appointmentId}`)
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text(); // Get as text first
            })
            .then(text => {
                console.log('Raw response text:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed JSON:', data);
                    console.log('Appointment property exists:', !!(data.appointment && data.appointment.property));
                    if (data.appointment && data.appointment.property) {
                        console.log('Property details:', data.appointment.property);
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                }
            })
            .catch(error => {
                console.error('API test error:', error);
            });
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
                        Trạng thái: <span class="badge ${getStatusClass(appointment.Status)}">${appointment.Status || 'ĐANG CHỜ'}</span>
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

    // Helper functions for clear button
    function showClearButton() {
        const clearBtn = document.getElementById('clearAgentSearch');
        if (clearBtn) {
            clearBtn.style.display = 'block';
        }
    }

    function hideClearButton() {
        const clearBtn = document.getElementById('clearAgentSearch');
        if (clearBtn) {
            clearBtn.style.display = 'none';
        }
    }

    // Utility functions
    function formatDate(dateString) {
        if (!dateString) return 'N/A';

        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            return dateString;
        }
    }

    function formatPrice(price) {
        if (!price || isNaN(price)) return 'Chưa có giá';

        const numPrice = parseFloat(price);
        if (numPrice >= 1000000000) {
            return (numPrice / 1000000000).toFixed(1) + ' tỷ VNĐ';
        } else if (numPrice >= 1000000) {
            return (numPrice / 1000000).toFixed(1) + ' triệu VNĐ';
        } else if (numPrice >= 1000) {
            return (numPrice / 1000).toFixed(0) + 'k VNĐ';
        } else {
            return numPrice.toLocaleString('vi-VN') + ' VNĐ';
        }
    }

    function getStatusClass(status) {
        switch (status?.toLowerCase()) {
            case 'confirmed':
            case 'đã xác nhận':
                return 'status-confirmed';
            case 'completed':
            case 'hoàn thành':
                return 'status-completed';
            case 'cancelled':
            case 'đã hủy':
                return 'status-cancelled';
            case 'pending':
            case 'đang chờ':
            default:
                return 'status-pending';
        }
    }

    function getAppointmentPerson(appointment) {
        if (appointment.user_customer) {
            return appointment.user_customer.Name || 'Khách hàng';
        } else if (appointment.user_owner) {
            return appointment.user_owner.Name || 'Chủ sở hữu';
        } else {
            return 'Chưa xác định';
        }
    }

    function truncateText(text, maxLength) {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }
});
</script>
