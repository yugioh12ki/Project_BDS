$(document).ready(function() {
    console.log('Document ready - Property listing script loaded');
    
    // Variables
    var currentStep = 1;
    var selectedPropertyId = null;
    var selectedPropertyType = null;
    
    // Load properties when modal opens
    $('#createPropertyListingModal').on('shown.bs.modal', function() {
        console.log('Modal shown event fired');
        resetModal();
        loadPropertiesFromAPI();
    });
    
    function resetModal() {
        // Reset state
        currentStep = 1;
        selectedPropertyId = null;
        selectedPropertyType = null;
        
        // Reset UI
        $('.property-selection-list, .listing-details-section, .package-selection-section').addClass('d-none');
        $('.property-selection-list').removeClass('d-none');
        $('#nextButton').text('Tiếp tục').prop('disabled', true);
        $('#backButton').addClass('d-none');
        updateStepUI(1);
    }
    
    function loadPropertiesFromAPI() {
        const propertySelectionList = $('.property-selection-list');
        
        if (!propertySelectionList.length) {
            console.error('Property selection container not found');
            return;
        }

        // Show loading
        propertySelectionList.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2 text-muted">Đang tải danh sách bất động sản...</p>
            </div>
        `);

        // Use the route helper if available, otherwise use direct URL
        const apiUrl = typeof route !== 'undefined' ? 
            route('property.get-for-listing') : 
            '/property/get-for-listing';

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                console.log('Loaded properties:', data);
                renderProperties(data);
            })
            .catch(error => {
                console.error('Error loading properties:', error);
                propertySelectionList.html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Lỗi khi tải dữ liệu bất động sản. Vui lòng thử lại sau.
                    </div>
                `);
            });
    }
    
    function renderProperties(properties) {
        const propertySelectionList = $('.property-selection-list');
        
        if (!properties || properties.length === 0) {
            propertySelectionList.html(`
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Bạn chưa có bất động sản nào. Hãy thêm bất động sản trước khi tạo tin đăng.
                </div>
            `);
            return;
        }

        let html = '';
        properties.forEach(property => {
            const imageUrl = property.imageUrl || property.ImageURL;
            const price = property.TypePro === 'Sale' || property.TypePro === 'Sold' ?
                `${(property.Price / 1000000000).toFixed(1)} tỷ VND` :
                `${(property.Price / 1000000).toFixed(0)} triệu VND`;

            const propertyType = property.danhMuc ? property.danhMuc.ten_pro : 'Bất động sản';
            const area = property.chiTiet ? (property.chiTiet.Area || 'N/A') : 'N/A';
            const bedroom = property.chiTiet ? (property.chiTiet.Bedroom || '0') : '0';
            const bathroom = property.chiTiet ? (property.chiTiet.Bath_WC || '0') : '0';

            const title = property.TypePro === 'Sale' || property.TypePro === 'Sold' ?
                `Bán ${propertyType} ${property.District}` :
                `Cho thuê ${propertyType} ${property.District}`;

            const status = property.TypePro === 'Sale' || property.TypePro === 'Sold' ? 'Đang bán' : 'Đang cho thuê';
            const statusClass = property.TypePro === 'Sale' || property.TypePro === 'Sold' ? 'bg-danger' : 'bg-primary';

            html += `
                <div class="property-item border rounded mb-3 overflow-hidden" data-property-id="${property.PropertyID}" data-property-type="${property.TypePro}">
                    <div class="property-content p-0">
                        <div class="property-image bg-light text-center" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                            ${imageUrl ?
                                `<img src="${imageUrl}" alt="${property.Title || title}" style="max-height: 100%; max-width: 100%; object-fit: contain;">` :
                                `<svg class="text-secondary" width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                    <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                                </svg>`
                            }
                        </div>
                        <div class="p-3">
                            <h5 class="mb-1 fw-bold">${property.Title || title}</h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-geo-alt me-1"></i>
                                ${[property.Address, property.Ward, property.District, property.Province].filter(Boolean).join(', ')}
                            </p>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge rounded-pill me-2" style="background-color: #eef2ff; color: #4f46e5;">
                                    ${propertyType}
                                </span>
                                <span class="badge rounded-pill ${statusClass}">
                                    ${status}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between text-center py-3 border-top border-bottom">
                                <div class="px-2">
                                    <i class="bi bi-rulers d-block mb-1"></i>
                                    <strong class="d-block">${area}</strong>
                                    <small class="text-muted">m²</small>
                                </div>
                                <div class="px-2">
                                    <i class="bi bi-door-open d-block mb-1"></i>
                                    <strong class="d-block">${bedroom}</strong>
                                    <small class="text-muted">Phòng ngủ</small>
                                </div>
                                <div class="px-2">
                                    <i class="bi bi-droplet d-block mb-1"></i>
                                    <strong class="d-block">${bathroom}</strong>
                                    <small class="text-muted">Phòng tắm</small>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <h5 class="fw-bold text-primary mb-0">
                                        ${price}${(property.TypePro === 'Rent' || property.TypePro === 'Rented') ? '<span class="small">/tháng</span>' : ''}
                                    </h5>
                                </div>
                                <div class="form-check">
                                    <input type="radio" 
                                           class="form-check-input" 
                                           name="selectedProperty" 
                                           id="property_${property.PropertyID}" 
                                           value="${property.PropertyID}" 
                                           data-type="${property.TypePro}" 
                                           required>
                                    <label class="form-check-label" for="property_${property.PropertyID}">
                                        Chọn bất động sản này
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        propertySelectionList.html(html);
        bindPropertyEvents();
    }
    
    // Simple property selection
    function bindPropertyEvents() {
        // Clear existing handlers
        $('.property-item').off('click');
        $('input[name="selectedProperty"]').off('change');
        
        // Handle property item clicks
        $('.property-item').on('click', function(e) {
            // Don't select if clicking on the radio button itself
            if ($(e.target).is('input[type="radio"]') || $(e.target).is('label')) {
                return;
            }
            
            const radio = $(this).find('input[type="radio"]');
            if (radio.length) {
                radio.prop('checked', true);
                radio.trigger('change');
            }
        });
        
        // Handle radio button changes
        $('input[name="selectedProperty"]').on('change', function() {
            selectedPropertyId = $(this).val();
            selectedPropertyType = $(this).data('type');
            
            // Update UI
            $('.property-item').removeClass('border-primary');
            $(this).closest('.property-item').addClass('border-primary');
            
            // Enable next button
            $('#nextButton').prop('disabled', false);
            
            console.log('Selected property:', selectedPropertyId, selectedPropertyType);
        });
    }
    
    // Simple next button handler
    $('#nextButton').on('click', function() {
        console.log('Next button clicked, current step:', currentStep);
        
        switch(currentStep) {
            case 1:
                if (!selectedPropertyId) {
                    alert('Vui lòng chọn một bất động sản để tiếp tục.');
                    return;
                }
                
                // Move to step 2
                $('.property-selection-list').addClass('d-none');
                $('.listing-details-section').removeClass('d-none');
                currentStep = 2;
                
                // Show back button
                $('#backButton').removeClass('d-none');
                
                // Update step UI
                updateStepUI(2);
                
                console.log('Moved to step 2');
                break;
                
            case 2:
                if (!validateStep2()) {
                    return;
                }
                
                // Move to step 3
                $('.listing-details-section').addClass('d-none');
                $('.package-selection-section').removeClass('d-none');
                currentStep = 3;
                
                // Change button text
                $('#nextButton').text('Hoàn tất');
                
                // Update step UI
                updateStepUI(3);
                
                console.log('Moved to step 3');
                break;
                
            case 3:
                if (!$('input[name="selectedPackage"]:checked').length) {
                    alert('Vui lòng chọn gói đăng tin');
                    return;
                }
                
                // Submit form
                submitForm();
                break;
        }
    });
    
    // Back button handler
    $('#backButton').on('click', function() {
        moveToStep(currentStep - 1);
    });
    
    function moveToStep(step) {
        // Hide all sections
        $('.property-selection-list, .listing-details-section, .package-selection-section').addClass('d-none');
        
        // Show appropriate section and update UI
        switch(step) {
            case 1:
                $('.property-selection-list').removeClass('d-none');
                $('#backButton').addClass('d-none');
                $('#nextButton').text('Tiếp tục').prop('disabled', !selectedPropertyId);
                break;
            case 2:
                $('.listing-details-section').removeClass('d-none');
                $('#backButton').removeClass('d-none');
                $('#nextButton').text('Tiếp tục').prop('disabled', false);
                break;
            case 3:
                $('.package-selection-section').removeClass('d-none');
                $('#backButton').removeClass('d-none');
                $('#nextButton').text('Hoàn tất').prop('disabled', false);
                break;
        }
        
        updateStepUI(step);
        currentStep = step;
    }
    
    function updateStepUI(step) {
        $('.step-circle').each(function(index) {
            const stepNumber = index + 1;
            const $circle = $(this);
            const $wrapper = $circle.find('.circle-wrapper');
            const $span = $wrapper.find('span');
            const $text = $circle.find('div:last-child');
            
            if (stepNumber <= step) {
                $circle.addClass('active');
                $wrapper.removeClass('border-secondary').addClass('border-primary');
                $span.removeClass('text-secondary').addClass('text-primary');
                $text.removeClass('text-secondary').addClass('text-primary');
            } else {
                $circle.removeClass('active');
                $wrapper.removeClass('border-primary').addClass('border-secondary');
                $span.removeClass('text-primary').addClass('text-secondary');
                $text.removeClass('text-primary').addClass('text-secondary');
            }
        });
    }
    
    function validateStep2() {
        const title = $('input[name="title"]').val().trim();
        const description = $('textarea[name="description"]').val().trim();
        const images = $('#imageInput')[0].files;
        
        if (!title) {
            alert('Vui lòng nhập tiêu đề tin đăng');
            $('input[name="title"]').focus();
            return false;
        }
        
        if (!description) {
            alert('Vui lòng nhập mô tả chi tiết');
            $('textarea[name="description"]').focus();
            return false;
        }
        
        if (!images || images.length < 3) {
            alert('Vui lòng tải lên ít nhất 3 hình ảnh');
            return false;
        }
        
        return true;
    }
    
    function submitForm() {
        const form = $('#propertyListingForm');
        
        // Add selected property data to form
        if (selectedPropertyId) {
            // Remove existing hidden inputs
            form.find('input[name="property_id"]').remove();
            form.find('input[name="property_type"]').remove();
            
            // Add new hidden inputs
            form.append(`<input type="hidden" name="property_id" value="${selectedPropertyId}">`);
            if (selectedPropertyType) {
                form.append(`<input type="hidden" name="property_type" value="${selectedPropertyType}">`);
            }
        }
        
        // Submit the form
        form.submit();
    }
    
    // Handle image upload
    $('#addImagesBtn').on('click', function() {
        $('#imageInput').click();
    });
    
    $('#imageInput').on('change', function() {
        const files = this.files;
        if (files.length > 0) {
            $('#addImagesBtn').html(`<i class="bi bi-check-lg me-2"></i>Đã chọn ${files.length} hình ảnh`);
            $('#addImagesBtn').removeClass('btn-outline-primary').addClass('btn-success');
        }
    });
    
    // Handle package selection
    $('.package-card').on('click', function() {
        $(this).find('input[type="radio"]').prop('checked', true);
        $('.package-card').removeClass('border-primary').addClass('border-2');
        $(this).removeClass('border-2').addClass('border-primary');
    });
    
    // Reset modal when closed
    $('#createPropertyListingModal').on('hidden.bs.modal', function() {
        resetModal();
    });
});