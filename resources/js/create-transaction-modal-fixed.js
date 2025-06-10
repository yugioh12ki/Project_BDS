// 4-Step Transaction Modal - Complete Implementation
class TransactionModal {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.selectedProperty = null;
        this.selectedDocuments = [];
        this.selectedTemplates = [];
        this.transactionType = null;
        this.saleContractType = null;
        this.paymentMethod = null;
        this.isUploading = false;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadInitialData();
        this.initializeStep3();
        this.bindModalEvents();
    }

    bindModalEvents() {
        // Handle modal show event to reset and initialize the modal
        $('#createTransactionModal').on('shown.bs.modal', () => {
            console.log('Transaction modal opened, initializing...');
            this.resetModal();
            this.loadInitialData();
            this.initializeStep3(); // This will set today's date

            // Multiple attempts to ensure transaction date is set
            setTimeout(() => {
                this.setTransactionDate();
            }, 100);

            setTimeout(() => {
                this.setTransactionDate();
            }, 500);
        });

        // Handle modal hide event for cleanup
        $('#createTransactionModal').on('hidden.bs.modal', () => {
            console.log('Transaction modal closed, cleaning up...');
            this.resetModal();
        });
    }

    bindEvents() {
        this.unbindEvents();
        console.log('Binding events for TransactionModal');

        const modalSelector = '#createTransactionModal';

        // Navigation buttons
        $(document).off('click.transactionModal', `${modalSelector} #nextBtn`).on('click.transactionModal', `${modalSelector} #nextBtn`, () => this.nextStep());
        $(document).off('click.transactionModal', `${modalSelector} #prevBtn`).on('click.transactionModal', `${modalSelector} #prevBtn`, () => this.prevStep());
        $(document).off('click.transactionModal', `${modalSelector} #submitBtn`).on('click.transactionModal', `${modalSelector} #submitBtn`, () => this.submitTransaction());

        // Step click navigation
        $(document).off('click.transactionModal', `${modalSelector} .step-item`).on('click.transactionModal', `${modalSelector} .step-item`, (e) => {
            const step = parseInt($(e.currentTarget).data('step'));
            if (step <= this.getMaxAvailableStep()) {
                this.goToStep(step);
            }
        });

        // Property selection
        $(document).off('click.transactionModal', `${modalSelector} .property-card`).on('click.transactionModal', `${modalSelector} .property-card`, (e) => {
            const $clickedCard = $(e.currentTarget);

            // Toggle selection
            if ($clickedCard.hasClass('selected')) {
                // Deselect
                $clickedCard.removeClass('selected');
                this.selectedProperty = null;
                this.resetTransactionType();
            } else {
                // Select new property
                $(`${modalSelector} .property-card`).removeClass('selected');
                $clickedCard.addClass('selected');
                this.selectedProperty = $clickedCard.data('property-id');

                // Auto-set transaction type based on property type
                const transactionType = $clickedCard.data('transaction-type');
                if (transactionType) {
                    this.transactionType = transactionType;
                    this.autoSetTransactionType(transactionType);
                }
            }

            this.updateNavigationButtons();
        });

        // Document upload
        $(document).off('click.transactionModal', `${modalSelector} #createModalUploadZone`).on('click.transactionModal', `${modalSelector} #createModalUploadZone`, () => {
            $('#createModalDocumentUpload').click();
        });

        $(document).off('change.transactionModal', `${modalSelector} #createModalDocumentUpload`).on('change.transactionModal', `${modalSelector} #createModalDocumentUpload`, (e) => {
            this.handleDocumentUpload(e.target.files);
        });

        // Template selection
        $(document).off('click.transactionModal', `${modalSelector} .template-item`).on('click.transactionModal', `${modalSelector} .template-item`, (e) => {
            const $template = $(e.currentTarget);
            if ($template.hasClass('selected')) {
                $template.removeClass('selected');
                this.selectedTemplates = this.selectedTemplates.filter(id => id !== $template.data('template-id'));
            } else {
                $template.addClass('selected');
                this.selectedTemplates.push($template.data('template-id'));
            }
            this.updateNavigationButtons();
        });

        // Payment method selection
        $(document).off('click.transactionModal', `${modalSelector} .payment-method`).on('click.transactionModal', `${modalSelector} .payment-method`, (e) => {
            $(`${modalSelector} .payment-method`).removeClass('selected');
            $(e.currentTarget).addClass('selected');
            this.paymentMethod = $(e.currentTarget).data('payment');
            this.updateNavigationButtons();
        });
    }

    unbindEvents() {
        $(document).off('.transactionModal');
    }

    initializeStep3() {
        console.log('Initializing Step 3 functionality');
        this.initCustomerSearch();
        this.initTransactionTypeHandler();
        this.initPaymentCalculation();
        this.setTransactionDate();
    }

    setTransactionDate() {
        // Set transaction date to today in yyyy-mm-dd format (ISO format for HTML date input)
        const today = new Date().toISOString().split('T')[0];
        console.log('Setting transaction date to:', today);

        const $transactionDateInput = $('#transactionDate');

        // Check if element exists
        if ($transactionDateInput.length === 0) {
            console.warn('Transaction date input not found!');
            return;
        }

        // Set the value
        $transactionDateInput.val(today);

        // Verify the value was set
        const setValue = $transactionDateInput.val();
        console.log('Transaction date value after setting:', setValue);

        // Force trigger change event to ensure value is set
        $transactionDateInput.trigger('change');
    }

    initCustomerSearch() {
        const customerSearchInput = $('#customerSearch');
        const searchResults = $('#customerSearchResults');
        let searchTimeout;

        console.log('Initializing customer search...');

        customerSearchInput.on('input', (e) => {
            const query = e.target.value.trim();
            console.log('Search query:', query);

            clearTimeout(searchTimeout);

            if (query.length < 2) {
                searchResults.addClass('d-none').empty();
                return;
            }

            searchTimeout = setTimeout(() => {
                this.searchCustomers(query);
            }, 300);
        });

        customerSearchInput.on('focus', (e) => {
            const query = e.target.value.trim();
            if (query.length >= 2) {
                searchResults.removeClass('d-none');
            }
        });

        $(document).on('click', (e) => {
            if (!$(e.target).closest('.customer-search-container').length) {
                searchResults.addClass('d-none');
            }
        });
    }

    async searchCustomers(query) {
        const searchResults = $('#customerSearchResults');
        console.log('Searching for customers with query:', query);

        try {
            searchResults.removeClass('d-none').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Đang tìm kiếm...</div>');

            const response = await fetch(`/agent/search-customers?query=${encodeURIComponent(query)}`);
            console.log('Response status:', response.status);

            const data = await response.json();
            console.log('Response data:', data);

            if (data.success) {
                this.displaySearchResults(data.customers);
            } else {
                searchResults.html('<div class="text-center p-3 text-muted">Có lỗi xảy ra khi tìm kiếm</div>');
            }
        } catch (error) {
            console.error('Error searching customers:', error);
            searchResults.html('<div class="text-center p-3 text-muted">Có lỗi xảy ra khi tìm kiếm</div>');
        }
    }

    displaySearchResults(customers) {
        const searchResults = $('#customerSearchResults');
        console.log('Displaying search results:', customers);

        if (customers.length === 0) {
            searchResults.removeClass('d-none').html('<div class="text-center p-3 text-muted">Không tìm thấy khách hàng nào</div>');
            return;
        }

        const resultsHtml = customers.map(customer => `
            <div class="customer-search-item" data-customer-id="${customer.id}">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-user-circle fa-2x text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">${customer.name}</div>
                        <div class="text-muted small">
                            <span>${customer.phone || 'Chưa có SĐT'}</span>
                            ${customer.identity_card ? ` • ${customer.identity_card}` : ''}
                        </div>
                        <div class="text-muted small">${customer.address}</div>
                    </div>
                </div>
            </div>
        `).join('');

        searchResults.removeClass('d-none').html(resultsHtml);

        searchResults.find('.customer-search-item').on('click', (e) => {
            const customerId = $(e.currentTarget).data('customer-id');
            const customer = customers.find(c => c.id === customerId);
            this.selectCustomer(customer);
        });
    }

    selectCustomer(customer) {
        console.log('Selecting customer:', customer);

        $('#customerSearchResults').addClass('d-none');
        $('#customerSearch').val('');

        const selectedCustomerInfo = $('#selectedCustomerInfo');

        $('#selectedCustomerName').text(customer.name);
        $('#selectedCustomerPhone').text(customer.phone || 'Chưa có');
        $('#selectedCustomerIdCard').text(customer.identity_card || 'Chưa có');
        $('#selectedCustomerAddress').text(customer.address);

        selectedCustomerInfo.removeClass('d-none');
        $('#selectedCustomerId').val(customer.id);

        this.updateNavigationButtons();
    }

    clearSelectedCustomer() {
        $('#selectedCustomerInfo').addClass('d-none');
        $('#selectedCustomerId').val('');
        $('#customerSearch').val('');
        this.updateNavigationButtons();
    }

    initTransactionTypeHandler() {
        console.log('Initializing transaction type handler');

        $(document).off('click.transactionType', '.transaction-type-card').on('click.transactionType', '.transaction-type-card', (e) => {
            const $card = $(e.currentTarget);
            const transactionType = $card.data('type');
            const saleType = $card.data('sale-type');

            console.log('Transaction type selected:', transactionType, saleType);

            if (transactionType) {
                $('.transaction-type-card[data-type]').removeClass('selected');
                $card.addClass('selected');

                this.transactionType = transactionType;
                this.handleTransactionTypeChange(transactionType);
            } else if (saleType) {
                $('.transaction-type-card[data-sale-type]').removeClass('selected');
                $card.addClass('selected');

                this.saleContractType = saleType;
                this.handleSaleContractTypeChange(saleType);
            }
        });

        // Add event handler for change customer button
        $('#changeCustomerBtn').on('click', () => {
            this.clearSelectedCustomer();
        });
    }

    handleTransactionTypeChange(type) {
        console.log('Handling transaction type change:', type);

        const rentFields = $('.rent-transaction-fields');
        const saleFields = $('.sale-transaction-fields');
        const saleContractTypeSection = $('#saleContractType');

        if (type === 'rent') {
            rentFields.removeClass('d-none');
            saleFields.addClass('d-none');
            saleContractTypeSection.addClass('d-none');

            $('#rentMonths').prop('required', true);
            $('#salePrice').prop('required', false);
        } else if (type === 'sale') {
            rentFields.addClass('d-none');
            saleFields.removeClass('d-none');
            saleContractTypeSection.removeClass('d-none');

            $('#rentMonths').prop('required', false);
            $('#salePrice').prop('required', true);
        }

        // Set transaction date when transaction type changes
        this.setTransactionDate();

        this.updateNavigationButtons();
    }

    handleSaleContractTypeChange(saleType) {
        console.log('Handling sale contract type change:', saleType);

        const depositFields = $('.deposit-contract-fields');

        if (saleType === 'deposit') {
            depositFields.removeClass('d-none');
            $('#paymentInstallments').prop('required', true);
            $('#depositAmount').prop('required', true);
        } else {
            depositFields.addClass('d-none');
            $('#paymentInstallments').prop('required', false);
            $('#depositAmount').prop('required', false);
        }

        this.updateNavigationButtons();
    }

    initPaymentCalculation() {
        console.log('Initializing payment calculation');

        $('#rentMonths, #salePrice, #paymentInstallments, #depositAmount').on('input change', () => {
            this.calculatePayments();
        });
    }

    calculatePayments() {
        if (!this.transactionType) return;

        if (this.transactionType === 'rent') {
            this.calculateRentPayments();
        } else if (this.transactionType === 'sale') {
            this.calculateSalePayments();
        }
    }

    calculateRentPayments() {
        const months = parseInt($('#rentMonths').val());
        const monthlyRent = this.selectedProperty ? parseFloat($('#propertiesContainer .property-card.selected').data('price')) : 0;

        if (months && monthlyRent) {
            const totalRent = monthlyRent * months;
            console.log('Calculated rent payment:', totalRent);
        }
    }

    calculateSalePayments() {
        const salePrice = parseFloat($('#salePrice').val());
        const installments = parseInt($('#paymentInstallments').val());
        const depositAmount = parseFloat($('#depositAmount').val());

        if (salePrice && this.saleContractType === 'deposit' && installments && depositAmount) {
            const remainingAmount = salePrice - depositAmount;
            $('#remainingAmount').val(remainingAmount);

            this.generatePaymentSchedule(salePrice, installments, depositAmount);
        } else {
            $('#remainingAmount').val('');
            $('#paymentSchedule').addClass('d-none');
        }
    }

    generatePaymentSchedule(salePrice, installments, depositAmount) {
        const remainingAmount = salePrice - depositAmount;
        const installmentAmount = remainingAmount / (installments - 1);

        const paymentSchedule = $('#paymentSchedule');
        const scheduleList = paymentSchedule.find('.payment-schedule-list');

        let scheduleHtml = '';
        const startDate = new Date();

        scheduleHtml += `
            <div class="payment-schedule-item deposit">
                <div class="payment-info">
                    <h6>Đợt 1: Tiền đặt cọc</h6>
                    <div class="payment-date">Ngày: ${startDate.toLocaleDateString('vi-VN')}</div>
                </div>
                <div class="payment-amount">${this.formatCurrency(depositAmount)}</div>
            </div>
        `;

        for (let i = 2; i <= installments; i++) {
            const paymentDate = new Date(startDate);
            paymentDate.setMonth(paymentDate.getMonth() + (i - 1));

            scheduleHtml += `
                <div class="payment-schedule-item">
                    <div class="payment-info">
                        <h6>Đợt ${i}: Thanh toán</h6>
                        <div class="payment-date">Ngày: ${paymentDate.toLocaleDateString('vi-VN')}</div>
                    </div>
                    <div class="payment-amount">${this.formatCurrency(installmentAmount)}</div>
                </div>
            `;
        }

        scheduleList.html(scheduleHtml);
        paymentSchedule.removeClass('d-none');
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    // Rest of the methods (loadInitialData, nextStep, prevStep, etc.)
    loadInitialData() {
        this.loadProperties();
        this.loadContractTemplates();
    }

    async loadProperties() {
        try {
            const response = await fetch('/agent/properties', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.displayProperties(data.properties);
            } else {
                console.error('API returned error:', data.message);
                $('#propertiesContainer').html('<div class="text-center p-4"><p class="text-danger">Lỗi: ' + data.message + '</p></div>');
            }
        } catch (error) {
            console.error('Error loading properties:', error);
            $('#propertiesContainer').html('<div class="text-center p-4"><p class="text-danger">Có lỗi xảy ra khi tải danh sách bất động sản</p></div>');
        }
    }

    displayProperties(properties) {
        const container = $('#propertiesContainer');

        if (properties.length === 0) {
            container.html('<div class="text-center p-4"><p class="text-muted">Không có bất động sản nào được phân công.</p></div>');
            return;
        }

        const propertiesHtml = properties.map(property => `
            <div class="property-card" data-property-id="${property.id}" data-price="${property.price}" data-transaction-type="${property.transaction_type}">
                <div class="property-price text-success fw-bold mb-2">${property.formatted_price}</div>
                <div class="property-title fw-bold mb-2">${property.title}</div>
                <div class="property-address text-muted mb-2">
                    <i class="fas fa-map-marker-alt me-1"></i>${property.address || 'Địa chỉ không có'}
                </div>
                <div class="property-details">
                    <span class="badge ${property.transaction_type === 'rent' ? 'bg-info' : 'bg-warning'} me-1">
                        ${property.transaction_type === 'rent' ? 'Cho thuê' : 'Bán'}
                    </span>
                    <span class="badge bg-secondary">${property.property_type || 'N/A'}</span>
                </div>
                <div class="property-owner text-muted mt-2">
                    <small><i class="fas fa-user me-1"></i>Chủ: ${property.owner_name || 'Chưa xác định'}</small>
                </div>
                <div class="property-description text-muted mt-2">
                    <small>${property.description ? property.description.substring(0, 100) + '...' : 'Không có mô tả'}</small>
                </div>
            </div>
        `).join('');

        container.html(propertiesHtml);
    }

    async loadContractTemplates() {
        try {
            const response = await fetch('/agent/contract-templates', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                this.displayContractTemplates(data.templates);
            } else {
                console.error('API returned error:', data.message);
                $('#contractTemplates').html('<div class="text-center p-4"><p class="text-danger">Lỗi: ' + data.message + '</p></div>');
            }
        } catch (error) {
            console.error('Error loading templates:', error);
            $('#contractTemplates').html('<div class="text-center p-4"><p class="text-danger">Có lỗi xảy ra khi tải mẫu hợp đồng</p></div>');
        }
    }

    displayContractTemplates(templates) {
        const container = $('#contractTemplates');

        if (templates.length === 0) {
            container.html('<div class="text-center p-4"><p class="text-muted">Không có mẫu hợp đồng nào.</p></div>');
            return;
        }

        const templatesHtml = templates.map(template => `
            <div class="template-item" data-template-id="${template.id}">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="template-icon me-3">
                            <i class="fas fa-file-contract fa-2x text-primary"></i>
                        </div>
                        <div class="template-info">
                            <h6 class="mb-1">${template.display_name}</h6>
                            <p class="text-muted small mb-0">${template.description || 'Mẫu hợp đồng chuẩn'}</p>
                            <small class="text-muted">${template.file_type.toUpperCase()} • ${template.size}</small>
                        </div>
                    </div>
                    <div class="template-actions">
                        <button class="btn btn-outline-primary btn-sm" onclick="downloadTemplate('${template.name}', '${template.display_name}')" title="Tải xuống mẫu">
                            <i class="fas fa-download"></i> Tải xuống
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        container.html(templatesHtml);
    }

    nextStep() {
        if (this.currentStep < this.totalSteps && this.canProceedToNextStep()) {
            this.goToStep(this.currentStep + 1);
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.goToStep(this.currentStep - 1);
        }
    }

    goToStep(step) {
        if (step < 1 || step > this.totalSteps) return;

        $('.step-content').removeClass('active');
        $(`#step${step}`).addClass('active');

        $('.step-item').removeClass('active completed');

        for (let i = 1; i <= this.totalSteps; i++) {
            const $stepItem = $(`.step-item[data-step="${i}"]`);
            if (i < step) {
                $stepItem.addClass('completed');
                $stepItem.find('.step-number').addClass('d-none');
                $stepItem.find('.fa-check').removeClass('d-none');
            } else if (i === step) {
                $stepItem.addClass('active');
                $stepItem.find('.step-number').removeClass('d-none');
                $stepItem.find('.fa-check').addClass('d-none');
            } else {
                $stepItem.find('.step-number').removeClass('d-none');
                $stepItem.find('.fa-check').addClass('d-none');
            }
        }

        const progressWidth = ((step - 1) / (this.totalSteps - 1)) * 100;
        $('#progressLine').css('width', progressWidth + '%');

        this.currentStep = step;
        this.updateNavigationButtons();
        $('#stepInfo').text(`Bước ${step} / ${this.totalSteps}`);
    }

    canProceedToNextStep() {
        switch (this.currentStep) {
            case 1:
                return this.selectedProperty !== null;
            case 2:
                return this.selectedDocuments.length > 0 || this.selectedTemplates.length > 0;
            case 3:
                return this.validateTransactionDetailsQuiet();
            case 4:
                return this.paymentMethod !== null;
            default:
                return false;
        }
    }

    validateTransactionDetailsQuiet() {
        const customerId = $('#selectedCustomerId').val();
        if (!customerId) {
            return false;
        }

        if (!this.transactionType) {
            return false;
        }

        if (this.transactionType === 'rent') {
            const rentMonths = $('#rentMonths').val();
            if (!rentMonths || rentMonths <= 0) {
                return false;
            }
        } else if (this.transactionType === 'sale') {
            const salePrice = $('#salePrice').val();
            if (!salePrice || salePrice <= 0) {
                return false;
            }

            if (this.saleContractType === 'deposit') {
                const installments = $('#paymentInstallments').val();
                const depositAmount = $('#depositAmount').val();

                if (!installments || installments < 2) {
                    return false;
                }

                if (!depositAmount || depositAmount <= 0) {
                    return false;
                }

                if (parseFloat(depositAmount) >= parseFloat(salePrice)) {
                    return false;
                }
            }
        }

        return true;
    }

    validateTransactionDetails() {
        const customerId = $('#selectedCustomerId').val();
        if (!customerId) {
            alert('Vui lòng chọn khách hàng');
            return false;
        }

        if (!this.transactionType) {
            alert('Vui lòng chọn loại giao dịch');
            return false;
        }

        if (this.transactionType === 'rent') {
            const rentMonths = $('#rentMonths').val();
            if (!rentMonths || rentMonths <= 0) {
                alert('Vui lòng nhập số tháng thuê hợp lệ');
                return false;
            }
        } else if (this.transactionType === 'sale') {
            const salePrice = $('#salePrice').val();
            if (!salePrice || salePrice <= 0) {
                alert('Vui lòng nhập giá bán hợp lệ');
                return false;
            }

            if (this.saleContractType === 'deposit') {
                const installments = $('#paymentInstallments').val();
                const depositAmount = $('#depositAmount').val();

                if (!installments || installments < 2) {
                    alert('Vui lòng chọn số đợt thanh toán hợp lệ');
                    return false;
                }

                if (!depositAmount || depositAmount <= 0) {
                    alert('Vui lòng nhập tiền đặt cọc hợp lệ');
                    return false;
                }

                if (parseFloat(depositAmount) >= parseFloat(salePrice)) {
                    alert('Tiền đặt cọc phải nhỏ hơn giá bán');
                    return false;
                }
            }
        }

        return true;
    }

    getMaxAvailableStep() {
        for (let step = 1; step <= this.totalSteps; step++) {
            if (step === 1) continue;

            this.currentStep = step - 1;
            if (!this.canProceedToNextStep()) {
                return step - 1;
            }
        }
        return this.totalSteps;
    }

    updateNavigationButtons() {
        const $prevBtn = $('#prevBtn');
        const $nextBtn = $('#nextBtn');
        const $submitBtn = $('#submitBtn');

        if (this.currentStep === 1) {
            $prevBtn.hide();
        } else {
            $prevBtn.show();
        }

        if (this.currentStep === this.totalSteps) {
            $nextBtn.hide();
            $submitBtn.removeClass('d-none');
        } else {
            $nextBtn.show();
            $submitBtn.addClass('d-none');
        }

        const canProceed = this.canProceedToNextStep();
        $nextBtn.prop('disabled', !canProceed);
        $submitBtn.prop('disabled', !canProceed);
    }

    async handleDocumentUpload(files) {
        if (this.isUploading) return;

        const validFiles = Array.from(files).filter(file => {
            const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            const maxSize = 10 * 1024 * 1024; // 10MB

            if (!validTypes.includes(file.type)) {
                alert(`File ${file.name} không được hỗ trợ. Chỉ cho phép PDF, DOC, DOCX.`);
                return false;
            }

            if (file.size > maxSize) {
                alert(`File ${file.name} quá lớn. Tối đa 10MB.`);
                return false;
            }

            return true;
        });

        if (validFiles.length === 0) return;

        this.isUploading = true;

        for (const file of validFiles) {
            await this.uploadSingleDocument(file);
        }

        this.isUploading = false;
        this.updateNavigationButtons();
    }

    async uploadSingleDocument(file) {
        const formData = new FormData();
        formData.append('document', file);
        formData.append('property_id', this.selectedProperty);

        try {
            const response = await fetch('/agent/upload-transaction-document', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                this.selectedDocuments.push({
                    id: data.document.id,
                    name: file.name,
                    path: data.document.path,
                    size: file.size
                });
                this.displayUploadedFiles();
            } else {
                alert('Lỗi upload: ' + data.message);
            }
        } catch (error) {
            console.error('Upload error:', error);
            alert('Có lỗi xảy ra khi upload file');
        }
    }

    displayUploadedFiles() {
        const container = $('#uploadedFiles');

        if (this.selectedDocuments.length === 0) {
            container.empty();
            return;
        }

        const filesHtml = this.selectedDocuments.map((doc, index) => `
            <div class="uploaded-file-item" data-index="${index}">
                <div class="d-flex align-items-center">
                    <div class="file-icon me-3">
                        <i class="fas fa-file-pdf text-danger"></i>
                    </div>
                    <div class="file-info flex-grow-1">
                        <div class="file-name">${doc.name}</div>
                        <div class="file-size text-muted small">${this.formatFileSize(doc.size)}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="transactionModal.removeUploadedFile(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `).join('');

        container.html(filesHtml);
    }

    removeUploadedFile(index) {
        this.selectedDocuments.splice(index, 1);
        this.displayUploadedFiles();
        this.updateNavigationButtons();
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    async submitTransaction() {
        if (!this.validateTransactionDetails()) return;

        try {
            const transactionData = this.collectTransactionData();

            const response = await fetch('/agent/transactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify(transactionData)
            });

            const data = await response.json();

            if (data.success) {
                alert('Tạo giao dịch thành công!');
                $('#createTransactionModal').modal('hide');
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        } catch (error) {
            console.error('Error submitting transaction:', error);
            alert('Có lỗi xảy ra khi tạo giao dịch');
        }
    }

    collectTransactionData() {
        const customerId = $('#selectedCustomerId').val();
        const transactionDate = $('#transactionDate').val();

        const data = {
            property_id: this.selectedProperty,
            customer_id: customerId,
            transaction_type: this.transactionType,
            transaction_date: transactionDate,
            payment_method: this.paymentMethod,
            documents: this.selectedDocuments,
            templates: this.selectedTemplates,
            payment_note: $('#paymentNote').val()
        };

        if (this.transactionType === 'rent') {
            data.rent_months = $('#rentMonths').val();
        } else if (this.transactionType === 'sale') {
            data.sale_price = $('#salePrice').val();
            data.sale_contract_type = this.saleContractType;

            if (this.saleContractType === 'deposit') {
                data.payment_installments = $('#paymentInstallments').val();
                data.deposit_amount = $('#depositAmount').val();
            }
        }

        return data;
    }

    resetModal() {
        this.currentStep = 1;
        this.selectedProperty = null;
        this.selectedDocuments = [];
        this.selectedTemplates = [];
        this.transactionType = null;
        this.saleContractType = null;
        this.paymentMethod = null;

        this.goToStep(1);
        $('.property-card, .template-item, .transaction-type-card, .payment-method').removeClass('selected');
        $('#uploadedFiles').empty();
        $('#selectedCustomerInfo').addClass('d-none');
        $('#selectedCustomerId').val('');
        $('#customerSearch').val('');
        $('.rent-transaction-fields, .sale-transaction-fields, #saleContractType, .deposit-contract-fields, #paymentSchedule').addClass('d-none');
    }

    autoSetTransactionType(transactionType) {
        console.log('Auto-setting transaction type:', transactionType);

        // Auto-select the corresponding transaction type card
        $(`.transaction-type-card[data-type="${transactionType}"]`).click();

        // Hide the transaction type selection section since it's already determined
        $('.transaction-type-selection').addClass('d-none');

        // Show a read-only info about the transaction type
        const transactionTypeInfo = $('#selectedTransactionTypeInfo');
        if (transactionTypeInfo.length === 0) {
            // Create the info element if it doesn't exist
            $('.transaction-type-selection').after(`
                <div id="selectedTransactionTypeInfo" class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Loại giao dịch: <strong>${transactionType === 'rent' ? 'Cho thuê' : 'Bán'}</strong>
                    <small class="text-muted d-block">Được xác định tự động dựa trên bất động sản đã chọn</small>
                </div>
            `);
        } else {
            transactionTypeInfo.removeClass('d-none').html(`
                <i class="fas fa-info-circle me-2"></i>
                Loại giao dịch: <strong>${transactionType === 'rent' ? 'Cho thuê' : 'Bán'}</strong>
                <small class="text-muted d-block">Được xác định tự động dựa trên bất động sản đã chọn</small>
            `);
        }
    }

    resetTransactionType() {
        console.log('Resetting transaction type');

        // Show the transaction type selection section
        $('.transaction-type-selection').removeClass('d-none');

        // Hide the auto-selected info
        $('#selectedTransactionTypeInfo').addClass('d-none');

        // Clear selections
        $('.transaction-type-card').removeClass('selected');
        this.transactionType = null;
        this.saleContractType = null;

        // Hide all conditional fields
        $('.rent-transaction-fields, .sale-transaction-fields, #saleContractType, .deposit-contract-fields').addClass('d-none');
    }
}

// Initialize when document is ready
$(document).ready(function() {
    if (typeof window.transactionModal === 'undefined') {
        window.transactionModal = new TransactionModal();
    }
});

// Toast notification function (global)
window.showToast = function(message, type = 'info') {
    const toastClass = type === 'success' ? 'bg-success' :
                     type === 'warning' ? 'bg-warning' :
                     type === 'error' ? 'bg-danger' : 'bg-primary';

    const toastHtml = `
        <div class="toast align-items-center text-white ${toastClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

    // Add to toast container (tạo nếu chưa có)
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '1055';
        document.body.appendChild(toastContainer);
    }

    const toastElement = $(toastHtml);
    $(toastContainer).append(toastElement);

    // Initialize and show toast
    const toast = new bootstrap.Toast(toastElement[0]);
    toast.show();

    // Auto remove after hide
    toastElement[0].addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Global function to download template
// Global function for downloading template (accessible from inline onclick)
window.downloadTemplate = function(templateName, displayName) {
    try {
        console.log('Downloading template:', templateName);

        // Create download URL
        const downloadUrl = `/agent/contract-templates/${encodeURIComponent(templateName)}/download`;

        // Trigger download
        window.location.href = downloadUrl;

        // Show success message
        if (typeof showToast === 'function') {
            showToast(`Đang tải xuống: ${displayName}`, 'success');
        }

    } catch (error) {
        console.error('Error downloading template:', error);
        if (typeof showToast === 'function') {
            showToast('Có lỗi xảy ra khi tải xuống template', 'error');
        }
    }
};

// Initialize when document is ready
