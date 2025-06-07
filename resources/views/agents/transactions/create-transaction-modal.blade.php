<!-- Modal Tạo Giao Dịch - 4 Bước -->
<style>
/* Styles cho step progress */
.step.completed .step-circle {
    background-color: #28a745 !important;
    color: white !important;
    border-color: #28a745 !important;
}

.step.completed .step-label {
    color: #28a745 !important;
    font-weight: 600;
}

.step.active .step-circle {
    background-color: #007bff !important;
    color: white !important;
    border-color: #007bff !important;
}

.step.active .step-label {
    color: #007bff !important;
    font-weight: 600;
}

/* Animation cho completed steps */
.step.completed .step-circle::after {
    content: "✓";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 14px;
    font-weight: bold;
}

.step.completed .step-circle {
    position: relative;
    font-size: 0; /* Hide số */
}

/* Styles cho property details */
.info-item {
    padding: 8px 0;
}

.info-item .label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

.info-item .value {
    font-size: 1rem;
    color: #212529;
    margin-top: 2px;
}

.owner-info {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
}

.owner-info .info-item {
    padding: 4px 0;
}

.selected-property-details {
    padding: 10px 0;
}
</style>

<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-labelledby="createTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="createTransactionModalLabel">
                    <i class="fas fa-handshake text-primary me-2"></i>
                    Tạo Giao Dịch Mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Progress Steps -->
            <div class="px-4 pt-2 pb-0">
                <div class="progress-steps">
                    <div class="step active" data-step="1">
                        <div class="step-circle">1</div>
                        <div class="step-label">Chọn Bất Động Sản</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-circle">2</div>
                        <div class="step-label">Tải Tài Liệu</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-circle">3</div>
                        <div class="step-label">Tạo Giao Dịch</div>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-circle">4</div>
                        <div class="step-label">Thanh Toán</div>
                    </div>
                    <div class="progress-line"></div>
                </div>
            </div>
            
            <form id="createTransactionForm" action="{{ route('agent.transactions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-4">
                    <!-- Bước 1: Tìm kiếm và chọn bất động sản -->
                    <div class="step-content" id="step-1">
                        <div class="step-container">
                            <h6 class="step-title">
                                <i class="fas fa-search text-primary me-2"></i>
                                Tìm kiếm và chọn bất động sản
                            </h6>
                            
                            <div class="property-search-container mb-4">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="propertySearchInput" 
                                           placeholder="Tìm kiếm theo tên BDS, địa chỉ, phường/xã, quận/huyện, tỉnh/thành, loại BDS..."
                                           style="border-left: none; padding-left: 10px;">
                                </div>
                                <div class="search-results-info mt-2" id="searchResultsInfo" style="display: none;">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="searchResultsText"></span>
                                    </small>
                                </div>
                            </div>                            <div class="property-list-container" id="propertyListContainer">
                                @if(isset($properties) && count($properties) > 0)
                                    @foreach($properties as $property)
                                        @if($property && isset($property->PropertyID))
                                        <div class="property-card border rounded p-3 mb-3" 
                                             data-property-id="{{ $property->PropertyID ?? '' }}" 
                                             data-property-type="{{ $property->TypePro ?? '' }}" 
                                             data-property-price="{{ $property->Price ?? 0 }}"
                                             data-property-title="{{ $property->Title ?? '' }}"
                                             data-property-address="{{ $property->Address ?? '' }}"
                                             data-property-ward="{{ $property->Ward ?? 'N/A' }}"
                                             data-property-district="{{ $property->District ?? 'N/A' }}"
                                             data-property-province="{{ $property->Province ?? 'N/A' }}"
                                             data-property-propertytype="{{ optional($property->danhMuc)->ten_pro ?? 'N/A' }}"
                                             style="display: block; visibility: visible; opacity: 1;"
                                             title="Nhấp để xem chi tiết và chọn bất động sản này">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="property-info">
                                                        <h6 class="property-title mb-2 text-dark fw-bold">
                                                            <i class="fas fa-home text-primary me-2"></i>
                                                            {{ $property->Title ?? 'Không có tiêu đề' }}
                                                        </h6>
                                                        
                                                        <div class="property-location mb-2">
                                                            <div class="d-flex align-items-center text-muted mb-1">
                                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                                <span class="fw-medium">Địa chỉ:</span>
                                                            </div>
                                                            <div class="address-details ps-4">
                                                                <div class="text-dark">{{ $property->Address ?? 'Không có địa chỉ' }}</div>
                                                                <div class="location-hierarchy text-muted small">
                                                                    <span class="badge bg-light text-dark me-1">{{ $property->Ward ?? 'N/A' }}</span>
                                                                    <span class="badge bg-light text-dark me-1">{{ $property->District ?? 'N/A' }}</span>
                                                                    <span class="badge bg-light text-dark">{{ $property->Province ?? 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                                <div class="col-md-4 text-end">
                                                    <!-- Hiển thị loại giao dịch -->
                                                    <div class="transaction-type-badge mb-2">
                                                        @if($property->TypePro === 'Rent')
                                                            <span class="badge bg-info px-3 py-2">
                                                                <i class="fas fa-key me-1"></i>
                                                                Cho Thuê
                                                            </span>
                                                        @elseif($property->TypePro === 'Sale')
                                                            <span class="badge bg-success px-3 py-2">
                                                                <i class="fas fa-hand-holding-usd me-1"></i>
                                                                Bán
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary px-3 py-2">
                                                                <i class="fas fa-question me-1"></i>
                                                                Không xác định
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="property-price mb-3">
                                                        <div class="price-display">
                                                            <span class="price-amount h5 text-success fw-bold">
                                                                {{ number_format($property->Price ?? 0, 0, '.', '.') }}đ
                                                            </span>
                                                            @if($property->TypePro === 'Rent')
                                                                <div class="text-muted small">/ tháng</div>
                                                            @endif                                                        
                                                        </div>
                                                    </div>                                                    
                                                    <button type="button" class="btn btn-primary select-property-btn w-100">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        Chọn BDS này
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-home fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">Không có bất động sản nào</h6>                                <p class="text-muted">Hiện tại chưa có bất động sản nào để tạo giao dịch.</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="selected-property-info" id="selectedPropertyInfo" style="display: none;">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-check-circle me-2"></i>
                                            Bất động sản đã chọn
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="selected-property-details">
                                                    <h6 class="property-name text-dark fw-bold mb-2" id="selectedPropertyTitle">
                                                        <i class="fas fa-home text-primary me-2"></i>
                                                        <span id="selectedPropertyName"></span>
                                                    </h6>
                                                    
                                                    <!-- Địa chỉ chi tiết -->
                                                    <div class="location-info mb-3">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                            <span class="fw-medium">Địa chỉ chi tiết:</span>
                                                        </div>
                                                        <div class="address-full ps-4">
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Thông tin cơ bản -->
                                                    <div class="property-info-grid">
                                                        <div class="row g-2">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-end">                                                <button type="button" class="btn btn-outline-warning btn-sm mb-2" onclick="window.transactionModal.changeProperty()">
                                                    <i class="fas fa-exchange-alt me-1"></i>
                                                    Đổi BDS khác
                                                </button>
                                                <div class="text-success small">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Đã sẵn sàng tạo giao dịch
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden inputs for selected property -->
                            <input type="hidden" id="property_id" name="property_id">
                            <input type="hidden" id="transaction_type" name="transaction_type">
                        </div>
                    </div>

                    <!-- Bước 2: Tải tài liệu -->
                    <div class="step-content" id="step-2" style="display: none;">
                        <div class="step-container">
                            <h6 class="step-title">
                                <i class="fas fa-upload text-primary me-2"></i>
                                Tải lên tài liệu giao dịch
                            </h6>
                            
                            <div class="document-upload-area">
                                <div class="upload-zone" id="documentUploadZone">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                    </div>
                                    <h6 class="mt-3">Kéo thả tài liệu vào đây</h6>
                                    <p class="text-muted">hoặc <strong>nhấp để chọn file</strong></p>
                                    <p class="small text-muted">Hỗ trợ: PDF, DOC, DOCX, JPG, PNG (tối đa 10MB)</p>
                                    <input type="file" id="contract_documents" name="contract_documents[]" 
                                           class="d-none" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                </div>
                                
                                <div id="selectedFiles" class="selected-files mt-3"></div>
                                
                                <div class="upload-status mt-3" id="uploadStatus" style="display: none;">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <span id="uploadStatusText">Đang kiểm tra tệp...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bước 3: Tạo giao dịch -->
                    <div class="step-content" id="step-3" style="display: none;">
                        <div class="step-container">
                            <h6 class="step-title">
                                <i class="fas fa-file-contract text-primary me-2"></i>
                                Thông tin giao dịch
                            </h6>
                            
                            <div class="row g-3">
                                <!-- Chọn khách hàng -->
                                <div class="col-md-6">
                                    <label for="customer_id" class="form-label fw-medium">
                                        <i class="fas fa-user text-info me-1"></i>
                                        Khách hàng <span class="text-danger">*</span>
                                    </label>                                    <select class="form-select" id="customer_id" name="customer_id" required>
                                        <option value="">Chọn khách hàng...</option>
                                        @php
                                            $customerList = $customers ?? [];
                                            if (!is_array($customerList) && !is_object($customerList)) {
                                                $customerList = [];
                                            }
                                        @endphp
                                        
                                        @if(!empty($customerList))
                                            @foreach($customerList as $customer)
                                                @if(is_object($customer) && isset($customer->UserID))
                                                <option value="{{ $customer->UserID }}">
                                                    {{ $customer->Name ?? 'N/A' }} - {{ $customer->Phone ?? 'N/A' }}
                                                </option>
                                                @endif
                                            @endforeach                                        @else
                                            <option value="" disabled>Không có khách hàng nào</option>
                                        @endif
                                    </select>
                                    
                                    <!-- Customer details display -->
                                    <div id="selectedCustomerInfo" style="display: none;"></div>
                                </div>
                                
                                <!-- Ngày giao dịch -->
                                <div class="col-md-6">
                                    <label for="transaction_date" class="form-label fw-medium">
                                        <i class="fas fa-calendar-alt text-warning me-1"></i>
                                        Ngày giao dịch <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" class="form-control" id="transaction_date" 
                                           name="transaction_date" required disabled
                                           value="{{ date('Y-m-d\TH:i') }}"
                                           title="Ngày giao dịch được cố định là thời gian hiện tại">
                                    <input type="hidden" name="transaction_date_hidden" value="{{ date('Y-m-d\TH:i') }}">
                                </div>
                                
                                <!-- Rental specific fields -->
                                <div id="rentalFields" style="display: none;">
                                    <div class="col-md-4">
                                        <label for="rental_months" class="form-label fw-medium">
                                            <i class="fas fa-clock text-info me-1"></i>
                                            Số tháng thuê <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="rental_months" name="rental_months" 
                                               placeholder="Vd: 12" min="1" max="120">
                                        <div class="form-text">Thời gian hợp đồng thuê</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="payment_type" class="form-label fw-medium">
                                            <i class="fas fa-credit-card text-success me-1"></i>
                                            Hình thức thanh toán <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="payment_type" name="payment_type">
                                            <option value="">Chọn hình thức...</option>
                                            <option value="monthly">Thanh toán hàng tháng</option>
                                            <option value="quarterly">Thanh toán theo quý</option>
                                            <option value="yearly">Thanh toán theo năm</option>
                                            <option value="advance">Thanh toán trước</option>
                                        </select>
                                    </div>                                    <div class="col-md-4">
                                        <label for="monthly_price" class="form-label fw-medium">
                                            <i class="fas fa-money-check text-primary me-1"></i>
                                            Giá thuê/tháng <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="monthly_price" name="monthly_price" 
                                                   placeholder="Giá thuê hàng tháng" min="0">
                                            <span class="input-group-text">₫</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="deposit_amount" class="form-label fw-medium">
                                            <i class="fas fa-shield-alt text-warning me-1"></i>
                                            Tiền cọc <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="deposit_amount" name="deposit_amount" 
                                                   placeholder="Số tiền cọc" min="0">
                                            <span class="input-group-text">₫</span>
                                        </div>
                                        <div class="form-text">Thường bằng 1-3 tháng tiền thuê</div>
                                    </div>
                                </div>
                                
                                <!-- Giá giao dịch -->
                                <div class="col-md-12">
                                    <label for="total_price" class="form-label fw-medium">
                                        <i class="fas fa-money-bill-wave text-success me-1"></i>
                                        Giá trị giao dịch <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="total_price" name="total_price" 
                                               placeholder="Giá trị giao dịch" required min="0" readonly>
                                        <span class="input-group-text">₫</span>
                                    </div>
                                    <div class="form-text" id="price-preview"></div>
                                </div>
                            </div>
                            
                            <!-- Ghi chú -->
                            <div class="mt-4">
                                <label for="notes" class="form-label fw-medium">
                                    <i class="fas fa-sticky-note text-secondary me-1"></i>
                                    Ghi chú
                                </label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Nhập ghi chú về giao dịch (không bắt buộc)..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Bước 4: Thanh toán -->
                    <div class="step-content" id="step-4" style="display: none;">
                        <div class="step-container">
                            <h6 class="step-title">
                                <i class="fas fa-credit-card text-primary me-2"></i>
                                Phương thức thanh toán lần đầu
                            </h6>
                            
                            <div class="transaction-summary mb-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Tóm tắt giao dịch</h6>
                                        <div class="row">
                                        </div>
                                    </div>
                                </div>
                            </div>
                              <div class="payment-methods">
                                <h6 class="mb-3">Chọn phương thức thanh toán:</h6>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="payment-method border rounded p-4 text-center cursor-pointer" data-method="cash">
                                            <div class="payment-icon mb-3">
                                                <i class="fas fa-money-bill-wave fa-3x text-success"></i>
                                            </div>
                                            <h6 class="payment-title">Tiền mặt</h6>
                                            <p class="text-muted small">Thanh toán bằng tiền mặt trực tiếp</p>
                                            <div class="payment-features">
                                                <div class="badge bg-success mb-2">
                                                    <i class="fas fa-check me-1"></i>
                                                    Thanh toán ngay
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="payment-method border rounded p-4 text-center cursor-pointer" data-method="bank">
                                            <div class="payment-icon mb-3">
                                                <i class="fas fa-university fa-3x text-primary"></i>
                                            </div>
                                            <h6 class="payment-title">Chuyển khoản</h6>
                                            <p class="text-muted small">Thanh toán qua chuyển khoản ngân hàng</p>
                                            <div class="payment-features">
                                                <div class="badge bg-info mb-2">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Chờ xác nhận
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="payment-note mt-3">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Lưu ý:</strong> Với thanh toán tiền mặt, giao dịch sẽ được xác nhận ngay lập tức. 
                                        Với chuyển khoản, giao dịch sẽ ở trạng thái chờ xác nhận cho đến khi nhận được tiền.
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" id="payment_method" name="payment_method">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" id="prevStepBtn" style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i>
                        Quay lại
                    </button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Hủy bỏ
                    </button>
                    <button type="button" class="btn btn-primary" id="nextStepBtn">
                        Tiếp tục
                        <i class="fas fa-arrow-right ms-1"></i>
                    </button>                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                        <i class="fas fa-check me-1"></i>
                        Hoàn tất giao dịch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Property Details Modal để xem chi tiết BDS -->
<div class="modal fade" id="propertyDetailsModal" tabindex="-1" aria-labelledby="propertyDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="propertyDetailsModalLabel">
                    <i class="fas fa-home text-primary me-2"></i>Chi tiết bất động sản
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Loading state -->
                <div id="propertyDetailsLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-3 text-muted">Đang tải thông tin chi tiết bất động sản...</p>
                </div>
                
                <!-- Property details content -->
                <div id="propertyDetailsContent" class="d-none">
                    <!-- Content will be loaded here -->
                </div>
                
                <!-- Error state -->
                <div id="propertyDetailsError" class="d-none">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Lỗi:</strong> Không thể tải thông tin chi tiết bất động sản.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Đóng
                </button>
            </div>
        </div>
    </div>
</div>

</div>