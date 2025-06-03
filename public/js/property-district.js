/**
 * Property District Display Handler
 * Xử lý việc hiển thị bất động sản theo quận/huyện
 */

document.addEventListener('DOMContentLoaded', function() {
    // Lấy các elements
    const districtCards = document.querySelectorAll('.district-card');
    const initialInstructions = document.getElementById('initialInstructions');
    const propertyDetailContent = document.getElementById('propertyDetailContent');
    const selectedDistrictName = document.getElementById('selectedDistrictName');
    const selectedDistrictProvince = document.getElementById('selectedDistrictProvince');
    const propertyTypeCount = document.getElementById('propertyTypeCount');
    const propertyCards = document.getElementById('propertyCards');
    const propertyCardTemplate = document.getElementById('propertyCardTemplate');
    
    // Không hiển thị thống kê khi trang mới tải
    // showAllPropertiesStatistics(); - Đã bị tắt để ban đầu không hiển thị thông tin    // Thêm event listeners cho các district cards
    districtCards.forEach(card => {
        card.addEventListener('click', function() {
            const district = this.dataset.district;
            showDistrictProperties(district);
            
            // Thêm active class cho card được chọn
            districtCards.forEach(c => c.classList.remove('border-primary'));
            this.classList.add('border-primary');
        });
    });
    
    // Hiển thị properties của district được chọn
    function showDistrictProperties(district) {
        // Ẩn instructions, hiển thị content
        initialInstructions.classList.add('d-none');
        propertyDetailContent.classList.remove('d-none');
          // Giữ ẩn badge tổng số BĐS theo yêu cầu
        const totalBadge = document.getElementById('totalBadge');
        if (totalBadge) {
            totalBadge.style.display = 'none';
        }
          
        // Cập nhật header
        selectedDistrictName.textContent = district;
        
        // Cập nhật tên quận/huyện trong phần thống kê
        const statisticsDistrictElement = document.getElementById('statisticsDistrict');
        if (statisticsDistrictElement) {
            statisticsDistrictElement.style.display = '';
            statisticsDistrictElement.textContent = district;
        }
        
        // Lấy properties của quận đã chọn
        const properties = window.propertyData[district];
        if (!properties || properties.length === 0) return;
        
        // Cập nhật province
        selectedDistrictProvince.textContent = properties[0].province;
        
        // Đếm số lượng BĐS bán/thuê
        const saleCount = properties.filter(p => p.type === 'Sale').length;
        const rentCount = properties.filter(p => p.type === 'Rent').length;
        const totalCount = properties.length;
        
        // Tính giá trung bình
        let totalPrice = 0;
        let validPriceCount = 0;
        
        // Tính giá trung bình riêng cho BĐS bán và BĐS thuê
        let saleTotalPrice = 0;
        let rentTotalPrice = 0;
        let saleValidCount = 0;
        let rentValidCount = 0;
          properties.forEach(property => {
            try {
                // Sử dụng giá trị thô nếu có
                const price = property.rawPrice ? Number(property.rawPrice) : 0;
                
                if (price > 0) {
                    totalPrice += price;
                    validPriceCount++;
                    
                    if (property.type === 'Sale') {
                        saleTotalPrice += price;
                        saleValidCount++;
                    } else if (property.type === 'Rent') {
                        rentTotalPrice += price;
                        rentValidCount++;
                    }
                }
            } catch (e) {
                console.error('Lỗi khi xử lý giá', e);
            }
        });
          // Tính giá trung bình chung và riêng cho từng loại
        const avgPrice = validPriceCount > 0 ? Math.round(totalPrice / validPriceCount) : 0;
        const avgSalePrice = saleValidCount > 0 ? Math.round(saleTotalPrice / saleValidCount) : 0;
        const avgRentPrice = rentValidCount > 0 ? Math.round(rentTotalPrice / rentValidCount) : 0;
        
        // Cập nhật header và thống kê
        propertyTypeCount.textContent = `${saleCount} BĐS bán, ${rentCount} BĐS thuê`;
        
        // Cập nhật thống kê tổng quan
        document.getElementById('saleCount').textContent = saleCount;
        document.getElementById('rentCount').textContent = rentCount;
        document.getElementById('totalCount').textContent = totalCount;
        
        // Định dạng giá trung bình
        const formattedAvgPrice = new Intl.NumberFormat('vi-VN').format(avgPrice);
        document.getElementById('avgPrice').textContent = formattedAvgPrice;
        
        // Cập nhật thông tin tooltip chi tiết về giá nếu có
        const avgPriceElement = document.getElementById('avgPrice');
        if (avgPriceElement) {
            // Định dạng giá trung bình cho từng loại để hiển thị trong tooltip
            const formattedAvgSalePrice = saleValidCount > 0 ? new Intl.NumberFormat('vi-VN').format(avgSalePrice) : 'N/A';
            const formattedAvgRentPrice = rentValidCount > 0 ? new Intl.NumberFormat('vi-VN').format(avgRentPrice) : 'N/A';
            
            // Tạo nội dung tooltip
            avgPriceElement.setAttribute('data-bs-toggle', 'tooltip');
            avgPriceElement.setAttribute('data-bs-placement', 'top');
            avgPriceElement.setAttribute('title', `BĐS Bán: ${formattedAvgSalePrice} | BĐS Thuê: ${formattedAvgRentPrice}`);
            
            // Khởi tạo tooltip (cần Bootstrap JS)
            try {
                new bootstrap.Tooltip(avgPriceElement);
            } catch (e) {
                console.log('Bootstrap tooltip không khả dụng', e);
            }
        }
        
        // Xóa tất cả property cards hiện tại
        propertyCards.innerHTML = '';
        
        // Thêm các property cards mới
        properties.forEach(property => {
            const propertyCard = createPropertyCard(property);
            propertyCards.appendChild(propertyCard);
        });
    }

    // Tạo property card từ template
    function createPropertyCard(property) {
        const template = propertyCardTemplate.content.cloneNode(true);
        const card = template.querySelector('.property-card');
        
        // Cập nhật data-district
        card.dataset.district = property.district;
        
        // Cập nhật nội dung
        card.querySelector('.property-title').textContent = property.title;
        
        // Badge loại BĐS
        const typeBadge = card.querySelector('.property-type-badge');
        typeBadge.textContent = property.typeName;
        typeBadge.classList.add(property.typeClass);
        
        card.querySelector('.property-address').textContent = property.address;
        card.querySelector('.property-price').innerHTML = property.price;
        card.querySelector('.property-category').textContent = property.category;
        card.querySelector('.property-date').textContent = 'Ngày đăng: ' + property.date;
        
        // Chủ sở hữu (có thể null)
        if (property.owner) {
            card.querySelector('.property-owner').textContent = 'Chủ sở hữu: ' + property.owner;
        } else {
            card.querySelector('.property-owner-container').classList.add('d-none');
        }
          // Mô tả (có thể rỗng)
        if (property.description) {
            card.querySelector('.property-description').textContent = property.description;
        } else {
            card.querySelector('.property-description').classList.add('d-none');
        }
        
        return card;
    }
    
    // Hiển thị thống kê tổng quan cho tất cả các bất động sản
    function showAllPropertiesStatistics() {
        // Tổng hợp tất cả các properties từ tất cả các quận
        let allProperties = [];
        for (const district in window.propertyData) {
            if (window.propertyData.hasOwnProperty(district)) {
                allProperties = allProperties.concat(window.propertyData[district]);
            }
        }
        
        // Đếm số lượng BĐS bán/thuê
        const saleCount = allProperties.filter(p => p.type === 'Sale').length;
        const rentCount = allProperties.filter(p => p.type === 'Rent').length;
        const totalCount = allProperties.length;
        
        // Tính giá trung bình
        let totalPrice = 0;
        let validPriceCount = 0;
        
        // Tính giá trung bình riêng cho BĐS bán và BĐS thuê
        let saleTotalPrice = 0;
        let rentTotalPrice = 0;
        let saleValidCount = 0;
        let rentValidCount = 0;
        
        allProperties.forEach(property => {
            try {
                // Sử dụng giá trị thô nếu có
                const price = property.rawPrice ? Number(property.rawPrice) : 0;
                
                if (price > 0) {
                    totalPrice += price;
                    validPriceCount++;
                    
                    if (property.type === 'Sale') {
                        saleTotalPrice += price;
                        saleValidCount++;
                    } else if (property.type === 'Rent') {
                        rentTotalPrice += price;
                        rentValidCount++;
                    }
                }
            } catch (e) {
                console.error('Lỗi khi xử lý giá', e);
            }
        });
        
        // Tính giá trung bình chung và riêng cho từng loại
        const avgPrice = validPriceCount > 0 ? Math.round(totalPrice / validPriceCount) : 0;
        const avgSalePrice = saleValidCount > 0 ? Math.round(saleTotalPrice / saleValidCount) : 0;
        const avgRentPrice = rentValidCount > 0 ? Math.round(rentTotalPrice / rentValidCount) : 0;
        
        // Cập nhật thống kê tổng quan
        document.getElementById('saleCount').textContent = saleCount;
        document.getElementById('rentCount').textContent = rentCount;
        document.getElementById('totalCount').textContent = totalCount;
        
        // Định dạng giá trung bình
        const formattedAvgPrice = new Intl.NumberFormat('vi-VN').format(avgPrice);
        document.getElementById('avgPrice').textContent = formattedAvgPrice;
        
        // Cập nhật tên thống kê
        const statisticsDistrictElement = document.getElementById('statisticsDistrict');
        if (statisticsDistrictElement) {
            statisticsDistrictElement.textContent = 'Tất cả khu vực';
        }
        
        // Cập nhật thông tin tooltip chi tiết về giá
        const avgPriceElement = document.getElementById('avgPrice');
        if (avgPriceElement) {
            // Định dạng giá trung bình cho từng loại để hiển thị trong tooltip
            const formattedAvgSalePrice = saleValidCount > 0 ? new Intl.NumberFormat('vi-VN').format(avgSalePrice) : 'N/A';
            const formattedAvgRentPrice = rentValidCount > 0 ? new Intl.NumberFormat('vi-VN').format(avgRentPrice) : 'N/A';
            
            // Tạo nội dung tooltip
            avgPriceElement.setAttribute('data-bs-toggle', 'tooltip');
            avgPriceElement.setAttribute('data-bs-placement', 'top');
            avgPriceElement.setAttribute('title', `BĐS Bán: ${formattedAvgSalePrice} | BĐS Thuê: ${formattedAvgRentPrice}`);
            
            // Khởi tạo tooltip (cần Bootstrap JS)
            try {
                new bootstrap.Tooltip(avgPriceElement);
            } catch (e) {
                console.log('Bootstrap tooltip không khả dụng', e);
            }
        }
    }
});
