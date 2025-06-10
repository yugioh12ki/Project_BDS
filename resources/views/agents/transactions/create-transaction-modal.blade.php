<!-- Modal Tạo Giao Dịch Mới với 4 bước thực sự -->
<style>
/* Enhanced 4-Step Modal Styles */
.create-transaction-modal {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.create-transaction-modal .modal-body {
    padding: 30px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

/* Progress Steps Container */
.create-transaction-modal .steps-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    position: relative;
    padding: 0 30px;
}

.create-transaction-modal .steps-container::before {
    content: '';
    position: absolute;
    top: 25px;
    left: 80px;
    right: 80px;
    height: 3px;
    background: linear-gradient(90deg, #e9ecef 0%, #dee2e6 100%);
    border-radius: 2px;
    z-index: 1;
}

.create-transaction-modal .steps-container .progress-line {
    position: absolute;
    top: 25px;
    left: 80px;
    height: 3px;
    background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    border-radius: 2px;
    transition: width 0.5s ease;
    z-index: 2;
    width: 0%;
}

.create-transaction-modal .step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 3;
    cursor: pointer;
    transition: all 0.3s ease;
}

.create-transaction-modal .step-circle {
    width: 50px;
    height: 50px;
    border: 3px solid #dee2e6;
    background: #ffffff;
    color: #6c757d;
    font-weight: bold;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.create-transaction-modal .step-label {
    font-size: 12px;
    color: #6c757d;
    text-align: center;
    max-width: 90px;
    line-height: 1.3;
    font-weight: 500;
    transition: all 0.3s ease;
}

/* Step States */
.create-transaction-modal .step-item.completed .step-circle {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border-color: #28a745;
    transform: scale(1.05);
}

.create-transaction-modal .step-item.completed .step-circle i {
    animation: checkmark 0.5s ease-in-out;
}

.create-transaction-modal .step-item.completed .step-label {
    color: #28a745;
    font-weight: 600;
}

.create-transaction-modal .step-item.active .step-circle {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-color: #007bff;
    transform: scale(1.1);
    animation: pulse 2s infinite;
}

.create-transaction-modal .step-item.active .step-label {
    color: #007bff;
    font-weight: 600;
}

/* Step Content Container */
.create-transaction-modal .step-content-container {
    min-height: 500px;
    position: relative;
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
}

.create-transaction-modal .step-content {
    display: none;
    animation: fadeInUp 0.5s ease-in-out;
}

.create-transaction-modal .step-content.active {
    display: block;
}

/* Step Headers */
.create-transaction-modal .step-header {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
}

.create-transaction-modal .step-header .step-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 18px;
}

.create-transaction-modal .step-header h5 {
    margin: 0;
    font-weight: 600;
    color: #2c3e50;
}

/* Property Cards */
.create-transaction-modal .property-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    background: white;
    position: relative;
    overflow: hidden;
}

.create-transaction-modal .property-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0,123,255,0.1), transparent);
    transition: left 0.5s ease;
}

.create-transaction-modal .property-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,123,255,0.15);
    border-color: #007bff;
}

.create-transaction-modal .property-card:hover::before {
    left: 100%;
}

.create-transaction-modal .property-card.selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #f0fff4 100%);
    box-shadow: 0 8px 25px rgba(40,167,69,0.2);
}

.create-transaction-modal .property-card .property-price {
    font-size: 18px;
    font-weight: 700;
    color: #e74c3c;
    margin-bottom: 8px;
}

.create-transaction-modal .property-card .property-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 5px;
}

.create-transaction-modal .property-card .property-address {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 10px;
}

/* Document Upload Zone */
.create-transaction-modal .upload-zone {
    border: 3px dashed #dee2e6;
    border-radius: 15px;
    padding: 50px 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 25px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.create-transaction-modal .upload-zone:hover {
    border-color: #007bff;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    transform: translateY(-2px);
}

.create-transaction-modal .upload-zone.dragover {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%);
    transform: scale(1.02);
}

.create-transaction-modal .upload-zone .upload-icon {
    font-size: 48px;
    color: #007bff;
    margin-bottom: 15px;
}

/* Contract Template Items */
.create-transaction-modal .template-item {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.create-transaction-modal .template-item:hover {
    border-color: #007bff;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    transform: translateY(-2px);
}

.create-transaction-modal .template-item.selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #f0fff4 100%);
    transform: translateY(-2px);
}

/* Transaction Type Cards */
.create-transaction-modal .transaction-type-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 30px 20px;
    text-align: center;
    margin-bottom: 20px;
    background: white;
    position: relative;
    overflow: hidden;
}

.create-transaction-modal .transaction-type-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.create-transaction-modal .transaction-type-card:hover {
    border-color: #007bff;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,123,255,0.15);
}

.create-transaction-modal .transaction-type-card:hover::before {
    transform: scaleX(1);
}

.create-transaction-modal .transaction-type-card.selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #f0fff4 100%);
    box-shadow: 0 8px 25px rgba(40,167,69,0.2);
}

.create-transaction-modal .transaction-type-card.selected::before {
    background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    transform: scaleX(1);
}

.create-transaction-modal .transaction-type-icon {
    font-size: 36px;
    margin-bottom: 15px;
    color: #007bff;
}

.create-transaction-modal .transaction-type-card.selected .transaction-type-icon {
    color: #28a745;
}

/* Payment Method Cards */
.create-transaction-modal .payment-method {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    margin-bottom: 15px;
    background: white;
    position: relative;
}

.create-transaction-modal .payment-method:hover {
    border-color: #007bff;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,123,255,0.15);
}

.create-transaction-modal .payment-method.selected {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff8 0%, #f0fff4 100%);
    box-shadow: 0 6px 20px rgba(40,167,69,0.2);
}

.create-transaction-modal .payment-icon {
    font-size: 32px;
    margin-bottom: 10px;
    color: #007bff;
}

.create-transaction-modal .payment-method.selected .payment-icon {
    color: #28a745;
}

/* Form Elements */
.create-transaction-modal .form-control,
.create-transaction-modal .form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
    transition: all 0.3s ease;
    font-size: 14px;
}

.create-transaction-modal .form-control:focus,
.create-transaction-modal .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    transform: translateY(-1px);
}

.create-transaction-modal .form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

/* Navigation Buttons */
.create-transaction-modal .step-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f8f9fa;
}

.create-transaction-modal .btn-step {
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
}

.create-transaction-modal .btn-prev {
    background: #6c757d;
    color: white;
}

.create-transaction-modal .btn-prev:hover {
    background: #5a6268;
    transform: translateX(-3px);
}

.create-transaction-modal .btn-next {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
}

.create-transaction-modal .btn-next:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    transform: translateX(3px);
}

.create-transaction-modal .btn-submit {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.create-transaction-modal .btn-submit:hover {
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
    transform: scale(1.05);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(0,123,255,0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(0,123,255,0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(0,123,255,0);
    }
}

@keyframes checkmark {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
    }
}

/* Hidden utility classes */
.create-transaction-modal .d-none {
    display: none !important;
}

.create-transaction-modal .fade-in {
    animation: fadeInUp 0.5s ease-in-out;
}

/* Customer Search Styles */
.create-transaction-modal .customer-search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
}

.create-transaction-modal .customer-search-item {
    padding: 12px 15px;
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.create-transaction-modal .customer-search-item:hover {
    background-color: #f8f9fa;
}

.create-transaction-modal .customer-search-item:last-child {
    border-bottom: none;
}

.create-transaction-modal .selected-customer-card {
    background: linear-gradient(135deg, #f8fff8 0%, #f0fff4 100%);
    border: 2px solid #28a745;
    border-radius: 12px;
    padding: 20px;
}

.create-transaction-modal .customer-detail {
    margin-bottom: 8px;
    font-size: 14px;
}

.create-transaction-modal .customer-detail strong {
    color: #2c3e50;
    min-width: 120px;
    display: inline-block;
}

/* Payment Schedule Styles */
.create-transaction-modal .payment-schedule-item {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.create-transaction-modal .payment-schedule-item.deposit {
    border-color: #007bff;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
}

.create-transaction-modal .payment-info h6 {
    margin: 0;
    color: #2c3e50;
    font-size: 16px;
}

.create-transaction-modal .payment-amount {
    font-size: 18px;
    font-weight: 700;
    color: #e74c3c;
}

.create-transaction-modal .payment-date {
    font-size: 12px;
    color: #6c757d;
}

/* Transaction Type Specific Fields */
.create-transaction-modal .rent-transaction-fields,
.create-transaction-modal .sale-transaction-fields {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e9ecef;
}

.create-transaction-modal .deposit-contract-fields {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border: 1px solid #007bff;
    border-radius: 8px;
    padding: 20px;
    margin-top: 15px;
}
</style>

<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-labelledby="createTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content create-transaction-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="createTransactionModalLabel">
                    <i class="fas fa-handshake text-primary me-2"></i>
                    Tạo Giao Dịch Mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Progress Steps -->
                <div class="steps-container">
                    <div class="progress-line" id="progressLine"></div>
                    <div class="step-item active" data-step="1">
                        <div class="step-circle">
                            <span class="step-number">1</span>
                            <i class="fas fa-check d-none"></i>
                        </div>
                        <div class="step-label">Chọn Bất Động Sản</div>
                    </div>
                    <div class="step-item" data-step="2">
                        <div class="step-circle">
                            <span class="step-number">2</span>
                            <i class="fas fa-check d-none"></i>
                        </div>
                        <div class="step-label">Quản Lý Tài Liệu</div>
                    </div>
                    <div class="step-item" data-step="3">
                        <div class="step-circle">
                            <span class="step-number">3</span>
                            <i class="fas fa-check d-none"></i>
                        </div>
                        <div class="step-label">Chi Tiết Giao Dịch</div>
                    </div>
                    <div class="step-item" data-step="4">
                        <div class="step-circle">
                            <span class="step-number">4</span>
                            <i class="fas fa-check d-none"></i>
                        </div>
                        <div class="step-label">Thanh Toán</div>
                    </div>
                </div>

                <!-- Step Content Container -->
                <div class="step-content-container">
                    <!-- Step 1: Property Selection -->
                    <div class="step-content active" id="step1">
                        <div class="step-header">
                            <div class="step-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <h5>Chọn Bất Động Sản</h5>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <p class="text-muted mb-4">Chọn bất động sản mà bạn được phân công để tạo giao dịch:</p>
                                <div id="propertiesContainer">
                                    <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Đang tải...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Document Management -->
                    <div class="step-content" id="step2">
                        <div class="step-header">
                            <div class="step-icon">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <h5>Quản Lý Tài Liệu</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3">Tải lên tài liệu tùy chỉnh</h6>
                                <input type="file" id="createModalDocumentUpload" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display: none;">
                                <div class="upload-zone" id="createModalUploadZone">
                                    <div class="upload-icon">
                                        <i class="fas fa-file-upload"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-2">Kéo thả tài liệu hoặc click để chọn</h6>
                                    <p class="text-muted mb-0">Chỉ hỗ trợ: PDF, DOC, DOCX (Tối đa 10MB)</p>
                                </div>
                                <div id="uploadedFiles" class="mt-3"></div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3">Hoặc chọn từ mẫu hợp đồng có sẵn</h6>
                                <div id="contractTemplates">
                                    <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Đang tải...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Transaction Details -->
                    <div class="step-content" id="step3">
                        <div class="step-header">
                            <div class="step-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h5>Chi Tiết Giao Dịch</h5>
                        </div>

                        <!-- Transaction Type Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-semibold mb-3">Loại giao dịch</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="transaction-type-card" data-type="rent">
                                            <div class="transaction-type-icon">
                                                <i class="fas fa-home"></i>
                                            </div>
                                            <h6 class="fw-semibold">Cho Thuê</h6>
                                            <p class="text-muted mb-0">Tạo hợp đồng cho thuê định kỳ</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="transaction-type-card" data-type="sale">
                                            <div class="transaction-type-icon">
                                                <i class="fas fa-key"></i>
                                            </div>
                                            <h6 class="fw-semibold">Bán</h6>
                                            <p class="text-muted mb-0">Tạo hợp đồng mua bán</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sale Contract Type (Hidden by default) -->
                        <div class="row mb-4 d-none" id="saleContractType">
                            <div class="col-12">
                                <h6 class="fw-semibold mb-3">Loại hợp đồng bán</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="transaction-type-card" data-sale-type="deposit">
                                            <div class="transaction-type-icon">
                                                <i class="fas fa-file-signature"></i>
                                            </div>
                                            <h6 class="fw-semibold">Hợp Đồng Đặt Cọc</h6>
                                            <p class="text-muted mb-0">Thanh toán theo đợt</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="transaction-type-card" data-sale-type="full_payment">
                                            <div class="transaction-type-icon">
                                                <i class="fas fa-money-check-alt"></i>
                                            </div>
                                            <h6 class="fw-semibold">Thanh Toán Một Lần</h6>
                                            <p class="text-muted mb-0">Thanh toán toàn bộ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Search and Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-semibold mb-3">Thông tin khách hàng</h6>
                                <div class="customer-search-container">
                                    <label class="form-label">Tìm kiếm khách hàng <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control" id="customerSearch" placeholder="Nhập tên, SĐT hoặc CMND/CCCD để tìm kiếm khách hàng..." autocomplete="off">
                                        <i class="fas fa-search position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                                    </div>

                                    <!-- Customer Search Results -->
                                    <div id="customerSearchResults" class="customer-search-results d-none">
                                        <div class="search-loading d-none">
                                            <div class="d-flex align-items-center justify-content-center py-3">
                                                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                                <span>Đang tìm kiếm...</span>
                                            </div>
                                        </div>
                                        <div class="search-results-list"></div>
                                        <div class="search-no-results d-none">
                                            <div class="text-center py-3 text-muted">
                                                <i class="fas fa-user-slash mb-2"></i>
                                                <div>Không tìm thấy khách hàng</div>
                                                <small>Vui lòng thử từ khóa khác</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hidden input to store selected customer ID -->
                                    <input type="hidden" id="selectedCustomerId" name="customer_id" value="">
                                </div>
                            </div>
                        </div>

                        <!-- Selected Customer Info (Hidden initially) -->
                        <div class="row mb-4 d-none" id="selectedCustomerInfo">
                            <div class="col-12">
                                <div class="selected-customer-card">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="customer-info">
                                            <h6 class="fw-semibold mb-2 text-success">
                                                <i class="fas fa-user-check me-2"></i>Khách hàng đã chọn
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="customer-detail">
                                                        <strong>Họ tên:</strong> <span id="selectedCustomerName">-</span>
                                                    </div>
                                                    <div class="customer-detail">
                                                        <strong>Số điện thoại:</strong> <span id="selectedCustomerPhone">-</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="customer-detail">
                                                        <strong>CMND/CCCD:</strong> <span id="selectedCustomerIdCard">-</span>
                                                    </div>
                                                    <div class="customer-detail">
                                                        <strong>Địa chỉ:</strong> <span id="selectedCustomerAddress">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="changeCustomerBtn">
                                            <i class="fas fa-edit me-1"></i>Đổi khách hàng
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Details -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="fw-semibold mb-3">Chi tiết giao dịch</h6>

                                <!-- Rent-specific fields -->
                                <div class="rent-transaction-fields d-none">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Số tháng thuê <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="rentMonths" min="1" placeholder="Nhập số tháng thuê">
                                            <small class="form-text text-muted">Giá thuê sẽ được lấy từ thông tin bất động sản</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Ngày tạo giao dịch</label>
                                            <input type="date" class="form-control" id="transactionDate" value="{{ date('Y-m-d') }}">
                                            <small class="form-text text-muted">Tự động đặt ngày hôm nay ({{ date('d/m/Y') }})</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sale-specific fields -->
                                <div class="sale-transaction-fields d-none">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="salePrice" placeholder="Nhập giá bán">
                                                <span class="input-group-text">VNĐ</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Ngày tạo giao dịch</label>
                                            <input type="date" class="form-control" id="transactionDate" value="{{ date('Y-m-d') }}">
                                            <small class="form-text text-muted">Tự động đặt ngày hôm nay ({{ date('d/m/Y') }})</small>
                                        </div>
                                    </div>

                                    <!-- Deposit Contract Fields -->
                                    <div class="deposit-contract-fields d-none">
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6 class="fw-semibold mb-3 text-primary">
                                                    <i class="fas fa-calculator me-2"></i>Phân chia thanh toán theo đợt
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">Số đợt thanh toán <span class="text-danger">*</span></label>
                                                <select class="form-select" id="paymentInstallments">
                                                    <option value="">Chọn số đợt</option>
                                                    <option value="2">2 đợt</option>
                                                    <option value="3">3 đợt</option>
                                                    <option value="4">4 đợt</option>
                                                    <option value="5">5 đợt</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Tiền đặt cọc (Đợt 1) <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="depositAmount" placeholder="Nhập tiền cọc">
                                                    <span class="input-group-text">VNĐ</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Số tiền còn lại</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="remainingAmount" readonly>
                                                    <span class="input-group-text">VNĐ</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Schedule -->
                                        <div id="paymentSchedule" class="mt-4 d-none">
                                            <h6 class="fw-semibold mb-3">Lịch thanh toán</h6>
                                            <div class="payment-schedule-list"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Payment Method -->
                    <div class="step-content" id="step4">
                        <div class="step-header">
                            <div class="step-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h5>Phương Thức Thanh Toán</h5>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <p class="text-muted mb-4">Chọn phương thức thanh toán cho giao dịch:</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="payment-method" data-payment="cash">
                                            <div class="payment-icon">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <h6 class="fw-semibold">Tiền Mặt</h6>
                                            <p class="text-muted mb-0">Thanh toán trực tiếp bằng tiền mặt</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="payment-method" data-payment="vnpay">
                                            <div class="payment-icon">
                                                <i class="fas fa-credit-card"></i>
                                            </div>
                                            <h6 class="fw-semibold">VNPAY</h6>
                                            <p class="text-muted mb-0">Thanh toán online (Đang phát triển)</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="form-label">Ghi Chú Thanh Toán</label>
                                    <textarea class="form-control" id="paymentNote" rows="3" placeholder="Nhập ghi chú về phương thức thanh toán..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="step-navigation">
                    <button type="button" class="btn btn-step btn-prev" id="prevBtn" style="display: none;">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </button>
                    <div class="step-info">
                        <span id="stepInfo">Bước 1 / 4</span>
                    </div>
                    <button type="button" class="btn btn-step btn-next" id="nextBtn">
                        Tiếp theo<i class="fas fa-arrow-right ms-2"></i>
                    </button>
                    <button type="button" class="btn btn-step btn-submit d-none" id="submitBtn">
                        <i class="fas fa-check me-2"></i>Tạo Giao Dịch
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

