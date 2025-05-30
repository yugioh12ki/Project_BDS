@props([
    'id' => 'ownerSearch',
    'name' => 'OwnerID',
    'placeholder' => 'Nhập tên chủ sở hữu...',
    'required' => false,
    'apiUrl' => '/test/search/owners'
])

<div class="position-relative">
    <input type="text" 
           class="form-control" 
           id="{{ $id }}" 
           placeholder="{{ $placeholder }}" 
           autocomplete="off"
           @if($required) required @endif>
    <!-- Hidden input for Owner ID -->
    <input type="hidden" id="{{ $id }}Id" name="{{ $name }}">
    
    <!-- Dropdown gợi ý chủ sở hữu -->
    <div id="{{ $id }}Dropdown" class="property-dropdown" style="display: none;">
        <div id="{{ $id }}List"></div>
    </div>
    
    <!-- Thông tin chủ sở hữu đã chọn -->
    <div id="selected{{ ucfirst($id) }}Info" class="alert alert-info mt-2" style="display: none;">
        <i class="bi bi-person-check me-2"></i>
        Chủ sở hữu: <strong><span id="selected{{ ucfirst($id) }}Name"></span></strong>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize owner autocomplete with exact same pattern as customer search
    const $ownerSearch = $('#{{ $id }}');
    const $ownerId = $('#{{ $id }}Id');
    const $ownerDropdown = $('#{{ $id }}Dropdown');
    const $ownerList = $('#{{ $id }}List');
    const $selectedOwnerInfo = $('#selected{{ ucfirst($id) }}Info');
    const $selectedOwnerName = $('#selected{{ ucfirst($id) }}Name');
    
    // Tự động load danh sách top owners khi vừa mở trang
    setTimeout(() => {
        if ($ownerSearch.is(':visible') && $ownerDropdown.css('display') === 'none') {
            // Chỉ tự động load nếu input đang hiển thị và dropdown chưa hiện
            loadTopOwners();
        }
    }, 300); // Trì hoãn một chút để đảm bảo DOM đã sẵn sàng
    
    // Listen for customer component focus to keep owner data intact
    $(document).on('customer:focus', function() {
        // Store current owner data to prevent loss when customer component is active
        const currentOwnerId = $ownerId.val();
        const currentOwnerName = $ownerSearch.val();
        
        if (currentOwnerId && currentOwnerName) {
            console.log('Storing owner data while customer component is active:', currentOwnerName);
            // Store in global window object for persistence
            if (!window.currentAppointmentOwnerData) {
                window.currentAppointmentOwnerData = {};
            }
            
            window.currentAppointmentOwnerData.id = currentOwnerId;
            window.currentAppointmentOwnerData.name = currentOwnerName;
        }
    });
    
    // Listen for customer:focused event from customer component
    $(document).on('customer:focused', function(e, customerId, customerName) {
        console.log('Customer component focused with ID:', customerId, 'Name:', customerName);
        // No action needed, just logging for debugging
    });
    
    // Function để load danh sách top owners
    function loadTopOwners() {
        // Hiển thị loading trong dropdown
        $ownerList.html('<div class="p-3 text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Đang tải...</div>');
        $ownerDropdown.show();
        
        // Tạo hiệu ứng nổi bật cho input khi dropdown hiển thị
        $ownerSearch.addClass('dropdown-active-input');
        
        // Call API endpoint để lấy top chủ sở hữu
        $.ajax({
            url: '{{ $apiUrl }}',
            method: 'GET',
            data: { 
                top: true,
                limit: 5 // Giới hạn chỉ lấy 5 dòng dữ liệu
            },
            success: function(response) {
                $ownerList.empty();
                
                if (response.owners && response.owners.length > 0) {
                    // Hiển thị tiêu đề
                    $ownerList.append('<div class="dropdown-header"><i class="bi bi-people-fill me-2"></i>Chủ sở hữu nổi bật</div>');
                    
                    response.owners.forEach(function(owner) {
                        const ownerHtml = `
                            <div class="owner-option" data-owner-id="${owner.id}" data-owner-name="${owner.name}">
                                <div class="owner-name">${owner.name}</div>
                                <div class="owner-details">
                                    ${owner.phone ? 'SĐT: ' + owner.phone : ''}
                                    ${owner.email ? ' - Email: ' + owner.email : ''}
                                </div>
                            </div>
                        `;
                        $ownerList.append(ownerHtml);
                    });
                    
                    // Thêm gợi ý tìm kiếm thêm
                    $ownerList.append('<div class="dropdown-footer"><i class="bi bi-info-circle me-1"></i>Nhập để tìm kiếm thêm</div>');
                    
                    // Áp dụng hiệu ứng để người dùng chú ý hơn
                    $ownerDropdown.css('animation', 'pulseDropdown 1.5s ease infinite');
                    
                    // Đảm bảo dropdown hiển thị
                    $ownerDropdown.show();
                } else {
                    $ownerList.append('<div class="p-3 text-muted">Không có dữ liệu chủ sở hữu</div>');
                }
            },
            error: function() {
                $ownerList.empty();
                $ownerList.append('<div class="p-3 text-danger">Lỗi khi tải danh sách chủ sở hữu</div>');
                // Add a retry button
                $ownerList.append(`
                    <div class="text-center mb-3">
                        <button class="btn btn-sm btn-outline-primary retry-owner-load">
                            <i class="bi bi-arrow-repeat me-1"></i>Thử lại
                        </button>
                    </div>
                `);
                
                // Handle retry button click
                $('.retry-owner-load').on('click', function() {
                    loadTopOwners();
                });
            }
        });
    }
    
    // Function để tìm kiếm chủ sở hữu theo từ khóa
    function searchOwners(searchTerm) {
        // Hiển thị loading trong dropdown
        $ownerList.html('<div class="p-3 text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Đang tìm kiếm...</div>');
        $ownerDropdown.show();
        
        // Call API endpoint để tìm kiếm
        $.ajax({
            url: '{{ $apiUrl }}',
            method: 'GET',
            data: { term: searchTerm },
            success: function(response) {
                $ownerList.empty();
                
                if (response.owners && response.owners.length > 0) {
                    response.owners.forEach(function(owner) {
                        const ownerHtml = `
                            <div class="owner-option" data-owner-id="${owner.id}" data-owner-name="${owner.name}">
                                <div class="owner-name">${owner.name}</div>
                                <div class="owner-details">
                                    ${owner.phone ? 'SĐT: ' + owner.phone : ''}
                                    ${owner.email ? ' - Email: ' + owner.email : ''}
                                </div>
                            </div>
                        `;
                        $ownerList.append(ownerHtml);
                    });
                } else {
                    $ownerList.append('<div class="p-3 text-muted">Không tìm thấy chủ sở hữu phù hợp</div>');
                }
            },
            error: function() {
                $ownerList.empty();
                $ownerList.append('<div class="p-3 text-danger">Lỗi khi tìm kiếm chủ sở hữu</div>');
                // Add a retry button
                $ownerList.append(`
                    <div class="text-center mb-3">
                        <button class="btn btn-sm btn-outline-primary retry-owner-search">
                            <i class="bi bi-arrow-repeat me-1"></i>Thử lại
                        </button>
                    </div>
                `);
                
                // Handle retry button click
                $('.retry-owner-search').on('click', function() {
                    searchOwners($ownerSearch.val());
                });
            }
        });
    }
    
    // Hiển thị danh sách dropdown khi click vào input
    $ownerSearch.on('focus', function() {
        // LUÔN hiển thị danh sách dropdown khi focus vào input - theo yêu cầu
        // Lưu giá trị hiện tại để giữ lại nếu đã có lựa chọn
        const currentOwnerId = $ownerId.val();
        const currentOwnerName = $ownerSearch.val();
        
        // Luôn hiển thị danh sách gợi ý khi focus vào input
        loadTopOwners();
        
        // Nếu đã có owner được chọn trước đó, đảm bảo giữ nguyên ID và giá trị trong input
        if (currentOwnerId) {
            console.log('Owner already selected, preserving input value:', currentOwnerName);
            // Hiển thị gợi ý nhưng vẫn giữ nguyên ID và tên chủ sở hữu đã chọn
            $ownerId.val(currentOwnerId);
            $ownerSearch.val(currentOwnerName);
            
            // Tạo hiệu ứng nhấp nháy nhẹ để thông báo người dùng biết rằng dữ liệu được giữ lại
            $ownerSearch.addClass('preserved-data-highlight');
            setTimeout(() => {
                $ownerSearch.removeClass('preserved-data-highlight');
            }, 1000);
            
            // Trigger owner:focused event to notify other components
            $ownerSearch.trigger('owner:focused', [currentOwnerId, currentOwnerName]);
        }
    });
    
    // Xử lý khi nhập để search
    $ownerSearch.on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        const selectedOwnerName = $selectedOwnerName.text().toLowerCase().trim();
        
        // Lưu trữ giá trị hiện tại của input
        const currentInputValue = $(this).val();
        
        // Nếu đã có owner được chọn (có owner ID)
        if ($ownerId.val()) {
            // Nếu người dùng thay đổi text (khác với tên đã chọn)
            if (searchTerm !== selectedOwnerName) {
                console.log('Text changed, searching for new owner');
                
                // CHÚ Ý: KHÔNG xóa ID và tên hiện tại nếu người dùng đang nhập thêm ký tự
                // Chỉ xóa ID nếu người dùng xóa hết text ban đầu hoặc thay đổi hoàn toàn
                if (!searchTerm.startsWith(selectedOwnerName.substring(0, 3)) && 
                    !selectedOwnerName.startsWith(searchTerm.substring(0, 3))) {
                    $ownerId.val('');
                    // Giữ nguyên thông tin hiển thị bên dưới cho đến khi chọn owner mới
                }
            } else {
                // Nếu text trùng với tên owner đã chọn, giữ nguyên selection và input
                console.log('Preserving selected owner:', selectedOwnerName);
            }
        }
        
        if (searchTerm.length < 2) {
            // Nếu xóa tất cả ký tự, hiển thị lại danh sách top owners
            loadTopOwners();
            return;
        }
        
        // Tìm kiếm với từ khóa - KHÔNG xóa lựa chọn hiện tại
        searchOwners(searchTerm);
    });
    
    // Handle owner selection - simplified without property handling
    $(document).on('click', '.owner-option', function() {
        const ownerId = $(this).data('owner-id');
        const ownerName = $(this).data('owner-name');
        
        console.log('Owner selected from dropdown:', {
            ownerId: ownerId,
            ownerName: ownerName,
            rawData: {
                'data-owner-id': $(this).attr('data-owner-id'),
                'data-owner-name': $(this).attr('data-owner-name')
            }
        });
        
        // Validate that we have valid data
        if (!ownerId || !ownerName || ownerId === 'undefined' || ownerName === 'undefined') {
            console.error('Invalid owner data received from dropdown click:', {
                ownerId: ownerId,
                ownerName: ownerName,
                element: $(this)[0]
            });
            alert('Lỗi: Dữ liệu chủ sở hữu không hợp lệ. Vui lòng thử lại.');
            return;
        }
        
        // Lưu ID owner vào hidden input
        $ownerId.val(ownerId);
        
        // ĐẢM BẢO giá trị hiển thị trong input field - để luôn hiển thị tên chủ sở hữu đã chọn
        $ownerSearch.val(ownerName);
        
        // Cập nhật thông tin hiển thị bên dưới
        $selectedOwnerName.text(ownerName);
        $selectedOwnerInfo.show();
        
        // QUAN TRỌNG: Cố định lại giá trị input sau khi đã chọn từ dropdown để đảm bảo không mất
        setTimeout(function() {
            // Double-check để đảm bảo tên chủ sở hữu vẫn hiển thị trong input
            if ($ownerSearch.val() !== ownerName) {
                $ownerSearch.val(ownerName);
            }
        }, 100);
        
        // Lưu dữ liệu owner vào window object để tránh mất dữ liệu khi chuyển đổi giữa các trường
        window.selectedOwnerData = {
            id: ownerId,
            name: ownerName
        };
        
        // Bổ sung hiệu ứng nhấp nháy để thông báo đã chọn thành công
        $ownerSearch.addClass('success-selection-highlight');
        setTimeout(() => {
            $ownerSearch.removeClass('success-selection-highlight');
        }, 1000);
        
        $ownerDropdown.hide();
        
        // Đảm bảo tất cả các ID field được cập nhật đồng bộ
        $('#ownerSearchId').val(ownerId);
        $('#selectedOwnerId').val(ownerId);
        
        console.log('Updated all owner ID fields:', {
            ownerAutocompleteId: $ownerId.val(),
            ownerSearchId: $('#ownerSearchId').val(), 
            selectedOwnerId: $('#selectedOwnerId').val()
        });
        
        // Trigger custom event to notify other components about owner selection
        $ownerSearch.trigger('owner:selected', [{
            id: ownerId,
            name: ownerName,
            fullName: ownerName,
            text: ownerName
        }]);
    });
    
    // Removed property reload event handler
    
    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $ownerDropdown.hide();
            $ownerSearch.removeClass('dropdown-active-input');
        }
    });
    
    // Helper functions to get selected data
    window.getSelectedOwnerData = function() {
        // Also check the window's global storage in addition to the component's local storage
        const componentOwnerId = $ownerId.val();
        const globalOwnerId = window.selectedOwnerData ? window.selectedOwnerData.id : null;
        const persistentOwnerId = window.currentAppointmentOwnerData ? window.currentAppointmentOwnerData.id : null;
        
        // Take the first non-empty ID from either the component or global storage
        const effectiveOwnerId = componentOwnerId || globalOwnerId || persistentOwnerId || '';
        const effectiveOwnerName = $selectedOwnerName.text() || 
                                 (window.selectedOwnerData ? window.selectedOwnerData.name : '') || 
                                 (window.currentAppointmentOwnerData ? window.currentAppointmentOwnerData.name : '');
        
        // If ID is retrieved from window storage but not set in the component, update the component
        if (!componentOwnerId && effectiveOwnerId) {
            $ownerId.val(effectiveOwnerId);
        }
        
        return {
            ownerId: effectiveOwnerId,
            ownerName: effectiveOwnerName
        };
    };
    
    window.clearOwnerSelection = function(preserveInput = false) {
        if (!preserveInput) {
            $ownerSearch.val('');
        }
        $ownerId.val('');
        $selectedOwnerInfo.hide();
        
        // Clear all global window storage for this owner
        if (window.selectedOwnerData) {
            window.selectedOwnerData = null;
        }
        
        if (window.currentAppointmentOwnerData) {
            window.currentAppointmentOwnerData = null;
        }
    };
    
    // Property selection handlers have been removed (moved to the modal component)
});
</script>
