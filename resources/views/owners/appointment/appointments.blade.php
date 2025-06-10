@extends('_layout._layowner.app')

@section('appointment')

<div class="container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3>Lịch Hẹn</h3>
            </div>
        </div>
    </div>
</div>

<!-- Main Content - Appointment List -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tất Cả Lịch Hẹn</h5>
                    <p class="mb-0 text-muted small">Quản lý và xem chi tiết tất cả lịch hẹn bất động sản của bạn</p>
                </div>
                        <div class="card-body">
                            <!-- Tab trạng thái -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="nav nav-tabs appointment-status-tabs" role="tablist" id="statusTabs">
                                        <button type="button" class="nav-link active" data-filter="khoitao" id="btnKhoiTao" data-count="{{ $appointments->where('Status', 'Khởi Tạo')->count() }}">
                                            Chờ Xác Nhận <span class="badge rounded-pill bg-warning text-dark">{{ $appointments->where('Status', 'Khởi Tạo')->count() }}</span>
                                        </button>
                                        <button type="button" class="nav-link " data-filter="dangthuchien" id="btnDangThucHien" data-count="{{ $appointments->where('Status', 'Đang Thực Hiện')->count() }}">
                                            Đang Thực Hiện <span class="badge rounded-pill bg-info text-white">{{ $appointments->where('Status', 'Đang Thực Hiện')->count() }}</span>
                                        </button>
                                        <button type="button" class="nav-link" data-filter="hoanthanh" id="btnHoanThanh" data-count="{{ $appointments->where('Status', 'Hoàn Thành')->count() }}">
                                            Đã Xác Nhận <span class="badge rounded-pill bg-success text-white">{{ $appointments->where('Status', 'Hoàn Thành')->count() }}</span>
                                        </button>
                                        <button type="button" class="nav-link" data-filter="huyhen" id="btnHuyHen" data-count="{{ $appointments->where('Status', 'Hủy Hẹn')->count() }}">
                                            Đã Hủy <span class="badge rounded-pill bg-danger text-white">{{ $appointments->where('Status', 'Hủy Hẹn')->count() }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tìm kiếm -->
                            <div class="row mb-3 mt-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Tìm kiếm lịch hẹn..." id="searchAppointment">
                                        <button class="btn btn-outline-secondary" type="button"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-appointments" id="appointmentsTable">
                                    <thead>
                                        <tr>
                                            <th>Bất Động Sản</th>
                                            <th>Khách Hàng</th>
                                            <th>Môi Giới</th>
                                            <th>Trạng Thái</th>
                                            <th class="text-end">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="appointmentsTbody">
                                        @forelse($appointments as $appointment)
                                            <tr data-status="{{
                                                $appointment->Status == 'Khởi Tạo' ? 'khoitao' :
                                                ($appointment->Status == 'Đang Thực Hiện' ? 'dangthuchien' :
                                                ($appointment->Status == 'Hoàn Thành' ? 'hoanthanh' :
                                                ($appointment->Status == 'Hủy Hẹn' ? 'huyhen' : 'unknown')))
                                            }}" data-appointment-status="{{ $appointment->Status }}" class="appointment-row">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="property-thumb me-2">
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                <i class="fa fa-home text-muted"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $appointment->property ? $appointment->property->Title : $appointment->TitleAppoint }}</div>
                                                            <small class="text-muted">{{ $appointment->property ? $appointment->property->Address : 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="customer-avatar me-2">
                                                            @php
                                                                $customerName = $appointment->cusUser ? $appointment->cusUser->Name : 'N/A';
                                                                $customerId = $appointment->cusUser ? $appointment->cusUser->UserID : 'N/A';
                                                                $colors = ['bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-primary', 'bg-secondary'];
                                                                $colorIndex = abs(crc32($customerId)) % count($colors);
                                                                $bgColor = $colors[$colorIndex];
                                                            @endphp
                                                            <div class="{{ $bgColor }} rounded-circle d-flex align-items-center justify-content-center text-white"
                                                                style="width: 32px; height: 32px; font-size: 12px;"
                                                                title="{{ $customerName }}">
                                                                {{ $appointment->cusUser ? strtoupper(substr($appointment->cusUser->Name, 0, 2)) : 'N/A' }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">
                                                                {{ $appointment->cusUser ? $appointment->cusUser->Name : 'Chưa có thông tin' }}
                                                            </div>
                                                            <small class="text-muted">{{ $appointment->cusUser ? $appointment->cusUser->Phone : '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="agent-avatar me-2">
                                                            @php
                                                                $agentName = $appointment->agentUser ? $appointment->agentUser->Name : 'Chưa phân công';
                                                                $agentId = $appointment->agentUser ? $appointment->agentUser->UserID : 'N/A';
                                                                $agentColors = ['bg-primary', 'bg-info', 'bg-success', 'bg-warning', 'bg-danger', 'bg-dark'];
                                                                $agentColorIndex = abs(crc32($agentId)) % count($agentColors);
                                                                $agentBgColor = $appointment->agentUser ? $agentColors[$agentColorIndex] : 'bg-secondary';
                                                            @endphp
                                                            <div class="{{ $agentBgColor }} rounded-circle d-flex align-items-center justify-content-center text-white"
                                                                style="width: 32px; height: 32px; font-size: 12px;"
                                                                title="{{ $agentName }}">
                                                                {{ $appointment->agentUser ? strtoupper(substr($appointment->agentUser->Name, 0, 2)) : 'N/A' }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $appointment->agentUser ? $appointment->agentUser->Name : 'Chưa phân công' }}</div>
                                                            <small class="text-muted">{{ $appointment->agentUser ? $appointment->agentUser->Phone : '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($appointment->Status == 'Khởi Tạo')
                                                        <span class="badge bg-warning text-dark">Chờ Xác Nhận</span>
                                                    @elseif($appointment->Status == 'Đang Thực Hiện')
                                                        <span class="badge bg-info">Đang Thực Hiện</span>
                                                    @elseif($appointment->Status == 'Hoàn Thành')
                                                        <span class="badge bg-success">Hoàn Thành</span>
                                                    @elseif($appointment->Status == 'Hủy Hẹn')
                                                        <span class="badge bg-danger">Đã Hủy</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $appointment->Status }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal" data-appointment-id="{{ $appointment->AppointmentID }}">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        @if($appointment->Status == 'Khởi Tạo')
                                                            <button class="btn btn-outline-success btn-sm"
                                                               onclick="confirmAppointment('{{ $appointment->AppointmentID ?? 'NULL' }}', '{{ $appointment->AgentID ?? 'NULL' }}')"
                                                                title="Xác nhận lịch hẹn">
                                                                <i class="fa fa-check"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-sm"
                                                                onclick="cancelAppointment('{{ $appointment->AppointmentID ?? 'NULL' }}', '{{ $appointment->AgentID ?? 'NULL' }}')"
                                                                title="Hủy lịch hẹn">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-3">Không có lịch hẹn nào</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

<!-- Appointment Detail Modal -->
<div class="modal fade" id="appointmentDetailModal" tabindex="-1" aria-labelledby="appointmentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentDetailModalLabel">
                    <i class="fa fa-calendar-check me-2"></i>Chi tiết lịch hẹn
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="appointmentDetailContent">
                    <!-- Content will be loaded here -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                        <p class="mt-2">Đang tải thông tin lịch hẹn...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Bootstrap error debugging và logging cho owner
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== OWNER APPOINTMENTS DEBUG LOG ===');
    console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? 'Available' : 'Not Available');
    console.log('Appointment detail modal element:', document.getElementById('appointmentDetailModal'));

    // Filter trạng thái
    document.querySelectorAll('.appointment-status-tabs [data-filter]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            console.log('Filter clicked:', this.getAttribute('data-filter'));

            // Xóa class active ở tất cả, set active ở cái được click
            document.querySelectorAll('.appointment-status-tabs [data-filter]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            let filter = this.getAttribute('data-filter');
            // Lọc từng dòng
            document.querySelectorAll('.appointment-row').forEach(function(row) {
                if(row.getAttribute('data-status') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Nếu không có dòng nào, hiện dòng trống
            let visibleRows = Array.from(document.querySelectorAll('.appointment-row')).filter(r => r.style.display !== 'none');
            let tbody = document.getElementById('appointmentsTbody');
            let emptyRow = document.getElementById('empty-appointment-row');
            if (visibleRows.length === 0) {
                if (!emptyRow) {
                    let tr = document.createElement('tr');
                    tr.id = 'empty-appointment-row';
                    tr.innerHTML = `<td colspan="9" class="text-center py-3 text-muted">Không có lịch hẹn nào ở trạng thái này</td>`;
                    tbody.appendChild(tr);
                }
            } else {
                if (emptyRow) emptyRow.remove();
            }
        });
    });

    // Khi load trang, filter mặc định trạng thái đầu tiên
    let firstBtn = document.querySelector('.appointment-status-tabs [data-filter].active');
    if (firstBtn) {
        console.log('Auto-clicking first filter button');
        firstBtn.click();
    }

    // Bắt lỗi Bootstrap modal
    document.addEventListener('DOMContentLoaded', function() {
        // Bắt lỗi chung cho tất cả modal
        document.querySelectorAll('.modal').forEach(function(modalElement) {
            modalElement.addEventListener('show.bs.modal', function(event) {
                console.log('Modal đang mở:', modalElement.id, event);

                // Kiểm tra backdrop configuration
                try {
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        console.log('Modal instance backdrop:', modalInstance._config?.backdrop);
                    }
                } catch (error) {
                    console.error('Lỗi khi truy cập modal instance:', error);
                }
            });

            modalElement.addEventListener('hidden.bs.modal', function(event) {
                console.log('Modal đã đóng:', modalElement.id);
            });
        });
    });

    // Xử lý click cho button appointment detail
    document.querySelectorAll('[data-bs-target="#appointmentDetailModal"]').forEach(function(button) {
        button.addEventListener('click', function(e) {
            const appointmentId = this.getAttribute('data-appointment-id');
            console.log('Opening appointment detail for ID:', appointmentId);

            if (!appointmentId) {
                console.error('❌ Không có appointment ID');
                e.preventDefault();
                return;
            }

            // Load appointment details
            loadAppointmentDetails(appointmentId);
        });
    });

    // Hàm load chi tiết appointment
    function loadAppointmentDetails(appointmentId) {
        const content = document.getElementById('appointmentDetailContent');

        // Reset content với loading
        content.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2">Đang tải thông tin lịch hẹn...</p>
            </div>
        `;

        // Gọi API để lấy chi tiết appointment
        fetch(`/owner/appointments/${appointmentId}/detail`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.appointment) {
                const appointment = data.appointment;
                content.innerHTML = `
                    <div class="row">
                        <!-- Thông tin cuộc hẹn -->
                        <div class="col-12 mb-4">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fa fa-calendar me-2"></i>Thông tin cuộc hẹn</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Tiêu đề:</strong> ${appointment.title}</p>
                                            <p class="mb-2"><strong>Ngày hẹn:</strong> ${appointment.date}</p>
                                            <p class="mb-2"><strong>Thời gian:</strong> ${appointment.start_time} - ${appointment.end_time}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2"><strong>Trạng thái:</strong> ${appointment.status_badge}</p>
                                            <p class="mb-2"><strong>Mô tả:</strong> ${appointment.description}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin bất động sản -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fa fa-home me-2"></i>Thông tin bất động sản</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><strong>Tiêu đề:</strong> ${appointment.property.title}</p>
                                    <p class="mb-2"><strong>Địa chỉ:</strong> ${appointment.property.full_address}</p>
                                    <p class="mb-2"><strong>Giá:</strong> ${appointment.property.price}</p>

                                    <hr>
                                    <h6 class="text-success">Thông tin chủ sở hữu</h6>
                                    <p class="mb-1"><strong>Tên:</strong> ${appointment.property_owner.name}</p>
                                    <p class="mb-1"><strong>SĐT:</strong> ${appointment.property_owner.phone}</p>
                                    <p class="mb-1"><strong>CMND:</strong> ${appointment.property_owner.cmnd}</p>
                                    <p class="mb-0"><strong>Địa chỉ:</strong> ${appointment.property_owner.address}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin khách hàng -->
                        <div class="col-md-6 mb-4">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fa fa-user me-2"></i>Thông tin khách hàng</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="customer-avatar me-3">
                                            <div class="bg-info rounded-circle d-flex align-items-center justify-content-center text-white"
                                                style="width: 50px; height: 50px; font-size: 18px;">
                                                ${appointment.customer.initials}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">${appointment.customer.name}</h6>
                                            <p class="mb-0 text-muted">Khách hàng</p>
                                        </div>
                                    </div>
                                    <p class="mb-1"><strong>Số điện thoại:</strong> ${appointment.customer.phone}</p>
                                    <p class="mb-0"><strong>Địa chỉ:</strong> ${appointment.customer.address}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin môi giới -->
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fa fa-user-tie me-2"></i>Thông tin môi giới</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="agent-avatar me-3">
                                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center text-dark"
                                                style="width: 50px; height: 50px; font-size: 18px;">
                                                ${appointment.agent.initials}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h6 class="mb-1">${appointment.agent.name}</h6>
                                                    <p class="mb-0 text-muted">Môi giới</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>SĐT:</strong> ${appointment.agent.phone}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>Địa chỉ:</strong> ${appointment.agent.address}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        ${data.message || 'Không thể tải thông tin lịch hẹn. Vui lòng thử lại.'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading appointment details:', error);
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-circle me-2"></i>
                    Có lỗi xảy ra khi tải thông tin. Vui lòng thử lại.
                </div>
            `;
        });
    }

    console.log('Owner appointments debug script initialized successfully');
});

// Global error handler cho debugging
window.addEventListener('error', function(event) {
    if (event.message.includes('backdrop') || event.message.includes('bootstrap') || event.message.includes('modal')) {
        console.error('🚨 BOOTSTRAP/MODAL ERROR DETECTED:', {
            message: event.message,
            filename: event.filename,
            lineno: event.lineno,
            colno: event.colno,
            error: event.error,
            stack: event.error?.stack
        });

        // Hiển thị thông báo cho owner
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-warning alert-dismissible fade show position-fixed';
        alertDiv.style.cssText = 'top: 20px; left: 20px; z-index: 10000; max-width: 400px;';
        alertDiv.innerHTML = `
            <strong>Thông báo hệ thống:</strong> Đã phát hiện lỗi giao diện.
            Hệ thống vẫn hoạt động bình thường.
            <br><small>Lỗi: ${event.message}</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        // Auto remove after 10 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 10000);
    }
});
</script>
@endpush
