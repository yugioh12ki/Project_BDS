$(document).ready(function() {
    const $propertySearch = $('#propertySearch');
    const $ownerInfo = $('#ownerInfo');
    const $ownerName = $('#ownerName');
    const $propertyID = $('#propertyID');
    const $propertyList = $('#propertyList');
    const $customerSearch = $('#customerSearch');
    const $customerList = $('#customerList');

    // --- AUTOCOMPLETE BẤT ĐỘNG SẢN giống hình ---
    $propertySearch.on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        console.log('Searching for:', searchTerm); // Debug log

        if (searchTerm.length < 2) {
            $propertyList.hide();
            $propertyID.val('');
            $ownerInfo.hide();
            return;
        }

        // Tìm exact match trước
        const exactMatch = window.propertyList.find(p => 
            p.title.toLowerCase() === searchTerm
        );

        if (exactMatch) {
            console.log('Exact match found:', exactMatch); // Debug log
            $propertyID.val(exactMatch.id);
            $ownerInfo.html(`<i class="bi bi-info-circle me-2"></i>Chủ sở hữu: <strong>${exactMatch.ownerName}</strong>`);
            $ownerInfo.show();
        }

        // Hiển thị suggestions
        const filtered = window.propertyList.filter(p => 
            p.title.toLowerCase().includes(searchTerm)
        );

        if (filtered.length > 0) {
            let html = '';
            filtered.forEach(item => {
                html += `<li class="property-item">
                    <a href="#" class="dropdown-item property-option"
                        data-id="${item.id}" 
                        data-title="${item.title}"
                        data-owner="${item.ownerName}">
                        <div class="property-title">${item.title}</div>
                        <div class="owner-name">
                            <i class="bi bi-person"></i> 
                            Chủ sở hữu: ${item.ownerName}
                        </div>
                    </a>
                </li>`;
            });
            $propertyList.html(html).show();
        } else {
            $propertyList.html('<li class="dropdown-item">Không tìm thấy BĐS phù hợp</li>').show();
        }
    });

    // Sửa lại phần chọn property
    $(document).on('click', '.property-option', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const title = $(this).data('title');
        const owner = $(this).data('owner');
        
        console.log("Selected Property:", {id, title, owner}); // Debug

        $propertySearch.val(title);
        $propertyID.val(id);
        $ownerInfo.html(`<i class="bi bi-info-circle me-2"></i>Chủ sở hữu: <strong>${owner}</strong>`);
        $ownerInfo.show();
        $propertyList.hide();

        // Reset customer fields
        $customerSearch.val('');
        $customerList.hide();
    });

    // Click ngoài thì ẩn dropdown
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#propertySearch, #propertyList').length) {
            $propertyList.hide();
        }
        if (!$(e.target).closest('#customerSearch, #customerList').length) {
            $customerList.hide();
        }
    });

    // --- Đảm bảo khi blur mà user nhập y nguyên tiêu đề, vẫn set ID ---
    $propertySearch.on('blur', function() {
        setTimeout(() => { // Đợi 1 chút để tránh mất sự kiện click
            const searchTerm = $(this).val().toLowerCase().trim();
            const found = window.propertyList.find(p =>
                p.title.toLowerCase() === searchTerm
            );
            if (found) {
                $propertyID.val(found.id);
                $ownerInfo.html(`<i class="bi bi-info-circle me-2"></i>Chủ sở hữu: <strong>${found.ownerName}</strong>`);
                $ownerInfo.show();
            }
        }, 200);
    });

    // --- GỢI Ý KHÁCH HÀNG (giữ nguyên logic như bạn đang dùng) ---
    $customerSearch.on('input', function() {
        let query = $(this).val().trim();
        let propertyId = $propertyID.val();
        if (!propertyId) {
            $customerList.html(`<li class="dropdown-item">Vui lòng nhập bất động sản trước</li>`).show();
            return;
        }

        if (query.length > 0) {
            $.ajax({
                url: "/agent/customers/search",
                method: 'GET',
                data: {
                    term: query,
                    propertyId: propertyId
                },
                success: function(response) {
                    let html = '';
                    if (response.customers && response.customers.length > 0) {
                        response.customers.forEach(function(customer) {
                            html += `
                                <li>
                                    <a class="dropdown-item customer-option" href="#" 
                                       data-id="${customer.id}" 
                                       data-name="${customer.name}"
                                       data-phone="${customer.phone}">
                                        <div class="customer-name">${customer.name}</div>
                                        <small class="text-muted">
                                            <i class="bi bi-telephone"></i> ${customer.phone}
                                        </small>
                                    </a>
                                </li>`;
                        });
                        $customerList.html(html).show();
                    } else {
                        html = '<li class="dropdown-item">Không có khách hàng phù hợp</li>';
                    }
                    $customerList.html(html).show();
                }
            });
        } else {
            $customerList.hide();
        }
    });

    // Khi click vào 1 dòng khách hàng
    $(document).on('click', '.customer-option', function(e) {
        e.preventDefault();
        let name = $(this).data('name');
        let phone = $(this).data('phone');
        $customerSearch.val(name);
        $('input[name="CustomerPhone"]').val(phone); // auto điền SĐT nếu muốn
        $customerList.hide();
    });

});
