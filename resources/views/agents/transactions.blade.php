@extends('_layout._layagent.app')

@section('title', 'Giao Dịch')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/transaction-modal.css') }}">
@endsection

@section('transactions')
<div class="transactions-page">
    <div class="content-wrapper">
        <!-- Header Section -->
        <div class="page-header">
            <div class="header-actions">
                <div>
                    <h2>Danh sách giao dịch</h2>
                    <p class="text-muted mb-0">Quản lý và theo dõi tất cả giao dịch bất động sản</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm"
                        onclick="openCreateTransactionModal()">
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

            <!-- Transaction Table -->
            <table class="transaction-table" id="transactionTable">
                <thead>
                    <tr>
                        <th class="sortable" data-sort="id">ID Giao dịch
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable" data-sort="property">Bất động sản
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th>Khách hàng</th>
                        <th class="sortable" data-sort="type">Loại giao dịch
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable" data-sort="value">Giá trị
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable" data-sort="date">Ngày thanh toán
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th class="sortable" data-sort="status">Trạng thái
                            <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th>Thao tác</th>
                    </tr>
                </thead>                <tbody id="transactionTableBody">
                    @if(isset($transactions) && $transactions->count() > 0)
                        @foreach($transactions as $transaction)
                        <tr class="transaction-row" data-transaction-id="{{ $transaction->TransactionID ?? '' }}">
                            <td class="transaction-id">#{{ $transaction->TransactionID ?? 'N/A' }}</td>
                            <td class="property-info">
                                <div class="property-details">
                                    <div class="property-title">{{ optional($transaction->property)->Title ?? 'N/A' }}</div>
                                    <div class="property-code text-muted">{{ optional($transaction->property)->PropertyID ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="customer-info">
                                <div class="customer-details">
                                    <div class="customer-name">{{ optional($transaction->trans_cus)->Name ?? 'N/A' }}</div>
                                    <div class="customer-phone text-muted">{{ optional($transaction->trans_cus)->Phone ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="transaction-type">
                                <span class="type-badge {{ ($transaction->TransactionType ?? '') === 'Sale' ? 'type-sale' : 'type-rent' }}">
                                    {{ ($transaction->TransactionType ?? '') === 'Sale' ? 'Mua bán' : 'Cho thuê' }}
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
                                   data-action="view" data-transaction-id="{{ $transaction->TransactionID ?? '' }}">
                                    👁️ Chi tiết
                                </a>
                                <a href="#" class="action-btn btn-edit" title="Chỉnh sửa"
                                   data-action="edit" data-transaction-id="{{ $transaction->TransactionID ?? '' }}">
                                    ✏️ Sửa
                                </a>
                                <a href="#" class="action-btn btn-docs" title="Tài liệu"
                                   data-action="docs" data-transaction-id="{{ $transaction->TransactionID ?? '' }}">
                                    📄 Tài liệu
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                    <tr class="empty-row">
                        <td colspan="8" class="no-results">
                            <i class="fas fa-inbox"></i>
                            <h4>Không có giao dịch nào</h4>
                            <p>Chưa có giao dịch nào được tìm thấy</p>
                        </td>
                    </tr>
                    @endif
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
        </div>
    </div>
</div>

<!-- Include Modal Tạo Giao Dịch -->
@include('agents.transactions.create-transaction-modal', ['properties' => $properties ?? [], 'customers' => $customers ?? []])

<!-- Include Các Modal Quản Lý Giao Dịch -->
@include('agents.transactions.transaction-modals')

@endsection

@section('scripts')
{{-- <script src="{{ asset('js/create-transaction-modal.js') }}"></script> --}}
<script>
// ===== TRANSACTION TABLE FUNCTIONS =====

// Transaction action functions
function handleViewTransaction(transactionId) {
    loadTransactionDetails(transactionId);
    const modal = new bootstrap.Modal(document.getElementById('viewTransactionModal'));
    modal.show();
}

function handleEditTransaction(transactionId) {
    loadTransactionForEdit(transactionId);
    const modal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
    modal.show();
}

function handleViewDocuments(transactionId) {
    loadTransactionDocuments(transactionId);
    const modal = new bootstrap.Modal(document.getElementById('documentsModal'));
    modal.show();
}

// Load transaction details for view modal
function loadTransactionDetails(transactionId) {
    // Show loading
    document.getElementById('viewModalLoading').style.display = 'block';
    document.getElementById('viewModalContent').style.display = 'none';

    // Make API call to get transaction details
    fetch(`/agent/transactions/${transactionId}/details`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load transaction details');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const transaction = data.transaction;

                // Basic transaction info
                document.getElementById('viewTransactionId').textContent = transaction.TransactionID;
                document.getElementById('viewTransactionType').textContent = transaction.FormattedType;
                document.getElementById('viewTransactionDate').textContent = transaction.FormattedDate;
                document.getElementById('viewTransactionValue').textContent = transaction.FormattedAmount;

                // Set status with badge
                const statusElement = document.getElementById('viewTransactionStatus');
                statusElement.textContent = transaction.StatusText;
                statusElement.className = 'detail-value status-badge';

                // Add appropriate status class
                statusElement.classList.remove('status-paid', 'status-pending', 'status-cancelled');
                if (transaction.Status === 1) {
                    statusElement.classList.add('status-paid');
                } else if (transaction.Status === 0) {
                    statusElement.classList.add('status-pending');
                } else if (transaction.Status === 2) {
                    statusElement.classList.add('status-cancelled');
                }

                // Property and people info
                document.getElementById('viewPropertyId').textContent = transaction.PropertyID || 'N/A';
                document.getElementById('viewPropertyTitle').textContent = transaction.PropertyTitle || 'N/A';
                document.getElementById('viewCustomerName').textContent = transaction.CustomerName || 'N/A';
                document.getElementById('viewOwnerName').textContent = transaction.OwnerName || 'N/A';
                document.getElementById('viewAgentName').textContent = transaction.AgentName || 'N/A';

                // Load payment details
                loadPaymentDetailsFromAPI(transaction.PaymentDetails);

                // Load commission info
                loadCommissionInfoFromAPI(transaction.Commission);

                // Hide loading, show content
                document.getElementById('viewModalLoading').style.display = 'none';
                document.getElementById('viewModalContent').style.display = 'block';
            } else {
                throw new Error(data.message || 'Failed to load transaction details');
            }
        })
        .catch(error => {
            console.error('Error loading transaction details:', error);
            alert('Có lỗi xảy ra khi tải thông tin giao dịch: ' + error.message);

            // Hide loading on error
            document.getElementById('viewModalLoading').style.display = 'none';
            document.getElementById('viewModalContent').style.display = 'block';
        });
}

// Load payment details from API data
function loadPaymentDetailsFromAPI(paymentDetails) {
    const paymentTable = document.getElementById('paymentDetailsTable');

    let paymentHtml = '';
    if (paymentDetails && paymentDetails.length > 0) {
        paymentDetails.forEach(payment => {
            const statusClass = payment.status === 'Hoàn Thành' ? 'badge bg-success' :
                               payment.status === 'Chờ đợi' ? 'badge bg-warning' : 'badge bg-danger';

            paymentHtml += `
                <tr>
                    <td>${payment.num_pay}</td>
                    <td>${payment.formatted_price}</td>
                    <td>${payment.rent_month || 'N/A'}</td>
                    <td>${payment.formatted_date}</td>
                    <td>${payment.payment_type}</td>
                    <td><span class="${statusClass}">${payment.status_text}</span></td>
                </tr>
            `;
        });
    } else {
        paymentHtml = `
            <tr>
                <td colspan="6" class="text-center text-muted">Chưa có chi tiết thanh toán</td>
            </tr>
        `;
    }

    paymentTable.innerHTML = paymentHtml;
}

// Load commission information from API data
function loadCommissionInfoFromAPI(commissionData) {
    const commissionDiv = document.getElementById('commissionInfo');

    if (commissionData) {
        const statusClass = commissionData.status === 'Success' ? 'text-success' :
                           commissionData.status === 'Pending' ? 'text-warning' : 'text-danger';

        commissionDiv.innerHTML = `
            <div class="row">
                <div class="col-md-3">
                    <strong>Tỷ lệ hoa hồng:</strong><br>
                    <span class="text-primary">${commissionData.formatted_percentage}</span>
                </div>
                <div class="col-md-3">
                    <strong>Số tiền hoa hồng:</strong><br>
                    <span class="text-success">${commissionData.formatted_amount}</span>
                </div>
                <div class="col-md-3">
                    <strong>Trạng thái:</strong><br>
                    <span class="${statusClass}">${commissionData.status_text}</span>
                </div>
                <div class="col-md-3">
                    <strong>Ngày thanh toán:</strong><br>
                    <span>${commissionData.formatted_paid_date || 'Chưa thanh toán'}</span>
                </div>
            </div>
        `;
    } else {
        commissionDiv.innerHTML = `
            <div class="text-center text-muted">
                <i class="fas fa-info-circle me-2"></i>Chưa có thông tin hoa hồng
            </div>
        `;
    }
}

// Load transaction data for edit modal
function loadTransactionForEdit(transactionId) {
    // Make API call to get transaction data for editing
    fetch(`/agent/transactions/${transactionId}/edit-data`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load transaction data');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const transaction = data.transaction;

                // Check if all required elements exist before setting values
                const elements = {
                    'editTransactionId': transaction.TransactionID,
                    'editTransactionType': transaction.Type,
                    'editTransactionStatus': transaction.Status,
                    'editTransactionValue': transaction.FormattedAmount,
                    'editTransactionDate': transaction.FormattedDateForInput,
                    'editPropertyId': transaction.PropertyID || 'N/A',
                    'editCustomerName': transaction.CustomerName || 'N/A',
                    'editTransactionNotes': transaction.Notes || ''
                };

                // Set values for existing elements only
                for (const [elementId, value] of Object.entries(elements)) {
                    const element = document.getElementById(elementId);
                    if (element) {
                        element.value = value;
                    } else {
                        console.warn(`Element with ID '${elementId}' not found`);
                    }
                }

                console.log('Transaction data loaded successfully for editing');
            } else {
                throw new Error(data.message || 'Failed to load transaction data');
            }
        })
        .catch(error => {
            console.error('Error loading transaction for edit:', error);
            alert('Có lỗi xảy ra khi tải dữ liệu giao dịch: ' + error.message);
        });
}

// Load transaction documents
function loadTransactionDocuments(transactionId) {
    // Show loading
    document.getElementById('documentsLoading').style.display = 'table-row';
    document.getElementById('noDocuments').style.display = 'none';

    // Make API call to get documents
    fetch(`/agent/transactions/${transactionId}/documents`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load documents');
            }
            return response.json();
        })
        .then(data => {
            const tbody = document.getElementById('documentsTableBody');
            let documentsHtml = '';

            // Hide loading
            document.getElementById('documentsLoading').style.display = 'none';

            if (data.success && data.documents && data.documents.length > 0) {
                data.documents.forEach(doc => {
                    const fileName = doc.file_name;
                    const uploadDate = doc.formatted_date;

                    documentsHtml += `
                        <tr>
                            <td>
                                <i class="fas fa-file-${getFileIcon(doc.document_type)} me-2 text-primary"></i>
                                ${fileName}
                            </td>
                            <td>
                                <span class="badge bg-secondary">${doc.document_type}</span>
                            </td>
                            <td>
                                <small class="text-muted">${doc.file_path}</small>
                            </td>
                            <td>${uploadDate}</td>
                            <td>
                                <button class="doc-action-btn btn-download-doc" onclick="downloadDocument(${transactionId}, ${doc.document_id})" title="Tải xuống">
                                    <i class="fas fa-download"></i> Tải xuống
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                documentsHtml = `
                    <tr id="noDocuments">
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-folder-open mb-2"></i>
                            <br>Chưa có tài liệu nào cho giao dịch này
                        </td>
                    </tr>
                `;
            }

            tbody.innerHTML = documentsHtml;
        })
        .catch(error => {
            console.error('Error loading documents:', error);

            // Hide loading
            document.getElementById('documentsLoading').style.display = 'none';

            // Show error message
            const tbody = document.getElementById('documentsTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle mb-2"></i>
                        <br>Có lỗi xảy ra khi tải danh sách tài liệu
                    </td>
                </tr>
            `;
        });
}

// Helper function to get file icon
function getFileIcon(documentType) {
    switch (documentType.toUpperCase()) {
        case 'PDF': return 'pdf';
        case 'DOCX': return 'word';
        case 'XLSX': return 'excel';
        case 'JPG':
        case 'PNG': return 'image';
        default: return 'alt';
    }
}

// Document actions
function viewDocument(filePath) {
    // Open document in new tab
    window.open(filePath, '_blank');
}

function downloadDocument(transactionId, documentId) {
    // Trigger download via API
    window.location.href = `/agent/transactions/${transactionId}/documents/${documentId}/download`;
}

function deleteDocument(transactionId, documentId) {
    if (confirm('Bạn có chắc chắn muốn xóa tài liệu này?')) {
        fetch(`/agent/transactions/${transactionId}/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to delete document');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast('Đã xóa tài liệu thành công', 'success');
                // Reload documents list
                loadTransactionDocuments(transactionId);
            } else {
                throw new Error(data.message || 'Failed to delete document');
            }
        })
        .catch(error => {
            console.error('Error deleting document:', error);
            showToast('Có lỗi xảy ra khi xóa tài liệu: ' + error.message, 'error');
        });
    }
}

// Save transaction changes
function saveTransactionChanges() {
    const form = document.getElementById('editTransactionForm');
    const formData = new FormData(form);

    const transactionId = formData.get('transaction_id');
    const newStatus = formData.get('status');
    const notes = formData.get('notes');

    // Validate required fields
    if (!newStatus) {
        showToast('Vui lòng chọn trạng thái giao dịch', 'warning');
        return;
    }

    // Convert status to numeric values for API
    let statusValue;
    switch (newStatus) {
        case 'Pending': statusValue = 0; break;
        case 'Paid': statusValue = 1; break;
        case 'Cancelled': statusValue = 2; break;
        default:
            showToast('Trạng thái không hợp lệ', 'error');
            return;
    }

    // Show loading button
    const saveBtn = event.target;
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang cập nhật...';
    saveBtn.disabled = true;

    // Make API call to update transaction status
    fetch(`/agent/transactions/${transactionId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: statusValue,
            notes: notes
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to update transaction status');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update UI - status in table
            const currentRow = document.querySelector(`tr[data-transaction-id="${transactionId}"]`);
            const currentStatusElement = currentRow.querySelector('.status-badge');

            let newStatusClass;
            switch (statusValue) {
                case 1: // Paid
                    newStatusClass = 'bg-success';
                    break;
                case 0: // Pending
                    newStatusClass = 'bg-warning text-dark';
                    break;
                case 2: // Cancelled
                    newStatusClass = 'bg-danger';
                    break;
            }

            // Update table row
            if (currentStatusElement) {
                currentStatusElement.textContent = data.transaction.status_text;
                currentStatusElement.className = `status-badge badge ${newStatusClass}`;
            }

            // Show success message
            if (statusValue === 1) {
                showToast('Cập nhật thành công! Trạng thái bất động sản sẽ được cập nhật tự động.', 'success');
            } else {
                showToast('Cập nhật trạng thái giao dịch thành công!', 'success');
            }

            // Reset button
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editTransactionModal'));
            modal.hide();

            // Refresh table data if needed
            filterTransactions();
        } else {
            throw new Error(data.message || 'Failed to update transaction status');
        }
    })
    .catch(error => {
        console.error('Error updating transaction:', error);
        showToast('Có lỗi xảy ra khi cập nhật giao dịch: ' + error.message, 'error');

        // Reset button
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

// Open edit modal from view modal
function openEditTransactionModal() {
    const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewTransactionModal'));
    viewModal.hide();

    const transactionId = document.getElementById('viewTransactionId').textContent;
    setTimeout(() => {
        handleEditTransaction(transactionId);
    }, 300);
}

// Upload documents
function uploadDocuments() {
    const fileInput = document.getElementById('documentFileInput');
    const documentType = document.getElementById('documentType');

    if (fileInput.files.length === 0) {
        showToast('Vui lòng chọn file để tải lên', 'warning');
        return;
    }

    if (!documentType.value) {
        showToast('Vui lòng chọn loại tài liệu', 'warning');
        return;
    }

    // Validate file types
    const allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xlsx'];
    const files = Array.from(fileInput.files);
    const invalidFiles = files.filter(file => {
        const ext = file.name.split('.').pop().toLowerCase();
        return !allowedTypes.includes(ext);
    });

    if (invalidFiles.length > 0) {
        showToast('Một số file không được hỗ trợ. Chỉ chấp nhận: PDF, DOC, DOCX, JPG, PNG, XLSX', 'error');
        return;
    }

    // Validate file size (max 10MB per file)
    const oversizedFiles = files.filter(file => file.size > 10 * 1024 * 1024);
    if (oversizedFiles.length > 0) {
        showToast('Một số file vượt quá kích thước cho phép (10MB)', 'error');
        return;
    }

    // Get transaction ID
    const currentTransactionId = document.getElementById('viewTransactionId').textContent;
    if (!currentTransactionId) {
        showToast('Không tìm thấy ID giao dịch', 'error');
        return;
    }

    // Upload each file
    showToast('Đang tải lên tài liệu...', 'info');

    const uploadPromises = files.map(file => {
        const formData = new FormData();
        formData.append('document', file);
        formData.append('document_type', documentType.value);

        return fetch(`/agent/transactions/${currentTransactionId}/documents`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Failed to upload ${file.name}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || `Failed to upload ${file.name}`);
            }
            return data;
        });
    });

    Promise.all(uploadPromises)
        .then(results => {
            showToast(`Đã tải lên thành công ${results.length} tài liệu!`, 'success');

            // Clear form
            fileInput.value = '';
            documentType.value = '';

            // Reload documents list
            loadTransactionDocuments(currentTransactionId);
        })
        .catch(error => {
            console.error('Error uploading documents:', error);
            showToast('Có lỗi xảy ra khi tải lên tài liệu: ' + error.message, 'error');
        });
}

// Download all documents separately
function downloadAllDocuments() {
    const currentTransactionId = document.getElementById('viewTransactionId').textContent;
    if (!currentTransactionId) {
        showToast('Không tìm thấy ID giao dịch', 'error');
        return;
    }

    showToast('Đang chuẩn bị tải xuống từng tài liệu...', 'info');

    // Get all document download buttons on the current page
    const downloadButtons = document.querySelectorAll('.btn-download-doc');

    if (downloadButtons.length === 0) {
        showToast('Không có tài liệu để tải xuống', 'warning');
        return;
    }

    // Create a delay between downloads to avoid overwhelming the browser
    let downloadIndex = 0;
    const downloadInterval = setInterval(() => {
        if (downloadIndex >= downloadButtons.length) {
            clearInterval(downloadInterval);
            showToast(`Đã tải xuống ${downloadButtons.length} tài liệu thành công!`, 'success');
            return;
        }

        // Trigger click on download button
        downloadButtons[downloadIndex].click();
        downloadIndex++;
    }, 800); // Download every 800ms to give time for each download
}

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast element
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : type === 'error' ? 'danger' : 'primary'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    // Add to page
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    const toastElement = document.createElement('div');
    toastElement.innerHTML = toastHtml;
    toastContainer.appendChild(toastElement.firstElementChild);

    // Show toast
    const toast = new bootstrap.Toast(toastContainer.lastElementChild);
    toast.show();

    // Auto remove after hide
    toastContainer.lastElementChild.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

function exportTransactions() {
    console.log('Exporting transactions to Excel...');

    // Get all visible rows
    const rows = document.querySelectorAll('.transaction-table tbody tr:not(.empty-row):not([style*="display: none"])');
    if (rows.length === 0) {
        alert('Không có dữ liệu để xuất');
        return;
    }

    // Show loading
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) loadingOverlay.style.display = 'flex';

    setTimeout(() => {
        // Prepare data for export
        const exportData = [];
        const headers = ['ID Giao dịch', 'Bất động sản', 'Khách hàng', 'Loại', 'Giá trị', 'Ngày', 'Trạng thái'];
        exportData.push(headers);

        rows.forEach(row => {
            const rowData = [
                (row.querySelector('.transaction-id')?.textContent || '').trim(),
                (row.querySelector('.property-title')?.textContent || '').trim(),
                (row.querySelector('.customer-name')?.textContent || '').trim(),
                (row.querySelector('.type-badge')?.textContent || '').trim(),
                (row.querySelector('.transaction-value')?.textContent || '').trim(),
                (row.querySelector('.transaction-date')?.textContent || '').trim(),
                (row.querySelector('.status-badge')?.textContent || '').trim()
            ];
            exportData.push(rowData);
        });

        // Create CSV content
        const csvContent = exportData.map(row =>
            row.map(field => `"${field.replace(/"/g, '""')}"`).join(',')
        ).join('\n');

        // Download CSV file
        const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `transactions_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Hide loading
        if (loadingOverlay) loadingOverlay.style.display = 'none';

        alert('File Excel đã được tải xuống!');
    }, 500);
}

// Function to refresh transactions list after creating new transaction
function refreshTransactionsList() {
    console.log('Refreshing transactions list...');
    window.location.reload();
}

function openCreateTransactionModal() {
    console.log('Opening create transaction modal...');
    $('#createTransactionModal').modal('show');
}

// ===== TRANSACTION TABLE FUNCTIONALITY =====
document.addEventListener('DOMContentLoaded', function() {
    // Get filter elements
    const searchInput = document.getElementById('searchTransaction');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFromFilter = document.getElementById('dateFrom');
    const dateToFilter = document.getElementById('dateTo');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const exportExcelBtn = document.getElementById('exportExcel');
    const tableRows = document.querySelectorAll('.transaction-table tbody tr:not(.empty-row)');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const noResults = document.querySelector('.empty-row');

    // Pagination elements
    const paginationContainer = document.getElementById('paginationContainer');
    const paginationInfo = document.getElementById('paginationInfo');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const paginationNumbers = document.getElementById('paginationNumbers');

    // Pagination settings
    let currentPage = 1;
    const itemsPerPage = 10;
    let filteredRows = Array.from(tableRows);
    let sortColumn = null;
    let sortDirection = 'asc';

    // ===== EVENT DELEGATION FOR ACTION BUTTONS =====
    document.addEventListener('click', function(e) {
        // Handle action buttons
        if (e.target.closest('.action-btn')) {
            e.preventDefault();
            const actionBtn = e.target.closest('.action-btn');
            const action = actionBtn.getAttribute('data-action');
            const transactionId = actionBtn.getAttribute('data-transaction-id');

            console.log('Action clicked:', action, 'Transaction ID:', transactionId);

            if (action && transactionId) {
                switch (action) {
                    case 'view':
                        handleViewTransaction(transactionId);
                        break;
                    case 'edit':
                        handleEditTransaction(transactionId);
                        break;
                    case 'docs':
                        handleViewDocuments(transactionId);
                        break;
                    default:
                        console.warn('Unknown action:', action);
                }
            }
        }
    });

    // Initialize
    updateTable();
    initSortableHeaders();

    // Filter function
    function filterTransactions() {
        showLoading();

        setTimeout(() => {
            const searchTerm = searchInput?.value.toLowerCase() || '';
            const statusValue = statusFilter?.value || '';
            const typeValue = typeFilter?.value || '';
            const dateFrom = dateFromFilter?.value || '';
            const dateTo = dateToFilter?.value || '';

            filteredRows = [];

            tableRows.forEach(row => {
                const transactionId = row.querySelector('.transaction-id')?.textContent.toLowerCase() || '';
                const propertyCode = row.querySelector('.property-code')?.textContent.toLowerCase() || '';
                const propertyTitle = row.querySelector('.property-title')?.textContent.toLowerCase() || '';
                const statusBadge = row.querySelector('.status-badge');
                const typeBadge = row.querySelector('.type-badge');
                const transactionDate = row.querySelector('.transaction-date')?.textContent || '';

                // Text search
                const matchesSearch = !searchTerm ||
                    transactionId.includes(searchTerm) ||
                    propertyCode.includes(searchTerm) ||
                    propertyTitle.includes(searchTerm);

                // Status filter
                let matchesStatus = !statusValue;
                if (statusValue && statusBadge) {
                    const statusText = statusBadge.textContent.trim();
                    if (statusValue === 'Pending' && statusText.includes('Chờ xử lý')) {
                        matchesStatus = true;
                    } else if (statusValue === 'Paid' && statusText.includes('Đã thanh toán')) {
                        matchesStatus = true;
                    } else if (statusValue === 'Cancelled' && statusText.includes('Đã hủy')) {
                        matchesStatus = true;
                    }
                }

                // Type filter
                let matchesType = !typeValue;
                if (typeValue && typeBadge) {
                    const typeText = typeBadge.textContent.trim();
                    if (typeValue === 'Sale' && typeText.includes('Mua bán')) {
                        matchesType = true;
                    } else if (typeValue === 'Rent' && typeText.includes('Cho thuê')) {
                        matchesType = true;
                    }
                }

                // Date filter
                let matchesDate = true;
                if ((dateFrom || dateTo) && transactionDate) {
                    const rowDate = parseDate(transactionDate);
                    if (rowDate) {
                        if (dateFrom) {
                            const fromDate = new Date(dateFrom);
                            matchesDate = matchesDate && rowDate >= fromDate;
                        }
                        if (dateTo) {
                            const toDate = new Date(dateTo);
                            matchesDate = matchesDate && rowDate <= toDate;
                        }
                    }
                }

                if (matchesSearch && matchesStatus && matchesType && matchesDate) {
                    filteredRows.push(row);
                }
            });

            currentPage = 1;
            updateTable();
            hideLoading();
        }, 300);
    }

    // Parse date from Vietnamese format (dd/mm/yyyy)
    function parseDate(dateStr) {
        if (!dateStr || dateStr === 'N/A') return null;
        const parts = dateStr.split('/');
        if (parts.length === 3) {
            return new Date(parts[2], parts[1] - 1, parts[0]);
        }
        return null;
    }

    // Update table display
    function updateTable() {
        // Hide all rows first
        tableRows.forEach(row => {
            row.style.display = 'none';
        });

        // Show/hide no results row
        if (filteredRows.length === 0) {
            if (noResults) noResults.style.display = 'table-row';
        } else {
            if (noResults) noResults.style.display = 'none';

            // Calculate pagination
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const rowsToShow = filteredRows.slice(startIndex, endIndex);

            // Show current page rows
            rowsToShow.forEach(row => {
                row.style.display = 'table-row';
            });
        }

        updatePagination();
        updatePaginationInfo();
    }

    // Update pagination
    function updatePagination() {
        if (!paginationContainer) return;

        const totalPages = Math.ceil(filteredRows.length / itemsPerPage);

        if (totalPages <= 1) {
            paginationContainer.style.display = 'none';
            return;
        }

        paginationContainer.style.display = 'flex';

        // Update prev/next buttons
        if (prevPageBtn) {
            prevPageBtn.disabled = currentPage === 1;
        }
        if (nextPageBtn) {
            nextPageBtn.disabled = currentPage === totalPages;
        }

        // Update page numbers
        if (paginationNumbers) {
            paginationNumbers.innerHTML = '';
            for (let i = 1; i <= Math.min(totalPages, 5); i++) {
                const pageBtn = document.createElement('button');
                pageBtn.className = `pagination-number ${i === currentPage ? 'active' : ''}`;
                pageBtn.textContent = i;
                pageBtn.addEventListener('click', () => {
                    currentPage = i;
                    updateTable();
                });
                paginationNumbers.appendChild(pageBtn);
            }
        }
    }

    // Update pagination info
    function updatePaginationInfo() {
        if (!paginationInfo) return;

        const startIndex = (currentPage - 1) * itemsPerPage + 1;
        const endIndex = Math.min(currentPage * itemsPerPage, filteredRows.length);
        const total = filteredRows.length;

        if (total > 0) {
            paginationInfo.textContent = `Hiển thị ${startIndex}-${endIndex} của ${total} giao dịch`;
        } else {
            paginationInfo.textContent = 'Hiển thị 0 giao dịch';
        }
    }

    // Show/Hide loading
    function showLoading() {
        if (loadingOverlay) loadingOverlay.style.display = 'flex';
    }

    function hideLoading() {
        if (loadingOverlay) loadingOverlay.style.display = 'none';
    }

    // Initialize sortable headers
    function initSortableHeaders() {
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', () => {
                const sortKey = header.dataset.sort;
                sortTable(sortKey, header);
            });
        });
    }

    // Sort table
    function sortTable(column, headerElement) {
        if (sortColumn === column) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortColumn = column;
            sortDirection = 'asc';
        }

        filteredRows.sort((a, b) => {
            let aVal = '';
            let bVal = '';

            switch (column) {
                case 'id':
                    aVal = a.querySelector('.transaction-id')?.textContent || '';
                    bVal = b.querySelector('.transaction-id')?.textContent || '';
                    break;
                case 'property':
                    aVal = a.querySelector('.property-title')?.textContent || '';
                    bVal = b.querySelector('.property-title')?.textContent || '';
                    break;
                case 'type':
                    aVal = a.querySelector('.type-badge')?.textContent || '';
                    bVal = b.querySelector('.type-badge')?.textContent || '';
                    break;
                case 'value':
                    aVal = a.querySelector('.transaction-value')?.textContent.replace(/[^\d]/g, '') || '0';
                    bVal = b.querySelector('.transaction-value')?.textContent.replace(/[^\d]/g, '') || '0';
                    aVal = parseInt(aVal);
                    bVal = parseInt(bVal);
                    break;
                case 'date':
                    aVal = parseDate(a.querySelector('.transaction-date')?.textContent) || new Date(0);
                    bVal = parseDate(b.querySelector('.transaction-date')?.textContent) || new Date(0);
                    break;
                case 'status':
                    aVal = a.querySelector('.status-badge')?.textContent || '';
                    bVal = b.querySelector('.status-badge')?.textContent || '';
                    break;
            }

            if (typeof aVal === 'string' && typeof bVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }

            let result = 0;
            if (aVal < bVal) result = -1;
            if (aVal > bVal) result = 1;

            return sortDirection === 'asc' ? result : -result;
        });

        updateSortIcons(headerElement, sortDirection);
        updateTable();
    }

    // Update sort icons
    function updateSortIcons(activeHeader, direction) {
        // Reset all sort icons
        document.querySelectorAll('.sortable').forEach(header => {
            header.classList.remove('sort-asc', 'sort-desc');
        });

        // Set active sort icon
        activeHeader.classList.add(direction === 'asc' ? 'sort-asc' : 'sort-desc');
    }

    // Clear filters function
    function clearFilters() {
        if (searchInput) searchInput.value = '';
        if (statusFilter) statusFilter.value = '';
        if (typeFilter) typeFilter.value = '';
        if (dateFromFilter) dateFromFilter.value = '';
        if (dateToFilter) dateToFilter.value = '';

        filteredRows = Array.from(tableRows);
        currentPage = 1;
        sortColumn = null;
        sortDirection = 'asc';

        // Reset sort icons
        document.querySelectorAll('.sortable').forEach(header => {
            header.classList.remove('sort-asc', 'sort-desc');
        });

        updateTable();
    }

    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', filterTransactions);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterTransactions);
    }
    if (typeFilter) {
        typeFilter.addEventListener('change', filterTransactions);
    }
    if (dateFromFilter) {
        dateFromFilter.addEventListener('change', filterTransactions);
    }
    if (dateToFilter) {
        dateToFilter.addEventListener('change', filterTransactions);
    }
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearFilters);
    }

    // Pagination event listeners
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                updateTable();
            }
        });
    }

    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updateTable();
            }
        });
    }

    // ===== DRAG & DROP FUNCTIONALITY FOR DOCUMENTS =====
    // Initialize drag & drop for document upload
    initializeDocumentUpload();

    function initializeDocumentUpload() {
        const uploadArea = document.getElementById('documentUploadArea');
        const fileInput = document.getElementById('documentFileInput');

        if (!uploadArea || !fileInput) return;

        // Click to select files
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        // Drag & drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('dragover');
            });
        });

        // Handle dropped files
        uploadArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;

            fileInput.files = files;

            // Show file count
            if (files.length > 0) {
                const fileText = files.length === 1 ? '1 file được chọn' : `${files.length} files được chọn`;
                const uploadContent = uploadArea.querySelector('.upload-content p');
                if (uploadContent) {
                    uploadContent.textContent = fileText;
                    uploadContent.style.color = '#28a745';
                    uploadContent.style.fontWeight = 'bold';
                }
            }
        });

        // Handle file input change
        fileInput.addEventListener('change', (e) => {
            const files = e.target.files;
            if (files.length > 0) {
                const fileText = files.length === 1 ? '1 file được chọn' : `${files.length} files được chọn`;
                const uploadContent = uploadArea.querySelector('.upload-content p');
                if (uploadContent) {
                    uploadContent.textContent = fileText;
                    uploadContent.style.color = '#28a745';
                    uploadContent.style.fontWeight = 'bold';
                }
            }
        });
    }

    // ===== DOCUMENT UPLOAD FUNCTIONALITY =====
    const uploadArea = document.getElementById('documentUploadArea');
    const fileInput = document.getElementById('documentFileInput');

    if (uploadArea && fileInput) {
        // Click to upload
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        // Drag and drop functionality
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateFileInputDisplay();
            }
        });

        // File input change
        fileInput.addEventListener('change', updateFileInputDisplay);
    }

    function updateFileInputDisplay() {
        const fileInput = document.getElementById('documentFileInput');
        const uploadContent = uploadArea.querySelector('.upload-content');

        if (fileInput.files.length > 0) {
            const fileNames = Array.from(fileInput.files).map(file => file.name).join(', ');
            uploadContent.innerHTML = `
                <i class="fas fa-file-check mb-2 text-success"></i>
                <p class="mb-2 text-success">Đã chọn ${fileInput.files.length} file(s)</p>
                <small class="text-muted">${fileNames}</small>
            `;
        } else {
            uploadContent.innerHTML = `
                <i class="fas fa-cloud-upload-alt mb-2"></i>
                <p class="mb-2">Kéo thả file hoặc click để chọn</p>
                <small class="text-muted">Hỗ trợ: PDF, DOC, DOCX, JPG, PNG (Max: 10MB)</small>
            `;
        }
    }
});
</script>
@endsection
