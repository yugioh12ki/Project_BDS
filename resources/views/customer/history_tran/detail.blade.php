<!-- Transaction Detail Content -->
<div class="container-fluid">
    <!-- Transaction Info Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="mb-2" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-home me-2" style="color: #3498db;"></i>
                                {{ $transaction->trans_property->Title ?? 'N/A' }}
                            </h4>
                            <p class="text-muted mb-1">
                                <i class="fas fa-hashtag me-1"></i>
                                Mã giao dịch: <strong>{{ $transaction->TransactionID }}</strong>
                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar me-1"></i>
                                Ngày tạo: {{ date('d/m/Y H:i', strtotime($transaction->TransactionDate)) }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge fs-6 px-3 py-2" style="border-radius: 20px;
                                @if($transaction->TranStatus == 'Pending')
                                    background: #f39c12; color: white;
                                @elseif($transaction->TranStatus == 'Paid')
                                    background: #27ae60; color: white;
                                @elseif($transaction->TranStatus == 'Cancelled')
                                    background: #e74c3c; color: white;
                                @else
                                    background: #95a5a6; color: white;
                                @endif
                            ">
                                @if($transaction->TranStatus == 'Pending')
                                    <i class="fas fa-clock me-1"></i>Đang xử lý
                                @elseif($transaction->TranStatus == 'Paid')
                                    <i class="fas fa-check-circle me-1"></i>Hoàn thành
                                @elseif($transaction->TranStatus == 'Cancelled')
                                    <i class="fas fa-times-circle me-1"></i>Đã hủy
                                @else
                                    {{ $transaction->TranStatus }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Property & Transaction Info -->
        <div class="col-md-6">
            <!-- Property Information -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light" style="border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0" style="color: #2c3e50;">
                        <i class="fas fa-building me-2" style="color: #3498db;"></i>Thông Tin Bất Động Sản
                    </h6>
                </div>
                <div class="card-body">
                    @if($transaction->trans_property->images->first())
                    <img src="{{ asset('storage/' . $transaction->trans_property->images->first()->ImagePath) }}"
                         class="img-fluid rounded mb-3"
                         style="width: 100%; height: 200px; object-fit: cover;"
                         alt="Property Image">
                    @endif

                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted d-block">Loại BDS</small>
                            <span class="fw-semibold">{{ $transaction->trans_property->danhMuc->ten_pro ?? 'N/A' }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Diện tích</small>
                            <span class="fw-semibold">{{ $transaction->trans_property->chiTiet->Area ?? 'N/A' }} m²</span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted d-block">Địa chỉ</small>
                            <span class="fw-semibold">{{ $transaction->trans_property->Address }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Summary -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light" style="border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0" style="color: #2c3e50;">
                        <i class="fas fa-calculator me-2" style="color: #27ae60;"></i>Tóm Tắt Giao Dịch
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Loại giao dịch</small>
                            <span class="fw-semibold">{{ $transaction->TransactionType == 'sale' ? 'Mua bán' : 'Cho thuê' }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Tổng số đợt</small>
                            <span class="fw-semibold">{{ $transaction->detailTransaction->count() }} đợt</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <small class="text-muted d-block">Tổng giá trị</small>
                            <h5 class="fw-bold" style="color: #27ae60;">
                                {{ number_format($transaction->TotalPrice, 0, ',', '.') }} VNĐ
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Payment Schedule & Documents -->
        <div class="col-md-6">
            <!-- Payment Schedule -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light" style="border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0" style="color: #2c3e50;">
                        <i class="fas fa-credit-card me-2" style="color: #3498db;"></i>Lịch Thanh Toán
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($transaction->detailTransaction as $detail)
                    <div class="payment-schedule-item mb-3 p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid
                        @if($detail->DTran_Status == 'Hoàn Thành') #27ae60 @else #f39c12 @endif">
                        <div class="row align-items-center">
                            <div class="col-2">
                                <div class="text-center">
                                    <div class="badge" style="width: 35px; height: 35px; line-height: 25px; border-radius: 50%;
                                        background: @if($detail->DTran_Status == 'Hoàn Thành') #27ae60 @else #f39c12 @endif; color: white;">
                                        {{ $detail->Num_Pay }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-5">
                                <small class="text-muted d-block">Số tiền</small>
                                <span class="fw-bold" style="color: #27ae60;">
                                    {{ number_format($detail->Price, 0, ',', '.') }} VNĐ
                                </span>
                            </div>
                            <div class="col-5 text-end">
                                <small class="text-muted d-block">{{ date('d/m/Y', strtotime($detail->DTran_Date)) }}</small>
                                @if($detail->DTran_Status == 'Hoàn Thành')
                                    <span class="badge bg-success">Đã thanh toán</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Documents -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-light" style="border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0" style="color: #2c3e50;">
                        <i class="fas fa-folder-open me-2" style="color: #e67e22;"></i>Tài Liệu Giao Dịch
                        @if($documents->count() > 0)
                            <span class="badge bg-primary ms-2">{{ $documents->count() }} tài liệu</span>
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if($documents->count() > 0)
                        @foreach($documents as $document)
                        <div class="document-item mb-3 p-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 10px; border: 1px solid #e9ecef; transition: all 0.3s ease;">
                            <div class="row align-items-center">
                                <div class="col-1 text-center">
                                    @php
                                        $extension = strtolower(pathinfo($document->FilePath, PATHINFO_EXTENSION));
                                        $iconClass = match($extension) {
                                            'pdf' => 'fas fa-file-pdf text-danger',
                                            'doc', 'docx' => 'fas fa-file-word text-primary',
                                            'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-warning',
                                            'txt' => 'fas fa-file-alt text-secondary',
                                            default => 'fas fa-file text-muted'
                                        };
                                    @endphp
                                    <i class="{{ $iconClass }}" style="font-size: 1.5rem;"></i>
                                </div>
                                <div class="col-7">
                                    <div class="document-info">
                                        <span class="badge bg-light text-dark mb-1" style="font-size: 0.7rem;">{{ $document->DocumentType }}</span>
                                        <h6 class="mb-1 fw-semibold" style="color: #2c3e50;">{{ $document->DocumentName }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ date('d/m/Y H:i', strtotime($document->created_at ?? now())) }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customer.document.view', $document->DocumentID) }}"
                                           class="btn btn-outline-primary btn-sm document-btn"
                                           target="_blank"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="Xem tài liệu"
                                           style="border-radius: 20px 0 0 20px; border-right: none;">
                                            <i class="fas fa-eye me-1"></i>Xem
                                        </a>
                                        <a href="{{ route('customer.document.download', $document->DocumentID) }}"
                                           class="btn btn-outline-success btn-sm document-btn"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="Tải xuống tài liệu"
                                           style="border-radius: 0 20px 20px 0;">
                                            <i class="fas fa-download me-1"></i>Tải
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <div class="empty-document-icon mb-3">
                                <i class="fas fa-folder-open" style="font-size: 3rem; color: #bdc3c7;"></i>
                            </div>
                            <h6 style="color: #2c3e50;">Chưa có tài liệu nào</h6>
                            <p class="mb-0" style="font-size: 0.9rem;">Tài liệu giao dịch sẽ được cập nhật khi có</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Logs (if any) -->
    @if($transaction->transactionlog->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-light" style="border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0" style="color: #2c3e50;">
                        <i class="fas fa-history me-2" style="color: #9b59b6;"></i>Lịch Sử Giao Dịch
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($transaction->transactionlog as $log)
                    <div class="log-item mb-2 p-2" style="background: #f8f9fa; border-radius: 6px;">
                        <div class="row">
                            <div class="col-9">
                                <span class="fw-semibold">{{ $log->ActionType }}</span>
                                <small class="text-muted d-block">{{ $log->LogDetails }}</small>
                            </div>
                            <div class="col-3 text-end">
                                <small class="text-muted">{{ date('d/m/Y H:i', strtotime($log->LogDate)) }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
/* Document Item Styling */
.document-item {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef !important;
}

.document-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.15) !important;
    border-color: #3498db !important;
}

/* Document Button Styling */
.document-btn {
    transition: all 0.3s ease;
    font-weight: 500;
    padding: 8px 16px;
    border-width: 1.5px !important;
}

.document-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Specific button colors */
.btn-outline-primary.document-btn:hover {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    border-color: #2980b9;
    color: white;
}

.btn-outline-success.document-btn:hover {
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
    border-color: #229954;
    color: white;
}

/* File type icons animation */
.document-item .fas {
    transition: all 0.3s ease;
}

.document-item:hover .fas {
    transform: scale(1.1);
}

/* Empty state animation */
.empty-document-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}

/* Button group styling */
.btn-group .document-btn:first-child {
    border-right: 1px solid #dee2e6 !important;
}

.btn-group .document-btn:hover:first-child {
    border-right: 1px solid #2980b9 !important;
}

.btn-group .document-btn:hover:last-child {
    border-left: 1px solid #229954 !important;
}

/* Tooltip styling */
.tooltip {
    font-size: 0.875rem;
}

.tooltip-inner {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    border-radius: 6px;
    padding: 8px 12px;
}

/* Document info styling */
.document-info h6 {
    transition: color 0.3s ease;
}

.document-item:hover .document-info h6 {
    color: #3498db !important;
}

/* Badge styling */
.badge.bg-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border: 1px solid #dee2e6;
    font-weight: 500;
}

/* Document count badge */
.card-header .badge.bg-primary {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%) !important;
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 12px;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .document-item .col-1 {
        flex: 0 0 auto;
        width: 15%;
    }

    .document-item .col-7 {
        flex: 0 0 auto;
        width: 50%;
    }

    .document-item .col-4 {
        flex: 0 0 auto;
        width: 35%;
    }

    .document-btn {
        padding: 6px 10px;
        font-size: 0.8rem;
    }

    .document-btn i {
        margin-right: 0 !important;
    }

    .document-btn span {
        display: none;
    }

    .btn-group .document-btn:first-child {
        border-radius: 15px 0 0 15px !important;
    }

    .btn-group .document-btn:last-child {
        border-radius: 0 15px 15px 0 !important;
    }
}

@media (max-width: 576px) {
    .document-item {
        padding: 15px !important;
    }

    .document-item .row {
        text-align: center;
    }

    .document-item .col-1,
    .document-item .col-7,
    .document-item .col-4 {
        flex: 0 0 auto;
        width: 100%;
        margin-bottom: 10px;
    }

    .document-item .col-4 {
        margin-bottom: 0;
    }
}
</style>

<script>
// Initialize tooltips for document buttons
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Add click feedback for document buttons
    document.querySelectorAll('.document-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.6)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.left = (e.offsetX - 10) + 'px';
            ripple.style.top = (e.offsetY - 10) + 'px';
            ripple.style.width = '20px';
            ripple.style.height = '20px';
            ripple.style.pointerEvents = 'none';

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// Add ripple animation CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
