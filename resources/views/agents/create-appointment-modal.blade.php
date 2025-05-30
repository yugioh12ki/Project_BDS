<!-- Create Appointment Modal -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo lịch hẹn mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="appointmentForm" method="POST" action="{{ route('agent.appointments.create') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Chủ sở hữu <span class="text-danger">*</span></label>
                        <x-owner-autocomplete 
                            id="ownerAutocomplete"
                            name="OwnerSearchName"
                            placeholder="Nhập tên chủ sở hữu..."
                            api-url="{{ route('agent.search.owners') }}"
                        />
                    </div>

                    <div class="mb-3" id="propertySelectionSection" style="display: none;">
                        <label class="form-label">Bất động sản <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <select class="form-select" id="propertyCombobox" name="PropertyID" required>
                                <option value="">-- Vui lòng chọn chủ sở hữu trước --</option>
                            </select>
                            <div id="propertyLoadingSpinner" class="position-absolute end-0 top-50 translate-middle-y me-3" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-text d-flex justify-content-between align-items-center">
                            <span>Chọn bất động sản của chủ sở hữu để tạo lịch hẹn</span>
                            <span id="propertySelectFeedback"></span>
                        </div>
                    </div>
                    
                    <div id="propertyDetailSection" class="mb-3 p-3 border rounded bg-light" style="display:none">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-building me-2 fs-5"></i>
                            <h6 class="mb-0 fw-bold" id="propertyDetailTitle">Tiêu đề bất động sản</h6>
                        </div>
                        <div class="text-muted mb-2">
                            <i class="bi bi-geo-alt me-1"></i> 
                            <span id="propertyDetailAddress">Địa chỉ bất động sản</span>
                        </div>
                        <div id="propertyExtraInfo" class="mt-2">
                            <!-- Các badge thông tin sẽ được thêm vào đây bằng JavaScript -->
                        </div>                    </div>
                    
                    <!-- Hidden inputs for form submission -->
                    <input type="hidden" id="selectedOwnerId" name="OwnerID" value="">
                    <input type="hidden" id="ownerSearchId" name="OwnerSearchId" value="">
                    
                    <div class="mb-3">
                        <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
                        <x-customer-autocomplete 
                            id="customerSearch"
                            name="CusID"
                            placeholder="Nhập tên khách hàng..."
                            api-url="{{ route('agent.search.customers') }}"
                        />
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="appointmentDateStart" name="AppointmentDateStart" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="appointmentDateEnd" name="AppointmentDateEnd" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="appointmentTitle" name="TitleAppoint" placeholder="Nhập tiêu đề lịch hẹn..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="DescAppoint" rows="3" placeholder="Nhập nội dung cuộc hẹn..." required></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div>
                                <strong>Lưu ý:</strong> Thời gian hẹn sẽ được tự động thông báo cho chủ sở hữu và khách hàng.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="submitAppointmentBtn" class="btn btn-primary">Tạo lịch hẹn</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {    // Lắng nghe event owner:selected từ owner-autocomplete component
    $(document).on('owner:selected', function(event, ownerData) {
        console.log('Owner selected:', ownerData);
        
        // Handle both old format [ownerId, ownerName] and new format {id, name}
        let ownerId, ownerName;
        if (typeof ownerData === 'object' && ownerData.id) {
            // New format: {id, name, fullName, text}
            ownerId = ownerData.id;
            ownerName = ownerData.name || ownerData.fullName || ownerData.text;
        } else {
            // Old format: first parameter is ownerId, but we'll handle it safely
            ownerId = ownerData;
            ownerName = arguments[2] || 'Unknown'; // fallback
        }
        
        // Validate ownerId is not undefined or empty
        if (!ownerId || ownerId === 'undefined' || ownerId === 'null') {
            console.error('Invalid owner ID received:', ownerId);
            alert('Lỗi: Không thể xác định ID chủ sở hữu. Vui lòng chọn lại.');
            return;
        }
        
        console.log('Processed owner data:', {ownerId, ownerName});
          // Cập nhật TẤT CẢ hidden input cho OwnerID để đảm bảo đồng bộ
        $('#selectedOwnerId').val(ownerId);
        $('#ownerSearchId').val(ownerId);
        $('#ownerAutocompleteId').val(ownerId);
        
        console.log('Updated all owner ID fields in modal:', {
            selectedOwnerId: $('#selectedOwnerId').val(),
            ownerSearchId: $('#ownerSearchId').val(),
            ownerAutocompleteId: $('#ownerAutocompleteId').val()
        });
        
        // Hiển thị section chọn bất động sản
        $('#propertySelectionSection').slideDown(300);
        
        // Load danh sách bất động sản từ AJAX
        loadOwnerProperties(ownerId, ownerName);
    });
    
    // Lắng nghe event customer:selected từ customer-autocomplete component
    $(document).on('customer:selected', function(event, customerId, customerName, customerData) {
        console.log('Customer selected:', {
            id: customerId,
            name: customerName,
            data: customerData
        });
        
        // Customer ID đã được set tự động bởi component vào field customerSearchId
        // Không cần làm gì thêm ở đây
    });
      // Function để load bất động sản của chủ sở hữu từ server
    function loadOwnerProperties(ownerId, ownerName) {
        // Validate parameters
        if (!ownerId || ownerId === 'undefined' || ownerId === 'null' || ownerId.toString().trim() === '') {
            console.error('Invalid ownerId provided to loadOwnerProperties:', ownerId);
            const $feedback = $('#propertySelectFeedback');
            $feedback.html('<span class="text-danger">Lỗi: ID chủ sở hữu không hợp lệ. Vui lòng chọn lại chủ sở hữu.</span>');
            return;
        }
        
        const $propertyCombobox = $('#propertyCombobox');
        const $loadingSpinner = $('#propertyLoadingSpinner');
        const $feedback = $('#propertySelectFeedback');
        
        console.log('Loading properties for owner:', {ownerId, ownerName});
        
        // Hiển thị loading
        $loadingSpinner.show();
        $propertyCombobox.prop('disabled', true);
        $propertyCombobox.html('<option value="">Đang tải dữ liệu...</option>');
        $feedback.html('<span class="text-info">Đang tải danh sách bất động sản...</span>');          // AJAX call để lấy danh sách bất động sản
        const ajaxUrl = `/agent/owners/${ownerId}/properties`;
        console.log('Making AJAX request to:', ajaxUrl);
        
        $.ajax({
            url: ajaxUrl,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            dataType: 'json',
            beforeSend: function(xhr, settings) {
                console.log('AJAX request starting:', {
                    url: settings.url,
                    ownerId: ownerId,
                    ownerName: ownerName
                });
            },
            success: function(response) {
                console.log('Properties loaded from server:', response);
                
                // Kiểm tra cấu trúc response từ server
                if (response.error) {
                    console.error('Server returned error:', response.error);
                    $feedback.html('<span class="text-danger">' + response.error + '</span>');
                    return;
                }
                
                const properties = response.properties || [];
                const owner = response.owner || null;
                
                console.log('Parsed properties:', properties);
                console.log('Owner info:', owner);
                
                // Lưu thông tin owner từ server response
                window.currentOwner = {
                    id: ownerId,
                    name: owner ? owner.name : ownerName,
                    properties: properties
                };
                
                populatePropertyCombobox(properties, owner ? owner.name : ownerName);
            },
            error: function(xhr, status, error) {
                console.error('Error loading properties:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                
                let errorMessage = 'Lỗi khi tải dữ liệu';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.error) {
                        errorMessage = errorResponse.error;
                    }
                } catch (e) {
                    // Ignore parsing error, use default message
                }
                
                $propertyCombobox.html('<option value="">Không thể tải dữ liệu bất động sản</option>');
                $feedback.html('<span class="text-danger">' + errorMessage + '. Vui lòng thử lại.</span>');
            },
            complete: function() {
                $loadingSpinner.hide();
                $propertyCombobox.prop('disabled', false);
            }
        });
    }
    
    // Function để populate property combobox
    function populatePropertyCombobox(properties, ownerName) {
        const $propertyCombobox = $('#propertyCombobox');
        const $feedback = $('#propertySelectFeedback');
        
        // Clear combobox
        $propertyCombobox.empty();
        
        if (!properties || properties.length === 0) {
            $propertyCombobox.append('<option value="">-- Chủ sở hữu này chưa có bất động sản nào --</option>');
            $propertyCombobox.prop('disabled', true);
            $feedback.html('<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Chủ sở hữu này chưa có bất động sản nào</span>');
            return;
        }
        
        // Thêm CSS để tạo giao diện như trong hình mẫu
        if (!$('#property-select-custom-style').length) {
            $('head').append(`
                <style id="property-select-custom-style">
                    #propertyCombobox {
                        background-color: #e3f6ff;
                        padding: 0;
                    }
                    #propertyCombobox option.owner-header {
                        padding: 8px;
                        font-weight: bold;
                    }
                    #propertyCombobox option.property-count {
                        padding: 8px;
                        font-weight: bold;
                    }
                    #propertyCombobox option.property-item {
                        padding: 8px;
                        background-color: white;
                    }
                </style>
            `);
        }
        
        // Thêm option hiển thị chủ sở hữu
        $propertyCombobox.append(`<option class="owner-header" disabled>Chủ sở hữu: ${ownerName}</option>`);
        
        // Thêm option hiển thị số lượng bất động sản
        $propertyCombobox.append(`<option class="property-count" disabled>Danh sách ${properties.length} bất động sản:</option>`);
        
        // Lưu dữ liệu properties để sử dụng sau
        window.currentProperties = properties;
          // Thêm từng bất động sản
        properties.forEach(function(property) {
            const propertyId = property.id || property.PropertyID;
            const title = property.title || property.Title || 'Không có tiêu đề';
            const address = property.address || property.Address || '';
            
            // Chuẩn hóa dữ liệu property để tương thích với các function khác
            const normalizedProperty = {
                PropertyID: propertyId,
                Title: title,
                Address: address,
                FullAddress: property.fullAddress || property.address || '',
                Price: property.price || property.Price || '',
                FormattedPrice: property.formattedPrice || property.FormattedPrice || '',
                Type: property.type || property.Type || '',
                CategoryName: property.categoryName || property.CategoryName || '',
                Category: property.categoryName || property.CategoryName || '',
                Area: property.area || property.Area || '',
                Bedroom: property.bedroom || property.Bedroom || '',

            };
            
            const $option = $('<option></option>')
                .addClass('property-item')
                .val(propertyId)
                .text(title)
                .data('property', normalizedProperty);
            
            $propertyCombobox.append($option);
        });
        
        $propertyCombobox.prop('disabled', false);
        $feedback.html(`<span class="text-success"><i class="bi bi-check-circle"></i> Tìm thấy ${properties.length} bất động sản</span>`);
          // Nếu chỉ có 1 bất động sản, tự động chọn
        if (properties.length === 1) {
            $propertyCombobox.val(properties[0].id || properties[0].PropertyID);
            $propertyCombobox.trigger('change');
        }
    }
    
    // Xử lý khi chọn bất động sản từ combobox
    $('#propertyCombobox').on('change', function() {
        const propertyId = $(this).val();
        const selectedOption = $(this).find('option:selected');
        const propertyData = selectedOption.data('property');
        
        if (propertyId && propertyData) {
            showPropertyDetails(propertyData);
            
            // Tự động điền thông tin cuộc hẹn
            autoFillAppointmentInfo(propertyData);
        } else {
            $('#propertyDetailSection').slideUp(300);
        }
    });
      // Function để hiển thị chi tiết bất động sản
    function showPropertyDetails(property) {
        const title = property.Title || property.title || 'Không có tiêu đề';
        const address = property.Address || property.address || property.FullAddress || property.fullAddress || 'Không có địa chỉ';
        
        $('#propertyDetailTitle').text(title);
        $('#propertyDetailAddress').text(address);
        
        // Hiển thị thông tin bổ sung
        let extraInfo = [];
        
        if (property.CategoryName || property.categoryName || property.Category) {
            extraInfo.push(`<span class="badge bg-info me-1">${property.CategoryName || property.categoryName || property.Category}</span>`);
        }
        
        if (property.FormattedPrice || property.formattedPrice || property.Price) {
            extraInfo.push(`<span class="badge bg-success me-1">${property.FormattedPrice || property.formattedPrice || property.Price}</span>`);
        }
        
        if (property.Area || property.area) {
            extraInfo.push(`<span class="badge bg-secondary me-1">${property.Area || property.area} m²</span>`);
        }
        
        if (property.Bedroom || property.bedroom) {
            extraInfo.push(`<span class="badge bg-secondary me-1">${property.Bedroom || property.bedroom} phòng ngủ</span>`);
        }
        
        if (property.Type || property.type) {
            const typeText = (property.Type || property.type) === 'Rent' ? 'Cho thuê' : 'Bán';
            const typeClass = (property.Type || property.type) === 'Rent' ? 'bg-primary' : 'bg-danger';
            extraInfo.push(`<span class="badge ${typeClass} me-1">${typeText}</span>`);
        }
        
        $('#propertyExtraInfo').html(extraInfo.join(' '));
        $('#propertyDetailSection').slideDown(300);
    }      // Function để tự động điền thông tin cuộc hẹn
    function autoFillAppointmentInfo(property) {
        const title = property.Title || property.title || 'Không có tiêu đề';
        const category = property.CategoryName || property.categoryName || property.Category || '';
        const address = property.Address || property.address || property.FullAddress || property.fullAddress || '';
        const price = property.FormattedPrice || property.formattedPrice || property.Price || '';
        const propertyId = property.PropertyID || property.id;
        
        // Đảm bảo PropertyID được đặt chính xác
        $('#propertyCombobox').val(propertyId);
        
        // Hiển thị chi tiết bất động sản
        showPropertyDetails(property);
        
        // Đề xuất thời gian hẹn (nếu trống)
        if (!$('#appointmentDateStart').val()) {
            // Tạo thời gian bắt đầu mặc định (1 giờ tới)
            const startDate = new Date();
            startDate.setHours(startDate.getHours() + 1);
            startDate.setMinutes(0); // Đặt phút về 0 để làm tròn giờ
            
            // Tạo thời gian kết thúc mặc định (2 giờ sau thời gian bắt đầu)
            const endDate = new Date(startDate);
            endDate.setHours(endDate.getHours() + 1);
            
            // Format datetime-local (YYYY-MM-DDThh:mm)
            const formatDateTimeLocal = (date) => {
                return date.getFullYear() + '-' +
                       String(date.getMonth() + 1).padStart(2, '0') + '-' +
                       String(date.getDate()).padStart(2, '0') + 'T' +
                       String(date.getHours()).padStart(2, '0') + ':' +
                       String(date.getMinutes()).padStart(2, '0');
            };
            
            $('#appointmentDateStart').val(formatDateTimeLocal(startDate));
            $('#appointmentDateEnd').val(formatDateTimeLocal(endDate));
        }
        
        // Tự động điền tiêu đề cuộc hẹn nếu chưa có
        const $appointmentTitle = $('#appointmentTitle');
        if (!$appointmentTitle.val()) {
            let appointmentTitleText = 'Tham quan bất động sản';
            if (category) appointmentTitleText += `: ${category}`;
            appointmentTitleText += ` - ${title}`;
            $appointmentTitle.val(appointmentTitleText);
        }
        
        // Tự động điền mô tả nếu chưa có
        const $appointmentDesc = $('textarea[name="DescAppoint"]');
        if (!$appointmentDesc.val()) {
            let description = `Cuộc hẹn xem bất động sản ${title}`;
            if (category) description += ` loại ${category}`;
            if (address) description += ` tại ${address}`;
            if (price) description += `. Giá: ${price}`;
            
            // Thêm thông tin thời gian
            const startDate = $('#appointmentDateStart').val();
            if (startDate) {
                const formattedDate = new Date(startDate).toLocaleString('vi-VN');
                description += `\nThời gian: ${formattedDate}`;
            }
            
            $appointmentDesc.val(description);
        }    }// Xử lý submit form - Đảm bảo chỉ bind 1 lần
    $(document).off('click', '#submitAppointmentBtn').on('click', '#submitAppointmentBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Disable button để tránh double-click
        const $btn = $(this);
        if ($btn.prop('disabled')) {
            console.log('Button already disabled, preventing double submission');
            return;
        }
        
        $btn.prop('disabled', true).text('Đang tạo...');
        
        // Timeout để enable lại button sau 3 giây để tránh block vĩnh viễn
        setTimeout(() => {
            $btn.prop('disabled', false).text('Tạo lịch hẹn');
        }, 3000);
          // Kiểm tra validation
        const ownerId = $('#selectedOwnerId').val();
        const propertyId = $('#propertyCombobox').val();
        const customerId = $('#customerSearchId').val(); // Kiểm tra đúng ID field
        const startDate = $('#appointmentDateStart').val();
        const endDate = $('#appointmentDateEnd').val();
        const title = $('#appointmentTitle').val();
        const description = $('textarea[name="DescAppoint"]').val();
        
        console.log('Form validation data:', {
            ownerId,
            propertyId,
            customerId,
            startDate,
            endDate,
            title,
            description
        });
        
        // Validation
        if (!ownerId) {
            alert('Vui lòng chọn chủ sở hữu!');
            return;
        }
        
        if (!propertyId) {
            alert('Vui lòng chọn bất động sản!');
            $('#propertyCombobox').focus();
            return;
        }
        
        if (!customerId) {
            alert('Vui lòng chọn khách hàng!');
            $('#customerSearch').focus();
            return;
        }
        
        if (!startDate || !endDate) {
            alert('Vui lòng nhập đầy đủ thời gian bắt đầu và kết thúc!');
            return;
        }
        
        if (new Date(startDate) >= new Date(endDate)) {
            alert('Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc!');
            return;
        }
        
        if (!title.trim()) {
            alert('Vui lòng nhập tiêu đề lịch hẹn!');
            $('#appointmentTitle').focus();
            return;
        }
        
        if (!description.trim()) {
            alert('Vui lòng nhập nội dung cuộc hẹn!');
            $('textarea[name="DescAppoint"]').focus();
            return;
        }
        
        // Đảm bảo dữ liệu được gắn đúng form fields theo model Appointment
        $('#selectedOwnerId').attr('name', 'OwnerID'); // Đảm bảo name attribute đúng
        $('#propertyCombobox').attr('name', 'PropertyID'); // Đảm bảo name attribute đúng
          // Kiểm tra xem dữ liệu đã sẵn sàng chưa
        const formData = {
            OwnerID: ownerId,
            PropertyID: propertyId,
            CusID: customerId,
            AppointmentDateStart: startDate,
            AppointmentDateEnd: endDate,
            TitleAppoint: title,
            DescAppoint: description,
            Status: 'Khởi tạo' // Nhất quán với AgentController.php và độ dài ngắn hơn
        };
          console.log('Form validation passed, submitting...', formData);
        
        // Submit form - chỉ submit 1 lần
        const form = document.getElementById('appointmentForm');
        if (form) {
            form.submit();
        } else {
            console.error('Form not found!');
            $btn.prop('disabled', false).text('Tạo lịch hẹn');
        }
    });// Reset form khi đóng modal
    $('#createAppointmentModal').on('hidden.bs.modal', function() {
        // Reset form
        document.getElementById('appointmentForm').reset();
        
        // Reset các section
        $('#propertySelectionSection').hide();
        $('#propertyDetailSection').hide();
        $('#propertySelectFeedback').empty();
        
        // Reset combobox
        $('#propertyCombobox').html('<option value="">-- Vui lòng chọn chủ sở hữu trước --</option>');
        
        // Reset hidden inputs
        $('#selectedOwnerId').val('');
        
        // Clear properties data
        window.currentProperties = null;
        window.currentOwner = null;
        
        // Xóa bỏ thông báo lỗi nếu có
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Clear owner selection nếu có function
        if (typeof window.clearOwnerSelection === 'function') {
            window.clearOwnerSelection();
        }
        
        // Clear customer selection nếu có function  
        if (typeof window.clearCustomerSelection === 'function') {
            window.clearCustomerSelection();
        }
        
        // Hiển thị thông báo hoàn tất
        console.log('Form reset successfully');
    });
});
</script>
