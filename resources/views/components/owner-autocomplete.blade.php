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
    <input type="hidden" id="{{ $id }}Id" name="{{ $name }}">
    
    <!-- Dropdown gợi ý chủ sở hữu - matches existing pattern -->
    <div id="{{ $id }}Dropdown" class="property-dropdown" style="display: none;">
        <div id="{{ $id }}List"></div>
    </div>
    
    <!-- Thông tin chủ sở hữu đã chọn - matches existing pattern -->
    <div id="selected{{ ucfirst($id) }}Info" class="alert alert-info mt-2" style="display: none;">
        <i class="bi bi-person-check me-2"></i>
        Đã chọn: <strong><span id="selected{{ ucfirst($id) }}Name"></span></strong>
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
        
        $ownerId.val(ownerId);
        $ownerSearch.val(ownerName);
        $selectedOwnerName.text(ownerName);
        $selectedOwnerInfo.show();
        $ownerDropdown.hide();
        
        // Trigger custom event for property loading
        $ownerSearch.trigger('owner:selected', [ownerId, ownerName]);
    });
    
    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $ownerDropdown.hide();
        }
    });
});
</script>
