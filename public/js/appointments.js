$(document).ready(function() {
    const $propertySearch = $('#propertySearch');
    const $ownerInfo = $('#ownerInfo');
    const $ownerName = $('#ownerName');
    const $propertyID = $('#propertyID');
    const $propertyList = $('#propertyList');
    const $customerSearch = $('#customerSearch');
    const $customerList = $('#customerList');
    const $ownerSearchInput = $('#ownerSearchInput');
    const $searchOwnerBtn = $('#searchOwnerBtn');
    const $ownerResults = $('#ownerResults');
    const $selectedOwnerInfo = $('#selectedOwnerInfo');
    const $ownerPropertiesContainer = $('#ownerPropertiesContainer');
    const $ownerPropertiesList = $('#ownerPropertiesList');
    
    // Property filter and search elements
    const $propertyOwnerSearch = $('#propertyOwnerSearch');
    const $searchResults = $('#searchResults');
    const $searchResultsList = $('#searchResultsList');
    const $resetSearch = $('#resetSearch');
    const $appointmentFilter = $('#appointmentFilter');

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
        let id = $(this).data('id');
        let name = $(this).data('name');
        let phone = $(this).data('phone');
        $customerSearch.val(name);
        $('#customerId').val(id); // Set customer ID to hidden field
        $('input[name="CustomerPhone"]').val(phone); // auto điền SĐT nếu muốn
        $customerList.hide();
    });

    // --- TÌM KIẾM CHỦ SỞ HỮU ---
    
    // Search for owners
    function searchOwners() {
        const searchTerm = $ownerSearchInput.val().trim();
        
        if (searchTerm.length < 2) {
            $ownerResults.html('').addClass('d-none');
            return;
        }
        
        // Hiển thị loading state
        $ownerResults.html('<div class="list-group-item text-center"><i class="bi bi-hourglass-split me-2"></i>Đang tìm kiếm...</div>').removeClass('d-none');
        
        // Gọi API tìm kiếm chủ sở hữu
        $.ajax({
            url: "/agent/owners/search",
            method: 'GET',
            data: {
                term: searchTerm
            },
            success: function(response) {
                displayOwnerResults(response.owners || []);
            },
            error: function() {
                $ownerResults.html(`
                    <div class="list-group-item text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Lỗi khi tìm kiếm, vui lòng thử lại
                    </div>
                `).removeClass('d-none');
            }
        });
    }

    // Display owner search results
    function displayOwnerResults(owners) {
        if (owners.length === 0) {
            $ownerResults.html(`
                <div class="list-group-item">
                    <i class="bi bi-person-x me-2"></i>
                    Không tìm thấy chủ sở hữu phù hợp
                </div>
            `).removeClass('d-none');
            return;
        }

        let html = '';
        owners.forEach(owner => {
            html += `
                <div class="list-group-item list-group-item-action owner-option" 
                     data-owner-id="${owner.UserID}" 
                     data-owner-name="${owner.Name}"
                     data-owner-email="${owner.Email || ''}"
                     data-owner-phone="${owner.Phone || ''}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${owner.Name}</h6>
                            <small class="text-muted">
                                ${owner.Email ? `<i class="bi bi-envelope me-1"></i>${owner.Email}` : ''}
                                ${owner.Phone ? `<i class="bi bi-telephone ms-2 me-1"></i>${owner.Phone}` : ''}
                            </small>
                        </div>
                        <span class="badge bg-primary">${owner.property_count || 0} BĐS</span>
                    </div>
                </div>
            `;
        });
        
        $ownerResults.html(html).removeClass('d-none');
    }

    // Load owner properties
    function loadOwnerProperties(ownerId, ownerName) {
        // Hiển thị loading state
        $ownerPropertiesList.html(`
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2">Đang tải danh sách bất động sản...</p>
            </div>
        `);
        
        $.ajax({
            url: '/agent/owners/' + ownerId + '/properties',
            method: 'GET',
            success: function(response) {
                displayOwnerProperties(response.properties || [], ownerName);
                showSelectedOwner(ownerName, response.properties?.length || 0);
            },
            error: function() {
                $ownerPropertiesList.html(`
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Không thể tải danh sách bất động sản
                        </div>
                    </div>
                `);
            }
        });
    }

    // Display owner properties
    function displayOwnerProperties(properties, ownerName) {
        if (properties.length === 0) {
            $ownerPropertiesList.html(`
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Chủ sở hữu <strong>${ownerName}</strong> chưa có bất động sản nào.
                    </div>
                </div>
            `);
            return;
        }

        let html = '';
        properties.forEach(property => {
            const images = property.images || [];
            const firstImage = images.length > 0 ? images[0].ImagePath : '/images/default-property.jpg';
            const price = property.Price ? new Intl.NumberFormat('vi-VN').format(property.Price) + ' đ' : 'Giá thỏa thuận';
            
            // Lấy thông tin chi tiết BĐS nếu có
            const area = property.chiTiet?.Area || '';
            const bedrooms = property.chiTiet?.Bedrooms || property.chiTiet?.Bedroom || '';
            const bathrooms = property.chiTiet?.Bathrooms || property.chiTiet?.Bath_WC || '';
            
            html += `
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card property-card h-100">
                        <div class="position-relative">
                            <img src="${firstImage}" class="card-img-top property-image" alt="${property.Title}" 
                                 onerror="this.src='/images/default-property.jpg'">
                            <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                                ${property.danhMuc?.Protype_Name || property.categoryName || 'Khác'}
                            </span>
                            ${property.Status === 'Đã bán' ? '<span class="badge bg-danger position-absolute top-0 start-0 m-2">Đã bán</span>' : ''}
                            <div class="position-absolute bottom-0 start-0 w-100 p-2" 
                                 style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                                <h6 class="text-white mb-0">${price}</h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">${property.Title}</h5>
                            <p class="card-text text-muted small">
                                <i class="bi bi-geo-alt me-1"></i>
                                ${property.Address || ''}
                                ${property.Ward ? ', ' + property.Ward : ''}
                                ${property.District ? ', ' + property.District : ''}
                            </p>
                            <div class="property-details d-flex justify-content-between mt-3">
                                ${area ? `<span class="text-muted"><i class="bi bi-rulers me-1"></i>${area} m²</span>` : ''}
                                ${bedrooms ? `<span class="text-muted"><i class="bi bi-door-open me-1"></i>${bedrooms} PN</span>` : ''}
                                ${bathrooms ? `<span class="text-muted"><i class="bi bi-droplet me-1"></i>${bathrooms} WC</span>` : ''}
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-outline-primary btn-sm view-property-btn" 
                                        data-property-id="${property.PropertyID}">
                                    <i class="bi bi-eye me-1"></i>Chi tiết
                                </button>
                                <button class="btn btn-primary btn-sm create-appointment-btn" 
                                        data-property-id="${property.PropertyID}"
                                        data-property-title="${property.Title}"
                                        data-owner-id="${property.OwnerID}"
                                        data-owner-name="${ownerName}">
                                    <i class="bi bi-calendar-plus me-1"></i>Tạo lịch hẹn
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $ownerPropertiesList.html(html);
    }

    // Show selected owner info
    function showSelectedOwner(ownerName, propertyCount) {
        $('#selectedOwnerName').text(ownerName);
        $('#propertyCountBadge').text(`${propertyCount} bất động sản`);
        $selectedOwnerInfo.removeClass('d-none');
    }

    // Owner search events
    $searchOwnerBtn.on('click', function() {
        searchOwners();
    });

    $ownerSearchInput.on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            searchOwners();
        }
    });

    $ownerSearchInput.on('input', function() {
        const searchTerm = $(this).val().trim();
        if (searchTerm.length === 0) {
            $ownerResults.addClass('d-none').empty();
            $selectedOwnerInfo.addClass('d-none');
            $ownerPropertiesList.empty();
        }
    });

    // Select owner from results
    $(document).on('click', '.owner-option', function() {
        const ownerId = $(this).data('owner-id');
        const ownerName = $(this).data('owner-name');
        
        $ownerSearchInput.val(ownerName);
        $ownerResults.addClass('d-none');
        
        loadOwnerProperties(ownerId, ownerName);
    });

    // Create appointment from property card
    $(document).on('click', '.create-appointment-btn', function() {
        const propertyId = $(this).data('property-id');
        const propertyTitle = $(this).data('property-title');
        const ownerName = $(this).data('owner-name');
        
        // Fill the create appointment modal with property info
        $propertySearch.val(propertyTitle);
        $propertyID.val(propertyId);
        $ownerName.text(ownerName);
        $ownerInfo.css('display', 'flex');
        
        // Reset customer fields
        $customerSearch.val('');
        $('#customerId').val('');
        
        // Show the create appointment modal
        $('#createAppointmentModal').modal('show');
        
        // Auto-set start date to tomorrow at 9 AM and end date to one hour later
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(9, 0, 0);
        
        const endTime = new Date(tomorrow);
        endTime.setHours(10, 0, 0);
        
        // Format dates for datetime-local inputs
        const tomorrowFormatted = tomorrow.toISOString().slice(0, 16);
        const endTimeFormatted = endTime.toISOString().slice(0, 16);
        
        $('#appointmentDateStart').val(tomorrowFormatted);
        $('#appointmentDateEnd').val(endTimeFormatted);
    });

    // View property details
    $(document).on('click', '.view-property-btn', function() {
        const propertyId = $(this).data('property-id');
        // Redirect to property detail page
        window.open(`/properties/${propertyId}`, '_blank');
    });

    // Function to filter appointments by property (retained for compatibility)
    function handlePropertyAppointmentView(propertyId, propertyTitle) {
        if (!propertyId) return;
        
        // Filter appointments by property
        filterAppointmentsByProperty(propertyId);
        
        // Update search box text if title is provided
        if (propertyTitle) {
            $propertyOwnerSearch.val(propertyTitle);
        }
        
        // Ensure the "All Appointments" tab is active
        $('a[href="#all-appointments"]').tab('show');
        
        // Scroll to the appointment section with smooth animation
        $('html, body').animate({
            scrollTop: $('#all-appointments').offset().top - 100
        }, 500);
    }

    // Hide results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#ownerSearchInput, #ownerResults').length) {
            $ownerResults.addClass('d-none');
        }
    });
    
    // Form submission validation
    $('#appointmentForm').on('submit', function(e) {
        const propertyID = $('#propertyID').val();
        const customerID = $('#customerId').val();
        const startDate = $('#appointmentDateStart').val();
        const endDate = $('#appointmentDateEnd').val();
        const title = $('#appointmentTitle').val();
        
        if (!propertyID) {
            e.preventDefault();
            alert('Vui lòng chọn bất động sản trước khi tạo lịch hẹn');
            return false;
        }
        
        if (!customerID) {
            e.preventDefault();
            alert('Vui lòng chọn khách hàng trước khi tạo lịch hẹn');
            return false;
        }
        
        if (!startDate || !endDate) {
            e.preventDefault();
            alert('Vui lòng chọn thời gian bắt đầu và kết thúc cho lịch hẹn');
            return false;
        }
        
        if (new Date(startDate) >= new Date(endDate)) {
            e.preventDefault();
            alert('Thời gian kết thúc phải sau thời gian bắt đầu');
            return false;
        }
        
        if (!title.trim()) {
            e.preventDefault();
            alert('Vui lòng nhập tiêu đề cho lịch hẹn');
            return false;
        }
        
        return true;
    });
    
    // --- Property search and filtering ---
    $propertyOwnerSearch.on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        
        if (searchTerm.length < 2) {
            $searchResults.hide();
            return;
        }
        
        // Filter properties based on search term
        const filteredProperties = window.propertyList.filter(p => 
            (p.title && p.title.toLowerCase().includes(searchTerm)) || 
            (p.ownerName && p.ownerName.toLowerCase().includes(searchTerm)) ||
            (p.address && p.address.toLowerCase().includes(searchTerm)) ||
            (p.district && p.district.toLowerCase().includes(searchTerm)) ||
            (p.ward && p.ward.toLowerCase().includes(searchTerm))
        );
        
        displaySearchResults(filteredProperties, searchTerm);
    });
    
    // Function to display search results with improved formatting
    function displaySearchResults(results, searchTerm) {
        if (results.length === 0) {
            $searchResultsList.html(`
                <li class="search-result-item">
                    <div class="text-center py-3">
                        <i class="bi bi-search text-muted mb-2" style="font-size: 1.5rem;"></i>
                        <p class="mb-0">Không tìm thấy kết quả cho "${searchTerm}"</p>
                    </div>
                </li>
            `);
            $searchResults.show();
            return;
        }
        
        let html = '';
        results.forEach(item => {
            const ownerName = item.ownerName || 'Không xác định';
            const address = item.address ? `${item.address}${item.ward ? ', ' + item.ward : ''}${item.district ? ', ' + item.district : ''}` : 'Không có địa chỉ';
            
            html += `
                <li class="search-result-item" data-property-id="${item.id}" data-owner-id="${item.ownerId}">
                    <div class="result-property-title">${item.title}</div>
                    <div class="result-owner-name">
                        <i class="bi bi-person"></i> Chủ sở hữu: ${ownerName}
                    </div>
                    <div class="small text-muted">
                        <i class="bi bi-geo-alt"></i> ${address}
                    </div>
                </li>
            `;
        });
        
        $searchResultsList.html(html);
        $searchResults.show();
    }
    
    // Handle search result click with improved behavior
    $(document).on('click', '.search-result-item', function() {
        const propertyId = $(this).data('property-id');
        if (!propertyId) return;
        
        // Filter appointments by property
        filterAppointmentsByProperty(propertyId);
        
        // Hide search results
        $searchResults.hide();
        
        // Update search box text
        const propertyTitle = $(this).find('.result-property-title').text().trim();
        $propertyOwnerSearch.val(propertyTitle);
        
        // Scroll to appointments section
        $('html, body').animate({
            scrollTop: $('#all-appointments').offset().top - 100
        }, 500);
        
        // Ensure the "All Appointments" tab is active
        $('a[href="#all-appointments"]').tab('show');
    });
    
    // Reset search with animation
    $resetSearch.on('click', function() {
        $propertyOwnerSearch.val('');
        $searchResults.hide();
        
        // Show all appointments with fade effect
        $('.empty-filtered').hide();
        $('.appointment-card').fadeIn(300);
    });
    
    // Function to filter appointments by property with improved empty state handling
    function filterAppointmentsByProperty(propertyId) {
        let foundAny = false;
        
        $('.appointment-card').each(function() {
            const cardPropertyId = $(this).data('property-id');
            if (propertyId && cardPropertyId == propertyId) {
                $(this).fadeIn(300);
                foundAny = true;
            } else {
                $(this).hide();
            }
        });
        
        // Show empty state message if no appointments found
        const $tabPane = $('.appointment-card').first().closest('.tab-pane');
        const $emptyMessage = $tabPane.find('.empty-filtered');
        
        if (!foundAny) {
            if ($emptyMessage.length === 0) {
                $tabPane.append(`
                    <div class="empty-filtered p-4 text-center text-muted">
                        <i class="bi bi-calendar-x mb-2" style="font-size: 2rem;"></i>
                        <p>Không có lịch hẹn nào cho bất động sản này</p>
                        <button class="btn btn-sm btn-outline-primary mt-2 reset-filter">
                            <i class="bi bi-arrow-counterclockwise"></i> Hiển thị tất cả
                        </button>
                    </div>
                `);
                
                // Add event handler for the reset filter button
                $('.reset-filter').on('click', function() {
                    $resetSearch.click();
                });
            } else {
                $emptyMessage.show();
            }
        } else {
            $('.empty-filtered').hide();
        }
    }
});
