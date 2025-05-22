document.addEventListener('DOMContentLoaded', function() {
  const propertyItems = document.querySelectorAll('.property-item');
  const continueBtn = document.getElementById('continueToListingDetailsBtn');
  const propertySelectionList = document.querySelector('.property-selection-list');
  
  // Luôn tải dữ liệu từ API khi mở modal, bất kể có property items ban đầu hay không
  loadPropertiesFromAPI();
  
  function loadPropertiesFromAPI() {
    // Hiển thị loading indicator
    propertySelectionList.innerHTML = `
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2 text-muted">Đang tải danh sách bất động sản...</p>
      </div>
    `;
    
    fetch(route('owner.property.get-for-listing'))
      .then(response => response.json())
      .then(data => {
        console.log('Loaded properties from API:', data);
        
        if (data.length === 0) {
          propertySelectionList.innerHTML = `
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              Bạn chưa có bất động sản nào. Hãy thêm bất động sản trước khi tạo tin đăng.
            </div>
          `;
          return;
        }
        
        // Render properties
        let html = '';
        data.forEach(property => {
          const thumbnailImage = property.images && property.images.length > 0 ? 
            property.images.find(img => img.IsThumbnail == 1) || property.images[0] : null;
          const imageUrl = thumbnailImage ? thumbnailImage.ImageURL : null;
          
          const price = property.TypePro === 'Sale' || property.TypePro === 'Sold' ? 
            `${(property.Price / 1000000000).toFixed(1)} tỷ VND` : 
            `${(property.Price / 1000000).toFixed(0)} triệu VND`;
          
          const propertyType = property.danhMuc ? property.danhMuc.ten_pro : 'Bất động sản';
          const area = property.chiTiet ? property.chiTiet.Area || 'N/A' : 'N/A';
          const bedroom = property.chiTiet ? property.chiTiet.Bedroom || '0' : '0';
          const bathroom = property.chiTiet ? property.chiTiet.Bath_WC || '0' : '0';
          
          const title = property.TypePro === 'Sale' || property.TypePro === 'Sold' ? 
            `Bán ${propertyType} ${property.District}` : 
            `Cho thuê ${propertyType} ${property.District}`;
            
          const status = property.TypePro === 'Sale' || property.TypePro === 'Sold' ? 'Đang bán' : 'Đang cho thuê';
          const statusClass = property.TypePro === 'Sale' || property.TypePro === 'Sold' ? 'bg-danger' : 'bg-primary';
          
          html += `
            <div class="property-item border rounded mb-3 overflow-hidden">
              <div class="property-content p-0">
                <!-- Property Image -->
                <div class="property-image bg-light text-center" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                  ${imageUrl ? 
                    `<img src="${imageUrl}" alt="${property.Title}" style="max-height: 100%; max-width: 100%; object-fit: contain;">` : 
                    `<svg class="text-secondary" width="80" height="80" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                      <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                    </svg>`
                  }
                </div>
                
                <!-- Property Details -->
                <div class="p-3">
                  <h5 class="mb-1 fw-bold">${property.Title || title}</h5>
                  
                  <p class="text-muted mb-2">
                    <i class="bi bi-geo-alt me-1"></i> ${property.Address}, ${property.Ward || ''}, ${property.District || ''}, ${property.Province || ''}
                  </p>
                  
                  <div class="d-flex align-items-center mb-2">
                    <span class="badge rounded-pill me-2" style="background-color: #eef2ff; color: #4f46e5;">
                      ${propertyType}
                    </span>
                    
                    <span class="badge rounded-pill ${statusClass}">
                      ${status}
                    </span>
                  </div>
                  
                  <!-- Property Features -->
                  <div class="d-flex justify-content-between text-center py-3 border-top border-bottom">
                    <div class="px-2">
                      <i class="bi bi-rulers d-block mb-1"></i>
                      <strong class="d-block">${area}</strong>
                      <small class="text-muted">m²</small>
                    </div>
                    <div class="px-2">
                      <i class="bi bi-door-open d-block mb-1"></i>
                      <strong class="d-block">${bedroom}</strong>
                      <small class="text-muted">Phòng ngủ</small>
                    </div>
                    <div class="px-2">
                      <i class="bi bi-droplet d-block mb-1"></i>
                      <strong class="d-block">${bathroom}</strong>
                      <small class="text-muted">Phòng tắm</small>
                    </div>
                  </div>
                  
                  <!-- Price and Select Button -->
                  <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                      <h5 class="fw-bold text-primary mb-0">
                        ${price}${property.TypePro === 'Rent' || property.TypePro === 'Rented' ? '<span class="small">/tháng</span>' : ''}
                      </h5>
                    </div>
                    
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="selectedProperty" id="property${property.PropertyID}" value="${property.PropertyID}" data-type="${property.TypePro}">
                      <label class="form-check-label" for="property${property.PropertyID}">
                        Chọn bất động sản này
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          `;
        });
        
        propertySelectionList.innerHTML = html;
        
        // Rebind events for newly created elements
        bindPropertyEvents();
      })
      .catch(error => {
        console.error('Error loading properties:', error);
        propertySelectionList.innerHTML = `
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Lỗi khi tải dữ liệu bất động sản: ${error.message}. Vui lòng thử lại sau.
          </div>
        `;
      });
  }
  
  function bindPropertyEvents() {
    const allPropertyItems = document.querySelectorAll('.property-item');
    
    // Make entire property item clickable to select the radio button
    allPropertyItems.forEach(item => {
      item.addEventListener('click', function(e) {
        // Don't select if clicking on a link or button inside the item
        if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
          return;
        }
        
        const radio = item.querySelector('input[type="radio"]');
        if (radio) {
          radio.checked = true;
          highlightSelectedProperty();
        }
      });
    });
    
    // Bật nút tiếp tục nếu có bất động sản
    if (allPropertyItems.length > 0 && continueBtn) {
      continueBtn.disabled = false;
    }
  }
  
  // Function to highlight the selected property
  function highlightSelectedProperty() {
    const allPropertyItems = document.querySelectorAll('.property-item');
    
    allPropertyItems.forEach(item => {
      const radio = item.querySelector('input[type="radio"]');
      if (radio && radio.checked) {
        item.classList.add('border-primary');
      } else {
        item.classList.remove('border-primary');
      }
    });
  }
  
  // Continue button handler
  if (continueBtn) {
    // Disable the button initially until a property is selected
    continueBtn.disabled = true;
    
    continueBtn.addEventListener('click', function() {
      const selectedProperty = document.querySelector('input[name="selectedProperty"]:checked');
      
      if (!selectedProperty) {
        alert('Vui lòng chọn một bất động sản để tiếp tục.');
        return;
      }
      
      // Lấy loại bất động sản (bán/thuê) từ data attribute
      const propertyType = selectedProperty.getAttribute('data-type');
      const propertyId = selectedProperty.value;
      
      if (propertyId) {
        // Hiển thị loading state trên nút
        continueBtn.disabled = true;
        continueBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang xử lý...';
        
        // Lưu vào localStorage để sử dụng ở bước tiếp theo
        localStorage.setItem('selectedPropertyId', propertyId);
        localStorage.setItem('selectedPropertyType', propertyType);
        
        // Gửi AJAX request để tạo tin đăng
        fetch(route('owner.property.listings.store'), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: JSON.stringify({
            property_id: propertyId,
            property_type: propertyType
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Chuyển sang bước tiếp theo
            if (propertyType === 'Sale' || propertyType === 'Sold') {
              alert('Tiếp tục với loại tin đăng BÁN cho bất động sản đã chọn');
            } else if (propertyType === 'Rent' || propertyType === 'Rented') {
              alert('Tiếp tục với loại tin đăng CHO THUÊ cho bất động sản đã chọn');
            }
            
            // Trong môi trường thực tế, sẽ điều hướng đến trang tiếp theo hoặc hiển thị bước thứ 2
            window.location.href = data.redirect;
          } else {
            alert('Có lỗi xảy ra: ' + (data.message || 'Không xác định'));
            // Khôi phục trạng thái nút
            continueBtn.disabled = false;
            continueBtn.innerHTML = 'Tiếp tục';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Đã xảy ra lỗi khi gửi dữ liệu.');
          // Khôi phục trạng thái nút
          continueBtn.disabled = false;
          continueBtn.innerHTML = 'Tiếp tục';
        });
      } else {
        alert('Không thể xác định loại bất động sản, vui lòng thử lại.');
      }
    });
  }
  
  // Lắng nghe sự kiện khi modal được mở
  const createPropertyListingModal = document.getElementById('createPropertyListingModal');
  if (createPropertyListingModal) {
    createPropertyListingModal.addEventListener('shown.bs.modal', function () {
      // Tải lại dữ liệu mỗi khi modal được mở
      loadPropertiesFromAPI();
    });
  }
}); 