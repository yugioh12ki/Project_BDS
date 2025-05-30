@extends('_layout._layadmin.app')

@section('property')
<!-- Include enhanced styles -->
<link rel="stylesheet" href="{{ asset('css/property-enhancements.css') }}">

<div class="property-page-wrapper">
    <ul class="nav nav-tabs" id="mainPropertyTabs">
        <li class="nav-item">
            <a class="nav-link active" id="approve-tab" data-bs-toggle="tab" href="#approve-content">Kiểm Duyệt</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="assign-tab" data-bs-toggle="tab" href="#assign-content">Phân Công Môi Giới</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="approve-content">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>
                    Kiểm Duyệt Bất Động Sản
                    {{ isset($typePro) ? '- ' . $typePro : '' }}
                    @if(isset($status) && $status == 'pending')
                        <span class="badge bg-warning">Đang chờ duyệt</span>
                    @endif
                </h5>

                <div class="view-controls">
                    <div class="btn-group" role="group" aria-label="View mode">
                        <button type="button" class="btn btn-outline-secondary active" id="listViewBtn">
                            <i class="fas fa-list"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="mapViewBtn">
                            <i class="fas fa-map-marked-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Row: Property Table & Map (layout 8:4) -->
            <div class="row">
                <!-- Left column - Property Table (8/12 width) -->
                <div class="col-lg-8">
                    <div class="card property-table-card enhanced-card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-list"></i> Danh sách bất động sản
                            </h6>
                        </div>
                        <div class="card-body">
                            @if(isset($properties))
                                @include('_system.partialview.property_table', ['properties' => $properties, 'columns' => $columns])
                            @else
                                <div class="alert alert-info">Không có dữ liệu BĐS</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right column - Map (4/12 width) -->
                <div class="col-lg-4">
                    <div class="card map-card enhanced-card h-100" style="border-radius: 0 !important; overflow: hidden;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-map-marked-alt"></i> Bản đồ tương tác
                            </h6>
                            <div class="map-controls">
                                <button class="btn btn-sm btn-outline-light map-control-btn" id="fitAllMarkersBtn" title="Hiển thị tất cả vị trí">
                                    <i class="fas fa-compress-arrows-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-light map-control-btn" id="refreshMapBtn" title="Làm mới bản đồ">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="padding: 0 !important; margin: 0 !important; border: none !important;">
                            <div id="googleMap" style="height: 600px !important; width: 100% !important; border: none !important; border-radius: 0 !important; margin: 0 !important; padding: 0 !important; box-shadow: none !important;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property Details Modal -->
            <div class="modal fade" id="propertyDetailsModal" tabindex="-1" aria-labelledby="propertyDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="propertyDetailsModalLabel">
                                <i class="fas fa-info-circle me-2"></i>Thông tin chi tiết bất động sản
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="propertyDetailsContent">
                            <!-- Property details will be loaded here -->
                            <div class="text-center p-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                                <p class="mt-2 text-muted">Đang tải thông tin...</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Đóng
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="assign-content">
            <!-- Header với thống kê tổng quan -->
            <div class="assignment-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-0"><i class="fas fa-users-cog text-primary me-2"></i>Phân Công Môi Giới</h4>
                        <p class="text-muted mb-0">Quản lý phân công bất động sản cho môi giới</p>
                    </div>
                    <div class="col-md-6">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="stat-card">
                                    <div class="stat-number text-primary">{{ $agents->count() }}</div>
                                    <div class="stat-label">Môi giới</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-card">
                                    <div class="stat-number text-success">
                                        {{ $properties->where('Status', 'active')->whereNotNull('AgentID')->count() }}
                                    </div>
                                    <div class="stat-label">Đã phân công</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-card">
                                    <div class="stat-number text-warning">
                                        {{ $properties->where('Status', 'active')->whereNull('AgentID')->count() }}
                                    </div>
                                    <div class="stat-label">Chưa phân công</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Panel môi giới bên trái -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-users me-2"></i>Danh sách môi giới</h6>
                                <span class="badge bg-secondary">{{ $agents->count() }} người</span>
                            </div>
                            <div class="mt-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="agentSearch" placeholder="Tìm kiếm môi giới...">
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0 agents-card-body" style="max-height: 600px; overflow-y: auto;">
                            <div class="list-group list-group-flush" id="agentsList">
                                @foreach($agents as $agent)
                                <div class="list-group-item agent-item p-3 border-0 position-relative"
                                     data-agent-id="{{ $agent->UserID }}"
                                     style="cursor: pointer; transition: all 0.2s ease;">
                                    <div class="d-flex align-items-start">
                                        <div class="agent-avatar me-3">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                                 style="width: 45px; height: 45px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: bold;">
                                                {{ substr($agent->Name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1 fw-bold">{{ $agent->Name }}</h6>
                                                    <div class="text-muted small mb-1">
                                                        <i class="fas fa-envelope me-1"></i>{{ $agent->Email }}
                                                    </div>
                                                    <div class="text-muted small mb-1">
                                                        <i class="fas fa-phone me-1"></i>{{ $agent->Phone ?? 'Chưa có SĐT' }}
                                                    </div>
                                                    <div class="text-muted small">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        <span class="fw-medium">{{ $agent->profile_agent->AreaAgent ?? 'Chưa cơ sở' }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-{{ $agent->active_property_count >= 10 ? 'danger' : ($agent->active_property_count >= 7 ? 'warning' : 'success') }} mb-1">
                                                        {{ $agent->active_property_count }}/10
                                                    </span>
                                                    <div class="progress" style="height: 4px; width: 60px;">
                                                        <div class="progress-bar bg-{{ $agent->active_property_count >= 10 ? 'danger' : ($agent->active_property_count >= 7 ? 'warning' : 'success') }}"
                                                             style="width: {{ ($agent->active_property_count / 10) * 100 }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="selection-indicator position-absolute top-0 end-0 p-2" style="display: none;">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Panel quản lý bất động sản bên phải -->
                <div class="col-lg-8">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div class="mb-2 mb-md-0">
                                    <h6 class="mb-1">
                                        <i class="fas fa-building me-2"></i>Quản lý bất động sản
                                        @if(isset($typePro) && $typePro)
                                            <span class="badge {{ $typePro === 'Rent' ? 'bg-info' : 'bg-warning' }} ms-2">
                                                {{ $typePro === 'Rent' ? 'Thuê' : 'Bán' }}
                                            </span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">Môi giới: <span id="selectedAgentName" class="fw-bold text-primary">Chưa chọn</span></small>
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary active" id="showAssignedBtn">
                                        <i class="fas fa-check-circle me-1"></i>Đã phân công
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="showAvailableBtn">
                                        <i class="fas fa-plus-circle me-1"></i>Có thể phân công
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <!-- Tab hiển thị bất động sản đã phân công -->
                            <div id="assignedProperties" class="property-section">
                                <div id="loadingAssigned" class="text-center p-5">
                                    <div class="spinner-border text-primary mb-3" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                    <h6 class="text-muted">Vui lòng chọn môi giới</h6>
                                    <p class="small text-muted mb-0">Chọn một môi giới từ danh sách bên trái để xem các bất động sản đã được phân công</p>
                                </div>
                                <div class="table-responsive" style="display: none;">
                                    <table class="table table-hover align-middle mb-0" id="assignedPropertiesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="60" class="text-center">ID</th>
                                                <th>Thông tin bất động sản</th>
                                                <th width="120">Khu vực</th>
                                                <th width="100" class="text-center">Trạng thái</th>
                                                <th width="100" class="text-center">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Được điền bởi JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab hiển thị bất động sản có thể phân công -->
                            <div id="availableProperties" class="property-section" style="display: none;">
                                <div id="loadingAvailable" class="text-center p-5" style="display: none;">
                                    <div class="spinner-border text-primary mb-3" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                    <h6 class="text-muted">Đang tải dữ liệu...</h6>
                                    <p class="small text-muted mb-0">Vui lòng chờ trong giây lát</p>
                                </div>

                                <!-- Công cụ phân công -->
                                <div class="assignment-tools p-3 bg-light border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="alert alert-info mb-0 py-2">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <small>Chọn BĐS để phân công cho <strong id="assignAgentName" class="text-primary">Môi giới</strong></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="selectAllProperties">
                                                    <label class="form-check-label small" for="selectAllProperties">
                                                        Chọn tất cả
                                                    </label>
                                                </div>
                                                <button type="button" class="btn btn-primary btn-sm" id="assignSelectedBtn" disabled>
                                                    <i class="fas fa-check me-1"></i>Phân công (<span id="selectedCount">0</span>)
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="availablePropertiesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="40" class="text-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="headerCheckbox">
                                                    </div>
                                                </th>
                                                <th width="60" class="text-center">ID</th>
                                                <th>Thông tin bất động sản</th>
                                                <th width="120">Khu vực</th>
                                                <th width="100" class="text-center">Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($properties->where('Status', 'active')->whereNull('AgentID') as $property)
                                            <tr class="property-row" data-property-id="{{ $property->PropertyID }}">
                                                <td class="text-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input property-checkbox"
                                                               type="checkbox"
                                                               value="{{ $property->PropertyID }}"
                                                               data-property-id="{{ $property->PropertyID }}">
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark">{{ $property->PropertyID }}</span>
                                                </td>
                                                <td>
                                                    <div class="property-info">
                                                        <h6 class="mb-1 text-truncate" style="max-width: 250px;" title="{{ $property->Title }}">
                                                            {{ $property->Title }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-user me-1"></i>
                                                            {{ optional($property->chusohuu)->Name ?? 'Không có chủ sở hữu' }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $property->District }}<br>
                                                        {{ $property->Province }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success">{{ $property->Status }}</span>
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
            </div>

            <style>
            /* Assignment page styles */
            .assignment-header {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 0.5rem;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .stat-card {
                padding: 0.5rem;
            }

            .stat-number {
                font-size: 1.5rem;
                font-weight: bold;
                line-height: 1;
            }

            .stat-label {
                font-size: 0.75rem;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .agent-item {
                transition: all 0.3s ease;
                border-left: 3px solid transparent;
            }

            .agent-item:hover {
                background-color: #f8f9fa;
                border-left-color: #007bff;
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }

            .agent-item.active {
                background-color: #e3f2fd !important;
                border-left-color: #007bff !important;
                box-shadow: 0 4px 12px rgba(0,123,255,0.15);
            }

            .agent-item.active .selection-indicator {
                display: block !important;
            }

            .agent-avatar {
                position: relative;
            }

            .agent-avatar::after {
                content: '';
                position: absolute;
                bottom: -2px;
                right: -2px;
                width: 12px;
                height: 12px;
                background-color: #28a745;
                border: 2px solid white;
                border-radius: 50%;
            }

            .property-section {
                min-height: 400px;
            }

            .assignment-tools {
                border-bottom: 1px solid #dee2e6;
            }

            .property-info h6 {
                color: #495057;
            }

            .property-row {
                transition: all 0.2s ease;
            }

            .property-row:hover {
                background-color: #f8f9fa;
            }

            .property-checkbox:checked {
                background-color: #007bff;
                border-color: #007bff;
            }

            .table-responsive {
                border-radius: 0.375rem;
            }

            .table th {
                font-weight: 600;
                font-size: 0.875rem;
                color: #495057;
                border-bottom: 2px solid #dee2e6;
            }

            .btn-group .btn {
                border-radius: 0.375rem;
            }

            .btn-group .btn.active {
                background-color: #007bff;
                border-color: #007bff;
                color: white;
            }

            /* Responsive improvements */
            @media (max-width: 991.98px) {
                .assignment-header .col-md-6:first-child {
                    margin-bottom: 1rem;
                }

                .stat-card {
                    margin-bottom: 0.5rem;
                }

                .assignment-tools .col-md-6:first-child {
                    margin-bottom: 1rem;
                }
            }

            @media (max-width: 767.98px) {
                .agents-card-body {
                    max-height: 300px !important;
                }

                .property-info h6 {
                    font-size: 0.875rem;
                }

                .table-responsive {
                    font-size: 0.875rem;
                }
            }
            </style>

            <script>
            // Global function để refresh thống kê
            function refreshStatistics() {
                const totalAgents = document.querySelectorAll('.agent-item').length;
                const assignedCount = document.querySelector('.stat-number.text-success');
                const unassignedCount = document.querySelector('.stat-number.text-warning');

                // Calculate current assigned properties from agent badges
                let currentAssigned = 0;
                document.querySelectorAll('.agent-item').forEach(item => {
                    const badge = item.querySelector('.badge');
                    if (badge) {
                        const agentCount = parseInt(badge.textContent.split('/')[0]);
                        currentAssigned += agentCount;
                    }
                });

                // Calculate unassigned properties by counting properties in available tab
                let currentUnassigned = 0;
                const availablePropertiesTable = document.querySelector('#availableProperties table tbody');
                if (availablePropertiesTable) {
                    const rows = availablePropertiesTable.querySelectorAll('tr');
                    // Chỉ đếm những row thực sự có dữ liệu (không phải empty message)
                    rows.forEach(row => {
                        if (!row.querySelector('td[colspan]')) {
                            currentUnassigned++;
                        }
                    });
                }

                // Update statistics display if they exist
                if (assignedCount) {
                    assignedCount.textContent = currentAssigned;
                }
                if (unassignedCount) {
                    unassignedCount.textContent = currentUnassigned;
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                let selectedAgentId = null;
                let selectedAgentName = null;
                let selectedAgentCount = 0;

                // Lấy loại bất động sản hiện tại từ Blade (nếu có)
                const currentTypePro = @json($typePro ?? '');

                // Xử lý khi click vào môi giới
                const agentItems = document.querySelectorAll('.agent-item');
                agentItems.forEach(item => {
                    item.addEventListener('click', function() {
                        // Loại bỏ trạng thái đã chọn từ tất cả các item
                        agentItems.forEach(agentItem => {
                            agentItem.classList.remove('active');
                        });

                        // Đánh dấu item hiện tại là đã chọn
                        this.classList.add('active');

                        // Lưu thông tin môi giới được chọn
                        selectedAgentId = this.getAttribute('data-agent-id');
                        selectedAgentName = this.querySelector('h6').textContent.trim();
                        selectedAgentCount = parseInt(this.querySelector('.badge').textContent.split('/')[0]);

                        // Cập nhật tên môi giới trong các phần hiển thị
                        document.getElementById('selectedAgentName').textContent = selectedAgentName;
                        document.getElementById('assignAgentName').textContent = selectedAgentName;

                        console.log('Selected agent:', { id: selectedAgentId, name: selectedAgentName, count: selectedAgentCount });

                        // Hiển thị bất động sản đã phân công và kích hoạt tab "Đã phân công"
                        showAssignedTab();
                        loadAssignedProperties(selectedAgentId);

                        // Tự động hiển thị tab đã phân công sau khi chọn môi giới
                        setTimeout(() => {
                            showAssignedTab();
                        }, 100);
                    });
                });

                // Tìm kiếm môi giới
                const agentSearch = document.getElementById('agentSearch');
                agentSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    agentItems.forEach(item => {
                        const agentName = item.querySelector('h6').textContent.toLowerCase();
                        const agentEmail = item.querySelector('.text-muted').textContent.toLowerCase();
                        if (agentName.includes(searchTerm) || agentEmail.includes(searchTerm)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });

                // Chuyển đổi tab giữa danh sách đã phân công và sẵn sàng phân công
                const showAssignedBtn = document.getElementById('showAssignedBtn');
                const showAvailableBtn = document.getElementById('showAvailableBtn');
                const assignedProperties = document.getElementById('assignedProperties');
                const availableProperties = document.getElementById('availableProperties');

                // Helper functions for tab management
                function showAssignedTab() {
                    showAssignedBtn.classList.remove('btn-outline-secondary');
                    showAssignedBtn.classList.add('btn-outline-primary', 'active');
                    showAvailableBtn.classList.remove('btn-outline-primary', 'active');
                    showAvailableBtn.classList.add('btn-outline-secondary');
                    assignedProperties.style.display = '';
                    availableProperties.style.display = 'none';

                    // Đảm bảo bảng và dữ liệu hiển thị đúng
                    const tableDiv = document.querySelector('#assignedProperties .table-responsive');
                    if (tableDiv) tableDiv.style.display = 'block';
                }

                function showAvailableTab() {
                    showAvailableBtn.classList.remove('btn-outline-secondary');
                    showAvailableBtn.classList.add('btn-outline-primary', 'active');
                    showAssignedBtn.classList.remove('btn-outline-primary', 'active');
                    showAssignedBtn.classList.add('btn-outline-secondary');
                    assignedProperties.style.display = 'none';
                    availableProperties.style.display = '';

                    // Tải bất động sản khả dụng (không có AgentID)
                    loadAvailableProperties();
                }

                document.getElementById('showAssignedBtn').addEventListener('click', function() {
                    if (!selectedAgentId) {
                        showNotification('warning', 'Vui lòng chọn môi giới trước');
                        return;
                    }
                    showAssignedTab();
                    loadAssignedProperties(selectedAgentId);
                });

                showAvailableBtn.addEventListener('click', function() {
                    if (!selectedAgentId) {
                        showNotification('warning', 'Vui lòng chọn môi giới trước');
                        return;
                    }
                    showAvailableTab();
                });

                // Chọn tất cả các bất động sản
                const headerCheckbox = document.getElementById('headerCheckbox');
                headerCheckbox.addEventListener('change', function() {
                    const propertyCheckboxes = document.querySelectorAll('.property-checkbox');
                    propertyCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateSelectedCount();
                });

                // Cập nhật số lượng đã chọn
                function updateSelectedCount() {
                    const count = document.querySelectorAll('.property-checkbox:checked').length;
                    document.getElementById('selectedCount').textContent = count;
                    document.getElementById('assignSelectedBtn').disabled = count === 0;
                }

                // Xử lý sự kiện khi chọn từng bất động sản
                document.querySelectorAll('.property-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelectedCount);
                });

                // Phân công bất động sản đã chọn
                document.getElementById('assignSelectedBtn').addEventListener('click', function() {
                    if (!selectedAgentId) {
                        alert('Vui lòng chọn môi giới trước');
                        return;
                    }

                    const selectedProperties = Array.from(document.querySelectorAll('.property-checkbox:checked')).map(cb => cb.value);

                    if (selectedProperties.length === 0) {
                        alert('Vui lòng chọn ít nhất một bất động sản');
                        return;
                    }

                    // Kiểm tra số lượng bất động sản active đã phân công
                    if (selectedAgentCount + selectedProperties.length > 10) {
                        alert(`Môi giới đã quản lý ${selectedAgentCount} bất động sản active. Không thể thêm ${selectedProperties.length} bất động sản nữa (giới hạn 10).`);
                        return;
                    }

                    // Xác nhận phân công
                    if (confirm(`Xác nhận phân công ${selectedProperties.length} bất động sản cho môi giới ${selectedAgentName}?`)) {
                        // Show loading state
                        const assignBtn = document.getElementById('assignSelectedBtn');
                        const originalHTML = assignBtn.innerHTML;
                        assignBtn.disabled = true;
                        assignBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang phân công...';

                        // Tạo form data
                        const formData = new FormData();
                        formData.append('agentId', selectedAgentId);
                        selectedProperties.forEach(propId => {
                            formData.append('propertyIds[]', propId);
                        });
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                        // Gửi yêu cầu phân công
                        fetch('{{ route("admin.assign.properties") }}', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Cập nhật UI
                                showNotification('success', 'Phân công thành công!');

                                // Cập nhật số lượng bất động sản của môi giới
                                const badge = document.querySelector(`.agent-item[data-agent-id="${selectedAgentId}"] .badge`);
                                const progressBar = document.querySelector(`.agent-item[data-agent-id="${selectedAgentId}"] .progress-bar`);
                                const newCount = parseInt(badge.textContent.split('/')[0]) + selectedProperties.length;
                                badge.textContent = `${newCount}/10`;

                                // Cập nhật màu badge
                                badge.className = `badge ${newCount >= 10 ? 'bg-danger' : (newCount >= 7 ? 'bg-warning' : 'bg-success')} mb-1`;

                                // Cập nhật progress bar
                                if (progressBar) {
                                    progressBar.style.width = `${(newCount / 10) * 100}%`;
                                    progressBar.className = `progress-bar ${newCount >= 10 ? 'bg-danger' : (newCount >= 7 ? 'bg-warning' : 'bg-success')}`;
                                }

                                // Cập nhật biến toàn cục
                                selectedAgentCount = newCount;

                                // Xóa các dòng đã phân công khỏi bảng
                                selectedProperties.forEach(propId => {
                                    const row = document.querySelector(`.property-row[data-property-id="${propId}"]`);
                                    if (row) row.remove();
                                });

                                // Reset các checkbox
                                const headerCheckbox = document.getElementById('headerCheckbox');
                                const selectAllProperties = document.getElementById('selectAllProperties');
                                if (headerCheckbox) headerCheckbox.checked = false;
                                if (selectAllProperties) selectAllProperties.checked = false;
                                updateSelectedCount();

                                // Cập nhật thống kê
                                refreshStatistics();

                                // Hiển thị lại tab đã phân công và cập nhật danh sách
                                showAssignedTab();
                                loadAssignedProperties(selectedAgentId);
                            } else {
                                showNotification('error', data.message || 'Có lỗi xảy ra khi phân công');
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            showNotification('error', 'Có lỗi xảy ra khi phân công');
                        })
                        .finally(() => {
                            // Restore button state
                            const assignBtn = document.getElementById('assignSelectedBtn');
                            assignBtn.disabled = false;
                            assignBtn.innerHTML = '<i class="fas fa-check me-1"></i>Phân công (<span id="selectedCount">0</span>)';
                            updateSelectedCount(); // Update the count
                        });
                    }
                });

                // Hàm tải danh sách bất động sản đã phân công cho môi giới
                function loadAssignedProperties(agentId) {
                    const loadingDiv = document.getElementById('loadingAssigned');
                    const tableDiv = document.querySelector('#assignedProperties .table-responsive');
                    const tableBody = document.querySelector('#assignedPropertiesTable tbody');

                    if (!loadingDiv || !tableDiv || !tableBody) {
                        console.error('Missing DOM elements for assigned properties');
                        return;
                    }

                    // Hiển thị loading
                    loadingDiv.style.display = 'block';
                    tableDiv.style.display = 'none';
                    tableBody.innerHTML = '';

                    // Kiểm tra vị trí hiển thị của phần tử
                    if (loadingDiv.offsetParent === null) {
                        console.warn('loadingDiv is not visible in the DOM');
                    }
                    if (tableDiv.offsetParent === null) {
                        console.warn('tableDiv is not visible in the DOM');
                    }

                    // Gọi API lấy bất động sản đã phân công
                    console.log('Fetching properties for agent ID:', agentId, 'with TypePro:', currentTypePro);

                    // Tạo URL với tham số TypePro nếu có
                    let assignedUrl = `{{ url('/admin/agent') }}/${agentId}/properties`;
                    if (currentTypePro) {
                        assignedUrl += `?typePro=${encodeURIComponent(currentTypePro)}`;
                    }

                    // Gọi API lấy bất động sản đã phân công - FIX: Sửa URL để đảm bảo đúng định dạng ID của agent
                    fetch(assignedUrl)
                        .then(response => {
                            console.log('API Response status:', response.status);

                            // Kiểm tra nếu response không thành công
                            if (!response.ok) {
                                throw new Error(`Network response was not ok: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            loadingDiv.style.display = 'none';
                            tableDiv.style.display = 'block';

                            console.log('Assigned properties data:', data); // Thêm log để debug

                            if (data.length === 0) {
                                tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="text-center py-3">
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                            Không có bất động sản nào được phân công cho môi giới này
                                        </td>
                                    </tr>
                                `;
                            } else {
                                tableBody.innerHTML = data.map(property => `
                                    <tr class="property-row" data-property-id="${property.PropertyID}">
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">${property.PropertyID}</span>
                                        </td>
                                        <td>
                                            <div class="property-info">
                                                <h6 class="mb-1 text-truncate" style="max-width: 250px;" title="${property.Title}">
                                                    ${property.Title}
                                                </h6>
                                                <div class="d-flex align-items-center">
                                                    <small class="text-muted me-2">
                                                        <i class="fas fa-user me-1"></i>
                                                        ${property.OwnerName || 'Không có chủ sở hữu'}
                                                    </small>
                                                    <span class="badge ${property.TypePro === 'Rent' ? 'bg-info' : 'bg-warning'} text-white">
                                                        ${property.TypePro === 'Rent' ? 'Thuê' : 'Bán'}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                ${property.District}<br>
                                                ${property.Province}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-${getBadgeColor(property.Status)}">${property.Status}</span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-danger unassign-btn"
                                                    data-property-id="${property.PropertyID}"
                                                    title="Hủy phân công">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `).join('');

                                // Xử lý sự kiện hủy phân công
                                document.querySelectorAll('.unassign-btn').forEach(btn => {
                                    btn.addEventListener('click', function() {
                                        const propertyId = this.getAttribute('data-property-id');
                                        if (confirm('Xác nhận hủy phân công bất động sản này?')) {
                                            // Gọi API hủy phân công
                                            const formData = new FormData();
                                            formData.append('propertyId', propertyId);
                                            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                                            fetch('{{ route("admin.unassign.property") }}', {
                                                method: 'POST',
                                                body: formData
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    // Cập nhật UI
                                                    showNotification('success', 'Hủy phân công thành công!');

                                                    // Cập nhật số lượng
                                                    const badge = document.querySelector(`.agent-item[data-agent-id="${selectedAgentId}"] .badge`);
                                                    const progressBar = document.querySelector(`.agent-item[data-agent-id="${selectedAgentId}"] .progress-bar`);
                                                    const newCount = parseInt(badge.textContent.split('/')[0]) - 1;
                                                    badge.textContent = `${newCount}/10`;

                                                    // Cập nhật màu badge
                                                    badge.className = `badge ${newCount >= 10 ? 'bg-danger' : (newCount >= 7 ? 'bg-warning' : 'bg-success')} mb-1`;

                                                    // Cập nhật progress bar
                                                    if (progressBar) {
                                                        progressBar.style.width = `${(newCount / 10) * 100}%`;
                                                        progressBar.className = `progress-bar ${newCount >= 10 ? 'bg-danger' : (newCount >= 7 ? 'bg-warning' : 'bg-success')}`;
                                                    }

                                                    // Cập nhật biến toàn cục
                                                    selectedAgentCount = newCount;

                                                    // Cập nhật thống kê
                                                    refreshStatistics();

                                                    // Xóa dòng khỏi bảng
                                                    this.closest('tr').remove();

                                                    // Nếu không còn dòng nào, hiển thị thông báo
                                                    if (tableBody.children.length === 0) {
                                                        tableBody.innerHTML = `
                                                            <tr>
                                                                <td colspan="5" class="text-center py-3">
                                                                    <i class="fas fa-info-circle text-info me-2"></i>
                                                                    Không có bất động sản nào được phân công cho môi giới này
                                                                </td>
                                                            </tr>
                                                        `;
                                                    }
                                                } else {
                                                    showNotification('error', data.message || 'Có lỗi xảy ra khi hủy phân công');
                                                }
                                            })
                                            .catch(error => {
                                                console.error(error);
                                                showNotification('error', 'Có lỗi xảy ra khi hủy phân công');
                                            });
                                        }
                                    });
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching assigned properties:', error);
                            loadingDiv.style.display = 'none';
                            tableDiv.style.display = 'block';
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="6" class="text-center py-3">
                                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                        Có lỗi xảy ra khi tải dữ liệu: ${error.message}
                                    </td>
                                </tr>
                            `;
                        });
                }

                // Hàm tải danh sách bất động sản khả dụng (status = "active" và AgentID = NULL hoặc AgentID = "")
                function loadAvailableProperties() {
                    const loadingDiv = document.getElementById('loadingAvailable');
                    const tableDiv = document.querySelector('#availableProperties .table-responsive');
                    const tableBody = document.querySelector('#availablePropertiesTable tbody');

                    // Kiểm tra các phần tử DOM
                    if (!loadingDiv) console.error('loadingAvailable element not found');
                    if (!tableDiv) console.error('availableProperties .table-responsive element not found');
                    if (!tableBody) {
                        // Nếu tbody chưa tồn tại, tạo mới
                        const table = document.getElementById('availablePropertiesTable');
                        if (table) {
                            if (!table.querySelector('tbody')) {
                                const newTbody = document.createElement('tbody');
                                table.appendChild(newTbody);
                                console.log('Created new tbody element for availablePropertiesTable');
                                tableBody = newTbody;
                            }
                        } else {
                            console.error('availablePropertiesTable element not found');
                        }
                    }

                    // Hiển thị loading
                    if (loadingDiv) loadingDiv.style.display = 'block';
                    if (tableDiv) tableDiv.style.display = 'none';
                    if (tableBody) tableBody.innerHTML = '';

                    console.log('Fetching available properties with status=active and TypePro:', currentTypePro);

                    // Tạo URL với tham số status và TypePro
                    let availableUrl = '{{ route("admin.available.properties") }}?status=active';
                    if (currentTypePro) {
                        availableUrl += `&typePro=${encodeURIComponent(currentTypePro)}`;
                    }

                    // Gọi API lấy bất động sản khả dụng (chỉ lấy status="active" và AgentID=null hoặc AgentID="")
                    fetch(availableUrl)
                        .then(response => {
                            console.log('API Response status for available properties:', response.status);
                            // Kiểm tra nếu response không thành công
                            if (!response.ok) {
                                throw new Error(`Network response was not ok: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (loadingDiv) loadingDiv.style.display = 'none';
                            if (tableDiv) tableDiv.style.display = 'block';

                            if (!tableBody) return;

                            console.log('Available properties data:', data); // Thêm log để debug

                            if (data.length === 0) {
                                tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="text-center py-3">
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                            Không có bất động sản nào khả dụng để phân công
                                        </td>
                                    </tr>
                                `;
                            } else {
                                tableBody.innerHTML = data.map(property => `
                                    <tr class="property-row" data-property-id="${property.PropertyID}">
                                        <td class="text-center">
                                            <div class="form-check">
                                                <input class="form-check-input property-checkbox"
                                                       type="checkbox"
                                                       value="${property.PropertyID}"
                                                       data-property-id="${property.PropertyID}">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">${property.PropertyID}</span>
                                        </td>
                                        <td>
                                            <div class="property-info">
                                                <h6 class="mb-1 text-truncate" style="max-width: 250px;" title="${property.Title}">
                                                    ${property.Title}
                                                </h6>
                                                <div class="d-flex align-items-center">
                                                    <small class="text-muted me-2">
                                                        <i class="fas fa-user me-1"></i>
                                                        ${property.OwnerName || 'Không có chủ sở hữu'}
                                                    </small>
                                                    <span class="badge ${property.TypePro === 'Rent' ? 'bg-info' : 'bg-warning'} text-white">
                                                        ${property.TypePro === 'Rent' ? 'Thuê' : 'Bán'}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                ${property.District}<br>
                                                ${property.Province}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-${getBadgeColor(property.Status)}">${property.Status}</span>
                                        </td>
                                    </tr>
                                `).join('');

                                // Gán lại sự kiện cho các checkbox
                                document.querySelectorAll('.property-checkbox').forEach(checkbox => {
                                    checkbox.addEventListener('change', updateSelectedCount);
                                });

                                // Gán lại sự kiện cho cả hai selectAll checkbox
                                const selectAllCheckbox = document.getElementById('headerCheckbox');
                                const selectAllProperties = document.getElementById('selectAllProperties');

                                // Đồng bộ cả hai checkbox "Chọn tất cả"
                                const syncCheckboxes = (isChecked) => {
                                    const propertyCheckboxes = document.querySelectorAll('.property-checkbox');
                                    propertyCheckboxes.forEach(checkbox => {
                                        checkbox.checked = isChecked;
                                    });

                                    // Đồng bộ trạng thái của cả hai checkbox
                                    if (selectAllCheckbox) selectAllCheckbox.checked = isChecked;
                                    if (selectAllProperties) selectAllProperties.checked = isChecked;

                                    updateSelectedCount();
                                };

                                // Gán sự kiện cho checkbox trong header
                                if (selectAllCheckbox) {
                                    selectAllCheckbox.checked = false;
                                    selectAllCheckbox.addEventListener('change', function() {
                                        syncCheckboxes(this.checked);
                                    });
                                }

                                // Gán sự kiện cho checkbox "Chọn tất cả" bên trên
                                if (selectAllProperties) {
                                    selectAllProperties.checked = false;
                                    selectAllProperties.addEventListener('change', function() {
                                        syncCheckboxes(this.checked);
                                    });
                                }
                            }

                            // Cập nhật thống kê sau khi load xong
                            refreshStatistics();
                        })
                        .catch(error => {
                            console.error('Error fetching available properties:', error);
                            if (loadingDiv) loadingDiv.style.display = 'none';
                            if (tableDiv) tableDiv.style.display = 'block';
                            if (tableBody) {
                                tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Đã xảy ra lỗi khi tải dữ liệu: ${error.message}
                                        </td>
                                    </tr>
                                `;
                            }
                        });
                }

                // Xác định màu cho badge dựa trên trạng thái
                function getBadgeColor(status) {
                    switch(status.toLowerCase()) {
                        case 'active': return 'success';
                        case 'pending': return 'warning';
                        case 'inactive': return 'secondary';
                        case 'rejected': return 'danger';
                        default: return 'info';
                    }
                }

                // Auto-refresh statistics every 30 seconds
                setInterval(refreshStatistics, 30000); // Every 30 seconds

                // Add loading state management
                function setLoadingState(element, isLoading) {
                    if (isLoading) {
                        element.disabled = true;
                        element.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
                    } else {
                        element.disabled = false;
                        element.innerHTML = '<i class="fas fa-check me-1"></i>Phân công (<span id="selectedCount">0</span>)';
                    }
                }

                // Enhanced features for better UX

                // Keyboard shortcuts
                document.addEventListener('keydown', function(e) {
                    // Ctrl + A to select all available properties
                    if (e.ctrlKey && e.key === 'a' && document.getElementById('availableProperties').style.display !== 'none') {
                        e.preventDefault();
                        const selectAllCheckbox = document.getElementById('selectAllProperties');
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = !selectAllCheckbox.checked;
                            selectAllCheckbox.dispatchEvent(new Event('change'));
                        }
                    }

                    // Enter to assign selected properties
                    if (e.key === 'Enter' && document.activeElement === document.getElementById('assignSelectedBtn')) {
                        e.preventDefault();
                        document.getElementById('assignSelectedBtn').click();
                    }
                });

                // Initialize tooltips for better guidance
                function initializeTooltips() {
                    const tooltipElements = document.querySelectorAll('[title]');
                    tooltipElements.forEach(element => {
                        element.setAttribute('data-bs-toggle', 'tooltip');
                        element.setAttribute('data-bs-placement', 'top');
                    });

                    // Initialize Bootstrap tooltips if available
                    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                        new bootstrap.Tooltip(document.body, {
                            selector: '[data-bs-toggle="tooltip"]'
                        });
                    }
                }

                // Call on DOM ready
                initializeTooltips();

                // Enhanced error handling with retry mechanism
                function fetchWithRetry(url, options, retries = 3) {
                    return fetch(url, options)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                            return response.json();
                        })
                        .catch(error => {
                            if (retries > 0) {
                                console.warn(`Request failed, retrying... (${retries} attempts left)`);
                                return new Promise(resolve => {
                                    setTimeout(() => {
                                        resolve(fetchWithRetry(url, options, retries - 1));
                                    }, 1000);
                                });
                            }
                            throw error;
                        });
                }

                // Smooth scroll to selected agent
                function scrollToSelectedAgent(agentId) {
                    const agentElement = document.querySelector(`.agent-item[data-agent-id="${agentId}"]`);
                    if (agentElement) {
                        agentElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }

                // Auto-save draft selections to localStorage
                function saveDraftSelections() {
                    const selectedProps = Array.from(document.querySelectorAll('.property-checkbox:checked')).map(cb => cb.value);
                    if (selectedAgentId && selectedProps.length > 0) {
                        localStorage.setItem('draft_assignment', JSON.stringify({
                            agentId: selectedAgentId,
                            agentName: selectedAgentName,
                            properties: selectedProps,
                            timestamp: Date.now()
                        }));
                    } else {
                        localStorage.removeItem('draft_assignment');
                    }
                }

                // Restore draft selections on page load
                function restoreDraftSelections() {
                    const draft = localStorage.getItem('draft_assignment');
                    if (draft) {
                        try {
                            const draftData = JSON.parse(draft);
                            const hourAgo = Date.now() - (60 * 60 * 1000); // 1 hour

                            if (draftData.timestamp > hourAgo) {
                                showNotification('info', `Khôi phục lựa chọn cho môi giới ${draftData.agentName} (${draftData.properties.length} bất động sản)`);

                                // Select the agent
                                const agentElement = document.querySelector(`.agent-item[data-agent-id="${draftData.agentId}"]`);
                                if (agentElement) {
                                    agentElement.click();

                                    // Wait a bit then select properties
                                    setTimeout(() => {
                                        showAvailableTab();
                                        setTimeout(() => {
                                            draftData.properties.forEach(propId => {
                                                const checkbox = document.querySelector(`.property-checkbox[value="${propId}"]`);
                                                if (checkbox) checkbox.checked = true;
                                            });
                                            updateSelectedCount();
                                        }, 500);
                                    }, 200);
                                }
                            } else {
                                localStorage.removeItem('draft_assignment');
                            }
                        } catch (e) {
                            console.error('Error restoring draft selections:', e);
                            localStorage.removeItem('draft_assignment');
                        }
                    }
                }

                // Save draft on property selection change
                document.addEventListener('change', function(e) {
                    if (e.target.classList.contains('property-checkbox')) {
                        saveDraftSelections();
                    }
                });

                // Restore draft selections on page load
                setTimeout(restoreDraftSelections, 1000);

                // Add visual feedback for agent workload
                function updateAgentWorkloadDisplay() {
                    document.querySelectorAll('.agent-item').forEach(item => {
                        const badge = item.querySelector('.badge');
                        const count = parseInt(badge.textContent.split('/')[0]);
                        const workloadIndicator = item.querySelector('.workload-indicator');

                        // Add workload indicator if it doesn't exist
                        if (!workloadIndicator) {
                            const indicator = document.createElement('div');
                            indicator.className = 'workload-indicator position-absolute';
                            indicator.style.cssText = `
                                top: 5px;
                                left: 5px;
                                width: 8px;
                                height: 8px;
                                border-radius: 50%;
                                z-index: 10;
                            `;
                            item.style.position = 'relative';
                            item.appendChild(indicator);
                        }

                        const indicator = item.querySelector('.workload-indicator');
                        if (count >= 9) {
                            indicator.style.backgroundColor = '#dc3545'; // red
                        } else if (count >= 7) {
                            indicator.style.backgroundColor = '#ffc107'; // yellow
                        } else if (count >= 5) {
                            indicator.style.backgroundColor = '#fd7e14'; // orange
                        } else {
                            indicator.style.backgroundColor = '#28a745'; // green
                        }
                    });
                }

                // Update workload display on page load
                updateAgentWorkloadDisplay();

                // Gọi refresh statistics khi load trang để có dữ liệu ban đầu chính xác
                setTimeout(() => {
                    refreshStatistics();
                }, 500);

                // ...existing code...
            });
            </script>
        </div>
    </div>
<<<<<<< HEAD
</div>



<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
   integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
   crossorigin=""/>

<!-- Leaflet Fullscreen Plugin CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/2.0.0/Control.FullScreen.css" />

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
   integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
   crossorigin=""></script>

<!-- Leaflet Fullscreen Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/2.0.0/Control.FullScreen.min.js"></script>

<!-- Map JavaScript -->
<script>
// Make map and markers globally accessible
var map;
var markers = [];

// Function to show notification
function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : type === 'info' ? 'info' : 'danger'} alert-dismissible fade show notification-toast`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Add to document
    document.body.appendChild(notification);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

// Initialize map with Leaflet
function initMap() {
    // Default center of Vietnam
    var vietnamLat = 16.0;
    var vietnamLng = 106.0;

    // Create map
    map = L.map('googleMap').setView([vietnamLat, vietnamLng], 5);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add fullscreen control
    if (typeof L.Control.Fullscreen === 'function') {
        map.addControl(new L.Control.Fullscreen());
    }

    // Add markers for properties if coordinates exist
    markers = [];
    var bounds = [];

    @if(isset($propertyCoordinates))
        @foreach($propertyCoordinates as $property)
            var lat = {{ $property['lat'] }};
            var lng = {{ $property['lng'] }};

            // Create marker
            var marker = L.marker([lat, lng], {
                title: '{{ $property['title'] }}',
                propertyId: '{{ $property['id'] }}'
            }).addTo(map);

            // Add popup with property info
            marker.bindPopup('<div class="map-info-window">' +
                '<strong>{{ $property['title'] }}</strong>' +
                '<p>{{ $property['address'] }}</p>' +
                '</div>');

            // Add marker click event
            marker.on('click', function() {
                highlightProperty('{{ $property['id'] }}');
            });

            // Add to markers array and bounds
            markers.push(marker);
            bounds.push([lat, lng]);
        @endforeach

        // If we have markers, fit the map to show all of them
        if (markers.length > 0) {
            // Create a bounds object
            var boundsObj = L.latLngBounds(bounds);
            map.fitBounds(boundsObj);
            // Don't zoom in too far on only one marker
            if (markers.length === 1) {
                map.setZoom(15);
            }
        }
    @endif

    // Function to select property on map when clicked in list
    window.selectPropertyOnMap = function(id, lat, lng, address) {
        if (typeof map === 'undefined') return;

        // Find the marker with the matching property ID
        var targetMarker = markers.find(marker => marker.options.propertyId === id);

        if (targetMarker) {
            // Open popup for this marker
            targetMarker.openPopup();

            // Center map on this property
            map.setView(targetMarker.getLatLng(), 15);

            // Highlight the property in the list
            highlightProperty(id);
        } else if (lat && lng && lat !== '' && lng !== '') {
            // If no markers but we have valid coordinates, center on those
            map.setView([parseFloat(lat), parseFloat(lng)], 15);

            // Create a new marker
            var row = document.getElementById('property-row-' + id);
            var title = row ? row.querySelector('.property-title').textContent : 'Bất động sản #' + id;

            var newMarker = L.marker([parseFloat(lat), parseFloat(lng)], {
                propertyId: id,
                title: title
            }).addTo(map);

            // Get additional property information
            var propertyType = '';
            var propertyPrice = '';
            var propertyDate = '';

            if (row) {
                try {
                    // Lấy loại bất động sản từ row data-category an toàn
                    let categorySelector = row.getAttribute('data-category') ?
                        document.querySelector(`.property-row[data-category="${row.getAttribute('data-category')}"] td:nth-child(3)`) : null;
                    propertyType = categorySelector ? categorySelector.textContent.trim() : '';
                } catch (e) {
                    propertyType = '';
                    console.log('Error getting property type:', e);
                }

                // Lấy giá và ngày an toàn hơn
                propertyPrice = row.getAttribute('data-price') ? new Intl.NumberFormat('vi-VN').format(row.getAttribute('data-price')) + ' VND' : '';
                propertyDate = row.getAttribute('data-date') || '';
            }

            // Add detailed popup with formatted address and property info
            var popupContent = '<div class="map-info-window">' +
                '<strong>' + title + '</strong>';

            if (address && address !== '') {
                popupContent += '<p><i class="fas fa-map-marker-alt"></i> ' + address + '</p>';
            }

            if (propertyType) {
                popupContent += '<p><i class="fas fa-home"></i> ' + propertyType + '</p>';
            }

            if (propertyPrice) {
                popupContent += '<p><i class="fas fa-tags"></i> ' + propertyPrice + '</p>';
            }

            if (propertyDate) {
                popupContent += '<p><i class="far fa-calendar-alt"></i> ' + propertyDate + '</p>';
            }

            popupContent += '</div>';

            newMarker.bindPopup(popupContent).openPopup();

            // Add click event
            newMarker.on('click', function() {
                highlightProperty(id);
            });

            markers.push(newMarker);

            // Highlight the property in the list
            highlightProperty(id);
        } else if (address && address !== '') {
            // If no markers and no coordinates, but we have an address, try to geocode it
            geocodeAddress(address, id);
        } else {
            // If no address found in parameters, try to get it from row attribute
            var row = document.getElementById('property-row-' + id);
            if (row && row.getAttribute('data-full-address')) {
                // Use full address (combines Address, Ward, District, Province)
                geocodeAddress(row.getAttribute('data-full-address'), id);
            } else if (row && row.getAttribute('data-address')) {
                // Fallback to just Address field if full address isn't available
                geocodeAddress(row.getAttribute('data-address'), id);
            } else {
                showNotification('error', 'Không tìm thấy địa chỉ cho bất động sản này');
            }
        }
    };    // Function to convert address to coordinates using Nominatim (OpenStreetMap's geocoder) with enhanced area searching
    function geocodeAddress(address, propertyId) {
        if (!address) return;

        // Hiển thị thông báo đang tìm kiếm
        showNotification('info', 'Đang tìm kiếm vị trí của bất động sản #' + propertyId + '...');

        // Đảm bảo địa chỉ được chuẩn bị đúng
        address = address.trim();

        // Lấy thông tin chi tiết từ data attribute để xây dựng địa chỉ hoàn chỉnh
        var row = document.getElementById('property-row-' + propertyId);
        var addressParts = {
            address: row ? row.getAttribute('data-address') : '',
            ward: row ? row.getAttribute('data-ward') : '',
            district: row ? row.getAttribute('data-district') : '',
            province: row ? row.getAttribute('data-province') : ''
        };

        console.log('Address parts:', addressParts);

        // Xây dựng danh sách địa chỉ để tìm kiếm theo độ ưu tiên
        var searchAddresses = buildSearchAddressList(addressParts, address);

        console.log('Search addresses:', searchAddresses);

        // Thực hiện tìm kiếm theo thứ tự ưu tiên
        performGeocodingWithFallback(searchAddresses, propertyId, 0);
    }

    // Xây dựng danh sách địa chỉ tìm kiếm theo độ ưu tiên
    function buildSearchAddressList(parts, originalAddress) {
        var addresses = [];

        // 1. Địa chỉ đầy đủ ban đầu
        if (originalAddress && originalAddress.trim() !== '') {
            addresses.push(originalAddress + ', Việt Nam');
        }

        // 2. Địa chỉ được xây dựng từ các phần đầy đủ
        if (parts.address && parts.ward && parts.district && parts.province) {
            addresses.push(`${parts.address}, ${parts.ward}, ${parts.district}, ${parts.province}, Việt Nam`);
        }

        // 3. Địa chỉ không có số nhà (chỉ phường, quận, tỉnh)
        if (parts.ward && parts.district && parts.province) {
            addresses.push(`${parts.ward}, ${parts.district}, ${parts.province}, Việt Nam`);
        }

        // 4. Chỉ quận và tỉnh
        if (parts.district && parts.province) {
            addresses.push(`${parts.district}, ${parts.province}, Việt Nam`);
        }

        // 5. Chỉ tỉnh
        if (parts.province) {
            addresses.push(`${parts.province}, Việt Nam`);
        }

        // 6. Fallback cuối cùng - tìm theo tên gần đúng
        if (parts.district && parts.province) {
            // Thử tìm với tên rút gọn
            var shortDistrict = parts.district.replace(/^(Quận|Huyện|Thành phố|TP\.)\s*/i, '');
            var shortProvince = parts.province.replace(/^(Tỉnh|Thành phố|TP\.)\s*/i, '');
            addresses.push(`${shortDistrict}, ${shortProvince}, Vietnam`);
        }

        return addresses.filter(addr => addr && addr.trim() !== ', Việt Nam');
    }

    // Thực hiện geocoding với fallback
    function performGeocodingWithFallback(addresses, propertyId, currentIndex) {
        if (currentIndex >= addresses.length) {
            showNotification('error', 'Không thể tìm thấy vị trí cho bất động sản này sau khi thử tất cả các cách');
            return;
        }

        var currentAddress = addresses[currentIndex];
        console.log(`Trying address ${currentIndex + 1}/${addresses.length}:`, currentAddress);

        // Hiển thị thông báo tiến trình
        if (currentIndex > 0) {
            showNotification('info', `Đang thử tìm kiếm theo vùng... (${currentIndex + 1}/${addresses.length})`);
        }

        // Sử dụng Nominatim API (miễn phí) của OpenStreetMap
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(currentAddress) + '&addressdetails=1&limit=5')
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                // Tìm kết quả tốt nhất dựa trên type và importance
                var bestResult = findBestGeocodingResult(data, currentIndex);

                if (bestResult) {
                    const lat = parseFloat(bestResult.lat);
                    const lng = parseFloat(bestResult.lon);

                    console.log(`Found location for address ${currentIndex + 1}:`, bestResult.display_name);

                    // Tạo marker và hiển thị
                    createPropertyMarker(propertyId, lat, lng, bestResult.display_name, currentIndex);

                    // Thông báo thành công với mức độ chính xác
                    var accuracyMessage = getAccuracyMessage(currentIndex);
                    showNotification('success', `Đã tìm thấy vị trí ${accuracyMessage}`);

                } else {
                    // Thử địa chỉ tiếp theo
                    performGeocodingWithFallback(addresses, propertyId, currentIndex + 1);
                }
            } else {
                // Thử địa chỉ tiếp theo
                performGeocodingWithFallback(addresses, propertyId, currentIndex + 1);
            }
        })
        .catch(error => {
            console.error('Geocoding error:', error);
            // Thử địa chỉ tiếp theo nếu có lỗi
            performGeocodingWithFallback(addresses, propertyId, currentIndex + 1);
        });
    }

    // Tìm kết quả geocoding tốt nhất
    function findBestGeocodingResult(results, searchLevel) {
        if (!results || results.length === 0) return null;

        // Ưu tiên theo loại địa điểm và importance
        var priorityTypes = ['house', 'building', 'residential', 'place', 'road', 'suburb', 'village', 'town', 'city', 'state'];

        // Với tìm kiếm cấp cao hơn (theo vùng), chấp nhận kết quả rộng hơn
        if (searchLevel >= 2) {
            priorityTypes = ['place', 'suburb', 'village', 'town', 'city', 'county', 'state', 'house', 'building'];
        }

        var bestResult = null;
        var bestScore = -1;

        results.forEach(result => {
            var score = 0;

            // Điểm theo importance
            score += (parseFloat(result.importance) || 0) * 10;

            // Điểm theo loại
            var typeIndex = priorityTypes.findIndex(type =>
                result.type && result.type.toLowerCase().includes(type) ||
                result.class && result.class.toLowerCase().includes(type)
            );
            if (typeIndex >= 0) {
                score += (priorityTypes.length - typeIndex) * 2;
            }

            // Điểm nếu có address details đầy đủ
            if (result.address) {
                score += Object.keys(result.address).length * 0.5;
            }

            if (score > bestScore) {
                bestScore = score;
                bestResult = result;
            }
        });

        return bestResult;
    }

    // Lấy thông báo độ chính xác
    function getAccuracyMessage(searchLevel) {
        switch(searchLevel) {
            case 0: return 'chính xác (địa chỉ đầy đủ)';
            case 1: return 'chính xác (địa chỉ chi tiết)';
            case 2: return 'theo vùng phường/xã';
            case 3: return 'theo vùng quận/huyện';
            case 4: return 'theo vùng tỉnh/thành phố';
            default: return 'gần đúng';
        }
    }

    // Tạo marker cho bất động sản
    function createPropertyMarker(propertyId, lat, lng, displayName, searchLevel) {
        // Kiểm tra xem marker đã tồn tại chưa
        let existingMarker = markers.find(marker => marker.options.propertyId === propertyId);

        if (existingMarker) {
            // Cập nhật vị trí marker hiện có
            existingMarker.setLatLng([lat, lng]);
            map.setView([lat, lng], getZoomLevel(searchLevel));
            existingMarker.openPopup();
            return;
        }

        // Lấy thông tin bất động sản từ data attribute của row
        var row = document.getElementById('property-row-' + propertyId);
        var title = row ? row.querySelector('.property-title').textContent : 'Bất động sản #' + propertyId;

        // Lấy thêm thông tin chi tiết từ data attribute (nếu có)
        var propertyType = '';
        var propertyPrice = '';
        var propertyDate = '';

        if (row) {
            try {
                // Lấy loại bất động sản từ row data-category
                let categorySelector = row.getAttribute('data-category') ?
                    document.querySelector(`.property-row[data-category="${row.getAttribute('data-category')}"] td:nth-child(3)`) : null;
                propertyType = categorySelector ? categorySelector.textContent.trim() : '';
            } catch (e) {
                propertyType = '';
                console.log('Error getting property type:', e);
            }

            // Lấy giá và ngày an toàn hơn
            propertyPrice = row.getAttribute('data-price') ? new Intl.NumberFormat('vi-VN').format(row.getAttribute('data-price')) + ' VND' : '';
            propertyDate = row.getAttribute('data-date') || '';
        }

        // Tạo marker mới với icon khác nhau tùy theo độ chính xác
        var markerIcon = getMarkerIcon(searchLevel);
        var marker = L.marker([lat, lng], {
            propertyId: propertyId,
            title: title,
            icon: markerIcon
        }).addTo(map);

        // Popup chi tiết cho marker với nhiều thông tin hơn
        var popupContent = '<div class="map-info-window enhanced-popup">' +
            '<div class="popup-header">' +
            '<strong class="popup-title">' + title + '</strong>' +
            '<div class="accuracy-badge">' + getAccuracyMessage(searchLevel) + '</div>' +
            '</div>';

        if (displayName) {
            popupContent += '<div class="popup-address"><i class="fas fa-map-marker-alt text-danger"></i> ' + displayName + '</div>';
        }

        if (propertyType) {
            popupContent += '<div class="popup-info"><i class="fas fa-home text-info"></i> <strong>Loại:</strong> ' + propertyType + '</div>';
        }

        if (propertyPrice) {
            popupContent += '<div class="popup-info"><i class="fas fa-tags text-success"></i> <strong>Giá:</strong> ' + propertyPrice + '</div>';
        }

        if (propertyDate) {
            popupContent += '<div class="popup-info"><i class="far fa-calendar-alt text-warning"></i> <strong>Ngày đăng:</strong> ' + propertyDate + '</div>';
        }

        popupContent += '</div>';

        marker.bindPopup(popupContent).openPopup();

        // Sự kiện click cho marker
        marker.on('click', function() {
            highlightProperty(propertyId);
        });

        markers.push(marker);

        // Di chuyển bản đồ tới marker mới tạo với zoom level phù hợp
        map.setView([lat, lng], getZoomLevel(searchLevel));

        // Cập nhật data attribute của dòng bất động sản
        if (row) {
            row.setAttribute('data-lat', lat);
            row.setAttribute('data-lng', lng);
        }

        // Highlight property trong danh sách
        highlightProperty(propertyId);
    }

    // Lấy zoom level dựa trên độ chính xác tìm kiếm
    function getZoomLevel(searchLevel) {
        switch(searchLevel) {
            case 0:
            case 1: return 16; // Địa chỉ chính xác
            case 2: return 14; // Phường/xã
            case 3: return 12; // Quận/huyện
            case 4: return 10; // Tỉnh/thành phố
            default: return 8; // Mặc định
        }
    }

    // Lấy icon marker dựa trên độ chính xác
    function getMarkerIcon(searchLevel) {
        var iconColor = searchLevel <= 1 ? 'green' : searchLevel <= 2 ? 'orange' : 'red';

        // Sử dụng default Leaflet marker với màu khác nhau
        return new L.Icon({
            iconUrl: `https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${iconColor}.png`,
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
    }

    // Function to highlight property in list
    function highlightProperty(id) {
        // Remove highlight from all rows
        document.querySelectorAll('.property-row').forEach(function(row) {
            row.classList.remove('highlighted-row');
        });

        // Add highlight to selected row
        var row = document.getElementById('property-row-' + id);
        if (row) {
            row.classList.add('highlighted-row');
            row.scrollIntoView({behavior: 'smooth', block: 'center'});
        }
    }
}

// Handle property approval
document.addEventListener('DOMContentLoaded', function() {
    // Delegate event handler for approve buttons (works for dynamically added elements)
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('approve-btn') ||
           (e.target.parentElement && e.target.parentElement.classList.contains('approve-btn'))) {

            const button = e.target.classList.contains('approve-btn') ? e.target : e.target.parentElement;
            const propertyId = button.getAttribute('data-property-id');

            if (confirm('Bạn có chắc chắn muốn duyệt bất động sản này không?')) {
                updatePropertyStatus(propertyId, 'approved');
            }
        }
    });

    // Delegate event handler for reject buttons
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('reject-btn') ||
           (e.target.parentElement && e.target.parentElement.classList.contains('reject-btn'))) {

            const button = e.target.classList.contains('reject-btn') ? e.target : e.target.parentElement;
            const propertyId = button.getAttribute('data-property-id');

            if (confirm('Bạn có chắc chắn muốn từ chối bất động sản này không?')) {
                updatePropertyStatus(propertyId, 'rejected');
            }
        }
    });

    // Use the new PropertyManagement module for status updates
    function updatePropertyStatus(propertyId, status, reason = null) {
        // Call the function from our PropertyManagement module
        PropertyManagement.updatePropertyStatus(propertyId, status, reason);
    }
});
</script>
<!-- Load app.js with PropertyManagement module -->
<script src="{{ mix('js/app.js') }}" defer></script>

<!-- OpenStreetMap attrbution -->
<script>
    // Initialize the map when document is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
    });
</script>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tab navigation
        const mainTabs = document.querySelectorAll('#mainPropertyTabs .nav-link');

        mainTabs.forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                // Gỡ bỏ active từ tất cả các tab
                mainTabs.forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content > .tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                // Thêm active cho tab được chọn
                this.classList.add('active');

                // Hiển thị nội dung tương ứng
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.classList.add('show', 'active');

                    // Nếu là tab assignment, cập nhật thống kê
                    if (this.getAttribute('href') === '#assign-content') {
                        // Gọi refreshStatistics sau một khoảng thời gian ngắn để đảm bảo DOM đã được render
                        setTimeout(() => {
                            if (typeof refreshStatistics === 'function') {
                                refreshStatistics();
                            }
                        }, 100);
                    }
                }
            });
        });

        // View mode toggle (list/map)
        const listViewBtn = document.getElementById('listViewBtn');
        const mapViewBtn = document.getElementById('mapViewBtn');

        if (listViewBtn && mapViewBtn) {
            // Set initial state based on screen size
            if (window.innerWidth < 992) {
                // On mobile, start with list view
                PropertyManagement.toggleViewMode('list');
            }

            // List view button click
            listViewBtn.addEventListener('click', function() {
                PropertyManagement.toggleViewMode('list');
            });

            // Map view button click
            mapViewBtn.addEventListener('click', function() {
                PropertyManagement.toggleViewMode('map');
            });
        }

        // Fit all markers button
        const fitAllMarkersBtn = document.getElementById('fitAllMarkersBtn');
        if (fitAllMarkersBtn) {
            fitAllMarkersBtn.addEventListener('click', function() {
                if (typeof map !== 'undefined' && markers && markers.length > 0) {
                    PropertyManagement.fitAllMarkersToMap(map, markers);
                }
            });
        }
    });
</script>

<style>
    .property-page-wrapper {
        background-color: transparent;
    }

    /* Enhanced Navigation Tabs Styling */
    .nav-tabs {
        display: flex;
        justify-content: flex-start;
        border-bottom: 2px solid #1976d2;
        margin-bottom: 0;
        background: linear-gradient(135deg, #f0f8ff 0%, #e3f2fd 100%);
        border-radius: 12px 12px 0 0;
        padding: 8px 16px 0 16px;
        box-shadow: 0 2px 8px rgba(25, 118, 210, 0.1);
    }

    .nav-tabs .nav-item {
        margin-bottom: 0;
        margin-right: 4px;
    }

    .nav-tabs .nav-link {
        border: 2px solid #e3f2fd;
        border-radius: 12px 12px 0 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #2c3e50;
        background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
        margin-right: 0;
        font-weight: 600;
        padding: 12px 24px;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.875rem;
        border-bottom: none;
    }

    .nav-tabs .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(25, 118, 210, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .nav-tabs .nav-link:hover {
        border-color: #1976d2;
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        color: #1565c0;
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(25, 118, 210, 0.3);
    }

    .nav-tabs .nav-link:hover::before {
        left: 100%;
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        border-color: #1976d2;
        color: white !important;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(25, 118, 210, 0.4);
        animation: shimmer 2s infinite;
    }

    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #1976d2, #42a5f5, #1976d2);
        animation: flow 3s ease-in-out infinite;
    }

    @keyframes shimmer {
        0%, 100% {
            box-shadow: 0 8px 25px rgba(25, 118, 210, 0.4);
        }
        50% {
            box-shadow: 0 12px 35px rgba(25, 118, 210, 0.6);
        }
    }

    @keyframes flow {
        0%, 100% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
    }

    /* Enhanced Alert Styling */
    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .alert::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        animation: alertShimmer 3s ease-in-out infinite;
    }

    @keyframes alertShimmer {
        0% { left: -100%; }
        50%, 100% { left: 100%; }
    }

    .alert-info {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        color: #0d47a1;
        border-left: 4px solid #1976d2;
    }

    .alert-warning {
        background: linear-gradient(135deg, #fff3e0 0%, #ffcc80 100%);
        color: #e65100;
        border-left: 4px solid #ff9800;
    }

    .alert-success {
        background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
        color: #1b5e20;
        border-left: 4px solid #4caf50;
    }

    .alert-danger {
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
        color: #c62828;
        border-left: 4px solid #f44336;
    }

    /* Enhanced Map Info Window Styling */
    .map-info-window {
        min-width: 280px;
        max-width: 380px;
        padding: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
        border: 2px solid #e3f2fd;
    }

    .map-info-window.enhanced-popup {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .popup-header {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        padding: 15px 20px;
        position: relative;
        overflow: hidden;
    }

    .popup-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .popup-title {
        color: white !important;
        font-size: 16px !important;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
        text-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    .accuracy-badge {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 8px;
        border: 1px solid rgba(255,255,255,0.3);
        position: relative;
        z-index: 1;
    }

    .popup-content {
        padding: 20px;
    }

    .popup-address {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        padding: 12px 15px;
        margin: -20px -20px 15px -20px;
        font-size: 13px;
        color: #1565c0;
        font-weight: 500;
        border-bottom: 1px solid #e1f5fe;
    }

    .popup-info {
        display: flex;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
        color: #37474f;
        transition: all 0.2s ease;
    }

    .popup-info:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .popup-info:hover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        margin: 8px -20px;
        padding: 8px 20px;
        border-radius: 6px;
    }

    .popup-info i {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border-radius: 50%;
        font-size: 10px;
        color: #1976d2 !important;
        flex-shrink: 0;
    }

    .popup-info strong {
        color: #1565c0;
        margin-right: 8px;
        min-width: 60px;
        font-weight: 600;
    }

    /* Leaflet popup customization */
    .leaflet-popup-content-wrapper {
        background: transparent;
        border-radius: 12px;
        box-shadow: none;
        padding: 0;
    }

    .leaflet-popup-content {
        margin: 0;
        border-radius: 12px;
        overflow: hidden;
    }

    .leaflet-popup-tip {
        background: #1976d2;
        box-shadow: none;
        border: 2px solid #e3f2fd;
    }

    .leaflet-popup-close-button {
        color: white !important;
        font-size: 20px !important;
        font-weight: bold;
        width: 30px !important;
        height: 30px !important;
        display: flex !important;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        margin: 8px 8px 0 0;
        transition: all 0.3s ease;
        top: 0 !important;
        right: 0 !important;
        z-index: 1000;
        text-decoration: none !important;
    }

    .leaflet-popup-close-button:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
        color: #ffeb3b !important;
    }
    /* Enhanced Property Cards and General Styling */
    .property-details-card,
    .map-card,
    .property-table-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
    }

    .property-details-card:hover,
    .map-card:hover,
    .property-table-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    .property-details-card .card-header,
    .map-card .card-header,
    .property-table-card .card-header {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        border-bottom: none;
        color: white;
        padding: 15px 20px;
        position: relative;
        overflow: hidden;
    }

    .property-details-card .card-header::before,
    .map-card .card-header::before,
    .property-table-card .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .property-details-card .card-header h6,
    .map-card .card-header h6,
    .property-table-card .card-header h6 {
        color: white;
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
        position: relative;
        z-index: 1;
        text-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    .property-details-card .card-header i,
    .map-card .card-header i,
    .property-table-card .card-header i {
        margin-right: 8px;
        opacity: 0.9;
    }

    /* Property Information Cards */
    .info-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fdff 100%);
        border: 1px solid #e3f2fd;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border-color: #1976d2;
    }

    .info-card-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1565c0;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e3f2fd;
        display: flex;
        align-items: center;
    }

    .info-card-title i {
        color: #1976d2;
        margin-right: 10px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border-radius: 50%;
        font-size: 12px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        padding: 8px 0;
        transition: all 0.2s ease;
        border-radius: 6px;
    }

    .info-item:hover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 8px 12px;
        margin: 12px -12px;
    }

    .info-item:not(:last-child) {
        border-bottom: 1px solid #f0f4f8;
    }

    .info-label {
        font-weight: 600;
        color: #37474f;
        flex: 0 0 auto;
        margin-right: 15px;
        display: flex;
        align-items: center;
        min-width: 120px;
    }

    .info-label i {
        margin-right: 8px;
        color: #1976d2;
        width: 16px;
    }

    .info-value {
        color: #546e7a;
        text-align: right;
        flex: 1;
        word-break: break-word;
        font-weight: 500;
    }

    /* Property Badges */
    .property-badges {
        margin-top: 15px;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .property-badges .badge {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid transparent;
        transition: all 0.3s ease;
    }

    .property-badges .badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .property-id-badge {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%) !important;
        color: #1565c0 !important;
        border-color: #1976d2 !important;
    }

    .badge-rent {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
        color: white !important;
        border-color: #f5576c !important;
    }

    .badge-sale {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
        color: white !important;
        border-color: #00f2fe !important;
    }

    /* Owner Details Enhancement */
    .owner-details {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 15px;
        margin-top: 15px;
        border: 1px solid #dee2e6;
    }

    .owner-details .info-item {
        background: rgba(255,255,255,0.7);
        border-radius: 6px;
        padding: 8px 12px;
        margin-bottom: 8px;
        border-bottom: none;
    }

    .owner-details .info-item:hover {
        background: rgba(255,255,255,0.9);
        transform: none;
        margin: 8px 0;
    }
    }

    .nav-tabs .nav-link {
        border: 1px solid #ddd;
        border-radius: 4px 4px 0 0;
        margin-right: 5px;
        padding: 10px 15px;
        transition: background-color 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        background-color: #0056b3;
        color: #ffffff;
    }

    /* Property table styles */
    .property-row {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .property-row:hover {
        background-color: #f1f1f1;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .highlighted-row {
        background-color: #e9f5ff !important;
        border-left: 3px solid #0d6efd;
    }

    .pending-property {
        border-left: 3px solid #ffc107;
    }

    /* Enhanced Table Styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 1rem;
        background: white;
    }

    .table thead th {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        color: #ffffff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        padding: 15px 12px;
        border-bottom: none;
        position: sticky;
        top: 0;
        z-index: 10;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .table tbody td {
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f4f8;
        background: white;
        transition: all 0.2s ease;
    }

    .property-row {
        cursor: pointer;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
    }

    .property-row:hover {
        background: linear-gradient(135deg, #f8fdff 0%, #e3f2fd 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(25, 118, 210, 0.1);
    }

    .property-row:hover td {
        background: transparent;
    }

    .highlighted-row {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%) !important;
        border-left: 4px solid #1976d2;
        box-shadow: 0 4px 15px rgba(25, 118, 210, 0.15);
    }

    .highlighted-row td {
        background: transparent !important;
    }

    .pending-property {
        border-left: 4px solid #ff9800;
        background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    }

    /* Property Title Styling */
    .property-title {
        font-weight: 600;
        color: #1565c0;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .property-title:hover {
        color: #0d47a1;
        transform: translateX(2px);
    }

    /* Action Buttons Enhancement */
    .action-buttons-container {
        opacity: 0.7;
        transition: all 0.3s ease;
        display: flex;
        gap: 5px;
        justify-content: center;
    }

    .property-row:hover .action-buttons-container {
        opacity: 1;
        transform: scale(1.05);
    }

    .btn-group {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .btn-group .btn {
        border: none;
        padding: 6px 12px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Property table header */
    .property-table-header {
        background-color: #f8f9fa;
        padding: 12px;
        border-radius: 8px 8px 0 0;
        border: 1px solid #dee2e6;
        border-bottom: none;
    }

    /* Batch action buttons */
    .batch-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* Checkbox column */
    .checkbox-column {
        width: 40px;
        text-align: center;
    }

    /* Property title formatting */
    .property-title {
        font-weight: 500;
        color: #0056b3;
    }

    /* Table responsive scroll */
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    /* Scrollbar styling */
    .table-responsive::-webkit-scrollbar {
        width: 6px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #0056b3;
        border-radius: 10px;
    }

    /* Google map container */
    #googleMap {
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .col-md-7, .col-md-5 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        #googleMap {
            height: 300px !important;
            margin-top: 20px;
        }
    }

    /* Responsive layout for view toggle */
    @media (max-width: 991.98px) {
        .map-column.d-none {
            /* Enhanced Map Info Panel Styling */
    .map-info-panel {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .map-stat {
        padding: 0.5rem;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Enhanced Map Controls */
    .map-controls .btn {
        border-radius: 50%;
        width: 36px;
        height: 36px;
        padding: 0;
        margin-left: 5px;
        transition: all 0.2s ease;
    }

    .map-controls .btn:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(255,255,255,0.3);
    }

    /* Property Details Panel Enhancement */
    .property-notifications-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 12px;
        min-height: 500px;
        border: 1px solid #e9ecef;
    }

    .no-selection {
        background: linear-gradient(135deg, #f1f3f4 0%, #ffffff 100%);
        border-radius: 12px;
        border: 2px dashed #dee2e6;
        transition: all 0.3s ease;
    }

    .no-selection:hover {
        border-color: #1976d2;
        box-shadow: 0 4px 15px rgba(25, 118, 210, 0.1);
    }

    /* Enhanced Card Styling */
    .property-details-card,
    .map-card,
    .property-table-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .property-details-card:hover,
    .map-card:hover,
    .property-table-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    .property-details-card .card-header,
    .map-card .card-header,
    .property-table-card .card-header {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        border-bottom: none;
        color: white;
        padding: 1.25rem 1.5rem;
    }

    .property-details-card .card-header h6,
    .map-card .card-header h6,
    .property-table-card .card-header h6 {
        color: white;
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Google Map Enhancement */
    #googleMap {
        border: 3px solid #fff;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        transition: all 0.3s ease;
    }

    #googleMap:hover {
        box-shadow: 0 12px 35px rgba(0,0,0,0.2) !important;
    }

    /* Responsive Design Improvements */
    @media (max-width: 991.98px) {
        .property-details-card,
        .map-card {
            margin-bottom: 1.5rem;
        }

        .map-info-panel {
            margin-bottom: 1rem;
        }

        #googleMap {
            height: 350px !important;
        }
    }

    @media (max-width: 768px) {
        .property-details-card .card-header,
        .map-card .card-header {
            padding: 1rem;
        }

        .map-controls .btn {
            width: 32px;
            height: 32px;
        }
    }

    display: none !important;
        }

        .property-list-column.d-none {
            display: none !important;
        }

        .view-controls {
            display: flex;
        }
    }

    @media (min-width: 992px) {
        .view-controls {
            display: none;
        }
    }

    /* Popover styles for status confirmation */
    .status-action-buttons {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .popover {
        max-width: 200px;
    }

    .map-info-window {
        padding: 8px;
        max-width: 250px;
    }

    .nav-tabs .nav-link.active {
        background-color: #0056b3;
        border-color: #ddd #ddd transparent;
        color: #ffffff;
    }

    .tab-content {
        border-radius: 0 0 8px 8px;
        padding: 20px;
        min-height: 400px;
    }

    /* Toast notification styles */
    .notification-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        z-index: 9999;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        border-radius: 6px;
        animation: slide-in 0.3s ease-out forwards;
    }

    /* Property media display */
    .media-count-badge {
        margin-right: 5px;
        font-size: 0.75em;
        padding: 2px 5px;
    }

    .media-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .image-item {
        border-radius: 4px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }

    .image-item:hover {
        transform: scale(1.05);
        z-index: 1;
    }

    .video-container {
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .empty-media-message {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 4px;
        color: #6c757d;
        text-align: center;
    }

    @keyframes slide-in {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    /* Property updating animation */
    .property-row.updating {
        opacity: 0.6;
        position: relative;
    }

    .property-row.updating:after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.5) url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzgiIGhlaWdodD0iMzgiIHZpZXdCb3g9IjAgMCAzOCAzOCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBzdHJva2U9IiMzNDk4ZGIiPiAgICA8ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPiAgICAgICAgPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMSAxKSIgc3Ryb2tlLXdpZHRoPSIyIj4gICAgICAgICAgICA8Y2lyY2xlIHN0cm9rZS1vcGFjaXR5PSIuMyIgY3g9IjE4IiBjeT0iMTgiIHI9IjE4Ii8+ICAgICAgICAgICAgPHBhdGggZD0iTTM2IDE4YzAtOS45NC04LjA2LTE4LTE4LTE4Ij4gICAgICAgICAgICAgICAgPGFuaW1hdGVUcmFuc2Zvcm0gICAgICAgICAgICAgICAgICAgIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgICAgICAgICAgICAgICAgICAgIHR5cGU9InJvdGF0ZSIgICAgICAgICAgICAgICAgICAgIGZyb209IjAgMTggMTgiICAgICAgICAgICAgICAgICAgICB0bz0iMzYwIDE4IDE4IiAgICAgICAgICAgICAgICAgICAgZHVyPSIxcyIgICAgICAgICAgICAgICAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIi8+ICAgICAgICAgICAgPC9wYXRoPiAgICAgICAgPC9nPiAgICA8L2c+PC9zdmc+') center no-repeat;
        z-index: 2;
    }

    /* Keyboard shortcut hints */
    .shortcut-hint {
        display: inline-block;
        min-width: 20px;
        height: 20px;
        padding: 0 4px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-size: 12px;
        line-height: 18px;
        text-align: center;
        color: #495057;
        margin-left: 5px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    /* CSV export button */
    .csv-export-btn {
        transition: all 0.3s ease;
    }

    .csv-export-btn:hover {
        background-color: #28a745;
        color: #fff;
    }

    /* Selected row highlight */
    .property-row.selected-for-batch {
        background-color: #fffde7;
        box-shadow: 0 0 0 1px #ffc107;
    }

    /* Property status badges */
    .status-badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .status-badge.approved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-badge.rejected {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Property Details Modal Styling */
    #propertyDetailsModal .modal-dialog {
        max-width: 900px;
    }

    #propertyDetailsModal .modal-header {
        background: linear-gradient(135deg, #0d6efd, #0056b3);
        color: white;
        border-bottom: none;
    }

    #propertyDetailsModal .modal-header .modal-title {
        font-weight: 600;
    }

    #propertyDetailsModal .property-id-badge {
        background: rgba(255,255,255,0.2) !important;
        color: white !important;
        border: 1px solid rgba(255,255,255,0.3);
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
    }

    /* Modal badges styling */
    .property-badges .badge-rent {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
        color: white !important;
        border: none;
    }

    .property-badges .badge-sale {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
        color: white !important;
        border: none;
    }

    .property-badges .badge-unknown {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%) !important;
        color: #8b4513 !important;
        border: none;
    }

    .property-badges .badge-approved {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%) !important;
        color: #22c55e !important;
        border: none;
    }

    .property-badges .badge-pending {
        background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%) !important;
        color: #f59e0b !important;
        border: none;
    }

    .property-badges .badge-rejected {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%) !important;
        color: #ef4444 !important;
        border: none;
    }

    /* Property tabs in modal */
    .property-tabs-container {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .property-nav-tabs {
        border-bottom: none;
        padding: 0 1rem;
    }

    .property-nav-tabs .nav-link {
        border: none;
        background: transparent;
        color: #6c757d;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 1rem 1.5rem;
        position: relative;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .property-nav-tabs .nav-link:hover {
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(0, 86, 179, 0.1) 100%);
        color: #0d6efd;
        transform: translateY(-2px);
    }

    .property-nav-tabs .nav-link.active {
        background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
        color: white !important;
        border-radius: 25px 25px 0 0;
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.4);
    }

    /* Info cards in modal */
    .info-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    }

    .info-card-title {
        color: #495057;
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
    }

    .info-card-title i {
        color: #0d6efd;
        margin-right: 0.5rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
        padding: 0.5rem 0;
    }

    .info-item:not(:last-child) {
        border-bottom: 1px solid #f8f9fa;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
        flex: 0 0 auto;
        margin-right: 1rem;
        display: flex;
        align-items: center;
    }

    .info-value {
        color: #6c757d;
        text-align: right;
        flex: 1;
        word-break: break-word;
    }

    /* Owner details styling in modal */
    .owner-details .info-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        border: none;
        transition: all 0.3s ease;
    }

    .owner-details .info-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }

    .owner-details .info-label {
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .owner-details .info-value {
        font-size: 1rem;
        color: #495057;
        font-weight: 500;
        text-align: left;
    }

    /* User avatar in modal */
    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.5rem;
        text-transform: uppercase;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border: 3px solid white;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
    }

    /* Media gallery in modal */
    .media-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .media-item img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .media-item img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .video-container video,
    .video-container iframe {
        width: 100%;
        height: 120px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .image-caption,
    .video-caption {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.5rem;
        text-align: center;
        font-weight: 500;
    }

    .upload-date {
        font-size: 0.7rem;
        color: #adb5bd;
        text-align: center;
        margin-top: 0.25rem;
    }

    .empty-media-message {
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        text-align: center;
        color: #6c757d;
        font-size: 0.9rem;
    }

    /* Tab content animation */
    .tab-content .tab-pane {
        animation: fadeInUp 0.4s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Modal responsive design */
    @media (max-width: 768px) {
        #propertyDetailsModal .modal-dialog {
            max-width: 95%;
            margin: 1rem;
        }

        .property-nav-tabs .nav-link {
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
        }

        .property-nav-tabs .nav-link i {
            display: none;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }

        .info-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .info-label {
            margin-bottom: 0.25rem;
            margin-right: 0;
        }

        .info-value {
            text-align: left;
        }

        .media-gallery {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.75rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced property selection with animation
    const propertyRows = document.querySelectorAll('.property-row');
    const propertyDetailsArea = document.getElementById('property-notifications-area');
    
    // Initialize map refresh button
    const refreshMapBtn = document.getElementById('refreshMapBtn');
    if (refreshMapBtn) {
        refreshMapBtn.addEventListener('click', function() {
            this.classList.add('fa-spin');
            setTimeout(() => {
                this.classList.remove('fa-spin');
                if (typeof PropertyManagement !== 'undefined') {
                    PropertyManagement.refreshMap();
                }
            }, 1000);
        });
    }

    // Enhanced property row interaction
    propertyRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 25px rgba(25, 118, 210, 0.15)';
        });

        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('highlighted-row')) {
                this.style.transform = '';
                this.style.boxShadow = '';
            }
        });

        row.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('.btn')) {
                return;
            }

            // Remove highlight from other rows
            propertyRows.forEach(r => r.classList.remove('highlighted-row'));
            
            // Add highlight to clicked row
            this.classList.add('highlighted-row');

            // Animate property details panel
            if (propertyDetailsArea) {
                propertyDetailsArea.style.opacity = '0.5';
                setTimeout(() => {
                    propertyDetailsArea.style.opacity = '1';
                }, 300);
            }

            // Update visible properties counter
            updateVisiblePropertiesCount();
        });
    });

    // Update map statistics
    function updateVisiblePropertiesCount() {
        const visibleCount = document.querySelectorAll('.property-row:not(.d-none)').length;
        const visiblePropertiesElement = document.getElementById('visibleProperties');
        if (visiblePropertiesElement) {
            visiblePropertiesElement.textContent = visibleCount;
            
            // Animate counter
            visiblePropertiesElement.style.transform = 'scale(1.2)';
            setTimeout(() => {
                visiblePropertiesElement.style.transform = 'scale(1)';
            }, 200);
        }
    }

    // Enhanced map interaction
    if (typeof PropertyManagement !== 'undefined') {
        // Override the original selectPropertyOnMap function for better UX
        const originalSelectProperty = window.selectPropertyOnMap;
        window.selectPropertyOnMap = function(propertyId, lat, lng, address) {
            // Call original function
            if (originalSelectProperty) {
                originalSelectProperty(propertyId, lat, lng, address);
            }

            // Add visual feedback
            const mapElement = document.getElementById('googleMap');
            if (mapElement) {
                mapElement.style.border = '3px solid #1976d2';
                setTimeout(() => {
                    mapElement.style.border = '3px solid #fff';
                }, 1000);
            }

            // Show notification
            showMapNotification('Đã chọn bất động sản trên bản đồ', 'success');
        };
    }

    // Map notification system
    function showMapNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification-toast`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideInRight 0.3s ease;
        `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, 3000);
    }

    // Initialize counters
    updateVisiblePropertiesCount();

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .notification-toast {
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .fa-spin {
            animation: fa-spin 1s infinite linear;
        }
        @keyframes fa-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
});
</script>

<script src="{{ asset('js/agent-sorting.js') }}"></script>

=======
@else
<h1>Danh sách Property</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
    {{-- Combobox chọn type --}}
    <div class="d-flex align-items-center">
        <label for="type-select" class="me-2">Loại hình:</label>
        <select id="type-select" class="form-control" style="width: 200px;">
            <option value="all">Sale/Rent</option>
            <option value="Sale">Sale</option>
            <option value="Rent">Rent</option>
        </select>
    </div>

    <div style="margin: 20px"></div>

    {{-- Ô input và button tìm kiếm --}}
    <div class="d-flex align-items-center">
        <form action="#" class="d-flex align-items-center">
            <input type="text" name="keyword" id="search-input" class="form-control" placeholder="Nhập từ khóa tìm kiếm" style="width: 300px;">
            <button type="submit" class="btn btn-secondary ms-2">
                <i class="fa fa-search"></i> Tìm kiếm
            </button>
        </form>
    </div>
</div>

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#propertyModal">
    <i class="fa fa-plus"></i> Thêm mới
</button>



<div id="property-list">
    @if(isset($error))
    <div class="alert alert-danger">{{ $error }}</div>
    @else
    @include('_system.partialview.property_table', ['property' => $properties, 'columns' => $columns])
    @endif
</div>


{{-- Modal thêm property --}}
<div class="modal fade" id="propertyModal" tabindex="-1" aria-labelledby="propertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <h5 class="modal-title" id="propertyModalLabel">Thêm Bất Động Sản</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body">
            <ul class="nav nav-tabs" id="propertyTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Thông tin bất động sản</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab">Chi tiết bất động sản</button>
              </li>
            </ul>
            <div class="tab-content mt-3">
                @include('_system.partialview.create_property', ['owners' => $owners, 'agents' => $agents, 'categories' => $categories])
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-primary">Lưu</button>
          </div>
        </form>
      </div>
    </div>
</div>

<script>
    const typeSelect = document.getElementById('type-select');
    const propertyList = document.getElementById('property-list');

    if (typeSelect && propertyList) {
        typeSelect.addEventListener('change', function () {
            const type = this.value;

            // Gửi yêu cầu AJAX để lấy danh sách property theo role
            fetch(`/admin/property/type/${type}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Không tìm thấy dữ liệu.');
                }
                return response.text();
            })
            .then(html => {
                propertyList.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                propertyList.innerHTML = `
                    <div class="alert alert-danger">Không tìm thấy property nào.</div>
                `;
            });
        });
    }
</script>


@endif
>>>>>>> origin/Quynh_Customer
@endsection
