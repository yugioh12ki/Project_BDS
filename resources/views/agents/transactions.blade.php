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
                <a href="#" class="btn-create-transaction">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tạo giao dịch
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-wrapper">
                <div class="search-input-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Tìm kiếm giao dịch theo ID, mã BDS..." class="search-box">
                </div>
                <select class="filter-select">
                    <option>Tất cả trạng thái</option>
                    <option>Đang xử lý</option>
                    <option>Hoàn thành</option>
                    <option>Đã hủy</option>
                </select>
                <select class="filter-select">
                    <option>Tất cả loại</option>
                    <option>Mua bán</option>
                    <option>Cho thuê</option>
                    <option>Thuê</option>
                </select>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-container">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>ID Giao dịch</th>
                        <th>Mã BĐS</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Ngày giao dịch</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- TODO: Sẽ foreach ở đây --}}
                    <tr>
                        <td class="transaction-id">1</td>
                        <td class="property-code">P001</td>
                        <td>
                            <span class="type-badge type-sale">Mua bán</span>
                        </td>
                        <td class="transaction-value">4.800.000.000 ₫</td>
                        <td class="transaction-date">2/6/2025</td>
                        <td>
                            <span class="status-badge status-processing">Đang xử lý</span>
                        </td>
                        <td>
                            <a href="#" class="action-btn">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="transaction-id">4409d728-63d4-4fcc-a442-09af40fd08e0</td>
                        <td class="property-code">P001</td>
                        <td>
                            <span class="type-badge type-sale">Mua bán</span>
                        </td>
                        <td class="transaction-value">4.800.000.000 ₫</td>
                        <td class="transaction-date">2/6/2025</td>
                        <td>
                            <span class="status-badge status-processing">Đang xử lý</span>
                        </td>
                        <td>
                            <a href="#" class="action-btn">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
