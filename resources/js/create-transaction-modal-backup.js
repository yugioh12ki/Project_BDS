// Quản lý modal tạo giao dịch với 4 bước tiến độ (BACKUP FILE - CLEAN VERSION)
// Backup của file chính đã được sửa lỗi JavaScript syntax
class TransactionModalBackup {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.selectedProperty = null;
        this.uploadedFiles = [];
        this.customers = [];
        
        // Track completion status for each step
        this.stepCompleted = {
            1: false, // Property selection
            2: false, // Document upload
            3: false, // Transaction info
            4: false  // Payment method
        };
        
        console.log('TransactionModalBackup initialized - clean version without HTML template literal errors');
    }

    // Basic methods for backup reference
    init() {
        console.log('Backup version - init method');
    }

    resetModal() {
        console.log('Backup version - resetModal method');
    }

    loadProperties() {
        console.log('Backup version - loadProperties method');
    }

    selectProperty(propertyId) {
        console.log('Backup version - selectProperty method:', propertyId);
    }

    nextStep() {
        console.log('Backup version - nextStep method');
    }

    prevStep() {
        console.log('Backup version - prevStep method');
    }

    submitTransaction() {
        console.log('Backup version - submitTransaction method');
    }
}

// Backup version - not automatically initialized
console.log('Transaction Modal Backup file loaded successfully - no syntax errors');

    init() {
        console.log('TransactionModal initialized');
        
        // Khởi tạo modal khi mở
        $('#createTransactionModal').on('shown.bs.modal', () => {
            console.log('Transaction modal opened, starting initialization...');
            this.resetModal();
            this.loadProperties();
            this.loadCustomers();
        });

        // Xử lý nút tiếp tục
        $('#nextStepBtn').on('click', () => {
            this.nextStep();
        });

        // Xử lý nút quay lại
        $('#prevStepBtn').on('click', () => {
            this.prevStep();
        });

        // Khởi tạo các chức năng cho từng bước
        this.initStep1(); // Chọn bất động sản
        this.initStep2(); // Upload file hợp đồng
        this.initStep3(); // Thông tin giao dịch
        this.initStep4(); // Phương thức thanh toán

        // Xử lý submit form
        $('#createTransactionForm').on('submit', (e) => {
            e.preventDefault();
            this.submitTransaction();
        });
    }    resetModal() {
        console.log('Resetting transaction modal...');
        
        // Reset về bước 1
        this.currentStep = 1;
        this.selectedProperty = null;
        this.uploadedFiles = [];
        
        // Reset UI
        this.updateStepIndicator();
        this.showStep(1);
        
        // Reset form
        document.getElementById('createTransactionForm').reset();
        
        // Clear selected property info and show property list
        $('#selectedPropertyInfo').hide();
        $('#propertyListContainer').show();
        
        // Remove selection highlight from all property cards
        $('.property-card').removeClass('selected-property');
        
        // Clear search input and filters
        $('#propertySearchInput').val('');
        $('input[name="step1_transaction_type"]').prop('checked', false);
        $('.property-card').show(); // Show all properties
        
        // Reset hidden inputs
        $('#property_id').val('');
        $('#transaction_type').val('');
        
        // Reset buttons - disable next button until property is selected
        $('#prevStepBtn').hide();
        $('#nextStepBtn').show().text('Tiếp tục').prop('disabled', true);
        
        console.log('Modal reset completed');
    }// Bước 1: Chọn bất động sản
    initStep1() {
        // Tìm kiếm bất động sản
        $('#propertySearchInput').on('input', (e) => {
            const searchTerm = e.target.value.trim();
            this.searchProperties(searchTerm);
        });

        // Chọn bất động sản - sử dụng event delegation
        $(document).on('click', '.property-card', (e) => {
            const propertyId = $(e.currentTarget).data('property-id');
            this.selectProperty(propertyId);
        });

        // Chọn bất động sản - nút chọn
        $(document).on('click', '.select-property-btn', (e) => {
            e.stopPropagation(); // Prevent card click
            const propertyId = $(e.currentTarget).closest('.property-card').data('property-id');
            this.selectProperty(propertyId);
        });

        // Xem chi tiết bất động sản
        $(document).on('click', '.view-property-details', (e) => {
            e.stopPropagation(); // Prevent card click
            const propertyId = $(e.currentTarget).data('property-id');
            this.showPropertyDetails(propertyId);
        });

        // Chọn BDS từ modal chi tiết
        $(document).on('click', '#selectPropertyFromDetails', () => {
            const propertyId = $('#selectPropertyFromDetails').data('property-id');
            if (propertyId) {
                $('#propertyDetailsModal').modal('hide');
                this.selectProperty(propertyId);
            }
        });

        // Lọc theo loại giao dịch
        $('input[name="step1_transaction_type"]').on('change', (e) => {
            const transactionType = e.target.value;
            this.filterPropertiesByType(transactionType);
        });
    }

    // Bước 2: Upload file
    initStep2() {
        const uploadZone = $('#documentUploadZone');
        const fileInput = $('#contract_documents');

        // Click để chọn file
        uploadZone.on('click', () => {
            fileInput.click();
        });

        // Drag & drop
        uploadZone.on('dragover', (e) => {
            e.preventDefault();
            uploadZone.addClass('dragover');
        });

        uploadZone.on('dragleave', () => {
            uploadZone.removeClass('dragover');
        });

        uploadZone.on('drop', (e) => {
            e.preventDefault();
            uploadZone.removeClass('dragover');
            const files = e.originalEvent.dataTransfer.files;
            this.handleFileSelection(files);
        });

        // File input change
        fileInput.on('change', (e) => {
            this.handleFileSelection(e.target.files);
        });

        // Xóa file
        $(document).on('click', '.remove-file', (e) => {
            const index = $(e.currentTarget).data('index');
            this.removeFile(index);
        });
    }

    // Bước 3: Thông tin giao dịch
    initStep3() {
        // Xử lý thay đổi loại giao dịch
        $(document).on('change', 'input[name="transaction_type"]', () => {
            const transactionType = $('input[name="transaction_type"]:checked').val();
            this.handleTransactionTypeChange(transactionType);
        });

        // Format giá tiền
        $('#total_price').on('input', (e) => {
            this.formatPrice(e.target);
        });

        // Set ngày giao dịch mặc định
        $('#transaction_date').val(new Date().toISOString().slice(0, 16));
    }

    // Bước 4: Phương thức thanh toán
    initStep4() {
        // Xử lý chọn phương thức thanh toán
        $(document).on('click', '.payment-method', (e) => {
            $('.payment-method').removeClass('selected');
            $(e.currentTarget).addClass('selected');
            const method = $(e.currentTarget).data('method');
            $('#payment_method').val(method);
        });
    }    // Load danh sách bất động sản  
    async loadProperties() {
        try {
            console.log('Loading properties for transaction modal...');
            
            // Properties are already rendered by Blade template
            const container = $('#propertyListContainer');
            const existingCards = container.find('.property-card');
            
            console.log('Found property cards:', existingCards.length);
            
            if (existingCards.length === 0) {
                // No properties found, show empty state
                container.html(`
                    <div class="text-center py-4">
                        <i class="fas fa-home fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Không có bất động sản nào</h6>
                        <p class="text-muted">Hiện tại chưa có bất động sản nào để tạo giao dịch.</p>
                        <small class="text-info">
                            <i class="fas fa-info-circle me-1"></i>
                            Vui lòng thêm bất động sản trước khi tạo giao dịch.
                        </small>
                    </div>
                `);
            } else {
                // Properties found, ensure they are visible and functional
                container.show();
                existingCards.show();
                
                console.log('Properties loaded successfully');
                
                // Add hover effects for property cards
                existingCards.hover(
                    function() {
                        $(this).addClass('shadow-sm border-primary');
                    },
                    function() {
                        $(this).removeClass('shadow-sm border-primary');
                    }
                );
                
                // Show search results info
                const searchInfo = $('#searchResultsInfo');
                const searchText = $('#searchResultsText');
                searchText.text(`Tìm thấy ${existingCards.length} bất động sản`);
                searchInfo.show();
            }
            
        } catch (error) {
            console.error('Error loading properties:', error);
            
            // Show error state
            $('#propertyListContainer').html(`
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6 class="text-warning">Lỗi tải dữ liệu</h6>
                    <p class="text-muted">Không thể tải danh sách bất động sản. Vui lòng thử lại.</p>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.transactionModal.loadProperties()">
                        <i class="fas fa-redo me-1"></i>Thử lại
                    </button>
                </div>
            `);
        }
    }// Load danh sách khách hàng
    async loadCustomers() {
        try {
            const response = await fetch('/agent/api/customers');
            const customers = await response.json();
            this.customers = customers;
            this.renderCustomerOptions(customers);
        } catch (error) {
            console.error('Lỗi tải danh sách khách hàng:', error);
        }
    }

    // Render danh sách khách hàng vào select options
    renderCustomerOptions(customers) {
        console.log('Rendering customer options:', customers);
        
        // Tìm các select element cho khách hàng
        const customerSelects = ['#buyer_customer_id', '#seller_customer_id', '#renter_customer_id', '#lessor_customer_id'];
        
        customerSelects.forEach(selectId => {
            const selectElement = $(selectId);
            if (selectElement.length) {
                // Clear existing options except the first default option
                selectElement.find('option:not(:first)').remove();
                
                // Add customer options
                customers.forEach(customer => {
                    const option = new Option(
                        `${customer.Name} - ${customer.Phone}`, 
                        customer.CustomerID
                    );
                    selectElement.append(option);
                });
            }
        });
    }

    // Tìm kiếm bất động sản
    searchProperties(searchTerm) {
        $('.property-card').each(function() {
            const text = $(this).text().toLowerCase();
            const isMatch = text.includes(searchTerm.toLowerCase());
            $(this).toggle(isMatch);
        });
    }    // Lọc theo loại giao dịch
    filterPropertiesByType(transactionType) {
        $('.property-card').show();
        
        if (transactionType === 'sale') {
            $('.property-card').each(function() {
                const typePro = $(this).data('property-type');
                if (typePro !== 'Sale') {
                    $(this).hide();
                }
            });
        } else if (transactionType === 'rental') {
            $('.property-card').each(function() {
                const typePro = $(this).data('property-type');
                if (typePro !== 'Rent') {
                    $(this).hide();
                }
            });
        }
    }    // Chọn bất động sản
    async selectProperty(propertyId) {
        try {
            console.log('Selecting property:', propertyId);
            
            // Hiển thị loading
            this.showLoadingProperty();
            
            // Fetch thông tin chi tiết từ API
            const response = await fetch(`/agent/api/property/${propertyId}/details`);
            if (!response.ok) {
                throw new Error('Không thể tải thông tin bất động sản');
            }
            
            const apiResponse = await response.json();
            if (!apiResponse.success) {
                throw new Error(apiResponse.message || 'Không thể tải thông tin bất động sản');
            }
            
            this.selectedProperty = apiResponse.data;
            console.log('Property data loaded:', apiResponse.data);

            // Set hidden inputs
            $('#property_id').val(propertyId);
            
            // Tự động thiết lập transaction_type dựa trên TypePro
            let transactionType = '';
            if (this.selectedProperty.TypePro === 'Rent') {
                transactionType = 'rental';
            } else if (this.selectedProperty.TypePro === 'Sale') {
                transactionType = 'sale';
            }
            $('#transaction_type').val(transactionType);
            console.log('Transaction type set to:', transactionType);

            // Mark property card as selected
            $('.property-card').removeClass('selected-property');
            $(`.property-card[data-property-id="${propertyId}"]`).addClass('selected-property');

            // Ẩn danh sách và hiển thị thông tin đã chọn
            $('#propertyListContainer').hide();
            $('.property-search-container').hide();
            $('#searchResultsInfo').hide();
            
            this.showSelectedPropertyDetails();
            $('#selectedPropertyInfo').show();
            
            // Cho phép chuyển sang bước tiếp theo
            $('#nextStepBtn').prop('disabled', false);
            
        } catch (error) {
            console.error('Lỗi khi chọn bất động sản:', error);
            this.showError('Không thể tải thông tin bất động sản. Vui lòng thử lại.');
        }
    }// Hiển thị thông tin bất động sản đã chọn
    showSelectedProperty() {
        const property = this.selectedProperty;
        
        let transactionIcon, transactionLabel, transactionColor;
        if (property.TypePro === 'Rent') {
            transactionIcon = 'fas fa-key';
            transactionLabel = 'Cho Thuê';
            transactionColor = 'info';
        } else if (property.TypePro === 'Sale') {
            transactionIcon = 'fas fa-hand-holding-usd';
            transactionLabel = 'Bán';
            transactionColor = 'success';
        } else {
            transactionIcon = 'fas fa-question';
            transactionLabel = 'Không xác định';
            transactionColor = 'secondary';
        }
        
        const html = `
            <div class="selected-property-card border border-success rounded p-4">
                <div class="row">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-1 text-success">Đã chọn bất động sản</h5>
                                <p class="mb-0 text-muted">Loại giao dịch: <span class="badge bg-${transactionColor}"><i class="${transactionIcon} me-1"></i>${transactionLabel}</span></p>
                            </div>
                        </div>
                        
                        <div class="property-details">
                            <h6 class="fw-bold mb-2">${property.Title || property.PropertyName}</h6>
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                ${property.Address}
                            </p>
                            
                            <!-- Hiển thị giá -->
                            <div class="price-display mb-3">
                                <span class="h5 text-success fw-bold">
                                    <i class="fas fa-tag me-1"></i>${this.formatCurrency(property.Price)}
                                    ${property.TypePro === 'Rent' ? '/tháng' : ''}
                                </span>
                            </div>
                            
                            <!-- Thông tin chủ sở hữu -->
                            ${property.owner ? `
                            <div class="owner-info bg-light rounded p-3 mb-3">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-user-tie me-2"></i>Thông tin chủ sở hữu
                                </h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted">Tên:</small>
                                        <div class="fw-medium">${property.owner.Name}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Điện thoại:</small>
                                        <div class="fw-medium">${property.owner.Phone || 'N/A'}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted">Email:</small>
                                        <div class="fw-medium">${property.owner.Email || 'N/A'}</div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                            
                            <!-- Chi tiết property -->
                            ${property.detailProperty ? `
                            <div class="property-details-summary bg-light rounded p-3">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-info-circle me-2"></i>Chi tiết bất động sản
                                </h6>
                                <div class="row g-2">
                                    ${property.detailProperty.Bedroom ? `<div class="col-md-3"><small><i class="fas fa-bed me-1"></i>${property.detailProperty.Bedroom} phòng ngủ</small></div>` : ''}
                                    ${property.detailProperty.Bath_WC ? `<div class="col-md-3"><small><i class="fas fa-bath me-1"></i>${property.detailProperty.Bath_WC} phòng tắm</small></div>` : ''}
                                    ${property.detailProperty.Balcony ? `<div class="col-md-3"><small><i class="fas fa-home me-1"></i>${property.detailProperty.Balcony} ban công</small></div>` : ''}
                                    ${property.detailProperty.HouseLength && property.detailProperty.HouseWidth ? `<div class="col-md-3"><small><i class="fas fa-ruler-combined me-1"></i>${property.detailProperty.HouseLength}x${property.detailProperty.HouseWidth}m</small></div>` : ''}
                                    ${property.detailProperty.view ? `<div class="col-md-6"><small><i class="fas fa-eye me-1"></i>View: ${property.detailProperty.view}</small></div>` : ''}
                                    ${property.detailProperty.near ? `<div class="col-md-6"><small><i class="fas fa-map-signs me-1"></i>Gần: ${property.detailProperty.near}</small></div>` : ''}
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-outline-info btn-sm mb-2 w-100" onclick="transactionModal.showPropertyDetails('${property.PropertyID}')">
                            <i class="fas fa-eye me-1"></i>
                            Xem Chi Tiết Đầy Đủ
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm mb-2 w-100" onclick="transactionModal.changeProperty()">
                            <i class="fas fa-exchange-alt me-1"></i>
                            Đổi BDS Khác
                        </button>
                        <div class="text-success small mt-3">
                            <i class="fas fa-check-circle me-1"></i>
                            Sẵn sàng chuyển sang bước tiếp theo
                        </div>
                    </div>
                </div>
            </div>
        `;
                            <div>
                                <h5 class="mb-1 text-success">Đã chọn bất động sản</h5>
                                <p class="mb-0 text-muted">Tự động xác định loại giao dịch: <span class="badge bg-${transactionColor}"><i class="${transactionIcon} me-1"></i>${transactionLabel}</span></p>
                            </div>
                        </div>
                        
                        <div class="property-details">
                            <h6 class="fw-bold mb-2">${property.PropertyName}</h6>
                            <p class="text-muted mb-3">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                ${fullAddress}
                            </p>
                            
                            <!-- Hiển thị thông tin giao dịch đã xác định -->
                            <div class="transaction-info-display">
                                <div class="alert alert-${transactionColor} alert-dismissible fade show" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="${transactionIcon} fa-2x me-3"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Loại giao dịch: ${transactionLabel}</h6>
                                            <p class="mb-0">
                                                <strong>Giá: ${this.formatCurrency(property.Price)}</strong>
                                                ${property.TypePro === 'Rent' ? '/tháng' : ''}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-outline-warning btn-sm mb-2" onclick="transactionModal.showPropertyList()">
                            <i class="fas fa-exchange-alt me-1"></i>
                            Đổi BDS khác
                        </button>
                        <div class="text-success small">
                            <i class="fas fa-check-circle me-1"></i>
                            Sẵn sàng chuyển sang bước tiếp theo
                        </div>
                    </div>
                </div>
            </div>        `;
        
        $('#selectedPropertyInfo').html(html);
    }    // Đổi bất động sản khác (cho phép chọn lại)
    changeProperty() {
        console.log('Changing property selection');
        
        // Ẩn thông tin chi tiết
        $('#selectedPropertyInfo').hide();
        
        // HIỂN THỊ lại danh sách BDS và phần tìm kiếm
        $('#propertyListContainer').show();
        $('.property-search-container').show(); // Hiển thị lại phần tìm kiếm
        $('#searchResultsInfo').show(); // Hiển thị lại thông tin kết quả tìm kiếm
        
        // Bỏ chọn property hiện tại
        $('.property-card').removeClass('selected-property');
        
        // Reset selectedProperty
        this.selectedProperty = null;
        
        // Clear hidden inputs
        $('#property_id').val('');
        $('#transaction_type').val('');
        
        // Disable next button
        $('#nextStepBtn').prop('disabled', true);
    }    // Hiển thị modal chi tiết bất động sản
    async showPropertyDetails(propertyId) {
        console.log('Showing property details for:', propertyId);
        
        // Show modal
        $('#propertyDetailsModal').modal('show');
        
        // Show loading state
        $('#propertyDetailsLoading').removeClass('d-none');
        $('#propertyDetailsContent').addClass('d-none');
        $('#propertyDetailsError').addClass('d-none');
        
        try {
            // Fetch property details từ API test endpoint
            const response = await fetch('/test/property-details/' + propertyId);
            if (!response.ok) {
                throw new Error('Không thể tải thông tin bất động sản');
            }
            
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Không thể tải thông tin bất động sản');
            }
            
            // Hide loading, show content
            $('#propertyDetailsLoading').addClass('d-none');
            $('#propertyDetailsContent').removeClass('d-none');
            
            // Render property details
            this.renderPropertyDetails(data.property);
            
            // Store property ID for selection
            $('#selectPropertyFromDetails').data('property-id', propertyId);
            
        } catch (error) {
            console.error('Error loading property details:', error);
            
            // Hide loading, show error
            $('#propertyDetailsLoading').addClass('d-none');
            $('#propertyDetailsError').removeClass('d-none');
            $('#propertyDetailsError .alert').html(
                '<i class="fas fa-exclamation-triangle me-2"></i>' +
                '<strong>Lỗi:</strong> ' + error.message
            );
        }
    }

    // Render property details trong modal
    renderPropertyDetails(property) {
        const formatPrice = (price, type) => {
            if (!price || price <= 0) return 'Liên hệ để biết giá';
            const formatted = new Intl.NumberFormat('vi-VN').format(price);
            return type === 'Rent' ? `${formatted} đ/tháng` : `${formatted} đ`;
        };

        const html = `
            <div class="property-details-content">
                <!-- Header với thông tin cơ bản -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0 text-primary">
                                <i class="fas fa-home me-2"></i>${property.Title}
                            </h4>
                            <div class="transaction-type-badge">
                                ${property.TypePro === 'Rent' ? 
                                    '<span class="badge bg-info px-3 py-2"><i class="fas fa-key me-1"></i>Cho Thuê</span>' :
                                    '<span class="badge bg-success px-3 py-2"><i class="fas fa-hand-holding-usd me-1"></i>Bán</span>'
                                }
                            </div>
                        </div>
                        
                        <div class="property-basic-info">
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                <strong>Địa chỉ:</strong> ${property.Address}
                            </p>
                            <div class="price-display mb-3">
                                <span class="h4 text-success fw-bold">
                                    <i class="fas fa-tag me-2"></i>${formatPrice(property.Price, property.TypePro)}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin chủ sở hữu -->
                ${property.owner ? `
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-primary border-bottom pb-2">
                            <i class="fas fa-user-tie me-2"></i>Thông tin chủ sở hữu
                        </h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted">Tên chủ sở hữu</small>
                                                <div class="fw-bold">${property.owner.Name}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-phone text-success me-2"></i>
                                            <div>
                                                <small class="text-muted">Điện thoại</small>
                                                <div class="fw-bold">${property.owner.Phone || 'Chưa cập nhật'}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope text-info me-2"></i>
                                            <div>
                                                <small class="text-muted">Email</small>
                                                <div class="fw-bold">${property.owner.Email || 'Chưa cập nhật'}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}

                <!-- Chi tiết bất động sản -->
                ${property.detailProperty ? `
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="text-primary border-bottom pb-2">
                            <i class="fas fa-info-circle me-2"></i>Chi tiết bất động sản
                        </h6>
                        <div class="row g-3">
                            ${property.detailProperty.Bedroom ? `
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <i class="fas fa-bed text-primary me-2"></i>
                                    <strong>Phòng ngủ:</strong> ${property.detailProperty.Bedroom}
                                </div>
                            </div>` : ''}
                            
                            ${property.detailProperty.Bath_WC ? `
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <i class="fas fa-bath text-info me-2"></i>
                                    <strong>Phòng tắm:</strong> ${property.detailProperty.Bath_WC}
                                </div>
                            </div>` : ''}
                            
                            ${property.detailProperty.Balcony ? `
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <i class="fas fa-home text-warning me-2"></i>
                                    <strong>Ban công:</strong> ${property.detailProperty.Balcony}
                                </div>
                            </div>` : ''}
                            
                            ${property.detailProperty.HouseLength && property.detailProperty.HouseWidth ? `
                            <div class="col-md-3">
                                <div class="detail-item">
                                    <i class="fas fa-ruler-combined text-secondary me-2"></i>
                                    <strong>Kích thước:</strong> ${property.detailProperty.HouseLength}m x ${property.detailProperty.HouseWidth}m
                                </div>
                            </div>` : ''}
                            
                            ${property.detailProperty.view ? `
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <i class="fas fa-eye text-success me-2"></i>
                                    <strong>View:</strong> ${property.detailProperty.view}
                                </div>
                            </div>` : ''}
                            
                            ${property.detailProperty.near ? `
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <i class="fas fa-map-signs text-info me-2"></i>
                                    <strong>Gần:</strong> ${property.detailProperty.near}
                                </div>
                            </div>` : ''}
                            
                            ${property.detailProperty.WaterPrice ? `
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <i class="fas fa-tint text-primary me-2"></i>
                                    <strong>Giá nước:</strong> ${property.detailProperty.WaterPrice}
                                </div>
                            </div>` : ''}
                            
                            ${property.detailProperty.PowerPrice ? `
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <i class="fas fa-bolt text-warning me-2"></i>
                                    <strong>Giá điện:</strong> ${property.detailProperty.PowerPrice}
                                </div>
                            </div>` : ''}
                            
                            ${property.detailProperty.legal ? `
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <i class="fas fa-gavel text-danger me-2"></i>
                                    <strong>Pháp lý:</strong> ${property.detailProperty.legal}
                                </div>
                            </div>` : ''}
                            
                            ${property.detailProperty.Interior ? `
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <i class="fas fa-couch text-brown me-2"></i>
                                    <strong>Nội thất:</strong> ${property.detailProperty.Interior}
                                </div>
                            </div>` : ''}
                        </div>
                    </div>
                </div>
                ` : '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Chưa có thông tin chi tiết về bất động sản này.</div>'}
                
                <!-- Action buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Bất động sản này có thể được sử dụng để tạo giao dịch ${property.TypePro === 'Rent' ? 'cho thuê' : 'bán'}.</strong>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#propertyDetailsContent').html(html);
    }
        this.selectedProperty = null;
        $('#property_id').val('');
        $('#transaction_type').val('');
        
        // Disable nút tiếp theo
        $('#nextStepBtn').prop('disabled', true);
        
        // Scroll to top of property list
        $('#propertyListContainer')[0]?.scrollIntoView({ behavior: 'smooth' });
    }

    // Hiển thị lại danh sách bất động sản (legacy method - giữ lại để tương thích)
    showPropertyList() {
        this.changeProperty();
    }

    // Hiển thị loading khi đang tải thông tin property
    showLoadingProperty() {
        $('#selectedPropertyInfo').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-3 text-muted">Đang tải thông tin bất động sản...</p>
            </div>
        `).show();
    }

    // Hiển thị thông tin chi tiết bất động sản đã chọn
    showSelectedPropertyDetails() {
        const property = this.selectedProperty;
          // Tạo địa chỉ đầy đủ từ các thành phần
        const addressParts = [
            property.Address,
            property.Ward, 
            property.District,
            property.Province
        ].filter(part => part && part !== 'undefined' && part.trim() !== '');
        
        const fullAddress = addressParts.length > 0 ? addressParts.join(', ') : 'Không có địa chỉ';
        
        // Xác định loại giao dịch
        let transactionLabel = 'Chưa xác định';
        let transactionIcon = 'fas fa-question-circle';
        let transactionColor = 'secondary';
        
        if (property.TypePro === 'Sale') {
            transactionLabel = 'Bán';
            transactionIcon = 'fas fa-handshake';
            transactionColor = 'success';
        } else if (property.TypePro === 'Rent') {
            transactionLabel = 'Cho Thuê';
            transactionIcon = 'fas fa-key';
            transactionColor = 'info';
        }
        
        const html = `
            <div class="selected-property-card border border-success rounded p-4 mb-4">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Header với trạng thái đã chọn -->
                        <div class="d-flex align-items-center mb-4">
                            <i class="fas fa-check-circle text-success fa-2x me-3"></i>
                            <div>
                                <h5 class="mb-1 text-success">✓ Đã chọn bất động sản</h5>
                                <p class="mb-0 text-muted">Loại giao dịch: <span class="badge bg-${transactionColor}"><i class="${transactionIcon} me-1"></i>${transactionLabel}</span></p>
                            </div>
                            <div class="ms-auto">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.transactionModal.changeProperty()">
                                    <i class="fas fa-exchange-alt me-1"></i>Đổi BDS khác
                                </button>
                            </div>
                        </div>
                          <!-- Thông tin cơ bản -->
                        <div class="property-basic-info mb-4">
                            <h4 class="fw-bold text-primary mb-3">
                                <i class="fas fa-home me-2"></i>${property.PropertyName || property.Title || 'Không có tiêu đề'}
                            </h4>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">                                    <div class="info-item">
                                        <strong><i class="fas fa-map-marker-alt text-danger me-2"></i>Địa chỉ:</strong>
                                        <p class="ms-4 mb-1">${fullAddress}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong><i class="fas fa-money-bill-wave text-success me-2"></i>Giá:</strong>
                                        <p class="ms-4 mb-1 text-success fw-bold fs-5">${this.formatCurrency(property.Price || property.RentalPrice)}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong><i class="fas fa-building text-info me-2"></i>Loại BDS:</strong>
                                        <p class="ms-4 mb-1">${property.PropertyType || property.property_type_name || 'Không xác định'}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong><i class="fas fa-user text-warning me-2"></i>Chủ sở hữu:</strong>
                                        <p class="ms-4 mb-1">${property.owner_name || 'Không xác định'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                          <!-- Chi tiết kỹ thuật -->
                        <div class="property-detailed-info">
                            <h5 class="fw-bold text-secondary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Chi tiết kỹ thuật
                            </h5>
                            <div class="row">                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="detail-card border rounded p-3 text-center h-100">
                                        <i class="fas fa-expand-arrows-alt text-primary fa-2x mb-2"></i>
                                        <h6 class="mb-1">Diện tích</h6>
                                        <p class="mb-0 fw-bold">${property.detailProperty?.Area ? property.detailProperty.Area + ' m²' : (property.Area ? property.Area + ' m²' : 'N/A')}</p>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="detail-card border rounded p-3 text-center h-100">
                                        <i class="fas fa-bed text-success fa-2x mb-2"></i>
                                        <h6 class="mb-1">Phòng ngủ</h6>
                                        <p class="mb-0 fw-bold">${property.detailProperty?.Bedroom || property.Bedroom || 'N/A'}</p>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="detail-card border rounded p-3 text-center h-100">
                                        <i class="fas fa-bath text-info fa-2x mb-2"></i>
                                        <h6 class="mb-1">Phòng tắm</h6>
                                        <p class="mb-0 fw-bold">${property.detailProperty?.Bath_WC || property.Bathroom || 'N/A'}</p>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="detail-card border rounded p-3 text-center h-100">
                                        <i class="fas fa-tag text-warning fa-2x mb-2"></i>
                                        <h6 class="mb-1">Trạng thái</h6>
                                        <p class="mb-0 fw-bold">${property.Status || 'N/A'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${property.Description ? `
                        <!-- Mô tả -->
                        <div class="property-description mt-4">
                            <h5 class="fw-bold text-secondary mb-3">
                                <i class="fas fa-file-alt me-2"></i>Mô tả
                            </h5>
                            <div class="border rounded p-3 bg-light">
                                <p class="mb-0">${property.Description}</p>
                            </div>
                        </div>                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        
        $('#selectedPropertyInfo').html(html).show();
    }

    // Format currency helper method
    formatCurrency(amount) {
        if (!amount) return '0 VNĐ';
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    // Show error message
    showError(message) {
        $('#selectedPropertyInfo').html(`
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `).show();
    }

    // Navigation methods
    nextStep() {
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.showStep(this.currentStep);
            this.updateStepIndicator();
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.showStep(this.currentStep);
            this.updateStepIndicator();
        }
    }

    showStep(step) {
        $('.step-content').hide();
        $(`#step-${step}`).show();
        
        if (step === 1) {
            $('#prevStepBtn').hide();
        } else {
            $('#prevStepBtn').show();
        }
        
        if (step === this.totalSteps) {
            $('#nextStepBtn').hide();
            $('#submitBtn').show();
        } else {
            $('#nextStepBtn').show();
            $('#submitBtn').hide();
        }
    }    updateStepIndicator() {
        $('.step').each((index, step) => {
            const stepNumber = parseInt($(step).data('step'));
            
            // Remove all previous classes
            $(step).removeClass('active completed');
            
            if (stepNumber < this.currentStep) {
                // Bước đã hoàn thành - màu xanh
                $(step).addClass('completed');
            } else if (stepNumber === this.currentStep) {
                // Bước hiện tại - màu active
                $(step).addClass('active');
            }
            // Bước chưa tới - giữ nguyên style mặc định
        });
    }

    // Submit transaction
    async submitTransaction() {
        console.log('Submitting transaction...');
        // Implementation for transaction submission
    }
}

// Khởi tạo TransactionModal khi DOM đã sẵn sàng
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM ready, creating TransactionModal instance...');
    window.transactionModal = new TransactionModal();
    console.log('TransactionModal instance created and available globally as window.transactionModal');
});
