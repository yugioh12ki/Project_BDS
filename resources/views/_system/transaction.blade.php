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
                    <i class="fas fa-cloud-upload-alt me-2"></i>
                    Tải lên mẫu
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
                            <div class="dropdown">
                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-plus me-1"></i>Thêm mẫu
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-upload me-2"></i>Tải lên file</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-link me-2"></i>Từ liên kết</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Quản lý thư mục</a></li>
                                </ul>
                            </div>
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
                                               class="btn btn-primary btn-action"
                                               download="{{ $template['name'] }}">
                                                <i class="fas fa-download me-2"></i>
                                                Tải xuống
                                            </a>
                                            <button type="button"
                                                    class="btn btn-outline-success btn-action btn-preview"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#previewModal"
                                                    data-file-url="{{ $template['download_url'] }}"
                                                    data-file-name="{{ $template['display_name'] }}"
                                                    data-preview-url="{{ $template['preview_url'] }}">
                                                <i class="fas fa-eye me-2"></i>
                                                Xem trước
                                            </button>
                                            <button type="button"
                                                    class="btn btn-outline-info btn-action btn-print"
                                                    data-file-url="{{ $template['download_url'] }}"
                                                    data-file-name="{{ $template['display_name'] }}"
                                                    data-print-url="{{ $template['print_url'] }}">
                                                <i class="fas fa-print me-2"></i>
                                                In
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
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <h4 class="empty-title">Chưa có mẫu hợp đồng</h4>
                            <p class="empty-text text-muted mb-4">
                                Hiện tại chưa có file hợp đồng .docx nào trong thư mục.<br>
                                Vui lòng liên hệ quản trị viên để cập nhật mẫu hợp đồng.
                            </p>
                            <button class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>
                                Thêm mẫu hợp đồng
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab 2: Upload Template -->
        <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1 text-white">
                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                Tải lên mẫu hợp đồng mới
                            </h5>
                            <p class="text-white-50 mb-0 small">
                                Thêm mẫu hợp đồng .docx vào thư viện
                            </p>
                        </div>
                        <div class="header-actions">
                            <button class="btn btn-outline-light btn-sm" id="clearUploads" data-bs-toggle="tooltip" title="Xóa tất cả">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Upload Methods -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="upload-methods">
                                <div class="method-tabs">
                                    <button class="method-tab active" data-method="file">
                                        <i class="fas fa-file-upload"></i>
                                        <span>Tải lên file</span>
                                    </button>
                                    <button class="method-tab" data-method="url">
                                        <i class="fas fa-link"></i>
                                        <span>Từ liên kết</span>
                                    </button>
                                    <button class="method-tab" data-method="multiple">
                                        <i class="fas fa-layer-group"></i>
                                        <span>Nhiều file</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Method -->
                    <div class="upload-content" id="file-upload" style="display: block;">
                        <div class="upload-zone" id="dropZone">
                            <div class="upload-zone-content">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h4 class="upload-title">Kéo thả file vào đây</h4>
                                <p class="upload-subtitle">hoặc <button type="button" class="btn-link" id="browseFiles">duyệt file</button> để chọn</p>
                                <div class="upload-info">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Chỉ chấp nhận file .docx, tối đa 10MB
                                    </small>
                                </div>
                                <input type="file" id="fileInput" multiple accept=".docx" style="display: none;">
                            </div>
                        </div>

                        <!-- Upload Progress -->
                        <div class="upload-progress" id="uploadProgress" style="display: none;">
                            <h5 class="mb-3">Đang tải lên</h5>
                            <div id="uploadList"></div>
                        </div>

                        <!-- Upload Results -->
                        <div class="upload-results" id="uploadResults" style="display: none;">
                            <h5 class="mb-3">Kết quả tải lên</h5>
                            <div id="resultsList"></div>
                            <div class="mt-3">
                                <button class="btn btn-success" id="goToContracts">
                                    <i class="fas fa-eye me-2"></i>Xem mẫu đã tải lên
                                </button>
                                <button class="btn btn-outline-primary" id="uploadMore">
                                    <i class="fas fa-plus me-2"></i>Tải lên thêm
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- URL Upload Method -->
                    <div class="upload-content" id="url-upload" style="display: none;">
                        <div class="url-upload-form">
                            <div class="form-group mb-3">
                                <label for="fileUrl" class="form-label">Liên kết file .docx</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-link"></i>
                                    </span>
                                    <input type="url" class="form-control" id="fileUrl" placeholder="https://example.com/contract.docx">
                                    <button class="btn btn-primary" type="button" id="downloadFromUrl">
                                        <i class="fas fa-download me-2"></i>Tải về
                                    </button>
                                </div>
                                <div class="form-text">Nhập liên kết trực tiếp đến file .docx</div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="fileName" class="form-label">Tên file (tùy chọn)</label>
                                <input type="text" class="form-control" id="fileName" placeholder="Tên mẫu hợp đồng">
                                <div class="form-text">Để trống để sử dụng tên từ URL</div>
                            </div>

                            <div class="url-preview" id="urlPreview" style="display: none;">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Thông tin file</h6>
                                    <div id="urlFileInfo"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Multiple Files Upload Method -->
                    <div class="upload-content" id="multiple-upload" style="display: none;">
                        <div class="multiple-upload-area">
                            <div class="batch-upload-zone">
                                <div class="batch-upload-header">
                                    <h5><i class="fas fa-layer-group me-2"></i>Tải lên nhiều file cùng lúc</h5>
                                    <p class="text-muted">Chọn nhiều file .docx để tải lên hàng loạt</p>
                                </div>

                                <div class="batch-drop-zone" id="batchDropZone">
                                    <div class="batch-drop-content">
                                        <i class="fas fa-files"></i>
                                        <h4>Thả nhiều file vào đây</h4>
                                        <p>hoặc <button type="button" class="btn-link" id="browseBatchFiles">chọn nhiều file</button></p>
                                        <input type="file" id="batchFileInput" multiple accept=".docx" style="display: none;">
                                    </div>
                                </div>

                                <div class="batch-controls mt-3" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="overwriteExisting">
                                                <label class="form-check-label" for="overwriteExisting">
                                                    Ghi đè file trùng tên
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <button class="btn btn-primary" id="startBatchUpload">
                                                <i class="fas fa-upload me-2"></i>Bắt đầu tải lên
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="batch-queue" id="batchQueue" style="display: none;">
                                    <h6>Hàng đợi tải lên</h6>
                                    <div id="batchList"></div>
                                </div>
                            </div>
                        </div>
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
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Tổng giá trị
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($transactions->sum('TotalPrice'), 0, ',', '.') }}₫
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-info"></i>
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
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="refreshTransactions" data-bs-toggle="tooltip" title="Làm mới">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                                <i class="fas fa-plus me-1"></i>Thêm giao dịch
                            </button>
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
                                    <th class="border-0">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
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
                                    <td onclick="event.stopPropagation();">
                                        <div class="form-check">
                                            <input class="form-check-input transaction-checkbox" type="checkbox" value="{{ $transaction->TransactionID }}">
                                        </div>
                                    </td>
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
                                            @if($transaction->TranStatus !== 'Paid')
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                    title="Thanh toán"
                                                    onclick="processPayment({{ $transaction->TransactionID }})">
                                                <i class="fas fa-credit-card"></i>
                                            </button>
                                            @endif
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
                                                                            <td>{{ date('d/m/Y', strtotime($payment->DTrans_Date)) }}</td>
                                                                            <td>
                                                                                <span class="badge bg-secondary-subtle text-secondary">
                                                                                    {{ $payment->PaymentType ?? 'N/A' }}
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                @if($payment->DTrans_Status == 'Completed')
                                                                                    <span class="badge bg-success">Hoàn thành</span>
                                                                                @elseif($payment->DTrans_Status == 'Pending')
                                                                                    <span class="badge bg-warning">Chờ xử lý</span>
                                                                                @else
                                                                                    <span class="badge bg-secondary">{{ $payment->DTrans_Status }}</span>
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
                                                        @if($transaction->trans_contract && count($transaction->trans_contract) > 0)
                                                            <div class="mb-3">
                                                                <h6 class="text-info small mb-2">Hợp đồng đã ký:</h6>
                                                                @foreach($transaction->trans_contract as $contract)
                                                                <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <i class="fas fa-file-contract text-primary me-2"></i>
                                                                        <div>
                                                                            <div class="fw-semibold small">Hợp đồng #{{ $contract->ContractID }}</div>
                                                                            <small class="text-muted">
                                                                                Ký ngày: {{ date('d/m/Y', strtotime($contract->SignedDate)) }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                    <span class="badge bg-success">Đã ký</span>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        @endif

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

                                                        @if((!$transaction->trans_contract || count($transaction->trans_contract) == 0) && (!$transaction->document || count($transaction->document) == 0))
                                                            <div class="text-center text-muted py-3">
                                                                <i class="fas fa-folder-open fa-2x mb-2"></i>
                                                                <p class="mb-0">Chưa có tài liệu</p>
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
                                                Hiện tại chưa có giao dịch nào được tạo.<br>
                                                Nhấn "Thêm giao dịch" để bắt đầu.
                                            </p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                                                <i class="fas fa-plus me-2"></i>Thêm giao dịch đầu tiên
                                            </button>
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
                                        <td>${payment.DTrans_Date ? formatDate(payment.DTrans_Date) : 'N/A'}</td>
                                        <td>
                                            <span class="badge ${payment.DTrans_Status === 'Hoàn Thành' ? 'bg-success' : 'bg-warning'}">
                                                ${payment.DTrans_Status}
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
                        function populateDocuments(documents, contracts) {
                            const content = document.getElementById('documentsContent');
                            let html = '';

                            // Add contracts section
                            if (contracts && contracts.length > 0) {
                                html += `
                                    <div class="mb-4">
                                        <h6 class="text-info mb-3">
                                            <i class="fas fa-file-contract me-2"></i>Hợp đồng đã ký
                                        </h6>
                                `;

                                contracts.forEach(contract => {
                                    html += `
                                        <div class="d-flex align-items-center justify-content-between border rounded p-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-contract text-primary me-2"></i>
                                                <div>
                                                    <div class="fw-semibold">Hợp đồng #${contract.ContractID}</div>
                                                    <small class="text-muted">Ký ngày: ${formatDate(contract.SignedDate)}</small>
                                                </div>
                                            </div>
                                            <span class="badge bg-success">Đã ký</span>
                                        </div>
                                    `;
                                });

                                html += '</div>';
                            }

                            // Add documents section
                            if (documents && documents.length > 0) {
                                html += `
                                    <div class="mb-4">
                                        <h6 class="text-warning mb-3">
                                            <i class="fas fa-folder-open me-2"></i>Tài liệu đính kèm
                                        </h6>
                                `;

                                documents.forEach(doc => {
                                    html += `
                                        <div class="d-flex align-items-center justify-content-between border rounded p-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-alt text-info me-2"></i>
                                                <div>
                                                    <div class="fw-semibold">${doc.DocumentType || 'Tài liệu'}</div>
                                                    <small class="text-muted">Upload: ${formatDate(doc.UploadedDate)}</small>
                                                </div>
                                            </div>
                                            <button class="btn btn-outline-primary btn-sm" onclick="viewDocument('${doc.FilePath}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    `;
                                });

                                html += '</div>';
                            }

                            // If no documents or contracts
                            if ((!documents || documents.length === 0) && (!contracts || contracts.length === 0)) {
                                html = `
                                    <div class="text-center py-4">
                                        <div class="text-muted mb-3">
                                            <i class="fas fa-folder-open fa-3x"></i>
                                        </div>
                                        <h6 class="text-muted">Chưa có tài liệu</h6>
                                        <p class="text-muted">Giao dịch này chưa có tài liệu hoặc hợp đồng nào.</p>
                                    </div>
                                `;
                            }

                            content.innerHTML = html;
                        }

                        // Helper functions for formatting
                        function formatCurrency(amount) {
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
                            const statusClasses = {
                                'Pending': 'bg-warning',
                                'Paid': 'bg-primary',
                                'Completed': 'bg-success',
                                'Cancelled': 'bg-danger'
                            };
                            return statusClasses[status] || 'bg-secondary';
                        }

                        // Function to view document (placeholder)
                        function viewDocument(filePath) {
                            // This could open the document in a new window or modal
                            window.open(`/storage/${filePath}`, '_blank');
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
    background: linear-gradient(135deg, #4e73df 0%, #5a67d8 100%);
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

/* Print specific styles */
@media print {
    .modal-header,
    .preview-toolbar,
    .modal-footer {
        display: none !important;
    }

    .modal-content {
        box-shadow: none !important;
        border: none !important;
    }

    .preview-content {
        height: auto !important;
        overflow: visible !important;
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
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM Content Loaded - Initializing...');

    // Bỏ gọi hàm setupTransactionList() vì chưa định nghĩa
    // Chỉ gọi setupTransactionModal()
    setupTransactionModal();

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
</script>
@endif
@endsection
