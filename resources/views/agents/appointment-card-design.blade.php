<div class="col-md-6 col-xl-4 mb-3">
    <div class="card appointment-card h-100">
        <!-- Card header -->
        <div class="card-header border-0">
            <div class="d-flex align-items-center">
                <i class="bi bi-calendar2-week text-primary me-2"></i>
                <h5 class="card-title mb-0">Lịch hẹn - {{ $property_title }}</h5>
            </div>
            <div>
                @switch($status)
                    @case('Chờ xử lý')
                        <span class="badge bg-warning">Chờ xác nhận</span>
                        @break
                    @case('Thành công')
                        <span class="badge bg-success">Đã xác nhận</span>
                        @break
                    @case('Đã hủy')
                        <span class="badge bg-danger">Đã hủy</span>
                        @break
                    @case('Hoàn thành')
                        <span class="badge bg-info">Hoàn thành</span>
                        @break
                    @default
                        <span class="badge bg-secondary">{{ $status }}</span>
                @endswitch
            </div>
        </div>
        
        <!-- Card body -->
        <div class="card-body pt-0">
            <!-- Ngày hẹn -->
            <div class="d-flex align-items-center mb-3">
                <div class="icon-wrapper">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div class="ms-3">
                    <div class="fw-bold">Ngày hẹn</div>
                    <div>{{ $appointment_date }}</div>
                </div>
            </div>
            
            <!-- Thời gian -->
            <div class="d-flex align-items-center mb-3">
                <div class="icon-wrapper">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="ms-3">
                    <div class="fw-bold">Thời gian</div>
                    <div>{{ $appointment_time }}</div>
                </div>
            </div>
            
            <!-- Người mời giới -->
            <div class="d-flex align-items-center mb-3">
                <div class="icon-wrapper">
                    <i class="bi bi-person"></i>
                </div>
                <div class="ms-3">
                    <div class="fw-bold">Người mời giới</div>
                    <div>{{ $agent_name }}</div>
                    <div class="text-muted small">
                        <i class="bi bi-telephone-fill"></i> {{ $agent_phone }}
                    </div>
                </div>
            </div>
            
            <!-- Khách hàng -->
            <div class="d-flex align-items-center">
                <div class="icon-wrapper">
                    <i class="bi bi-people"></i>
                </div>
                <div class="ms-3">
                    <div class="fw-bold">Khách hàng</div>
                    <div>{{ $customer_name }}</div>
                    <div class="text-muted small">
                        <i class="bi bi-telephone-fill"></i> {{ $customer_phone }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card footer -->
        <div class="card-footer d-flex justify-content-between py-3">
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal{{ $appointment_id }}">
                <i class="bi bi-eye"></i> Chi tiết
            </button>
            
            <!-- Nút xác nhận / từ chối chỉ hiển thị với lịch hẹn chờ xử lý -->
            @if($status == 'Chờ xử lý')
            <div>
                <form method="POST" action="{{ route('agent.appointments.update-status', $appointment_id) }}" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="Thành công">
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-check-lg"></i> Xác nhận
                    </button>
                </form>
                
                <form method="POST" action="{{ route('agent.appointments.update-status', $appointment_id) }}" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="Đã hủy">
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-x-lg"></i> Từ chối
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
