$(document).ready(function() {
    const $propertySearch = $('#propertySearch');
    const $ownerInfo = $('#ownerInfo');
    const $ownerName = $('#ownerName');
    const $propertyID = $('#propertyID');
    const $propertyList = $('#propertyList');
    const $customerSearch = $('#customerSearch');
    const $customerList = $('#customerList');

    // Helper function để hiển thị thông tin chủ sở hữu
    function showOwnerInfo(property) {
        console.log('showOwnerInfo called with property:', property);
        if (property) {
            const ownerName = property.ownerName || 'Không xác định';
            $propertyID.val(property.id);
            $ownerName.text(ownerName);
            $ownerInfo.show();
            
            // Hiển thị chi tiết thêm để debug
            console.log('Property ID set to:', property.id);
            console.log('Owner name set to:', ownerName);
        } else {
            $propertyID.val('');
            $ownerInfo.hide();
            console.log('Owner info hidden');
        }
    }

    // Helper function để tìm kiếm property
    function findProperty(searchTerm) {
        if (!searchTerm) return null;
        
        // Tìm exact match trước (full text)
        let found = window.propertyList.find(p => 
            p.title.toLowerCase() === searchTerm.toLowerCase()
        );
        
        // Nếu không có exact match, tìm exact match bắt đầu bằng searchTerm
        if (!found) {
            found = window.propertyList.find(p => 
                p.title.toLowerCase().startsWith(searchTerm.toLowerCase())
            );
        }
        
        // Nếu vẫn không có, tìm partial match (nếu chỉ có 1 kết quả)
        if (!found) {
            const partialMatches = window.propertyList.filter(p => 
                p.title.toLowerCase().includes(searchTerm.toLowerCase())
            );
            if (partialMatches.length === 1) {
                found = partialMatches[0];
            }
        }
        
        return found;
    }

    // --- AUTOCOMPLETE BẤT ĐỘNG SẢN giống hình ---
    $propertySearch.on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        console.log('Searching for:', searchTerm); // Debug log

        if (searchTerm.length < 1) {
            $propertyList.hide();
            showOwnerInfo(null);
            return;
        }

        // Check if propertyList is defined
        if (!window.propertyList || !Array.isArray(window.propertyList)) {
            console.error('Property list is not an array:', window.propertyList);
            return;
        }

        // Hiển thị suggestions
        const filtered = window.propertyList.filter(p => 
            p.title && p.title.toLowerCase().includes(searchTerm)
        );

        console.log('Filtered properties:', filtered.length, 'results');

        if (filtered.length > 0) {
            let html = '';
            filtered.forEach(item => {
                // Đảm bảo ownerName không undefined
                const ownerName = item.ownerName || 'Không xác định';
                const isSelected = item.id == $propertyID.val(); // Highlight property đã chọn
                const selectedClass = isSelected ? 'active' : '';
                
                html += `<li class="property-item ${selectedClass}">
                    <a href="#" class="dropdown-item property-option"
                        data-id="${item.id}" 
                        data-title="${item.title}"
                        data-owner="${ownerName}">
                        <div class="property-title">${item.title}</div>
                        <div class="owner-name">
                            <i class="bi bi-person"></i> 
                            Chủ sở hữu: ${ownerName}
                        </div>
                    </a>
                </li>`;
            });
            $propertyList.html(html).show();
            
            // Nếu chỉ có 1 kết quả và nhập chuẩn xác, tự động chọn và hiển thị thông tin chủ sở hữu
            if (filtered.length === 1 && filtered[0].title.toLowerCase() === searchTerm.toLowerCase()) {
                const exactMatch = filtered[0];
                console.log("Auto-selecting exact match:", exactMatch);
                $propertyID.val(exactMatch.id);
                $ownerName.text(exactMatch.ownerName || 'Không xác định');
                $ownerInfo.css('display', 'flex');
            }
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
        
        console.log("Selected Property via Click:", {id, title, owner}); // Debug

        $propertySearch.val(title);
        $propertyID.val(id);
        
        // Hiển thị thông tin chủ sở hữu một cách rõ ràng
        $ownerName.text(owner);
        $ownerInfo.css('display', 'flex'); // Đảm bảo hiển thị
        
        // Tìm property trong list để log chi tiết (để debug)
        const selectedProperty = window.propertyList.find(p => String(p.id) === String(id));
        if (selectedProperty) {
            console.log("Found property details:", selectedProperty);
        } else {
            console.warn("Property not found in list:", id);
        }
        
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
            
            // Nếu không có gì thì ẩn thông tin chủ sở hữu
            if (!searchTerm) {
                showOwnerInfo(null);
                return;
            }
            
            // Kiểm tra nếu đã có property ID được chọn
            if ($propertyID.val()) {
                console.log('Property already selected, ID:', $propertyID.val());
                
                // Đảm bảo owner info hiển thị
                if ($ownerInfo.is(':hidden') && window.propertyList) {
                    const selectedProperty = window.propertyList.find(p => String(p.id) === String($propertyID.val()));
                    if (selectedProperty) {
                        $ownerName.text(selectedProperty.ownerName || 'Không xác định');
                        $ownerInfo.css('display', 'flex');
                        console.log('Fixed missing owner display for:', selectedProperty.title);
                    }
                }
                return;
            }
            
            // Tìm property dựa vào searchTerm
            const found = window.propertyList ? window.propertyList.find(p => 
                p.title && p.title.toLowerCase() === searchTerm.toLowerCase()
            ) : null;
            
            if (found) {
                console.log('Found matching property on blur:', found);
                $propertyID.val(found.id);
                $propertySearch.val(found.title); // Đảm bảo hiển thị đúng tiêu đề đầy đủ
                $ownerName.text(found.ownerName || 'Không xác định');
                $ownerInfo.css('display', 'flex');
            } else {
                // Nếu không tìm thấy match, clear thông tin
                $propertyID.val('');
                $ownerInfo.hide();
                console.log('No match found for:', searchTerm);
            }
        }, 200);
    });

    // --- GỢI Ý KHÁCH HÀNG (giữ nguyên logic như bạn đang dùng) ---
    $customerSearch.on('input', function() {
        let query = $(this).val().trim();
        let propertyId = $propertyID.val();
        if (!propertyId) {
            $customerList.html(`<li class="dropdown-item text-danger">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Vui lòng chọn bất động sản trước
            </li>`).show();
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
                                       data-phone="${customer.phone || 'Không có SĐT'}">
                                        <div class="customer-name">${customer.name}</div>
                                        <small class="text-muted">
                                            <i class="bi bi-telephone"></i> ${customer.phone || 'Không có SĐT'}
                                        </small>
                                    </a>
                                </li>`;
                        });
                    } else {
                        html = `<li class="dropdown-item">
                            <div>Không có khách hàng phù hợp</div>
                            <small class="text-muted">Thử nhập tên hoặc số điện thoại</small>
                        </li>`;
                    }
                    $customerList.html(html).show();
                },
                error: function() {
                    $customerList.html(`<li class="dropdown-item text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Lỗi tìm kiếm, vui lòng thử lại
                    </li>`).show();
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
