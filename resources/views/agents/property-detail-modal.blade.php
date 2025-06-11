<!-- Property Detail Modal -->
<div class="modal fade" id="propertyDetailModal" tabindex="-1" aria-labelledby="propertyDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="propertyDetailModalLabel">
                    <i class="bi bi-eye me-2"></i>Chi tiết bất động sản
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                <!-- Loading state -->
                <div id="modalLoadingContent" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-3 text-muted">Đang tải thông tin bất động sản...</p>
                </div>
                
                <!-- Property content will be loaded here -->
                <div id="modalPropertyContent" class="d-none">
                    <!-- Content will be dynamically generated -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for property detail modal */
#propertyDetailModal .modal-body {
    max-height: 80vh;
    overflow-y: auto;
}

#propertyDetailModal .property-info h4 {
    color: #2c3e50;
    font-weight: 600;
}

#propertyDetailModal .property-info h6 {
    color: #34495e;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
}

#propertyDetailModal .bg-light {
    background-color: #f8f9fa !important;
    border: 1px solid #e9ecef;
}

#propertyDetailModal .badge {
    font-size: 0.8em;
    padding: 0.5em 0.8em;
}

#propertyDetailModal .card {
    border: 1px solid #e9ecef;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

#propertyDetailModal .map-container iframe {
    border-radius: 8px;
}

/* Icon spacing and styling */
#propertyDetailModal .bi {
    vertical-align: -0.125em;
}

#propertyDetailModal .fs-2 {
    font-size: 2rem !important;
}

#propertyDetailModal .fs-4 {
    font-size: 1.5rem !important;
}

/* Price highlighting */
#propertyDetailModal .text-success {
    color: #198754 !important;
}

/* Hover effects for clickable elements */
#propertyDetailModal a:hover {
    opacity: 0.8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #propertyDetailModal .modal-dialog {
        margin: 0.5rem;
    }
    
    #propertyDetailModal .property-info h4 {
        font-size: 1.1rem;
    }
    
    #propertyDetailModal .fs-2 {
        font-size: 1.5rem !important;
    }
    
    #propertyDetailModal .fs-5 {
        font-size: 1rem !important;
    }
    
    #propertyDetailModal .map-container {
        height: 250px !important;
    }
}

/* Smooth loading animation */
#propertyDetailModal .spinner-border {
    animation: spinner-border 1s linear infinite;
}

/* Better spacing for detail sections */
#propertyDetailModal .property-info > .row {
    margin-bottom: 1rem;
}

#propertyDetailModal .d-flex.align-items-center {
    min-height: 60px;
}

/* Enhanced card styling */
#propertyDetailModal .card-body {
    padding: 1.25rem;
}

/* Map container styling */
#propertyDetailModal .map-container {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 2px solid #e9ecef;
}

/* Custom 7-column grid for larger screens */
@media (min-width: 1400px) {
    #propertyDetailModal .col-xxl-1-7 {
        flex: 0 0 auto;
        width: 14.2857%; /* 100% / 7 columns = 14.2857% */
    }
}

/* Responsive border adjustments for 7-column layout */
@media (max-width: 575px) {
    #propertyDetailModal .property-info .row.g-0 .col-6:nth-child(odd) {
        border-right: 1px solid #e9ecef;
    }
    #propertyDetailModal .property-info .row.g-0 .col-6:nth-child(n+3) {
        border-top: 1px solid #e9ecef;
    }
}

@media (min-width: 576px) and (max-width: 767px) {
    #propertyDetailModal .property-info .row.g-0 .col-md-4:not(:nth-child(3n)) {
        border-right: 1px solid #e9ecef;
    }
    #propertyDetailModal .property-info .row.g-0 .col-md-4:nth-child(n+4) {
        border-top: 1px solid #e9ecef;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {    
    // Configuration
    const MODAL_CONFIG = {
        modalId: 'propertyDetailModal',
        loadingContentId: 'modalLoadingContent',
        propertyContentId: 'modalPropertyContent',
        appointmentBtnId: 'modalAppointmentBtn',
        apiEndpoint: window.location.pathname.includes('/test/') ? '/test/api/property/' : '/agent/api/property/',
        appointmentsUrl: '/agent/appointments'
    };

    // Helper functions
    function getTypeClass(type) {
        const typeClasses = {
            'Sale': 'bg-primary',
            'Rent': 'bg-success'
        };
        return typeClasses[type] || 'bg-secondary';
    }

    function getStatusBadge(status) {
        const statusConfig = {
            'active': { class: 'bg-success', text: 'Đang hiển thị' },
            'Active': { class: 'bg-success', text: 'Đang hiển thị' },
            'sold': { class: 'bg-danger', text: 'Đã bán' },
            'Sold': { class: 'bg-danger', text: 'Đã bán' },
            'rented': { class: 'bg-info', text: 'Đã cho thuê' },
            'Rented': { class: 'bg-info', text: 'Đã cho thuê' },
        };
        
        const config = statusConfig[status] || { class: 'bg-secondary', text: status };
        return `<span class="badge ${config.class}">${config.text}</span>`;
    }
    
    function formatPrice(price, type) {
        if (!price || price <= 0) return 'Liên hệ để biết giá';
        
        const formattedPrice = new Intl.NumberFormat('vi-VN').format(price);
        const suffix = type === 'Rent' ? '/tháng' : '';
        return `${formattedPrice} VNĐ${suffix}`;
    }    function generateGoogleMapsEmbed(address, latitude, longitude) {
        console.log('🗺️ Generating map for:', { address, latitude, longitude });
        
        // Use coordinates if available, otherwise use address
        let mapUrl;
        if (latitude && longitude && !isNaN(parseFloat(latitude)) && !isNaN(parseFloat(longitude))) {
            mapUrl = `https://maps.google.com/maps?q=${parseFloat(latitude)},${parseFloat(longitude)}&t=&z=15&ie=UTF8&iwloc=&output=embed`;
            console.log('✅ Using coordinates for map:', mapUrl);
        } else if (address) {
            const encodedAddress = encodeURIComponent(address);
            mapUrl = `https://maps.google.com/maps?q=${encodedAddress}&t=&z=15&ie=UTF8&iwloc=&output=embed`;
            console.log('✅ Using address for map:', mapUrl);
        } else {
            console.log('❌ No valid location data for map');
            return '';
        }
        
        return `
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Vị trí trên bản đồ</h6>
                    <div class="map-container" style="position: relative; height: 400px; border-radius: 8px; overflow: hidden;">
                        <iframe 
                            width="100%"
                            height="300"
                            style="border:0"
                            loading="lazy"
                            allowfullscreen
                            src="${mapUrl}">
                        </iframe>
                    </div>
                    ${latitude && longitude ? `
                    <div class="mt-2 text-muted small">
                        <i class="bi bi-pin-map me-1"></i>
                        Tọa độ: ${latitude}, ${longitude}
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }    // Map API response to expected format
    function mapPropertyData(apiProperty) {
        console.log('📦 Mapping property data:', apiProperty);
        
        // Fix images path
        let images = [];
        if (apiProperty.images && Array.isArray(apiProperty.images)) {
            images = apiProperty.images.map(img => ({
                ...img,
                ImagePath: img.ImagePath?.replace(/\\/g, '/').replace('public/', '/storage/')
            }));
            console.log('🖼️ Processed images:', images);
        } else {
            console.warn('⚠️ No images found in property data');
        }
          // Check if address fields are available
        let fullAddress = 'Địa chỉ không xác định';
        if (apiProperty.Address) {
            fullAddress = apiProperty.Address;
            if (apiProperty.Ward) fullAddress += ', ' + apiProperty.Ward;
            if (apiProperty.District) fullAddress += ', ' + apiProperty.District;
            if (apiProperty.Province) fullAddress += ', ' + apiProperty.Province;
            console.log('📍 Full address:', fullAddress);
        } else {
            console.warn('⚠️ Address information missing:', apiProperty);
        }

        // Handle both nested and flat property data structure
        const mapped = {
            // Basic information
            id: apiProperty.PropertyID,
            title: apiProperty.Title || 'Bất động sản không xác định',
            description: apiProperty.Description || '',
            address: apiProperty.Address || 'Địa chỉ không xác định',
            ward: apiProperty.Ward || '',
            district: apiProperty.District || '',
            province: apiProperty.Province || '',
            full_address: fullAddress,
            price: apiProperty.Price || 0,
            formatted_price: formatPrice(apiProperty.Price, apiProperty.TypePro),
            type: apiProperty.TypePro || 'Sale',
            type_name: apiProperty.TypePro === 'Sale' ? 'Bán' : 'Cho thuê',
            status: apiProperty.Status || 'active',
            
            // Category information - check both nested and flat structure
            category: apiProperty.danhMuc?.ten_pro || apiProperty.category || 'Không xác định',
            property_type: apiProperty.danhMuc?.Type || apiProperty.property_type || 'Không xác định',
            
            // Images
            image_url: images.length > 0 ? images[0].ImagePath : null,
            images: images,
            
            // Location - ensure we have valid coordinates
            latitude: (apiProperty.Latitude && !isNaN(parseFloat(apiProperty.Latitude))) 
                ? parseFloat(apiProperty.Latitude) : null,
            longitude: (apiProperty.Longitude && !isNaN(parseFloat(apiProperty.Longitude))) 
                ? parseFloat(apiProperty.Longitude) : null,
            
            // Owner information - check both nested and flat structure
            owner_name: apiProperty.owner?.Name || apiProperty.owner_name || 'Chưa xác định',
            owner_phone: apiProperty.owner?.Phone || apiProperty.owner_phone || 'Chưa cập nhật',
            owner_email: apiProperty.owner?.Email || apiProperty.owner_email || 'Chưa cập nhật',
            owner_address: apiProperty.owner?.Address || apiProperty.owner_address || '',
              // Property details - check both nested and flat structure
            area: apiProperty.chiTiet?.Area || apiProperty.area || 
                  (apiProperty.chiTiet?.HouseLength && apiProperty.chiTiet?.HouseWidth 
                    ? apiProperty.chiTiet.HouseLength * apiProperty.chiTiet.HouseWidth 
                    : null) ||
                  (apiProperty.house_length && apiProperty.house_width 
                    ? apiProperty.house_length * apiProperty.house_width 
                    : null),
            bedroom: apiProperty.chiTiet?.Bedroom || apiProperty.bedroom || null,
            bathroom: apiProperty.chiTiet?.Bath_WC || apiProperty.bathroom || null,
            balcony: apiProperty.chiTiet?.Balcony || apiProperty.balcony || null,
            levelhouse: apiProperty.chiTiet?.Levelhouse || apiProperty.levelhouse || null,
            floor: apiProperty.chiTiet?.Floor || apiProperty.floor || null,
            road: apiProperty.chiTiet?.Road || apiProperty.road || null,
            house_length: apiProperty.chiTiet?.HouseLength || apiProperty.house_length || null,
            house_width: apiProperty.chiTiet?.HouseWidth || apiProperty.house_width || null,
            legal: apiProperty.chiTiet?.legal || apiProperty.legal || null,
            view: apiProperty.chiTiet?.view || apiProperty.view || null,
            near: apiProperty.chiTiet?.near || apiProperty.near || null,
            interior: apiProperty.chiTiet?.Interior || apiProperty.interior || null,
            water_price: apiProperty.chiTiet?.WaterPrice || apiProperty.water_price || null,
            power_price: apiProperty.chiTiet?.PowerPrice || apiProperty.power_price || null,
            utilities: apiProperty.chiTiet?.Utilities || apiProperty.utilities || null
        };
        
        console.log('✅ Final mapped property:', mapped);
        return mapped;
    }
      function generatePropertyHTML(property) {
        let html = '';
        
        // 1. Tiêu đề và thông tin cơ bản ở đầu tiên
        html += `
            <div class="property-info">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">${property.title}</h4>
                            <div>
                                <span class="fs-4 fw-bold text-success">${property.formatted_price}</span>
                            </div>
                        </div>
                        <p class="text-muted mb-2">
                            <i class="bi bi-geo-alt me-1"></i>${property.full_address}
                        </p>                        <div class="mb-2">
                            <span class="badge bg-light text-dark me-2">
                                <i class="bi bi-building me-1"></i>${property.category || 'Không xác định'}
                            </span>
                            <span class="badge bg-info text-dark me-2">
                                <i class="bi bi-tag me-1"></i>${property.property_type || 'Không xác định'}
                            </span>
                            <span class="badge ${getTypeClass(property.type)}">
                                ${property.type_name || property.type || 'Không xác định'}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // 2. Google Maps hiển thị ở vị trí thứ hai
        html += generateGoogleMapsEmbed(property.full_address, property.latitude, property.longitude);
          // 3. Property Images Gallery - Display all 4 images
        if (property.images && property.images.length > 0) {
            html += `
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="mb-3"><i class="bi bi-images me-2"></i>Hình ảnh bất động sản (${property.images.length} ảnh)</h6>
                        <div class="row g-2">
                            ${property.images.map((image, index) => `
                                <div class="col-md-6 col-lg-3">
                                    <div class="position-relative">
                                        <img src="${image.ImagePath}" 
                                             alt="${image.Caption || property.title}" 
                                             class="img-fluid rounded w-100" 
                                             style="height: 200px; object-fit: cover; cursor: pointer;"
                                             onclick="openImageModal('${image.ImagePath}', '${image.Caption || property.title}')">
                                        ${image.IsThumbnail ? '<span class="badge bg-primary position-absolute top-0 start-0 m-1">Ảnh chính</span>' : ''}
                                    </div>
                                    <div class="small mt-1 text-muted">${image.Caption || `Hình ${index + 1}`}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
        } else {
            html += `
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Không có hình ảnh cho bất động sản này
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Chi tiết căn hộ
        html += `
            <div class="property-info">
                <!-- Chi tiết căn hộ -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Thông tin chi tiết</h6>
                        <div class="card">
                            <div class="card-body p-0">                                <div class="row g-0 text-center">
                                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1-7 p-3 border-end">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-rulers fs-5 mb-2"></i>
                                            <strong>${property.area ? property.area + ' m²' : 'N/A'}</strong>
                                            <small class="text-muted">Diện tích</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1-7 p-3 border-end">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-door-open fs-5 mb-2"></i>
                                            <strong>${property.bedroom || 'N/A'}</strong>
                                            <small class="text-muted">Phòng ngủ</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1-7 p-3 border-end border-top border-top-md-0">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-droplet fs-5 mb-2"></i>
                                            <strong>${property.bathroom || 'N/A'}</strong>
                                            <small class="text-muted">Phòng tắm</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1-7 p-3 border-end border-top border-top-lg-0">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-layers fs-5 mb-2"></i>
                                            <strong>${property.floor || 'N/A'}</strong>
                                            <small class="text-muted">Số tầng</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1-7 p-3 border-end border-top border-top-lg-0">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-building fs-5 mb-2"></i>
                                            <strong>${property.levelhouse || 'N/A'}</strong>
                                            <small class="text-muted">Cấp nhà</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1-7 p-3 border-end border-top border-top-xl-0">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-house-door fs-5 mb-2"></i>
                                            <strong>${property.balcony === 0 ? 'Có' : property.balcony === 1 ? 'Không' : 'N/A'}</strong>
                                            <small class="text-muted">Ban công</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 col-xxl-1-7 p-3 border-top border-top-xl-0">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-signpost fs-5 mb-2"></i>
                                            <strong>${property.road ? property.road + 'm' : 'N/A'}</strong>
                                            <small class="text-muted">Đường trước nhà</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Thông tin pháp lý và nội thất -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h6 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Thông tin pháp lý</h6>
                        <div class="card">
                            <div class="card-body p-3">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <strong>Pháp lý:</strong> 
                                        <span class="text-muted">${property.legal || 'Không có thông tin'}</span>
                                    </li>
                                    <li>
                                        <strong>Hướng nhà:</strong> 
                                        <span class="text-muted">${property.view || 'Không có thông tin'}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="bi bi-house-door me-2"></i>Nội thất & Tiện ích</h6>
                        <div class="card">
                            <div class="card-body p-3">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <strong>Nội thất:</strong> 
                                        <span class="text-muted">${property.interior || 'Không có thông tin'}</span>
                                    </li>
                                    <li>
                                        <strong>Địa điểm lân cận:</strong> 
                                        <span class="text-muted">${property.near || 'Không có thông tin'}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Chi phí sinh hoạt -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="mb-3"><i class="bi bi-cash-coin me-2"></i>Chi phí sinh hoạt</h6>
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <strong>Giá nước:</strong> 
                                        <span class="text-muted">${property.water_price || 'Không có thông tin'}</span>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <strong>Giá điện:</strong> 
                                        <span class="text-muted">${property.power_price || 'Không có thông tin'}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Phí dịch vụ:</strong> 
                                        <span class="text-muted">${property.utilities || 'Không có thông tin'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Thông tin chủ nhà -->
                ${property.owner_name ? `
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="mb-3"><i class="bi bi-person me-2"></i>Thông tin chủ nhà</h6>
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="bi bi-person-circle fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">${property.owner_name}</h6>
                                        <div>
                                            ${property.owner_phone ? `<a href="tel:${property.owner_phone}" class="text-decoration-none me-3"><i class="bi bi-telephone me-1"></i>${property.owner_phone}</a>` : ''}
                                            ${property.owner_email ? `<a href="mailto:${property.owner_email}" class="text-decoration-none"><i class="bi bi-envelope me-1"></i>${property.owner_email}</a>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}
                
                <!-- Mô tả -->
                ${property.description ? `
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="mb-3"><i class="bi bi-file-text me-2"></i>Mô tả</h6>
                        <div class="card">
                            <div class="card-body p-3">
                                <p class="mb-0">${property.description}</p>
                            </div>
                        </div>
                    </div>                </div>
                ` : ''}
            </div>
        `;
        
        return html;
    }

    function generateErrorHTML(message) {
        return `
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
                </div>
                <h5>Lỗi khi tải thông tin bất động sản</h5>
                <p class="text-muted">${message}</p>
                <button type="button" class="btn btn-outline-primary mt-3" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại
                </button>
            </div>
        `;
    }

    // Main function to load property details
    async function loadPropertyDetails(propertyId) {
        console.log('🔍 Loading property details for ID:', propertyId);
        
        // Validate propertyId
        if (!propertyId || typeof propertyId !== 'string' || propertyId.trim() === '') {
            console.error('❌ Invalid property ID provided');
            return;
        }
        
        const modal = document.getElementById(MODAL_CONFIG.modalId);
        if (!modal) {
            console.error('❌ Modal element not found');
            return;
        }
        
        const loadingContent = document.getElementById(MODAL_CONFIG.loadingContentId);
        const propertyContent = document.getElementById(MODAL_CONFIG.propertyContentId);
        const modalTitle = modal.querySelector('.modal-title');
        const appointmentBtn = document.getElementById(MODAL_CONFIG.appointmentBtnId);
        
        // Show loading and hide content
        if (loadingContent) loadingContent.classList.remove('d-none');
        if (propertyContent) {
            propertyContent.classList.add('d-none');
            propertyContent.innerHTML = '';
        }
        
        try {
            const apiUrl = MODAL_CONFIG.apiEndpoint + propertyId.trim();
            console.log('🌐 Fetching property data from API:', apiUrl);
            
            const response = await fetch(apiUrl);
            
            if (!response.ok) {
                throw new Error(`API returned status code ${response.status}`);
            }
            
            const data = await response.json();
            console.log('📊 API response:', data);
            
            if (!data.success || !data.property) {
                throw new Error('API returned invalid data format');
            }
            
            // Map API response to our format
            const mappedProperty = mapPropertyData(data.property);
            console.log('🗺️ Mapped property data:', mappedProperty);
            
            // Update modal title with property name
            if (modalTitle && mappedProperty.title) {
                modalTitle.innerHTML = `<i class="bi bi-eye me-2"></i>${mappedProperty.title}`;
            }
            
            // Generate HTML from property data
            const propertyHTML = generatePropertyHTML(mappedProperty);
            if (propertyContent) {
                propertyContent.innerHTML = propertyHTML;
                propertyContent.classList.remove('d-none');
            }
            
            // Update appointment button
            if (appointmentBtn) {
                appointmentBtn.style.display = 'inline-flex';
                appointmentBtn.href = MODAL_CONFIG.appointmentsUrl + '?property_id=' + mappedProperty.id;
            }
        } catch (error) {
            console.error('❌ Error loading property details:', error);
            
            if (propertyContent) {
                propertyContent.innerHTML = generateErrorHTML(error.message || 'Không thể tải thông tin bất động sản.');
                propertyContent.classList.remove('d-none');
            }
            
            if (appointmentBtn) {
                appointmentBtn.style.display = 'none';
            }
        } finally {
            if (loadingContent) {
                loadingContent.classList.add('d-none');
            }
        }
    }    
    
    // Event listener for property detail buttons
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.property-detail-link');
        if (!button) return;
        
        // Thêm debugging
        console.log('🖱️ Property detail button clicked:', button);
        
        // Lấy property ID từ button
        let propertyId = button.getAttribute('data-property-id');
        
        // Fix cho trường hợp button có thuộc tính data-property-id nhưng giá trị là rỗng
        // Thử lấy từ text content hoặc các thuộc tính khác
        if (!propertyId || propertyId === '') {
            // Thử lấy từ text content của một element có class property-id trong button
            const idElement = button.querySelector('.property-id');
            if (idElement) {
                propertyId = idElement.textContent.trim();
            }
            
            // Thử lấy từ URL nếu là thẻ a
            if (!propertyId && button.href) {
                const idMatch = button.href.match(/\/property\/([^\/]+)/);
                if (idMatch) propertyId = idMatch[1];
            }
            
            // Thử lấy từ các custom attributes khác
            ['data-id', 'id', 'data-property', 'data-key'].forEach(attr => {
                if (!propertyId && button.hasAttribute(attr)) {
                    propertyId = button.getAttribute(attr);
                }
            });
            
            console.log('🔍 Fallback property ID:', propertyId);
        }

        if (!propertyId || propertyId === '') {
            console.error('⚠️ Không tìm thấy ID bất động sản');
            
            // Hiển thị thông báo lỗi trong modal
            const modal = document.getElementById(MODAL_CONFIG.modalId);
            if (!modal) return;
            
            const loadingContent = document.getElementById(MODAL_CONFIG.loadingContentId);
            const propertyContent = document.getElementById(MODAL_CONFIG.propertyContentId);
            
            if (loadingContent) loadingContent.classList.add('d-none');
            if (propertyContent) {
                propertyContent.innerHTML = generateErrorHTML('Không tìm thấy ID của bất động sản. Vui lòng thử lại.');
                propertyContent.classList.remove('d-none');
            }
            return;
        }
        
        console.log('🚀 Loading property details for ID:', propertyId);
        loadPropertyDetails(propertyId);
    });
    
    // Make loadPropertyDetails globally available
    window.loadPropertyDetails = loadPropertyDetails;
    console.log('Property modal script initialized successfully');
    
    // Function to open image in modal
    window.openImageModal = function(imageSrc, caption) {
        // Create image modal if not exists
        let imageModal = document.getElementById('imageViewModal');
        if (!imageModal) {
            const modalHTML = `
                <div class="modal fade" id="imageViewModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imageViewCaption"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center p-0">
                                <img id="imageViewImg" src="" class="img-fluid" alt="" style="max-height: 80vh;">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            imageModal = document.getElementById('imageViewModal');
        }
        
        // Update modal content
        document.getElementById('imageViewImg').src = imageSrc;
        document.getElementById('imageViewImg').alt = caption;
        document.getElementById('imageViewCaption').textContent = caption;
        
        // Show modal
        new bootstrap.Modal(imageModal).show();
    };
});
</script>
