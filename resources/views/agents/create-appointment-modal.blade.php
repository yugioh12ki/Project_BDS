<div class="modal fade" id="createAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-plus me-2"></i>Tạo lịch hẹn mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="appointmentForm" method="POST" action="{{ route('agent.appointments.create') }}" novalidate>
                @csrf
                <div class="modal-body">

                    <!-- Chủ sở hữu Section -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="position-relative">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-person-badge me-1"></i>Chủ sở hữu <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="ownerInput" class="form-control form-control-lg" autocomplete="off"
                                           placeholder="Nhập ít nhất 2 ký tự để tìm kiếm...">
                                    <input type="hidden" id="ownerIdInput" name="OwnerID">
                                    <!-- Owner suggestions dropdown -->
                                    <div id="ownerSuggestions" class="list-group shadow-sm" style="display: none;">
                                        <!-- Dynamic content will be inserted here -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Owner Info Display -->
                                <div id="selectedOwnerInfo" class="owner-info-card" style="display: none;">
                                    <div class="card border-success h-100">
                                        <div class="card-header bg-success text-white py-2">
                                            <h6 class="mb-0"><i class="bi bi-check-circle me-1"></i>Chủ sở hữu đã chọn</h6>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="owner-avatar me-3">
                                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white"
                                                         style="width: 40px; height: 40px; font-weight: bold;">
                                                        <span id="ownerInitials">?</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold" id="selectedOwnerName">Tên chủ sở hữu</div>
                                                    <small class="text-muted" id="selectedOwnerContact">Email | SĐT</small>
                                                    <div><span class="badge bg-primary" id="propertyCountBadge">0 bất động sản</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bất động sản Section -->
                    <div class="mb-4" id="propertySelectionSection" style="display:none;">
                        <label class="form-label fw-bold">
                            <i class="bi bi-buildings me-1"></i>Bất động sản <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-lg" id="propertySelect" name="PropertyID" required>
                            <option value="">-- Vui lòng chọn bất động sản --</option>
                        </select>

                        <!-- Selected Property Info -->
                        <div id="selectedPropertyInfo" class="mt-3" style="display: none;">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white py-2">
                                    <h6 class="mb-0"><i class="bi bi-building me-1"></i>Bất động sản đã chọn</h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="mb-1" id="selectedPropertyTitle">Tiêu đề BĐS</h6>
                                            <p class="mb-1 text-muted">
                                                <i class="bi bi-geo-alt me-1"></i><span id="selectedPropertyAddress">Địa chỉ</span>
                                            </p>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-primary" id="selectedPropertyType">Loại</span>
                                                <span class="badge bg-success" id="selectedPropertyStatus">Trạng thái</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="fw-bold text-primary" id="selectedPropertyPrice">Giá</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Khách hàng Section - Ẩn ban đầu -->
                    <div class="mb-4" id="customerSelectionSection" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="position-relative">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-people me-1"></i>Khách hàng <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-lg" id="customerInput" autocomplete="off"
                                           placeholder="Nhập ít nhất 2 ký tự để tìm kiếm khách hàng...">
                                    <input type="hidden" name="CusID" id="customerIdInput">
                                    <!-- Customer suggestions dropdown -->
                                    <div id="customerSuggestions" class="list-group shadow-sm" style="display: none;">
                                        <!-- Dynamic content will be inserted here -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Customer Info Display -->
                                <div id="selectedCustomerInfo" class="customer-info-card" style="display: none;">
                                    <div class="card border-warning h-100">
                                        <div class="card-header bg-warning text-dark py-2">
                                            <h6 class="mb-0"><i class="bi bi-check-circle me-1"></i>Khách hàng đã chọn</h6>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="customer-avatar me-3">
                                                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center text-dark"
                                                         style="width: 40px; height: 40px; font-weight: bold;">
                                                        <span id="customerInitials">?</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold" id="selectedCustomerName">Tên khách hàng</div>
                                                    <small class="text-muted" id="selectedCustomerContact">Email | SĐT</small>
                                                    <div><span class="badge bg-info" id="customerTypeBadge">Khách hàng</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thời gian hẹn -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calendar-check me-1"></i>Thời gian bắt đầu <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control form-control-lg" name="AppointmentDateStart" id="appointmentDateStart" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calendar-x me-1"></i>Thời gian kết thúc <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control form-control-lg" name="AppointmentDateEnd" id="appointmentDateEnd" required>
                        </div>
                    </div>

                    <!-- Thông tin lịch hẹn -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">
                                <i class="bi bi-chat-text me-1"></i>Tiêu đề lịch hẹn <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" name="TitleAppoint"
                                   placeholder="VD: Hẹn xem nhà và thảo luận hợp đồng..." required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-file-text me-1"></i>Nội dung chi tiết <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" name="DescAppoint" rows="4"
                                  placeholder="Mô tả chi tiết về cuộc hẹn, mục đích, yêu cầu đặc biệt..." required></textarea>
                    </div>

                    <!-- Thông tin tóm tắt cuộc hẹn -->
                    <div id="appointmentSummary" class="appointment-summary-card" style="display: none;">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="bi bi-clipboard-check me-1"></i>Tóm tắt cuộc hẹn</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Chủ sở hữu:</strong> <span id="summaryOwner">-</span><br>
                                        <strong>Khách hàng:</strong> <span id="summaryCustomer">-</span><br>
                                        <strong>Bất động sản:</strong> <span id="summaryProperty">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Ngày hẹn:</strong> <span id="summaryDate">-</span><br>
                                        <strong>Thời gian:</strong> <span id="summaryTime">-</span><br>
                                        <strong>Địa điểm:</strong> <span class="text-muted">Tại bất động sản</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Lưu ý:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Chủ sở hữu và khách hàng sẽ nhận được thông báo về lịch hẹn</li>
                            <li>Lịch hẹn sẽ ở trạng thái "Khởi tạo" chờ chủ sở hữu xác nhận</li>
                            <li>Địa điểm mặc định là tại bất động sản được chọn</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Hủy
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitAppointmentBtn">
                        <i class="bi bi-check-lg me-1"></i>Tạo lịch hẹn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CSS cho modal -->
<style>
.owner-info-card, .customer-info-card, .appointment-summary-card {
    transition: all 0.3s ease;
}

.owner-avatar, .customer-avatar {
    flex-shrink: 0;
}

/* Cải thiện giao diện dropdown suggestions */
#ownerSuggestions, #customerSuggestions {
    background: white !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.375rem !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    z-index: 99999 !important; /* Tăng z-index cao hơn để đảm bảo hiển thị trên cùng */
    position: absolute !important;
    top: calc(100% + 2px) !important; /* Đặt ngay bên dưới input với khoảng cách 2px */
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    max-height: 300px !important;
    overflow-y: auto !important;
    margin: 0 !important; /* Loại bỏ margin để tránh đẩy layout */
    padding: 0 !important;
}

#ownerSuggestions .list-group-item,
#customerSuggestions .list-group-item {
    border: none;
    border-bottom: 1px solid #f1f3f4;
    padding: 12px 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.95rem;
}

#ownerSuggestions .list-group-item:last-child,
#customerSuggestions .list-group-item:last-child {
    border-bottom: none;
    border-radius: 0 0 0.375rem 0.375rem;
}

#ownerSuggestions .list-group-item:first-child,
#customerSuggestions .list-group-item:first-child {
    border-radius: 0.375rem 0.375rem 0 0;
}

#ownerSuggestions .list-group-item:hover,
#customerSuggestions .list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

#ownerSuggestions .list-group-item:active,
#customerSuggestions .list-group-item:active {
    background-color: #e9ecef;
    transform: translateY(0);
}

/* Styling cho các badge trong suggestion */
.owner-suggestion .badge,
.customer-suggestion .badge {
    font-size: 0.75rem;
    padding: 4px 8px;
}

/* Cải thiện input focus */
#ownerInput:focus, #customerInput:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Disabled customer input styling */
#customerInput:disabled {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
    cursor: not-allowed;
}

#customerInput:disabled::placeholder {
    color: #adb5bd;
}

/* Fix để đảm bảo suggestion dropdowns không bị che và không đẩy layout */
.position-relative {
    position: relative !important;
    overflow: visible !important;
}

/* Đảm bảo các container không ảnh hưởng đến dropdown positioning */
.modal-body {
    overflow: visible !important;
    position: relative;
}

.card-body {
    overflow: visible !important;
}

.row {
    overflow: visible !important;
}

.col-md-6 {
    overflow: visible !important;
}

/* Đảm bảo modal có z-index thích hợp */
.modal {
    z-index: 1050;
}

.modal-backdrop {
    z-index: 1040;
}

/* Loading indicator cho suggestions */
.suggestions-loading {
    text-align: center;
    padding: 12px;
    color: #6c757d;
    font-style: italic;
}

.suggestions-loading .spinner-border {
    width: 1rem;
    height: 1rem;
}

/* Empty state cho suggestions */
.suggestions-empty {
    text-align: center;
    padding: 12px;
    color: #6c757d;
    font-style: italic;
}

/* Đảm bảo click events hoạt động trên suggestions */
#ownerSuggestions .list-group-item,
#customerSuggestions .list-group-item {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

/* Responsive cho modal trên màn hình nhỏ */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 10px auto;
    }

    #ownerSuggestions, #customerSuggestions {
        max-height: 200px !important;
    }

    .owner-info-card, .customer-info-card {
        margin-top: 15px;
    }
}

/* Scroll behavior cho suggestions */
#ownerSuggestions, #customerSuggestions {
    scrollbar-width: thin;
    scrollbar-color: #dee2e6 transparent;
}

#ownerSuggestions::-webkit-scrollbar,
#customerSuggestions::-webkit-scrollbar {
    width: 6px;
}

#ownerSuggestions::-webkit-scrollbar-track,
#customerSuggestions::-webkit-scrollbar-track {
    background: transparent;
}

#ownerSuggestions::-webkit-scrollbar-thumb,
#customerSuggestions::-webkit-scrollbar-thumb {
    background-color: #dee2e6;
    border-radius: 3px;
}

#ownerSuggestions::-webkit-scrollbar-thumb:hover,
#customerSuggestions::-webkit-scrollbar-thumb:hover {
    background-color: #adb5bd;
}

/* QUAN TRỌNG: Đảm bảo dropdown không đẩy layout */
#ownerSuggestions, #customerSuggestions {
    /* Loại bỏ khỏi document flow để không ảnh hưởng layout */
    position: absolute !important;
    /* Đảm bảo không chiếm không gian trong layout */
    float: none !important;
    clear: none !important;
    /* Không ảnh hưởng đến chiều cao của container */
    height: auto !important;
    min-height: 0 !important;
}

/* Loading state */
.suggestions-loading {
    text-align: center;
    padding: 16px;
    color: #6c757d;
    font-style: italic;
}

.suggestions-loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* No results state */
.suggestions-empty {
    text-align: center;
    padding: 16px;
    color: #6c757d;
    font-style: italic;
}

/* Suggestion item text styling */
.suggestion-name {
    font-weight: 600;
    color: #212529;
    margin-bottom: 2px;
}

.suggestion-contact {
    font-size: 0.85rem;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 8px;
}

.suggestion-contact span {
    display: flex;
    align-items: center;
    gap: 2px;
}

#propertySelect option {
    padding: 8px 12px;
}

.form-control-lg {
    font-size: 1rem;
    padding: 0.75rem 1rem;
}

.search-highlight {
    background-color: #fff3cd;
    padding: 2px 4px;
    border-radius: 3px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #ownerSuggestions, #customerSuggestions {
        max-height: 250px;
        overflow-y: auto;
    }

    #ownerSuggestions .list-group-item,
    #customerSuggestions .list-group-item {
        padding: 10px 12px;
        font-size: 0.9rem;
    }
}

/* Scrollbar styling for suggestion lists */
#ownerSuggestions::-webkit-scrollbar,
#customerSuggestions::-webkit-scrollbar {
    width: 6px;
}

#ownerSuggestions::-webkit-scrollbar-track,
#customerSuggestions::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#ownerSuggestions::-webkit-scrollbar-thumb,
#customerSuggestions::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#ownerSuggestions::-webkit-scrollbar-thumb:hover,
#customerSuggestions::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>

<!-- JavaScript cho chức năng modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ownerInput = document.getElementById('ownerInput');
    const ownerSuggestions = document.getElementById('ownerSuggestions');
    const ownerIdInput = document.getElementById('ownerIdInput');
    const customerInput = document.getElementById('customerInput');
    const customerSuggestions = document.getElementById('customerSuggestions');
    const customerIdInput = document.getElementById('customerIdInput');
    const propertySelect = document.getElementById('propertySelect');
    const propertySelectionSection = document.getElementById('propertySelectionSection');
    const customerSelectionSection = document.getElementById('customerSelectionSection');
    const selectedOwnerInfo = document.getElementById('selectedOwnerInfo');
    const selectedCustomerInfo = document.getElementById('selectedCustomerInfo');
    const selectedPropertyInfo = document.getElementById('selectedPropertyInfo');
    const appointmentSummary = document.getElementById('appointmentSummary');

    let selectedOwner = null;
    let selectedCustomer = null;
    let selectedProperty = null;

    // Auto set datetime to tomorrow 9 AM - 10 AM
    function setDefaultDateTime() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(9, 0, 0, 0);

        const endTime = new Date(tomorrow);
        endTime.setHours(10, 0, 0, 0);

        document.getElementById('appointmentDateStart').value = tomorrow.toISOString().slice(0, 16);
        document.getElementById('appointmentDateEnd').value = endTime.toISOString().slice(0, 16);
    }

    // Initialize modal
    $('#createAppointmentModal').on('show.bs.modal', function() {
        resetModal();
        setDefaultDateTime();
    });

    function resetModal() {
        ownerInput.value = '';
        ownerIdInput.value = '';
        customerInput.value = '';
        customerIdInput.value = '';
        propertySelect.innerHTML = '<option value="">-- Vui lòng chọn bất động sản --</option>';

        selectedOwnerInfo.style.display = 'none';
        selectedCustomerInfo.style.display = 'none';
        selectedPropertyInfo.style.display = 'none';
        propertySelectionSection.style.display = 'none';
        customerSelectionSection.style.display = 'none'; // Ẩn section khách hàng từ đầu
        appointmentSummary.style.display = 'none';

        selectedOwner = null;
        selectedCustomer = null;
        selectedProperty = null;
    }

    // Owner search functionality
    let ownerSearchTimeout;
    ownerInput.addEventListener('input', function() {
        clearTimeout(ownerSearchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            ownerSuggestions.style.display = 'none';
            selectedOwnerInfo.style.display = 'none';
            propertySelectionSection.style.display = 'none';
            selectedPropertyInfo.style.display = 'none';
            customerSelectionSection.style.display = 'none'; // Ẩn section khách hàng
            ownerIdInput.value = '';
            selectedOwner = null;
            updateSummary();
            return;
        }

        ownerSearchTimeout = setTimeout(() => {
            searchOwners(query);
        }, 300);
    });

    function searchOwners(query) {
        // Show loading state
        ownerSuggestions.innerHTML = '<div class="list-group-item suggestions-loading"><i class="bi bi-hourglass-split me-2"></i>Đang tìm kiếm...</div>';
        ownerSuggestions.style.display = 'block';

        fetch(`/agent/search-owners?term=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displayOwnerSuggestions(data.owners || []);
            })
            .catch(error => {
                console.error('Error searching owners:', error);
                ownerSuggestions.innerHTML = '<div class="list-group-item text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Lỗi tìm kiếm</div>';
                ownerSuggestions.style.display = 'block';
            });
    }

    function displayOwnerSuggestions(owners) {
        if (owners.length === 0) {
            ownerSuggestions.innerHTML = '<div class="list-group-item suggestions-empty"><i class="bi bi-search me-2"></i>Không tìm thấy chủ sở hữu phù hợp</div>';
            setTimeout(() => {
                adjustSuggestionPosition(ownerInput, ownerSuggestions);
            }, 0);
            return;
        }

        let html = '';
        owners.forEach(owner => {
            const initials = owner.Name.substring(0, 2).toUpperCase();
            const contactInfo = [];
            if (owner.Email) contactInfo.push(`<i class="bi bi-envelope me-1"></i>${owner.Email}`);
            if (owner.Phone) contactInfo.push(`<i class="bi bi-telephone me-1"></i>${owner.Phone}`);

            html += `
                <div class="list-group-item list-group-item-action owner-suggestion"
                     data-owner-id="${owner.UserID}"
                     data-owner-name="${owner.Name}"
                     data-owner-email="${owner.Email || ''}"
                     data-owner-phone="${owner.Phone || ''}"
                     style="cursor: pointer; user-select: none;">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white"
                                 style="width: 40px; height: 40px; font-weight: bold; font-size: 0.85rem;">
                                ${initials}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="suggestion-name">${owner.Name}</div>
                            <div class="suggestion-contact">
                                ${contactInfo.join(' • ')}
                            </div>
                        </div>
                        <div class="ms-2">
                            <span class="badge bg-primary">${owner.property_count || 0} BĐS</span>
                        </div>
                    </div>
                </div>
            `;
        });

        ownerSuggestions.innerHTML = html;
        // Sử dụng setTimeout để đảm bảo DOM được update trước khi positioning
        setTimeout(() => {
            adjustSuggestionPosition(ownerInput, ownerSuggestions);
        }, 0);

        // Add click handlers for owner suggestions
        ownerSuggestions.querySelectorAll('.owner-suggestion').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                selectOwner({
                    UserID: this.dataset.ownerId,
                    Name: this.dataset.ownerName,
                    Email: this.dataset.ownerEmail,
                    Phone: this.dataset.ownerPhone
                });
            });
        });
    }

    function selectOwner(owner) {
        selectedOwner = owner;
        ownerInput.value = owner.Name;
        ownerIdInput.value = owner.UserID;
        ownerSuggestions.style.display = 'none';

        // Display owner info
        document.getElementById('ownerInitials').textContent = owner.Name.substring(0, 2).toUpperCase();
        document.getElementById('selectedOwnerName').textContent = owner.Name;
        document.getElementById('selectedOwnerContact').textContent = `${owner.Email || 'N/A'} | ${owner.Phone || 'N/A'}`;
        selectedOwnerInfo.style.display = 'block';

        // Reset customer selection when owner changes
        customerInput.value = '';
        selectedCustomerInfo.style.display = 'none';
        customerSelectionSection.style.display = 'none'; // Ẩn section khách hàng cho đến khi chọn property
        selectedCustomer = null;

        // Load properties for this owner
        loadOwnerProperties(owner.UserID);
        updateSummary();
    }

    function loadOwnerProperties(ownerId) {
        fetch(`/agent/search-properties?owner_id=${ownerId}`)
            .then(response => response.json())
            .then(data => {
                const properties = data || [];

                propertySelect.innerHTML = '<option value="">-- Vui lòng chọn bất động sản --</option>';

                if (properties.length === 0) {
                    propertySelect.innerHTML += '<option value="" disabled>Chủ sở hữu chưa có bất động sản được phân công</option>';
                } else {
                    properties.forEach(property => {
                        propertySelect.innerHTML += `<option value="${property.id}">${property.title}</option>`;
                    });
                }

                propertySelectionSection.style.display = 'block';
                document.getElementById('propertyCountBadge').textContent = `${properties.length} bất động sản`;
            })
            .catch(error => {
                console.error('Error loading properties:', error);
                propertySelect.innerHTML = '<option value="" disabled>Lỗi tải danh sách bất động sản</option>';
                propertySelectionSection.style.display = 'block';
            });
    }

    // Property selection
    propertySelect.addEventListener('change', function() {
        if (this.value) {
            // Find property details (you might need to store them when loading)
            selectedProperty = {
                id: this.value,
                title: this.options[this.selectedIndex].text
            };

            document.getElementById('selectedPropertyTitle').textContent = selectedProperty.title;
            document.getElementById('selectedPropertyAddress').textContent = 'Địa chỉ BĐS'; // You might need to load this
            document.getElementById('selectedPropertyType').textContent = 'Loại BĐS'; // You might need to load this
            document.getElementById('selectedPropertyStatus').textContent = 'Đang hoạt động';
            document.getElementById('selectedPropertyPrice').textContent = 'Giá theo thỏa thuận';

            selectedPropertyInfo.style.display = 'block';

            // Hiển thị section khách hàng và enable input sau khi chọn property
            customerSelectionSection.style.display = 'block';
        } else {
            selectedPropertyInfo.style.display = 'none';
            selectedProperty = null;

            // Ẩn section khách hàng nếu không chọn property
            customerSelectionSection.style.display = 'none';
            customerInput.value = '';
            selectedCustomerInfo.style.display = 'none';
            selectedCustomer = null;
        }
        updateSummary();
    });

    // Customer search functionality
    let customerSearchTimeout;
    customerInput.addEventListener('input', function() {
        clearTimeout(customerSearchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            customerSuggestions.style.display = 'none';
            selectedCustomerInfo.style.display = 'none';
            customerIdInput.value = '';
            selectedCustomer = null;
            updateSummary();
            return;
        }

        customerSearchTimeout = setTimeout(() => {
            searchCustomers(query);
        }, 300);
    });

    function searchCustomers(query) {
        const propertyId = propertySelect.value;
        let url = `/agent/search-customers?query=${encodeURIComponent(query)}`;
        if (propertyId) {
            url += `&propertyId=${propertyId}`;
        }

        // Show loading state
        customerSuggestions.innerHTML = '<div class="list-group-item suggestions-loading"><i class="bi bi-hourglass-split me-2"></i>Đang tìm kiếm...</div>';
        customerSuggestions.style.display = 'block';

        fetch(url)
            .then(response => response.json())
            .then(data => {
                displayCustomerSuggestions(data.customers || []);
            })
            .catch(error => {
                console.error('Error searching customers:', error);
                customerSuggestions.innerHTML = '<div class="list-group-item text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Lỗi tìm kiếm</div>';
                customerSuggestions.style.display = 'block';
            });
    }

    function displayCustomerSuggestions(customers) {
        if (customers.length === 0) {
            customerSuggestions.innerHTML = '<div class="list-group-item suggestions-empty"><i class="bi bi-search me-2"></i>Không tìm thấy khách hàng phù hợp</div>';
            setTimeout(() => {
                adjustSuggestionPosition(customerInput, customerSuggestions);
            }, 0);
            return;
        }

        let html = '';
        customers.forEach(customer => {
            const initials = customer.name.substring(0, 2).toUpperCase();
            const contactInfo = [];
            if (customer.email) contactInfo.push(`<i class="bi bi-envelope me-1"></i>${customer.email}`);
            if (customer.phone) contactInfo.push(`<i class="bi bi-telephone me-1"></i>${customer.phone}`);

            html += `
                <div class="list-group-item list-group-item-action customer-suggestion"
                     data-customer-id="${customer.id}"
                     data-customer-name="${customer.name}"
                     data-customer-email="${customer.email || ''}"
                     data-customer-phone="${customer.phone || ''}"
                     style="cursor: pointer; user-select: none;">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center text-dark"
                                 style="width: 40px; height: 40px; font-weight: bold; font-size: 0.85rem;">
                                ${initials}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="suggestion-name">${customer.name}</div>
                            <div class="suggestion-contact">
                                ${contactInfo.join(' • ')}
                            </div>
                        </div>
                        <div class="ms-2">
                            <span class="badge bg-info">Khách hàng</span>
                        </div>
                    </div>
                </div>
            `;
        });

        customerSuggestions.innerHTML = html;
        // Sử dụng setTimeout để đảm bảo DOM được update trước khi positioning
        setTimeout(() => {
            adjustSuggestionPosition(customerInput, customerSuggestions);
        }, 0);

        // Add click handlers for customer suggestions
        customerSuggestions.querySelectorAll('.customer-suggestion').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                selectCustomer({
                    id: this.dataset.customerId,
                    name: this.dataset.customerName,
                    email: this.dataset.customerEmail,
                    phone: this.dataset.customerPhone
                });
            });
        });
    }

    function selectCustomer(customer) {
        selectedCustomer = customer;
        customerInput.value = customer.name;
        customerIdInput.value = customer.id;
        customerSuggestions.style.display = 'none';

        // Display customer info
        document.getElementById('customerInitials').textContent = customer.name.substring(0, 2).toUpperCase();
        document.getElementById('selectedCustomerName').textContent = customer.name;
        document.getElementById('selectedCustomerContact').textContent = `${customer.email || 'N/A'} | ${customer.phone || 'N/A'}`;
        selectedCustomerInfo.style.display = 'block';

        updateSummary();
    }

    function updateSummary() {
        if (selectedOwner && selectedCustomer && selectedProperty) {
            document.getElementById('summaryOwner').textContent = selectedOwner.Name;
            document.getElementById('summaryCustomer').textContent = selectedCustomer.name;
            document.getElementById('summaryProperty').textContent = selectedProperty.title;

            const startDateTime = document.getElementById('appointmentDateStart').value;
            if (startDateTime) {
                const date = new Date(startDateTime);
                document.getElementById('summaryDate').textContent = date.toLocaleDateString('vi-VN');
                document.getElementById('summaryTime').textContent = date.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});
            }

            appointmentSummary.style.display = 'block';
        } else {
            appointmentSummary.style.display = 'none';
        }
    }

    // Update summary when datetime changes
    document.getElementById('appointmentDateStart').addEventListener('change', updateSummary);

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!ownerInput.contains(e.target) && !ownerSuggestions.contains(e.target)) {
            ownerSuggestions.style.display = 'none';
        }
        if (!customerInput.contains(e.target) && !customerSuggestions.contains(e.target)) {
            customerSuggestions.style.display = 'none';
        }
    });

    // Add keyboard navigation for suggestions
    let ownerSelectedIndex = -1;
    let customerSelectedIndex = -1;

    ownerInput.addEventListener('keydown', function(e) {
        const suggestions = ownerSuggestions.querySelectorAll('.owner-suggestion');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            ownerSelectedIndex = Math.min(ownerSelectedIndex + 1, suggestions.length - 1);
            updateOwnerHighlight(suggestions);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            ownerSelectedIndex = Math.max(ownerSelectedIndex - 1, -1);
            updateOwnerHighlight(suggestions);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (ownerSelectedIndex >= 0 && suggestions[ownerSelectedIndex]) {
                suggestions[ownerSelectedIndex].click();
            }
        } else if (e.key === 'Escape') {
            ownerSuggestions.style.display = 'none';
            ownerSelectedIndex = -1;
        }
    });

    customerInput.addEventListener('keydown', function(e) {
        const suggestions = customerSuggestions.querySelectorAll('.customer-suggestion');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            customerSelectedIndex = Math.min(customerSelectedIndex + 1, suggestions.length - 1);
            updateCustomerHighlight(suggestions);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            customerSelectedIndex = Math.max(customerSelectedIndex - 1, -1);
            updateCustomerHighlight(suggestions);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (customerSelectedIndex >= 0 && suggestions[customerSelectedIndex]) {
                suggestions[customerSelectedIndex].click();
            }
        } else if (e.key === 'Escape') {
            customerSuggestions.style.display = 'none';
            customerSelectedIndex = -1;
        }
    });

    function updateOwnerHighlight(suggestions) {
        suggestions.forEach((item, index) => {
            if (index === ownerSelectedIndex) {
                item.style.backgroundColor = '#e9ecef';
            } else {
                item.style.backgroundColor = '';
            }
        });
    }

    function updateCustomerHighlight(suggestions) {
        suggestions.forEach((item, index) => {
            if (index === customerSelectedIndex) {
                item.style.backgroundColor = '#e9ecef';
            } else {
                item.style.backgroundColor = '';
            }
        });
    }

    // Form submission with AJAX and enhanced validation
    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate required fields
        if (!ownerIdInput.value || !customerIdInput.value || !propertySelect.value) {
            showAlert('Vui lòng chọn đầy đủ chủ sở hữu, bất động sản và khách hàng', 'error');
            return false;
        }

        // Validate datetime fields
        const startTime = document.getElementById('appointmentDateStart').value;
        const endTime = document.getElementById('appointmentDateEnd').value;
        const titleAppoint = document.querySelector('input[name="TitleAppoint"]').value;
        const descAppoint = document.querySelector('textarea[name="DescAppoint"]').value;

        if (!startTime || !endTime) {
            showAlert('Vui lòng chọn thời gian bắt đầu và kết thúc', 'error');
            return false;
        }

        if (!titleAppoint.trim()) {
            showAlert('Vui lòng nhập tiêu đề cuộc hẹn', 'error');
            return false;
        }

        if (!descAppoint.trim()) {
            showAlert('Vui lòng nhập mô tả chi tiết cuộc hẹn', 'error');
            return false;
        }

        // Check if start time is in the future
        const now = new Date();
        const appointmentStart = new Date(startTime);
        if (appointmentStart <= now) {
            showAlert('Thời gian bắt đầu phải sau thời điểm hiện tại', 'error');
            return false;
        }

        // Check if end time is after start time
        const appointmentEnd = new Date(endTime);
        if (appointmentEnd <= appointmentStart) {
            showAlert('Thời gian kết thúc phải sau thời gian bắt đầu', 'error');
            return false;
        }

        // Show loading state
        const submitBtn = document.getElementById('submitAppointmentBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Đang tạo...';

        // Prepare form data
        const formData = new FormData(this);

        // Submit via AJAX
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                               document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert(data.message, 'success');

                // Show appointment details if available
                if (data.appointment) {
                    setTimeout(() => {
                        showAppointmentCreatedDetails(data.appointment);
                    }, 1000);
                }

                // Reset form and close modal after delay
                setTimeout(() => {
                    $('#createAppointmentModal').modal('hide');
                    resetModal();

                    // Reload page to show updated data
                    if (typeof location !== 'undefined') {
                        location.reload();
                    }
                }, 3000);

            } else {
                showAlert(data.message || 'Có lỗi xảy ra khi tạo lịch hẹn', 'error');
            }
        })
        .catch(error => {
            console.error('Error creating appointment:', error);
            showAlert('Lỗi kết nối. Vui lòng thử lại sau.', 'error');
        })
        .finally(() => {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Function to show alerts
    function showAlert(message, type = 'info') {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.custom-alert');
        existingAlerts.forEach(alert => alert.remove());

        const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
        const iconClass = type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-exclamation-triangle' : 'bi-info-circle';

        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show custom-alert" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 999999; min-width: 300px;">
                <i class="bi ${iconClass} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', alertHtml);

        // Auto remove after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.custom-alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    // Function to show appointment created details
    function showAppointmentCreatedDetails(appointment) {
        const detailsHtml = `
            <div class="alert alert-info alert-dismissible fade show custom-alert" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 999998; min-width: 350px;">
                <h6 class="alert-heading"><i class="bi bi-calendar-check me-2"></i>Chi tiết cuộc hẹn</h6>
                <hr>
                <p class="mb-1"><strong>Mã cuộc hẹn:</strong> #${appointment.id}</p>
                <p class="mb-1"><strong>Tiêu đề:</strong> ${appointment.title}</p>
                <p class="mb-1"><strong>Trạng thái:</strong> <span class="badge bg-warning text-dark">${appointment.status}</span></p>
                <p class="mb-1"><strong>Thời gian:</strong> ${appointment.start_time} - ${appointment.end_time}</p>
                <p class="mb-1"><strong>Bất động sản:</strong> ${appointment.property_title}</p>
                <p class="mb-1"><strong>Khách hàng:</strong> ${appointment.customer_name}</p>
                <p class="mb-0"><strong>Chủ sở hữu:</strong> ${appointment.owner_name}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', detailsHtml);

        // Auto remove after 8 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.custom-alert');
            alerts.forEach(alert => {
                if (alert.querySelector('.alert-heading')) {
                    alert.remove();
                }
            });
        }, 8000);
    }

    // Đảm bảo dropdown positioning đúng khi modal được mở
    document.getElementById('createAppointmentModal').addEventListener('shown.bs.modal', function() {
        // Reset tất cả dropdowns
        ownerSuggestions.style.display = 'none';
        customerSuggestions.style.display = 'none';

        // Đảm bảo modal body có thể scroll và overflow visible
        const modalBody = this.querySelector('.modal-body');
        if (modalBody) {
            modalBody.style.overflow = 'visible';
        }
    });

    // Hide suggestions khi click outside
    document.addEventListener('click', function(e) {
        if (!ownerInput.contains(e.target) && !ownerSuggestions.contains(e.target)) {
            ownerSuggestions.style.display = 'none';
            ownerSelectedIndex = -1;
        }

        if (!customerInput.contains(e.target) && !customerSuggestions.contains(e.target)) {
            customerSuggestions.style.display = 'none';
            customerSelectedIndex = -1;
        }
    });

    // Đảm bảo suggestions hiển thị đúng vị trí và không đẩy layout
    function adjustSuggestionPosition(input, suggestions) {
        // Đảm bảo suggestions được hiển thị trước khi tính toán position
        suggestions.style.display = 'block';
        suggestions.style.visibility = 'hidden'; // Ẩn tạm để tính toán

        // Reset position để tính toán chính xác
        suggestions.style.top = 'calc(100% + 2px)';
        suggestions.style.bottom = 'auto';
        suggestions.style.position = 'absolute';
        suggestions.style.left = '0';
        suggestions.style.right = '0';
        suggestions.style.width = '100%';
        suggestions.style.zIndex = '99999';

        // Force layout calculation
        suggestions.offsetHeight;

        const inputRect = input.getBoundingClientRect();
        const modalBody = document.querySelector('#createAppointmentModal .modal-body');
        const modalBodyRect = modalBody ? modalBody.getBoundingClientRect() : { bottom: window.innerHeight };
        const suggestionsRect = suggestions.getBoundingClientRect();

        // Kiểm tra xem có đủ chỗ hiển thị dropdown bên dưới không
        const spaceBelow = modalBodyRect.bottom - inputRect.bottom - 10; // 10px buffer
        const suggestionsHeight = Math.min(300, suggestionsRect.height);

        if (spaceBelow < suggestionsHeight && inputRect.top - modalBodyRect.top > suggestionsHeight) {
            // Hiển thị phía trên input
            suggestions.style.top = 'auto';
            suggestions.style.bottom = 'calc(100% + 2px)';
        } else {
            // Hiển thị phía dưới input (mặc định)
            suggestions.style.top = 'calc(100% + 2px)';
            suggestions.style.bottom = 'auto';
        }

        // Hiển thị lại suggestions
        suggestions.style.visibility = 'visible';
    }
});
</script>
