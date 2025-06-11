// Quản lý modal tạo giao dịch với 4 bước tiến độ
// Updated: Fixed JavaScript syntax and added property details functionality
class TransactionModal {
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

        this.init();
    }

    init() {
        console.log('TransactionModal initialized');

        // Khởi tạo modal khi mở
        $('#createTransactionModal').on('shown.bs.modal', () => {
            console.log('Transaction modal opened, starting initialization...');
            this.resetModal();
            this.loadProperties();
            this.loadCustomers();
            this.updateNavigationButtons(); // Initialize button states
        });

        // Xử lý nút tiếp tục
        $('#nextStepBtn').on('click', () => {
            if (this.currentStep === this.totalSteps) {
                // On final step, submit transaction
                this.submitTransaction();
            } else {
                // Regular next step
                this.nextStep();
            }
        });

        // Xử lý nút quay lại
        $('#prevStepBtn').on('click', () => {
            this.prevStep();
        });        // Khởi tạo các chức năng cho từng bước
        this.initAllSteps(); // Initialize all steps and their events

        // Xử lý submit form
        $('#createTransactionForm').on('submit', (e) => {
            e.preventDefault();
            this.submitTransaction();
        });
    }

    resetModal() {
        console.log('Resetting transaction modal...');

        // Reset về bước 1
        this.currentStep = 1;
        this.selectedProperty = null;
        this.uploadedFiles = [];

        // Reset completion status
        this.stepCompleted = {
            1: false,
            2: false,
            3: false,
            4: false
        };

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
        $('#prevStepBtn').hide();        $('#nextStepBtn').show().text('Tiếp tục').prop('disabled', true);

        console.log('Modal reset completed');
    }

    // Bước 1: Chọn bất động sản
    initStep1() {
        // Tìm kiếm bất động sản
        $('#propertySearchInput').on('input', (e) => {
            this.searchProperties(e.target.value);
        });

        // Lọc theo loại giao dịch
        $(document).on('change', 'input[name="step1_transaction_type"]', (e) => {
            const transactionType = e.target.value;
            this.filterPropertiesByType(transactionType);
        });

        // Chọn bất động sản
        $(document).on('click', '.property-card', (e) => {
            const propertyCard = $(e.currentTarget);
            const propertyId = propertyCard.data('property-id');

            // Lấy dữ liệu từ attribute data
            const propertyData = {
                id: propertyId,
                title: propertyCard.find('.property-title').text(),
                price: propertyCard.find('.property-price').text(),
                location: propertyCard.find('.property-location').text(),
                type: propertyCard.find('.property-type').text(),
                area: propertyCard.data('area'),
                bedrooms: propertyCard.data('bedrooms'),
                bathrooms: propertyCard.data('bathrooms'),
                owner: propertyCard.data('owner'),
                status: propertyCard.data('status'),
                description: propertyCard.data('description')
            };

            this.selectProperty(propertyData);
        });

        // Show property details
        $(document).on('click', '.view-details-btn', (e) => {
            e.stopPropagation(); // Prevent property selection
            const propertyCard = $(e.currentTarget).closest('.property-card');
            const propertyId = propertyCard.data('property-id');
            this.showPropertyDetails(propertyId);
        });
    }

    // Bước 2: Upload file hợp đồng
    initStep2() {
        const uploadZone = $('#uploadZone');
        const fileInput = $('#contractFiles');

        // Drag & drop
        uploadZone.on('dragover', (e) => {
            e.preventDefault();
            uploadZone.addClass('dragover');
        });

        uploadZone.on('dragleave', () => {
            uploadZone.removeClass('dragover');
        });        uploadZone.on('drop', (e) => {
            e.preventDefault();
            uploadZone.removeClass('dragover');
            const files = e.originalEvent.dataTransfer.files;
            this.handleFileSelection({target: {files: files}});
        });

        // File input change
        fileInput.on('change', (e) => {
            this.handleFileSelection(e);
            this.updateNavigationButtons(); // Trigger validation
        });

        // Xóa file
        $(document).on('click', '.remove-file', (e) => {
            const index = $(e.currentTarget).data('index');
            this.removeFile(index);
            this.updateNavigationButtons(); // Trigger validation after removal
        });
    }

    // Bước 3: Thông tin giao dịch
    initStep3() {
        // Xử lý thay đổi loại giao dịch
        $(document).on('change', 'input[name="transaction_type"]', () => {
            const transactionType = $('input[name="transaction_type"]:checked').val();
            this.handleTransactionTypeChange(transactionType);
            this.updateNavigationButtons(); // Trigger validation
        });        // Format giá tiền và trigger validation
        $('#total_price').on('input', (e) => {
            this.formatPriceInput(e.target);
            this.updateNavigationButtons();
        });

        // Customer selection change
        $(document).on('change', '#customer_id', () => {
            this.updateNavigationButtons();
        });

        // Transaction date change
        $(document).on('change', '#transaction_date', () => {
            this.updateNavigationButtons();
        });
    }

    // Bước 4: Phương thức thanh toán
    initStep4() {
        // Xử lý thay đổi phương thức thanh toán
        $(document).on('change', 'input[name="payment_method"]', () => {
            this.updateNavigationButtons();
        });        // Commission amount validation
        $(document).on('input', '#commission_amount', () => {
            this.updateNavigationButtons();
        });    }

    // Load danh sách bất động sản
    async loadProperties() {
        try {
            console.log('Loading properties for transaction modal...');

            // Properties are already rendered by Blade template
            const container = $('#propertyListContainer');
            const existingCards = container.find('.property-card');

            console.log('Found property cards:', existingCards.length);

            if (existingCards.length === 0) {
                // No properties found, show empty state
                container.html('<div class="text-center py-4"><i class="fas fa-home fa-2x text-muted mb-3"></i><p class="text-muted">Không có bất động sản nào để hiển thị</p></div>');
            } else {
                // Properties loaded successfully
                console.log('Properties loaded successfully from Blade template');
            }

        } catch (error) {
            console.error('Error loading properties:', error);
            const container = $('#propertyListContainer');
            container.html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Không thể tải danh sách bất động sản. Vui lòng thử lại.</div>');
        }
    }

    // Hiển thị modal chi tiết bất động sản
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
            return type === 'Rent' ? formatted + ' đ/tháng' : formatted + ' đ';
        };

        let html = '<div class="property-details-content">';

        // Header với thông tin cơ bản
        html += '<div class="row mb-4">';
        html += '<div class="col-12">';
        html += '<div class="d-flex justify-content-between align-items-center mb-3">';
        html += '<h4 class="mb-0 text-primary">';
        html += '<i class="fas fa-home me-2"></i>' + property.Title;
        html += '</h4>';
        html += '<div class="transaction-type-badge">';
        if (property.TypePro === 'Rent') {
            html += '<span class="badge bg-info px-3 py-2"><i class="fas fa-key me-1"></i>Cho Thuê</span>';
        } else {
            html += '<span class="badge bg-success px-3 py-2"><i class="fas fa-hand-holding-usd me-1"></i>Bán</span>';
        }
        html += '</div>';
        html += '</div>';

        html += '<div class="property-basic-info">';
        html += '<p class="text-muted mb-2">';
        html += '<i class="fas fa-map-marker-alt text-danger me-2"></i>';
        html += '<strong>Địa chỉ:</strong> ' + property.Address;
        html += '</p>';
        html += '<div class="price-display mb-3">';
        html += '<span class="h4 text-success fw-bold">';
        html += '<i class="fas fa-tag me-2"></i>' + formatPrice(property.Price, property.TypePro);
        html += '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        // Thông tin chủ sở hữu
        if (property.owner) {
            html += '<div class="row mb-4">';
            html += '<div class="col-12">';
            html += '<h6 class="text-primary border-bottom pb-2">';
            html += '<i class="fas fa-user-tie me-2"></i>Thông tin chủ sở hữu';
            html += '</h6>';
            html += '<div class="card bg-light">';
            html += '<div class="card-body">';
            html += '<div class="row">';
            html += '<div class="col-md-4">';
            html += '<div class="d-flex align-items-center">';
            html += '<i class="fas fa-user text-primary me-2"></i>';
            html += '<div>';
            html += '<small class="text-muted">Tên chủ sở hữu</small>';
            html += '<div class="fw-bold">' + property.owner.Name + '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-md-4">';
            html += '<div class="d-flex align-items-center">';
            html += '<i class="fas fa-phone text-success me-2"></i>';
            html += '<div>';
            html += '<small class="text-muted">Điện thoại</small>';
            html += '<div class="fw-bold">' + (property.owner.Phone || 'Chưa cập nhật') + '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-md-4">';
            html += '<div class="d-flex align-items-center">';
            html += '<i class="fas fa-envelope text-info me-2"></i>';
            html += '<div>';
            html += '<small class="text-muted">Email</small>';
            html += '<div class="fw-bold">' + (property.owner.Email || 'Chưa cập nhật') + '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

        // Chi tiết bất động sản
        if (property.detailProperty) {
            html += '<div class="row mb-4">';
            html += '<div class="col-12">';
            html += '<h6 class="text-primary border-bottom pb-2">';
            html += '<i class="fas fa-info-circle me-2"></i>Chi tiết bất động sản';
            html += '</h6>';
            html += '<div class="row g-3">';

            if (property.detailProperty.Bedroom) {
                html += '<div class="col-md-3">';
                html += '<div class="detail-item">';
                html += '<i class="fas fa-bed text-primary me-2"></i>';
                html += '<strong>Phòng ngủ:</strong> ' + property.detailProperty.Bedroom;
                html += '</div>';
                html += '</div>';
            }

            if (property.detailProperty.Bath_WC) {
                html += '<div class="col-md-3">';
                html += '<div class="detail-item">';
                html += '<i class="fas fa-bath text-info me-2"></i>';
                html += '<strong>Phòng tắm:</strong> ' + property.detailProperty.Bath_WC;
                html += '</div>';
                html += '</div>';
            }

            if (property.detailProperty.Balcony) {
                html += '<div class="col-md-3">';
                html += '<div class="detail-item">';
                html += '<i class="fas fa-home text-warning me-2"></i>';
                html += '<strong>Ban công:</strong> ' + property.detailProperty.Balcony;
                html += '</div>';
                html += '</div>';
            }

            if (property.detailProperty.HouseLength && property.detailProperty.HouseWidth) {
                html += '<div class="col-md-3">';
                html += '<div class="detail-item">';
                html += '<i class="fas fa-ruler-combined text-secondary me-2"></i>';
                html += '<strong>Kích thước:</strong> ' + property.detailProperty.HouseLength + 'm x ' + property.detailProperty.HouseWidth + 'm';
                html += '</div>';
                html += '</div>';
            }

            if (property.detailProperty.view) {
                html += '<div class="col-md-6">';
                html += '<div class="detail-item">';
                html += '<i class="fas fa-eye text-success me-2"></i>';
                html += '<strong>View:</strong> ' + property.detailProperty.view;
                html += '</div>';
                html += '</div>';
            }

            if (property.detailProperty.near) {
                html += '<div class="col-md-6">';
                html += '<div class="detail-item">';
                html += '<i class="fas fa-map-signs text-info me-2"></i>';
                html += '<strong>Gần:</strong> ' + property.detailProperty.near;
                html += '</div>';
                html += '</div>';
            }

            if (property.detailProperty.WaterPrice) {
                html += '<div class="col-md-6">';
                html += '<div class="detail-item">';
                html += '<i class="fas fa-tint text-primary me-2"></i>';
                html += '<strong>Giá nước:</strong> ' + property.detailProperty.WaterPrice;
                html += '</div>';
                html += '</div>';
            }

            if (property.detailProperty.PowerPrice) {
                html += '<div class="col-md-6">';
                html += '<div class="detail-item">';
                html += '<i class="fas fa-bolt text-warning me-2"></i>';
                html += '<strong>Giá điện:</strong> ' + property.detailProperty.PowerPrice;
                html += '</div>';
                html += '</div>';
            }

            html += '</div>';
            html += '</div>';
            html += '</div>';
        } else {
            html += '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Chưa có thông tin chi tiết về bất động sản này.</div>';
        }

        html += '</div>';

        // Set the content
        $('#propertyDetailsContent').html(html);    }

    // Method mới: Chọn property và hiển thị chi tiết
    async selectPropertyAndShowDetails(propertyId) {
        console.log('Selecting property and showing details:', propertyId);

        try {
            // Tải chi tiết property từ API
            const response = await fetch('/test/property-details/' + propertyId);
            if (!response.ok) {
                throw new Error('Không thể tải thông tin bất động sản');
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Không thể tải thông tin bất động sản');
            }

            // Lưu thông tin property đã chọn
            this.selectedProperty = {
                id: propertyId,
                ...data.property
            };

            // Set hidden input
            $('#property_id').val(propertyId);

            // Auto-detect and set transaction type
            if (data.property.TypePro) {
                $('#transaction_type').val(data.property.TypePro);
                $('input[name="transaction_type"][value="' + data.property.TypePro + '"]').prop('checked', true);
            }
              // Ẩn danh sách và hiển thị chi tiết
            $('#propertyListContainer').hide();
            this.showSelectedPropertyDetails(data.property);

            // Update navigation buttons based on validation
            this.updateNavigationButtons();

            console.log('Property selected and details loaded successfully');

        } catch (error) {
            console.error('Error selecting property:', error);
            alert('Không thể tải thông tin chi tiết bất động sản. Vui lòng thử lại.');
            throw error;
        }
    }

    // Method để quay lại chọn property khác
    changeProperty() {
        console.log('Changing property selection...');

        // Clear selection
        this.selectedProperty = null;
        $('.property-card').removeClass('selected-property');

        // Reset form
        $('#property_id').val('');
        $('#transaction_type').val('');
        $('input[name="transaction_type"]').prop('checked', false);

        // Show property list, hide details
        $('#propertyListContainer').show();
        $('#selectedPropertyInfo').hide();

        // Disable next button
        $('#nextStepBtn').prop('disabled', true);

        // Reset all select buttons
        $('.select-property-btn').each(function() {
            $(this).html('<i class="fas fa-check-circle me-1"></i>Chọn BDS này');
            $(this).prop('disabled', false);
        });
    }

    // Các method khác (selectProperty, searchProperties, etc.)
    selectProperty(propertyId) {
        console.log('Selecting property:', propertyId);

        // Find property card
        const propertyCard = $('.property-card[data-property-id="' + propertyId + '"]');
        if (propertyCard.length === 0) {
            console.error('Property card not found for ID:', propertyId);
            return;
        }

        // Remove previous selection
        $('.property-card').removeClass('selected-property');

        // Add selection to current card
        propertyCard.addClass('selected-property');

        // Store selected property data
        this.selectedProperty = {
            id: propertyId,
            title: propertyCard.find('.property-title').text().trim(),
            address: propertyCard.find('.property-address').text().trim(),
            price: propertyCard.find('.property-price').text().trim(),
            type: propertyCard.data('type')
        };

        // Set hidden input
        $('#property_id').val(propertyId);

        // Auto-detect and set transaction type
        const propertyType = propertyCard.data('type');
        if (propertyType) {
            $('#transaction_type').val(propertyType);
            $('input[name="transaction_type"][value="' + propertyType + '"]').prop('checked', true);
        }

        // Show selected property info
        this.showSelectedProperty();

        // Enable next button
        $('#nextStepBtn').prop('disabled', false);

        console.log('Property selected successfully:', this.selectedProperty);
    }

    showSelectedProperty() {
        if (!this.selectedProperty) return;

        const html = '<div class="selected-property-card border border-success rounded p-4 mb-4">' +
            '<div class="d-flex justify-content-between align-items-center mb-3">' +
            '<h5 class="text-success mb-0">' +
            '<i class="fas fa-check-circle me-2"></i>Bất động sản đã chọn' +
            '</h5>' +
            '<button type="button" class="btn btn-outline-secondary btn-sm" onclick="transactionModal.clearSelection()">' +
            '<i class="fas fa-times me-1"></i>Chọn lại' +
            '</button>' +
            '</div>' +
            '<div class="row">' +
            '<div class="col-md-8">' +
            '<h6 class="text-primary">' + this.selectedProperty.title + '</h6>' +
            '<p class="text-muted mb-2">' +
            '<i class="fas fa-map-marker-alt me-2"></i>' + this.selectedProperty.address +
            '</p>' +
            '<p class="h6 text-success">' + this.selectedProperty.price + '</p>' +
            '</div>' +
            '<div class="col-md-4 text-end">' +
            '<button type="button" class="btn btn-info btn-sm mb-2 w-100" onclick="transactionModal.showPropertyDetails(\'' + this.selectedProperty.id + '\')">' +
            '<i class="fas fa-eye me-1"></i>Xem Chi Tiết' +
            '</button>' +
            '</div>' +
            '</div>' +
            '</div>';

        $('#selectedPropertyInfo').html(html).show();
        $('#propertyListContainer').hide();
    }

    clearSelection() {
        this.selectedProperty = null;
        $('.property-card').removeClass('selected-property');
        $('#selectedPropertyInfo').hide();
        $('#propertyListContainer').show();
        $('#property_id').val('');        $('#nextStepBtn').prop('disabled', true);
    }

    searchProperties(searchTerm) {
        const cards = $('.property-card');

        if (!searchTerm) {
            cards.show();
            return;
        }

        cards.each(function() {
            const card = $(this);
            const title = card.find('.property-title').text().toLowerCase();
            const address = card.find('.property-address').text().toLowerCase();
            const searchTermLower = searchTerm.toLowerCase();

            if (title.includes(searchTermLower) || address.includes(searchTermLower)) {
                card.show();
            } else {
                card.hide();
            }
        });    }

    filterPropertiesByType(type) {
        const cards = $('.property-card');

        if (!type) {
            cards.show();
            return;
        }
          cards.each(function() {
            const card = $(this);
            const cardType = card.data('type');

            if (cardType === type) {
                card.show();
            } else {
                card.hide();
            }
        });
    }

    // Step validation methods
    validateStep1() {
        // Check if property is selected
        if (!this.selectedProperty || !$('#property_id').val()) {
            return false;
        }

        this.stepCompleted[1] = true;
        return true;
    }

    validateStep2() {
        // Step 2 is optional - file upload
        // Return true even if no files are uploaded
        this.stepCompleted[2] = true;
        return true;
    }

    validateStep3() {
        // Check required fields
        const customerId = $('#customer_id').val();
        const transactionDate = $('#transaction_date').val();
        const totalPrice = $('#total_price').val();

        if (!customerId || !transactionDate || !totalPrice) {
            return false;
        }

        // Additional validation for rental properties
        if (this.selectedProperty && this.selectedProperty.TypePro === 'Rent') {
            const rentalMonths = $('#rental_months').val();
            const paymentType = $('#payment_type').val();
            const monthlyPrice = $('#monthly_price').val();

            if (!rentalMonths || !paymentType || !monthlyPrice) {
                return false;
            }
        }

        this.stepCompleted[3] = true;
        return true;
    }

    validateStep4() {
        // Check if payment method is selected
        const paymentMethod = $('input[name="payment_method"]:checked').val();

        if (!paymentMethod) {
            return false;
        }

        this.stepCompleted[4] = true;
        return true;
    }

    // Initialize all step-specific functionality
    initAllSteps() {
        this.initStep1();
        this.initStep2();
        this.initStep3();
        this.initStep4();
        this.initStepEvents();
    }

    // ============ CRITICAL MISSING METHODS ============

    // Navigation control
    updateNavigationButtons() {
        const isCurrentStepValid = this.validateCurrentStep();
        const isLastStep = this.currentStep === this.totalSteps;

        // Update Previous button
        $('#prevStepBtn').toggle(this.currentStep > 1);

        // Update Next/Submit button
        if (isLastStep) {
            $('#nextStepBtn').text('Hoàn tất giao dịch').prop('disabled', !isCurrentStepValid);
        } else {
            $('#nextStepBtn').text('Tiếp tục').prop('disabled', !isCurrentStepValid);
        }

        console.log(`Step ${this.currentStep}: Valid=${isCurrentStepValid}, LastStep=${isLastStep}`);
    }

    // Step display control
    showStep(stepNumber) {
        // Hide all steps
        for (let i = 1; i <= this.totalSteps; i++) {
            $(`#step${i}`).hide();
        }

        // Show current step
        $(`#step${stepNumber}`).show();
        this.currentStep = stepNumber;

        console.log(`Showing step ${stepNumber}`);
    }

    // Step indicator update
    updateStepIndicator() {
        $('.step').each((index, step) => {
            const stepNum = index + 1;
            const $step = $(step);

            $step.removeClass('active completed');

            if (stepNum < this.currentStep) {
                $step.addClass('completed');
            } else if (stepNum === this.currentStep) {
                $step.addClass('active');
            }
        });
    }

    // Validation error display
    showStepValidationError(message) {
        this.showToast('error', message || 'Vui lòng hoàn thành thông tin bắt buộc');
    }

    // Toast notification system
    showToast(type, message, duration = 5000) {
        const toastClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

        const toast = $(`
            <div class="alert ${toastClass} alert-dismissible fade show position-fixed"
                 style="top: 20px; right: 20px; z-index: 10000; min-width: 300px;">
                <i class="fas ${iconClass} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

        $('body').append(toast);

        setTimeout(() => {
            toast.alert('close');
        }, duration);
    }

    // Navigation methods
    nextStep() {
        if (this.currentStep < this.totalSteps && this.validateCurrentStep()) {
            this.markStepCompleted(this.currentStep);
            this.currentStep++;
            this.showStep(this.currentStep);
            this.updateStepIndicator();
            this.updateNavigationButtons();

            console.log(`Moved to step ${this.currentStep}`);
        } else if (!this.validateCurrentStep()) {
            this.showStepValidationError();
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.showStep(this.currentStep);
            this.updateStepIndicator();
            this.updateNavigationButtons();

            console.log(`Moved back to step ${this.currentStep}`);
        }
    }

    // File handling methods
    handleFileSelection(event) {
        const files = event.target.files;

        if (!files || files.length === 0) return;

        Array.from(files).forEach(file => {
            // Validate file type
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                this.showToast('error', `File ${file.name} không đúng định dạng cho phép (PDF, JPG, PNG)`);
                return;
            }

            // Validate file size (max 10MB)
            if (file.size > 10 * 1024 * 1024) {
                this.showToast('error', `File ${file.name} quá lớn (tối đa 10MB)`);
                return;
            }

            // Add to uploaded files
            const fileData = {
                id: Date.now() + Math.random(),
                name: file.name,
                size: file.size,
                file: file
            };

            this.uploadedFiles.push(fileData);
        });

        this.updateFilesList();
        this.updateNavigationButtons();

        console.log(`Added ${files.length} files, total: ${this.uploadedFiles.length}`);
    }

    removeFile(fileId) {
        this.uploadedFiles = this.uploadedFiles.filter(f => f.id !== fileId);
        this.updateFilesList();
        this.updateNavigationButtons();

        console.log(`Removed file, remaining: ${this.uploadedFiles.length}`);
    }

    // Transaction type handling
    handleTransactionTypeChange(type = null) {
        const transactionType = type || $('input[name="step3_transaction_type"]:checked').val();

        if (transactionType === 'Rent') {
            $('#rentalFields').show();
            $('#rental_months, #payment_type, #monthly_price').prop('required', true);
        } else {
            $('#rentalFields').hide();
            $('#rental_months, #payment_type, #monthly_price').prop('required', false);
        }

        console.log(`Transaction type changed to: ${transactionType}`);
    }

    // Customer management
    async loadCustomers() {
        try {
            const response = await fetch('/agent/api/customers');
            if (!response.ok) throw new Error('Không thể tải danh sách khách hàng');

            const data = await response.json();
            if (data.success) {
                this.customers = data.customers;
                this.populateCustomerSelect();
                console.log(`Loaded ${this.customers.length} customers`);
            }
        } catch (error) {
            console.error('Error loading customers:', error);
            this.showToast('error', 'Không thể tải danh sách khách hàng');
        }
    }

    populateCustomerSelect() {
        const $select = $('#customer_id');
        $select.empty().append('<option value="">-- Chọn khách hàng --</option>');

        this.customers.forEach(customer => {
            $select.append(`
                <option value="${customer.UserID}"
                        data-name="${customer.Name}"
                        data-phone="${customer.Phone || ''}"
                        data-email="${customer.Email || ''}">
                    ${customer.Name} - ${customer.Phone || 'N/A'}
                </option>
            `);
        });
    }

    showCustomerDetails() {
        const selectedOption = $('#customer_id option:selected');
        if (selectedOption.val()) {
            const customerInfo = `
                <div class="alert alert-info">
                    <h6><i class="fas fa-user me-2"></i>Thông tin khách hàng</h6>
                    <p class="mb-1"><strong>Tên:</strong> ${selectedOption.data('name')}</p>
                    <p class="mb-1"><strong>Điện thoại:</strong> ${selectedOption.data('phone') || 'Chưa cập nhật'}</p>
                    <p class="mb-0"><strong>Email:</strong> ${selectedOption.data('email') || 'Chưa cập nhật'}</p>
                </div>
            `;
            $('#selectedCustomerInfo').html(customerInfo).show();
        } else {
            this.hideCustomerDetails();
        }
    }

    hideCustomerDetails() {
        $('#selectedCustomerInfo').hide();
    }

    // Form submission
    async submitTransaction() {
        if (!this.validateAllSteps()) {
            this.showToast('error', 'Vui lòng hoàn thành tất cả thông tin bắt buộc');
            return;
        }

        const formData = new FormData();

        // Basic transaction data
        formData.append('property_id', $('#property_id').val());
        formData.append('customer_id', $('#customer_id').val());
        formData.append('transaction_type', $('#transaction_type').val());
        formData.append('transaction_date', $('#transaction_date').val());
        formData.append('total_price', $('#total_price').val().replace(/[^\d]/g, ''));
        formData.append('payment_method', $('input[name="payment_method"]:checked').val());
        formData.append('commission_amount', $('#commission_amount').val() || '0');
        formData.append('notes', $('#notes').val() || '');

        // Rental-specific fields
        if (this.selectedProperty?.TypePro === 'Rent') {
            formData.append('rental_months', $('#rental_months').val());
            formData.append('payment_type', $('#payment_type').val());
            formData.append('monthly_price', $('#monthly_price').val().replace(/[^\d]/g, ''));
        }

        // Add files
        this.uploadedFiles.forEach((fileData, index) => {
            formData.append(`contract_documents[${index}]`, fileData.file);
        });

        try {
            this.showToast('info', 'Đang tạo giao dịch...');

            const response = await fetch('/agent/transactions', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('success', 'Tạo giao dịch thành công!');
                setTimeout(() => {
                    $('#createTransactionModal').modal('hide');
                    location.reload();
                }, 2000);
            } else {
                throw new Error(data.message || 'Có lỗi xảy ra khi tạo giao dịch');
            }
        } catch (error) {
            console.error('Error submitting transaction:', error);
            this.showToast('error', error.message || 'Không thể tạo giao dịch');
        }
    }

    // Validation methods
    validateAllSteps() {
        return this.validateStep1() && this.validateStep2() &&
               this.validateStep3() && this.validateStep4();
    }

    validateCurrentStep() {
        switch (this.currentStep) {
            case 1: return this.validateStep1();
            case 2: return this.validateStep2();
            case 3: return this.validateStep3();
            case 4: return this.validateStep4();
            default: return false;
        }
    }
}

// Initialize when DOM is ready
$(document).ready(function() {
    window.transactionModal = new TransactionModal();
});
