@props([
    'id' => 'customerSearch',
    'name' => 'CustomerID',
    'placeholder' => 'Nhập tên khách hàng...',
    'required' => false,
    'apiUrl' => '/test/search/customers'
])

<div class="position-relative">
    <input type="text" 
           class="form-control" 
           id="{{ $id }}" 
           placeholder="{{ $placeholder }}" 
           autocomplete="off"
           @if($required) required @endif>
    <!-- Hidden input for Customer ID -->
    <input type="hidden" id="{{ $id }}Id" name="{{ $name }}">
    
    <!-- Dropdown gợi ý khách hàng -->
    <div id="{{ $id }}Dropdown" class="customer-dropdown" style="display: none;">
        <div id="{{ $id }}List"></div>
    </div>
    
    <!-- Thông tin khách hàng đã chọn -->
    <div id="selected{{ ucfirst($id) }}Info" class="alert alert-info mt-2" style="display: none;">
        <i class="bi bi-person-check me-2"></i>
        Khách hàng: <strong><span id="selected{{ ucfirst($id) }}Name"></span></strong>
        <div class="mt-1 small" id="{{ $id }}Details"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize customer autocomplete
    const $customerSearch = $('#{{ $id }}');
    const $customerId = $('#{{ $id }}Id');
    const $customerDropdown = $('#{{ $id }}Dropdown');
    const $customerList = $('#{{ $id }}List');
    const $selectedCustomerInfo = $('#selected{{ ucfirst($id) }}Info');
    const $selectedCustomerName = $('#selected{{ ucfirst($id) }}Name');
    const $customerDetails = $('#{{ $id }}Details');
    
    // Tự động load danh sách top customers khi vừa mở trang
    setTimeout(() => {
        if ($customerSearch.is(':visible') && $customerDropdown.css('display') === 'none') {
            // Chỉ tự động load nếu input đang hiển thị và dropdown chưa hiện
            loadTopCustomers();
        }
    }, 300); // Trì hoãn một chút để đảm bảo DOM đã sẵn sàng
    
    // Listen for owner component focus to keep customer data intact
    $(document).on('owner:focused', function(e, ownerId, ownerName) {
        console.log('Owner component focused with ID:', ownerId, 'Name:', ownerName);
        
        // Store current customer data to prevent loss when owner component is active
        const currentCustomerId = $customerId.val();
        const currentCustomerName = $customerSearch.val();
        
        if (currentCustomerId && currentCustomerName) {
            console.log('Storing customer data while owner component is active:', currentCustomerName);
            // Store in global window object for persistence
            if (!window.currentAppointmentCustomerData) {
                window.currentAppointmentCustomerData = {};
            }
            
            window.currentAppointmentCustomerData.id = currentCustomerId;
            window.currentAppointmentCustomerData.name = currentCustomerName;
        }
    });
    
    // Function để load danh sách top customers
    function loadTopCustomers() {
        // Hiển thị loading trong dropdown
        $customerList.html('<div class="p-3 text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Đang tải...</div>');
        $customerDropdown.show();
        
        // Tạo hiệu ứng nổi bật cho input khi dropdown hiển thị
        $customerSearch.addClass('dropdown-active-input');
        
        // Call API endpoint để lấy top khách hàng
        $.ajax({
            url: '{{ $apiUrl }}',
            method: 'GET',
            data: { 
                top: true,
                limit: 5 // Giới hạn chỉ lấy 5 dòng dữ liệu
            },
            success: function(response) {
                $customerList.empty();
                
                if (response.customers && response.customers.length > 0) {
                    // Hiển thị tiêu đề
                    $customerList.append('<div class="dropdown-header"><i class="bi bi-people-fill me-2"></i>Khách hàng nổi bật</div>');
                    
                    response.customers.forEach(function(customer) {
                        const customerHtml = `
                            <div class="customer-option" 
                                 data-customer-id="${customer.id}" 
                                 data-customer-name="${customer.name}"
                                 data-customer-phone="${customer.phone || ''}"
                                 data-customer-email="${customer.email || ''}">
                                <div class="customer-name">${customer.name}</div>
                                <div class="customer-details">
                                    ${customer.phone ? 'SĐT: ' + customer.phone : ''}
                                    ${customer.email ? ' - Email: ' + customer.email : ''}
                                </div>
                            </div>
                        `;
                        $customerList.append(customerHtml);
                    });
                    
                    // Thêm gợi ý tìm kiếm thêm
                    $customerList.append('<div class="dropdown-footer"><i class="bi bi-info-circle me-1"></i>Nhập để tìm kiếm thêm</div>');
                    
                    // Áp dụng hiệu ứng để người dùng chú ý hơn
                    $customerDropdown.css('animation', 'pulseDropdown 1.5s ease infinite');
                    
                    // Đảm bảo dropdown hiển thị
                    $customerDropdown.show();
                } else {
                    $customerList.append('<div class="p-3 text-muted">Không có dữ liệu khách hàng</div>');
                }
            },            error: function() {
                $customerList.empty();
                $customerList.append('<div class="p-3 text-danger">Lỗi khi tải danh sách khách hàng</div>');
                // Add a retry button
                $customerList.append(`
                    <div class="text-center mb-3">
                        <button class="btn btn-sm btn-outline-primary retry-customer-load">
                            <i class="bi bi-arrow-repeat me-1"></i>Thử lại
                        </button>
                    </div>
                `);
                
                // Handle retry button click
                $('.retry-customer-load').on('click', function() {
                    loadTopCustomers();
                });
            }
        });
    }
    
    // Function để tìm kiếm khách hàng theo từ khóa
    function searchCustomers(searchTerm) {
        // Hiển thị loading trong dropdown
        $customerList.html('<div class="p-3 text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Đang tìm kiếm...</div>');
        $customerDropdown.show();
        
        // Call API endpoint để tìm kiếm
        $.ajax({
            url: '{{ $apiUrl }}',
            method: 'GET',
            data: { term: searchTerm },
            success: function(response) {
                $customerList.empty();
                
                if (response.customers && response.customers.length > 0) {
                    response.customers.forEach(function(customer) {
                        const customerHtml = `
                            <div class="customer-option" 
                                 data-customer-id="${customer.id}" 
                                 data-customer-name="${customer.name}"
                                 data-customer-phone="${customer.phone || ''}"
                                 data-customer-email="${customer.email || ''}">
                                <div class="customer-name">${customer.name}</div>
                                <div class="customer-details">
                                    ${customer.phone ? 'SĐT: ' + customer.phone : ''}
                                    ${customer.email ? ' - Email: ' + customer.email : ''}
                                </div>
                            </div>
                        `;
                        $customerList.append(customerHtml);
                    });
                } else {
                    $customerList.append('<div class="p-3 text-muted">Không tìm thấy khách hàng phù hợp</div>');
                }
            },            error: function() {
                $customerList.empty();
                $customerList.append('<div class="p-3 text-danger">Lỗi khi tìm kiếm khách hàng</div>');
                // Add a retry button
                $customerList.append(`
                    <div class="text-center mb-3">
                        <button class="btn btn-sm btn-outline-primary retry-customer-search">
                            <i class="bi bi-arrow-repeat me-1"></i>Thử lại
                        </button>
                    </div>
                `);
                
                // Handle retry button click
                $('.retry-customer-search').on('click', function() {
                    searchCustomers($customerSearch.val());
                });
            }
        });
    }    // Hiển thị danh sách dropdown khi click vào input
    $customerSearch.on('focus', function() {
        // LUÔN hiển thị danh sách dropdown khi click vào input - theo yêu cầu
        // Nếu đã có lựa chọn trước đó, vẫn hiển thị dropdown nhưng lưu giá trị đã chọn
        const currentCustomerId = $customerId.val();
        const currentCustomerName = $customerSearch.val();

        // Luôn hiển thị danh sách gợi ý khi focus vào input
        loadTopCustomers();
        
        // Nếu đã có customer được chọn trước đó, đảm bảo giữ nguyên ID và giá trị trong input
        if (currentCustomerId) {
            console.log('Customer already selected, preserving input value:', currentCustomerName);
            // Hiển thị gợi ý nhưng vẫn giữ nguyên ID và tên khách hàng đã chọn
            $customerId.val(currentCustomerId);
            $customerSearch.val(currentCustomerName);
            
            // Tạo hiệu ứng nhấp nháy nhẹ để thông báo người dùng biết rằng dữ liệu được giữ lại
            $customerSearch.addClass('preserved-data-highlight');
            setTimeout(() => {
                $customerSearch.removeClass('preserved-data-highlight');
            }, 1000);
            
            // Trigger customer:focused event to notify other components
            $customerSearch.trigger('customer:focused', [currentCustomerId, currentCustomerName]);
        }
        
        // Trigger event to notify other components that customer search is focused - để giữ nguyên dữ liệu owner
        $customerSearch.trigger('customer:focus');
    });// Xử lý khi nhập để search
    $customerSearch.on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        const selectedCustomerName = $selectedCustomerName.text().toLowerCase().trim();
        
        // Lưu trữ giá trị hiện tại của input
        const currentInputValue = $(this).val();
        
        // Nếu đã có customer được chọn (có customer ID)
        if ($customerId.val()) {
            // Nếu người dùng thay đổi text (khác với tên đã chọn)
            if (searchTerm !== selectedCustomerName) {
                console.log('Text changed, searching for new customer');
                
                // Hiển thị thông báo đang tìm kiếm khách hàng mới
                $selectedCustomerInfo.hide();
                
                // CHÚ Ý: KHÔNG xóa ID và tên hiện tại nếu người dùng đang nhập thêm ký tự
                // Chỉ xóa ID nếu người dùng xóa hết text ban đầu hoặc thay đổi hoàn toàn
                if (!searchTerm.startsWith(selectedCustomerName.substring(0, 3)) && 
                    !selectedCustomerName.startsWith(searchTerm.substring(0, 3))) {
                    $customerId.val('');
                }
            } else {
                // Nếu text trùng với tên customer đã chọn, giữ nguyên selection và input
                console.log('Preserving selected customer:', selectedCustomerName);
                return;
            }
        }
        
        if (searchTerm.length < 2) {
            // Nếu xóa tất cả ký tự, hiển thị lại danh sách top customers
            loadTopCustomers();
            return;
        }
        
        // Tìm kiếm với từ khóa - KHÔNG xóa lựa chọn hiện tại
        searchCustomers(searchTerm);
    });    // Handle customer selection
    $(document).on('click', '.customer-option', function() {
        const customerId = $(this).data('customer-id');
        const customerName = $(this).data('customer-name');
        const customerPhone = $(this).data('customer-phone') || '';
        const customerEmail = $(this).data('customer-email') || '';
        
        console.log('Customer selected from dropdown:', customerId, customerName);
        
        // Lưu ID customer vào hidden input
        $customerId.val(customerId);
        
        // ĐẢM BẢO giá trị hiển thị trong input field - để luôn hiển thị tên khách hàng đã chọn
        $customerSearch.val(customerName);
        
        // Cập nhật thông tin hiển thị bên dưới
        $selectedCustomerName.text(customerName);
        
        // Hiển thị thông tin bổ sung
        let detailsHtml = '';
        if (customerPhone) {
            detailsHtml += `<span class="me-2"><i class="bi bi-telephone me-1"></i>${customerPhone}</span>`;
        }
        if (customerEmail) {
            detailsHtml += `<span><i class="bi bi-envelope me-1"></i>${customerEmail}</span>`;
        }
        $customerDetails.html(detailsHtml);
        
        // Hiển thị thông tin khách hàng đã chọn
        $selectedCustomerInfo.show();
        
        // QUAN TRỌNG: Cố định lại giá trị input sau khi đã chọn từ dropdown để đảm bảo không mất
        setTimeout(function() {
            // Double-check để đảm bảo tên khách hàng vẫn hiển thị trong input
            if ($customerSearch.val() !== customerName) {
                $customerSearch.val(customerName);
            }
        }, 100);
        
        // Lưu dữ liệu customer để tránh mất dữ liệu khi chuyển đổi giữa các trường
        window.selectedCustomerData = {
            id: customerId,
            name: customerName,
            phone: customerPhone, 
            email: customerEmail
        };
        
        // Ẩn dropdown khi đã chọn
        $customerDropdown.hide();
        
        // Bổ sung hiệu ứng nhấp nháy để thông báo đã chọn thành công
        $customerSearch.addClass('success-selection-highlight');
        setTimeout(() => {
            $customerSearch.removeClass('success-selection-highlight');
        }, 1000);
        
        // Trigger custom event cho việc chọn khách hàng
        $customerSearch.trigger('customer:selected', [customerId, customerName, {
            phone: customerPhone,
            email: customerEmail
        }]);
    });
      // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $customerDropdown.hide();
            $customerSearch.removeClass('dropdown-active-input');
        }
    });// Helper functions to get selected data
    window.getSelectedCustomerData = function() {
        // Also check the window's global storage in addition to the component's local storage
        const componentCustomerId = $customerId.val();
        const globalCustomerId = window.selectedCustomerData ? window.selectedCustomerData.id : null;
        const persistentCustomerId = window.currentAppointmentCustomerData ? window.currentAppointmentCustomerData.id : null;
        
        // Take the first non-empty ID from any available storage
        const effectiveCustomerId = componentCustomerId || globalCustomerId || persistentCustomerId || '';
        const effectiveCustomerName = $selectedCustomerName.text() || 
                                   (window.selectedCustomerData ? window.selectedCustomerData.name : '') || 
                                   (window.currentAppointmentCustomerData ? window.currentAppointmentCustomerData.name : '');
        
        // If ID is retrieved from window storage but not set in the component, update the component
        if (!componentCustomerId && effectiveCustomerId) {
            $customerId.val(effectiveCustomerId);
        }
        
        // Collect all customer data from all possible sources
        const customerData = {
            customerId: effectiveCustomerId,
            customerName: effectiveCustomerName,
            phone: (window.selectedCustomerData ? window.selectedCustomerData.phone : '') || 
                  (window.currentAppointmentCustomerData ? window.currentAppointmentCustomerData.phone : ''),
            email: (window.selectedCustomerData ? window.selectedCustomerData.email : '') ||
                  (window.currentAppointmentCustomerData ? window.currentAppointmentCustomerData.email : '')
        };
        
        console.log('Getting customer data with cross-component protection:', customerData);
        return customerData;
    };
      window.clearCustomerSelection = function(preserveInput = false) {
        // Nếu preserveInput = true, chỉ xóa hidden ID và thông tin hiển thị, giữ nguyên text trong input
        if (!preserveInput) {
            $customerSearch.val('');
        }
        $customerId.val('');
        $selectedCustomerInfo.hide();
        $customerDetails.empty();
        
        // Clear all global window storage for this customer
        if (window.selectedCustomerData) {
            window.selectedCustomerData = null;
        }
        
        if (window.currentAppointmentCustomerData) {
            window.currentAppointmentCustomerData = null;
        }
    };
});
</script>
