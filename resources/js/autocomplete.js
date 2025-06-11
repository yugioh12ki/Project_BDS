$('#ownerInput').on('input', function () {
    const keyword = $(this).val().trim();
    if (keyword.length >= 2) {
        $.ajax({
            url: '/agent/search-owners',
            data: { term: keyword },
            success: function (data) {
                let suggestions = '';
                (data.owners || []).forEach(function (owner) {
                    suggestions += `<a href="#" class="list-group-item list-group-item-action owner-suggestion" data-id="${owner.id}" data-name="${owner.name}">${owner.name} - ${owner.phone}</a>`;
                });
                if (suggestions) {
                    $('#ownerSuggestions').html(suggestions).show();
                } else {
                    $('#ownerSuggestions').hide();
                }
            }
        });
    } else {
        $('#ownerSuggestions').hide();
    }
});

$(document).on('click', '.owner-suggestion', function (e) {
    e.preventDefault();
    const ownerId = $(this).data('id');
    const ownerName = $(this).data('name');
    $('#ownerInput').val(ownerName);
    $('#ownerIdInput').val(ownerId);
    $('#ownerSuggestions').hide();
    // Load Property
    $('#propertySelectionSection').show();
    $('#propertySelect').html('<option value="">-- Đang tải bất động sản --</option>');
    $.get('/agent/search-properties', { owner_id: ownerId }, function (data) {
        let html = '<option value="">-- Chọn bất động sản --</option>';
        (data.properties || data).forEach(p => html += `<option value="${p.id}">${p.title}</option>`);
        $('#propertySelect').html(html);
    });
});

// Khi thay đổi input chủ sở hữu => reset ẩn property
$('#ownerInput').on('input', function () {
    $('#ownerIdInput').val('');
    $('#propertySelectionSection').hide();
    $('#propertySelect').html('<option value="">-- Vui lòng chọn chủ sở hữu trước --</option>');
});

// Khách hàng autocomplete
$('#customerInput').on('input', function () {
    const keyword = $(this).val().trim();
    if (keyword.length < 2) {
        $('#customerSuggestions').hide();
        return;
    }
    $.get('/agent/search-customers', { term: keyword }, function (res) {
        let html = '';
        (res.customers || res).forEach(function (cus) {
            // tuỳ API trả về
            const id = cus.id || cus.UserID;
            const name = cus.name || cus.Name;
            const phone = cus.phone || cus.Phone;
            html += `<a href="#" class="list-group-item list-group-item-action customer-suggestion" data-id="${id}" data-name="${name}">
                ${name} - ${phone ?? ''}
            </a>`;
        });
        if (html) {
            $('#customerSuggestions').html(html).show();
        } else {
            $('#customerSuggestions').hide();
        }
    });
});

// Khi chọn khách hàng
$(document).on('click', '.customer-suggestion', function (e) {
    e.preventDefault();
    const cusId = $(this).data('id');
    const cusName = $(this).data('name');
    $('#customerInput').val(cusName);
    $('#customerIdInput').val(cusId);
    $('#customerSuggestions').hide();
});

// Ẩn suggestion khi blur khỏi input (option)
$('#ownerInput').on('blur', function () {
    setTimeout(() => $('#ownerSuggestions').hide(), 200); // delay để kịp click
});
$('#customerInput').on('blur', function () {
    setTimeout(() => $('#customerSuggestions').hide(), 200);
});
