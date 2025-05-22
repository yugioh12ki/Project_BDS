// Property Search and Filter
document.addEventListener('DOMContentLoaded', function() {
    // Property List Tab
    const searchInput = document.getElementById('propertySearch');
    const typeFilter = document.getElementById('propertyTypeFilter');
    const statusFilter = document.getElementById('propertyStatusFilter');
    const propertyItems = document.querySelectorAll('.property-item');
    
    // Property Posts Tab
    const propertyPostSearch = document.getElementById('propertyPostSearch');
    const allPostsTab = document.getElementById('allPostsTab');
    const approvedPostsTab = document.getElementById('approvedPostsTab');
    const pendingPostsTab = document.getElementById('pendingPostsTab');
    const sellingPostsTab = document.getElementById('sellingPostsTab');
    const canceledPostsTab = document.getElementById('canceledPostsTab');
    const addNewPostBtn = document.getElementById('addNewPostBtn');
    
    const filterProperties = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const typeValue = typeFilter.value;
        const statusValue = statusFilter.value;
        
        propertyItems.forEach(item => {
            const title = item.dataset.title.toLowerCase();
            const address = item.dataset.address.toLowerCase();
            const type = item.dataset.type;
            const status = item.dataset.status;
            
            const matchesSearch = title.includes(searchTerm) || address.includes(searchTerm);
            const matchesType = typeValue === 'all' || type === typeValue;
            const matchesStatus = statusValue === 'all' || status === statusValue;
            
            if (matchesSearch && matchesType && matchesStatus) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    };
    
    if (searchInput) searchInput.addEventListener('input', filterProperties);
    if (typeFilter) typeFilter.addEventListener('change', filterProperties);
    if (statusFilter) statusFilter.addEventListener('change', filterProperties);
    
    // Xử lý chỉnh sửa bất động sản
    const editButtons = document.querySelectorAll('.edit-property');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // No need to prevent default since we're using data-bs-toggle and data-bs-target
            const propertyId = this.dataset.propertyId;
            
            // Gọi API để lấy thông tin bất động sản
            fetch(`/api/properties/${propertyId}`)
                .then(response => response.json())
                .then(data => {
                    // Mock data cho demo - trong thực tế, sẽ lấy từ response API
                    const mockData = {
                        PropertyID: propertyId,
                        Title: 'Căn hộ cao cấp, Trung tâm',
                        Address: '123 Nguyễn Huệ, Quận 1, TP.HCM',
                        Ward: 'Quận 1',
                        District: 'Quận 1',
                        Province: 'TP.HCM',
                        TypePro: 'Sale',
                        PropertyType: 1,
                        Price: 5200000000,
                        Description: 'Căn hộ cao cấp tại trung tâm thành phố với đầy đủ tiện nghi, view đẹp, an ninh 24/7.',
                        PostedDate: '2023-05-15',
                        chiTiet: {
                            Area: 85,
                            Bedroom: 2,
                            Bath_WC: 2,
                            Floor: 2,
                            legal: 'Sổ đỏ / sổ hồng',
                            Interior: 'đầy đủ',
                            WaterPrice: 30000,
                            PowerPrice: 3500,
                            Utilities: 'Có phí dịch vụ',
                            view: 'Sông Sài Gòn',
                            near: 'Công viên, trường học',
                            Road: 'Đường 12m'
                        },
                        danhMuc: {
                            ten_pro: 'Căn hộ'
                        },
                        images: [
                            {ImageURL: 'https://via.placeholder.com/600x400', IsThumbnail: 1}
                        ]
                    };
                    
                    // Điền thông tin vào form
                    document.getElementById('edit_property_id').value = mockData.PropertyID;
                    document.getElementById('edit_title').value = mockData.Title;
                    document.getElementById('edit_address').value = mockData.Address;
                    document.getElementById('edit_district').value = mockData.District;
                    document.getElementById('edit_ward').value = mockData.Ward;
                    document.getElementById('edit_province').value = mockData.Province;
                    document.getElementById('edit_type_pro').value = mockData.TypePro;
                    document.getElementById('edit_property_type').value = mockData.PropertyType;
                    document.getElementById('edit_price').value = mockData.Price;
                    document.getElementById('edit_description').value = mockData.Description;
                    
                    // Thông tin chi tiết
                    if (mockData.chiTiet) {
                        document.getElementById('edit_area').value = mockData.chiTiet.Area;
                        document.getElementById('edit_bedroom').value = mockData.chiTiet.Bedroom;
                        document.getElementById('edit_bathroom').value = mockData.chiTiet.Bath_WC;
                        document.getElementById('edit_floor').value = mockData.chiTiet.Floor;
                        document.getElementById('edit_legal').value = mockData.chiTiet.legal;
                        document.getElementById('edit_interior').value = mockData.chiTiet.Interior;
                        
                        // Load utility prices and additional fields
                        if (document.getElementById('edit_water_price'))
                            document.getElementById('edit_water_price').value = mockData.chiTiet.WaterPrice || '';
                        if (document.getElementById('edit_power_price'))
                            document.getElementById('edit_power_price').value = mockData.chiTiet.PowerPrice || '';
                        if (document.getElementById('edit_utilities'))
                            document.getElementById('edit_utilities').value = mockData.chiTiet.Utilities || '';
                        if (document.getElementById('edit_view'))
                            document.getElementById('edit_view').value = mockData.chiTiet.view || '';
                        if (document.getElementById('edit_near'))
                            document.getElementById('edit_near').value = mockData.chiTiet.near || '';
                        if (document.getElementById('edit_road'))
                            document.getElementById('edit_road').value = mockData.chiTiet.Road || '';
                    }
                    
                    // Handle legal documents
                    if (mockData.chiTiet && mockData.chiTiet.legal) {
                        const legalDocs = mockData.chiTiet.legal.split('/').map(doc => doc.trim());
                        
                        // Check the corresponding checkboxes
                        if (legalDocs.includes('Sổ hồng') && document.getElementById('edit_so_hong'))
                            document.getElementById('edit_so_hong').checked = true;
                        if (legalDocs.includes('Sổ đỏ') && document.getElementById('edit_so_do'))
                            document.getElementById('edit_so_do').checked = true;
                        if (legalDocs.includes('Hợp đồng mua bán') && document.getElementById('edit_hop_dong_mua_ban'))
                            document.getElementById('edit_hop_dong_mua_ban').checked = true;
                        // Add other document types as needed
                    }
                    
                    // Hiển thị hình ảnh
                    const imagesContainer = document.getElementById('property_images_container');
                    imagesContainer.innerHTML = '';
                    
                    if (mockData.images && mockData.images.length > 0) {
                        mockData.images.forEach(image => {
                            const imgEl = document.createElement('div');
                            imgEl.className = 'position-relative';
                            imgEl.innerHTML = `
                                <img src="${image.ImageURL}" alt="Property image" style="width: 120px; height: 90px; object-fit: cover; border-radius: 4px;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle" style="width: 24px; height: 24px; padding: 0; line-height: 24px;">
                                    <i class="bi bi-x"></i>
                                </button>
                                ${image.IsThumbnail ? '<div class="position-absolute bottom-0 start-0 bg-primary text-white px-2 py-1 small rounded-end">Ảnh bìa</div>' : ''}
                            `;
                            imagesContainer.appendChild(imgEl);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });
    
    // Xử lý xem chi tiết bất động sản
    const viewDetailButtons = document.querySelectorAll('[data-bs-target="#viewPropertyModal"]');
    const viewPropertyModal = new bootstrap.Modal(document.getElementById('viewPropertyModal'));
    
    viewDetailButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const propertyId = this.dataset.propertyId;
            
            // Gọi API để lấy thông tin bất động sản
            fetch(`/api/properties/${propertyId}`)
                .then(response => response.json())
                .then(data => {
                    // Mock data cho demo - trong thực tế, sẽ lấy từ response API
                    const mockData = {
                        PropertyID: propertyId,
                        Title: 'Căn hộ cao cấp, Trung tâm',
                        Address: '123 Nguyễn Huệ, Quận 1, TP.HCM',
                        Ward: 'Quận 1',
                        District: 'Quận 1',
                        Province: 'TP.HCM',
                        TypePro: 'Sale',
                        PropertyType: 1,
                        Price: 5200000000,
                        Description: 'Căn hộ cao cấp tại trung tâm thành phố với đầy đủ tiện nghi, view đẹp, an ninh 24/7.',
                        PostedDate: '2023-05-15',
                        chiTiet: {
                            Area: 85,
                            Bedroom: 2,
                            Bath_WC: 2,
                            Floor: 2,
                            legal: 'Sổ đỏ / sổ hồng',
                            Interior: 'đầy đủ',
                            WaterPrice: 30000,
                            PowerPrice: 3500,
                            Utilities: 'Có phí dịch vụ',
                            view: 'Sông Sài Gòn',
                            near: 'Công viên, trường học',
                            Road: 'Đường 12m'
                        },
                        danhMuc: {
                            ten_pro: 'Căn hộ'
                        },
                        images: [
                            {ImageURL: 'https://via.placeholder.com/600x400', IsThumbnail: 1}
                        ]
                    };
                    
                    // Điền thông tin vào modal xem chi tiết
                    document.getElementById('view_property_id').value = mockData.PropertyID;
                    document.getElementById('view_property_title').textContent = mockData.Title;
                    document.getElementById('view_property_address').textContent = `${mockData.Address}, ${mockData.Ward}, ${mockData.District}, ${mockData.Province}`;
                    
                    // Thiết lập hình ảnh
                    if (mockData.images && mockData.images.length > 0) {
                        const thumbnail = mockData.images.find(img => img.IsThumbnail) || mockData.images[0];
                        document.getElementById('view_property_image').src = thumbnail.ImageURL;
                    } else {
                        document.getElementById('view_property_image').src = 'https://via.placeholder.com/600x400?text=No+Image';
                    }
                    
                    // Thiết lập badge trạng thái
                    const statusBadge = document.getElementById('view_property_status');
                    if (mockData.TypePro === 'Sale') {
                        statusBadge.className = 'badge rounded-pill py-2 px-3 fw-normal bg-danger';
                        statusBadge.innerHTML = '<i class="bi bi-tag me-1"></i> Đang bán';
                    } else if (mockData.TypePro === 'Rent') {
                        statusBadge.className = 'badge rounded-pill py-2 px-3 fw-normal bg-primary';
                        statusBadge.innerHTML = '<i class="bi bi-key me-1"></i> Đang cho thuê';
                    } else if (mockData.TypePro === 'Sold') {
                        statusBadge.className = 'badge rounded-pill py-2 px-3 fw-normal bg-success';
                        statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i> Đã bán';
                    } else if (mockData.TypePro === 'Rented') {
                        statusBadge.className = 'badge rounded-pill py-2 px-3 fw-normal bg-info';
                        statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i> Đã cho thuê';
                    }
                    
                    // Thông tin cơ bản
                    document.getElementById('view_property_type').textContent = mockData.danhMuc ? mockData.danhMuc.ten_pro : 'Bất động sản';
                    document.getElementById('view_property_area').textContent = mockData.chiTiet.Area ? `${mockData.chiTiet.Area} m²` : 'N/A';
                    document.getElementById('view_property_bedroom').textContent = mockData.chiTiet.Bedroom || '0';
                    document.getElementById('view_property_bathroom').textContent = mockData.chiTiet.Bath_WC || '0';
                    
                    // Giá bán và giá thuê
                    if (mockData.TypePro === 'Sale' || mockData.TypePro === 'Sold') {
                        document.getElementById('view_property_price_sale').textContent = `${(mockData.Price / 1000000000).toFixed(1)} tỷ VNĐ`;
                        document.getElementById('view_property_price_rent').textContent = 'N/A';
                    } else if (mockData.TypePro === 'Rent' || mockData.TypePro === 'Rented') {
                        document.getElementById('view_property_price_sale').textContent = 'N/A';
                        document.getElementById('view_property_price_rent').textContent = `${(mockData.Price / 1000000)} triệu VNĐ/tháng`;
                    } else {
                        document.getElementById('view_property_price_sale').textContent = `${(mockData.Price / 1000000000).toFixed(1)} tỷ VNĐ`;
                        document.getElementById('view_property_price_rent').textContent = 'Liên hệ';
                    }
                    
                    // Thông tin chi tiết
                    document.getElementById('view_property_description').textContent = mockData.Description;
                    document.getElementById('view_property_floor').textContent = mockData.chiTiet.Floor || 'N/A';
                    document.getElementById('view_property_interior').textContent = mockData.chiTiet.Interior || 'N/A';
                    document.getElementById('view_property_view').textContent = mockData.chiTiet.view || 'N/A';
                    document.getElementById('view_property_road').textContent = mockData.chiTiet.Road || 'N/A';
                    document.getElementById('view_property_posted_date').textContent = new Date(mockData.PostedDate).toLocaleDateString('vi-VN');
                    document.getElementById('view_property_status_text').textContent = getStatusText(mockData.TypePro);
                    
                    // Phí dịch vụ (chỉ hiển thị cho thuê)
                    const utilityPricesSection = document.getElementById('utility_prices_section');
                    if (mockData.TypePro === 'Rent' || mockData.TypePro === 'Rented') {
                        utilityPricesSection.style.display = 'block';
                        document.getElementById('view_property_water_price').textContent = mockData.chiTiet.WaterPrice ? 
                            isNaN(mockData.chiTiet.WaterPrice) ? mockData.chiTiet.WaterPrice : `${Number(mockData.chiTiet.WaterPrice).toLocaleString('vi-VN')} VNĐ` : 'N/A';
                        document.getElementById('view_property_power_price').textContent = mockData.chiTiet.PowerPrice ? 
                            isNaN(mockData.chiTiet.PowerPrice) ? mockData.chiTiet.PowerPrice : `${Number(mockData.chiTiet.PowerPrice).toLocaleString('vi-VN')} VNĐ` : 'N/A';
                        document.getElementById('view_property_utilities').textContent = mockData.chiTiet.Utilities || 'N/A';
                    } else {
                        utilityPricesSection.style.display = 'none';
                    }
                    
                    // Địa điểm lân cận
                    document.getElementById('view_property_near').textContent = mockData.chiTiet.near || 'Không có thông tin';
                    
                    // Giấy tờ pháp lý
                    const legalDocsContainer = document.getElementById('legal_documents');
                    legalDocsContainer.innerHTML = '';
                    
                    if (mockData.chiTiet.legal) {
                        const legalDocs = mockData.chiTiet.legal.split('/').map(doc => doc.trim());
                        legalDocs.forEach(doc => {
                            const docElement = document.createElement('div');
                            docElement.className = 'mb-2';
                            docElement.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                    <span>${doc}</span>
                                </div>
                            `;
                            legalDocsContainer.appendChild(docElement);
                        });
                    } else {
                        legalDocsContainer.innerHTML = '<p class="text-muted">Không có thông tin giấy tờ pháp lý</p>';
                    }
                    
                    // Thiết lập nút chỉnh sửa
                    const editFromViewBtn = document.querySelector('.edit-from-view');
                    editFromViewBtn.dataset.propertyId = mockData.PropertyID;
                    
                    // Thêm sự kiện cho nút chỉnh sửa từ xem chi tiết
                    editFromViewBtn.addEventListener('click', function() {
                        // Đóng modal xem chi tiết
                        viewPropertyModal.hide();
                        
                        // Tìm nút chỉnh sửa tương ứng và kích hoạt
                        const editBtn = document.querySelector(`.edit-property[data-property-id="${this.dataset.propertyId}"]`);
                        if (editBtn) {
                            // Chờ một chút để modal view đóng hoàn toàn trước khi mở modal edit
                            setTimeout(() => {
                                editBtn.click();
                            }, 500);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });
    
    // Hàm helper để hiển thị trạng thái
    function getStatusText(typePro) {
        switch(typePro) {
            case 'Sale': return 'Đang bán';
            case 'Rent': return 'Đang cho thuê';
            case 'Sold': return 'Đã bán';
            case 'Rented': return 'Đã cho thuê';
            default: return typePro;
        }
    }
    
    // Xử lý form submit
    const editPropertyForm = document.getElementById('editPropertyForm');
    if (editPropertyForm) {
        editPropertyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Trong thực tế, gửi dữ liệu qua AJAX
            // const formData = new FormData(this);
            
            // Đóng modal
            const editPropertyModal = bootstrap.Modal.getInstance(document.getElementById('editPropertyModal'));
            editPropertyModal.hide();
            
            // Hiển thị thông báo thành công
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show">
                    Cập nhật thông tin bất động sản thành công!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.querySelector('.property-dashboard').insertAdjacentHTML('afterbegin', alertHtml);
        });
    }
    
    // Xử lý Tin đăng ký gửi bất động sản
    // Lọc đăng tin theo trạng thái
    const filterPostListings = (status = 'all') => {
        const postRows = document.querySelectorAll('#property-posts-content tbody tr');
        const searchTerm = propertyPostSearch ? propertyPostSearch.value.toLowerCase() : '';
        
        postRows.forEach(row => {
            const postTitle = row.querySelector('h6').textContent.toLowerCase();
            const postAddress = row.querySelector('small').textContent.toLowerCase();
            const postStatus = row.querySelector('td:nth-child(5) .badge').textContent.toLowerCase();
            
            // Tìm kiếm phải match với title hoặc địa chỉ
            const matchesSearch = postTitle.includes(searchTerm) || postAddress.includes(searchTerm) || searchTerm === '';
            
            // Lọc theo trạng thái
            let matchesStatus = true;
            if (status !== 'all') {
                switch(status) {
                    case 'approved':
                        matchesStatus = postStatus.includes('đã duyệt');
                        break;
                    case 'pending':
                        matchesStatus = postStatus.includes('chờ duyệt') || postStatus.includes('chờ tiến hành') || postStatus.includes('chờ thanh toán');
                        break;
                    case 'selling':
                        matchesStatus = postStatus.includes('thương lượng');
                        break;
                    case 'canceled':
                        matchesStatus = postStatus.includes('từ chối');
                        break;
                }
            }
            
            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
        });
    };
    
    // Lắng nghe events cho tab filter và search
    if (propertyPostSearch) {
        propertyPostSearch.addEventListener('input', () => filterPostListings('all'));
    }
    
    if (allPostsTab) {
        allPostsTab.addEventListener('click', function(e) {
            e.preventDefault();
            setActivePostTab(this);
            filterPostListings('all');
        });
    }
    
    if (approvedPostsTab) {
        approvedPostsTab.addEventListener('click', function(e) {
            e.preventDefault();
            setActivePostTab(this);
            filterPostListings('approved');
        });
    }
    
    if (pendingPostsTab) {
        pendingPostsTab.addEventListener('click', function(e) {
            e.preventDefault();
            setActivePostTab(this);
            filterPostListings('pending');
        });
    }
    
    if (sellingPostsTab) {
        sellingPostsTab.addEventListener('click', function(e) {
            e.preventDefault();
            setActivePostTab(this);
            filterPostListings('selling');
        });
    }
    
    if (canceledPostsTab) {
        canceledPostsTab.addEventListener('click', function(e) {
            e.preventDefault();
            setActivePostTab(this);
            filterPostListings('canceled');
        });
    }
    
    // Helper function để set active tab
    function setActivePostTab(tab) {
        const postTabs = document.querySelectorAll('#property-posts-content .nav-link');
        postTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
    }
    
    // Xử lý nút thêm tin đăng mới
    if (addNewPostBtn) {
        addNewPostBtn.addEventListener('click', function(e) {
            // Kiểm tra xem modal đã được initialized chưa
            if (!bootstrap.Modal.getInstance(document.getElementById('createPropertyListingModal'))) {
                // Mở modal đã được include trong trang
                const createListingModal = new bootstrap.Modal(document.getElementById('createPropertyListingModal'));
                createListingModal.show();
            }
            
            // Tải dữ liệu cho modal
            fetch(route('owner.property.get-for-listing'))
                .then(response => response.json())
                .then(data => {
                    console.log('Loaded properties for modal:', data);
                })
                .catch(error => {
                    console.error('Error loading properties:', error);
                });
        });
    }
    
    // Xử lý form tạo tin đăng
    const propertyTypeOptions = document.querySelectorAll('.property-type-option');
    if (propertyTypeOptions.length > 0) {
        propertyTypeOptions.forEach(option => {
            option.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
            });
        });
    }
    
    // Nút tiếp tục trong form tạo tin đăng
    const nextStepBtn = document.getElementById('nextStepBtn');
    if (nextStepBtn) {
        nextStepBtn.addEventListener('click', function() {
            // Trong thực tế, sẽ chuyển đến bước tiếp theo
            alert('Chuyển đến bước tiếp theo của quá trình tạo tin đăng');
        });
    }
    
    // Nút xem trước tin đăng
    const previewListingBtn = document.getElementById('previewListingBtn');
    if (previewListingBtn) {
        previewListingBtn.addEventListener('click', function() {
            alert('Xem trước tin đăng');
        });
    }
    
    // Xử lý dropdown provinces/cities/districts
    const propertyCity = document.getElementById('propertyCity');
    const propertyDistrict = document.getElementById('propertyDistrict');
    const propertyWard = document.getElementById('propertyWard');
    
    if (propertyCity) {
        propertyCity.addEventListener('change', function() {
            // Fake data for districts based on selected city
            let districtOptions = '<option selected>Chọn Quận/Huyện</option>';
            
            if (this.value === 'HCM') {
                districtOptions += `
                    <option value="Q1">Quận 1</option>
                    <option value="Q2">Quận 2</option>
                    <option value="Q3">Quận 3</option>
                    <option value="Q7">Quận 7</option>
                    <option value="QBT">Quận Bình Thạnh</option>
                `;
            } else if (this.value === 'HN') {
                districtOptions += `
                    <option value="HK">Hoàn Kiếm</option>
                    <option value="BD">Ba Đình</option>
                    <option value="TX">Thanh Xuân</option>
                `;
            }
            
            if (propertyDistrict) {
                propertyDistrict.innerHTML = districtOptions;
                // Reset ward selection
                if (propertyWard) {
                    propertyWard.innerHTML = '<option selected>Chọn Phường/Xã</option>';
                }
            }
        });
    }
    
    if (propertyDistrict) {
        propertyDistrict.addEventListener('change', function() {
            // Fake data for wards based on selected district
            let wardOptions = '<option selected>Chọn Phường/Xã</option>';
            
            if (this.value === 'Q1') {
                wardOptions += `
                    <option value="P1">Phường Bến Nghé</option>
                    <option value="P2">Phường Bến Thành</option>
                    <option value="P3">Phường Đa Kao</option>
                `;
            } else if (this.value === 'QBT') {
                wardOptions += `
                    <option value="P1">Phường 1</option>
                    <option value="P2">Phường 2</option>
                    <option value="P3">Phường 3</option>
                `;
            }
            
            if (propertyWard) {
                propertyWard.innerHTML = wardOptions;
            }
        });
    }
}); 