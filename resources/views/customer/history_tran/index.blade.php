@extends('_layout._layhome.home')

@section('content')
<div class="container-fluid" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 100vh; padding: 2rem 0;">
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: white; overflow: hidden;">
                    <div class="card-body text-center py-4" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #8b4513;">
                        <h1 class="mb-2" style="font-weight: 700; font-size: 2rem;">
                            <i class="fas fa-history me-3"></i>Lịch Sử Giao Dịch
                        </h1>
                        <p class="mb-0 opacity-90">Quản lý và theo dõi các giao dịch bất động sản của bạn</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Đang tải dữ liệu...</p>
        </div>

        <!-- Transactions List -->
        <div id="transactionsList">
            @if($transactions->count() > 0)
                @foreach($transactions as $transaction)
            <div class="card mb-4 border-0 shadow-sm transaction-card" style="border-radius: 15px; border-left: 4px solid #3498db; overflow: hidden; transition: all 0.3s ease;">
                <!-- Transaction Header -->
                <div class="card-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 15px 15px 0 0; border-bottom: 1px solid #e9ecef;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-1" style="color: #2c3e50; font-weight: 600;">
                                <i class="fas fa-home me-2" style="color: #3498db;"></i>
                                {{ $transaction->trans_property->Title ?? 'N/A' }}
                            </h5>
                            <small class="text-muted">
                                <i class="fas fa-hashtag me-1"></i>Mã giao dịch: {{ $transaction->TransactionID }}
                            </small>
                        </div>
                        <div class="col-md-3 text-center">
                            <span class="badge bg-light text-dark px-3 py-2" style="border: 1px solid #dee2e6; border-radius: 20px;">
                                <i class="fas fa-calendar me-1"></i>
                                {{ date('d/m/Y', strtotime($transaction->TransactionDate)) }}
                            </span>
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-outline-info btn-sm" onclick="viewTransactionDetail('{{ $transaction->TransactionID }}')" style="border-radius: 20px;">
                                <i class="fas fa-file-alt me-1"></i>Xem chi tiết
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="card-body" style="background: white;">
                    <!-- Property Info -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tag me-2" style="color: #3498db;"></i>
                                <div>
                                    <small class="text-muted d-block">Loại giao dịch</small>
                                    <span class="fw-semibold">{{ $transaction->TransactionType == 'sale' ? 'Mua bán' : 'Cho thuê' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-money-bill-wave me-2" style="color: #27ae60;"></i>
                                <div>
                                    <small class="text-muted d-block">Tổng giá trị</small>
                                    <span class="fw-bold" style="color: #27ae60; font-size: 1.1rem;">
                                        {{ number_format($transaction->TotalPrice, 0, ',', '.') }} VNĐ
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2" style="color: #f39c12;"></i>
                                <div>
                                    <small class="text-muted d-block">Trạng thái</small>
                                    @if($transaction->TranStatus == 'Pending')
                                        <span class="badge bg-warning text-dark">Đang xử lý</span>
                                    @elseif($transaction->TranStatus == 'Paid')
                                        <span class="badge bg-success">Hoàn thành</span>
                                    @elseif($transaction->TranStatus == 'Cancelled')
                                        <span class="badge bg-danger">Đã hủy</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $transaction->TranStatus }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Payment Details -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mb-3" style="color: #2c3e50; border-bottom: 2px solid #ffd700; padding-bottom: 8px; cursor: pointer;"
                                onclick="togglePaymentDetails('payment-details-{{ $transaction->TransactionID }}')"
                                data-bs-toggle="collapse"
                                data-bs-target="#payment-details-{{ $transaction->TransactionID }}"
                                aria-expanded="false">
                                <i class="fas fa-credit-card me-2"></i>Lịch Thanh Toán
                                <i class="fas fa-chevron-down float-end"></i>
                            </h6>

                            <div class="collapse" id="payment-details-{{ $transaction->TransactionID }}">
                                @foreach($transaction->detailTransaction as $detail)
                                <div class="payment-item mb-3 p-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 12px; border: 1px solid #e9ecef; transition: all 0.3s ease;">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="badge bg-gradient-primary" style="width: 40px; height: 40px; line-height: 28px; border-radius: 50%; font-size: 0.9rem; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                                                    {{ $detail->Num_Pay }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Số tiền</small>
                                            <span class="fw-bold" style="color: #27ae60; font-size: 1.1rem;">
                                                {{ number_format($detail->Price, 0, ',', '.') }} VNĐ
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Ngày hạn</small>
                                            <span class="fw-medium">{{ date('d/m/Y', strtotime($detail->DTran_Date)) }}</span>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            @if($detail->DTran_Status == 'Hoàn Thành')
                                                <span class="badge bg-success px-3 py-2" style="border-radius: 20px;">
                                                    <i class="fas fa-check-circle me-1"></i>Đã thanh toán
                                                </span>
                                            @elseif($detail->DTran_Status == 'Chờ đợi')
                                                <button class="btn btn-primary btn-sm" onclick="showPaymentModal('{{ $transaction->TransactionID }}', '{{ $detail->Num_Pay }}', '{{ $detail->Price }}')" style="border-radius: 20px; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); border: none;">
                                                    <i class="fas fa-credit-card me-1"></i>Thanh toán ngay
                                                </button>
                                            @else
                                                <span class="badge bg-secondary px-3 py-2" style="border-radius: 20px;">
                                                    {{ $detail->DTran_Status }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body text-center py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-clipboard-list" style="font-size: 4rem; color: #bdc3c7;"></i>
                    </div>
                    <h4 style="color: #2c3e50; font-weight: 600;">Chưa có giao dịch nào</h4>
                    <p class="text-muted mb-4">Bạn chưa có giao dịch bất động sản nào. Hãy khám phá các bất động sản phù hợp với bạn!</p>
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg" style="border-radius: 25px; padding: 12px 30px;">
                        <i class="fas fa-search me-2"></i>Tìm kiếm bất động sản
                    </a>
                </div>
            </div>
        @endif
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <div class="modal-header" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); border-radius: 15px 15px 0 0; border: none;">
                <h5 class="modal-title text-white" id="paymentModalLabel">
                    <i class="fas fa-credit-card me-2"></i>Chọn Phương Thức Thanh Toán
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background: white; padding: 30px;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="payment-method-card" onclick="selectPaymentMethod('momo')" style="cursor: pointer; border: 2px solid #e9ecef; border-radius: 15px; padding: 30px; text-align: center; transition: all 0.3s ease; background: white; height: 180px; display: flex; flex-direction: column; justify-content: center;">
                            <div class="mb-3" style="width: 70px; height: 70px; background: linear-gradient(135deg, #ae2d68 0%, #c4407b 100%); border-radius: 15px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-money-check-alt" style="color: white; font-size: 28px;"></i>
                            </div>
                            <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 8px;">MoMo</h6>
                            <p class="text-muted small mb-0">Ví điện tử MoMo</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="payment-method-card" onclick="selectPaymentMethod('vnpay')" style="cursor: pointer; border: 2px solid #e9ecef; border-radius: 15px; padding: 30px; text-align: center; transition: all 0.3s ease; background: white; height: 180px; display: flex; flex-direction: column; justify-content: center;">
                            <div class="mb-3" style="width: 70px; height: 70px; background: linear-gradient(135deg, #006a73 0%, #008b96 100%); border-radius: 15px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-university" style="color: white; font-size: 28px;"></i>
                            </div>
                            <h6 style="color: #2c3e50; font-weight: 600; margin-bottom: 8px;">VNPAY</h6>
                            <p class="text-muted small mb-0">Cổng thanh toán VNPAY</p>
                        </div>
                    </div>
                </div>

                <div class="payment-info mt-4 p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border-radius: 12px; border: 1px solid #e9ecef;">
                    <h6 style="color: #2c3e50; margin-bottom: 15px;">
                        <i class="fas fa-info-circle me-2" style="color: #3498db;"></i>Thông tin thanh toán:
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Số tiền:</strong> <span id="paymentAmount" style="color: #27ae60; font-weight: bold; font-size: 1.1rem;"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Kỳ thanh toán:</strong> <span id="paymentPeriod" style="font-weight: 600;"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 20px 30px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 20px; padding: 10px 20px;">Hủy</button>
                <button type="button" class="btn btn-primary" id="proceedPayment" disabled style="border-radius: 20px; padding: 10px 30px; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); border: none;">
                    <i class="fas fa-arrow-right me-1"></i>Tiến hành thanh toán
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Detail Modal -->
<div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <div class="modal-header" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #8b4513; border-radius: 15px 15px 0 0; border: none;">
                <h5 class="modal-title" style="color: #8b4513;" id="transactionDetailModalLabel">
                    <i class="fas fa-file-contract me-2"></i>Chi Tiết Giao Dịch
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background: white; padding: 30px;">
                <div class="transaction-detail-content">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Detail Modal -->
<div class="modal fade" id="paymentDetailModal" tabindex="-1" aria-labelledby="paymentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <div class="modal-header" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); border-radius: 15px 15px 0 0; border: none;">
                <h5 class="modal-title text-white" id="paymentDetailModalLabel">
                    <i class="fas fa-file-invoice me-2"></i>Chi Tiết Thanh Toán
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background: #fefefe; padding: 30px;">
                <div class="payment-detail-content">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedPaymentMethod = '';
let currentTransactionId = '';
let currentNumPay = '';

function showPaymentModal(transactionId, numPay, amount) {
    currentTransactionId = transactionId;
    currentNumPay = numPay;

    document.getElementById('paymentAmount').textContent = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
    document.getElementById('paymentPeriod').textContent = `Kỳ ${numPay}`;

    // Reset selection
    selectedPaymentMethod = '';
    document.getElementById('proceedPayment').disabled = true;
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.style.background = '#fff';
        card.style.boxShadow = 'none';
    });

    // Get the modal element and force resize
    const modalElement = document.getElementById('paymentModal');
    const modalDialog = modalElement.querySelector('.modal-dialog');
    if (modalDialog) {
        modalDialog.style.setProperty('width', '95vw', 'important');
        modalDialog.style.setProperty('max-width', '95vw', 'important');
        modalDialog.style.setProperty('margin', '0.5rem auto', 'important');
    }

    var paymentModal = new bootstrap.Modal(modalElement);
    paymentModal.show();

    // Force resize after modal is shown
    modalElement.addEventListener('shown.bs.modal', function() {
        const dialog = this.querySelector('.modal-dialog');
        if (dialog) {
            dialog.style.setProperty('width', '95vw', 'important');
            dialog.style.setProperty('max-width', '95vw', 'important');
        }
    }, { once: true });
}

function selectPaymentMethod(method) {
    selectedPaymentMethod = method;

    // Reset all cards
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.style.background = '#fff';
        card.style.boxShadow = 'none';
        card.style.border = '2px solid #e9ecef';
    });

    // Highlight selected card
    event.currentTarget.style.background = 'linear-gradient(135deg, #e3f2fd 0%, #f1f8ff 100%)';
    event.currentTarget.style.boxShadow = '0 5px 15px rgba(52, 152, 219, 0.3)';
    event.currentTarget.style.border = '2px solid #3498db';

    document.getElementById('proceedPayment').disabled = false;
}

function viewPaymentDetail(transactionId, numPay) {
    // Load payment detail - you can make an AJAX call here
    const content = `
        <div class="text-center">
            <i class="fas fa-check-circle text-success" style="font-size: 3rem; margin-bottom: 20px;"></i>
            <h5 style="color: #2c3e50;">Thanh toán thành công!</h5>
            <p class="text-muted">Giao dịch ${transactionId} - Kỳ ${numPay}</p>
            <div class="mt-3 p-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border-radius: 10px; border: 1px solid #d1fae5;">
                <p class="mb-1"><strong>Ngày thanh toán:</strong> ${new Date().toLocaleDateString('vi-VN')}</p>
                <p class="mb-0"><strong>Trạng thái:</strong> <span class="badge bg-success">Đã thanh toán</span></p>
            </div>
        </div>
    `;

    document.querySelector('.payment-detail-content').innerHTML = content;
    var paymentDetailModal = new bootstrap.Modal(document.getElementById('paymentDetailModal'));
    paymentDetailModal.show();
}

function viewTransactionDetail(transactionId) {
    console.log('viewTransactionDetail called with ID:', transactionId);

    // Show loading
    const content = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Đang tải chi tiết giao dịch...</p>
        </div>
    `;

    document.querySelector('.transaction-detail-content').innerHTML = content;

    // Get the modal element
    const modalElement = document.getElementById('transactionDetailModal');
    console.log('Modal element:', modalElement);

    // Force maximum width before showing modal
    const modalDialog = modalElement.querySelector('.modal-dialog');
    if (modalDialog) {
        modalDialog.style.setProperty('width', '99vw', 'important');
        modalDialog.style.setProperty('max-width', '99vw', 'important');
        modalDialog.style.setProperty('margin', '0.25rem auto', 'important');
    }

    // Check if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        var transactionDetailModal = new bootstrap.Modal(modalElement);
        transactionDetailModal.show();

        // Force resize after modal is shown
        modalElement.addEventListener('shown.bs.modal', function() {
            const dialog = this.querySelector('.modal-dialog');
            if (dialog) {
                dialog.style.setProperty('width', '99vw', 'important');
                dialog.style.setProperty('max-width', '99vw', 'important');
            }
        }, { once: true });

    } else {
        // Fallback if Bootstrap is not available
        console.error('Bootstrap not found, using fallback');
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        modalElement.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');

        // Add backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);

        // Force resize in fallback mode
        if (modalDialog) {
            modalDialog.style.setProperty('width', '99vw', 'important');
            modalDialog.style.setProperty('max-width', '99vw', 'important');
        }
    }

    // Make AJAX request to get transaction details
    fetch(`{{ url('/customer/transaction') }}/${transactionId}/detail`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.text())
    .then(data => {
        document.querySelector('.transaction-detail-content').innerHTML = data;
    })
    .catch(error => {
        console.error('Error:', error);
        document.querySelector('.transaction-detail-content').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem; margin-bottom: 20px;"></i>
                <h5 style="color: #2c3e50;">Không thể tải chi tiết giao dịch</h5>
                <p class="text-muted">Vui lòng thử lại sau</p>
            </div>
        `;
    });
}

document.getElementById('proceedPayment').addEventListener('click', function() {
    if (!selectedPaymentMethod) return;

    // Show loading
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang xử lý...';
    this.disabled = true;

    // Make AJAX request
    fetch('{{ route("customer.payment.process") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            transaction_id: currentTransactionId,
            num_pay: currentNumPay,
            payment_method: selectedPaymentMethod
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to payment gateway
            window.location.href = data.redirect_url;
        } else {
            alert('Có lỗi xảy ra: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xử lý thanh toán');
    })
    .finally(() => {
        this.innerHTML = '<i class="fas fa-arrow-right me-1"></i>Tiến hành thanh toán';
        this.disabled = false;
    });
});

// Add hover effects for payment method cards
document.addEventListener('DOMContentLoaded', function() {
    // Force modal sizing on page load
    const allModals = document.querySelectorAll('.modal');
    allModals.forEach(modal => {
        const dialog = modal.querySelector('.modal-dialog');
        if (dialog) {
            // Apply maximum width to all modals
            if (modal.id === 'transactionDetailModal' || modal.id === 'paymentDetailModal') {
                dialog.style.setProperty('width', '99vw', 'important');
                dialog.style.setProperty('max-width', '99vw', 'important');
            } else if (modal.id === 'paymentModal') {
                dialog.style.setProperty('width', '95vw', 'important');
                dialog.style.setProperty('max-width', '95vw', 'important');
            }
            dialog.style.setProperty('margin', '0.25rem auto', 'important');
        }
    });

    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            if (!this.style.background.includes('linear-gradient(135deg, rgb(227, 242, 253)')) {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 5px 15px rgba(52, 152, 219, 0.2)';
            }
        });

        card.addEventListener('mouseleave', function() {
            if (!this.style.background.includes('linear-gradient(135deg, rgb(227, 242, 253)')) {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            }
        });
    });

    // Make sure Bootstrap is available before using any of its components
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not defined. Please make sure you have included Bootstrap JS properly.');
        // Add fallback if necessary
        window.bootstrap = {
            Modal: function(el) {
                this.show = function() {
                    if (el) el.style.display = 'block';
                };
            }
        };
    }

    // Override Bootstrap modal show method to force sizing
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const originalShow = bootstrap.Modal.prototype.show;
        bootstrap.Modal.prototype.show = function() {
            const result = originalShow.apply(this, arguments);

            // Force resize after showing
            setTimeout(() => {
                const dialog = this._element.querySelector('.modal-dialog');
                if (dialog) {
                    if (this._element.id === 'transactionDetailModal' || this._element.id === 'paymentDetailModal') {
                        dialog.style.setProperty('width', '99vw', 'important');
                        dialog.style.setProperty('max-width', '99vw', 'important');
                    } else if (this._element.id === 'paymentModal') {
                        dialog.style.setProperty('width', '95vw', 'important');
                        dialog.style.setProperty('max-width', '95vw', 'important');
                    }
                    dialog.style.setProperty('margin', '0.25rem auto', 'important');
                }
            }, 100);

            return result;
        };
    }
});

// Function to toggle payment details visibility
function togglePaymentDetails(id) {
    const chevronIcon = event.currentTarget.querySelector('.fas.fa-chevron-down');
    if (chevronIcon) {
        if (chevronIcon.classList.contains('fa-chevron-down')) {
            chevronIcon.classList.remove('fa-chevron-down');
            chevronIcon.classList.add('fa-chevron-up');
        } else {
            chevronIcon.classList.remove('fa-chevron-up');
            chevronIcon.classList.add('fa-chevron-down');
        }
    }
}
</script>

<style>
/* HIGHEST PRIORITY MODAL WIDTH OVERRIDE - Force maximum width */
html body .modal-dialog,
html body .modal.fade .modal-dialog,
html body .modal.show .modal-dialog,
html body .modal .modal-dialog,
#transactionDetailModal .modal-dialog,
#paymentModal .modal-dialog,
#paymentDetailModal .modal-dialog {
    max-width: none !important;
    width: 99vw !important;
    margin: 0.25rem auto !important;
}

/* Force specific modal sizes */
html body .modal-xl .modal-dialog,
html body .modal-lg .modal-dialog {
    max-width: none !important;
    width: 99vw !important;
    margin: 0.25rem auto !important;
}

/* Override Bootstrap's default modal classes */
html body .modal-xl,
html body .modal-lg,
html body .modal-sm {
    --bs-modal-width: 99vw !important;
}

/* Additional targeting for specific modals */
body #transactionDetailModal,
body #paymentModal,
body #paymentDetailModal {
    --bs-modal-width: 99vw !important;
}

body #transactionDetailModal .modal-dialog,
body #paymentModal .modal-dialog,
body #paymentDetailModal .modal-dialog {
    width: 99vw !important;
    max-width: 99vw !important;
    margin: 0.25rem auto !important;
}

/* Override any existing CSS with highest specificity */
.modal.fade .modal-dialog,
.modal.show .modal-dialog,
.modal .modal-dialog {
    width: 99vw !important;
    max-width: 99vw !important;
    margin: 0.25rem auto !important;
}

/* Responsive adjustments for mobile only */
@media (max-width: 575px) {
    html body .modal-dialog {
        width: 98vw !important;
        max-width: 98vw !important;
        margin: 0.5rem auto !important;
    }
}

/* Additional overrides for content sizing */
.modal-content {
    width: 100% !important;
    max-width: 100% !important;
}

.payment-method-card {
    transition: all 0.3s ease;
}

.payment-method-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.2);
}

.transaction-card {
    transition: all 0.3s ease;
}

.transaction-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(52, 152, 219, 0.15) !important;
}

.payment-item {
    transition: all 0.3s ease;
}

.payment-item:hover {
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.2);
    transform: translateY(-2px);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(52, 152, 219, 0.15) !important;
}

.badge {
    transition: all 0.3s ease;
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.empty-state-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

/* Responsive Improvements */
@media (max-width: 768px) {
    .modal-dialog {
        max-width: 90%;
    }

    .payment-method-card {
        padding: 20px !important;
        height: auto !important;
        margin-bottom: 20px;
    }

    .payment-info {
        padding: 15px !important;
    }

    .transaction-card .card-header .row > div {
        text-align: center !important;
        margin-bottom: 10px;
    }

    .payment-item .row > div {
        text-align: center !important;
        margin-bottom: 15px;
    }

    .modal-body {
        padding: 20px !important;
    }

    .container {
        padding: 0 15px;
    }
}

/* Custom modal sizing - Make modals much wider and taller */
.modal-dialog {
    margin: 1rem auto;
    display: flex;
    align-items: center;
    min-height: calc(100vh - 2rem);
}

.modal-content {
    min-height: 85vh;
    max-height: 95vh;
    display: flex;
    flex-direction: column;
}

.modal-body {
    min-height: 70vh;
    max-height: 80vh;
    overflow-y: auto;
    flex: 1;
    padding: 40px !important;
}

.transaction-detail-content,
.payment-detail-content {
    min-height: 500px;
}

/* Ensure modal header and footer don't take too much space */
.modal-header {
    flex-shrink: 0;
    padding: 20px 40px;
}

.modal-footer {
    flex-shrink: 0;
    padding: 20px 40px;
}

@media (min-width: 1200px) {
    .modal-xl {
        max-width: 98vw; /* 98% of viewport width - rất rộng */
        width: 98vw;
    }

    .modal-lg {
        max-width: 90vw; /* 90% of viewport width */
        width: 90vw;
    }

    .modal-xl .modal-content {
        min-height: 90vh;
        max-height: 95vh;
    }

    .modal-xl .modal-body {
        min-height: 75vh;
        max-height: 80vh;
        padding: 50px !important;
    }
}

@media (min-width: 992px) {
    .modal-lg {
        max-width: 90vw; /* 90% of viewport width - rộng hơn */
        width: 90vw;
    }

    .modal-xl {
        max-width: 98vw; /* 98% of viewport width - rất rộng */
        width: 98vw;
    }

    .modal-lg .modal-content {
        min-height: 80vh;
        max-height: 90vh;
    }

    .modal-lg .modal-body {
        min-height: 65vh;
        max-height: 75vh;
        padding: 40px !important;
    }

    .modal-xl .modal-body {
        min-height: 70vh;
        max-height: 80vh;
        padding: 50px !important;
    }
}

@media (min-width: 768px) {
    .modal-lg {
        max-width: 95vw; /* 95% of viewport width - rộng hơn */
        width: 95vw;
    }

    .modal-xl {
        max-width: 98vw; /* 98% of viewport width - rất rộng */
        width: 98vw;
    }

    .modal-lg .modal-content,
    .modal-xl .modal-content {
        min-height: 75vh;
        max-height: 85vh;
    }

    .modal-lg .modal-body,
    .modal-xl .modal-body {
        min-height: 60vh;
        max-height: 70vh;
        padding: 35px !important;
    }
}

@media (max-width: 767px) {
    .modal-dialog {
        margin: 0.5rem;
        min-height: calc(100vh - 1rem);
    }

    .modal-content {
        min-height: 80vh;
        max-height: 95vh;
    }

    .modal-body {
        min-height: 60vh;
        max-height: 75vh;
        padding: 25px !important;
    }

    .modal-header,
    .modal-footer {
        padding: 15px 25px;
    }
}
</style>
@endsection
