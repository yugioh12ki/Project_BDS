@extends('_layout._layowner.app')

@section('transaction')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-3">
                <i class="bi bi-currency-exchange text-primary"></i>
                Quản lý Giao dịch & Hoa hồng
            </h1>
            <p class="text-muted">Theo dõi giao dịch và quản lý hoa hồng cho các môi giới</p>
        </div>
        <div class="col-md-4 text-end">
            <!-- Removed header buttons as requested -->
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('owner.transactions.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Mã giao dịch, khách hàng..."
                               name="search" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" name="from_date" value="{{ $fromDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" name="to_date" value="{{ $toDate }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions and Commission Combined Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-receipt text-primary"></i>
                    Danh sách giao dịch & Hoa hồng
                </h5>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="statusFilter" id="all-status" value="all" checked>
                    <label class="btn btn-outline-secondary" for="all-status">Tất cả</label>

                    <input type="radio" class="btn-check" name="statusFilter" id="paid-commissions" value="paid">
                    <label class="btn btn-outline-success" for="paid-commissions">Đã trả hoa hồng</label>

                    <input type="radio" class="btn-check" name="statusFilter" id="pending-commissions" value="pending">
                    <label class="btn btn-outline-warning" for="pending-commissions">Chờ trả hoa hồng</label>

                    <input type="radio" class="btn-check" name="statusFilter" id="no-commissions" value="no-commission">
                    <label class="btn btn-outline-info" for="no-commissions">Chưa có hoa hồng</label>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr class="text-center">
                            <th colspan="5" class="border-end border-light fw-bold">
                                <i class="bi bi-receipt me-2"></i>THÔNG TIN GIAO DỊCH
                            </th>
                            <th colspan="4" class="fw-bold">
                                <i class="bi bi-cash-coin me-2"></i>THÔNG TIN HOA HỒNG
                            </th>
                        </tr>
                        <tr class="text-center small">
                            <!-- Transaction columns -->
                            <th class="text-start" width="12%">Mã & Ngày</th>
                            <th class="text-start" width="18%">Bất động sản</th>
                            <th class="text-center" width="12%">Loại & Giá trị</th>
                            <th class="text-start" width="20%">Khách hàng & Môi giới</th>
                            <th class="text-center border-end border-light" width="8%">Thao tác</th>
                            <!-- Commission columns -->
                            <th class="text-center" width="10%">Mã hoa hồng</th>
                            <th class="text-center" width="12%">Tỷ lệ & Số tiền</th>
                            <th class="text-center" width="8%">Trạng thái</th>
                            <th class="text-center" width="10%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr data-transaction-type="{{ $transaction->TransactionType }}"
                            data-commission-status="{{ $transaction->commission ? $transaction->commission->StatusCommission : 'no-commission' }}"
                            class="align-middle">

                            <!-- TRANSACTION INFORMATION -->
                            <!-- Transaction ID & Date -->
                            <td class="border-end border-2">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-primary small">#{{ $transaction->TransactionID }}</span>
                                    <span class="text-muted small">{{ \Carbon\Carbon::parse($transaction->TransactionDate)->format('d/m/Y') }}</span>
                                    <span class="text-muted small">{{ \Carbon\Carbon::parse($transaction->TransactionDate)->format('H:i') }}</span>
                                    <div class="mt-1">
                                        <span class="badge bg-{{ $transaction->TranStatus == 'Paid' ? 'success' : ($transaction->TranStatus == 'Pending' ? 'warning' : 'danger') }} badge-sm">
                                            {{ $transaction->TranStatus == 'Paid' ? 'Đã thanh toán' : ($transaction->TranStatus == 'Pending' ? 'Chờ thanh toán' : 'Đã hủy') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Property Info -->
                            <td class="border-end border-2">
                                <div class="d-flex flex-column">
                                    <span class="fw-medium text-dark small">{{ Str::limit($transaction->property_title ?? 'N/A', 30) }}</span>
                                    <small class="text-muted">{{ Str::limit($transaction->property_address ?? '', 35) }}</small>
                                </div>
                            </td>

                            <!-- Type & Value -->
                            <td class="text-center border-end border-2">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge bg-{{ $transaction->TransactionType == 'Sale' ? 'primary' : 'info' }} mb-1">
                                        {{ $transaction->TransactionType == 'Sale' ? 'Bán' : 'Thuê' }}
                                    </span>
                                    <div class="fw-bold text-success small">
                                        {{ number_format($transaction->TotalPrice / 1000000, 1) }}M VND
                                    </div>
                                    @if($transaction->TransactionType == 'Rent' && $transaction->rent_months)
                                    <small class="text-muted">{{ $transaction->rent_months }} tháng</small>
                                    @endif
                                </div>
                            </td>

                            <!-- Customer & Agent -->
                            <td class="border-end border-2">
                                <div class="d-flex flex-column small">
                                    <div class="mb-1">
                                        <strong class="text-primary">KH:</strong>
                                        <span>{{ Str::limit($transaction->customer_name ?? 'N/A', 20) }}</span>
                                        @if($transaction->customer_phone)
                                        <br><small class="text-muted">{{ $transaction->customer_phone }}</small>
                                        @endif
                                    </div>
                                    <div>
                                        <strong class="text-warning">MG:</strong>
                                        <span>{{ Str::limit($transaction->agent_name ?? 'N/A', 20) }}</span>
                                        @if($transaction->agent_phone)
                                        <br><small class="text-muted">{{ $transaction->agent_phone }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Transaction Actions -->
                            <td class="text-center border-end border-2">
                                <button class="btn btn-outline-info btn-sm"
                                        onclick="viewTransactionDetail('{{ $transaction->TransactionID }}')"
                                        title="Xem chi tiết giao dịch">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>

                            <!-- COMMISSION INFORMATION -->
                            @if($transaction->commission)
                                <!-- Commission ID -->
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="fw-bold text-warning small">#{{ $transaction->commission->CommissionID }}</span>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($transaction->commission->created_at ?? now())->format('d/m/Y') }}</small>
                                    </div>
                                </td>

                                <!-- Percentage & Amount -->
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge bg-{{ $transaction->commission->TypeCom == 'Sale' ? 'primary' : 'info' }} badge-sm mb-1">
                                            {{ number_format($transaction->commission->Percentage * 100, 1) }}%
                                        </span>
                                        <div class="fw-bold text-success small">
                                            {{ number_format($transaction->commission->Amount / 1000000, 2) }}M VND
                                        </div>
                                    </div>
                                </td>

                                <!-- Commission Status -->
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        @if($transaction->commission->StatusCommission == 'Success')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Đã trả
                                            </span>
                                            @if($transaction->commission->PaidDate)
                                            <small class="text-muted mt-1">
                                                {{ \Carbon\Carbon::parse($transaction->commission->PaidDate)->format('d/m/Y') }}
                                            </small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock"></i> Chờ trả
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Commission Actions -->
                                <td class="text-center">
                                    <div class="d-flex flex-column gap-1">
                                        @if($transaction->commission->StatusCommission == 'Pending')
                                            <button class="btn btn-outline-success btn-sm"
                                                    onclick="showPaymentModal('{{ $transaction->commission->CommissionID }}', {{ $transaction->commission->Amount }})"
                                                    title="Thanh toán hoa hồng">
                                                <i class="bi bi-cash"></i>
                                            </button>
                                        @endif

                                        @if($transaction->commission->StatusCommission == 'Success')
                                            <button class="btn btn-outline-secondary btn-sm"
                                                    onclick="printCommissionInvoice('{{ $transaction->commission->CommissionID }}')"
                                                    title="In hóa đơn">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @else
                                <!-- No Commission -->
                                <td colspan="4" class="text-center">
                                    <div class="py-2">
                                        <div class="text-muted mb-2">
                                            <i class="bi bi-dash-circle fs-5 text-muted"></i>
                                            <div class="small"><strong>Chưa có hoa hồng</strong></div>
                                        </div>

                                        @if($transaction->TranStatus == 'Paid')
                                            <button class="btn btn-primary btn-sm"
                                                    onclick="showCommissionModal('{{ $transaction->TransactionID }}', '{{ $transaction->TransactionType }}', {{ $transaction->TotalPrice }}, {{ $transaction->rent_months ?? 1 }})"
                                                    title="Tạo hoa hồng cho giao dịch này">
                                                <i class="bi bi-plus-circle"></i> Tạo hoa hồng
                                            </button>
                                        @else
                                            <small class="text-muted">Chỉ có thể tạo hoa hồng<br>khi giao dịch đã thanh toán</small>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-1 text-muted"></i>
                                    <div class="mt-3">
                                        <h5>Không có giao dịch nào</h5>
                                        <p class="mb-0">Hiện tại chưa có giao dịch nào trong hệ thống</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="card-footer">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Commission Creation Modal -->
<div class="modal fade" id="commissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chia hoa hồng cho môi giới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="commissionForm" method="POST" action="{{ route('owner.transactions.create-commission') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="commission_transaction_id" name="transaction_id">
                    <input type="hidden" id="commission_transaction_type" name="transaction_type">
                    <input type="hidden" id="commission_total_price" name="total_price">
                    <input type="hidden" id="commission_rent_months" name="rent_months">

                    <div class="mb-3">
                        <label class="form-label">Thông tin giao dịch</label>
                        <div class="bg-light p-3 rounded">
                            <div id="transaction_info"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="commission_percentage" class="form-label">Tỷ lệ hoa hồng (%)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="commission_percentage" name="percentage"
                                   step="0.1" min="1" max="3" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">
                            <span id="percentage_range_text"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số tiền hoa hồng</label>
                        <div class="input-group">
                            <input type="number" class="form-control bg-light" id="commission_amount" name="amount" readonly>
                            <span class="input-group-text">VND</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo hoa hồng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thanh toán hoa hồng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm" method="POST" action="{{ route('owner.transactions.pay-commission') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="payment_commission_id" name="commission_id">

                    <div class="mb-3">
                        <label class="form-label">Số tiền thanh toán</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-light" id="payment_amount" readonly>
                            <span class="input-group-text">VND</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Phương thức thanh toán</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Chọn phương thức thanh toán</option>
                            <option value="transfer">Chuyển khoản</option>
                            <option value="cash">Tiền mặt</option>
                            <option value="vnpay">VNPAY</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="payment_note" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="payment_note" name="note" rows="3"
                                  placeholder="Ghi chú về việc thanh toán..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Xác nhận thanh toán</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transaction Detail Modal -->
<div class="modal fade" id="transactionDetailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-receipt me-2"></i>
                    Chi tiết giao dịch <span id="detail_transaction_id"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Loading State -->
                <div id="loading_state" class="d-flex justify-content-center align-items-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <span class="ms-3">Đang tải thông tin giao dịch...</span>
                </div>

                <!-- Transaction Detail Content -->
                <div id="transaction_detail_content" class="d-none">
                    <!-- Transaction Overview -->
                    <div class="bg-light p-4 border-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">
                                    <i class="bi bi-info-circle me-2"></i>THÔNG TIN GIAO DỊCH
                                </h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <small class="text-muted">Mã giao dịch</small>
                                        <div class="fw-bold text-primary" id="overview_transaction_id"></div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Ngày giao dịch</small>
                                        <div class="fw-medium" id="overview_transaction_date"></div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Loại giao dịch</small>
                                        <div id="overview_transaction_type"></div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Trạng thái</small>
                                        <div id="overview_transaction_status"></div>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted">Tổng giá trị</small>
                                        <div class="fw-bold text-success fs-5" id="overview_total_price"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">
                                    <i class="bi bi-building me-2"></i>THÔNG TIN BẤT ĐỘNG SẢN
                                </h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <small class="text-muted">Mã bất động sản</small>
                                        <div class="fw-bold text-info" id="overview_property_id"></div>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted">Tiêu đề</small>
                                        <div class="fw-medium" id="overview_property_title"></div>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted">Địa chỉ</small>
                                        <div id="overview_property_address"></div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Loại bất động sản</small>
                                        <div id="overview_property_type"></div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Giá niêm yết</small>
                                        <div class="fw-medium text-success" id="overview_property_price"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Information Tabs -->
                    <div class="p-4">
                        <ul class="nav nav-tabs mb-4" id="detailTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="participants-tab" data-bs-toggle="tab"
                                        data-bs-target="#participants" type="button" role="tab">
                                    <i class="bi bi-people me-2"></i>Thông tin người tham gia
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payments-tab" data-bs-toggle="tab"
                                        data-bs-target="#payments" type="button" role="tab">
                                    <i class="bi bi-credit-card me-2"></i>Lịch sử thanh toán
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="documents-tab" data-bs-toggle="tab"
                                        data-bs-target="#documents" type="button" role="tab">
                                    <i class="bi bi-file-earmark-text me-2"></i>Tài liệu
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="detailTabsContent">
                            <!-- Participants Tab -->
                            <div class="tab-pane fade show active" id="participants" role="tabpanel">
                                <div class="row g-4">
                                    <!-- Customer Info -->
                                    <div class="col-md-4">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-person-check me-2"></i>KHÁCH HÀNG
                                                </h6>
                                            </div>
                                            <div class="card-body" id="customer_info">
                                                <!-- Customer details will be loaded here -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Agent Info -->
                                    <div class="col-md-4">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-person-badge me-2"></i>MÔI GIỚI
                                                </h6>
                                            </div>
                                            <div class="card-body" id="agent_info">
                                                <!-- Agent details will be loaded here -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Owner Info -->
                                    <div class="col-md-4">
                                        <div class="card border-0 bg-light h-100">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-person-square me-2"></i>CHỦ SỞ HỮU
                                                </h6>
                                            </div>
                                            <div class="card-body" id="owner_info">
                                                <!-- Owner details will be loaded here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payments Tab -->
                            <div class="tab-pane fade" id="payments" role="tabpanel">
                                <div class="card border-0">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="bi bi-clock-history me-2"></i>Lịch sử thanh toán chi tiết
                                        </h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th width="10%">Lần TT</th>
                                                        <th width="20%">Ngày thanh toán</th>
                                                        <th width="20%">Số tiền</th>
                                                        <th width="15%">Thời hạn</th>
                                                        <th width="20%">Phương thức</th>
                                                        <th width="15%">Trạng thái</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="payment_history">
                                                    <!-- Payment history will be loaded here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Documents Tab -->
                            <div class="tab-pane fade" id="documents" role="tabpanel">
                                <div class="card border-0">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="bi bi-folder2-open me-2"></i>Tài liệu giao dịch
                                        </h6>
                                        <small class="text-muted">Tổng: <span id="documents_count">0</span> tài liệu</small>
                                    </div>
                                    <div class="card-body">
                                        <div id="documents_list" class="row g-3">
                                            <!-- Documents will be loaded here -->
                                        </div>
                                        <div id="no_documents" class="text-center py-4 d-none">
                                            <i class="bi bi-folder-x display-4 text-muted"></i>
                                            <div class="mt-3">
                                                <h6 class="text-muted">Chưa có tài liệu nào</h6>
                                                <p class="text-muted mb-0">Giao dịch này chưa có tài liệu đính kèm</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Đóng
                </button>
                <button type="button" class="btn btn-primary" onclick="printTransactionDetail()">
                    <i class="bi bi-printer me-2"></i>In chi tiết
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.icon-shape {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
}

.table th {
    padding: 0.75rem 0.5rem;
    font-weight: 600;
}

.border-end.border-2 {
    border-right: 2px solid #dee2e6 !important;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .table-responsive table {
        font-size: 0.85rem;
    }

    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }

    .modal-xl {
        max-width: 95%;
    }

    .modal-body .row {
        --bs-gutter-x: 0.5rem;
    }

    .modal-body .card-body {
        padding: 0.75rem;
    }
}

/* Manual modal fallback styles */
.modal.show {
    display: block !important;
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1040;
    width: 100vw;
    height: 100vh;
    background-color: #000;
    opacity: 0.5;
}

.modal-backdrop.show {
    opacity: 0.5;
}

.toast.show {
    display: block !important;
    opacity: 1;
}

/* Transaction Detail Modal Specific Styles */
#transactionDetailModal .modal-body {
    max-height: 80vh;
    overflow-y: auto;
}

#transactionDetailModal .nav-tabs {
    border-bottom: 2px solid #dee2e6;
}

#transactionDetailModal .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    background: none;
    color: #6c757d;
    font-weight: 500;
    padding: 0.75rem 1rem;
}

#transactionDetailModal .nav-link:hover {
    border-bottom-color: #0d6efd;
    color: #0d6efd;
}

#transactionDetailModal .nav-link.active {
    border-bottom-color: #0d6efd;
    color: #0d6efd;
    background: none;
}

#transactionDetailModal .tab-content {
    padding-top: 1rem;
}

#transactionDetailModal .card-header {
    font-weight: 600;
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

#transactionDetailModal .badge {
    font-size: 0.8rem;
}

#transactionDetailModal .text-muted {
    font-size: 0.9rem;
}

/* Print styles for transaction detail */
@media print {
    #transactionDetailModal .modal-header,
    #transactionDetailModal .modal-footer,
    #transactionDetailModal .nav-tabs {
        display: none !important;
    }

    #transactionDetailModal .modal-body {
        padding: 0;
    }

    #transactionDetailModal .tab-content {
        display: block !important;
    }

    #transactionDetailModal .tab-pane {
        display: block !important;
        opacity: 1 !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Handle commission form submission
document.getElementById('commissionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang xử lý...';

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide modal with fallback methods
            const modalElement = document.getElementById('commissionModal');
            if (typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getInstance(modalElement).hide();
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                $(modalElement).modal('hide');
            } else {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
            showSuccessToast(data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showErrorToast(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Có lỗi xảy ra khi tạo hoa hồng');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Handle payment form submission
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    const paymentMethod = formData.get('payment_method');

    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang xử lý...';

    // Check if VNPAY payment
    if (paymentMethod === 'vnpay') {
        // Process VNPAY payment
        const commissionId = formData.get('commission_id');

        fetch('{{ route("owner.transactions.process-vnpay-commission") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide modal
                const modalElement = document.getElementById('paymentModal');
                if (typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getInstance(modalElement).hide();
                } else if (typeof $ !== 'undefined' && $.fn.modal) {
                    $(modalElement).modal('hide');
                } else {
                    modalElement.style.display = 'none';
                    modalElement.classList.remove('show');
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }

                // Redirect to VNPAY
                alert('Đang chuyển hướng đến trang thanh toán VNPAY...');
                window.location.href = data.payment_url;
            } else {
                showErrorToast(data.message);
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Có lỗi xảy ra khi xử lý thanh toán VNPAY');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    } else {
        // Process traditional payment methods
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide modal with fallback methods
                const modalElement = document.getElementById('paymentModal');
                if (typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getInstance(modalElement).hide();
                } else if (typeof $ !== 'undefined' && $.fn.modal) {
                    $(modalElement).modal('hide');
                } else {
                    modalElement.style.display = 'none';
                    modalElement.classList.remove('show');
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
                showSuccessToast(data.message);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showErrorToast(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorToast('Có lỗi xảy ra khi thanh toán hoa hồng');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    }
});

function showCommissionModal(transactionId, transactionType, totalPrice, rentMonths) {
    document.getElementById('commission_transaction_id').value = transactionId;
    document.getElementById('commission_transaction_type').value = transactionType;
    document.getElementById('commission_total_price').value = totalPrice;
    document.getElementById('commission_rent_months').value = rentMonths;

    // Update transaction info
    let transactionInfo = `
        <strong>Mã giao dịch:</strong> ${transactionId}<br>
        <strong>Loại:</strong> ${transactionType === 'Sale' ? 'Bán' : 'Thuê'}<br>
        <strong>Giá trị:</strong> ${new Intl.NumberFormat('vi-VN').format(totalPrice)} VND
    `;

    if (transactionType === 'Rent') {
        transactionInfo += `<br><strong>Thời hạn:</strong> ${rentMonths} tháng`;
    }

    document.getElementById('transaction_info').innerHTML = transactionInfo;

    // Set percentage range based on transaction type
    const percentageInput = document.getElementById('commission_percentage');
    const rangeText = document.getElementById('percentage_range_text');

    if (transactionType === 'Sale') {
        percentageInput.min = 1;
        percentageInput.max = 3;
        rangeText.textContent = 'Bán: 1% - 3% của tổng giá trị';
    } else {
        percentageInput.min = 50;
        percentageInput.max = 200;
        rangeText.textContent = 'Thuê: 50% - 200% của giá thuê 1 tháng';
    }

    // Clear previous values
    percentageInput.value = '';
    document.getElementById('commission_amount').value = '';

    // Show modal with Bootstrap fallback
    const modalElement = document.getElementById('commissionModal');
    if (typeof bootstrap !== 'undefined') {
        new bootstrap.Modal(modalElement).show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
        $(modalElement).modal('show');
    } else {
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
        document.body.classList.add('modal-open');
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
    }
}

function showPaymentModal(commissionId, amount) {
    document.getElementById('payment_commission_id').value = commissionId;
    document.getElementById('payment_amount').value = new Intl.NumberFormat('vi-VN').format(amount);

    const modalElement = document.getElementById('paymentModal');
    if (typeof bootstrap !== 'undefined') {
        new bootstrap.Modal(modalElement).show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
        $(modalElement).modal('show');
    } else {
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
        document.body.classList.add('modal-open');
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
    }
}

// Calculate commission amount when percentage changes
document.getElementById('commission_percentage').addEventListener('input', function() {
    const percentage = parseFloat(this.value);
    const transactionType = document.getElementById('commission_transaction_type').value;
    const totalPrice = parseFloat(document.getElementById('commission_total_price').value);
    const rentMonths = parseInt(document.getElementById('commission_rent_months').value);

    if (percentage && totalPrice) {
        let amount;
        if (transactionType === 'Sale') {
            amount = totalPrice * (percentage / 100);
        } else {
            const monthlyRent = totalPrice / rentMonths;
            amount = monthlyRent * (percentage / 100);
        }

        document.getElementById('commission_amount').value = Math.round(amount);
    }
});

// Filter functionality for status
document.querySelectorAll('input[name="statusFilter"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const filterValue = this.value;
        const rows = document.querySelectorAll('tbody tr[data-commission-status]');

        rows.forEach(row => {
            const commissionStatus = row.getAttribute('data-commission-status');
            let show = false;

            switch(filterValue) {
                case 'all':
                    show = true;
                    break;
                case 'paid':
                    show = commissionStatus === 'Success';
                    break;
                case 'pending':
                    show = commissionStatus === 'Pending';
                    break;
                case 'no-commission':
                    show = commissionStatus === 'no-commission';
                    break;
            }

            row.style.display = show ? '' : 'none';
        });
    });
});

function highlightTransaction(transactionId) {
    // Remove previous highlights
    document.querySelectorAll('tr.table-warning').forEach(row => {
        row.classList.remove('table-warning');
    });

    // Find and highlight the transaction row
    const transactionRows = document.querySelectorAll('tbody tr[data-commission-status]');
    transactionRows.forEach(row => {
        const transactionIdCell = row.querySelector('td:first-child .fw-bold');
        if (transactionIdCell && transactionIdCell.textContent.trim() === transactionId) {
            row.classList.add('table-warning');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Remove highlight after 3 seconds
            setTimeout(() => {
                row.classList.remove('table-warning');
            }, 3000);
        }
    });
}

// Wait for DOM and Bootstrap to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit more for Vite/Bootstrap to load
    setTimeout(function() {
        if (typeof bootstrap === 'undefined') {
            console.warn('Bootstrap not loaded, using fallback modal methods');
        }
    }, 100);
});

function viewTransactionDetail(transactionId) {
    // Show modal and loading state
    const modalElement = document.getElementById('transactionDetailModal');
    let modal;

    // Check if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        modal = new bootstrap.Modal(modalElement);
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
        // Fallback to jQuery Bootstrap
        modal = $(modalElement);
    } else {
        // Manual modal display as fallback
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
        document.body.classList.add('modal-open');

        // Create backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);

        modal = {
            show: () => {},
            hide: () => {
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.body.classList.remove('modal-open');
                const existingBackdrop = document.querySelector('.modal-backdrop');
                if (existingBackdrop) {
                    existingBackdrop.remove();
                }
            }
        };
    }

    // Show the modal
    if (typeof modal.show === 'function') {
        modal.show();
    } else if (typeof modal.modal === 'function') {
        modal.modal('show');
    }

    // Show loading state
    document.getElementById('loading_state').classList.remove('d-none');
    document.getElementById('transaction_detail_content').classList.add('d-none');
    document.getElementById('detail_transaction_id').textContent = `#${transactionId}`;

    // Fetch transaction details
    fetch(`{{ route('owner.transactions.detail', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', transactionId), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            populateTransactionDetail(data.transaction);
            // Hide loading and show content
            document.getElementById('loading_state').classList.add('d-none');
            document.getElementById('transaction_detail_content').classList.remove('d-none');
        } else {
            throw new Error(data.message || 'Không thể tải thông tin giao dịch');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Có lỗi xảy ra khi tải chi tiết giao dịch: ' + error.message);
        // Hide modal with fallback methods
        if (typeof modal.hide === 'function') {
            modal.hide();
        } else if (typeof modal.modal === 'function') {
            modal.modal('hide');
        } else {
            const modalElement = document.getElementById('transactionDetailModal');
            modalElement.style.display = 'none';
            modalElement.classList.remove('show');
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    });
}

function populateTransactionDetail(transaction) {
    // Populate overview information
    document.getElementById('overview_transaction_id').textContent = transaction.TransactionID;
    document.getElementById('overview_transaction_date').textContent = formatDateTime(transaction.TransactionDate);

    // Transaction type badge
    const transactionTypeHtml = transaction.TransactionType === 'Sale'
        ? '<span class="badge bg-primary">Bán</span>'
        : '<span class="badge bg-info">Thuê</span>';
    document.getElementById('overview_transaction_type').innerHTML = transactionTypeHtml;

    // Transaction status badge
    let statusBadge = '';
    switch(transaction.TranStatus) {
        case 'Paid':
            statusBadge = '<span class="badge bg-success">Đã thanh toán</span>';
            break;
        case 'Pending':
            statusBadge = '<span class="badge bg-warning">Chờ thanh toán</span>';
            break;
        case 'Cancelled':
            statusBadge = '<span class="badge bg-danger">Đã hủy</span>';
            break;
        default:
            statusBadge = '<span class="badge bg-secondary">Không xác định</span>';
    }
    document.getElementById('overview_transaction_status').innerHTML = statusBadge;

    // Format total price
    document.getElementById('overview_total_price').textContent = formatCurrency(transaction.TotalPrice);

    // Property information
    document.getElementById('overview_property_id').textContent = transaction.PropertyID;
    document.getElementById('overview_property_title').textContent = transaction.property?.Title || 'N/A';

    const propertyAddress = `${transaction.property?.Address || ''}, ${transaction.property?.Ward || ''}, ${transaction.property?.District || ''}, ${transaction.property?.Province || ''}`;
    document.getElementById('overview_property_address').textContent = propertyAddress.replace(/^, |, $|, , /g, '').replace(/, {2,}/g, ', ') || 'N/A';

    document.getElementById('overview_property_type').textContent = transaction.property?.PropertyTypeName || 'Không xác định';
    document.getElementById('overview_property_price').textContent = formatCurrency(transaction.property?.Price || 0);

    // Populate participant information
    populateParticipants(transaction);

    // Populate payment history
    populatePaymentHistory(transaction.payment_details || []);

    // Populate documents
    populateDocuments(transaction.documents || []);
}

function populateParticipants(transaction) {
    // Customer Info
    const customerHtml = `
        <div class="d-flex align-items-center mb-3">
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                <i class="bi bi-person text-white fs-4"></i>
            </div>
            <div>
                <div class="fw-bold">${transaction.customer?.Name || 'N/A'}</div>
                <small class="text-muted">${transaction.customer?.UserID || ''}</small>
            </div>
        </div>
        <div class="mb-2">
            <i class="bi bi-envelope me-2 text-muted"></i>
            <span>${transaction.customer?.Email || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-telephone me-2 text-muted"></i>
            <span>${transaction.customer?.Phone || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-card-text me-2 text-muted"></i>
            <span>${transaction.customer?.IdentityCard || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-geo-alt me-2 text-muted"></i>
            <span>${formatAddress(transaction.customer) || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-calendar me-2 text-muted"></i>
            <span>${formatDate(transaction.customer?.Birth) || 'N/A'}</span>
        </div>
        <div>
            <i class="bi bi-gender-${transaction.customer?.Sex === 'Nam' ? 'male' : (transaction.customer?.Sex === 'Nữ' ? 'female' : 'ambiguous')} me-2 text-muted"></i>
            <span>${transaction.customer?.Sex || 'N/A'}</span>
        </div>
    `;
    document.getElementById('customer_info').innerHTML = customerHtml;

    // Agent Info
    const agentHtml = `
        <div class="d-flex align-items-center mb-3">
            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                <i class="bi bi-person-badge text-dark fs-4"></i>
            </div>
            <div>
                <div class="fw-bold">${transaction.agent?.Name || 'N/A'}</div>
                <small class="text-muted">${transaction.agent?.UserID || ''}</small>
            </div>
        </div>
        <div class="mb-2">
            <i class="bi bi-envelope me-2 text-muted"></i>
            <span>${transaction.agent?.Email || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-telephone me-2 text-muted"></i>
            <span>${transaction.agent?.Phone || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-card-text me-2 text-muted"></i>
            <span>${transaction.agent?.IdentityCard || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-geo-alt me-2 text-muted"></i>
            <span>${formatAddress(transaction.agent) || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-calendar me-2 text-muted"></i>
            <span>${formatDate(transaction.agent?.Birth) || 'N/A'}</span>
        </div>
        <div>
            <i class="bi bi-gender-${transaction.agent?.Sex === 'Nam' ? 'male' : (transaction.agent?.Sex === 'Nữ' ? 'female' : 'ambiguous')} me-2 text-muted"></i>
            <span>${transaction.agent?.Sex || 'N/A'}</span>
        </div>
    `;
    document.getElementById('agent_info').innerHTML = agentHtml;

    // Owner Info
    const ownerHtml = `
        <div class="d-flex align-items-center mb-3">
            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                <i class="bi bi-person-square text-white fs-4"></i>
            </div>
            <div>
                <div class="fw-bold">${transaction.owner?.Name || 'N/A'}</div>
                <small class="text-muted">${transaction.owner?.UserID || ''}</small>
            </div>
        </div>
        <div class="mb-2">
            <i class="bi bi-envelope me-2 text-muted"></i>
            <span>${transaction.owner?.Email || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-telephone me-2 text-muted"></i>
            <span>${transaction.owner?.Phone || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-card-text me-2 text-muted"></i>
            <span>${transaction.owner?.IdentityCard || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-geo-alt me-2 text-muted"></i>
            <span>${formatAddress(transaction.owner) || 'N/A'}</span>
        </div>
        <div class="mb-2">
            <i class="bi bi-calendar me-2 text-muted"></i>
            <span>${formatDate(transaction.owner?.Birth) || 'N/A'}</span>
        </div>
        <div>
            <i class="bi bi-gender-${transaction.owner?.Sex === 'Nam' ? 'male' : (transaction.owner?.Sex === 'Nữ' ? 'female' : 'ambiguous')} me-2 text-muted"></i>
            <span>${transaction.owner?.Sex || 'N/A'}</span>
        </div>
    `;
    document.getElementById('owner_info').innerHTML = ownerHtml;
}

function populatePaymentHistory(paymentDetails) {
    const tbody = document.getElementById('payment_history');

    if (!paymentDetails || paymentDetails.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-receipt display-4"></i>
                        <div class="mt-2">Chưa có lịch sử thanh toán</div>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    paymentDetails.forEach((payment, index) => {
        let statusBadge = '';
        switch(payment.DTran_Status) {
            case 'Hoàn Thành':
                statusBadge = '<span class="badge bg-success">Hoàn thành</span>';
                break;
            case 'Chờ đợi':
                statusBadge = '<span class="badge bg-warning">Chờ đợi</span>';
                break;
            case 'Hủy':
                statusBadge = '<span class="badge bg-danger">Đã hủy</span>';
                break;
            default:
                statusBadge = '<span class="badge bg-secondary">Không xác định</span>';
        }

        html += `
            <tr>
                <td>
                    <span class="badge bg-primary">${payment.Num_Pay}</span>
                </td>
                <td>${formatDateTime(payment.DTran_Date)}</td>
                <td>
                    <div class="fw-bold text-success">${formatCurrency(payment.Price)}</div>
                </td>
                <td>
                    ${payment.RentMonth ? `${payment.RentMonth} tháng` : 'N/A'}
                </td>
                <td>
                    <span class="badge bg-info">${payment.PaymentType || 'N/A'}</span>
                </td>
                <td>${statusBadge}</td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}

function populateDocuments(documents) {
    const container = document.getElementById('documents_list');
    const noDocuments = document.getElementById('no_documents');
    const count = document.getElementById('documents_count');

    count.textContent = documents.length;

    if (!documents || documents.length === 0) {
        container.innerHTML = '';
        noDocuments.classList.remove('d-none');
        return;
    }

    noDocuments.classList.add('d-none');

    let html = '';
    documents.forEach(document => {
        const fileExtension = document.DocumentType.toLowerCase();
        let iconClass = 'bi-file-earmark';
        let cardClass = 'border-secondary';

        switch(fileExtension) {
            case 'pdf':
                iconClass = 'bi-file-earmark-pdf';
                cardClass = 'border-danger';
                break;
            case 'doc':
            case 'docx':
                iconClass = 'bi-file-earmark-word';
                cardClass = 'border-primary';
                break;
            case 'xls':
            case 'xlsx':
                iconClass = 'bi-file-earmark-excel';
                cardClass = 'border-success';
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                iconClass = 'bi-file-earmark-image';
                cardClass = 'border-info';
                break;
        }

        html += `
            <div class="col-md-4">
                <div class="card border ${cardClass} h-100">
                    <div class="card-body text-center">
                        <i class="bi ${iconClass} display-4 text-muted mb-3"></i>
                        <h6 class="card-title">Tài liệu ${document.DocumentID}</h6>
                        <p class="card-text">
                            <small class="text-muted">
                                Loại: ${document.DocumentType}<br>
                                Ngày tải: ${formatDateTime(document.UploadedDate)}
                            </small>
                        </p>
                        <a href="${document.FilePath}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-download me-1"></i>Tải xuống
                        </a>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Helper functions
function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString('vi-VN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

function formatCurrency(amount) {
    if (!amount && amount !== 0) return 'N/A';
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

function formatAddress(person) {
    if (!person) return null;
    const parts = [person.Address, person.Ward, person.District, person.Province].filter(Boolean);
    return parts.length > 0 ? parts.join(', ') : null;
}

function getPropertyTypeName(typeId) {
    const types = {
        1: 'Đất Nền',
        2: 'Căn hộ chung cư',
        3: 'Chung cư mini',
        4: 'Nhà Riêng',
        5: 'Nhà biệt thự, liền kề',
        6: 'Nhà mặt phố',
        7: 'Shophouse, nhà phố thương mại'
    };
    return types[typeId] || 'Không xác định';
}

function printTransactionDetail() {
    // Implementation for printing transaction details
    window.print();
}

function printCommissionInvoice(commissionId) {
    // Implementation for printing commission invoice
    window.open(`{{ route('owner.transactions.commission-invoice', '') }}/${commissionId}`, '_blank');
}

// Toast notification functions
function showSuccessToast(message) {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    showToast(toastHtml);
}

function showErrorToast(message) {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-triangle me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    showToast(toastHtml);
}

function showToast(html) {
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    toastContainer.insertAdjacentHTML('beforeend', html);
    const toastElement = toastContainer.lastElementChild;

    // Show toast with fallback
    if (typeof bootstrap !== 'undefined') {
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    } else {
        // Manual toast display
        toastElement.style.display = 'block';
        toastElement.classList.add('show');

        // Auto hide after 5 seconds
        setTimeout(() => {
            toastElement.style.display = 'none';
            toastElement.remove();
        }, 5000);

        // Add close button functionality
        const closeBtn = toastElement.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                toastElement.style.display = 'none';
                toastElement.remove();
            });
        }
    }
}

// Success message handling
@if(session('success'))
    showSuccessToast('{{ session('success') }}');
@endif

@if(session('error'))
    showErrorToast('{{ session('error') }}');
@endif
</script>
@endpush
