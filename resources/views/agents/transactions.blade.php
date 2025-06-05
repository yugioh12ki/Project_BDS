@extends('_layout._layagent.app')

@section('title', 'Giao Dịch')

@section('transactions')
<div class="transactions-page">
    <div class="content-wrapper">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-actions">
                <div>
                    <h2>Danh sách giao dịch</h2>
                    <p class="text-muted mb-0">Quản lý và theo dõi tất cả giao dịch bất động sản</p>
                </div>                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTransactionModal">
                    + Tạo giao dịch
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-wrapper">
                <div class="search-input-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Tìm kiếm giao dịch theo ID, mã BDS..." class="search-box" id="searchTransaction">
                </div>
                <select class="filter-select" id="statusFilter">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Pending">Đang chờ xử lý</option>
                    <option value="Paid">Đã thanh toán</option>
                    <option value="Cancelled">Đã hủy</option>
                </select>
                <select class="filter-select" id="typeFilter">
                    <option value="">Tất cả loại</option>
                    <option value="Sale">Mua bán</option>
                    <option value="Rent">Cho thuê</option>
                </select>
                <div class="date-filter">
                    <input type="date" class="filter-select" id="dateFrom" placeholder="Từ ngày">
                    <input type="date" class="filter-select" id="dateTo" placeholder="Đến ngày">
                </div>
                <div class="filter-actions">
                    <button class="btn-clear-filters" id="clearFilters">
                        ✕ Xóa bộ lọc
                    </button>
                    <button class="btn-export-excel" id="exportExcel" onclick="exportTransactions()">
                        ↓ Xuất Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-container">
            <!-- Loading State -->
            <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                <div class="spinner"></div>
                <p>Đang tải dữ liệu...</p>
            </div>

            <table class="transactions-table" id="transactionsTable">
                <thead>
                    <tr>
                        <th class="sortable" data-column="TransactionID">
                            Mã giao dịch
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable" data-column="PropertyID">
                            Mã BDS
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th>Loại giao dịch</th>
                        <th class="sortable" data-column="TotalPrice">
                            Giá trị
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable" data-column="TransactionDate">
                            Ngày giao dịch
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable" data-column="TranStatus">
                            Trạng thái
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>                    @forelse($transactions as $transaction)
                    <tr>
                        <td class="transaction-id">{{ $transaction->TransactionID ?? 'N/A' }}</td>
                        <td class="property-code">{{ $transaction->PropertyID ?? 'N/A' }}</td>
                        <td>
                            <span class="type-badge {{ ($transaction->TransactionType ?? '') === 'Rent' ? 'type-rent' : 'type-sale' }}">
                                {{ ($transaction->TransactionType ?? '') === 'Rent' ? 'Cho thuê' : 'Mua bán' }}
                            </span>
                        </td>
                        <td class="transaction-value">{{ number_format($transaction->TotalPrice ?? 0, 0, ',', '.') }} ₫</td>
                        <td class="transaction-date">{{ $transaction->TransactionDate ? date('d/m/Y', strtotime($transaction->TransactionDate)) : 'N/A' }}</td>
                        <td>
                            <span class="status-badge 
                                @if(($transaction->TranStatus ?? '') === 'Paid') status-paid
                                @elseif(($transaction->TranStatus ?? '') === 'Pending') status-pending
                                @elseif(($transaction->TranStatus ?? '') === 'Cancelled') status-cancelled
                                @endif">
                                @if(($transaction->TranStatus ?? '') === 'Paid') Đã thanh toán
                                @elseif(($transaction->TranStatus ?? '') === 'Pending') Chờ xử lý
                                @elseif(($transaction->TranStatus ?? '') === 'Cancelled') Đã hủy
                                @else {{ $transaction->TranStatus ?? 'N/A' }}
                                @endif
                            </span>
                        </td>
                        <td class="action-buttons">
                            <a href="#" class="action-btn btn-view" title="Xem chi tiết" 
                               onclick="viewTransaction('{{ $transaction->TransactionID ?? '' }}')">
                                👁️ Chi tiết
                            </a>
                            <a href="#" class="action-btn btn-edit" title="Chỉnh sửa"
                               onclick="editTransaction('{{ $transaction->TransactionID ?? '' }}')">
                                ✏️ Sửa
                            </a>
                            <a href="#" class="action-btn btn-docs" title="Tài liệu"
                               onclick="viewDocuments('{{ $transaction->TransactionID ?? '' }}')">
                                📄 Tài liệu
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="7" class="no-results">
                            <i class="fas fa-inbox"></i>
                            <h4>Không có giao dịch nào</h4>
                            <p>Chưa có giao dịch nào được tìm thấy</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-container" id="paginationContainer">
                <div class="pagination-info">
                    <span id="paginationInfo">Hiển thị {{ isset($transactions) ? $transactions->count() : 0 }} giao dịch</span>
                </div>
                <div class="pagination">
                    <button class="pagination-btn" id="prevPage" disabled>
                        <i class="fas fa-chevron-left"></i>
                        Trước
                    </button>
                    <div class="pagination-numbers" id="paginationNumbers">
                        <button class="pagination-number active">1</button>
                    </div>
                    <button class="pagination-btn" id="nextPage" disabled>
                        Tiếp
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>    </div>
</div>

<!-- Modal Tạo Giao Dịch - 4 Bước -->
<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-labelledby="createTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="createTransactionModalLabel">
                    <i class="fas fa-handshake text-primary me-2"></i>
                    Tạo Giao Dịch Mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Progress Steps -->
            <div class="px-4 pt-2 pb-0">
                <div class="progress-steps">
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-label">Chọn Bất Động Sản</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-label">Tải Tài Liệu</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-label">Tạo Giao Dịch</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-circle">4</div>
                        <div class="step-label">Thanh Toán</div>
                    </div>
                    <div class="progress-line"></div>
                </div>
            </div>
            
            <form id="createTransactionForm" action="{{ route('agent.transactions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <!-- Bước 1: Tìm kiếm và chọn bất động sản -->
                    <div class="step-content" id="step-1">
                        <div class="step-container">
                            <h6 class="step-title">
                                <i class="fas fa-search text-primary me-2"></i>
                                Tìm kiếm và chọn bất động sản
                            </h6>
                            
                            <div class="property-search-container mb-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="propertySearchInput" 
                                           placeholder="Tìm kiếm bất động sản theo tên, địa chỉ...">
                                </div>
                            </div>
                            
                            <div class="property-list-container" id="propertyListContainer">
                                @forelse($properties ?? [] as $property)
                                <div class="property-card" data-property-id="{{ $property->PropertyID }}" 
                                     data-property-type="{{ $property->TypePro }}" 
                                     data-property-price="{{ $property->Price }}"
                                     data-property-title="{{ $property->Title }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="property-title mb-1">{{ $property->Title }}</h6>
                                            <p class="property-address text-muted mb-1">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $property->Address }}
                                            </p>
                                            <div class="property-meta">
                                                <span class="badge {{ $property->TypePro == 'Sale' ? 'bg-success' : 'bg-info' }}">
                                                    {{ $property->TypePro == 'Sale' ? 'Bán' : 'Thuê' }}
                                                </span>
                                                <span class="text-muted ms-2">ID: {{ $property->PropertyID }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="property-price mb-2">
                                                <strong class="text-primary">
                                                    {{ number_format($property->Price, 0, ',', '.') }}₫
                                                    @if($property->TypePro == 'Rent')<small>/tháng</small>@endif
                                                </strong>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm select-property-btn">
                                                <i class="fas fa-check me-1"></i>
                                                Chọn
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4">
                                    <i class="fas fa-home fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">Không có bất động sản nào</h6>
                                    <p class="text-muted">Vui lòng liên hệ quản trị viên để được phân công bất động sản</p>
                                </div>
                                @endforelse
                            </div>
                            
                            <div class="selected-property-info" id="selectedPropertyInfo" style="display: none;">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Đã chọn:</strong> <span id="selectedPropertyTitle"></span>
                                    <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="changeSelectedProperty()">
                                        Đổi bất động sản
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Hidden inputs for selected property -->
                            <input type="hidden" id="property_id" name="property_id">
                            <input type="hidden" id="transaction_type" name="transaction_type">
                        </div>
                    </div>

                    <!-- Bước 2: Tải tài liệu -->
                    <div class="step-content" id="step-2" style="display: none;">
                        <div class="step-container">
                            <h6 class="step-title">
                                <i class="fas fa-upload text-primary me-2"></i>
                                Tải lên tài liệu giao dịch
                            </h6>
                            
                            <div class="document-upload-area">
                                <div class="upload-zone" id="documentUploadZone">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                    </div>
                                    <h6 class="mt-3">Kéo thả tài liệu vào đây</h6>
                                    <p class="text-muted">hoặc <strong>nhấp để chọn file</strong></p>
                                    <p class="small text-muted">Hỗ trợ: PDF, DOC, DOCX, JPG, PNG (tối đa 10MB)</p>
                                    <input type="file" id="contract_documents" name="contract_documents[]" 
                                           class="d-none" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                
                                <div id="selectedFiles" class="selected-files mt-3"></div>
                                
                                <div class="upload-status mt-3" id="uploadStatus" style="display: none;">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <span id="uploadStatusText">Đang kiểm tra tệp...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bước 3: Tạo giao dịch -->
                    <div class="step-content" id="step-3" style="display: none;">
                        <div class="step-container">
                            <h6 class="step-title">
                                <i class="fas fa-file-contract text-primary me-2"></i>
                                Thông tin giao dịch
                            </h6>
                            
                            <div class="row g-3">
                                <!-- Chọn khách hàng -->
                                <div class="col-md-6">
                                    <label for="customer_id" class="form-label fw-medium">
                                        <i class="fas fa-user text-info me-1"></i>
                                        Khách hàng <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="customer_id" name="customer_id" required>
                                        <option value="">Chọn khách hàng...</option>
                                        @forelse($customers as $customer)
                                        <option value="{{ $customer->UserID ?? '' }}">
                                            {{ ($customer->Name ?? 'N/A') }} - {{ ($customer->Phone ?? 'N/A') }}
                                        </option>
                                        @empty
                                        <option value="" disabled>Không có khách hàng nào</option>
                                        @endforelse
                                    </select>
                                </div>

                                <!-- Ngày giao dịch -->
                                <div class="col-md-6">
                                    <label for="transaction_date" class="form-label fw-medium">
                                        <i class="fas fa-calendar-alt text-warning me-1"></i>
                                        Ngày giao dịch <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" class="form-control" id="transaction_date" 
                                           name="transaction_date" required>
                                </div>
                                
                                <!-- Rental specific fields -->
                                <div id="rentalFields" style="display: none;">
                                    <div class="col-md-4">
                                        <label for="rental_months" class="form-label fw-medium">
                                            <i class="fas fa-clock text-info me-1"></i>
                                            Số tháng thuê <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="rental_months" name="rental_months" 
                                               placeholder="Vd: 12" min="1" max="120">
                                        <div class="form-text">Thời gian hợp đồng thuê</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="payment_type" class="form-label fw-medium">
                                            <i class="fas fa-credit-card text-success me-1"></i>
                                            Hình thức thanh toán <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="payment_type" name="payment_type">
                                            <option value="">Chọn hình thức...</option>
                                            <option value="monthly">Thanh toán hàng tháng</option>
                                            <option value="quarterly">Thanh toán theo quý</option>
                                            <option value="yearly">Thanh toán theo năm</option>
                                            <option value="advance">Thanh toán trước</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="monthly_price" class="form-label fw-medium">
                                            <i class="fas fa-money-check text-primary me-1"></i>
                                            Giá thuê/tháng <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="monthly_price" name="monthly_price" 
                                                   placeholder="Giá thuê hàng tháng" min="0">
                                            <span class="input-group-text">₫</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Giá giao dịch -->
                                <div class="col-md-12">
                                    <label for="total_price" class="form-label fw-medium">
                                        <i class="fas fa-money-bill-wave text-success me-1"></i>
                                        Giá trị giao dịch <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="total_price" name="total_price" 
                                               placeholder="Giá trị giao dịch" required min="0" readonly>
                                        <span class="input-group-text">₫</span>
                                    </div>
                                    <div class="form-text" id="price-preview"></div>
                                </div>
                            </div>
                            
                            <!-- Ghi chú -->
                            <div class="mt-4">
                                <label for="notes" class="form-label fw-medium">
                                    <i class="fas fa-sticky-note text-secondary me-1"></i>
                                    Ghi chú
                                </label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Nhập ghi chú về giao dịch (không bắt buộc)..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Bước 4: Thanh toán -->
                    <div class="step-content" id="step-4" style="display: none;">
                        <div class="step-container">
                            <h6 class="step-title">
                                <i class="fas fa-credit-card text-primary me-2"></i>
                                Phương thức thanh toán lần đầu
                            </h6>
                            
                            <div class="transaction-summary mb-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Tóm tắt giao dịch</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Bất động sản:</strong> <span id="summaryPropertyTitle">-</span></p>
                                                <p class="mb-1"><strong>Khách hàng:</strong> <span id="summaryCustomer">-</span></p>
                                                <p class="mb-1"><strong>Loại giao dịch:</strong> <span id="summaryTransactionType">-</span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Ngày giao dịch:</strong> <span id="summaryDate">-</span></p>
                                                <p class="mb-1"><strong>Số tài liệu:</strong> <span id="summaryDocuments">0</span></p>
                                                <p class="mb-1"><strong>Tổng giá trị:</strong> <span id="summaryTotalPrice" class="text-primary fw-bold">-</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="payment-methods">
                                <h6 class="mb-3">Chọn phương thức thanh toán:</h6>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="payment-option" data-payment="cash">
                                            <div class="card h-100 border-2">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-money-bill-wave fa-3x text-success mb-3"></i>
                                                    <h6 class="card-title">Thanh toán tiền mặt</h6>
                                                    <p class="card-text text-muted">Thanh toán ngay bằng tiền mặt</p>
                                                    <button type="button" class="btn btn-success payment-method-btn" data-method="cash">
                                                        <i class="fas fa-check me-1"></i>
                                                        Chọn
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="payment-option" data-payment="bank">
                                            <div class="card h-100 border-2">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-university fa-3x text-primary mb-3"></i>
                                                    <h6 class="card-title">Chuyển khoản ngân hàng</h6>
                                                    <p class="card-text text-muted">Thanh toán qua chuyển khoản</p>
                                                    <button type="button" class="btn btn-primary payment-method-btn" data-method="bank">
                                                        <i class="fas fa-check me-1"></i>
                                                        Chọn
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" id="payment_method" name="payment_method">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" id="prevStepBtn" style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i>
                        Quay lại
                    </button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Hủy bỏ
                    </button>
                    <button type="button" class="btn btn-primary" id="nextStepBtn">
                        Tiếp tục
                        <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                    <button type="submit" class="btn btn-success" id="submitTransaction" style="display: none;">
                        <i class="fas fa-check me-1"></i>
                        Hoàn tất giao dịch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Styles cho Modal tạo giao dịch 4 bước */
.modal-xl {
    max-width: 1200px;
}

/* Progress Steps */
.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin: 20px 0;
    padding: 0 20px;
}

.progress-steps .step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
}

.progress-steps .step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.progress-steps .step.active .step-circle {
    background: #007bff;
    color: white;
}

.progress-steps .step.completed .step-circle {
    background: #28a745;
    color: white;
}

.progress-steps .step-label {
    font-size: 12px;
    text-align: center;
    color: #6c757d;
    font-weight: 500;
}

.progress-steps .step.active .step-label {
    color: #007bff;
    font-weight: 600;
}

.progress-steps .step.completed .step-label {
    color: #28a745;
    font-weight: 600;
}

.progress-steps .progress-line {
    position: absolute;
    top: 20px;
    left: 20px;
    right: 20px;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
}

.progress-steps .progress-line::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: #007bff;
    width: var(--progress, 0%);
    transition: width 0.3s ease;
}

/* Step Content */
.step-container {
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    background: #f8f9fa;
}

.step-title {
    color: #495057;
    margin-bottom: 20px;
    font-weight: 600;
    font-size: 18px;
}

/* Property Cards */
.property-card {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.property-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0,123,255,0.15);
}

.property-card.selected {
    border-color: #007bff;
    background: rgba(0,123,255,0.05);
    box-shadow: 0 4px 12px rgba(0,123,255,0.2);
}

.property-title {
    font-weight: 600;
    color: #495057;
}

.property-address {
    font-size: 14px;
}

.property-meta .badge {
    font-size: 11px;
}

.property-price {
    font-size: 18px;
}

/* Property Search */
.property-search-container .input-group-text {
    background: white;
    border-right: none;
}

.property-search-container .form-control {
    border-left: none;
}

.property-list-container {
    max-height: 400px;
    overflow-y: auto;
}

/* Document Upload */
.upload-zone {
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.upload-zone:hover {
    border-color: #007bff;
    background-color: rgba(13, 110, 253, 0.05);
}

.upload-zone.dragover {
    border-color: #007bff;
    background-color: rgba(13, 110, 253, 0.1);
}

.selected-files {
    max-height: 200px;
    overflow-y: auto;
}

.file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 8px;
    background: white;
}

.file-info {
    display: flex;
    align-items: center;
}

.file-icon {
    width: 32px;
    height: 32px;
    margin-right: 10px;
    color: #6c757d;
}

.remove-file {
    border: none;
    background: none;
    color: #dc3545;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.remove-file:hover {
    background-color: rgba(220, 53, 69, 0.1);
}

/* Payment Methods */
.payment-option .card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-option .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.payment-option.selected .card {
    border-color: #007bff !important;
    background: rgba(0,123,255,0.05);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.2);
}

/* Transaction Summary */
.transaction-summary .card {
    border: 1px solid #007bff;
}

/* Form Enhancements */
#price-preview {
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
}

/* Rental Fields */
#rentalFields {
    width: 100%;
}

/* Button States */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Step Content Animation */
.step-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Selected Property Info */
.selected-property-info .alert {
    border-left: 4px solid #28a745;
}

/* Upload Status */
.upload-status .alert {
    margin-bottom: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .progress-steps {
        padding: 0 10px;
    }
    
    .progress-steps .step-label {
        font-size: 10px;
    }
    
    .progress-steps .step-circle {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }
    
    .property-card {
        padding: 15px;
    }
    
    .modal-xl {
        max-width: 95%;
    }
}
</style>

<script>
// Export function
function exportTransactions() {
    window.location.href = '{{ route("agent.transactions.export") }}';
}

// View transaction details
function viewTransaction(transactionId) {
    fetch(`{{ route("agent.transactions.show", ["id" => "__ID__"]) }}`.replace('__ID__', transactionId))
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Lỗi: ' + data.error);
                return;
            }
            
            // Show transaction details in a modal or alert for now
            const details = `
Mã giao dịch: ${data.transaction.TransactionID}
Giá trị: ${data.formatted_price} ₫
Ngày giao dịch: ${data.formatted_date}
Loại: ${data.transaction.TransactionType}
Trạng thái: ${data.transaction.TranStatus}
            `;
            alert(details);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải chi tiết giao dịch');
        });
}

// Edit transaction
function editTransaction(transactionId) {
    alert('Chỉnh sửa giao dịch: ' + transactionId);
}

// View documents
function viewDocuments(transactionId) {
    alert('Tài liệu giao dịch: ' + transactionId);
}

// Basic filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTransaction');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');

    // Search functionality
    function performSearch() {
        const searchTerm = searchInput.value.trim();
        const status = statusFilter.value;
        const type = typeFilter.value;
        
        const params = new URLSearchParams();
        if (searchTerm) params.append('search', searchTerm);
        if (status) params.append('status', status);
        if (type) params.append('type', type);
        
        // Reload page with search parameters
        const baseUrl = '{{ route("agent.transactions") }}';
        const searchUrl = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
        window.location.href = searchUrl;
    }

    // Add event listeners
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    statusFilter.addEventListener('change', performSearch);
    typeFilter.addEventListener('change', performSearch);    // Clear filters
    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        typeFilter.value = '';
        window.location.href = '{{ route("agent.transactions") }}';
    });    // === 4-STEP TRANSACTION MODAL LOGIC ===
    
    let currentStep = 1;
    let selectedProperty = null;
    let selectedFiles = [];
    
    // Initialize modal
    const modal = document.getElementById('createTransactionModal');
    const nextBtn = document.getElementById('nextStepBtn');
    const prevBtn = document.getElementById('prevStepBtn');
    const submitBtn = document.getElementById('submitTransaction');
    
    // Step navigation
    function showStep(stepNumber) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(step => {
            step.style.display = 'none';
        });
        
        // Show current step
        document.getElementById(`step-${stepNumber}`).style.display = 'block';
        
        // Update progress indicators
        document.querySelectorAll('.progress-steps .step').forEach((step, index) => {
            const stepNum = index + 1;
            step.classList.remove('active', 'completed');
            
            if (stepNum < stepNumber) {
                step.classList.add('completed');
            } else if (stepNum === stepNumber) {
                step.classList.add('active');
            }
        });
        
        // Update progress line
        const progressLine = document.querySelector('.progress-line::after') || document.querySelector('.progress-line');
        if (progressLine) {
            const progressPercent = ((stepNumber - 1) / 3) * 100;
            progressLine.style.setProperty('--progress', `${progressPercent}%`);
        }
        
        // Update buttons
        prevBtn.style.display = stepNumber > 1 ? 'inline-block' : 'none';
        nextBtn.style.display = stepNumber < 4 ? 'inline-block' : 'none';
        submitBtn.style.display = stepNumber === 4 ? 'inline-block' : 'none';
        
        // Update button text and validation
        updateStepButtons(stepNumber);
    }
    
    function updateStepButtons(stepNumber) {
        const nextBtn = document.getElementById('nextStepBtn');
        
        switch(stepNumber) {
            case 1:
                nextBtn.textContent = selectedProperty ? 'Tiếp tục' : 'Chọn bất động sản';
                nextBtn.innerHTML = (selectedProperty ? 'Tiếp tục' : 'Chọn bất động sản') + ' <i class="fas fa-arrow-right ms-1"></i>';
                nextBtn.disabled = !selectedProperty;
                break;
            case 2:
                nextBtn.textContent = 'Tiếp tục';
                nextBtn.innerHTML = 'Tiếp tục <i class="fas fa-arrow-right ms-1"></i>';
                nextBtn.disabled = selectedFiles.length === 0;
                break;
            case 3:
                nextBtn.textContent = 'Xem thanh toán';
                nextBtn.innerHTML = 'Xem thanh toán <i class="fas fa-arrow-right ms-1"></i>';
                nextBtn.disabled = !validateStep3();
                break;
            case 4:
                // Final step - only submit button visible
                break;
        }
    }
    
    // Step 1: Property Selection
    function initializePropertySelection() {
        const propertyCards = document.querySelectorAll('.property-card');
        const searchInput = document.getElementById('propertySearchInput');
        const selectedPropertyInfo = document.getElementById('selectedPropertyInfo');
        
        // Property search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            propertyCards.forEach(card => {
                const title = card.querySelector('.property-title').textContent.toLowerCase();
                const address = card.querySelector('.property-address').textContent.toLowerCase();
                const shouldShow = title.includes(searchTerm) || address.includes(searchTerm);
                card.style.display = shouldShow ? 'block' : 'none';
            });
        });
        
        // Property selection
        propertyCards.forEach(card => {
            const selectBtn = card.querySelector('.select-property-btn');
            selectBtn.addEventListener('click', function() {
                // Remove previous selection
                propertyCards.forEach(c => c.classList.remove('selected'));
                
                // Select current property
                card.classList.add('selected');
                
                // Store selected property data
                selectedProperty = {
                    id: card.dataset.propertyId,
                    type: card.dataset.propertyType,
                    price: card.dataset.propertyPrice,
                    title: card.dataset.propertyTitle
                };
                
                // Update hidden inputs
                document.getElementById('property_id').value = selectedProperty.id;
                document.getElementById('transaction_type').value = selectedProperty.type;
                
                // Show selected property info
                document.getElementById('selectedPropertyTitle').textContent = selectedProperty.title;
                selectedPropertyInfo.style.display = 'block';
                
                // Hide property list
                document.getElementById('propertyListContainer').style.display = 'none';
                
                // Update button state
                updateStepButtons(1);
            });
        });
    }
    
    // Change selected property
    window.changeSelectedProperty = function() {
        selectedProperty = null;
        document.getElementById('selectedPropertyInfo').style.display = 'none';
        document.getElementById('propertyListContainer').style.display = 'block';
        document.querySelectorAll('.property-card').forEach(card => {
            card.classList.remove('selected');
        });
        updateStepButtons(1);
    };
    
    // Step 2: Document Upload
    function initializeDocumentUpload() {
        const uploadZone = document.getElementById('documentUploadZone');
        const fileInput = document.getElementById('contract_documents');
        const selectedFilesContainer = document.getElementById('selectedFiles');
        const uploadStatus = document.getElementById('uploadStatus');
        const uploadStatusText = document.getElementById('uploadStatusText');
        
        // Click to select files
        uploadZone.addEventListener('click', function(e) {
            if (e.target !== fileInput) {
                fileInput.click();
            }
        });
        
        // Drag and drop handlers
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        uploadZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files);
            handleFileSelection(files);
        });
        
        fileInput.addEventListener('change', function() {
            const files = Array.from(this.files);
            handleFileSelection(files);
        });
        
        function handleFileSelection(files) {
            uploadStatus.style.display = 'block';
            uploadStatusText.textContent = 'Đang kiểm tra tệp...';
            
            setTimeout(() => {
                files.forEach(file => {
                    // Validate file type
                    const allowedTypes = ['application/pdf', 'application/msword', 
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'image/jpeg', 'image/jpg', 'image/png'];
                    
                    if (!allowedTypes.includes(file.type)) {
                        alert(`File ${file.name} không được hỗ trợ. Vui lòng chọn file PDF, DOC, DOCX, JPG hoặc PNG.`);
                        return;
                    }
                    
                    // Validate file size (10MB max)
                    if (file.size > 10 * 1024 * 1024) {
                        alert(`File ${file.name} quá lớn. Vui lòng chọn file nhỏ hơn 10MB.`);
                        return;
                    }
                    
                    // Check if file already selected
                    if (selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                        alert(`File ${file.name} đã được chọn.`);
                        return;
                    }
                    
                    selectedFiles.push(file);
                    displaySelectedFile(file);
                });
                
                updateFileInput();
                uploadStatus.style.display = 'none';
                updateStepButtons(2);
            }, 500);
        }
        
        function displaySelectedFile(file) {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <div class="file-info">
                    <i class="fas fa-file-${getFileIcon(file.type)} file-icon"></i>
                    <div>
                        <div class="fw-medium">${file.name}</div>
                        <small class="text-muted">${formatFileSize(file.size)}</small>
                    </div>
                </div>
                <button type="button" class="remove-file" onclick="removeFile('${file.name}', ${file.size})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            selectedFilesContainer.appendChild(fileItem);
        }
        
        function getFileIcon(mimeType) {
            if (mimeType.includes('pdf')) return 'pdf';
            if (mimeType.includes('word')) return 'word';
            if (mimeType.includes('image')) return 'image';
            return 'alt';
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        function updateFileInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
        }
    }
    
    // Step 3: Transaction Creation
    function initializeTransactionCreation() {
        const totalPriceInput = document.getElementById('total_price');
        const pricePreview = document.getElementById('price-preview');
        const rentalFields = document.getElementById('rentalFields');
        const monthlyPriceInput = document.getElementById('monthly_price');
        const rentalMonthsInput = document.getElementById('rental_months');
        const paymentTypeSelect = document.getElementById('payment_type');
        
        // Set default transaction date
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('transaction_date').value = now.toISOString().slice(0, 16);
        
        // Handle property type changes
        function updateTransactionFields() {
            if (!selectedProperty) return;
            
            if (selectedProperty.type === 'Rent') {
                rentalFields.style.display = 'block';
                monthlyPriceInput.value = selectedProperty.price;
                monthlyPriceInput.required = true;
                rentalMonthsInput.required = true;
                paymentTypeSelect.required = true;
                
                calculateRentalPrice();
            } else {
                rentalFields.style.display = 'none';
                totalPriceInput.value = selectedProperty.price;
                updatePricePreview(selectedProperty.price);
                
                monthlyPriceInput.required = false;
                rentalMonthsInput.required = false;
                paymentTypeSelect.required = false;
            }
        }
        
        function calculateRentalPrice() {
            const monthlyPrice = parseFloat(monthlyPriceInput.value) || 0;
            const rentalMonths = parseInt(rentalMonthsInput.value) || 1;
            const paymentType = paymentTypeSelect.value;
            
            let totalPayment = monthlyPrice;
            
            if (paymentType === 'quarterly') {
                totalPayment = monthlyPrice * 3;
            } else if (paymentType === 'yearly') {
                totalPayment = monthlyPrice * 12;
            } else if (paymentType === 'advance' && rentalMonths > 0) {
                totalPayment = monthlyPrice * rentalMonths;
            }
            
            totalPriceInput.value = totalPayment;
            updatePricePreview(totalPayment);
        }
        
        function updatePricePreview(price) {
            if (price && !isNaN(price)) {
                const formatted = new Intl.NumberFormat('vi-VN').format(price);
                pricePreview.textContent = `${formatted} VND`;
            } else {
                pricePreview.textContent = '';
            }
        }
        
        // Event listeners
        monthlyPriceInput.addEventListener('input', calculateRentalPrice);
        rentalMonthsInput.addEventListener('input', calculateRentalPrice);
        paymentTypeSelect.addEventListener('change', calculateRentalPrice);
        
        // Customer and date validation
        document.getElementById('customer_id').addEventListener('change', () => updateStepButtons(3));
        document.getElementById('transaction_date').addEventListener('change', () => updateStepButtons(3));
        
        // Initialize fields when step 3 is shown
        updateTransactionFields();
    }
    
    function validateStep3() {
        const customerId = document.getElementById('customer_id').value;
        const transactionDate = document.getElementById('transaction_date').value;
        const totalPrice = document.getElementById('total_price').value;
        
        let isValid = customerId && transactionDate && totalPrice;
        
        // Additional validation for rental properties
        if (selectedProperty && selectedProperty.type === 'Rent') {
            const rentalMonths = document.getElementById('rental_months').value;
            const paymentType = document.getElementById('payment_type').value;
            const monthlyPrice = document.getElementById('monthly_price').value;
            
            isValid = isValid && rentalMonths && paymentType && monthlyPrice;
        }
        
        return isValid;
    }
    
    // Step 4: Payment
    function initializePaymentStep() {
        const paymentOptions = document.querySelectorAll('.payment-option');
        const paymentMethodBtns = document.querySelectorAll('.payment-method-btn');
        const paymentMethodInput = document.getElementById('payment_method');
        
        // Update transaction summary
        function updateTransactionSummary() {
            if (!selectedProperty) return;
            
            document.getElementById('summaryPropertyTitle').textContent = selectedProperty.title;
            
            const customerSelect = document.getElementById('customer_id');
            const customerText = customerSelect.options[customerSelect.selectedIndex]?.text || '-';
            document.getElementById('summaryCustomer').textContent = customerText;
            
            document.getElementById('summaryTransactionType').textContent = selectedProperty.type === 'Rent' ? 'Thuê' : 'Bán';
            
            const transactionDate = document.getElementById('transaction_date').value;
            if (transactionDate) {
                const date = new Date(transactionDate);
                document.getElementById('summaryDate').textContent = date.toLocaleString('vi-VN');
            }
            
            document.getElementById('summaryDocuments').textContent = selectedFiles.length;
            
            const totalPrice = document.getElementById('total_price').value;
            if (totalPrice) {
                const formatted = new Intl.NumberFormat('vi-VN').format(totalPrice);
                document.getElementById('summaryTotalPrice').textContent = `${formatted}₫`;
            }
        }
        
        // Payment method selection
        paymentMethodBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const method = this.dataset.method;
                
                // Remove previous selection
                paymentOptions.forEach(option => option.classList.remove('selected'));
                
                // Select current option
                this.closest('.payment-option').classList.add('selected');
                
                // Update hidden input
                paymentMethodInput.value = method;
                
                // Enable submit button
                submitBtn.disabled = false;
                
                // Update submit button text based on payment method
                if (method === 'cash') {
                    submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Hoàn tất thanh toán';
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-university me-1"></i>Xác nhận chuyển khoản';
                }
            });
        });
        
        updateTransactionSummary();
        submitBtn.disabled = true;
    }    
    // Navigation Event Listeners
    nextBtn.addEventListener('click', function() {
        if (currentStep < 4) {
            currentStep++;
            showStep(currentStep);
            
            // Initialize step-specific functionality
            if (currentStep === 3) {
                initializeTransactionCreation();
            } else if (currentStep === 4) {
                initializePaymentStep();
            }
        }
    });
    
    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    // Modal event listeners
    modal.addEventListener('show.bs.modal', function() {
        // Reset to step 1
        currentStep = 1;
        selectedProperty = null;
        selectedFiles = [];
        
        // Reset form
        document.getElementById('createTransactionForm').reset();
        document.getElementById('selectedFiles').innerHTML = '';
        document.getElementById('selectedPropertyInfo').style.display = 'none';
        document.getElementById('propertyListContainer').style.display = 'block';
        
        // Reset property selection
        document.querySelectorAll('.property-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Reset payment selection
        document.querySelectorAll('.payment-option').forEach(option => {
            option.classList.remove('selected');
        });
        
        // Show initial step
        showStep(1);
        
        // Initialize step 1
        initializePropertySelection();
        initializeDocumentUpload();
    });

    // Document upload functions
    function handleFileSelection(files) {
        const uploadStatus = document.getElementById('uploadStatus');
        const uploadStatusText = document.getElementById('uploadStatusText');
        
        uploadStatus.style.display = 'block';
        uploadStatusText.textContent = 'Đang kiểm tra tệp...';
        
        setTimeout(() => {
            files.forEach(file => {
                // Validate file type
                const allowedTypes = ['application/pdf', 'application/msword', 
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'image/jpeg', 'image/jpg', 'image/png'];
                
                if (!allowedTypes.includes(file.type)) {
                    alert(`File ${file.name} không được hỗ trợ. Vui lòng chọn file PDF, DOC, DOCX, JPG hoặc PNG.`);
                    return;
                }
                
                // Validate file size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    alert(`File ${file.name} quá lớn. Vui lòng chọn file nhỏ hơn 10MB.`);
                    return;
                }
                
                // Check if file already selected
                if (selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                    alert(`File ${file.name} đã được chọn.`);
                    return;
                }
                
                selectedFiles.push(file);
                displaySelectedFile(file);
            });
            
            updateFileInput();
            uploadStatus.style.display = 'none';
            updateStepButtons(2);
        }, 500);
    }
    
    function displaySelectedFile(file) {
        const selectedFilesContainer = document.getElementById('selectedFiles');
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.innerHTML = `
            <div class="file-info">
                <i class="fas fa-file-${getFileIcon(file.type)} file-icon"></i>
                <div>
                    <div class="fw-medium">${file.name}</div>
                    <small class="text-muted">${formatFileSize(file.size)}</small>
                </div>
            </div>
            <button type="button" class="remove-file" onclick="removeFile('${file.name}', ${file.size})">
                <i class="fas fa-times"></i>
            </button>
        `;
        selectedFilesContainer.appendChild(fileItem);
    }
    
    function getFileIcon(mimeType) {
        if (mimeType.includes('pdf')) return 'pdf';
        if (mimeType.includes('word')) return 'word';
        if (mimeType.includes('image')) return 'image';
        return 'alt';
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function updateFileInput() {
        const fileInput = document.getElementById('contract_documents');
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    // Global function for removing files
    window.removeFile = function(fileName, fileSize) {
        selectedFiles = selectedFiles.filter(f => !(f.name === fileName && f.size === fileSize));
        
        // Remove from display
        const fileItems = document.querySelectorAll('.file-item');
        fileItems.forEach(item => {
            if (item.querySelector('.fw-medium').textContent === fileName) {
                item.remove();
            }
        });
        
        updateFileInput();
        updateStepButtons(2);
    };

    // Form submission with payment method handling
    document.getElementById('createTransactionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const paymentMethod = document.getElementById('payment_method').value;
        const submitBtn = document.getElementById('submitTransaction');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang xử lý...';
        submitBtn.disabled = true;
        
        // Create FormData for file uploads
        const formData = new FormData(this);
        
        // Add additional data for the 4-step workflow
        formData.append('transaction_workflow', '4-step');
        if (selectedProperty) {
            formData.append('property_title', selectedProperty.title);
        }
        
        // Submit transaction
        fetch('{{ route("agent.transactions.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Handle different payment methods
                if (paymentMethod === 'cash') {
                    // Cash payment - show success and redirect
                    alert('✅ Giao dịch đã được tạo thành công!\n💰 Thanh toán bằng tiền mặt đã được xác nhận.');
                    
                    // Close modal and redirect to transactions page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createTransactionModal'));
                    modal.hide();
                    window.location.href = '{{ route("agent.transactions") }}';
                    
                } else if (paymentMethod === 'bank') {
                    // Bank transfer - show development message
                    alert('🏗️ Tính năng chuyển khoản ngân hàng đang được phát triển.\n\n' +
                          '📋 Giao dịch đã được tạo với trạng thái "Chờ thanh toán".\n' +
                          '🔄 Vui lòng liên hệ quản trị viên để hoàn tất thanh toán.');
                    
                    // Close modal and reload
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createTransactionModal'));
                    modal.hide();
                    window.location.reload();
                }
            } else {
                // Handle errors
                if (data.errors) {
                    let errorMessages = [];
                    Object.values(data.errors).forEach(fieldErrors => {
                        fieldErrors.forEach(error => errorMessages.push(error));
                    });
                    alert('❌ Lỗi validation:\n\n' + errorMessages.join('\n'));
                } else {
                    alert('❌ Lỗi: ' + (data.message || 'Có lỗi xảy ra khi tạo giao dịch'));
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Có lỗi xảy ra khi tạo giao dịch. Vui lòng thử lại.');
        })
        .finally(() => {
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});

// Global function for removing files (already defined above)
// Removed duplicate declaration
</script>
@endsection