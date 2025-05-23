document.addEventListener('DOMContentLoaded', function() {
  const propertyItems = document.querySelectorAll('.property-item');
  const continueBtn = document.getElementById('continueToListingDetailsBtn');
  const propertySelectionList = document.querySelector('.property-selection-list');

  // Luôn tải dữ liệu từ API khi mở modal, bất kể có property items ban đầu hay không
  loadPropertiesFromAPI();

  function loadPropertiesFromAPI() {
    // Tìm container để hiển thị danh sách bất động sản
    if (!propertySelectionList) {
      propertySelectionList = document.querySelector('.property-selection-list');

      // Nếu vẫn không tìm thấy, có thể modal chưa được render hoặc class khác
      if (!propertySelectionList) {
        console.error('Không tìm thấy container để hiển thị danh sách bất động sản');
        return;
      }
    }

    // Hiển thị loading indicator
    propertySelectionList.innerHTML = `
      <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2 text-muted">Đang tải danh sách bất động sản...</p>
      </div>
    `;

    // Lấy CSRF token cho các request POST sau này
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

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

        // Tìm form hoặc tạo form nếu chưa có
        let form = propertySelectionList.closest('form');

        // Thêm hidden input cho CSRF nếu chưa có
        if (form && !form.querySelector('input[name="_token"]') && csrfToken) {
          const tokenInput = document.createElement('input');
          tokenInput.type = 'hidden';
          tokenInput.name = '_token';
          tokenInput.value = csrfToken;
          form.appendChild(tokenInput);
        }

        // Render properties
        let html = '';
        data.forEach(property => {
          // Use imageUrl from API response directly
          const imageUrl = property.imageUrl;

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

          // Enable the continue button when a property is selected
          if (continueBtn) {
            continueBtn.disabled = false;
          }
        }
      });
    });

    // Bật nút tiếp tục nếu có bất động sản
    if (allPropertyItems.length > 0 && continueBtn) {
      continueBtn.disabled = false;

      // Check if any property is already selected
      const anySelected = document.querySelector('input[name="selectedProperty"]:checked') ||
                         document.querySelector('input[name="property_id"]:checked');
      if (anySelected) {
        highlightSelectedProperty();
      }
    }

    // Add event listeners to radio buttons
    const radioButtons = document.querySelectorAll('input[name="selectedProperty"], input[name="property_id"]');
    radioButtons.forEach(radio => {
      radio.addEventListener('change', function() {
        highlightSelectedProperty();
        if (continueBtn) {
          continueBtn.disabled = false;
        }
      });
    });
  }

  // Function to highlight the selected property
  function highlightSelectedProperty() {
    const allPropertyItems = document.querySelectorAll('.property-item');

    allPropertyItems.forEach(item => {
      const radio = item.querySelector('input[type="radio"]');
      if (radio && radio.checked) {
        item.classList.add('border-primary');
        item.querySelector('.circle-wrapper')?.classList.add('bg-primary', 'text-white');
        item.querySelector('.circle-wrapper')?.classList.remove('bg-light');
      } else {
        item.classList.remove('border-primary');
        item.querySelector('.circle-wrapper')?.classList.remove('bg-primary', 'text-white');
        item.querySelector('.circle-wrapper')?.classList.add('bg-light');
      }
    });
  }    // Continue button handler
  if (continueBtn) {
    // Disable the button initially until a property is selected
    continueBtn.disabled = true;

    continueBtn.addEventListener('click', function() {
      const selectedProperty = document.querySelector('input[name="selectedProperty"]:checked') || document.querySelector('input[name="property_id"]:checked');

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
        localStorage.setItem('selectedPropertyType', propertyType || '');

        // Kiểm tra xem form có tồn tại không
        const form = document.querySelector('form[action*="property.listings.store"]');

        if (form) {
          // Thêm input hidden để đảm bảo dữ liệu được gửi đi
          let hiddenInput = document.createElement('input');
          hiddenInput.type = 'hidden';
          hiddenInput.name = 'property_id';
          hiddenInput.value = propertyId;
          form.appendChild(hiddenInput);

          if (propertyType) {
            let typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = 'property_type';
            typeInput.value = propertyType;
            form.appendChild(typeInput);
          }

          // Submit form
          form.submit();
          return;
        }

        // Nếu không có form hiện có, tạo form động
        const dynamicForm = document.createElement('form');
        dynamicForm.method = 'POST';
        dynamicForm.action = route('owner.property.listings.store');

        // Thêm CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
          const csrfInput = document.createElement('input');
          csrfInput.type = 'hidden';
          csrfInput.name = '_token';
          csrfInput.value = csrfToken.getAttribute('content');
          dynamicForm.appendChild(csrfInput);
        }

        // Thêm property ID
        const propertyInput = document.createElement('input');
        propertyInput.type = 'hidden';
        propertyInput.name = 'property_id';
        propertyInput.value = propertyId;
        dynamicForm.appendChild(propertyInput);

        // Thêm property type nếu có
        if (propertyType) {
          const typeInput = document.createElement('input');
          typeInput.type = 'hidden';
          typeInput.name = 'property_type';
          typeInput.value = propertyType;
          dynamicForm.appendChild(typeInput);
        }

        // Thêm form vào document và submit
        document.body.appendChild(dynamicForm);
        dynamicForm.submit();
        return;
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

  // Kiểm tra xem modal có form hay không
  function checkAndAddFormIfNeeded() {
    const createPropertyListingModal = document.getElementById('createPropertyListingModal');
    if (!createPropertyListingModal) return;

    // Kiểm tra xem đã có form trong modal chưa
    const modalBody = createPropertyListingModal.querySelector('.modal-body');
    if (!modalBody) return;

    const existingForm = modalBody.closest('form');

    // Nếu không có form, thêm form bao quanh nội dung modal
    if (!existingForm) {
      // Lấy nội dung hiện tại của modal body
      const modalBodyContent = modalBody.innerHTML;
      const modalFooter = createPropertyListingModal.querySelector('.modal-footer');
      const footerContent = modalFooter ? modalFooter.innerHTML : '';

      // Tạo form mới
      const formElement = document.createElement('form');
      formElement.setAttribute('action', route('owner.property.listings.store'));
      formElement.setAttribute('method', 'POST');

      // Thêm CSRF token
      const csrfToken = document.querySelector('meta[name="csrf-token"]');
      if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        formElement.appendChild(csrfInput);
      }

      // Đưa nội dung vào form
      modalBody.innerHTML = '';
      formElement.innerHTML = modalBodyContent;
      modalBody.appendChild(formElement);

      // Nếu có footer, di chuyển button vào form
      if (modalFooter && footerContent) {
        modalFooter.innerHTML = '';
        const newFooter = document.createElement('div');
        newFooter.className = 'modal-footer justify-content-center border-0';
        newFooter.innerHTML = footerContent;
        formElement.appendChild(newFooter);
      }
    }
  }

  // Lắng nghe sự kiện khi modal được mở
  const createPropertyListingModal = document.getElementById('createPropertyListingModal');
  if (createPropertyListingModal) {
    createPropertyListingModal.addEventListener('shown.bs.modal', function () {
      // Kiểm tra form trước
      checkAndAddFormIfNeeded();

      // Tải lại dữ liệu mỗi khi modal được mở
      loadPropertiesFromAPI();
    });
  }
});