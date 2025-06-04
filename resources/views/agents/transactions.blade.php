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
                </div>
                <a href="#" class="btn btn-primary btn-sm">
                    + Tạo giao dịch
                </a>
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
                <tbody>
                    @forelse($transactions ?? [] as $transaction)
                    <tr>
                        <td class="transaction-id">{{ $transaction->TransactionID }}</td>
                        <td class="property-code">{{ $transaction->PropertyID }}</td>
                        <td>
                            <span class="type-badge {{ $transaction->TransactionType === 'Rent' ? 'type-rent' : 'type-sale' }}">
                                {{ $transaction->TransactionType === 'Rent' ? 'Cho thuê' : 'Mua bán' }}
                            </span>
                        </td>
                        <td class="transaction-value">{{ number_format($transaction->TotalPrice, 0, ',', '.') }} ₫</td>
                        <td class="transaction-date">{{ date('d/m/Y', strtotime($transaction->TransactionDate)) }}</td>
                        <td>
                            <span class="status-badge 
                                @if($transaction->TranStatus === 'Paid') status-paid
                                @elseif($transaction->TranStatus === 'Pending') status-pending
                                @elseif($transaction->TranStatus === 'Cancelled') status-cancelled
                                @endif">
                                @if($transaction->TranStatus === 'Paid') Đã thanh toán
                                @elseif($transaction->TranStatus === 'Pending') Chờ xử lý
                                @elseif($transaction->TranStatus === 'Cancelled') Đã hủy
                                @endif
                            </span>
                        </td>
                        <td class="action-buttons">
                            <a href="#" class="action-btn btn-view" title="Xem chi tiết" 
                               onclick="viewTransaction('{{ $transaction->TransactionID }}')">
                                👁️ Chi tiết
                            </a>
                            <a href="#" class="action-btn btn-edit" title="Chỉnh sửa"
                               onclick="editTransaction('{{ $transaction->TransactionID }}')">
                                ✏️ Sửa
                            </a>
                            <a href="#" class="action-btn btn-docs" title="Tài liệu"
                               onclick="viewDocuments('{{ $transaction->TransactionID }}')">
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
        </div>
    </div>
</div>

<script>
// Export function
function exportTransactions() {
    window.location.href = '{{ route("agent.transactions.export") }}';
}

// View transaction details
function viewTransaction(transactionId) {
    fetch(`{{ route("agent.transactions.show", ":id") }}`.replace(':id', transactionId))
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
    typeFilter.addEventListener('change', performSearch);

    // Clear filters
    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        typeFilter.value = '';
        window.location.href = '{{ route("agent.transactions") }}';
    });
});
</script>
@endsection