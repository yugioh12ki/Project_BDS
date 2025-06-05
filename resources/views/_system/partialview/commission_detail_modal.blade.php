<!-- Commission Detail Modal Content -->
<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">
        <i class="fas fa-info-circle me-2"></i>Chi tiết hoa hồng #{{ $commission->CommissionID }}
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="row">
        <!-- Commission Information -->
        <div class="col-md-4">
            <div class="detail-section mb-4">
                <h6 class="text-primary border-bottom pb-2 mb-3">
                    <i class="fas fa-percentage me-2"></i>Thông tin hoa hồng
                </h6>
                <div class="detail-item mb-2">
                    <span class="fw-bold text-muted">Mã hoa hồng:</span>
                    <span class="ms-2">{{ $commission->CommissionID }}</span>
                </div>
                <div class="detail-item mb-2">
                    <span class="fw-bold text-muted">Loại:</span>
                    <span class="ms-2 badge bg-info">{{ $commission->TypeCom }}</span>
                </div>
                <div class="detail-item mb-2">
                    <span class="fw-bold text-muted">Tỷ lệ:</span>
                    <span class="ms-2 text-success fw-bold">{{ $commission->Percentage }}%</span>
                </div>
                <div class="detail-item mb-2">
                    <span class="fw-bold text-muted">Số tiền:</span>
                    <span class="ms-2 text-primary fw-bold">{{ number_format($commission->Amount, 0) }} VNĐ</span>
                </div>
                <div class="detail-item mb-2">
                    <span class="fw-bold text-muted">Trạng thái:</span>
                    @if($commission->PaidDate)
                        <span class="ms-2 badge bg-success">Đã thanh toán</span>
                    @else
                        <span class="ms-2 badge bg-warning">Chờ thanh toán</span>
                    @endif
                </div>
                @if($commission->PaidDate)
                    <div class="detail-item mb-2">
                        <span class="fw-bold text-muted">Ngày TT:</span>
                        <span class="ms-2">{{ \Carbon\Carbon::parse($commission->PaidDate)->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Agent Information -->
        <div class="col-md-4">
            <div class="detail-section mb-4">
                <h6 class="text-success border-bottom pb-2 mb-3">
                    <i class="fas fa-user-tie me-2"></i>Thông tin môi giới
                </h6>
                @if($commission->comm_agent)
                    <div class="detail-item mb-2">
                        <span class="fw-bold text-muted">Tên:</span>
                        <span class="ms-2">{{ $commission->comm_agent->Name }}</span>
                    </div>
                    <div class="detail-item mb-2">
                        <span class="fw-bold text-muted">Điện thoại:</span>
                        <span class="ms-2">{{ $commission->comm_agent->Phone }}</span>
                    </div>
                    <div class="detail-item mb-2">
                        <span class="fw-bold text-muted">Email:</span>
                        <span class="ms-2">{{ $commission->comm_agent->Email ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item mb-2">
                        <span class="fw-bold text-muted">Mã Agent:</span>
                        <span class="ms-2 badge bg-secondary">{{ $commission->AgentID }}</span>
                    </div>
                @else
                    <p class="text-muted">Chưa có thông tin môi giới</p>
                @endif
            </div>
        </div>

        <!-- Transaction Information -->
        <div class="col-md-4">
            <div class="detail-section mb-4">
                <h6 class="text-info border-bottom pb-2 mb-3">
                    <i class="fas fa-handshake me-2"></i>Thông tin giao dịch
                </h6>
                @if($commission->comm_trans)
                    <div class="detail-item mb-2">
                        <span class="fw-bold text-muted">Mã GD:</span>
                        <span class="ms-2 badge bg-primary">{{ $commission->TransactionID }}</span>
                    </div>
                    <div class="detail-item mb-2">
                        <span class="fw-bold text-muted">Tổng giá trị:</span>
                        <span class="ms-2 text-success fw-bold">{{ number_format($commission->comm_trans->TotalPrice, 0) }} VNĐ</span>
                    </div>
                    <div class="detail-item mb-2">
                        <span class="fw-bold text-muted">Ngày GD:</span>
                        <span class="ms-2">{{ \Carbon\Carbon::parse($commission->comm_trans->TransactionDate)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="detail-item mb-2">
                        <span class="fw-bold text-muted">Loại GD:</span>
                        <span class="ms-2 badge bg-info">{{ $commission->comm_trans->TransactionType ?? $commission->TypeCom }}</span>
                    </div>
                    @if($commission->comm_trans->trans_property)
                        <div class="detail-item mb-2">
                            <span class="fw-bold text-muted">BĐS:</span>
                            <span class="ms-2">{{ $commission->comm_trans->trans_property->Title }}</span>
                        </div>
                        <div class="detail-item mb-2">
                            <span class="fw-bold text-muted">Địa chỉ:</span>
                            <span class="ms-2 text-muted small">{{ $commission->comm_trans->trans_property->Address }}</span>
                        </div>
                    @endif
                @else
                    <p class="text-muted">Chưa có thông tin giao dịch</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment History if available -->
    @if($commission->comm_trans && $commission->comm_trans->detailTransaction && $commission->comm_trans->detailTransaction->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="detail-section">
                <h6 class="text-warning border-bottom pb-2 mb-3">
                    <i class="fas fa-credit-card me-2"></i>Lịch sử thanh toán
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Lần TT</th>
                                <th>Số tiền</th>
                                <th>Ngày TT</th>
                                <th>Loại TT</th>
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
    @endif
</div>

<div class="modal-footer">
    @if(!$commission->PaidDate)
        <button type="button" class="btn btn-success">
            <i class="fas fa-credit-card me-1"></i>Thanh toán
        </button>
    @endif
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
</div>

<style>
.detail-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border: 1px solid #e9ecef;
}

.detail-item {
    display: flex;
    align-items: center;
    padding: 4px 0;
}

.detail-item .fw-bold {
    min-width: 100px;
    flex-shrink: 0;
}
</style>
