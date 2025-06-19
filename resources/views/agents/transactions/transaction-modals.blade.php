<!-- Modal Xem Chi Tiết Giao Dịch -->
<div class="modal fade" id="viewTransactionModal" tabindex="-1" aria-labelledby="viewTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header aqua-theme">
                <h5 class="modal-title" id="viewTransactionModalLabel">
                    <i class="fas fa-eye me-2"></i>Chi tiết giao dịch
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="transaction-details">
                    <!-- Loading State -->
                    <div class="text-center py-4" id="viewModalLoading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Đang tải thông tin...</p>
                    </div>

                    <!-- Content -->
                    <div id="viewModalContent" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="detail-label">ID Giao dịch:</label>
                                    <span class="detail-value" id="viewTransactionId">-</span>
                                </div>
                                <div class="detail-group">
                                    <label class="detail-label">Loại giao dịch:</label>
                                    <span class="detail-value" id="viewTransactionType">-</span>
                                </div>
                                <div class="detail-group">
                                    <label class="detail-label">Ngày giao dịch:</label>
                                    <span class="detail-value" id="viewTransactionDate">-</span>
                                </div>
                                <div class="detail-group">
                                    <label class="detail-label">Trạng thái:</label>
                                    <span class="detail-value" id="viewTransactionStatus">-</span>
                                </div>
                                <div class="detail-group">
                                    <label class="detail-label">Tổng giá trị:</label>
                                    <span class="detail-value" id="viewTransactionValue">-</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <label class="detail-label">Mã bất động sản:</label>
                                    <span class="detail-value" id="viewPropertyId">-</span>
                                </div>
                                <div class="detail-group">
                                    <label class="detail-label">Tiêu đề BĐS:</label>
                                    <span class="detail-value" id="viewPropertyTitle">-</span>
                                </div>
                                <div class="detail-group">
                                    <label class="detail-label">Khách hàng:</label>
                                    <span class="detail-value" id="viewCustomerName">-</span>
                                </div>
                                <div class="detail-group">
                                    <label class="detail-label">Chủ sở hữu:</label>
                                    <span class="detail-value" id="viewOwnerName">-</span>
                                </div>
                                <div class="detail-group">
                                    <label class="detail-label">Môi giới phụ trách:</label>
                                    <span class="detail-value" id="viewAgentName">-</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">Chi tiết thanh toán:</h6>
                                <div class="payment-details">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Lần thanh toán</th>
                                                    <th>Số tiền</th>
                                                    <th>Số tháng thuê</th>
                                                    <th>Ngày thanh toán</th>
                                                    <th>Phương thức</th>
                                                    <th>Trạng thái</th>
                                                </tr>
                                            </thead>
                                            <tbody id="paymentDetailsTable">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">
                                                        <div class="spinner-border spinner-border-sm me-2"></div>
                                                        Đang tải thông tin thanh toán...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="mb-3">Thông tin hoa hồng:</h6>
                                <div class="commission-info" id="commissionInfo">
                                    <div class="spinner-border spinner-border-sm me-2"></div>
                                    <span class="text-muted">Đang tải thông tin hoa hồng...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="openEditTransactionModal()">
                    <i class="fas fa-edit me-1"></i>Chỉnh sửa
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chỉnh Sửa Giao Dịch -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header aqua-theme">
                <h5 class="modal-title" id="editTransactionModalLabel">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa giao dịch
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTransactionForm">
                    <input type="hidden" id="editTransactionId" name="transaction_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Loại giao dịch <span class="text-danger">*</span></label>
                                <select class="form-select" id="editTransactionType" name="transaction_type" required disabled>
                                    <option value="">Chọn loại giao dịch</option>
                                    <option value="Sale">Mua bán</option>
                                    <option value="Rent">Cho thuê</option>
                                </select>
                                <small class="form-text text-muted">Loại giao dịch không thể thay đổi</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                <select class="form-select" id="editTransactionStatus" name="status" required>
                                    <option value="">Chọn trạng thái</option>
                                    <option value="Pending">Chờ xử lý</option>
                                    <option value="Paid">Đã thanh toán</option>
                                    <option value="Cancelled">Đã hủy</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Tổng giá trị <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editTransactionValue" name="total_price"
                                       placeholder="VD: 5,000,000,000" required readonly>
                                <small class="form-text text-muted">Được tính tự động từ chi tiết thanh toán</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Ngày giao dịch</label>
                                <input type="datetime-local" class="form-control" id="editTransactionDate" name="transaction_date" readonly>
                                <small class="form-text text-muted">Được cập nhật tự động</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Mã bất động sản</label>
                                <input type="text" class="form-control" id="editPropertyId" name="property_id" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Khách hàng</label>
                                <input type="text" class="form-control" id="editCustomerName" name="customer_name" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Ghi chú cập nhật</label>
                        <textarea class="form-control" id="editTransactionNotes" name="notes" rows="3"
                                  placeholder="Nhập lý do thay đổi trạng thái..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveTransactionChanges()">
                    <i class="fas fa-save me-1"></i>Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quản Lý Tài Liệu -->
<div class="modal fade" id="documentsModal" tabindex="-1" aria-labelledby="documentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header aqua-theme">
                <h5 class="modal-title" id="documentsModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Quản lý tài liệu giao dịch
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Upload Section -->
                    <div class="col-md-4">
                        <div class="upload-section">
                            <h6 class="mb-3">Tải lên tài liệu mới</h6>
                            <div class="upload-area" id="documentUploadArea">
                                <div class="upload-content">
                                    <i class="fas fa-cloud-upload-alt mb-2"></i>
                                    <p class="mb-2">Kéo thả file hoặc click để chọn</p>
                                    <small class="text-muted">Hỗ trợ: PDF, DOC, DOCX, JPG, PNG (Max: 10MB)</small>
                                </div>
                                <input type="file" id="documentFileInput" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display: none;">
                            </div>

                            <div class="form-group mt-3">
                                <label class="form-label">Loại tài liệu</label>
                                <select class="form-select" id="documentType">
                                    <option value="">Chọn loại tài liệu</option>
                                    <option value="PDF">Tài liệu PDF</option>
                                    <option value="DOCX">Tài liệu Word</option>
                                    <option value="JPG">Hình ảnh JPG</option>
                                    <option value="PNG">Hình ảnh PNG</option>
                                    <option value="XLSX">Bảng tính Excel</option>
                                    <option value="OTHER">Khác</option>
                                </select>
                                <small class="form-text text-muted">Chọn loại file phù hợp với tài liệu</small>
                            </div>

                            <button type="button" class="btn btn-primary w-100 mt-3" onclick="uploadDocuments()">
                                <i class="fas fa-upload me-1"></i>Tải lên
                            </button>
                        </div>
                    </div>

                    <!-- Documents List -->
                    <div class="col-md-8">
                        <div class="documents-list">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Danh sách tài liệu</h6>
                                <div class="document-filters">
                                    <select class="form-select form-select-sm" id="documentFilter">
                                        <option value="">Tất cả loại</option>
                                        <option value="PDF">PDF</option>
                                        <option value="DOCX">Word</option>
                                        <option value="JPG">JPG</option>
                                        <option value="PNG">PNG</option>
                                        <option value="XLSX">Excel</option>
                                        <option value="OTHER">Khác</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Documents Table -->
                            <div class="documents-table-container">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tên file</th>
                                            <th>Loại</th>
                                            <th>Đường dẫn</th>
                                            <th>Ngày tải lên</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="documentsTableBody">
                                        <!-- Loading State -->
                                        <tr id="documentsLoading">
                                            <td colspan="5" class="text-center py-3">
                                                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                                Đang tải danh sách tài liệu...
                                            </td>
                                        </tr>
                                        <!-- No Documents State -->
                                        <tr id="noDocuments" style="display: none;">
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="fas fa-folder-open mb-2"></i>
                                                <br>Chưa có tài liệu nào
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-outline-primary" onclick="downloadAllDocuments()">
                    <i class="fas fa-download me-1"></i>Tải tất cả
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal-dialog {
    max-width: 90%;
}

.modal-xl {
    max-width: 95%;
}

.modal-lg {
    max-width: 80%;
}

.detail-group {
    margin-bottom: 1rem;
    padding: 0.5rem;
    background-color: #f8f9fa;
    border-radius: 0.375rem;
    border-left: 3px solid #007bff;
}

.detail-label {
    font-weight: 600;
    color: #495057;
    display: block;
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

.detail-value {
    color: #212529;
    font-size: 0.95rem;
    font-weight: 500;
}

.detail-notes {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border-left: 4px solid #007bff;
    margin-top: 1rem;
}

/* Upload Area Styles - Disabled hover effects */
.upload-section {
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
}

.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    /* Removed transition and hover effects */
    background-color: #ffffff;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Removed hover effects for upload area */
.upload-area:hover {
    /* No hover effects */
}

.upload-area.dragover {
    border-color: #28a745;
    background-color: #d4edda;
    transform: scale(1.02);
    box-shadow: 0 6px 12px rgba(40,167,69,0.2);
}

.upload-content {
    text-align: center;
}

.upload-content i {
    font-size: 2.5rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

/* Removed hover effects for upload content */
.upload-area:hover .upload-content i {
    /* No hover effects */
}

.upload-area.dragover .upload-content i {
    color: #28a745;
}

/* Documents List Section */
.documents-list {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem;
}

/* Documents Table */
.documents-table-container {
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background-color: #ffffff;
}

.documents-table-container table {
    margin-bottom: 0;
    font-size: 0.875rem;
}

.documents-table-container thead th {
    position: sticky;
    top: 0;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    z-index: 10;
    font-weight: 600;
    color: #495057;
    padding: 0.75rem 0.5rem;
}

.documents-table-container tbody td {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
}

/* Disabled hover effects for table rows */
.documents-table-container tbody tr:hover {
    /* No hover effects */
}

/* Document Actions - Disabled hover effects, keep download button */
.doc-action-btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    margin: 0 0.125rem;
    border-radius: 0.25rem;
    text-decoration: none;
    display: inline-block;
    /* Removed transition and hover effects */
    border: 1px solid transparent;
}

/* Disabled hover effects for action buttons */
.doc-action-btn:hover {
    text-decoration: none;
    /* No hover effects */
}

/* Keep only download button styling */
.btn-download-doc {
    background-color: #e8f5e8;
    color: #2e7d32;
    border-color: #c8e6c9;
}

.btn-download-doc:hover {
    background-color: #c8e6c9;
    color: #1b5e20;
    border-color: #a5d6a7;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Removed view and delete button styling */

/* Status Badges in Modals */
.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-paid {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Form Styling */
.form-group .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 0.375rem;
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

/* Modal Header Styling */
.modal-header {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border-bottom: none;
    border-radius: 0.5rem 0.5rem 0 0;
}

.modal-header .modal-title {
    font-weight: 600;
    display: flex;
    align-items: center;
}

.modal-header .btn-close {
    filter: invert(1);
    opacity: 0.8;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

/* Modal Footer */
.modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
    border-radius: 0 0 0.5rem 0.5rem;
}

/* Filter Section in Documents Modal */
.document-filters .form-select {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    min-width: 150px;
}

/* Loading States */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Toast Notifications */
.toast-container {
    z-index: 9999;
}

.toast {
    min-width: 300px;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

/* Animation for modals */
.modal.fade .modal-dialog {
    transform: translateY(-50px);
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: translateY(0);
}

/* Additional styling for enhanced modals */
.payment-details .table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding: 0.75rem 0.5rem;
    font-size: 0.875rem;
}

.payment-details .table td {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
}

.commission-info {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-top: 1rem;
}

.commission-info .row > div {
    margin-bottom: 1rem;
}

.commission-info strong {
    color: #495057;
    font-size: 0.875rem;
    display: block;
    margin-bottom: 0.25rem;
}

.commission-info .text-primary {
    font-size: 1.1rem;
    font-weight: 600;
}

.commission-info .text-success {
    font-size: 1.1rem;
    font-weight: 600;
}

.commission-info .text-warning {
    font-size: 1rem;
    font-weight: 600;
}

.commission-info .text-danger {
    font-size: 1rem;
    font-weight: 600;
}

/* Enhanced table styling for payment details */
.table-responsive {
    border-radius: 0.5rem;
    overflow: hidden;
    border: 1px solid #dee2e6;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
}

.table thead th {
    border-bottom: 2px solid #dee2e6;
    background-color: #f8f9fa;
}

/* File type icons styling */
.fas.fa-file-pdf { color: #dc3545; }
.fas.fa-file-word { color: #0d6efd; }
.fas.fa-file-excel { color: #198754; }
.fas.fa-file-image { color: #fd7e14; }
.fas.fa-file-alt { color: #6c757d; }

/* Enhanced status badges */
.status-badge.detail-value {
    padding: 0.5rem 1rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
}

/* Form readonly styling */
.form-control[readonly] {
    background-color: #f8f9fa;
    opacity: 1;
    border-color: #dee2e6;
    color: #6c757d;
}

.form-select[disabled] {
    background-color: #f8f9fa;
    opacity: 1;
    border-color: #dee2e6;
    color: #6c757d;
}

/* Loading animation enhancement */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.1em;
}

/* Document filter enhancement */
.document-filters {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.document-filters label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #495057;
    white-space: nowrap;
    margin: 0;
}

/* Responsive enhancements for mobile */
@media (max-width: 768px) {
    .commission-info .row > div {
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .payment-details .table {
        font-size: 0.75rem;
    }

    .payment-details .table th,
    .payment-details .table td {
        padding: 0.5rem 0.25rem;
    }

    .modal-header .modal-title {
        font-size: 1rem;
    }

    .detail-group {
        margin-bottom: 1rem;
        padding: 1rem;
    }

    .detail-label {
        font-size: 0.8rem;
    }

    .detail-value {
        font-size: 0.9rem;
    }
}

/* Print styles for transaction details */
@media print {
    .modal-header,
    .modal-footer,
    .btn,
    .upload-section {
        display: none !important;
    }

    .modal-body {
        padding: 0;
    }

    .detail-group {
        page-break-inside: avoid;
        margin-bottom: 0.5rem;
    }

    .table {
        font-size: 0.75rem;
    }
}
</style>
