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
    <!-- Container to store property data -->
    <div id="propertyDataContainer" style="display: none;"></div>
    
    <!-- Dropdown gợi ý chủ sở hữu - matches existing pattern -->
    <div id="{{ $id }}Dropdown" class="property-dropdown" style="display: none;">
        <div id="{{ $id }}List"></div>
    </div>
    
    <!-- Thông tin chủ sở hữu đã chọn - matches existing pattern -->
    <div id="selected{{ ucfirst($id) }}Info" class="alert alert-info mt-2" style="display: none;">
        <i class="bi bi-person-check me-2"></i>
        Chủ sở hữu: <strong><span id="selected{{ ucfirst($id) }}Name"></span></strong>
        <div class="mt-2 alert alert-success" id="selectedPropertyInfo" style="display: none;">
            <i class="bi bi-building me-2"></i>
            Bất động sản: <strong><span id="selectedPropertyTitle"></span></strong>
        </div>
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
    
    $ownerSearch.on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        
        if (searchTerm.length < 2) {
            $ownerDropdown.hide();
            return;
        }
        
        // Clear previous selection
        $ownerId.val('');
        $selectedOwnerInfo.hide();
        
        // Search owners via AJAX
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
                    $ownerDropdown.show();
                } else {
                    $ownerList.append('<div class="p-3 text-muted">Không tìm thấy chủ sở hữu phù hợp</div>');
                    $ownerDropdown.show();
                }
            },
            error: function() {
                $ownerList.empty();
                $ownerList.append('<div class="p-3 text-danger">Lỗi khi tìm kiếm chủ sở hữu</div>');
                $ownerDropdown.show();
            }
        });
    });
    
    // Handle owner selection - same pattern as customer selection
    $(document).on('click', '.owner-option', function() {
        const ownerId = $(this).data('owner-id');
        const ownerName = $(this).data('owner-name');
        
        console.log('Owner selected from dropdown:', ownerId, ownerName);
        
        $ownerId.val(ownerId);
        $ownerSearch.val(ownerName);
        $selectedOwnerName.text(ownerName);
        $selectedOwnerInfo.show();
        $ownerDropdown.hide();
        
        // Đảm bảo OwnerID được cập nhật trong form
        $('#ownerSearchId').val(ownerId);
        
        // Tải danh sách bất động sản của chủ sở hữu theo OwnerID
        // Hiển thị thông báo đang tải
        $('#selectedPropertyInfo').show();
        $('#selectedPropertyTitle').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang tải dữ liệu bất động sản...');
        
        console.log('Gọi API lấy bất động sản cho chủ sở hữu:', ownerId);
        
        $.ajax({
            url: '{{ route("agent.owner.properties") }}',
            method: 'GET',
            data: { ownerId: ownerId },
            success: function(response) {
                console.log('API Response:', response);
                
                if (response.properties && response.properties.length > 0) {
                    console.log('Tìm thấy ' + response.properties.length + ' bất động sản');
                    console.log('Bất động sản đầu tiên:', response.properties[0]);
                    
                    // Hiển thị thông tin bất động sản đầu tiên
                    const firstProperty = response.properties[0];
                    const propertyTitle = firstProperty.title || 'Không có tiêu đề';
                    const propertyAddress = firstProperty.fullAddress || firstProperty.address || '';
                    
                    $('#selectedPropertyInfo').removeClass('alert-warning alert-danger').addClass('alert-success').show();
                    
                    // Hiển thị tiêu đề và địa chỉ
                    let displayText = propertyTitle;
                    if (propertyAddress) {
                        displayText += ` (${propertyAddress})`;
                    }
                    
                    // Thêm thông tin giá và loại bất động sản nếu có
                    if (firstProperty.formattedPrice) {
                        displayText += ` - ${firstProperty.formattedPrice}`;
                    }
                    
                    $('#selectedPropertyTitle').text(displayText);
                    
                    // Lưu vào hidden input để tham chiếu
                    $('#propertyDataContainer').data('properties', response.properties);
                    $('#propertyDataContainer').data('owner', response.owner);
                    
                    // Đảm bảo OwnerID được cập nhật từ API response nếu có
                    if (response.owner && response.owner.id) {
                        $('#ownerSearchId').val(response.owner.id);
                    } else {
                        // Giữ nguyên giá trị đã đặt
                        $('#ownerSearchId').val(ownerId);
                    }
                    
                    // Trigger custom event với đầy đủ thông tin properties và owner
                    console.log('Trigger owner:selected event với properties:', response.properties);
                    $ownerSearch.trigger('owner:selected', [ownerId, ownerName, response.properties, response.owner]);
                } else {
                    console.log('Không tìm thấy bất động sản cho chủ sở hữu này');
                    $('#selectedPropertyInfo').removeClass('alert-success alert-danger').addClass('alert-warning').show();
                    $('#selectedPropertyTitle').text('Chủ sở hữu này không có bất động sản nào');
                    
                    // Đảm bảo OwnerID vẫn được cập nhật
                    $('#ownerSearchId').val(ownerId);
                    
                    // Trigger custom event for property loading (without properties) nhưng vẫn có owner info
                    $ownerSearch.trigger('owner:selected', [ownerId, ownerName, [], response.owner]);
                }
            },
            error: function(xhr, status, error) {
                $('#selectedPropertyInfo').removeClass('alert-success alert-warning').addClass('alert-danger').show();
                $('#selectedPropertyTitle').text('Không thể tải thông tin bất động sản: ' + error);
                console.error('Error loading properties:', error, xhr.responseText);
                
                // Đảm bảo là ownerId vẫn được cập nhật
                $('#ownerSearchId').val(ownerId);
                
                // Trigger custom event for property loading (with error)
                $ownerSearch.trigger('owner:selected', [ownerId, ownerName]);
            }
        });
    });
    
    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $ownerDropdown.hide();
        }
    });
});
</script>
