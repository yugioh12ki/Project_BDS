@extends('_layout._layadmin.app')

@section('transaction')
@if(isset($error))
    <div class="fa fa-danger">
        {{ $error }}
    </div>
@else
<div class="transaction-container">
    <h1 class="transaction-title">Quản lý Giao dịch & Hợp đồng</h1>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="transactionTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="contracts-tab" data-bs-toggle="tab" data-bs-target="#contracts" type="button" role="tab" aria-controls="contracts" aria-selected="true">
                    <i class="fas fa-file-contract me-2"></i>
                    Mẫu hợp đồng
                    <span class="badge bg-light text-dark ms-2">{{ count($contractTemplates) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab" aria-controls="upload" aria-selected="false">
                    <i class="fas fa-brain me-2"></i>
                    Tải lên & Phân tích
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab" aria-controls="transactions" aria-selected="false">
                    <i class="fas fa-exchange-alt me-2"></i>
                    Danh sách giao dịch
                    @if(isset($transactions))
                        <span class="badge bg-light text-dark ms-2">{{ count($transactions) }}</span>
                    @endif
                </button>
            </li>
        </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="transactionTabContent">
        <!-- Tab 1: Mẫu hợp đồng -->
        <div class="tab-pane fade show active" id="contracts" role="tabpanel" aria-labelledby="contracts-tab">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="fas fa-file-contract me-2 text-primary"></i>
                                Thư viện mẫu hợp đồng
                            </h5>
                            <p class="text-muted mb-0 small">
                                {{ count($contractTemplates) }} mẫu hợp đồng có sẵn
                            </p>
                        </div>
                        <div class="header-actions">
                            <button class="btn btn-outline-primary btn-sm me-2" id="refreshContracts" data-bs-toggle="tooltip" title="Làm mới danh sách">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button class="btn btn-primary btn-sm" id="addContractBtn" data-bs-toggle="modal" data-bs-target="#addContractModal">
                                <i class="fas fa-plus me-1"></i>Thêm mẫu hợp đồng
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(count($contractTemplates) > 0)
                        <div class="row g-4">
                            @foreach($contractTemplates as $template)
                            <div class="col-lg-6 col-xl-4">
                                <div class="contract-card h-100 border-0 shadow-sm">
                                    <div class="contract-header">
                                        <div class="contract-icon">
                                            <i class="fas fa-file-contract"></i>
                                        </div>
                                        <div class="contract-status">
                                            <span class="badge bg-success">Có sẵn</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">
                                        <h5 class="contract-title mb-2">{{ $template['display_name'] }}</h5>

                                        <div class="contract-meta mb-3">
                                            <div class="meta-item">
                                                <i class="fas fa-hdd text-muted me-2"></i>
                                                <span class="text-muted">{{ number_format($template['size'] / 1024, 1) }} KB</span>
                                            </div>
                                            <div class="meta-item">
                                                <i class="fas fa-calendar-alt text-muted me-2"></i>
                                                <span class="text-muted">{{ date('d/m/Y', $template['modified']) }}</span>
                                            </div>
                                            <div class="meta-item">
                                                <i class="fas fa-file-word text-primary me-2"></i>
                                                <span class="text-muted">Microsoft Word</span>
                                            </div>
                                        </div>

                                        <div class="contract-description mb-4">
                                            <p class="text-muted small mb-0">
                                                Mẫu hợp đồng chuẩn được soạn thảo theo quy định pháp luật hiện hành,
                                                đảm bảo tính pháp lý và bảo vệ quyền lợi các bên.
                                            </p>
                                        </div>

                                        <div class="contract-actions">
                                            <a href="{{ $template['download_url'] }}"
                                               class="btn btn-primary btn-sm me-1"
                                               download="{{ $template['name'] }}"
                                               data-bs-toggle="tooltip" title="Tải xuống">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-outline-secondary btn-sm me-1 btn-print"
                                                    data-template="{{ $template['name'] }}"
                                                    data-display-name="{{ $template['display_name'] }}"
                                                    data-file-url="{{ $template['download_url'] }}"
                                                    data-print-url="{{ $template['print_url'] }}"
                                                    data-file-name="{{ $template['display_name'] }}"
                                                    title="In hợp đồng">
                                                <i class="fas fa-print"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-success btn-sm me-1 btn-preview"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#previewModal"
                                                    data-file-url="{{ $template['download_url'] }}"
                                                    data-file-name="{{ $template['display_name'] }}"
                                                    data-preview-url="{{ $template['preview_url'] }}"
                                                    title="Xem trước">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-info btn-sm me-1 btn-edit"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editContractModal"
                                                    data-template="{{ $template['name'] }}"
                                                    data-display-name="{{ $template['display_name'] }}"
                                                    title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-danger btn-sm btn-delete"
                                                    data-template="{{ $template['name'] }}"
                                                    data-display-name="{{ $template['display_name'] }}"
                                                    title="Xóa"
                                                    onclick="confirmDelete('{{ $template['name'] }}', '{{ $template['display_name'] }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state text-center py-5">
                            <div class="empty-icon mb-4">
                                <i class="fas fa-file-contract fa-5x text-muted"></i>
                            </div>
                            <h4 class="empty-title">Chưa có mẫu hợp đồng</h4>
                            <p class="empty-text text-muted mb-4">
                                Hiện tại chưa có file hợp đồng .docx nào trong thư mục.<br>
                                Hãy thêm mẫu hợp đồng đầu tiên để bắt đầu sử dụng.
                            </p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContractModal">
                                <i class="fas fa-plus me-2"></i>
                                Thêm mẫu hợp đồng đầu tiên
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab 2: AI Contract Analysis -->
        <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1 text-white">
                                <i class="fas fa-brain me-2"></i>
                                Phân tích hợp đồng thông minh
                            </h5>
                            <p class="text-white-50 mb-0 small">
                                Sử dụng AI để phân tích hợp đồng và đánh giá rủi ro
                            </p>
                        </div>
                        <div class="header-actions">
                            <button class="btn btn-outline-light btn-sm" id="clearAnalysis" data-bs-toggle="tooltip" title="Xóa kết quả">
                                <i class="fas fa-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Contract Source Selection -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="source-selection">
                                <h6 class="mb-3">
                                    <i class="fas fa-file-alt me-2"></i>Chọn nguồn hợp đồng để phân tích
                                </h6>
                                <div class="source-tabs">
                                    <button class="source-tab active" data-source="template">
                                        <i class="fas fa-file-contract"></i>
                                        <span>Từ thư viện mẫu</span>
                                    </button>
                                    <button class="source-tab" data-source="upload">
                                        <i class="fas fa-upload"></i>
                                        <span>Tải lên file mới</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Selection -->
                    <div class="source-content" id="template-selection" style="display: block;">
                        <div class="template-selector">
                            <h6 class="mb-3">Chọn mẫu hợp đồng từ thư viện:</h6>
                            <div class="template-list">
                                @if(count($contractTemplates) > 0)
                                    <div class="row g-3">
                                        @foreach($contractTemplates as $template)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="template-card" data-template="{{ $template['name'] }}" data-display-name="{{ $template['display_name'] }}">
                                                <div class="template-icon">
                                                    <i class="fas fa-file-word text-primary"></i>
                                                </div>
                                                <div class="template-info">
                                                    <h6 class="template-name">{{ $template['display_name'] }}</h6>
                                                    <small class="text-muted">{{ number_format($template['size'] / 1024, 1) }} KB</small>
                                                </div>
                                                <div class="template-check">
                                                    <i class="fas fa-check-circle text-success" style="display: none;"></i>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-templates text-center py-4">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">Chưa có mẫu hợp đồng</h6>
                                        <p class="text-muted">Vui lòng thêm mẫu hợp đồng trước khi phân tích</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="source-content" id="file-upload-analysis" style="display: none;">
                        <div class="upload-zone-analysis" id="dropZoneAnalysis">
                            <div class="upload-zone-content">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h4 class="upload-title">Tải lên hợp đồng để phân tích</h4>
                                <p class="upload-subtitle">Kéo thả file vào đây hoặc <button type="button" class="btn-link" id="browseAnalysisFile">chọn file</button></p>
                                <div class="upload-info">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Hỗ trợ file .doc, .docx và .pdf, tối đa 10MB
                                    </small>
                                </div>
                                <input type="file" id="analysisFileInput" accept=".doc,.docx,.pdf" style="display: none;">
                            </div>
                        </div>

                        <div class="upload-preview" id="uploadPreview" style="display: none;">
                            <div class="selected-file">
                                <div class="file-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="file-info">
                                    <h6 class="file-name"></h6>
                                    <small class="file-size text-muted"></small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="removeFile">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Analysis Controls -->
                    <div class="analysis-controls mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Sẵn sàng phân tích</h6>
                                <small class="text-muted">AI sẽ phân tích hợp đồng và đưa ra đánh giá chi tiết</small>
                            </div>
                            <button class="btn btn-primary btn-lg" id="startAnalysis" disabled>
                                <i class="fas fa-brain me-2"></i>
                                Bắt đầu phân tích
                            </button>
                        </div>
                    </div>

                    <!-- Analysis Progress -->
                    <div class="analysis-progress" id="analysisProgress" style="display: none;">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Đang phân tích...</span>
                            </div>
                            <h5 class="text-primary">AI đang phân tích hợp đồng</h5>
                            <p class="text-muted mb-0">Vui lòng chờ trong giây lát...</p>
                            <div class="progress mt-3" style="height: 8px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted mt-2 d-block" id="progressText">Đang khởi tạo phân tích...</small>
                        </div>
                    </div>

                    <!-- Analysis Results -->
                    <div class="analysis-results" id="analysisResults" style="display: none;">
                        <div class="results-header mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-line me-2 text-success"></i>
                                    Kết quả phân tích hợp đồng
                                </h5>
                                <div class="results-actions">
                                    <button class="btn btn-outline-secondary btn-sm me-2" id="exportAnalysis">
                                        <i class="fas fa-download me-1"></i>Xuất báo cáo
                                    </button>
                                    <button class="btn btn-success btn-sm" id="newAnalysis">
                                        <i class="fas fa-plus me-1"></i>Phân tích mới
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Results content will be dynamically populated -->
                        <div id="analysisContent"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Danh sách giao dịch -->
        <div class="tab-pane fade" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
            <!-- Statistics Overview -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Tổng giao dịch
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ count($transactions) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exchange-alt fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Hoàn thành
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $transactions->where('TranStatus', 'Completed')->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Đang xử lý
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $transactions->where('TranStatus', 'Pending')->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Doanh thu thực tế
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        @php
                                            $totalRevenue = 0;
                                            foreach($transactions as $transaction) {
                                                $totalRevenue += $transaction->detailTransaction->where('DTran_Status', 'Hoàn Thành')->sum('Price');
                                            }
                                        @endphp
                                        {{ number_format($totalRevenue, 0, ',', '.') }}₫
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Transactions Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="fas fa-exchange-alt me-2 text-primary"></i>
                                Danh sách giao dịch
                            </h5>
                            <p class="text-muted mb-0 small">
                                Quản lý và theo dõi các giao dịch bất động sản
                            </p>
                        </div>
                        <div class="header-actions">
                            <div class="btn-group me-2" role="group">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Xuất Excel">
                                    <i class="fas fa-file-excel"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" title="Xuất PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="printTransactions" data-bs-toggle="tooltip" title="In danh sách">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="refreshTransactions" data-bs-toggle="tooltip" title="Làm mới">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter Section -->
                <div class="card-body border-bottom">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="search-wrapper">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text"
                                       class="form-control search-input"
                                       id="transactionSearch"
                                       placeholder="Tìm kiếm theo ID, tên khách hàng, môi giới...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Pending">Đang xử lý</option>
                                <option value="Paid">Đã thanh toán</option>
                                <option value="Cancelled">Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="typeFilter">
                                <option value="">Loại giao dịch</option>
                                <option value="Sale">Bán</option>
                                <option value="Rent">Cho thuê</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="dateFrom" placeholder="Từ ngày">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="dateTo" placeholder="Đến ngày">
                        </div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="transactionsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">ID Giao dịch</th>
                                    <th class="border-0">Bất động sản</th>
                                    <th class="border-0">Khách hàng</th>
                                    <th class="border-0">Môi giới</th>
                                    <th class="border-0">Loại</th>
                                    <th class="border-0">Giá trị</th>

                                    <th class="border-0">Ngày GD</th>
                                    <th class="border-0">Trạng thái</th>
                                    <th class="border-0 text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr class="transaction-row" data-transaction-id="{{ $transaction->TransactionID }}">
                                    <td>
                                        <div class="fw-bold text-primary">{{ $transaction->TransactionID }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="property-icon me-2">
                                                <i class="fas fa-home text-muted"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ optional($transaction->trans_property)->Title ?? 'N/A' }}</div>
                                                <small class="text-muted">ID: {{ $transaction->PropertyID }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="customer-avatar me-2">
                                                <i class="fas fa-user-circle fa-lg text-info"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ optional($transaction->trans_cus)->Name ?? 'N/A' }}</div>
                                                <small class="text-muted">ID: {{ $transaction->CusID }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="agent-avatar me-2">
                                                <i class="fas fa-user-tie fa-lg text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ optional($transaction->trans_agent)->Name ?? 'N/A' }}</div>
                                                <small class="text-muted">ID: {{ $transaction->AgentID }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($transaction->TransactionType == 'Sale')
                                            <span class="badge bg-primary-subtle text-primary">
                                                <i class="fas fa-shopping-cart me-1"></i>Bán
                                            </span>
                                        @elseif($transaction->TransactionType == 'Rent')
                                            <span class="badge bg-info-subtle text-info">
                                                <i class="fas fa-calendar-alt me-1"></i>Cho thuê
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">
                                            {{ number_format($transaction->TotalPrice, 0, ',', '.') }}₫
                                        </div>
                                    </td>
                                    {{-- <td>
                                        @php
                                            $totalRevenue = $transaction->detailTransaction->where('DTran_Status', 'Hoàn Thành')->sum('Price');
                                            $remainingAmount = $transaction->TotalPrice - $totalRevenue;
                                        @endphp
                                        <div class="fw-bold {{ $totalRevenue > 0 ? 'text-success' : 'text-muted' }}">
                                            {{ number_format($totalRevenue, 0, ',', '.') }}₫
                                        </div>
                                        @if($remainingAmount > 0)
                                            <small class="text-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Còn {{ number_format($remainingAmount, 0, ',', '.') }}₫
                                            </small>
                                        @endif
                                    </td> --}}
                                    <td>
                                        <div>{{ date('d/m/Y', strtotime($transaction->TransactionDate)) }}</div>
                                        <small class="text-muted">{{ date('H:i', strtotime($transaction->TransactionDate)) }}</small>
                                    </td>
                                    <td>
                                        @switch($transaction->TranStatus)
                                            @case('Pending')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Đang xử lý
                                                </span>
                                                @break
                                            @case('Completed')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Hoàn thành
                                                </span>
                                                @break
                                            @case('Paid')
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-money-check-alt me-1"></i>Đã thanh toán
                                                </span>
                                                @break
                                            @case('Cancelled')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Đã hủy
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $transaction->TranStatus }}</span>
                                        @endswitch
                                    </td>
                                    <td onclick="event.stopPropagation();">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#transactionDetailsModal"
                                                    data-transaction-id="{{ $transaction->TransactionID }}"
                                                    title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Expandable Details Row -->
                                <tr class="transaction-details-row" id="details-{{ $transaction->TransactionID }}" style="display: none;">
                                    <td colspan="10">
                                        <div class="transaction-details-container p-4 bg-light border-start border-5 border-primary">
                                            <div class="row">
                                                <!-- Payment History Section -->
                                                <div class="col-md-6 mb-4">
                                                    <h6 class="fw-bold text-primary mb-3">
                                                        <i class="fas fa-credit-card me-2"></i>Lịch sử thanh toán
                                                    </h6>
                                                    <div class="payment-history" id="payment-history-{{ $transaction->TransactionID }}">
                                                        @if($transaction->detailTransaction && count($transaction->detailTransaction) > 0)
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-borderless">
                                                                    <thead>
                                                                        <tr class="text-muted small">
                                                                            <th>Lần TT</th>
                                                                            <th>Số tiền</th>
                                                                            <th>Ngày TT</th>
                                                                            <th>Phương thức</th>
                                                                            <th>Trạng thái</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($transaction->detailTransaction as $payment)
                                                                        <tr>
                                                                            <td><span class="badge bg-info">#{{ $payment->Num_Pay }}</span></td>
                                                                            <td class="fw-bold text-success">{{ number_format($payment->Price, 0, ',', '.') }}₫</td>
                                                                            <td>{{ date('d/m/Y', strtotime($payment->DTran_Date)) }}</td>
                                                                            <td>
                                                                                <span class="badge bg-secondary-subtle text-secondary">
                                                                                    {{ $payment->PaymentType ?? 'N/A' }}
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                @if($payment->DTran_Status == 'Hoàn Thành')
                                                                                    <span class="badge bg-success">Hoàn thành</span>
                                                                                @elseif($payment->DTran_Status == 'Chờ đợi')
                                                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                                                @elseif($payment->DTran_Status == 'Hủy')
                                                                                    <span class="badge bg-danger">Đã hủy</span>
                                                                                @else
                                                                                    <span class="badge bg-secondary">{{ $payment->DTran_Status }}</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <div class="text-center text-muted py-3">
                                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                                <p class="mb-0">Chưa có lịch sử thanh toán</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Documents Section -->
                                                <div class="col-md-6 mb-4">
                                                    <h6 class="fw-bold text-success mb-3">
                                                        <i class="fas fa-file-signature me-2"></i>Tài liệu & Hợp đồng
                                                    </h6>
                                                    <div class="documents-section">
                                                        <!-- Contracts -->


                                                        <!-- Documents -->
                                                        @if($transaction->document && count($transaction->document) > 0)
                                                            <div class="mb-3">
                                                                <h6 class="text-warning small mb-2">Tài liệu đính kèm:</h6>
                                                                @foreach($transaction->document as $doc)
                                                                <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fas fa-file-alt text-info me-2"></i>
                                                                        <div>
                                                                            <div class="fw-semibold small">{{ $doc->DocumentType ?? 'Tài liệu' }}</div>
                                                                            <small class="text-muted">
                                                                                Upload: {{ date('d/m/Y', strtotime($doc->UploadedDate)) }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                    <button class="btn btn-outline-primary btn-sm" onclick="viewDocument('{{ $doc->FilePath }}')">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        @endif


                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Commission Section (only for Paid transactions) -->
                                            @if($transaction->TranStatus == 'Paid')
                                            <div class="row mt-3 pt-3 border-top">
                                                <div class="col-12">
                                                    <h6 class="fw-bold text-warning mb-3">
                                                        <i class="fas fa-percentage me-2"></i>Thông tin hoa hồng
                                                    </h6>
                                                    <div class="commission-info" id="commission-info-{{ $transaction->TransactionID }}">
                                                        @if($transaction->trans_commission && count($transaction->trans_commission) > 0)
                                                            <div class="row">
                                                                @foreach($transaction->trans_commission as $commission)
                                                                <div class="col-md-6 mb-3">
                                                                    <div class="card border-warning h-100">
                                                                        <div class="card-body p-3">
                                                                            <div class="d-flex justify-content-between align-items-start">
                                                                                <div>
                                                                                    <h6 class="card-title text-warning mb-1">
                                                                                        <i class="fas fa-user-tie me-1"></i>
                                                                                        {{ optional($commission->comm_agent)->Name ?? 'N/A' }}
                                                                                    </h6>
                                                                                    <div class="commission-details">
                                                                                        <div class="d-flex justify-content-between mb-2">
                                                                                            <span class="text-muted small">Tỷ lệ:</span>
                                                                                            <span class="fw-bold">{{ $commission->CommissionRate ?? 0 }}%</span>
                                                                                        </div>
                                                                                        <div class="d-flex justify-content-between mb-2">
                                                                                            <span class="text-muted small">Số tiền:</span>
                                                                                            <span class="fw-bold text-success">
                                                                                                {{ number_format($commission->CommissionAmount ?? 0, 0, ',', '.') }}₫
                                                                                            </span>
                                                                                        </div>
                                                                                        <div class="d-flex justify-content-between">
                                                                                            <span class="text-muted small">Trạng thái:</span>
                                                                                            @if($commission->Status == 'Paid')
                                                                                                <span class="badge bg-success">Đã chi</span>
                                                                                            @else
                                                                                                <span class="badge bg-warning">Chưa chi</span>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @if($commission->Status != 'Paid')
                                                                                <button class="btn btn-outline-warning btn-sm"
                                                                                        onclick="showCommissionPayment({{ $transaction->TransactionID }}, {{ $commission->CommissionID }})">
                                                                                    <i class="fas fa-money-bill-wave"></i>
                                                                                </button>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="text-center text-muted py-3">
                                                                <i class="fas fa-calculator fa-2x mb-2"></i>
                                                                <p class="mb-0">Chưa tính hoa hồng cho giao dịch này</p>
                                                                <button class="btn btn-outline-warning btn-sm mt-2" onclick="calculateCommission({{ $transaction->TransactionID }})">
                                                                    <i class="fas fa-plus me-1"></i>Tính hoa hồng
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-icon mb-3">
                                                <i class="fas fa-exchange-alt"></i>
                                            </div>
                                            <h5 class="empty-title">Chưa có giao dịch</h5>
                                            <p class="empty-text text-muted">
                                                Hiện tại chưa có giao dịch nào được tạo trong hệ thống.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Table Footer with Pagination and Actions -->
                @if(count($transactions) > 0)
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="text-muted small me-3">
                                Hiển thị {{ count($transactions) }} giao dịch
                            </span>
                            <div class="bulk-actions" style="display: none;">
                                <button class="btn btn-outline-primary btn-sm me-2" id="bulkEdit">
                                    <i class="fas fa-edit me-1"></i>Sửa hàng loạt
                                </button>
                                <button class="btn btn-outline-danger btn-sm" id="bulkDelete">
                                    <i class="fas fa-trash me-1"></i>Xóa đã chọn
                                </button>
                            </div>
                        </div>
                        <div class="pagination-wrapper">
                            <!-- Pagination sẽ được thêm ở đây nếu cần -->
                            <nav>
                                <ul class="pagination pagination-sm mb-0">
                                    <li class="page-item disabled">
                                        <span class="page-link">Trước</span>
                                    </li>
                                    <li class="page-item active">
                                        <span class="page-link">1</span>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">2</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">3</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">Sau</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Transaction Details Modal with Tabs -->
            <div class="modal fade" id="transactionDetailsModal" tabindex="-1" aria-labelledby="transactionDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="transactionDetailsModalLabel">
                                <i class="fas fa-info-circle me-2"></i>Chi tiết giao dịch <span class="badge bg-warning text-dark ms-1 me-1">ID: <strong id="modalTransactionId" style="font-size: 1.1em;"></strong></span>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <script>
                        // Enhanced transaction modal with AJAX loading
                        document.addEventListener('DOMContentLoaded', function() {
                            const modal = document.getElementById('transactionDetailsModal');
                            if (modal) {
                                modal.addEventListener('show.bs.modal', function(event) {
                                    const button = event.relatedTarget;
                                    if (button) {
                                        const transactionId = button.getAttribute('data-transaction-id');
                                        if (transactionId) {
                                            // Update the modal header
                                            const modalIdSpan = document.getElementById('modalTransactionId');
                                            if (modalIdSpan) {
                                                modalIdSpan.textContent = transactionId;
                                            }

                                            // Load transaction details via AJAX
                                            loadTransactionDetails(transactionId);
                                        }
                                    }
                                });
                            }
                        });

                        // Function to load transaction details via AJAX
                        function loadTransactionDetails(transactionId) {
                            // Show loading spinners
                            showLoadingState();

                            // Make AJAX request to load transaction details
                            fetch(`/admin/transaction/${transactionId}/details`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        populateTransactionInfo(data.transaction);
                                        populatePaymentHistory(data.payment_history);
                                        populateDocuments(data.documents, data.contracts);
                                    } else {
                                        showErrorState(data.error || 'Lỗi khi tải thông tin giao dịch');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading transaction details:', error);
                                    showErrorState('Không thể tải thông tin giao dịch');
                                });
                        }

                        // Show loading state in all tabs
                        function showLoadingState() {
                            const loadingHtml = `
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                    <p class="text-muted mt-2">Đang tải thông tin...</p>
                                </div>
                            `;

                            document.getElementById('transactionInfoContent').innerHTML = loadingHtml;
                            document.getElementById('paymentHistoryContent').innerHTML = loadingHtml;
                            document.getElementById('documentsContent').innerHTML = loadingHtml;
                        }

                        // Show error state in all tabs
                        function showErrorState(message) {
                            const errorHtml = `
                                <div class="text-center py-4">
                                    <div class="text-danger mb-3">
                                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                                    </div>
                                    <h6 class="text-danger">Lỗi tải dữ liệu</h6>
                                    <p class="text-muted">${message}</p>
                                </div>
                            `;

                            document.getElementById('transactionInfoContent').innerHTML = errorHtml;
                            document.getElementById('paymentHistoryContent').innerHTML = errorHtml;
                            document.getElementById('documentsContent').innerHTML = errorHtml;
                        }

                        // Populate transaction info tab
                        function populateTransactionInfo(transaction) {
                            const content = document.getElementById('transactionInfoContent');
                            const html = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td width="40%" class="text-muted">Mã giao dịch:</td>
                                                        <td class="fw-bold">${transaction.TransactionID}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Loại giao dịch:</td>
                                                        <td>
                                                            <span class="badge ${transaction.TransactionType === 'Sale' ? 'bg-primary' : 'bg-info'}">
                                                                ${transaction.TransactionType === 'Sale' ? 'Bán' : 'Cho thuê'}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Tổng giá trị:</td>
                                                        <td class="fw-bold text-success">${formatCurrency(transaction.TotalPrice)}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Ngày giao dịch:</td>
                                                        <td>${formatDate(transaction.TransactionDate)}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Trạng thái:</td>
                                                        <td>
                                                            <span class="badge ${getStatusBadgeClass(transaction.TranStatus)}">
                                                                ${transaction.TranStatus}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-users me-2"></i>Thông tin liên quan
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td width="40%" class="text-muted">Bất động sản:</td>
                                                        <td class="fw-bold">${transaction.trans_property ? transaction.trans_property.Title : 'N/A'}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Khách hàng:</td>
                                                        <td>${transaction.trans_cus ? transaction.trans_cus.Name : 'N/A'}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Nhân viên:</td>
                                                        <td>${transaction.trans_agent ? transaction.trans_agent.Name : 'N/A'}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            content.innerHTML = html;
                        }

                        // Populate payment history tab
                        function populatePaymentHistory(paymentHistory) {
                            const content = document.getElementById('paymentHistoryContent');

                            if (!paymentHistory || paymentHistory.length === 0) {
                                content.innerHTML = `
                                    <div class="text-center py-4">
                                        <div class="text-muted mb-3">
                                            <i class="fas fa-credit-card fa-3x"></i>
                                        </div>
                                        <h6 class="text-muted">Chưa có lịch sử thanh toán</h6>
                                        <p class="text-muted">Giao dịch này chưa có khoản thanh toán nào.</p>
                                    </div>
                                `;
                                return;
                            }

                            let html = `
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Lần TT</th>
                                                <th>Số tiền</th>
                                                <th>Ngày thanh toán</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;

                            paymentHistory.forEach(payment => {
                                html += `
                                    <tr>
                                        <td>${payment.Num_Pay}</td>
                                        <td class="fw-bold text-success">${formatCurrency(payment.Price)}</td>
                                        <td>${payment.DTran_Date ? formatDate(payment.DTran_Date) : 'N/A'}</td>
                                        <td>
                                            <span class="badge ${payment.DTran_Status === 'Hoàn Thành' ? 'bg-success' : 'bg-warning'}">
                                                ${payment.DTran_Status}
                                            </span>
                                        </td>
                                    </tr>
                                `;
                            });

                            html += `
                                        </tbody>
                                    </table>
                                </div>
                            `;

                            content.innerHTML = html;
                        }

                        // Populate documents tab
                        function populateDocuments(documents) {
                            const content = document.getElementById('documentsContent');

                            if (!documents || documents.length === 0) {
                                content.innerHTML = `
                                    <div class="text-center py-4">
                                        <div class="text-muted mb-3">
                                            <i class="fas fa-folder-open fa-3x"></i>
                                        </div>
                                        <h6 class="text-muted">Chưa có tài liệu</h6>
                                        <p class="text-muted">Giao dịch này chưa có tài liệu nào được tải lên.</p>
                                    </div>
                                `;
                                return;
                            }

                            let html = '<div class="row">';

                            documents.forEach(doc => {
                                html += `
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="fas fa-file-alt fa-2x text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">${doc.DocumentName}</h6>
                                                        <small class="text-muted">${doc.DocumentType}</small>
                                                        <div class="mt-2">
                                                            <button class="btn btn-sm btn-outline-primary me-2" onclick="viewDocument('${doc.FilePath}')">
                                                                <i class="fas fa-eye me-1"></i>Xem
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-success" onclick="downloadDocument('${doc.FilePath}')">
                                                                <i class="fas fa-download me-1"></i>Tải xuống
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });

                            html += '</div>';
                            content.innerHTML = html;
                        }

                        // Toggle payment button visibility
                        function togglePaymentButton(status) {
                            const paymentBtn = document.getElementById('processPaymentBtn');
                            if (paymentBtn) {
                                if (status === 'Paid') {
                                    paymentBtn.style.display = 'none';
                                } else {
                                    paymentBtn.style.display = 'inline-block';
                                }
                            }
                        }

                        // Helper functions
                        function formatCurrency(amount) {
                            if (!amount) return 'N/A';
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(amount);
                        }

                        function formatDate(dateString) {
                            if (!dateString) return 'N/A';
                            const date = new Date(dateString);
                            return date.toLocaleDateString('vi-VN');
                        }

                        function getStatusBadgeClass(status) {
                            switch (status) {
                                case 'Paid': return 'bg-success';
                                case 'Pending': return 'bg-warning';
                                case 'Cancelled': return 'bg-danger';
                                default: return 'bg-secondary';
                            }
                        }

                        // Contract Template Event Handlers
                        document.addEventListener('DOMContentLoaded', function() {
                            console.log('🚀 Contract Template Handlers Loaded');

                            // Handle Preview buttons
                            document.addEventListener('click', function(e) {
                                if (e.target.closest('.btn-preview')) {
                                    e.preventDefault();
                                    const button = e.target.closest('.btn-preview');
                                    handleContractPreview(button);
                                }

                                if (e.target.closest('.btn-print')) {
                                    e.preventDefault();
                                    const button = e.target.closest('.btn-print');
                                    handleContractPrint(button);
                                }
                            });

                            // Handle modal preview buttons
                            document.getElementById('downloadFromPreview')?.addEventListener('click', function() {
                                const currentFileUrl = this.getAttribute('data-current-file-url');
                                const currentFileName = this.getAttribute('data-current-file-name');
                                if (currentFileUrl && currentFileName) {
                                    const link = document.createElement('a');
                                    link.href = currentFileUrl;
                                    link.download = currentFileName;
                                    link.click();
                                }
                            });

                            document.getElementById('printFromPreview')?.addEventListener('click', function() {
                                const currentPrintUrl = this.getAttribute('data-current-print-url');
                                if (currentPrintUrl) {
                                    window.open(currentPrintUrl, '_blank');
                                } else {
                                    // Fallback to print current modal content
                                    window.print();
                                }
                            });

                            // Handle zoom in/out functionality
                            let currentZoom = 100; // Default zoom level
                            const zoomStep = 20; // Zoom step in percentage

                            document.getElementById('zoomIn')?.addEventListener('click', function() {
                                currentZoom += zoomStep;
                                updateZoom();
                            });

                            document.getElementById('zoomOut')?.addEventListener('click', function() {
                                if (currentZoom > zoomStep) {
                                    currentZoom -= zoomStep;
                                    updateZoom();
                                }
                            });

                            // Function to apply zoom level to preview content
                            function updateZoom() {
                                const zoomLabel = document.querySelector('.zoom-level');
                                const previewDocument = document.querySelector('.preview-document');

                                if (zoomLabel) {
                                    zoomLabel.textContent = `${currentZoom}%`;
                                }

                                if (previewDocument) {
                                    previewDocument.style.transform = `scale(${currentZoom/100})`;
                                    previewDocument.style.transformOrigin = 'top left';
                                }
                            }
                        });

                        // Handle contract preview
                        function handleContractPreview(button) {
                            const previewUrl = button.getAttribute('data-preview-url');
                            const fileName = button.getAttribute('data-file-name');
                            const fileUrl = button.getAttribute('data-file-url');

                            console.log('Preview clicked:', { previewUrl, fileName, fileUrl });

                            // Update modal title
                            const modalTitle = document.querySelector('#previewModal .preview-file-name');
                            if (modalTitle) {
                                modalTitle.textContent = fileName || 'Hợp đồng';
                            }

                            // Store file info for download/print buttons
                            const downloadBtn = document.getElementById('downloadFromPreview');
                            const printBtn = document.getElementById('printFromPreview');

                            if (downloadBtn) {
                                downloadBtn.setAttribute('data-current-file-url', fileUrl);
                                downloadBtn.setAttribute('data-current-file-name', fileName);
                            }

                            if (printBtn) {
                                printBtn.setAttribute('data-current-print-url', button.getAttribute('data-print-url'));
                            }

                            // Load preview content
                            loadContractPreview(previewUrl, fileName);
                        }

                        // Handle contract print
                        function handleContractPrint(button) {
                            const printUrl = button.getAttribute('data-print-url');
                            const fileName = button.getAttribute('data-file-name');

                            console.log('Print clicked:', { printUrl, fileName });

                            if (printUrl) {
                                // Lấy nội dung từ print endpoint và hiển thị trong cửa sổ in
                                fetch(printUrl)
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        if (data && data.print_html) {
                                            // Tạo cửa sổ mới để in
                                            const printWindow = window.open('', '_blank');
                                            printWindow.document.write(data.print_html);
                                            printWindow.document.close();
                                            // Tự động mở hộp thoại in sau khi tải xong trang
                                            printWindow.onload = function() {
                                                printWindow.print();
                                            };
                                        } else {
                                            throw new Error('Không tìm thấy nội dung để in');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Print error:', error);
                                        alert('Không thể in tài liệu: ' + error.message);

                                        // Fallback: tải xuống file nếu không in được
                                        const fileUrl = button.getAttribute('data-file-url');
                                        if (fileUrl) {
                                            window.open(fileUrl, '_blank');
                                        }
                                    });
                            } else {
                                // Fallback: download and let user print manually
                                const fileUrl = button.getAttribute('data-file-url');
                                if (fileUrl) {
                                    window.open(fileUrl, '_blank');
                                }
                            }
                        }

                        // Load contract preview content
                        function loadContractPreview(previewUrl, fileName) {
                            const previewContent = document.getElementById('previewContent');

                            if (!previewContent) {
                                console.error('Preview content container not found');
                                return;
                            }

                            // Show loading state
                            previewContent.innerHTML = `
                                <div class="preview-loading text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Đang tải nội dung xem trước...</p>
                                </div>
                            `;

                            if (previewUrl) {
                                // Load content via AJAX
                                fetch(previewUrl)
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                        }
                                        return response.json(); // Chuyển từ text sang json vì endpoint trả về JSON
                                    })
                                    .then(data => {
                                        // Kiểm tra nếu có dữ liệu preview_html trong response
                                        if (data && data.preview_html) {
                                            // Hiển thị HTML từ response
                                            previewContent.innerHTML = `
                                                <div class="preview-document p-4">
                                                    ${data.preview_html}
                                                </div>
                                            `;
                                        } else {
                                            throw new Error('Không tìm thấy nội dung xem trước');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Preview loading error:', error);
                                        previewContent.innerHTML = `
                                            <div class="text-center py-5">
                                                <div class="text-warning mb-3">
                                                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                                                </div>
                                                <h6 class="text-warning">Không thể tải xem trước</h6>
                                                <p class="text-muted mb-3">Lỗi: ${error.message}</p>
                                                <p class="text-muted">Vui lòng tải xuống file để xem nội dung</p>
                                            </div>
                                        `;
                                    });
                            } else {
                                // Show message when no preview URL available
                                previewContent.innerHTML = `
                                    <div class="text-center py-5">
                                        <div class="text-info mb-3">
                                            <i class="fas fa-info-circle fa-3x"></i>
                                        </div>
                                        <h6 class="text-info">Xem trước không khả dụng</h6>
                                        <p class="text-muted">Vui lòng tải xuống file để xem nội dung</p>
                                    </div>
                                `;
                            }
                        }
                        </script>
                        <div class="modal-body p-0">
                            <!-- Modal Navigation Tabs -->
                            <ul class="nav nav-tabs nav-justified" id="transactionModalTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="transaction-info-tab" data-bs-toggle="tab"
                                            data-bs-target="#transaction-info" type="button" role="tab"
                                            aria-controls="transaction-info" aria-selected="true">
                                        <i class="fas fa-file-contract me-2"></i>Thông tin & Hợp đồng
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="payment-history-tab" data-bs-toggle="tab"
                                            data-bs-target="#payment-history" type="button" role="tab"
                                            aria-controls="payment-history" aria-selected="false">
                                        <i class="fas fa-credit-card me-2"></i>Lịch sử thanh toán
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab"
                                            data-bs-target="#documents" type="button" role="tab"
                                            aria-controls="documents" aria-selected="false">
                                        <i class="fas fa-folder-open me-2"></i>Tài liệu liên quan
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="transactionModalTabContent">
                                <!-- Tab 1: Transaction Info & Contract -->
                                <div class="tab-pane fade show active" id="transaction-info" role="tabpanel" aria-labelledby="transaction-info-tab">
                                    <div class="p-4" id="transactionInfoContent">
                                        <div class="text-center py-3">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Đang tải...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 2: Payment History -->
                                <div class="tab-pane fade" id="payment-history" role="tabpanel" aria-labelledby="payment-history-tab">
                                    <div class="p-4" id="paymentHistoryContent">
                                        <div class="text-center py-3">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Đang tải...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 3: Related Documents -->
                                <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                                    <div class="p-4" id="documentsContent">
                                        <div class="text-center py-3">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Đang tải...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Đóng
                            </button>
                            <button type="button" class="btn btn-success" id="processPaymentBtn" style="display: none;">
                                <i class="fas fa-credit-card me-2"></i>Xử lý thanh toán
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Transaction Modal -->
            <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="addTransactionModalLabel">
                                <i class="fas fa-plus me-2"></i>Thêm giao dịch mới
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="addTransactionForm">
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="propertySelect" class="form-label">Bất động sản</label>
                                        <select class="form-select" id="propertySelect" name="PropertyID" required>
                                            <option value="">Chọn bất động sản</option>
                                            <!-- Options will be populated dynamically -->
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="customerSelect" class="form-label">Khách hàng</label>
                                        <select class="form-select" id="customerSelect" name="CusID" required>
                                            <option value="">Chọn khách hàng</option>
                                            <!-- Options will be populated dynamically -->
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="agentSelect" class="form-label">Môi giới</label>
                                        <select class="form-select" id="agentSelect" name="AgentID" required>
                                            <option value="">Chọn môi giới</option>
                                            <!-- Options will be populated dynamically -->
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="transactionType" class="form-label">Loại giao dịch</label>
                                        <select class="form-select" id="transactionType" name="TransactionType" required>
                                            <option value="">Chọn loại</option>
                                            <option value="Sale">Bán</option>
                                            <option value="Rent">Cho thuê</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="totalPrice" class="form-label">Tổng giá trị</label>
                                        <input type="number" class="form-control" id="totalPrice" name="TotalPrice" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="transactionDate" class="form-label">Ngày giao dịch</label>
                                        <input type="date" class="form-control" id="transactionDate" name="TransactionDate" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>Thêm giao dịch
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>Xem trước hợp đồng
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="preview-container">
                    <div class="preview-toolbar d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
                        <div class="preview-info">
                            <h6 class="mb-0 preview-file-name">Tên file</h6>
                            <small class="text-muted">Microsoft Word Document</small>
                        </div>
                        <div class="preview-actions">
                            <button type="button" class="btn btn-outline-primary btn-sm me-2" id="zoomOut">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <span class="zoom-level mx-2">100%</span>
                            <button type="button" class="btn btn-outline-primary btn-sm me-3" id="zoomIn">
                                <i class="fas fa-search-plus"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-sm me-2" id="downloadFromPreview">
                                <i class="fas fa-download me-1"></i>Tải xuống
                            </button>
                            <button type="button" class="btn btn-info btn-sm" id="printFromPreview">
                                <i class="fas fa-print me-1"></i>In
                            </button>
                        </div>
                    </div>
                    <div class="preview-content" id="previewContent">
                        <div class="preview-loading text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <p class="mt-3 text-muted">Đang tải nội dung xem trước...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Simple Tab Navigation */
.nav-tabs {
    border-bottom: 2px solid #e3e6f0;
    margin-bottom: 1.5rem;
}

.nav-tabs .nav-link {
    border: none;
    border-radius: 8px 8px 0 0;
    padding: 0.75rem 1.25rem;
    margin-right: 0.25rem;
    color: #6c757d;
    background: transparent;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-tabs .nav-link:hover {
    border-color: transparent;
    background: rgba(138, 43, 226, 0.1);
    color: #8a2be2;
}

.nav-tabs .nav-link.active {
    color: white;
    background: linear-gradient(135deg, #8a2be2 0%, #9370db 100%);
    border-color: #8a2be2 #8a2be2 transparent;
    border-bottom-color: transparent;
    font-weight: 600;
    position: relative;
    box-shadow: 0 4px 12px rgba(138, 43, 226, 0.3);
}

.nav-tabs .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(135deg, #8a2be2 0%, #9370db 100%);
    border-radius: 2px 2px 0 0;
}

/* Badge styling for active tabs */
.nav-tabs .nav-link.active .badge {
    background: rgba(255, 255, 255, 0.9) !important;
    color: #8a2be2 !important;
    font-weight: 600;
}

/* Badge Styles for Tabs */
.nav-tabs .nav-link .badge {
    font-size: 0.7rem;
    padding: 0.3rem 0.5rem;
    border-radius: 12px;
    font-weight: 600;
}

/* Upload Styles */
.bg-gradient-success {
    background: linear-gradient(135deg, #1cc88a 0%, #17a2b8 100%);
}

.upload-methods {
    margin-bottom: 2rem;
}

.method-tabs {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.method-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: #f8f9fc;
    border: 2px solid #e3e6f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 150px;
}

.method-tab i {
    font-size: 2rem;
    color: #6c757d;
    transition: all 0.3s ease;
}

.method-tab span {
    font-weight: 500;
    color: #5a5c69;
    transition: all 0.3s ease;
}

.method-tab:hover {
    border-color: #4e73df;
    background: rgba(78, 115, 223, 0.05);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(78, 115, 223, 0.2);
}

.method-tab:hover i,
.method-tab:hover span {
    color: #4e73df;
}

.method-tab.active {
    background: linear-gradient(135deg, #4e73df 0%, #5a67d8 100%);
    border-color: #4e73df;
    color: white;
    box-shadow: 0 8px 25px rgba(78, 115, 223, 0.3);
}

.method-tab.active i,
.method-tab.active span {
    color: white;
}

/* Upload Zone */
.upload-zone {
    border: 3px dashed #4e73df;
    border-radius: 15px;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, rgba(78, 115, 223, 0.05) 0%, rgba(90, 103, 216, 0.05) 100%);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.upload-zone:hover,
.upload-zone.dragover {
    border-color: #1cc88a;
    background: linear-gradient(135deg, rgba(28, 200, 138, 0.1) 0%, rgba(23, 162, 184, 0.1) 100%);
    transform: scale(1.02);
}

.upload-zone-content {
    text-align: center;
    position: relative;
    z-index: 2;
}

.upload-icon {
    font-size: 4rem;
    color: #4e73df;
    margin-bottom: 1rem;
    animation: float 3s ease-in-out infinite;
}

.upload-zone:hover .upload-icon {
    color: #1cc88a;
    transform: scale(1.1);
}

.upload-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.upload-subtitle {
    color: #6c757d;
    margin-bottom: 1rem;
}

.upload-subtitle .btn-link {
    color: #4e73df;
    text-decoration: none;
    font-weight: 600;
    padding: 0;
    border: none;
    background: none;
}

.upload-subtitle .btn-link:hover {
    color: #1cc88a;
    text-decoration: underline;
}

.upload-info {
    padding: 0.75rem 1.5rem;
    background: rgba(78, 115, 223, 0.1);
    border-radius: 25px;
    display: inline-block;
    margin-top: 1rem;
}

/* Batch Upload */
.batch-upload-zone {
    background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 2rem;
    border: 2px solid #e3e6f0;
}

.batch-drop-zone {
    border: 2px dashed #6c757d;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    background: white;
    transition: all 0.3s ease;
    cursor: pointer;
}

.batch-drop-zone:hover,
.batch-drop-zone.dragover {
    border-color: #4e73df;
    background: rgba(78, 115, 223, 0.05);
}

.batch-drop-content i {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
    display: block;
}

.batch-drop-zone:hover .batch-drop-content i {
    color: #4e73df;
}

/* Upload Progress */
.upload-progress,
.upload-results {
    background: #f8f9fc;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e3e6f0;
}

.upload-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    background: white;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.upload-item-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #4e73df   0%, #5a67d8 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 1rem;
}

.upload-item-content {
    flex: 1;
}

.upload-item-name {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.upload-item-size {
    font-size: 0.8rem;
    color: #6c757d;
}

.upload-item-progress {
    width: 100px;
    height: 6px;
    background: #e3e6f0;
    border-radius: 3px;
    overflow: hidden;
    margin-left: 1rem;
}

.upload-item-progress-bar {
    height: 100%;
    background: linear-gradient(135deg, #1cc88a 0%, #17a2b8 100%);
    transition: width 0.3s ease;
}

.upload-item-status {
    margin-left: 1rem;
    font-size: 0.9rem;
}

.upload-item-status.success {
    color: #1cc88a;
}

.upload-item-status.error {
    color: #e74a3b;
}

/* URL Upload */
.url-upload-form {
    max-width: 600px;
    margin: 0 auto;
}

.url-preview {
    margin-top: 1rem;
}

/* Animations */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.uploading {
    animation: pulse 2s ease-in-out infinite;
}

/* Responsive */
@media (max-width: 768px) {
    .nav-tabs {
        border-bottom: 1px solid #e3e6f0;
        margin-bottom: 1rem;
    }

    .nav-tabs .nav-link {
        padding: 0.6rem 0.8rem;
        margin-right: 0.1rem;
        font-size: 0.9rem;
    }

    .nav-tabs .nav-link i {
        font-size: 0.9rem;
    }

    .nav-tabs .nav-link .badge {
        font-size: 0.6rem;
        padding: 0.2rem 0.4rem;
    }

    .method-tabs {
        gap: 0.5rem;
    }

    .method-tab {
        min-width: auto;
        padding: 0.75rem 1rem;
    }

    .upload-zone {
        padding: 2rem 1rem;
    }

    .upload-icon {
        font-size: 3rem;
    }

    .upload-title {
        font-size: 1.2rem;
    }
}

/* Enhanced Contract Cards Design (existing styles preserved) */
.contract-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 16px;
    overflow: hidden;
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fc 100%);
    border: 1px solid rgba(70, 115, 223, 0.1);
    position: relative;
}

.contract-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(70, 115, 223, 0.15);
    border-color: rgba(70, 115, 223, 0.3);
}

.contract-header {
    position: relative;
    background: linear-gradient(135deg, #4e73df 0%, #6610f2 100%);
    padding: 1.5rem;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.contract-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    backdrop-filter: blur(10px);
}

.contract-status .badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.contract-title {
    color: #2d3748;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 0.75rem;
}

.contract-meta {
    padding: 1rem;
    background: rgba(70, 115, 223, 0.05);
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
}

.meta-item:last-child {
    margin-bottom: 0;
}

.contract-description {
    background: #f8f9fc;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #4e73df;
}

.contract-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-action {
    flex: 1;
    min-width: 120px;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    font-size: 0.85rem;
}

.btn-action::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-action:hover::before {
    left: 100%;
}

.btn-primary.btn-action {
    background: linear-gradient(135deg, #4e73df 0%, #5a67d8 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
}

.btn-primary.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(78, 115, 223, 0.4);
}

.btn-outline-primary.btn-action {
    border: 2px solid #4e73df;
    color: #4e73df;
    background: transparent;
}

.btn-outline-primary.btn-action:hover {
    background: #4e73df;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(78, 115, 223, 0.3);
}

.btn-outline-success.btn-action {
    border: 2px solid #1cc88a;
    color: #1cc88a;
    background: transparent;
}

.btn-outline-success.btn-action:hover {
    background: #1cc88a;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(28, 200, 138, 0.3);
}

.btn-outline-info.btn-action {
    border: 2px solid #36b9cc;
    color: #36b9cc;
    background: transparent;
}

.btn-outline-info.btn-action:hover {
    background: #36b9cc;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(54, 185, 204, 0.3);
}

/* Preview Modal Styles */
.modal-xl {
    max-width: 95%;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #4e73df 0%, #5a67d8 100%);
}

.preview-container {
    height: 80vh;
    display: flex;
    flex-direction: column;
}

.preview-toolbar {
    flex-shrink: 0;
    background: #f8f9fc !important;
    border-bottom: 2px solid #e3e6f0 !important;
}

.preview-content {
    flex: 1;
    overflow: auto;
    background: #ffffff;
    position: relative;
}

.preview-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
}

.preview-iframe {
    width: 100%;
    height: 100%;
    border: none;
    background: white;
}

.zoom-level {
    font-weight: 600;
    color: #5a6c7d;
    min-width: 50px;
    text-align: center;
}

.preview-file-name {
    color: #2d3748;
    font-weight: 600;
}

.preview-document {
    padding: 2rem;
    background: white;
    min-width: 80%;
    margin: 0 auto;
    transform-origin: top left;
    transition: transform 0.3s ease;
    overflow-x: auto;
}

.preview-document h1,
.preview-document h2,
.preview-document h3,
.preview-document h4,
.preview-document h5 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
    color: #333;
}

.preview-document p {
    margin-bottom: 1rem;
    line-height: 1.6;
}

.preview-document table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
}

.preview-document table th,
.preview-document table td {
    border: 1px solid #dee2e6;
    padding: 0.75rem;
    text-align: left;
}

.preview-document table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

@media print {
    .preview-document {
        transform: scale(1) !important;
        width: 100% !important;
        padding: 0 !important;
    }
}

/* Empty State Styles */
.empty-state {
    padding: 3rem 2rem;
}

.empty-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    background: linear-gradient(135deg, #f8f9fc 0%, #e3e6f0 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #a0aec0;
    position: relative;
}

.empty-icon::before {
    content: '';
    position: absolute;
    inset: -2px;
    background: linear-gradient(135deg, #4e73df, #6610f2);
    border-radius: 50%;
    z-index: -1;
    opacity: 0.1;
}

.empty-title {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 1rem;
}

.empty-text {
    font-size: 1rem;
    line-height: 1.6;
    max-width: 400px;
    margin: 0 auto 2rem;
}

/* Enhanced Transaction List Styles */
.stats-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%);
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.search-wrapper {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    z-index: 2;
}

.search-input {
    padding-left: 2.5rem;
    border: 2px solid #e3e6f0;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

.transaction-row {
    transition: all 0.2s ease;
}

.transaction-row:hover {
    background-color: rgba(78, 115, 223, 0.05);
    transform: translateX(2px);
}

.transaction-row td {
    padding: 0.5rem 0.75rem !important;
    vertical-align: middle;
    font-size: 0.875rem;
}

.transaction-row .property-icon,
.transaction-row .customer-avatar,
.transaction-row .agent-avatar {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(78, 115, 223, 0.1);
    font-size: 0.75rem;
}

.transaction-row .badge {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
}

.transaction-row .btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.property-icon, .customer-avatar, .agent-avatar {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(78, 115, 223, 0.1);
}

.badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.5rem 0.75rem;
}

.btn-group .btn {
    border-radius: 6px;
    margin: 0 1px;
}

.bulk-actions {
    animation: slideInLeft 0.3s ease;
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.empty-state {
    padding: 4rem 2rem;
}

.empty-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto;
    background: linear-gradient(135deg, #f8f9fc 0%, #e3e6f0 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: #a0aec0;
}

.empty-title {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 1rem;
}

.empty-text {
    font-size: 1rem;
    line-height: 1.6;
    max-width: 400px;
    margin: 0 auto 2rem;
}

/* Modal Enhancements */
.modal-header.bg-primary,
.modal-header.bg-success {
    border-radius: 0.375rem 0.375rem 0 0;
}

.form-select:focus,
.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

/* Responsive Improvements for Transaction Table */
@media (max-width: 992px) {
    .transaction-row .btn-group {
        flex-direction: column;
    }

    .transaction-row .btn-group .btn {
        margin: 1px 0;
    }

    .header-actions {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
}

@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 1rem;
    }

    .search-wrapper {
        margin-bottom: 1rem;
    }

    .table-responsive {
        font-size: 0.875rem;
    }

    .transaction-row td {
        padding: 0.75rem 0.5rem;
    }

    .property-icon, .customer-avatar, .agent-avatar {
        width: 24px;
        height: 24px;
        font-size: 0.875rem;
    }
}

/* Animation for cards loading */
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

.contract-card {
    animation: fadeInUp 0.6s ease-out;
}

.contract-card:nth-child(1) { animation-delay: 0.1s; }
.contract-card:nth-child(2) { animation-delay: 0.2s; }
.contract-card:nth-child(3) { animation-delay: 0.3s; }
.contract-card:nth-child(4) { animation-delay: 0.4s; }
.contract-card:nth-child(5) { animation-delay: 0.5s; }
.contract-card:nth-child(6) { animation-delay: 0.6s; }

/* Clickable Row Styles */
.clickable-row {
    cursor: pointer;
    transition: all 0.3s ease;
}

.clickable-row:hover {
    background-color: rgba(78, 115, 223, 0.08) !important;
    transform: translateX(3px);
    box-shadow: 0 2px 8px rgba(78, 115, 223, 0.15);
}

.clickable-row.expanded {
    background-color: rgba(78, 115, 223, 0.05) !important;
    border-bottom: 2px solid #4e73df;
}

/* Transaction Details Row */
.transaction-details-row {
    background-color: #f8f9fc !important;
    animation: slideDown 0.4s ease;
}

.transaction-details-container {
    border-radius: 8px;
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.1);
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%) !important;
}

/* Animation for expanding details */
@keyframes slideDown {
    0% {
        opacity: 0;
        max-height: 0;
        padding-top: 0;
        padding-bottom: 0;
    }
    100% {
        opacity: 1;
        max-height: 500px;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
}

/* Commission Payment Modal */
.commission-payment-modal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.commission-info-card {
    background: linear-gradient(135deg, #fff5e6 0%, #fff 100%);
    border: 2px solid #ffc107;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.commission-info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.2);
}

/* Document View Button */
.document-item {
    background: #ffffff;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.document-item:hover {
    border-color: #4e73df;
    box-shadow: 0 2px 8px rgba(78, 115, 223, 0.15);
}

/* Enhanced table styles for expandable content */
.table-sm th,
.table-sm td {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.payment-history .table {
    margin-bottom: 0;
}

.payment-history thead th {
    background-color: rgba(78, 115, 223, 0.1);
    color: #4e73df;
    font-weight: 600;
    border: none;
}

/* Commission cards in expanded view */
.commission-details .card {
    transition: all 0.3s ease;
}

.commission-details .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Loading states */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(78, 115, 223, 0.3);
    border-radius: 50%;
    border-top-color: #4e73df;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive adjustments for expandable content */
@media (max-width: 768px) {
    .transaction-details-container {
        padding: 1rem !important;
    }

    .transaction-details-container .col-md-6 {
        margin-bottom: 2rem;
    }

    .commission-details .col-md-6 {
        margin-bottom: 1rem;
    }
}

/* AI Analysis Interface Styles */
.source-selection {
    background: #f8f9fc;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e3e6f0;
}

.source-tabs {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.source-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: white;
    border: 2px solid #e3e6f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 180px;
    text-decoration: none;
    color: inherit;
}

.source-tab i {
    font-size: 2rem;
    color: #6c757d;
    transition: all 0.3s ease;
}

.source-tab span {
    font-weight: 500;
    color: #5a5c69;
    transition: all 0.3s ease;
}

.source-tab:hover {
    border-color: #4e73df;
    background: rgba(78, 115, 223, 0.05);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(78, 115, 223, 0.2);
    text-decoration: none;
    color: inherit;
}

.source-tab:hover i,
.source-tab:hover span {
    color: #4e73df;
}

.source-tab.active {
    background: linear-gradient(135deg, #4e73df 0%, #5a67d8 100%);
    border-color: #4e73df;
    color: white;
    box-shadow: 0 8px 25px rgba(78, 115, 223, 0.3);
}

.source-tab.active i,
.source-tab.active span {
    color: white;
}

/* Template Selection */
.template-selector {
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e3e6f0;
}

.template-list .row {
    margin: 0;
}

.template-card {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: white;
    border: 2px solid #e3e6f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    height: 100%;
}

.template-card:hover {
    border-color: #4e73df;
    background: rgba(78, 115, 223, 0.05);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(78, 115, 223, 0.2);
}

.template-card.selected {
    border-color: #1cc88a;
    background: rgba(28, 200, 138, 0.1);
    box-shadow: 0 5px 15px rgba(28, 200, 138, 0.3);
}

.template-icon {
    margin-right: 1rem;
    font-size: 2rem;
}

.template-info {
    flex: 1;
}

.template-name {
    margin-bottom: 0.25rem;
    font-weight: 600;
    color: #2d3748;
}

.template-check {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
}

.template-check i {
    font-size: 1.2rem;
}

/* Upload Zone for Analysis */
.upload-zone-analysis {
    border: 3px dashed #4e73df;
    border-radius: 15px;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, rgba(78, 115, 223, 0.05) 0%, rgba(90, 103, 216, 0.05) 100%);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.upload-zone-analysis:hover,
.upload-zone-analysis.dragover {
    border-color: #1cc88a;
    background: linear-gradient(135deg, rgba(28, 200, 138, 0.1) 0%, rgba(23, 162, 184, 0.1) 100%);
    transform: scale(1.02);
}

.upload-zone-analysis .upload-icon {
    font-size: 4rem;
    color: #4e73df;
    margin-bottom: 1rem;
    animation: float 3s ease-in-out infinite;
}

.upload-zone-analysis:hover .upload-icon {
    color: #1cc88a;
    transform: scale(1.1);
}

/* Upload Preview */
.upload-preview {
    margin-top: 1.5rem;
    padding: 1rem;
    background: #f8f9fc;
    border-radius: 12px;
    border: 1px solid #e3e6f0;
}

.selected-file {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.selected-file .file-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #4e73df 0%, #5a67d8 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-right: 1rem;
}

.selected-file .file-info {
    flex: 1;
}

.selected-file .file-name {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

/* Analysis Controls */
.analysis-controls {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
    border-radius: 12px;
    border: 1px solid #e3e6f0;
}

/* Analysis Progress */
.analysis-progress {
    padding: 2rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e3e6f0;
    margin-top: 1.5rem;
}

.analysis-progress .progress {
    background: #e3e6f0;
    border-radius: 10px;
}

.analysis-progress .progress-bar {
    background: linear-gradient(135deg, #4e73df 0%, #5a67d8 100%);
    border-radius: 10px;
}

/* Analysis Results */
.analysis-results {
    margin-top: 1.5rem;
    padding: 2rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e3e6f0;
}

.results-header {
    border-bottom: 2px solid #e3e6f0;
    padding-bottom: 1rem;
}

.analysis-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 1.5rem 0;
}

.summary-card {
    text-align: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
    border-radius: 12px;
    border: 1px solid #e3e6f0;
}

.summary-card.excellent {
    background: linear-gradient(135deg, rgba(28, 200, 138, 0.1) 0%, rgba(23, 162, 184, 0.1) 100%);
    border-color: #1cc88a;
}

.summary-card.good {
    background: linear-gradient(135deg, rgba(54, 185, 204, 0.1) 0%, rgba(78, 115, 223, 0.1) 100%);
    border-color: #36b9cc;
}

.summary-card.warning {
    background: linear-gradient(135deg, rgba(246, 194, 62, 0.1) 0%, rgba(255, 193, 7, 0.1) 100%);
    border-color: #f6c23e;
}

.summary-card.danger {
    background: linear-gradient(135deg, rgba(231, 74, 59, 0.1) 0%, rgba(220, 53, 69, 0.1) 100%);
    border-color: #e74a3b;
}

.summary-icon {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.summary-value {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.summary-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.analysis-section {
    margin: 2rem 0;
    padding: 1.5rem;
    background: #f8f9fc;
    border-radius: 12px;
    border-left: 4px solid #4e73df;
}

.analysis-section h6 {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 1rem;
}

.analysis-list {
    list-style: none;
    padding: 0;
}

.analysis-list li {
    padding: 0.75rem 0;
    border-bottom: 1px solid #e3e6f0;
    display: flex;
    align-items: flex-start;
}

.analysis-list li:last-child {
    border-bottom: none;
}

.analysis-list li i {
    margin-right: 0.75rem;
    margin-top: 0.25rem;
    font-size: 1rem;
}

.analysis-list .benefit i {
    color: #1cc88a;
}

.analysis-list .risk i {
    color: #e74a3b;
}

.analysis-list .recommendation i {
    color: #36b9cc;
}

/* Empty states */
.empty-templates {
    padding: 3rem 2rem;
    background: white;
    border-radius: 12px;
    border: 2px dashed #e3e6f0;
}

.empty-templates i {
    opacity: 0.5;
}

/* Responsive for AI Analysis */
@media (max-width: 768px) {
    .source-tabs {
        flex-direction: column;
        gap: 0.75rem;
    }

    .source-tab {
        min-width: auto;
        padding: 1rem;
    }

    .template-card {
        padding: 0.75rem;
    }

    .upload-zone-analysis {
        padding: 2rem 1rem;
    }

    .upload-zone-analysis .upload-icon {
        font-size: 3rem;
    }

    .analysis-summary {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }

    .analysis-controls {
        padding: 1rem;
    }

    .analysis-results {
        padding: 1rem;
    }
}
</style>
<script>

// Setup transaction modal event handlers - Simplified version
function setupTransactionModal() {
    console.log('Setting up transaction modal with simplified approach');

    // Get direct references to modal elements
    const modal = document.getElementById('transactionDetailsModal');
    const modalTransactionIdSpan = document.getElementById('modalTransactionId');

    if (!modal) {
        console.error('Modal element not found: #transactionDetailsModal');
        return;
    }

    if (!modalTransactionIdSpan) {
        console.error('Modal transaction ID span not found: #modalTransactionId');
        return;
    }

    console.log('Modal elements found successfully');

    // Add event listener directly to all buttons that open the modal
    const buttons = document.querySelectorAll('button[data-bs-target="#transactionDetailsModal"]');
    console.log('Found modal trigger buttons:', buttons.length);

    buttons.forEach((button, index) => {
        const transactionId = button.getAttribute('data-transaction-id');
        console.log(`Button ${index+1} has transaction ID:`, transactionId);

        // Direct click handler to set the transaction ID in the modal
        button.addEventListener('click', function() {
            console.log('Button clicked with transaction ID:', transactionId);
            modalTransactionIdSpan.textContent = transactionId;
            console.log('Set transaction ID in modal to:', transactionId);
        });
    });

    console.log('Added click handlers to all modal buttons');
}

// Show error state in all tabs
function showErrorState(message) {
    const errorHtml = `
        <div class="text-center py-4">
            <div class="text-danger mb-3">
                <i class="fas fa-exclamation-triangle fa-3x"></i>
            </div>
            <h6 class="text-danger">Lỗi tải dữ liệu</h6>
            <p class="text-muted">${message}</p>
        </div>
    `;

    document.getElementById('transactionInfoContent').innerHTML = errorHtml;
    document.getElementById('paymentHistoryContent').innerHTML = errorHtml;
    document.getElementById('documentsContent').innerHTML = errorHtml;
}

// Populate transaction info tab
function populateTransactionInfo(transaction) {
    const content = document.getElementById('transactionInfoContent');
    const html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%" class="text-muted">Mã giao dịch:</td>
                                <td class="fw-bold">${transaction.TransactionID}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Loại giao dịch:</td>
                                <td>
                                    <span class="badge ${transaction.TransactionType === 'Sale' ? 'bg-primary' : 'bg-info'}">
                                        ${transaction.TransactionType === 'Sale' ? 'Bán' : 'Cho thuê'}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tổng giá trị:</td>
                                <td class="fw-bold text-success">${formatCurrency(transaction.TotalPrice)}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ngày giao dịch:</td>
                                <td>${formatDate(transaction.TransactionDate)}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Trạng thái:</td>
                                <td>
                                    <span class="badge ${getStatusBadgeClass(transaction.TranStatus)}">
                                        ${transaction.TranStatus}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-users me-2"></i>Thông tin liên quan
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%" class="text-muted">Bất động sản:</td>
                                <td class="fw-bold">${transaction.trans_property ? transaction.trans_property.Title : 'N/A'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Khách hàng:</td>
                                <td>${transaction.trans_cus ? transaction.trans_cus.Name : 'N/A'}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nhân viên:</td>
                                <td>${transaction.trans_agent ? transaction.trans_agent.Name : 'N/A'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    content.innerHTML = html;
}

// Populate payment history tab
function populatePaymentHistory(paymentHistory) {
    const content = document.getElementById('paymentHistoryContent');

    if (!paymentHistory || paymentHistory.length === 0) {
        content.innerHTML = `
            <div class="text-center py-4">
                <div class="text-muted mb-3">
                    <i class="fas fa-credit-card fa-3x"></i>
                </div>
                <h6 class="text-muted">Chưa có lịch sử thanh toán</h6>
                <p class="text-muted">Giao dịch này chưa có khoản thanh toán nào.</p>
            </div>
        `;
        return;
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Lần TT</th>
                        <th>Số tiền</th>
                        <th>Ngày thanh toán</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
    `;

    paymentHistory.forEach(payment => {
        let badgeClass = 'bg-secondary';
        if (payment.DTran_Status === 'Hoàn Thành') {
            badgeClass = 'bg-success';
        } else if (payment.DTran_Status === 'Chờ đợi') {
            badgeClass = 'bg-warning';
        } else if (payment.DTran_Status === 'Hủy') {
            badgeClass = 'bg-danger';
        }

        html += `
            <tr>
                <td>${payment.Num_Pay}</td>
                <td class="fw-bold text-success">${formatCurrency(payment.Price)}</td>
                <td>${payment.DTran_Date ? formatDate(payment.DTran_Date) : 'N/A'}</td>
                <td>
                    <span class="badge ${badgeClass}">
                        ${payment.DTran_Status}
                    </span>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    content.innerHTML = html;
}

// Populate documents tab
function populateDocuments(documents) {
    const content = document.getElementById('documentsContent');

    if (!documents || documents.length === 0) {
        content.innerHTML = `
            <div class="text-center py-4">
                <div class="text-muted mb-3">
                    <i class="fas fa-folder-open fa-3x"></i>
                </div>
                <h6 class="text-muted">Chưa có tài liệu</h6>
                <p class="text-muted">Giao dịch này chưa có tài liệu nào được tải lên.</p>
            </div>
        `;
        return;
    }

    let html = '<div class="row">';

    documents.forEach(doc => {
        html += `
            <div class="col-md-6 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-file-alt fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${doc.DocumentName}</h6>
                                <small class="text-muted">${doc.DocumentType}</small>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="viewDocument('${doc.FilePath}')">
                                        <i class="fas fa-eye me-1"></i>Xem
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="downloadDocument('${doc.FilePath}')">
                                        <i class="fas fa-download me-1"></i>Tải xuống
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    content.innerHTML = html;
}

// Toggle payment button visibility
function togglePaymentButton(status) {
    const paymentBtn = document.getElementById('processPaymentBtn');
    if (paymentBtn) {
        if (status === 'Paid') {
            paymentBtn.style.display = 'none';
        } else {
            paymentBtn.style.display = 'inline-block';
        }
    }
}

// Helper functions
function formatCurrency(amount) {
    if (!amount) return 'N/A';
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'Paid': return 'bg-success';
        case 'Pending': return 'bg-warning';
        case 'Cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

// Contract Template Event Handlers
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Contract Template Handlers Loaded');

    // Handle Preview buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-preview')) {
            e.preventDefault();
            const button = e.target.closest('.btn-preview');
            handleContractPreview(button);
        }

        if (e.target.closest('.btn-print')) {
            e.preventDefault();
            const button = e.target.closest('.btn-print');
            handleContractPrint(button);
        }
    });

    // Handle modal preview buttons
    document.getElementById('downloadFromPreview')?.addEventListener('click', function() {
        const currentFileUrl = this.getAttribute('data-current-file-url');
        const currentFileName = this.getAttribute('data-current-file-name');
        if (currentFileUrl && currentFileName) {
            const link = document.createElement('a');
            link.href = currentFileUrl;
            link.download = currentFileName;
            link.click();
        }
    });

    document.getElementById('printFromPreview')?.addEventListener('click', function() {
        const currentPrintUrl = this.getAttribute('data-current-print-url');
        if (currentPrintUrl) {
            window.open(currentPrintUrl, '_blank');
        } else {
            // Fallback to print current modal content
            window.print();
        }
    });

    // Handle zoom in/out functionality
    let currentZoom = 100; // Default zoom level
    const zoomStep = 20; // Zoom step in percentage

    document.getElementById('zoomIn')?.addEventListener('click', function() {
        currentZoom += zoomStep;
        updateZoom();
    });

    document.getElementById('zoomOut')?.addEventListener('click', function() {
        if (currentZoom > zoomStep) {
            currentZoom -= zoomStep;
            updateZoom();
        }
    });

    // Function to apply zoom level to preview content
    function updateZoom() {
        const zoomLabel = document.querySelector('.zoom-level');
        const previewDocument = document.querySelector('.preview-document');

        if (zoomLabel) {
            zoomLabel.textContent = `${currentZoom}%`;
        }

        if (previewDocument) {
            previewDocument.style.transform = `scale(${currentZoom/100})`;
            previewDocument.style.transformOrigin = 'top left';
        }
    }
});

// Handle contract preview
function handleContractPreview(button) {
    const previewUrl = button.getAttribute('data-preview-url');
    const fileName = button.getAttribute('data-file-name');
    const fileUrl = button.getAttribute('data-file-url');

    console.log('Preview clicked:', { previewUrl, fileName, fileUrl });

    // Update modal title
    const modalTitle = document.querySelector('#previewModal .preview-file-name');
    if (modalTitle) {
        modalTitle.textContent = fileName || 'Hợp đồng';
    }

    // Store file info for download/print buttons
    const downloadBtn = document.getElementById('downloadFromPreview');
    const printBtn = document.getElementById('printFromPreview');

    if (downloadBtn) {
        downloadBtn.setAttribute('data-current-file-url', fileUrl);
        downloadBtn.setAttribute('data-current-file-name', fileName);
    }

    if (printBtn) {
        printBtn.setAttribute('data-current-print-url', button.getAttribute('data-print-url'));
    }

    // Load preview content
    loadContractPreview(previewUrl, fileName);
}

// Handle contract print
function handleContractPrint(button) {
    const printUrl = button.getAttribute('data-print-url');
    const fileName = button.getAttribute('data-file-name');

    console.log('Print clicked:', { printUrl, fileName });

    if (printUrl) {
        // Lấy nội dung từ print endpoint và hiển thị trong cửa sổ in
        fetch(printUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data && data.print_html) {
                    // Tạo cửa sổ mới để in
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(data.print_html);
                    printWindow.document.close();
                    // Tự động mở hộp thoại in sau khi tải xong trang
                    printWindow.onload = function() {
                        printWindow.print();
                    };
                } else {
                    throw new Error('Không tìm thấy nội dung để in');
                }
            })
            .catch(error => {
                console.error('Print error:', error);
                alert('Không thể in tài liệu: ' + error.message);

                // Fallback: tải xuống file nếu không in được
                const fileUrl = button.getAttribute('data-file-url');
                if (fileUrl) {
                    window.open(fileUrl, '_blank');
                }
            });
    } else {
        // Fallback: download and let user print manually
        const fileUrl = button.getAttribute('data-file-url');
        if (fileUrl) {
            window.open(fileUrl, '_blank');
        }
    }
}

// Load contract preview content
function loadContractPreview(previewUrl, fileName) {
    const previewContent = document.getElementById('previewContent');

    if (!previewContent) {
        console.error('Preview content container not found');
        return;
    }

    // Show loading state
    previewContent.innerHTML = `
        <div class="preview-loading text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <p class="mt-3 text-muted">Đang tải nội dung xem trước...</p>
        </div>
    `;

    if (previewUrl) {
        // Load content via AJAX
        fetch(previewUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json(); // Chuyển từ text sang json vì endpoint trả về JSON
            })
            .then(data => {
                // Kiểm tra nếu có dữ liệu preview_html trong response
                if (data && data.preview_html) {
                    // Hiển thị HTML từ response
                    previewContent.innerHTML = `
                        <div class="preview-document p-4">
                            ${data.preview_html}
                        </div>
                    `;
                } else {
                    throw new Error('Không tìm thấy nội dung xem trước');
                }
            })
            .catch(error => {
                console.error('Preview loading error:', error);
                previewContent.innerHTML = `
                    <div class="text-center py-5">
                        <div class="text-warning mb-3">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                        </div>
                        <h6 class="text-warning">Không thể tải xem trước</h6>
                        <p class="text-muted mb-3">Lỗi: ${error.message}</p>
                        <p class="text-muted">Vui lòng tải xuống file để xem nội dung</p>
                    </div>
                `;
            });
    } else {
        // Show message when no preview URL available
        previewContent.innerHTML = `
            <div class="text-center py-5">
                <div class="text-info mb-3">
                    <i class="fas fa-info-circle fa-3x"></i>
                </div>
                <h6 class="text-info">Xem trước không khả dụng</h6>
                <p class="text-muted">Vui lòng tải xuống file để xem nội dung</p>
            </div>
        `;
    }
}

// AI Contract Analysis Functionality
function initializeAIAnalysis() {
    console.log('🤖 Initializing AI Contract Analysis...');

    // Source tab switching
    const sourceTabs = document.querySelectorAll('.source-tab');
    const sourceContents = document.querySelectorAll('.source-content');

    sourceTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const source = this.dataset.source;

            // Update active tab
            sourceTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Show corresponding content
            sourceContents.forEach(content => {
                content.style.display = 'none';
            });

            if (source === 'template') {
                document.getElementById('template-selection').style.display = 'block';
            } else if (source === 'upload') {
                document.getElementById('file-upload-analysis').style.display = 'block';
            }

            // Reset analysis button state
            updateAnalysisButtonState();
        });
    });

    // Template selection
    const templateCards = document.querySelectorAll('.template-card');
    templateCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selection from all cards
            templateCards.forEach(c => {
                c.classList.remove('selected');
                c.querySelector('.template-check i').style.display = 'none';
            });

            // Select current card
            this.classList.add('selected');
            this.querySelector('.template-check i').style.display = 'block';

            // Update analysis button state
            updateAnalysisButtonState();
        });
    });

    // File upload functionality
    const dropZone = document.getElementById('dropZoneAnalysis');
    const fileInput = document.getElementById('analysisFileInput');
    const browseButton = document.getElementById('browseAnalysisFile');
    const uploadPreview = document.getElementById('uploadPreview');
    const removeFileButton = document.getElementById('removeFile');

    let selectedFile = null;

    // Browse file button
    if (browseButton) {
        browseButton.addEventListener('click', () => {
            fileInput.click();
        });
    }

    // File input change
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleFileSelection(file);
            }
        });
    }

    // Drag and drop functionality
    if (dropZone) {
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelection(files[0]);
            }
        });
    }

    // Remove file button
    if (removeFileButton) {
        removeFileButton.addEventListener('click', function() {
            selectedFile = null;
            uploadPreview.style.display = 'none';
            dropZone.style.display = 'block';
            fileInput.value = '';
            updateAnalysisButtonState();
        });
    }

    // File selection handler
    function handleFileSelection(file) {
        // Validate file type
        const allowedTypes = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf'];
        if (!allowedTypes.includes(file.type)) {
            alert('Chỉ hỗ trợ file .docx và .pdf');
            return;
        }

        // Validate file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            alert('File quá lớn. Vui lòng chọn file dưới 10MB');
            return;
        }

        selectedFile = file;

        // Update UI
        dropZone.style.display = 'none';
        uploadPreview.style.display = 'block';

        // Set file info
        uploadPreview.querySelector('.file-name').textContent = file.name;
        uploadPreview.querySelector('.file-size').textContent = formatFileSize(file.size);

        // Update icon based on file type
        const fileIcon = uploadPreview.querySelector('.file-icon i');
        if (file.type.includes('pdf')) {
            fileIcon.className = 'fas fa-file-pdf';
        } else {
            fileIcon.className = 'fas fa-file-word';
        }

        updateAnalysisButtonState();
    }

    // Analysis button and process
    const startAnalysisButton = document.getElementById('startAnalysis');
    if (startAnalysisButton) {
        startAnalysisButton.addEventListener('click', function() {
            startContractAnalysis();
        });
    }

    // Clear analysis button
    const clearAnalysisButton = document.getElementById('clearAnalysis');
    if (clearAnalysisButton) {
        clearAnalysisButton.addEventListener('click', function() {
            clearAnalysisResults();
        });
    }

    // Export analysis button
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'exportAnalysis') {
            exportAnalysisReport();
        } else if (e.target && e.target.id === 'newAnalysis') {
            resetAnalysisInterface();
        }
    });

    function updateAnalysisButtonState() {
        const startButton = document.getElementById('startAnalysis');
        if (!startButton) return;

        const activeTab = document.querySelector('.source-tab.active');
        if (!activeTab) return;

        const source = activeTab.dataset.source;
        let canAnalyze = false;

        if (source === 'template') {
            const selectedTemplate = document.querySelector('.template-card.selected');
            canAnalyze = !!selectedTemplate;
        } else if (source === 'upload') {
            canAnalyze = !!selectedFile;
        }

        startButton.disabled = !canAnalyze;

        if (canAnalyze) {
            startButton.classList.remove('btn-secondary');
            startButton.classList.add('btn-primary');
        } else {
            startButton.classList.remove('btn-primary');
            startButton.classList.add('btn-secondary');
        }
    }

    function startContractAnalysis() {
        const activeTab = document.querySelector('.source-tab.active');
        if (!activeTab) return;

        const source = activeTab.dataset.source;
        const analysisProgress = document.getElementById('analysisProgress');
        const analysisResults = document.getElementById('analysisResults');

        // Hide results if visible
        analysisResults.style.display = 'none';

        // Show progress
        analysisProgress.style.display = 'block';

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        if (source === 'template') {
            const selectedTemplate = document.querySelector('.template-card.selected');
            if (!selectedTemplate) return;

            formData.append('source', 'template');
            formData.append('template_name', selectedTemplate.dataset.template);
        } else if (source === 'upload') {
            if (!selectedFile) return;

            formData.append('source', 'upload');
            formData.append('contract_file', selectedFile);
        }

        // Progress simulation
        simulateProgress();

        // Make AJAX request
        fetch('/admin/contract/analyze', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    if (response.status === 400 && errorData.error) {
                        throw new Error(`VALIDATION:${errorData.error}`);
                    }
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayAnalysisResults(data.analysis);
            } else {
                throw new Error(data.message || 'Lỗi không xác định');
            }
        })
        .catch(error => {
            console.error('Analysis error:', error);

            // Kiểm tra nếu là lỗi validation
            if (error.message.startsWith('VALIDATION:')) {
                const validationMessage = error.message.replace('VALIDATION:', '');
                displayValidationError(validationMessage);
            } else {
                displayAnalysisError(error.message);
            }
        })
        .finally(() => {
            analysisProgress.style.display = 'none';
        });
    }

    function simulateProgress() {
        const progressBar = document.querySelector('#analysisProgress .progress-bar');
        const progressText = document.getElementById('progressText');

        if (!progressBar || !progressText) return;

        const steps = [
            { percent: 20, text: 'Đang đọc nội dung hợp đồng...' },
            { percent: 40, text: 'Phân tích cấu trúc văn bản...' },
            { percent: 60, text: 'Đánh giá điều khoản và rủi ro...' },
            { percent: 80, text: 'Tạo khuyến nghị...' },
            { percent: 100, text: 'Hoàn thành phân tích!' }
        ];

        let stepIndex = 0;

        const progressInterval = setInterval(() => {
            if (stepIndex < steps.length) {
                const step = steps[stepIndex];
                progressBar.style.width = step.percent + '%';
                progressText.textContent = step.text;
                stepIndex++;
            } else {
                clearInterval(progressInterval);
            }
        }, 800);
    }

    function displayAnalysisResults(analysis) {
        const analysisResults = document.getElementById('analysisResults');
        const analysisContent = document.getElementById('analysisContent');

        if (!analysisResults || !analysisContent) return;

        // Create results HTML
        const resultsHTML = `
            <div class="analysis-summary">
                <div class="summary-card ${getRatingClass(analysis.overall_rating)}">
                    <div class="summary-icon">
                        <i class="fas ${getRatingIcon(analysis.overall_rating)}"></i>
                    </div>
                    <div class="summary-value">${analysis.overall_rating}/10</div>
                    <div class="summary-label">Điểm tổng quan</div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="summary-value">${analysis.benefits.length}</div>
                    <div class="summary-label">Lợi ích</div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                    </div>
                    <div class="summary-value">${analysis.risks.length}</div>
                    <div class="summary-label">Rủi ro</div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="fas fa-lightbulb text-info"></i>
                    </div>
                    <div class="summary-value">${analysis.recommendations.length}</div>
                    <div class="summary-label">Khuyến nghị</div>
                </div>
            </div>

            <div class="analysis-section">
                <h6><i class="fas fa-thumbs-up me-2 text-success"></i>Lợi ích của hợp đồng</h6>
                <ul class="analysis-list">
                    ${analysis.benefits.map(benefit => `
                        <li class="benefit">
                            <i class="fas fa-check-circle"></i>
                            <span>${benefit}</span>
                        </li>
                    `).join('')}
                </ul>
            </div>

            <div class="analysis-section">
                <h6><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Rủi ro cần lưu ý</h6>
                <ul class="analysis-list">
                    ${analysis.risks.map(risk => `
                        <li class="risk">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>${risk}</span>
                        </li>
                    `).join('')}
                </ul>
            </div>

            <div class="analysis-section">
                <h6><i class="fas fa-lightbulb me-2 text-info"></i>Khuyến nghị cải thiện</h6>
                <ul class="analysis-list">
                    ${analysis.recommendations.map(recommendation => `
                        <li class="recommendation">
                            <i class="fas fa-lightbulb"></i>
                            <span>${recommendation}</span>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;

        analysisContent.innerHTML = resultsHTML;
        analysisResults.style.display = 'block';

        // Scroll to results
        analysisResults.scrollIntoView({ behavior: 'smooth' });
    }

    function displayAnalysisError(message) {
        const analysisResults = document.getElementById('analysisResults');
        const analysisContent = document.getElementById('analysisContent');

        if (!analysisResults || !analysisContent) return;

        analysisContent.innerHTML = `
            <div class="text-center py-5">
                <div class="text-danger mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
                <h5 class="text-danger">Lỗi phân tích hợp đồng</h5>
                <p class="text-muted">${message}</p>
                <button class="btn btn-primary" onclick="resetAnalysisInterface()">
                    <i class="fas fa-redo me-2"></i>Thử lại
                </button>
            </div>
        `;

        analysisResults.style.display = 'block';
    }

    function displayValidationError(message) {
        const analysisResults = document.getElementById('analysisResults');
        const analysisContent = document.getElementById('analysisContent');

        if (!analysisResults || !analysisContent) return;

        analysisContent.innerHTML = `
            <div class="text-center py-5">
                <div class="text-warning mb-3">
                    <i class="fas fa-file-times fa-3x"></i>
                </div>
                <h5 class="text-warning">Tài liệu không phù hợp</h5>
                <p class="text-muted">${message}</p>
                <div class="alert alert-info mt-3">
                    <h6><i class="fas fa-info-circle me-2"></i>Hệ thống hỗ trợ phân tích:</h6>
                    <ul class="mb-0 text-start">
                        <li>Hợp đồng mua bán bất động sản</li>
                        <li>Hợp đồng cho thuê nhà, đất</li>
                        <li>Hợp đồng môi giới bất động sản</li>
                        <li>Các thỏa thuận liên quan đến giao dịch BDS</li>
                    </ul>
                </div>
                <button class="btn btn-primary mt-3" onclick="resetAnalysisInterface()">
                    <i class="fas fa-upload me-2"></i>Tải file khác
                </button>
            </div>
        `;

        analysisResults.style.display = 'block';
    }

    function clearAnalysisResults() {
        const analysisResults = document.getElementById('analysisResults');
        if (analysisResults) {
            analysisResults.style.display = 'none';
        }
    }

    function resetAnalysisInterface() {
        // Reset file selection
        selectedFile = null;
        const uploadPreview = document.getElementById('uploadPreview');
        const dropZone = document.getElementById('dropZoneAnalysis');
        const fileInput = document.getElementById('analysisFileInput');

        if (uploadPreview) uploadPreview.style.display = 'none';
        if (dropZone) dropZone.style.display = 'block';
        if (fileInput) fileInput.value = '';

        // Reset template selection
        const templateCards = document.querySelectorAll('.template-card');
        templateCards.forEach(card => {
            card.classList.remove('selected');
            card.querySelector('.template-check i').style.display = 'none';
        });

        // Reset tabs to template
        const sourceTabs = document.querySelectorAll('.source-tab');
        const sourceContents = document.querySelectorAll('.source-content');

        sourceTabs.forEach(tab => tab.classList.remove('active'));
        sourceContents.forEach(content => content.style.display = 'none');

        const templateTab = document.querySelector('.source-tab[data-source="template"]');
        if (templateTab) {
            templateTab.classList.add('active');
            document.getElementById('template-selection').style.display = 'block';
        }

        // Hide results
        clearAnalysisResults();

        // Update button state
        updateAnalysisButtonState();
    }

    function exportAnalysisReport() {
        // Simple implementation - could be enhanced to generate PDF
        const analysisContent = document.getElementById('analysisContent');
        if (!analysisContent) return;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Báo cáo phân tích hợp đồng</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .analysis-section { margin: 20px 0; }
                        .analysis-list { list-style: none; padding: 0; }
                        .analysis-list li { padding: 5px 0; }
                        .summary-card { display: inline-block; margin: 10px; padding: 15px; border: 1px solid #ddd; text-align: center; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Báo cáo phân tích hợp đồng</h1>
                        <p>Ngày tạo: ${new Date().toLocaleDateString('vi-VN')}</p>
                    </div>
                    ${analysisContent.innerHTML}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }

    function getRatingClass(rating) {
        if (rating >= 8) return 'excellent';
        if (rating >= 6) return 'good';
        if (rating >= 4) return 'warning';
        return 'danger';
    }

    function getRatingIcon(rating) {
        if (rating >= 8) return 'fa-star';
        if (rating >= 6) return 'fa-thumbs-up';
        if (rating >= 4) return 'fa-exclamation-triangle';
        return 'fa-times-circle';
    }

}

// Utility function for file size formatting (global scope)
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Initialize AI Analysis when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM Content Loaded - Initializing...');

    // Initialize existing functionality
    setupTransactionModal();

    // Initialize AI Analysis functionality
    initializeAIAnalysis();

    // Initialize Contract Template Management
    initializeContractManagement();

    // Initialize edit modal handlers
    initializeEditModalHandlers();

    // Initialize tooltips for all buttons with title attribute
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add a test button for debugging
    setTimeout(() => {
        console.log('🔍 Checking for modal trigger buttons...');
        const buttons = document.querySelectorAll('[data-bs-target="#transactionDetailsModal"]');
        console.log('Found modal trigger buttons:', buttons.length);
        buttons.forEach((btn, index) => {
            console.log(`Button ${index + 1}:`, {
                element: btn,
                transactionId: btn.getAttribute('data-transaction-id'),
                toggle: btn.getAttribute('data-bs-toggle'),
                target: btn.getAttribute('data-bs-target')
            });
        });
    }, 1000);
});

// Contract Template Management Functions
function initializeContractManagement() {
    // File upload handling
    const fileInput = document.getElementById('contractFileInput');
    const dropZone = document.getElementById('contractDropZone');
    const filePreview = document.getElementById('contractFilePreview');

    if (fileInput && dropZone) {
        // File input change
        fileInput.addEventListener('change', handleFileSelect);

        // Drag and drop
        dropZone.addEventListener('dragover', handleDragOver);
        dropZone.addEventListener('drop', handleFileDrop);
        dropZone.addEventListener('click', () => fileInput.click());
    }

    // Form submission
    const addForm = document.getElementById('addContractForm');
    if (addForm) {
        addForm.addEventListener('submit', handleAddContract);
    }

    const editForm = document.getElementById('editContractForm');
    if (editForm) {
        editForm.addEventListener('submit', handleEditContract);
    }
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (file) {
        displayFilePreview(file);
    }
}

function handleDragOver(event) {
    event.preventDefault();
    event.currentTarget.classList.add('drag-over');
}

function handleFileDrop(event) {
    event.preventDefault();
    event.currentTarget.classList.remove('drag-over');

    const files = event.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        document.getElementById('contractFileInput').files = files;
        displayFilePreview(file);
    }
}

function displayFilePreview(file) {
    const preview = document.getElementById('contractFilePreview');
    const fileName = document.querySelector('#contractFilePreview .file-name');
    const fileSize = document.querySelector('#contractFilePreview .file-size');

    if (preview && fileName && fileSize) {
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        preview.style.display = 'block';
        document.getElementById('contractDropZone').style.display = 'none';
    }
}

function removeContractFile() {
    const fileInput = document.getElementById('contractFileInput');
    const preview = document.getElementById('contractFilePreview');
    const dropZone = document.getElementById('contractDropZone');

    if (fileInput) fileInput.value = '';
    if (preview) preview.style.display = 'none';
    if (dropZone) dropZone.style.display = 'block';
}

function handleAddContract(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    // Find submit button in modal footer (outside form)
    const submitBtn = document.querySelector('#addContractModal button[type="submit"]');

    // Show loading state
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tải lên...';
    }

    fetch('/admin/contracts/add', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Thêm mẫu hợp đồng thành công!', 'success');

            // Close modal and reset form
            const modal = bootstrap.Modal.getInstance(document.getElementById('addContractModal'));
            modal.hide();
            event.target.reset();
            removeContractFile();

            // Refresh page
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi tải lên file!', 'error');
    })
    .finally(() => {
        // Reset loading state
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Tải lên';
        }
    });
}

function handleEditContract(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    // Find submit button in modal footer (outside form)
    const submitBtn = document.querySelector('#editContractModal button[type="submit"]');

    // Show loading state
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang cập nhật...';
    }

    fetch('/admin/contracts/edit', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Cập nhật mẫu hợp đồng thành công!', 'success');

            // Close modal and refresh
            const modal = bootstrap.Modal.getInstance(document.getElementById('editContractModal'));
            modal.hide();

            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi cập nhật!', 'error');
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Cập nhật';
        }
    });
}

function confirmDelete(templateName, displayName) {
    if (confirm(`Bạn có chắc chắn muốn xóa mẫu hợp đồng "${displayName}"?`)) {
        deleteContract(templateName);
    }
}

function deleteContract(templateName) {
    fetch('/admin/contracts/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ template: templateName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Xóa mẫu hợp đồng thành công!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi xóa!', 'error');
    });
}

function initializeEditModalHandlers() {
    // Handle edit button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-edit')) {
            const btn = e.target.closest('.btn-edit');
            const templateName = btn.getAttribute('data-template');
            const displayName = btn.getAttribute('data-display-name');

            // Populate edit modal
            document.getElementById('editTemplateName').value = templateName;
            document.getElementById('editContractName').value = displayName;

            // Clear description (will be populated from server if needed)
            document.getElementById('editContractDescription').value = '';
        }
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>

<!-- Add Contract Modal -->
<div class="modal fade" id="addContractModal" tabindex="-1" aria-labelledby="addContractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addContractModalLabel">
                    <i class="fas fa-plus me-2"></i>Thêm mẫu hợp đồng mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addContractForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="contractName" class="form-label">
                                <i class="fas fa-signature me-1"></i>Tên mẫu hợp đồng
                            </label>
                            <input type="text" class="form-control" id="contractName" name="contract_name" required
                                   placeholder="VD: Hợp đồng mua bán nhà đất">
                            <small class="form-text text-muted">Tên hiển thị của mẫu hợp đồng</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="contractDescription" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Mô tả
                            </label>
                            <textarea class="form-control" id="contractDescription" name="description" rows="3"
                                     placeholder="Mô tả ngắn gọn về mẫu hợp đồng này..."></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label class="form-label">
                                <i class="fas fa-file-upload me-1"></i>File hợp đồng
                            </label>

                            <!-- Drop Zone -->
                            <div class="upload-zone" id="contractDropZone">
                                <div class="upload-zone-content">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="upload-title">Kéo thả file vào đây</h5>
                                    <p class="upload-subtitle">hoặc <button type="button" class="btn-link">chọn file từ máy tính</button></p>
                                    <div class="upload-info">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Chỉ hỗ trợ file .docx, tối đa 10MB
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- File Preview -->
                            <div class="upload-preview" id="contractFilePreview" style="display: none;">
                                <div class="selected-file">
                                    <div class="file-icon">
                                        <i class="fas fa-file-word text-primary fa-2x"></i>
                                    </div>
                                    <div class="file-info">
                                        <h6 class="file-name mb-1"></h6>
                                        <small class="file-size text-muted"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeContractFile()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <input type="file" id="contractFileInput" name="contract_file"
                                   accept=".docx" style="display: none;" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Hủy
                </button>
                <button type="submit" form="addContractForm" class="btn btn-primary">
                    <i class="fas fa-upload me-2"></i>Tải lên
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Contract Modal -->
<div class="modal fade" id="editContractModal" tabindex="-1" aria-labelledby="editContractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="editContractModalLabel">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa mẫu hợp đồng
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editContractForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="editTemplateName" name="template_name">

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="editContractName" class="form-label">
                                <i class="fas fa-signature me-1"></i>Tên mẫu hợp đồng
                            </label>
                            <input type="text" class="form-control" id="editContractName" name="contract_name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="editContractDescription" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Mô tả
                            </label>
                            <textarea class="form-control" id="editContractDescription" name="description" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Lưu ý:</strong> Để trống nếu không muốn thay đổi file hợp đồng hiện tại.
                            </div>

                            <label class="form-label">
                                <i class="fas fa-file-upload me-1"></i>File hợp đồng mới (tùy chọn)
                            </label>
                            <input type="file" class="form-control" name="contract_file" accept=".docx">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Hủy
                </button>
                <button type="submit" form="editContractForm" class="btn btn-info">
                    <i class="fas fa-save me-2"></i>Cập nhật
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>Xem trước hợp đồng
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="previewContent" class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-3">Đang tải nội dung xem trước...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Đóng
                </button>
                <a href="#" class="btn btn-primary" id="downloadFromPreview" download>
                    <i class="fas fa-download me-2"></i>Tải xuống
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

<style>
/* Contract Card Styling */
.contract-card {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.contract-card:hover {
    border-color: #007bff;
    box-shadow: 0 8px 25px rgba(0,123,255,0.15);
    transform: translateY(-2px);
}

.contract-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 1rem;
    position: relative;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.contract-icon i {
    font-size: 1.5rem;
}

.contract-status .badge {
    font-size: 0.75rem;
}

.contract-title {
    color: #2c3e50;
    font-weight: 600;
    font-size: 1.1rem;
}

.contract-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    font-size: 0.85rem;
}

.contract-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-action {
    flex: 1;
    min-width: 0;
}

/* Upload Zone Styling */
.upload-zone {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-zone:hover {
    border-color: #007bff;
    background: #e7f3ff;
}

.upload-zone.drag-over {
    border-color: #28a745;
    background: #d4edda;
}

.upload-icon {
    margin-bottom: 1rem;
}

.upload-title {
    color: #495057;
    margin-bottom: 0.5rem;
}

.upload-subtitle {
    color: #6c757d;
    margin-bottom: 1rem;
}

.btn-link {
    color: #007bff;
    text-decoration: none;
    border: none;
    background: none;
    padding: 0;
}

.btn-link:hover {
    text-decoration: underline;
}

/* File Preview Styling */
.upload-preview {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    background: #f8f9fa;
}

.selected-file {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.file-info {
    flex: 1;
}

.file-name {
    color: #495057;
    margin: 0;
}

/* Empty State Styling */
.empty-state .empty-icon i {
    color: #dee2e6;
}

.empty-title {
    color: #6c757d;
    font-weight: 600;
}

.empty-text {
    font-size: 0.95rem;
}

/* Header Actions */
.header-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .contract-actions {
        justify-content: center;
    }

    .header-actions {
        flex-direction: column;
        gap: 0.25rem;
    }

    .upload-zone {
        padding: 1rem;
    }
}
</style>

<!-- Transaction Search and Filter JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get filter elements
    const searchInput = document.getElementById('transactionSearch');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFromFilter = document.getElementById('dateFrom');
    const dateToFilter = document.getElementById('dateTo');
    const refreshButton = document.getElementById('refreshTransactions');
    const transactionTable = document.getElementById('transactionsTable');

    // Only proceed if we're on the transactions tab and elements exist
    if (!searchInput || !transactionTable) {
        console.log('Transaction search elements not found, skipping initialization');
        return;
    }

    console.log('🔍 Initializing transaction search functionality');

    // Get all transaction rows (not including header)
    function getTransactionRows() {
        return transactionTable.querySelectorAll('tbody .transaction-row');
    }

    // Convert Vietnamese date format (DD/MM/YYYY) to Date object
    function parseVietnameseDate(dateStr) {
        if (!dateStr) return null;
        const parts = dateStr.trim().split('/');
        if (parts.length === 3) {
            return new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
        }
        return null;
    }

    // Main filter function
    function filterTransactions() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const statusValue = statusFilter ? statusFilter.value : '';
        const typeValue = typeFilter ? typeFilter.value : '';
        const dateFrom = dateFromFilter ? dateFromFilter.value : '';
        const dateTo = dateToFilter ? dateToFilter.value : '';

        const transactionRows = getTransactionRows();
        let visibleCount = 0;

        console.log('Filtering with:', { searchTerm, statusValue, typeValue, dateFrom, dateTo });

        transactionRows.forEach(row => {
            try {
                // Get text content from each column
                const cells = row.querySelectorAll('td');
                if (cells.length < 9) return; // Make sure we have enough columns

                const transactionId = cells[0].textContent.toLowerCase().trim();
                const propertyInfo = cells[1].textContent.toLowerCase().trim();
                const customerInfo = cells[2].textContent.toLowerCase().trim();
                const agentInfo = cells[3].textContent.toLowerCase().trim();
                const typeInfo = cells[4].textContent.toLowerCase().trim();
                const dateInfo = cells[6].textContent.trim(); // Date is in column 7 (index 6)
                const statusInfo = cells[7].textContent.toLowerCase().trim(); // Status is in column 8 (index 7)

                // Text search (search in ID, property, customer, agent)
                const matchesSearch = searchTerm === '' ||
                    transactionId.includes(searchTerm) ||
                    propertyInfo.includes(searchTerm) ||
                    customerInfo.includes(searchTerm) ||
                    agentInfo.includes(searchTerm);

                // Status filter
                let matchesStatus = true;
                if (statusValue && statusFilter) {
                    switch(statusValue) {
                        case 'Pending':
                            matchesStatus = statusInfo.includes('đang xử lý') || statusInfo.includes('pending');
                            break;
                        case 'Completed':
                            matchesStatus = statusInfo.includes('hoàn thành') || statusInfo.includes('completed');
                            break;
                        case 'Paid':
                            matchesStatus = statusInfo.includes('đã thanh toán') || statusInfo.includes('paid');
                            break;
                        case 'Cancelled':
                            matchesStatus = statusInfo.includes('đã hủy') || statusInfo.includes('cancelled');
                            break;
                        default:
                            matchesStatus = true;
                    }
                }

                // Type filter
                let matchesType = true;
                if (typeValue && typeFilter) {
                    switch(typeValue) {
                        case 'Sale':
                            matchesType = typeInfo.includes('bán') || typeInfo.includes('sale');
                            break;
                        case 'Rent':
                            matchesType = typeInfo.includes('cho thuê') || typeInfo.includes('rent');
                            break;
                        default:
                            matchesType = true;
                    }
                }

                // Date range filter
                let matchesDateRange = true;
                if (dateFrom || dateTo) {
                    const rowDate = parseVietnameseDate(dateInfo.split('\n')[0]); // Get first line (date part)

                    if (rowDate) {
                        if (dateFrom) {
                            const fromDate = new Date(dateFrom);
                            if (rowDate < fromDate) matchesDateRange = false;
                        }
                        if (dateTo) {
                            const toDate = new Date(dateTo);
                            if (rowDate > toDate) matchesDateRange = false;
                        }
                    }
                }

                // Show/hide row based on all filters
                const shouldShow = matchesSearch && matchesStatus && matchesType && matchesDateRange;

                if (shouldShow) {
                    row.style.display = '';
                    visibleCount++;

                    // Also ensure the expandable details row is hidden
                    const detailsRow = row.nextElementSibling;
                    if (detailsRow && detailsRow.classList.contains('transaction-details-row')) {
                        detailsRow.style.display = 'none';
                    }
                } else {
                    row.style.display = 'none';

                    // Also hide the expandable details row
                    const detailsRow = row.nextElementSibling;
                    if (detailsRow && detailsRow.classList.contains('transaction-details-row')) {
                        detailsRow.style.display = 'none';
                    }
                }
            } catch (error) {
                console.error('Error filtering row:', error);
            }
        });

        // Update visible count in footer
        updateVisibleCount(visibleCount);

        // Show/hide empty state
        toggleEmptyState(visibleCount === 0);

        console.log(`Filtered results: ${visibleCount} visible transactions`);
    }

    // Update the visible count in the footer
    function updateVisibleCount(count) {
        const countElement = document.querySelector('.card-footer .text-muted');
        if (countElement) {
            countElement.textContent = `Hiển thị ${count} giao dịch`;
        }
    }

    // Show/hide empty state message
    function toggleEmptyState(show) {
        const emptyRow = document.querySelector('tbody tr td[colspan]');
        if (emptyRow) {
            const emptyRowElement = emptyRow.closest('tr');
            emptyRowElement.style.display = show ? '' : 'none';
        }
    }

    // Clear all filters
    function clearFilters() {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = '';
        if (typeFilter) typeFilter.value = '';
        if (dateFromFilter) dateFromFilter.value = '';
        if (dateToFilter) dateToFilter.value = '';

        // Show all rows
        const transactionRows = getTransactionRows();
        transactionRows.forEach(row => {
            row.style.display = '';

            // Hide any expanded details
            const detailsRow = row.nextElementSibling;
            if (detailsRow && detailsRow.classList.contains('transaction-details-row')) {
                detailsRow.style.display = 'none';
            }
        });

        updateVisibleCount(transactionRows.length);
        toggleEmptyState(false);

        console.log('All filters cleared');
    }

    // Add event listeners
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('Search input changed:', this.value);
            filterTransactions();
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            console.log('Status filter changed:', this.value);
            filterTransactions();
        });
    }

    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            console.log('Type filter changed:', this.value);
            filterTransactions();
        });
    }

    if (dateFromFilter) {
        dateFromFilter.addEventListener('change', function() {
            console.log('Date from changed:', this.value);
            filterTransactions();
        });
    }

    if (dateToFilter) {
        dateToFilter.addEventListener('change', function() {
            console.log('Date to changed:', this.value);
            filterTransactions();
        });
    }

    // Refresh button functionality
    if (refreshButton) {
        refreshButton.addEventListener('click', function() {
            console.log('Refresh button clicked');
            clearFilters();
        });
    }

    // Add click functionality to expand/collapse transaction details
    const transactionRows = getTransactionRows();
    transactionRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on action buttons
            if (e.target.closest('.btn') || e.target.closest('button')) {
                return;
            }

            const detailsRow = row.nextElementSibling;
            if (detailsRow && detailsRow.classList.contains('transaction-details-row')) {
                const isVisible = detailsRow.style.display !== 'none';
                detailsRow.style.display = isVisible ? 'none' : 'table-row';
                console.log('Transaction details toggled for ID:', row.dataset.transactionId);
            }
        });

        // Add hover effect for better UX
        row.style.cursor = 'pointer';
    });

    console.log('✅ Transaction search functionality initialized successfully');
});
</script>
