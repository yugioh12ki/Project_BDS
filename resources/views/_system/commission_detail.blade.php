@extends('_layout._layadmin.app')
@section('title', 'Chi tiết hoa hồng #' . $commission->CommissionID)

@section('commission')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-info-circle me-2"></i>Chi tiết hoa hồng #{{ $commission->CommissionID }}
        </h1>
        <div>
            @if(!$commission->PaidDate)
                <button type="button" class="btn btn-success me-2" onclick="handlePayment()">
                    <i class="fas fa-credit-card me-1"></i>Thanh toán
                </button>
            @endif
            <button type="button" class="btn btn-secondary" onclick="window.close()">
                <i class="fas fa-times me-1"></i>Đóng
            </button>
        </div>
    </div>

    <!-- Commission Details -->
    <div class="row">
        <!-- Commission Information -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-percentage me-2"></i>Thông tin hoa hồng
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted">Mã hoa hồng:</td>
                            <td>{{ $commission->CommissionID }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Loại:</td>
                            <td><span class="badge bg-info">{{ $commission->TypeCom }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Tỷ lệ:</td>
                            <td class="text-success fw-bold">{{ $commission->Percentage ?? $commission->Commission_Rate ?? 'N/A' }}%</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Số tiền:</td>
                            <td class="text-primary fw-bold">{{ number_format($commission->Amount ?? $commission->Commission_Amount ?? 0, 0) }} VNĐ</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Trạng thái:</td>
                            <td>
                                @if($commission->PaidDate)
                                    <span class="badge bg-success">Đã thanh toán</span>
                                @else
                                    <span class="badge bg-warning">Chờ thanh toán</span>
                                @endif
                            </td>
                        </tr>
                        @if($commission->PaidDate)
                        <tr>
                            <td class="fw-bold text-muted">Ngày TT:</td>
                            <td>{{ \Carbon\Carbon::parse($commission->PaidDate)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="fw-bold text-muted">Ngày tạo:</td>
                            <td>{{ $commission->created_at ? $commission->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Agent Information -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i>Thông tin môi giới
                    </h5>
                </div>
                <div class="card-body">
                    @if($commission->comm_agent)
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted">Tên:</td>
                                <td>{{ $commission->comm_agent->Name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Email:</td>
                                <td>{{ $commission->comm_agent->Email }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Số ĐT:</td>
                                <td>{{ $commission->comm_agent->Phone }}</td>
                            </tr>
                        </table>
                    @else
                        <p class="text-muted">Chưa có thông tin môi giới</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Transaction Information -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-handshake me-2"></i>Thông tin giao dịch
                    </h5>
                </div>
                <div class="card-body">
                    @if($commission->comm_trans)
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted">Mã GD:</td>
                                <td><span class="badge bg-primary">{{ $commission->TransactionID }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Tổng giá trị:</td>
                                <td class="text-success fw-bold">{{ number_format($commission->comm_trans->TotalPrice, 0) }} VNĐ</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Ngày GD:</td>
                                <td>{{ \Carbon\Carbon::parse($commission->comm_trans->TransactionDate)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Loại GD:</td>
                                <td><span class="badge bg-info">{{ $commission->comm_trans->TransactionType ?? $commission->TypeCom }}</span></td>
                            </tr>
                            @if($commission->comm_trans->trans_property)
                            <tr>
                                <td class="fw-bold text-muted">BĐS:</td>
                                <td>{{ $commission->comm_trans->trans_property->Title }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Địa chỉ:</td>
                                <td class="text-muted small">{{ $commission->comm_trans->trans_property->Address }}</td>
                            </tr>
                            @endif
                        </table>
                    @else
                        <p class="text-muted">Chưa có thông tin giao dịch</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    @if($commission->comm_trans && $commission->comm_trans->detailTransaction && $commission->comm_trans->detailTransaction->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>Lịch sử thanh toán
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Lần thanh toán</th>
                                    <th>Số tiền</th>
                                    <th>Ngày thanh toán</th>
                                    <th>Loại thanh toán</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commission->comm_trans->detailTransaction as $payment)
                                <tr>
                                    <td><span class="badge bg-primary">{{ $payment->Num_Pay }}</span></td>
                                    <td class="text-success fw-bold">{{ number_format($payment->Price, 0) }} VNĐ</td>
                                    <td>{{ \Carbon\Carbon::parse($payment->DTran_Date)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($payment->PaymentType)
                                            <span class="badge bg-info">{{ $payment->PaymentType }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
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
    @endif
</div>

<script>
function handlePayment() {
    // Handle payment functionality
    alert('Chức năng thanh toán sẽ được triển khai.');
}

// Auto-close handling for popup windows
window.addEventListener('beforeunload', function() {
    if (window.opener) {
        // Optionally refresh parent window
        // window.opener.location.reload();
    }
});
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table td {
    vertical-align: middle;
    padding: 0.5rem;
}

.badge {
    font-size: 0.8em;
}

.text-muted.small {
    font-size: 0.875em;
}
</style>
@endsection
