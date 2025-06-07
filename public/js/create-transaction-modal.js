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
        this.initStep1(); // Chọn bất động sản
        this.initStep2(); // Upload file hợp đồng
        this.initStep3(); // Thông tin giao dịch
        this.initStep4(); // Phương thức thanh toán
        
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
        $('#prevStepBtn').hide();
        $('#nextStepBtn').show().text('Tiếp tục').prop('disabled', true);
        
        console.log('Modal reset completed');
    }

    // Bước 1: Chọn bất động sản
    initStep1() {
        // Tìm kiếm bất động sản
        $('#propertySearchInput').on('input', (e) => {
            this.searchProperties(e.target.value);
        });        // Lọc theo loại giao dịch
        $(document).on('change', 'input[name="step1_transaction_type"]', (e) => {
            const transactionType = e.target.value;
            this.filterPropertiesByType(transactionType);
        });
        
        // Chọn bất động sản
        $(document).on('click', '.property-card', (e) => {
            const propertyCard = $(e.currentTarget);
            const propertyId = propertyCard.data('property-id');
            
            if (!propertyId) {
                console.error('Property ID not found');
                return;
            }
            
            // Get property data from card attributes
            const propertyData = {
                id: propertyId,
                title: propertyCard.data('property-title') || propertyCard.find('.property-title').text().trim(),
                price: propertyCard.data('property-price'),
                type: propertyCard.data('property-type'),
                address: propertyCard.data('property-address'),
                ward: propertyCard.data('property-ward'),
                district: propertyCard.data('property-district'), 
                province: propertyCard.data('property-province'),
                propertytype: propertyCard.data('property-propertytype')
            };
            
            this.selectProperty(propertyData);
        });

        // Nút chọn BDS
        $(document).on('click', '.select-property-btn', (e) => {
            e.stopPropagation();
            const propertyCard = $(e.currentTarget).closest('.property-card');
            const propertyId = propertyCard.data('property-id');
            
            if (!propertyId) {
                console.error('Property ID not found');
                return;
            }
            
            // Get property data from card attributes
            const propertyData = {
                id: propertyId,
                title: propertyCard.data('property-title') || propertyCard.find('.property-title').text().trim(),
                price: propertyCard.data('property-price'),
                type: propertyCard.data('property-type'),
                address: propertyCard.data('property-address'),
                ward: propertyCard.data('property-ward'),
                district: propertyCard.data('property-district'), 
                province: propertyCard.data('property-province'),
                propertytype: propertyCard.data('property-propertytype')
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
        });

        // Format giá tiền và trigger validation
        $('#total_price').on('input', (e) => {
            this.formatPrice(e.target);
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
        // Bước 1: Phải chọn bất động sản
        const isValid = this.selectedProperty !== null && $('#property_id').val() !== '';
        
        // Visual feedback
        this.updateFieldValidation('#property_id', isValid);
        
        console.log('Step 1 validation - Property selected:', this.selectedProperty, 'Property ID:', $('#property_id').val());
        return isValid;
    }

    validateStep2() {
        // Bước 2: Tải file tài liệu (không bắt buộc)
        return true; // Optional step
    }

    validateStep3() {
        // Bước 3: Phải điền đầy đủ thông tin giao dịch
        const customerId = $('#customer_id').val();
        const transactionType = $('input[name="transaction_type"]:checked').val();
        const totalPrice = $('#total_price').val();
        const transactionDate = $('#transaction_date').val();
        
        // Validate individual fields
        const validations = {
            customer: !!customerId,
            type: !!transactionType,
            price: !!totalPrice && parseFloat(totalPrice) > 0,
            date: !!transactionDate
        };
        
        const isValid = Object.values(validations).every(v => v);
        
        // Visual feedback for each field
        this.updateFieldValidation('#customer_id', validations.customer);
        this.updateFieldValidation('input[name="transaction_type"]', validations.type);
        this.updateFieldValidation('#total_price', validations.price);
        this.updateFieldValidation('#transaction_date', validations.date);
        
        console.log('Step 3 validation:', {
            customerId,
            transactionType,
            totalPrice: !!totalPrice,
            transactionDate: !!transactionDate,
            isValid
        });
        
        return isValid;
    }

    validateStep4() {
        // Bước 4: Phải chọn phương thức thanh toán
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        const isValid = !!paymentMethod;
        
        // Visual feedback
        this.updateFieldValidation('input[name="payment_method"]', isValid);
        
        console.log('Step 4 validation - Payment method:', paymentMethod);
        return isValid;
    }

    // Update visual validation state for form fields
    updateFieldValidation(selector, isValid) {
        const elements = $(selector);
        
        // Handle different input types
        elements.each(function() {
            const $element = $(this);
            const $parent = $element.closest('.form-group, .mb-3, .col-md-6, .col-12');
            
            // Remove existing validation classes
            $element.removeClass('is-valid is-invalid');
            $parent.find('.valid-feedback, .invalid-feedback').remove();
            
            if (isValid === null || isValid === undefined) {
                // Neutral state - no validation styling
                return;
            }
            
            // Add validation classes
            $element.addClass(isValid ? 'is-valid' : 'is-invalid');
            
            // Add feedback message for invalid fields
            if (!isValid) {
                let message = 'Trường này là bắt buộc';
                
                // Custom messages based on field type
                if ($element.attr('id') === 'property_id') {
                    message = 'Vui lòng chọn bất động sản';
                } else if ($element.attr('id') === 'customer_id') {
                    message = 'Vui lòng chọn khách hàng';
                } else if ($element.attr('name') === 'transaction_type') {
                    message = 'Vui lòng chọn loại giao dịch';
                } else if ($element.attr('id') === 'total_price') {
                    message = 'Vui lòng nhập giá hợp lệ';
                } else if ($element.attr('id') === 'transaction_date') {
                    message = 'Vui lòng chọn ngày giao dịch';
                } else if ($element.attr('name') === 'payment_method') {
                    message = 'Vui lòng chọn phương thức thanh toán';
                }
                
                // Add invalid feedback
                const feedbackHtml = `<div class="invalid-feedback d-block">${message}</div>`;
                if ($element.attr('type') === 'radio') {
                    // For radio buttons, add feedback after the radio group
                    $parent.append(feedbackHtml);
                } else {
                    // For other inputs, add feedback after the input
                    $element.after(feedbackHtml);
                }
            }
        });    }

    // Step validation methods
    validateStep1() {
        // Check if property is selected
        const hasProperty = this.selectedProperty !== null;
        
        // Check if transaction type is selected
        const hasTransactionType = $('input[name="step1_transaction_type"]:checked').length > 0;
        
        const isValid = hasProperty && hasTransactionType;
        
        console.log(`Step 1 validation: Property=${hasProperty}, TransactionType=${hasTransactionType}, Valid=${isValid}`);
        
        return isValid;
    }    validateStep2() {
        // Step 2: File upload is optional, always return true
        // But we can provide visual feedback about files
        const fileCount = this.uploadedFiles.length;
        console.log(`Step 2 validation: Files=${fileCount}, Valid=true (optional)`);
        
        return true; // Optional step - always valid
    }

    validateStep3() {
        // Check required fields
        const customerId = $('#customer_id').val();
        const transactionDate = $('#transaction_date').val();
        const transactionPrice = $('#transaction_price').val();
        
        const hasCustomer = customerId && customerId !== '';
        const hasDate = transactionDate && transactionDate !== '';
        const hasPrice = transactionPrice && transactionPrice !== '' && parseFloat(transactionPrice.replace(/[^\d.]/g, '')) > 0;
        
        const isValid = hasCustomer && hasDate && hasPrice;
        
        console.log(`Step 3 validation: Customer=${hasCustomer}, Date=${hasDate}, Price=${hasPrice}, Valid=${isValid}`);
        
        return isValid;
    }

    validateStep4() {
        // Check if payment method is selected
        const hasPaymentMethod = $('input[name="payment_method"]:checked').length > 0;
        
        // If "other" payment method is selected, check if description is provided
        const otherSelected = $('input[name="payment_method"]:checked').val() === 'other';
        const hasOtherDescription = otherSelected ? $('#other_payment_description').val().trim() !== '' : true;
        
        const isValid = hasPaymentMethod && hasOtherDescription;
        
        console.log(`Step 4 validation: PaymentMethod=${hasPaymentMethod}, OtherDesc=${hasOtherDescription}, Valid=${isValid}`);
        
        return isValid;
    }

    // Generic validation method
    validateCurrentStep() {
        switch (this.currentStep) {
            case 1:
                return this.validateStep1();
            case 2:
                return this.validateStep2();
            case 3:
                return this.validateStep3();
            case 4:
                return this.validateStep4();
            default:
                return false;
        }
    }

    // Mark step as completed and update UI
    markStepCompleted(stepNumber) {
        this.stepCompleted[stepNumber] = true;
        this.updateStepIndicator();
        console.log(`Step ${stepNumber} marked as completed`);
    }

    // Check if step can be accessed
    canAccessStep(stepNumber) {
        // Can always go back to previous steps
        if (stepNumber <= this.currentStep) {
            return true;
        }
        
        // To go forward, all previous steps must be completed
        for (let i = 1; i < stepNumber; i++) {
            if (!this.stepCompleted[i]) {
                console.log(`Cannot access step ${stepNumber}: Step ${i} not completed`);
                return false;
            }
        }
          return true;
    }

    // Navigation methods
    nextStep() {
        // Validate current step before proceeding
        if (!this.validateCurrentStep()) {
            this.showStepValidationError();
            return;
        }
        
        // Show loading state
        this.showLoading(true);
        
        // Mark current step as completed
        this.markStepCompleted(this.currentStep);
        
        // Move to next step if possible
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            
            // Simulate loading for smooth transition
            setTimeout(() => {
                this.updateStepIndicator();
                this.showStep(this.currentStep);
                this.updateNavigationButtons();
                this.showLoading(false);
                
                // Show success toast for step completion
                this.showToast('success', `Bước ${this.currentStep - 1} đã hoàn thành!`, 2000);
                
                console.log(`Moved to step ${this.currentStep}`);
            }, 300);
        } else {
            this.showLoading(false);
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.showLoading(true);
            
            this.currentStep--;
            
            setTimeout(() => {
                this.updateStepIndicator();
                this.showStep(this.currentStep);
                this.updateNavigationButtons();
                this.showLoading(false);
                
                console.log(`Moved back to step ${this.currentStep}`);
            }, 200);
        }
    }

    // Show/hide loading overlay
    showLoading(show) {
        if (show) {
            if (!$('#transactionModalLoading').length) {
                const loadingHtml = `
                    <div id="transactionModalLoading" class="position-absolute w-100 h-100" style="
                        top: 0; left: 0; right: 0; bottom: 0;
                        background: rgba(255,255,255,0.8);
                        z-index: 9999;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    ">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="text-muted">Đang xử lý...</div>
                        </div>
                    </div>
                `;
                $('#createTransactionModal .modal-body').css('position', 'relative').append(loadingHtml);
            } else {
                $('#transactionModalLoading').show();
            }
        } else {
            $('#transactionModalLoading').hide();
        }
    }    // Submit transaction
    async submitTransaction() {
        console.log('Submitting transaction...');
        
        // If on last step and next button clicked, validate and submit
        if (this.currentStep === this.totalSteps) {
            if (!this.validateCurrentStep()) {
                this.showStepValidationError();
                return;
            }
            
            // Mark final step as completed
            this.markStepCompleted(this.currentStep);
            
            try {
                this.showLoading(true);
                
                // Collect form data
                const formData = new FormData();
                
                // Basic transaction data
                formData.append('property_id', $('#property_id').val());
                formData.append('customer_id', $('#customer_id').val());
                formData.append('transaction_type', $('input[name="step1_transaction_type"]:checked').val());
                formData.append('transaction_date', $('#transaction_date').val());
                formData.append('transaction_price', $('#transaction_price').val().replace(/[^\d.]/g, ''));
                formData.append('payment_method', $('input[name="payment_method"]:checked').val());
                formData.append('transaction_notes', $('#transaction_notes').val() || '');
                
                // Add other payment description if selected
                if ($('input[name="payment_method"]:checked').val() === 'other') {
                    formData.append('other_payment_description', $('#other_payment_description').val());
                }
                
                // Add uploaded files
                this.uploadedFiles.forEach((file, index) => {
                    formData.append(`documents[${index}]`, file);
                });
                
                // Add CSRF token
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                
                // Submit via AJAX
                const response = await fetch('/agent/transactions', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                const result = await response.json();
                
                this.showLoading(false);
                
                if (response.ok && result.success) {
                    this.showToast('success', 'Giao dịch đã được tạo thành công!', 3000);
                    
                    // Close modal after short delay
                    setTimeout(() => {
                        $('#createTransactionModal').modal('hide');
                        
                        // Reload page or update transactions list if needed
                        if (typeof window.refreshTransactionsList === 'function') {
                            window.refreshTransactionsList();
                        } else {
                            // Fallback: reload page
                            window.location.reload();
                        }
                    }, 2000);
                } else {
                    throw new Error(result.message || 'Có lỗi xảy ra khi tạo giao dịch');
                }
                
            } catch (error) {
                this.showLoading(false);
                console.error('Error submitting transaction:', error);
                this.showToast('error', error.message || 'Có lỗi xảy ra khi tạo giao dịch');
            }
        } else {
            // Regular next step
            this.nextStep();
        }
    }// Load customers (if needed)
    async loadCustomers() {
        console.log('Loading customers...');
        // Implementation for loading customers
    }

    // Method hiển thị chi tiết property đã chọn
    showSelectedPropertyDetails(property) {
        console.log('Showing property details:', property);
        
        // Set property name
        $('#selectedPropertyName').text(property.Name || 'Không có tên');
        
        // Set address details
        const fullAddress = [
            property.Address,
            property.Ward ? `Phường ${property.Ward}` : '',
            property.District ? `Quận ${property.District}` : '',
            property.City || ''
        ].filter(Boolean).join(', ');
        
        $('.address-full').html(`
            <div class="fw-medium text-dark">${fullAddress}</div>
        `);
        
        // Set property info grid
        const propertyInfoRows = [];
        
        // Row 1: Giá và Diện tích
        if (property.Price || property.Area) {
            propertyInfoRows.push(`
                <div class="col-6">
                    <div class="info-item">
                        <i class="fas fa-money-bill-wave text-success me-2"></i>
                        <span class="label">Giá:</span>
                        <div class="value text-success fw-bold">${this.formatPrice(property.Price)}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="info-item">
                        <i class="fas fa-expand-arrows-alt text-info me-2"></i>
                        <span class="label">Diện tích:</span>
                        <div class="value">${property.Area || 'N/A'} m²</div>
                    </div>
                </div>
            `);
        }
        
        // Row 2: Phòng ngủ và Phòng tắm
        if (property.Bedroom || property.Bathroom) {
            propertyInfoRows.push(`
                <div class="col-6">
                    <div class="info-item">
                        <i class="fas fa-bed text-primary me-2"></i>
                        <span class="label">Phòng ngủ:</span>
                        <div class="value">${property.Bedroom || 'N/A'}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="info-item">
                        <i class="fas fa-bath text-info me-2"></i>
                        <span class="label">Phòng tắm:</span>
                        <div class="value">${property.Bathroom || 'N/A'}</div>
                    </div>
                </div>
            `);
        }
        
        // Row 3: Loại giao dịch và Hướng nhà
        if (property.TypePro || property.Direction) {
            propertyInfoRows.push(`
                <div class="col-6">
                    <div class="info-item">
                        <i class="fas fa-handshake text-warning me-2"></i>
                        <span class="label">Loại:</span>
                        <div class="value">${this.getTransactionTypeText(property.TypePro)}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="info-item">
                        <i class="fas fa-compass text-secondary me-2"></i>
                        <span class="label">Hướng:</span>
                        <div class="value">${property.Direction || 'N/A'}</div>
                    </div>
                </div>
            `);
        }
        
        // Set property info grid content
        $('.property-info-grid .row').html(propertyInfoRows.join(''));
        
        // Thêm thông tin chủ sở hữu nếu có
        if (property.owner) {
            $('.property-info-grid .row').append(`
                <div class="col-12 mt-3">
                    <div class="owner-info border-top pt-3">
                        <h6 class="mb-2">
                            <i class="fas fa-user-circle text-primary me-2"></i>
                            Thông tin chủ sở hữu
                        </h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="info-item">
                                    <span class="label">Tên:</span>
                                    <div class="value fw-medium">${property.owner.Name || 'N/A'}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-item">
                                    <span class="label">SĐT:</span>
                                    <div class="value">${property.owner.Phone || 'N/A'}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-item">
                                    <span class="label">Email:</span>
                                    <div class="value">${property.owner.Email || 'N/A'}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-item">
                                    <span class="label">CCCD:</span>
                                    <div class="value">${property.owner.Identity || 'N/A'}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
        
        // Show selected property info, hide property list
        $('#selectedPropertyInfo').show();
        $('#propertyListContainer').hide();
          console.log('Property details displayed successfully');
    }

    // Core UI Methods
    updateStepIndicator() {
        console.log(`Updating step indicator for step ${this.currentStep}`);
        
        // Update step indicator dots
        $('.step-indicator .step').each((index, element) => {
            const stepNum = index + 1;
            const $step = $(element);
            
            // Remove all state classes
            $step.removeClass('active completed');
            
            if (stepNum < this.currentStep || this.stepCompleted[stepNum]) {
                $step.addClass('completed');
            } else if (stepNum === this.currentStep) {
                $step.addClass('active');
            }
        });
        
        // Update progress bar if exists
        const progressPercentage = ((this.currentStep - 1) / (this.totalSteps - 1)) * 100;
        $('.step-progress-bar').css('width', `${progressPercentage}%`);
    }

    showStep(stepNumber) {
        console.log(`Showing step ${stepNumber}`);
        
        // Hide all steps
        $('.step-content').hide();
        
        // Show current step
        $(`#step${stepNumber}`).show();
        
        // Update step navigation
        this.updateNavigationButtons();
        
        // Auto-focus first input in the step if exists
        setTimeout(() => {
            $(`#step${stepNumber} input:visible:first, #step${stepNumber} select:visible:first`).focus();
        }, 100);
    }

    updateNavigationButtons() {
        const isFirstStep = this.currentStep === 1;
        const isLastStep = this.currentStep === this.totalSteps;
        const currentStepValid = this.validateCurrentStep();
        
        // Previous button
        if (isFirstStep) {
            $('#prevStepBtn').hide();
        } else {
            $('#prevStepBtn').show().prop('disabled', false);
        }
        
        // Next/Submit button
        const $nextBtn = $('#nextStepBtn');
        $nextBtn.show();
        
        if (isLastStep) {
            $nextBtn.text('Tạo giao dịch').removeClass('btn-primary').addClass('btn-success');
        } else {
            $nextBtn.text('Tiếp tục').removeClass('btn-success').addClass('btn-primary');
        }
        
        // Enable/disable based on validation
        $nextBtn.prop('disabled', !currentStepValid);
        
        console.log(`Navigation updated - Step: ${this.currentStep}, Valid: ${currentStepValid}, IsLast: ${isLastStep}`);
    }

    handleFileSelection(files) {
        console.log('Processing selected files:', files);
        
        if (!files || files.length === 0) return;
        
        Array.from(files).forEach(file => {
            // Validate file type
            const validTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png',
                'image/jpg'
            ];
            
            if (!validTypes.includes(file.type)) {
                this.showToast('error', `File ${file.name} không đúng định dạng. Chỉ chấp nhận PDF, DOC, DOCX, JPG, PNG.`);
                return;
            }
            
            // Validate file size (max 10MB)
            if (file.size > 10 * 1024 * 1024) {
                this.showToast('error', `File ${file.name} quá lớn. Kích thước tối đa 10MB.`);
                return;
            }
            
            // Check if file already exists
            const existingFile = this.uploadedFiles.find(f => f.name === file.name && f.size === file.size);
            if (existingFile) {
                this.showToast('warning', `File ${file.name} đã được chọn.`);
                return;
            }
            
            // Add to uploaded files array
            this.uploadedFiles.push(file);
            
            console.log(`Added file: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)}MB)`);
        });
        
        // Update file list UI
        this.updateFileList();
        
        // Clear input to allow re-selection of same file
        $('#documentUpload').val('');
        
        this.showToast('success', `Đã thêm ${files.length} file thành công.`);
    }

    removeFile(index) {
        if (index >= 0 && index < this.uploadedFiles.length) {
            const removedFile = this.uploadedFiles.splice(index, 1)[0];
            console.log(`Removed file: ${removedFile.name}`);
            
            this.updateFileList();
            this.showToast('info', `Đã xóa file ${removedFile.name}.`);
        }
    }

    updateFileList() {
        const $fileList = $('#uploadedFilesList');
        
        if (this.uploadedFiles.length === 0) {
            $fileList.html('<div class="text-muted text-center py-3">Chưa có file nào được chọn</div>');
            return;
        }
        
        let html = '';
        this.uploadedFiles.forEach((file, index) => {
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const fileIcon = this.getFileIcon(file.type);
            
            html += `
                <div class="uploaded-file-item d-flex align-items-center justify-content-between p-2 border rounded mb-2">
                    <div class="d-flex align-items-center">
                        <i class="${fileIcon} me-2 text-primary"></i>
                        <div>
                            <div class="file-name fw-medium">${file.name}</div>
                            <div class="file-size text-muted small">${fileSize} MB</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="transactionModal.removeFile(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });
        
        $fileList.html(html);
    }

    getFileIcon(fileType) {
        const iconMap = {
            'application/pdf': 'fas fa-file-pdf',
            'application/msword': 'fas fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'fas fa-file-word',
            'image/jpeg': 'fas fa-file-image',
            'image/jpg': 'fas fa-file-image',
            'image/png': 'fas fa-file-image'
        };
        
        return iconMap[fileType] || 'fas fa-file';
    }    handleTransactionTypeChange(transactionType) {
        console.log(`Transaction type changed to: ${transactionType}`);
        
        // Update hidden input
        $('#transaction_type').val(transactionType);
        
        // Update property price based on transaction type if property is selected
        if (this.selectedProperty) {
            const price = this.selectedProperty.Price;
            $('#transaction_price').val(price || 0);
            
            // Update price display
            const formattedPrice = this.formatPrice(price);
            $('.price-display').text(formattedPrice);
        }
        
        // Update form labels based on transaction type
        this.updateFormLabels(transactionType);
    }

    updateFormLabels(transactionType) {
        const labels = {
            'sell': {
                priceLabel: 'Giá bán',
                noteLabel: 'Ghi chú về việc bán'
            },
            'rent': {
                priceLabel: 'Giá thuê/tháng',
                noteLabel: 'Ghi chú về việc cho thuê'
            },
            'buy': {
                priceLabel: 'Giá mua',
                noteLabel: 'Ghi chú về việc mua'
            },
            'lease': {
                priceLabel: 'Giá thuê/tháng',
                noteLabel: 'Ghi chú về việc thuê'
            }
        };
        
        const currentLabels = labels[transactionType] || labels['sell'];
        
        // Update labels in the form
        $('label[for="transaction_price"]').text(currentLabels.priceLabel);
        $('label[for="transaction_notes"]').text(currentLabels.noteLabel);
        $('#transaction_notes').attr('placeholder', currentLabels.noteLabel);
    }    formatPriceInput(element) {
        if (!element) return;
        
        const $element = $(element);
        let value = $element.val();
        
        // Remove all non-digit characters except decimal point
        value = value.replace(/[^\d.]/g, '');
        
        // Convert to number and format
        const numValue = parseFloat(value) || 0;
        
        // Format with thousand separators
        const formatted = numValue.toLocaleString('vi-VN');
        
        // Update the input
        $element.val(formatted);
        
        // Update any price displays
        $('.price-display').text(this.formatPrice(numValue));
    }    showStepValidationError() {
        let errorMessage = '';
        
        switch (this.currentStep) {
            case 1:
                errorMessage = 'Vui lòng chọn bất động sản và loại giao dịch.';
                break;
            case 2:
                errorMessage = 'Bước này là tùy chọn. Bạn có thể tiếp tục.';
                break;
            case 3:
                errorMessage = 'Vui lòng điền đầy đủ thông tin khách hàng và giao dịch.';
                break;
            case 4:
                errorMessage = 'Vui lòng chọn phương thức thanh toán.';
                break;
            default:
                errorMessage = 'Vui lòng hoàn thành thông tin bước hiện tại.';
        }
        
        // For step 2, show info toast instead of error
        const toastType = this.currentStep === 2 ? 'info' : 'error';
        this.showToast(toastType, errorMessage, 5000);
        
        // Add shake animation to current step (except step 2)
        if (this.currentStep !== 2) {
            $(`#step${this.currentStep}`).addClass('shake');
            setTimeout(() => {
                $(`#step${this.currentStep}`).removeClass('shake');
            }, 500);
        }
    }

    showToast(type, message, duration = 3000) {
        console.log(`Toast: ${type} - ${message}`);
        
        // Create toast container if not exists
        if (!$('#toastContainer').length) {
            $('body').append(`
                <div id="toastContainer" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                </div>
            `);
        }
        
        // Toast types and their styling
        const toastTypes = {
            'success': {
                bgClass: 'bg-success',
                icon: 'fas fa-check-circle',
                title: 'Thành công'
            },
            'error': {
                bgClass: 'bg-danger',
                icon: 'fas fa-exclamation-circle',
                title: 'Lỗi'
            },
            'warning': {
                bgClass: 'bg-warning',
                icon: 'fas fa-exclamation-triangle',
                title: 'Cảnh báo'
            },
            'info': {
                bgClass: 'bg-info',
                icon: 'fas fa-info-circle',
                title: 'Thông báo'
            }
        };
        
        const toastConfig = toastTypes[type] || toastTypes['info'];
        const toastId = 'toast_' + Date.now();
        
        // Create toast HTML
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white ${toastConfig.bgClass} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        <i class="${toastConfig.icon} me-2"></i>
                        <div>
                            <div class="fw-bold">${toastConfig.title}</div>
                            <div>${message}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        // Add toast to container
        $('#toastContainer').append(toastHtml);
        
        // Initialize and show toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            delay: duration
        });
        
        toast.show();
        
        // Remove from DOM after hide
        toastElement.addEventListener('hidden.bs.toast', () => {
            $(toastElement).remove();
        });
    }
    
    // Helper methods
    formatPrice(price) {
        if (!price) return 'Thỏa thuận';
        
        // Convert to number if it's string
        const numPrice = typeof price === 'string' ? parseFloat(price) : price;
        
        if (numPrice >= 1000000000) {
            return (numPrice / 1000000000).toFixed(1) + ' tỷ VNĐ';
        } else if (numPrice >= 1000000) {
            return (numPrice / 1000000).toFixed(0) + ' triệu VNĐ';
        } else {
            return numPrice.toLocaleString('vi-VN') + ' VNĐ';
        }
    }
    
    getTransactionTypeText(type) {
        const types = {
            'sell': 'Bán',
            'rent': 'Cho thuê',
            'buy': 'Mua',
            'lease': 'Thuê'
        };
        return types[type] || type || 'N/A';
    }
}

// Initialize when DOM is ready
$(document).ready(function() {
    window.transactionModal = new TransactionModal();
});