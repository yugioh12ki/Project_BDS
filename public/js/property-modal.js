/**
 * Property Modal JavaScript
 * Xử lý modal chi tiết bất động sản với validation hình ảnh
 * Có thể sử dụng trong bất kỳ trang nào có modal #propertyDetailModal
 */

// Configuration
const MODAL_CONFIG = {
    modalId: 'propertyDetailModal',
    loadingContentId: 'modalLoadingContent',
    propertyContentId: 'modalPropertyContent',
    appointmentBtnId: 'modalAppointmentBtn',
    apiEndpoint: '/agent/api/property/',
    appointmentsUrl: '/agent/appointments'
};

// Helper function để lấy class cho loại bất động sản
function getTypeClass(type) {
    const typeClasses = {
        'Sale': 'bg-primary',
        'Rent': 'bg-success'
    };
    return typeClasses[type] || 'bg-secondary';
}

// Helper function để format giá tiền
function formatPrice(price, type) {
    if (!price || price <= 0) return 'Liên hệ để biết giá';
    
    const formattedPrice = new Intl.NumberFormat('vi-VN').format(price);
    const suffix = type === 'Rent' ? '/tháng' : '';
    return `${formattedPrice} VNĐ${suffix}`;
}

// Helper function để tạo Google Maps embed URL
function createGoogleMapsUrl(address) {
    if (!address) return null;
    const encodedAddress = encodeURIComponent(address);
    return `https://www.google.com/maps?q=${encodedAddress}&output=embed`;
}

// Helper function để validate số lượng hình ảnh
function validatePropertyImages(property) {
    const images = property.images || [];
    return {
        isValid: images.length === 4,
        imageCount: images.length,
        message: images.length !== 4 ? 
            `Bất động sản cần có đúng 4 hình ảnh (hiện tại: ${images.length})` : null
    };
}

// Helper function để tạo image gallery
function generateImageGallery(images) {
    if (!images || images.length === 0) {
        return `
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Không có hình ảnh nào được tải lên
            </div>
        `;
    }

    return `
        <div class="row g-2">
            ${images.map((image, index) => `
                <div class="col-6 col-md-3">
                    <div class="position-relative">
                        <img src="${image.url || image.image_path || image}" 
                             alt="Hình ảnh ${index + 1}" 
                             class="img-fluid rounded w-100" 
                             style="height: 150px; object-fit: cover; cursor: pointer;"
                             onclick="openImageModal('${image.url || image.image_path || image}', 'Hình ảnh ${index + 1}')">
                        <div class="position-absolute top-0 end-0 m-1">
                            <span class="badge bg-dark">${index + 1}/4</span>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Helper function để mở modal xem ảnh lớn
function openImageModal(imageUrl, title) {
    // Tạo modal động để xem ảnh lớn
    const existingModal = document.getElementById('imageViewModal');
    if (existingModal) {
        existingModal.remove();
    }

    const modalHtml = `
        <div class="modal fade" id="imageViewModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${imageUrl}" alt="${title}" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('imageViewModal'));
    modal.show();
}

// Hàm tạo HTML cho thông tin bất động sản
function generatePropertyHTML(property) {
    // Validate số lượng hình ảnh
    const imageValidation = validatePropertyImages(property);
    
    // Nếu không đủ 4 ảnh, hiển thị cảnh báo
    if (!imageValidation.isValid) {
        return `
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Lỗi hiển thị bất động sản</strong><br>
                ${imageValidation.message}<br>
                <small class="text-muted">Vui lòng cập nhật đủ 4 hình ảnh để hiển thị bất động sản này.</small>
            </div>
            <div class="text-center py-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        `;
    }

    return `
        <!-- Property Images Gallery -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h6 class="mb-3"><i class="bi bi-images me-2"></i>Hình ảnh bất động sản (${property.images.length}/4)</h6>
                ${generateImageGallery(property.images)}
            </div>
        </div>
        
        <div class="property-info">
            <!-- Basic Info -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">${property.title || ''}</h5>
                        <span class="badge ${getTypeClass(property.type)}">${property.type_name || property.type || 'Không xác định'}</span>
                    </div>
                    <p class="text-muted mb-0 mt-1">
                        <i class="bi bi-geo-alt me-1"></i>${property.full_address || property.address || 'Không có thông tin'}
                    </p>
                </div>
            </div>
            
            <!-- Property Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-currency-dollar fs-3 text-success mb-2"></i>
                            <h6>Giá</h6>
                            <p class="mb-0 fw-bold text-success fs-5">${property.formatted_price || formatPrice(property.price, property.type)}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-house fs-3 text-primary mb-2"></i>
                            <h6>Loại BDS</h6>
                            <p class="mb-0 fw-bold">${property.category || 'Không xác định'}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Property Details Grid -->
            ${property.area || property.bedroom || property.bathroom || property.floor ? `
            <hr>
            <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Thông tin chi tiết</h6>
            <div class="row mb-4">
                ${property.area ? `
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <i class="bi bi-rulers fs-3 text-primary mb-2"></i>
                        <div class="fw-bold">${property.area} m²</div>
                        <small class="text-muted">Diện tích</small>
                    </div>
                </div>
                ` : ''}
                ${property.bedroom ? `
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <i class="bi bi-door-open fs-3 text-primary mb-2"></i>
                        <div class="fw-bold">${property.bedroom}</div>
                        <small class="text-muted">Phòng ngủ</small>
                    </div>
                </div>
                ` : ''}
                ${property.bathroom ? `
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <i class="bi bi-droplet fs-3 text-primary mb-2"></i>
                        <div class="fw-bold">${property.bathroom}</div>
                        <small class="text-muted">Phòng tắm</small>
                    </div>
                </div>
                ` : ''}
                ${property.floor ? `
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <i class="bi bi-building fs-3 text-primary mb-2"></i>
                        <div class="fw-bold">${property.floor}</div>
                        <small class="text-muted">Số tầng</small>
                    </div>
                </div>
                ` : ''}
            </div>
            ` : ''}
            
            <!-- Additional Details -->
            ${property.road || property.legal || property.interior ? `
            <div class="row mb-4">
                ${property.road ? `
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-signpost me-2"></i>Đường</h6>
                            <p class="card-text">${property.road}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
                ${property.legal ? `
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-file-text me-2"></i>Pháp lý</h6>
                            <p class="card-text">${property.legal}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
                ${property.interior ? `
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-house-gear me-2"></i>Nội thất</h6>
                            <p class="card-text">${property.interior}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
            ` : ''}
            
            <!-- Utility Prices (for rent properties) -->
            ${property.type === 'Rent' && (property.water_price || property.power_price) ? `
            <hr>
            <h6 class="mb-3"><i class="bi bi-gear me-2"></i>Phí dịch vụ</h6>
            <div class="row mb-4">
                ${property.water_price ? `
                <div class="col-md-6 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-droplet-fill fs-3 text-info mb-2"></i>
                            <h6>Giá nước</h6>
                            <p class="mb-0 fw-bold">${property.water_price} VNĐ/m³</p>
                        </div>
                    </div>
                </div>
                ` : ''}
                ${property.power_price ? `
                <div class="col-md-6 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-lightning-fill fs-3 text-warning mb-2"></i>
                            <h6>Giá điện</h6>
                            <p class="mb-0 fw-bold">${property.power_price} VNĐ/kWh</p>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
            ` : ''}
            
            <!-- Utilities -->
            ${property.utilities ? `
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-tools me-2"></i>Tiện ích</h6>
                            <p class="card-text">${property.utilities}</p>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}
            
            <!-- Description -->
            ${property.description ? `
            <hr>
            <h6 class="mb-3"><i class="bi bi-card-text me-2"></i>Mô tả</h6>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">${property.description}</p>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}
            
            <!-- Google Maps -->
            <hr>
            <h6 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Vị trí</h6>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div id="propertyMap" style="height: 300px; width: 100%;">
                        <iframe 
                            width="100%" 
                            height="300" 
                            frameborder="0" 
                            style="border:0; border-radius: 0.375rem;"
                            src="${createGoogleMapsUrl(property.full_address || property.address || 'Việt Nam')}"
                            allowfullscreen>
                        </iframe>
                    </div>
                    <p class="text-muted mt-2">
                        <i class="bi bi-geo-alt me-1"></i>
                        ${property.full_address || property.address || 'Không có thông tin địa chỉ'}
                    </p>
                </div>
            </div>
            
            <!-- Owner Information -->
            ${property.owner_name ? `
            <hr>
            <h6 class="mb-3"><i class="bi bi-person me-2"></i>Thông tin chủ sở hữu</h6>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px">
                                    <i class="bi bi-person-fill fs-4 text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">${property.owner_name}</h6>
                                    <small class="text-muted">Chủ sở hữu</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                ${property.owner_phone ? `
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-telephone me-2 text-primary"></i>
                                        <a href="tel:${property.owner_phone}" class="text-decoration-none">
                                            ${property.owner_phone}
                                        </a>
                                    </div>
                                </div>
                                ` : ''}
                                
                                ${property.owner_email ? `
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-envelope me-2 text-primary"></i>
                                        <a href="mailto:${property.owner_email}" class="text-decoration-none">
                                            ${property.owner_email}
                                        </a>
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}
            
            <!-- Property Info Footer -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-hash fs-4 text-secondary mb-2"></i>
                            <h6>ID bất động sản</h6>
                            <p class="mb-0 fw-bold">${property.id}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar fs-4 text-secondary mb-2"></i>
                            <h6>Ngày đăng</h6>
                            <p class="mb-0 fw-bold">${property.posted_date || 'Không có thông tin'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Hàm tạo HTML cho trạng thái lỗi
function generateErrorHTML(errorMessage) {
    return `
        <div class="text-center py-5">
            <div class="text-danger mb-3">
                <i class="bi bi-exclamation-triangle fs-1"></i>
            </div>
            <h5 class="text-danger">Lỗi tải thông tin</h5>
            <p class="text-muted">${errorMessage}</p>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
        </div>
    `;
}

// Hàm load thông tin property từ API vào modal
async function loadPropertyDetails(propertyId) {
    console.log('🔍 Loading property details for ID:', propertyId);
    console.log('🔍 API Endpoint will be:', `${MODAL_CONFIG.apiEndpoint}${propertyId}`);
    
    const modal = document.getElementById(MODAL_CONFIG.modalId);
    if (!modal) {
        console.error('❌ Property detail modal not found');
        return;
    }
    
    console.log('✅ Modal found:', modal);
    
    const loadingContent = document.getElementById(MODAL_CONFIG.loadingContentId);
    const propertyContent = document.getElementById(MODAL_CONFIG.propertyContentId);
    const modalTitle = modal.querySelector('.modal-title');
    const appointmentBtn = document.getElementById(MODAL_CONFIG.appointmentBtnId);
    
    // Hiển thị loading và ẩn nội dung
    if (loadingContent) {
        loadingContent.classList.remove('d-none');
    }
    if (propertyContent) {
        propertyContent.classList.add('d-none');
        propertyContent.innerHTML = '';
    }
    
    try {
        const apiUrl = `${MODAL_CONFIG.apiEndpoint}${propertyId}`;
        console.log('🌐 Making API call to:', apiUrl);
        
        const response = await fetch(apiUrl);
        console.log('📡 API Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('📦 API Response data:', data);
        
        if (!data.success) {
            throw new Error(data.error || 'Không thể tải thông tin bất động sản');
        }
        
        const property = data.property;
        
        // Cập nhật tiêu đề modal
        if (modalTitle) {
            modalTitle.innerHTML = `
                <i class="bi bi-house-door me-2"></i>
                ${property.title || 'Chi tiết bất động sản'}
            `;
        }
        
        // Tạo HTML cho chi tiết bất động sản
        if (propertyContent) {
            propertyContent.innerHTML = generatePropertyHTML(property);
        }
        
        // Cập nhật nút đặt lịch hẹn
        if (appointmentBtn) {
            appointmentBtn.href = MODAL_CONFIG.appointmentsUrl;
            appointmentBtn.style.display = 'inline-block';
        }
        
        // Ẩn loading và hiển thị nội dung
        if (loadingContent) {
            loadingContent.classList.add('d-none');
        }
        if (propertyContent) {
            propertyContent.classList.remove('d-none');
        }
        
        console.log('✅ Property details loaded successfully');
        
    } catch (error) {
        console.error('❌ Error loading property details:', error);
        
        if (propertyContent) {
            propertyContent.innerHTML = generateErrorHTML(error.message);
            propertyContent.classList.remove('d-none');
        }
        
        if (loadingContent) {
            loadingContent.classList.add('d-none');
        }
    }
}

// Hàm khởi tạo các event listeners
function initPropertyModal() {
    console.log('Initializing property modal...');
    
    // Lưu URL cho trang đặt lịch hẹn
    window.appointmentsUrl = document.querySelector('a[href*="appointments"]')?.getAttribute('href') || '#';
    
    // Thêm event listener cho các nút xem chi tiết
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.property-detail-link');
        if (!button) return;
        
        console.log('Property detail button clicked:', button);
        
        const propertyId = button.getAttribute('data-property-id');
        console.log('Property ID from button:', propertyId);
        
        if (!propertyId) {
            console.warn('Không tìm thấy ID của bất động sản', button);
            return;
        }
        
        console.log('Loading property details for ID:', propertyId);
        // Gọi hàm load property details với propertyId
        loadPropertyDetails(propertyId);
    });
    
    // Log để xác nhận script đã được tải
    console.log('Property modal script initialized successfully');
}

// Khởi tạo khi trang đã tải xong
document.addEventListener('DOMContentLoaded', initPropertyModal);

// Hàm tạo HTML cho thông tin bất động sản
function generatePropertyHTML(property) {
    // Validate số lượng hình ảnh
    const imageValidation = validatePropertyImages(property);
    
    // Nếu không đủ 4 ảnh, hiển thị cảnh báo
    if (!imageValidation.isValid) {
        return `
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Lỗi hiển thị bất động sản</strong><br>
                ${imageValidation.message}<br>
                <small class="text-muted">Vui lòng cập nhật đủ 4 hình ảnh để hiển thị bất động sản này.</small>
            </div>
            <div class="text-center py-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        `;
    }

    return `
        <!-- Property Images Gallery -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h6 class="mb-3"><i class="bi bi-images me-2"></i>Hình ảnh bất động sản (${property.images.length}/4)</h6>
                ${generateImageGallery(property.images)}
            </div>
        </div>
        
        <div class="property-info">
            <!-- Basic Info -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">${property.title || ''}</h5>
                        <span class="badge ${getTypeClass(property.type)}">${property.type_name || property.type || 'Không xác định'}</span>
                    </div>
                    <p class="text-muted mb-0 mt-1">
                        <i class="bi bi-geo-alt me-1"></i>${property.full_address || property.address || 'Không có thông tin'}
                    </p>
                </div>
            </div>
            
            <!-- Property Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-currency-dollar fs-3 text-success mb-2"></i>
                            <h6>Giá</h6>
                            <p class="mb-0 fw-bold text-success fs-5">${property.formatted_price || formatPrice(property.price, property.type)}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-house fs-3 text-primary mb-2"></i>
                            <h6>Loại BDS</h6>
                            <p class="mb-0 fw-bold">${property.category || 'Không xác định'}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Property Details Grid -->
            ${property.area || property.bedroom || property.bathroom || property.floor ? `
            <hr>
            <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Thông tin chi tiết</h6>
            <div class="row mb-4">
                ${property.area ? `
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <i class="bi bi-rulers fs-3 text-primary mb-2"></i>
                        <div class="fw-bold">${property.area} m²</div>
                        <small class="text-muted">Diện tích</small>
                    </div>
                </div>
                ` : ''}
                ${property.bedroom ? `
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <i class="bi bi-door-open fs-3 text-primary mb-2"></i>
                        <div class="fw-bold">${property.bedroom}</div>
                        <small class="text-muted">Phòng ngủ</small>
                    </div>
                </div>
                ` : ''}
                ${property.bathroom ? `
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <i class="bi bi-droplet fs-3 text-primary mb-2"></i>
                        <div class="fw-bold">${property.bathroom}</div>
                        <small class="text-muted">Phòng tắm</small>
                    </div>
                </div>
                ` : ''}
                ${property.floor ? `
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <i class="bi bi-building fs-3 text-primary mb-2"></i>
                        <div class="fw-bold">${property.floor}</div>
                        <small class="text-muted">Số tầng</small>
                    </div>
                </div>
                ` : ''}
            </div>
            ` : ''}
            
            <!-- Additional Details -->
            ${property.road || property.legal || property.interior ? `
            <div class="row mb-4">
                ${property.road ? `
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-signpost me-2"></i>Đường</h6>
                            <p class="card-text">${property.road}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
                ${property.legal ? `
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-file-text me-2"></i>Pháp lý</h6>
                            <p class="card-text">${property.legal}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
                ${property.interior ? `
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-house-gear me-2"></i>Nội thất</h6>
                            <p class="card-text">${property.interior}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
            ` : ''}
            
            <!-- Utility Prices (for rent properties) -->
            ${property.type === 'Rent' && (property.water_price || property.power_price) ? `
            <hr>
            <h6 class="mb-3"><i class="bi bi-gear me-2"></i>Phí dịch vụ</h6>
            <div class="row mb-4">
                ${property.water_price ? `
                <div class="col-md-6 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-droplet-fill fs-3 text-info mb-2"></i>
                            <h6>Giá nước</h6>
                            <p class="mb-0 fw-bold">${property.water_price} VNĐ/m³</p>
                        </div>
                    </div>
                </div>
                ` : ''}
                ${property.power_price ? `
                <div class="col-md-6 mb-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-lightning-fill fs-3 text-warning mb-2"></i>
                            <h6>Giá điện</h6>
                            <p class="mb-0 fw-bold">${property.power_price} VNĐ/kWh</p>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
            ` : ''}
            
            <!-- Utilities -->
            ${property.utilities ? `
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-tools me-2"></i>Tiện ích</h6>
                            <p class="card-text">${property.utilities}</p>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}
            
            <!-- Description -->
            ${property.description ? `
            <hr>
            <h6 class="mb-3"><i class="bi bi-card-text me-2"></i>Mô tả</h6>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">${property.description}</p>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}
            
            <!-- Google Maps -->
            <hr>
            <h6 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Vị trí</h6>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div id="propertyMap" style="height: 300px; width: 100%;">
                        <iframe 
                            width="100%" 
                            height="300" 
                            frameborder="0" 
                            style="border:0; border-radius: 0.375rem;"
                            src="${createGoogleMapsUrl(property.full_address || property.address || 'Việt Nam')}"
                            allowfullscreen>
                        </iframe>
                    </div>
                    <p class="text-muted mt-2">
                        <i class="bi bi-geo-alt me-1"></i>
                        ${property.full_address || property.address || 'Không có thông tin địa chỉ'}
                    </p>
                </div>
            </div>
            
            <!-- Owner Information -->
            ${property.owner_name ? `
            <hr>
            <h6 class="mb-3"><i class="bi bi-person me-2"></i>Thông tin chủ sở hữu</h6>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px">
                                    <i class="bi bi-person-fill fs-4 text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">${property.owner_name}</h6>
                                    <small class="text-muted">Chủ sở hữu</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                ${property.owner_phone ? `
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-telephone me-2 text-primary"></i>
                                        <a href="tel:${property.owner_phone}" class="text-decoration-none">
                                            ${property.owner_phone}
                                        </a>
                                    </div>
                                </div>
                                ` : ''}
                                
                                ${property.owner_email ? `
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-envelope me-2 text-primary"></i>
                                        <a href="mailto:${property.owner_email}" class="text-decoration-none">
                                            ${property.owner_email}
                                        </a>
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}
            
            <!-- Property Info Footer -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-hash fs-4 text-secondary mb-2"></i>
                            <h6>ID bất động sản</h6>
                            <p class="mb-0 fw-bold">${property.id}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar fs-4 text-secondary mb-2"></i>
                            <h6>Ngày đăng</h6>
                            <p class="mb-0 fw-bold">${property.posted_date || 'Không có thông tin'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Hàm tạo HTML cho trạng thái lỗi
function generateErrorHTML(errorMessage) {
    return `
        <div class="text-center py-5">
            <div class="text-danger mb-3">
                <i class="bi bi-exclamation-triangle fs-1"></i>
            </div>
            <h5 class="text-danger">Lỗi tải thông tin</h5>
            <p class="text-muted">${errorMessage}</p>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
        </div>
    `;
}

// Hàm load thông tin property từ API vào modal
async function loadPropertyDetails(propertyId) {
    console.log('🔍 Loading property details for ID:', propertyId);
    console.log('🔍 API Endpoint will be:', `${MODAL_CONFIG.apiEndpoint}${propertyId}`);
    
    const modal = document.getElementById(MODAL_CONFIG.modalId);
    if (!modal) {
        console.error('❌ Property detail modal not found');
        return;
    }
    
    console.log('✅ Modal found:', modal);
    
    const loadingContent = document.getElementById(MODAL_CONFIG.loadingContentId);
    const propertyContent = document.getElementById(MODAL_CONFIG.propertyContentId);
    const modalTitle = modal.querySelector('.modal-title');
    const appointmentBtn = document.getElementById(MODAL_CONFIG.appointmentBtnId);
    
    // Hiển thị loading và ẩn nội dung
    if (loadingContent) {
        loadingContent.classList.remove('d-none');
    }
    if (propertyContent) {
        propertyContent.classList.add('d-none');
        propertyContent.innerHTML = '';
    }
    
    try {
        const apiUrl = `${MODAL_CONFIG.apiEndpoint}${propertyId}`;
        console.log('🌐 Making API call to:', apiUrl);
        
        const response = await fetch(apiUrl);
        console.log('📡 API Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('📦 API Response data:', data);
        
        if (!data.success) {
            throw new Error(data.error || 'Không thể tải thông tin bất động sản');
        }
        
        const property = data.property;
        
        // Cập nhật tiêu đề modal
        if (modalTitle) {
            modalTitle.innerHTML = `
                <i class="bi bi-house-door me-2"></i>
                ${property.title || 'Chi tiết bất động sản'}
            `;
        }
        
        // Tạo HTML cho chi tiết bất động sản
        if (propertyContent) {
            propertyContent.innerHTML = generatePropertyHTML(property);
        }
        
        // Cập nhật nút đặt lịch hẹn
        if (appointmentBtn) {
            appointmentBtn.href = MODAL_CONFIG.appointmentsUrl;
            appointmentBtn.style.display = 'inline-block';
        }
        
        // Ẩn loading và hiển thị nội dung
        if (loadingContent) {
            loadingContent.classList.add('d-none');
        }
        if (propertyContent) {
            propertyContent.classList.remove('d-none');
        }
        
        console.log('✅ Property details loaded successfully');
        
    } catch (error) {
        console.error('❌ Error loading property details:', error);
        
        if (propertyContent) {
            propertyContent.innerHTML = generateErrorHTML(error.message);
            propertyContent.classList.remove('d-none');
        }
        
        if (loadingContent) {
            loadingContent.classList.add('d-none');
        }
    }
}
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-door-open fs-3 text-primary mb-2"></i>
                                <div class="fw-bold">${property.bedroom || 'N/A'}</div>
                                <small class="text-muted">Phòng ngủ</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-droplet fs-3 text-primary mb-2"></i>
                                <div class="fw-bold">${property.bathroom || 'N/A'}</div>
                                <small class="text-muted">Phòng tắm</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <i class="bi bi-building fs-3 text-primary mb-2"></i>
                                <div class="fw-bold">${property.floor || 'N/A'}</div>
                                <small class="text-muted">Số tầng</small>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    
                    <!-- Additional Details -->
                    ${property.road || property.legal || property.interior ? `
                    <div class="row mb-4">
                        ${property.road ? `
                        <div class="col-md-4 mb-3">
                            <div class="detail-item">
                                <div class="detail-label">Đường</div>
                                <div class="detail-value">${property.road}</div>
                            </div>
                        </div>
                        ` : ''}
                        ${property.legal ? `
                        <div class="col-md-4 mb-3">
                            <div class="detail-item">
                                <div class="detail-label">Pháp lý</div>
                                <div class="detail-value">${property.legal}</div>
                            </div>
                        </div>
                        ` : ''}
                        ${property.interior ? `
                        <div class="col-md-4 mb-3">
                            <div class="detail-item">
                                <div class="detail-label">Nội thất</div>
                                <div class="detail-value">${property.interior}</div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    ` : ''}
                    
                    <!-- Utility Prices (for rent properties) -->
                    ${property.type === 'Rent' && (property.water_price || property.power_price) ? `
                    <hr>
                    <h6 class="mb-3">Phí dịch vụ</h6>
                    <div class="row mb-4">
                        ${property.water_price ? `
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="bi bi-droplet-fill fs-3 text-info mb-2"></i>
                                    <h6>Giá nước</h6>
                                    <p class="mb-0 fw-bold">${property.water_price} VNĐ/m³</p>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                        ${property.power_price ? `
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="bi bi-lightning-fill fs-3 text-warning mb-2"></i>
                                    <h6>Giá điện</h6>
                                    <p class="mb-0 fw-bold">${property.power_price} VNĐ/kWh</p>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    ` : ''}
                    
                    <!-- Utilities -->
                    ${property.utilities ? `
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="detail-item">
                                <div class="detail-label">Tiện ích</div>
                                <div class="detail-value">${property.utilities}</div>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    
                    <!-- Description -->
                    ${property.description ? `
                    <hr>
                    <h6 class="mb-3">Mô tả</h6>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="detail-item">
                                <div class="detail-value">${property.description}</div>
                            </div>
                        </div>
                    </div>
                    ` : ''}                    <!-- Google Maps -->
                    <hr>
                    <h6 class="mb-3">Vị trí</h6>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div id="propertyMap" style="height: 300px; width: 100%;">
                                <iframe 
                                    width="100%" 
                                    height="300" 
                                    frameborder="0" 
                                    style="border:0"
                                    src="https://www.google.com/maps?q=${encodeURIComponent(property.full_address || property.address || 'Việt Nam')}&output=embed"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <p class="text-muted mt-2">
                                <i class="bi bi-geo-alt me-1"></i>
                                ${property.full_address || property.address || 'Không có thông tin địa chỉ'}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Owner Information -->
                    ${property.owner_name ? `
                    <hr>
                    <h6 class="mb-3">Thông tin chủ sở hữu</h6>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px">
                                    <i class="bi bi-person-fill fs-4 text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">${property.owner_name}</h6>
                                    <small class="text-muted">Chủ sở hữu</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                ${property.owner_phone ? `
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <div class="detail-label">Số điện thoại</div>
                                        <div class="detail-value">
                                            <a href="tel:${property.owner_phone}" class="text-decoration-none">
                                                <i class="bi bi-telephone me-1"></i>${property.owner_phone}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                ` : ''}
                                
                                ${property.owner_email ? `
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <div class="detail-label">Email</div>
                                        <div class="detail-value">
                                            <a href="mailto:${property.owner_email}" class="text-decoration-none">
                                                <i class="bi bi-envelope me-1"></i>${property.owner_email}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    
                    <!-- Property Info Footer -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">ID bất động sản</div>
                                <div class="detail-value">${property.id}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">Ngày đăng</div>
                                <div class="detail-value">${property.posted_date || 'Không có thông tin'}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <a href="${window.appointmentsUrl || '#'}" class="btn btn-primary">
                        <i class="bi bi-calendar-plus me-2"></i>Đặt lịch hẹn
                    </a>
                </div>
            `;
        }
        
    } catch (error) {
        console.error('Error loading property details:', error);
        if (modalBody) {
            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="text-danger mb-3">
                        <i class="bi bi-exclamation-triangle fs-1"></i>
                    </div>
                    <h5 class="text-danger">Lỗi tải thông tin</h5>
                    <p class="text-muted">${error.message}</p>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            `;
        }
    }
}

// Hàm khởi tạo các event listeners
function initPropertyModal() {
    console.log('Initializing property modal...');
    
    // Lưu URL cho trang đặt lịch hẹn
    window.appointmentsUrl = document.querySelector('a[href*="appointments"]')?.getAttribute('href') || '#';
    
    // Thêm event listener cho các nút xem chi tiết
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.property-detail-link');
        if (!button) return;
        
        console.log('Property detail button clicked:', button);
        
        const propertyId = button.getAttribute('data-property-id');
        console.log('Property ID from button:', propertyId);
        
        if (!propertyId) {
            console.warn('Không tìm thấy ID của bất động sản', button);
            return;
        }
        
        console.log('Loading property details for ID:', propertyId);
        // Gọi hàm load property details với propertyId
        loadPropertyDetails(propertyId);
    });
    
    // Log để xác nhận script đã được tải
    console.log('Property modal script initialized successfully');
}

// Khởi tạo khi trang đã tải xong
document.addEventListener('DOMContentLoaded', initPropertyModal);
