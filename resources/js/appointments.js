$(document).ready(function() {
    const $propertySearch = $('#propertySearch');
    const $ownerInfo = $('#ownerInfo');
    const $ownerName = $('#ownerName');
    const $propertyID = $('#propertyID');

    $propertySearch.on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        console.log('Searching for:', searchTerm); // Debug log

        if (searchTerm.length >= 2) {
            // Tìm trong danh sách properties đã load
            const found = window.propertyList.find(p => 
                p.title.toLowerCase().includes(searchTerm)
            );
            
            console.log('Found property:', found); // Debug log

            if (found) {
                // Hiển thị thông tin chủ sở hữu
                $propertyID.val(found.id);
                $ownerName.text(found.ownerName);
                $ownerInfo.slideDown();
            } else {
                // Xóa và ẩn thông tin nếu không tìm thấy
                $propertyID.val('');
                $ownerName.text('');
                $ownerInfo.slideUp();
            }
        } else {
            // Xóa và ẩn thông tin nếu input quá ngắn
            $propertyID.val('');
            $ownerName.text('');
            $ownerInfo.slideUp();
        }
    });

    $('#propertySearch').on('keyup', function() {
        const searchTerm = $(this).val().trim();
        
        if (searchTerm.length >= 2) {
            $.get('/agent/search-properties?term=' + searchTerm, function(data) {
                if (data) {
                    $('#propertyID').val(data.id);
                    $('#ownerInfo').html(`<div class="mt-2 alert alert-info">Chủ sở hữu: ${data.ownerName}</div>`).show();
                } else {
                    $('#propertyID').val('');
                    $('#ownerInfo').hide();
                }
            });
        } else {
            $('#propertyID').val('');
            $('#ownerInfo').hide();
        }
    });
});
