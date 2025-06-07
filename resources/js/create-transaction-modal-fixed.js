// Quản lý modal tạo giao dịch với 4 bước tiến độ
// Updated: Fixed JavaScript syntax and added property details functionality
class TransactionModal {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.selectedProperty = null;
        this.uploadedFiles = [];
        this.customers = [];
        
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
    }

    resetModal() {
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
    }

    // Bước 1: Chọn bất động sản
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
    }

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
        $('#propertyDetailsContent').html(html);
    }

    // Các method khác (selectProperty, searchProperties, etc.)
    selectProperty(propertyId) {
        console.log('Selecting property:', propertyId);
        
        // Find property card
        const propertyCard = $(`.property-card[data-property-id="${propertyId}"]`);
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
            $(`input[name="transaction_type"][value="${propertyType}"]`).prop('checked', true);
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
        $('#property_id').val('');
        $('#nextStepBtn').prop('disabled', true);
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
            const searchLower = searchTerm.toLowerCase();
            
            if (title.includes(searchLower) || address.includes(searchLower)) {
                card.show();
            } else {
                card.hide();
            }
        });
    }

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

    // Navigation methods
    nextStep() {
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.updateStepIndicator();
            this.showStep(this.currentStep);
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateStepIndicator();
            this.showStep(this.currentStep);
        }
    }

    updateStepIndicator() {
        $('.step-indicator .step').each((index, element) => {
            const stepNum = index + 1;
            const stepEl = $(element);
            
            stepEl.removeClass('active completed');
            
            if (stepNum < this.currentStep) {
                stepEl.addClass('completed');
            } else if (stepNum === this.currentStep) {
                stepEl.addClass('active');
            }
        });
        
        // Update buttons
        $('#prevStepBtn').toggle(this.currentStep > 1);
        
        if (this.currentStep === this.totalSteps) {
            $('#nextStepBtn').text('Hoàn thành');
        } else {
            $('#nextStepBtn').text('Tiếp tục');
        }
    }

    showStep(step) {
        $('.step-content').hide();
        $('#step-' + step).show();
    }

    // File handling methods
    handleFileSelection(files) {
        Array.from(files).forEach(file => {
            if (this.validateFile(file)) {
                this.uploadedFiles.push(file);
            }
        });
        this.updateFileList();
    }

    validateFile(file) {
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const maxSize = 10 * 1024 * 1024; // 10MB
        
        if (!allowedTypes.includes(file.type)) {
            alert('Chỉ cho phép file PDF, Word và hình ảnh');
            return false;
        }
        
        if (file.size > maxSize) {
            alert('File không được vượt quá 10MB');
            return false;
        }
        
        return true;
    }

    updateFileList() {
        const container = $('#uploadedFilesList');
        if (this.uploadedFiles.length === 0) {
            container.empty();
            return;
        }
        
        let html = '';
        this.uploadedFiles.forEach((file, index) => {
            html += '<div class="uploaded-file-item d-flex justify-content-between align-items-center p-2 border rounded mb-2">';
            html += '<div>';
            html += '<i class="fas fa-file me-2"></i>';
            html += '<span>' + file.name + '</span>';
            html += '<small class="text-muted ms-2">(' + this.formatFileSize(file.size) + ')</small>';
            html += '</div>';
            html += '<button type="button" class="btn btn-sm btn-outline-danger remove-file" data-index="' + index + '">';
            html += '<i class="fas fa-times"></i>';
            html += '</button>';
            html += '</div>';
        });
        
        container.html(html);
    }

    removeFile(index) {
        this.uploadedFiles.splice(index, 1);
        this.updateFileList();
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Other utility methods
    formatPrice(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('vi-VN');
        }
        input.value = value;
    }

    handleTransactionTypeChange(type) {
        console.log('Transaction type changed to:', type);
        // Handle any specific logic based on transaction type
    }

    // Submit transaction
    async submitTransaction() {
        console.log('Submitting transaction...');
        // Implementation for form submission
        try {
            // Collect form data and submit
            alert('Chức năng submit transaction sẽ được implement sau');
        } catch (error) {
            console.error('Error submitting transaction:', error);
            alert('Có lỗi xảy ra khi tạo giao dịch');
        }
    }

    // Load customers (if needed)
    async loadCustomers() {
        console.log('Loading customers...');
        // Implementation for loading customers
    }
}

// Initialize when DOM is ready
$(document).ready(function() {
    window.transactionModal = new TransactionModal();
});
