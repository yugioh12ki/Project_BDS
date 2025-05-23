@extends('_layout._layadmin.app')
@section('appointment')
@if(session('success'))
    <div class="alert alert-success">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif
@if(isset($error))
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-circle"></i> {{ $error }}
    </div>
@else
<h1>Quản lý Lịch hẹn</h1>


<div class="appointment-layout">
    <!-- Cột 1: Danh sách môi giới (Agent) -->
    <div class="agent-list">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">Danh sách môi giới</h4>
        </div>
        <div class="small text-muted mb-3">
            <i class="fas fa-sort-amount-up-alt me-1"></i> Sắp xếp theo số lượng phân công (ít → nhiều)
        </div>
        <div class="list-group agent-items">
            @foreach($agents as $agent)
            <a href="#" class="list-group-item list-group-item-action agent-item"
               data-agent-id="{{ $agent->UserID }}">
                {{ $agent->Name }}
            </a>
            @endforeach
        </div>
        <!-- Phân trang cho danh sách môi giới -->
        <div class="agent-pagination mt-3">
            {{ $agents->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Cột 2: Danh sách lịch hẹn -->
    <div class="appointment-lists">
        <div class="appointment-tabs">
            <ul class="nav nav-tabs" id="appointmentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="with-owners-tab" data-bs-toggle="tab"
                            data-bs-target="#with-owners" type="button" role="tab"
                            aria-controls="with-owners" aria-selected="true">
                        Cuộc hẹn với chủ sở hữu
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="with-customers-tab" data-bs-toggle="tab"
                            data-bs-target="#with-customers" type="button" role="tab"
                            aria-controls="with-customers" aria-selected="false">
                        Cuộc hẹn với khách hàng
                    </button>
                </li>
            </ul>

            <div class="tab-content p-3 border border-top-0 rounded-bottom bg-white" id="appointmentTabContent">
                <!-- Tab: Cuộc hẹn với chủ sở hữu -->
                <div class="tab-pane fade show active" id="with-owners" role="tabpanel" aria-labelledby="with-owners-tab">
                    <div class="owners-appointments-list">
                        <p class="initial-message text-center py-4">Vui lòng chọn môi giới để xem danh sách cuộc hẹn</p>
                        <div class="owner-appointments-content d-none">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tiêu đề</th>
                                        <th>Mô tả</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày kết thúc</th>
                                        <th>Trạng thái</th>
                                        <th>Chức năng</th>
                                    </tr>
                                </thead>
                                <tbody id="owners-appointments-body">
                                    <!-- Dữ liệu sẽ được thêm vào bằng JavaScript -->
                                </tbody>
                            </table>
                            <!-- Phân trang cho cuộc hẹn với chủ sở hữu -->
                            <div class="owners-pagination pagination-container mt-3 d-flex justify-content-between align-items-center">
                                <div class="pagination-info">Hiển thị <span id="owner-items-showing">0</span> / <span id="owner-items-total">0</span> cuộc hẹn</div>
                                <div class="pagination-controls">
                                    <button class="btn btn-sm btn-outline-secondary me-1" id="owner-prev-page" disabled>
                                        <i class="fas fa-chevron-left"></i> Trước
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" id="owner-next-page" disabled>
                                        Sau <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Cuộc hẹn với khách hàng -->
                <div class="tab-pane fade" id="with-customers" role="tabpanel" aria-labelledby="with-customers-tab">
                    <div class="customers-appointments-list">
                        <p class="initial-message text-center py-4">Vui lòng chọn môi giới để xem danh sách cuộc hẹn</p>
                        <div class="customer-appointments-content d-none">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tiêu đề</th>
                                        <th>Mô tả</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày kết thúc</th>
                                        <th>Trạng thái</th>
                                        <th>Chức năng</th>
                                    </tr>
                                </thead>
                                <tbody id="customers-appointments-body">
                                    <!-- Dữ liệu sẽ được thêm vào bằng JavaScript -->
                                </tbody>
                            </table>
                            <!-- Phân trang cho cuộc hẹn với khách hàng -->
                            <div class="customers-pagination pagination-container mt-3 d-flex justify-content-between align-items-center">
                                <div class="pagination-info">Hiển thị <span id="customer-items-showing">0</span> / <span id="customer-items-total">0</span> cuộc hẹn</div>
                                <div class="pagination-controls">
                                    <button class="btn btn-sm btn-outline-secondary me-1" id="customer-prev-page" disabled>
                                        <i class="fas fa-chevron-left"></i> Trước
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" id="customer-next-page" disabled>
                                        Sau <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Template cho Chi tiết cuộc hẹn -->
<div class="modal fade" id="appointmentDetailModal" tabindex="-1" aria-labelledby="appointmentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentDetailModalLabel">Chi tiết cuộc hẹn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="appointmentDetail">
                <!-- Nội dung chi tiết sẽ được thêm vào bằng JavaScript -->
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger btn-sm delete-appointment-btn">
                    <i class="fa fa-trash"></i> Xóa
                </button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xác nhận xóa -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                Bạn có chắc chắn muốn xóa cuộc hẹn này?
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteAppointmentForm" action="" method="POST" class="delete-appointment-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endif
@endsection

<style>
/* Styling for agent list */
.agent-item {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.agent-item:hover {
    background-color: #f0f7ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    border-left: 3px solid #83b4ff;
}

.agent-item.active {
    background-color: #e9f5ff;
    border-left: 3px solid #0d6efd;
    box-shadow: 0 0 15px rgba(13, 110, 253, 0.15);
}

/* Agent pagination styling */
.agent-pagination {
    margin-top: 15px;
}

.agent-pagination .pagination {
    justify-content: center;
    margin-bottom: 0;
}

.agent-pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.agent-pagination .page-link {
    color: #0d6efd;
    transition: all 0.2s ease;
}

.agent-pagination .page-link:hover {
    background-color: #e9f5ff;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý khi chọn môi giới
    const agentItems = document.querySelectorAll('.agent-item');
    let currentAppointmentId = null;

    // Thêm biến cho phân trang
    let allOwnerAppointments = [];
    let allCustomerAppointments = [];
    let currentOwnerPage = 1;
    let currentCustomerPage = 1;
    const itemsPerPage = 10; // Số lượng cuộc hẹn mỗi trang

    agentItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();

            // Xóa trạng thái active của tất cả các mục
            agentItems.forEach(item => item.classList.remove('active'));

            // Đặt active cho mục đang chọn
            this.classList.add('active');

            const agentId = this.getAttribute('data-agent-id');
            fetchAppointmentsByAgent(agentId);
        });
    });

    // Hàm lấy danh sách cuộc hẹn theo môi giới
    function fetchAppointmentsByAgent(agentId) {
        // Hiển thị thông báo đang tải
        document.querySelector('#owners-appointments-body').innerHTML = '<tr><td colspan="6" class="text-center">Đang tải dữ liệu...</td></tr>';
        document.querySelector('#customers-appointments-body').innerHTML = '<tr><td colspan="6" class="text-center">Đang tải dữ liệu...</td></tr>';

        // Ẩn thông báo ban đầu và hiển thị bảng
        document.querySelectorAll('.initial-message').forEach(el => el.classList.add('d-none'));
        document.querySelector('.owner-appointments-content').classList.remove('d-none');
        document.querySelector('.customer-appointments-content').classList.remove('d-none');

        // Reset phân trang
        currentOwnerPage = 1;
        currentCustomerPage = 1;

        // Gọi API để lấy dữ liệu
        fetch(`/admin/appointment/agent/${agentId}`)
            .then(response => response.json())
            .then(data => {
                // Lưu toàn bộ dữ liệu cuộc hẹn
                allOwnerAppointments = data.withOwners;
                allCustomerAppointments = data.withCustomers;

                // Cập nhật UI phân trang
                updatePaginationInfo('owner', allOwnerAppointments.length);
                updatePaginationInfo('customer', allCustomerAppointments.length);

                // Hiển thị dữ liệu trang đầu tiên
                renderAppointmentsPage('owners-appointments-body', allOwnerAppointments, currentOwnerPage, itemsPerPage);
                renderAppointmentsPage('customers-appointments-body', allCustomerAppointments, currentCustomerPage, itemsPerPage);

                // Cập nhật trạng thái nút phân trang
                updatePaginationButtons();
            })
            .catch(error => {
                console.error('Lỗi khi tải dữ liệu:', error);
                document.querySelector('#owners-appointments-body').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Lỗi khi tải dữ liệu</td></tr>';
                document.querySelector('#customers-appointments-body').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Lỗi khi tải dữ liệu</td></tr>';

                // Ẩn phân trang
                document.querySelector('.owners-pagination').classList.add('d-none');
                document.querySelector('.customers-pagination').classList.add('d-none');
            });
    }

    // Hàm hiển thị danh sách cuộc hẹn
    function renderAppointments(appointments, targetId) {
        const tableBody = document.getElementById(targetId);

        if (!appointments || appointments.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Không có cuộc hẹn nào</td></tr>';
            return;
        }

        let html = '';
        appointments.forEach(appointment => {
            // Xử lý an toàn cho ngày
            let startDate, endDate;
            try {
                startDate = appointment.AppointmentDateStart ?
                    new Date(appointment.AppointmentDateStart).toLocaleString('vi-VN') : 'Chưa xác định';
                endDate = appointment.AppointmentDateEnd ?
                    new Date(appointment.AppointmentDateEnd).toLocaleString('vi-VN') : 'Chưa xác định';
            } catch (error) {
                console.error('Lỗi định dạng ngày:', error);
                startDate = 'Định dạng không hợp lệ';
                endDate = 'Định dạng không hợp lệ';
            }

            const statusClass = appointment.Status === 'Hoàn thành' ? 'bg-success' :
                              (appointment.Status === 'Đang chờ' ? 'bg-warning' : 'bg-secondary');

            // Cắt ngắn mô tả nếu quá dài
            const desc = appointment.DescAppoint || '';
            const shortDesc = desc.length > 100 ? desc.substring(0, 100) + '...' : desc;

            html += `
                <tr>
                    <td>${appointment.TitleAppoint || ''}</td>
                    <td>${shortDesc}</td>
                    <td>${startDate}</td>
                    <td>${endDate}</td>
                    <td><span class="badge ${statusClass}">${appointment.Status || 'Đang chờ'}</span></td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm view-appointment"
                                data-appointment-id="${appointment.AppointmentID}">
                            <i class="fas fa-eye"></i> Xem
                        </button>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;

        // Cập nhật thông tin phân trang
        updatePaginationInfo('owners', appointments.length);

        // Gán sự kiện cho các nút xem chi tiết
        document.querySelectorAll('.view-appointment').forEach(button => {
            button.addEventListener('click', function() {
                const appointmentId = this.getAttribute('data-appointment-id');
                showAppointmentDetail(appointmentId);
            });
        });
    }

    // Hàm hiển thị danh sách cuộc hẹn (phân trang)
    function renderAppointmentsPage(targetId, appointments, currentPage, itemsPerPage) {
        const tableBody = document.getElementById(targetId);

        if (!appointments || appointments.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Không có cuộc hẹn nào</td></tr>';
            return;
        }

        // Tính toán chỉ số bắt đầu và kết thúc cho trang hiện tại
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, appointments.length);
        const currentPageItems = appointments.slice(startIndex, endIndex);

        // Cập nhật thông tin hiển thị
        const type = targetId === 'owners-appointments-body' ? 'owner' : 'customer';
        document.getElementById(`${type}-items-showing`).textContent = `${startIndex + 1}-${endIndex}`;
        document.getElementById(`${type}-items-total`).textContent = appointments.length;

        let html = '';
        currentPageItems.forEach(appointment => {
            // Xử lý an toàn cho ngày
            let startDate, endDate;
            try {
                startDate = appointment.AppointmentDateStart ?
                    new Date(appointment.AppointmentDateStart).toLocaleString('vi-VN') : 'Chưa xác định';
                endDate = appointment.AppointmentDateEnd ?
                    new Date(appointment.AppointmentDateEnd).toLocaleString('vi-VN') : 'Chưa xác định';
            } catch (error) {
                console.error('Lỗi định dạng ngày:', error);
                startDate = 'Định dạng không hợp lệ';
                endDate = 'Định dạng không hợp lệ';
            }

            const statusClass = appointment.Status === 'Hoàn thành' ? 'bg-success' :
                             (appointment.Status === 'Đang chờ' ? 'bg-warning' : 'bg-secondary');

            // Cắt ngắn mô tả nếu quá dài
            const desc = appointment.DescAppoint || '';
            const shortDesc = desc.length > 100 ? desc.substring(0, 100) + '...' : desc;

            html += `
                <tr>
                    <td>${appointment.TitleAppoint || ''}</td>
                    <td>${shortDesc}</td>
                    <td>${startDate}</td>
                    <td>${endDate}</td>
                    <td><span class="badge ${statusClass}">${appointment.Status || 'Đang chờ'}</span></td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm view-appointment"
                                data-appointment-id="${appointment.AppointmentID}">
                            <i class="fas fa-eye"></i> Xem
                        </button>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;

        // Gán sự kiện cho các nút xem chi tiết
        document.querySelectorAll('.view-appointment').forEach(button => {
            button.addEventListener('click', function() {
                const appointmentId = this.getAttribute('data-appointment-id');
                showAppointmentDetail(appointmentId);
            });
        });
    }

    // Hàm cập nhật thông tin phân trang
    function updatePaginationInfo(type, totalItems) {
        const itemsPerPage = 10; // Số mục hiển thị trên mỗi trang
        const currentPage = 1; // Trang hiện tại, sẽ được cập nhật khi người dùng chuyển trang

        // Tính toán tổng số trang
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        // Cập nhật thông tin hiển thị
        document.getElementById(`${type}-items-showing`).innerText = Math.min(itemsPerPage, totalItems);
        document.getElementById(`${type}-items-total`).innerText = totalItems;

        // Kích hoạt hoặc vô hiệu hóa nút phân trang
        document.getElementById(`${type}-prev-page`).disabled = (currentPage === 1);
        document.getElementById(`${type}-next-page`).disabled = (currentPage === totalPages);
    }

    // Hàm hiển thị chi tiết cuộc hẹn
    function showAppointmentDetail(appointmentId) {
        currentAppointmentId = appointmentId;

        // Hiển thị thông báo loading trong modal
        document.getElementById('appointmentDetail').innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</div>';

        // Hiển thị modal ngay lập tức để người dùng thấy loading
        try {
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap is undefined! Using jQuery fallback if available.');
                // Fallback sử dụng jQuery nếu có
                if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
                    $('#appointmentDetailModal').modal('show');
                } else {
                    document.getElementById('appointmentDetailModal').style.display = 'block';
                    document.getElementById('appointmentDetailModal').classList.add('show');
                }
            } else {
                const modal = new bootstrap.Modal(document.getElementById('appointmentDetailModal'));
                modal.show();
            }
        } catch (error) {
            console.error('Error showing modal:', error);
            // Fallback nếu không thể hiển thị modal
            document.getElementById('appointmentDetailModal').style.display = 'block';
            document.getElementById('appointmentDetailModal').classList.add('show');
        }

        // Tải dữ liệu chi tiết bằng AJAX
        fetch(`/admin/appointment/detail/${appointmentId}`)
            .then(response => response.json())
            .then(data => {
                const appointment = data.appointment;

                // Format dates safely
                let startDate, endDate;
                try {
                    startDate = appointment.AppointmentDateStart ?
                        new Date(appointment.AppointmentDateStart).toLocaleString('vi-VN') : 'Chưa xác định';
                    endDate = appointment.AppointmentDateEnd ?
                        new Date(appointment.AppointmentDateEnd).toLocaleString('vi-VN') : 'Chưa xác định';
                } catch (error) {
                    console.error('Lỗi định dạng ngày:', error);
                    startDate = 'Định dạng không hợp lệ';
                    endDate = 'Định dạng không hợp lệ';
                }

                // Determine status class for badge
                const statusClass = appointment.Status === 'Hoàn thành' ? 'bg-success' :
                                   (appointment.Status === 'Đang chờ' ? 'bg-warning' : 'bg-secondary');

                // Build HTML for appointment detail
                const html = `
                <div class="appointment-detail">
                    <!-- Thông tin ID cuộc hẹn -->
                    <div class="mb-2">
                        <span class="badge bg-secondary">ID: ${appointment.AppointmentID}</span>
                    </div>

                    <!-- Thông tin chính cuộc hẹn -->
                    <div class="mb-3">
                        <h6 class="text-primary fw-bold mb-0">${appointment.TitleAppoint || 'Không có tiêu đề'}</h6>
                        <small class="text-muted">
                            Trạng thái:
                            <span class="badge ${statusClass}">
                                ${appointment.Status || 'ĐANG CHỜ'}
                            </span>
                        </small>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Thời gian cuộc hẹn -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <div>
                                    <div class="small fw-bold">Bắt đầu</div>
                                    <div>${startDate}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar-check text-primary me-2"></i>
                                <div>
                                    <div class="small fw-bold">Kết thúc</div>
                                    <div>${endDate}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Các bên tham gia -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-tie text-primary me-2"></i>
                                <div>
                                    <div class="small fw-bold">Môi giới</div>
                                    <div>${appointment.user_agent ? appointment.user_agent.Name : 'Chưa xác định'}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user text-primary me-2"></i>
                                <div>
                                    <div class="small fw-bold">Người hẹn</div>
                                    <div>${!appointment.OwnerID ?
                                          (appointment.user_customer ? appointment.user_customer.Name : 'Chưa xác định') :
                                          (appointment.user_owner ? appointment.user_owner.Name : 'Chưa xác định')}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    ${appointment.property ? `
                    <!-- Thông tin BĐS nếu có -->
                    <div class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-home text-primary me-2"></i>
                            <div>
                                <div class="small fw-bold">Bất động sản</div>
                                <div>${appointment.property.Title || 'Không có thông tin'}</div>
                            </div>
                        </div>
                    </div>
                    ` : ''}

                    <!-- Mô tả cuộc hẹn -->
                    <div class="mb-0">
                        <div class="small fw-bold text-muted mb-2">Mô tả chi tiết:</div>
                        <div class="bg-light p-3 rounded appointment-notes">
                            ${appointment.DescAppoint || 'Không có mô tả chi tiết'}
                        </div>
                    </div>
                </div>`;

                // Hiển thị HTML
                document.getElementById('appointmentDetail').innerHTML = html;

                // Cập nhật form xóa
                document.getElementById('deleteAppointmentForm').action =
                    `/admin/appointment/${appointmentId}`;

            })
            .catch(error => {
                console.error('Lỗi khi tải chi tiết cuộc hẹn:', error);
                document.getElementById('appointmentDetail').innerHTML =
                    '<div class="text-center py-4 text-danger"><i class="fas fa-exclamation-circle me-2"></i>Không thể tải chi tiết cuộc hẹn. Vui lòng thử lại sau.</div>';
            });
    }

    // Xử lý khi nhấn nút xóa trong modal chi tiết
    document.querySelector('.delete-appointment-btn').addEventListener('click', function() {
        // Ẩn modal chi tiết
        try {
            if (typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getInstance(document.getElementById('appointmentDetailModal')).hide();
                // Hiển thị modal xác nhận xóa
                new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
            } else {
                // Fallback nếu bootstrap không được định nghĩa
                document.getElementById('appointmentDetailModal').style.display = 'none';
                document.getElementById('appointmentDetailModal').classList.remove('show');

                document.getElementById('deleteConfirmModal').style.display = 'block';
                document.getElementById('deleteConfirmModal').classList.add('show');
            }
        } catch (error) {
            console.error('Error with modal handling:', error);
            // Fallback thủ công
            document.getElementById('appointmentDetailModal').style.display = 'none';
            document.getElementById('appointmentDetailModal').classList.remove('show');

            document.getElementById('deleteConfirmModal').style.display = 'block';
            document.getElementById('deleteConfirmModal').classList.add('show');
        }
    });

    // Thêm sự kiện lắng nghe cho các nút phân trang
    document.getElementById('owner-prev-page').addEventListener('click', function() {
        if (currentOwnerPage > 1) {
            currentOwnerPage--;
            renderAppointmentsPage('owners-appointments-body', allOwnerAppointments, currentOwnerPage, itemsPerPage);
            updatePaginationButtons();
        }
    });

    document.getElementById('owner-next-page').addEventListener('click', function() {
        const totalOwnerPages = Math.ceil(allOwnerAppointments.length / itemsPerPage);
        if (currentOwnerPage < totalOwnerPages) {
            currentOwnerPage++;
            renderAppointmentsPage('owners-appointments-body', allOwnerAppointments, currentOwnerPage, itemsPerPage);
            updatePaginationButtons();
        }
    });

    document.getElementById('customer-prev-page').addEventListener('click', function() {
        if (currentCustomerPage > 1) {
            currentCustomerPage--;
            renderAppointmentsPage('customers-appointments-body', allCustomerAppointments, currentCustomerPage, itemsPerPage);
            updatePaginationButtons();
        }
    });

    document.getElementById('customer-next-page').addEventListener('click', function() {
        const totalCustomerPages = Math.ceil(allCustomerAppointments.length / itemsPerPage);
        if (currentCustomerPage < totalCustomerPages) {
            currentCustomerPage++;
            renderAppointmentsPage('customers-appointments-body', allCustomerAppointments, currentCustomerPage, itemsPerPage);
            updatePaginationButtons();
        }
    });

    // Hàm cập nhật trạng thái nút phân trang
    function updatePaginationButtons() {
        // Cập nhật nút phân trang cho chủ sở hữu
        const totalOwnerPages = Math.ceil(allOwnerAppointments.length / itemsPerPage);
        document.getElementById('owner-prev-page').disabled = currentOwnerPage <= 1;
        document.getElementById('owner-next-page').disabled = currentOwnerPage >= totalOwnerPages || totalOwnerPages === 0;

        // Cập nhật nút phân trang cho khách hàng
        const totalCustomerPages = Math.ceil(allCustomerAppointments.length / itemsPerPage);
        document.getElementById('customer-prev-page').disabled = currentCustomerPage <= 1;
        document.getElementById('customer-next-page').disabled = currentCustomerPage >= totalCustomerPages || totalCustomerPages === 0;
    }
});
</script>
