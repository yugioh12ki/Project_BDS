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
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTransactionModal">
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
                        <th>ID Giao dịch</th>
                        <th>Bất động sản</th>
                        <th>Khách hàng</th>
                        <th>Loại giao dịch</th>
                        <th>Giá trị</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
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
                                    <div class="customer-name">{{ optional($transaction->customer)->Name ?? 'N/A' }}</div>
                                    <div class="customer-phone text-muted">{{ optional($transaction->customer)->Phone ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="transaction-type">
                                <span class="type-badge {{ ($transaction->TranType ?? '') === 'Sale' ? 'type-sale' : 'type-rent' }}">
                                    {{ ($transaction->TranType ?? '') === 'Sale' ? 'Mua bán' : 'Cho thuê' }}
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

@endsection
