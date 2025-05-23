<div class="appointment-detail">
    <!-- Thông tin ID cuộc hẹn - Giúp debug và quản lý -->
    <div class="mb-2">
        <span class="badge bg-secondary">ID: {{ $appointment->AppointmentID }}</span>
    </div>
    
    <!-- Thông tin chính cuộc hẹn -->
    <div class="mb-3">
        <h6 class="text-primary fw-bold mb-0">{{ $appointment->TitleAppoint ?? 'Không có tiêu đề' }}</h6>
        <small class="text-muted">
            Trạng thái: 
            <span class="badge {{ strtolower($appointment->Status) === 'hoàn thành' ? 'bg-success' : (strtolower($appointment->Status) === 'đang chờ' ? 'bg-warning' : 'bg-secondary') }}">
                {{ $appointment->Status ?? 'ĐANG CHỜ' }}
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
                    <div>{{ $appointment->AppointmentDateStart ? date('d/m/Y H:i', strtotime($appointment->AppointmentDateStart)) : 'Chưa xác định' }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <i class="fas fa-calendar-check text-primary me-2"></i>
                <div>
                    <div class="small fw-bold">Kết thúc</div>
                    <div>{{ $appointment->AppointmentDateEnd ? date('d/m/Y H:i', strtotime($appointment->AppointmentDateEnd)) : 'Chưa xác định' }}</div>
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
                    <div>{{ $appointment->user_agent->Name ?? 'Chưa xác định' }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <i class="fas fa-user text-primary me-2"></i>
                <div>
                    <div class="small fw-bold">Người hẹn</div>
                    <div>{{ empty($appointment->user_owner) ? ($appointment->user_customer->Name ?? 'Chưa xác định') : ($appointment->user_owner->Name ?? 'Chưa xác định') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin BĐS nếu có -->
    @if($appointment->property)
    <div class="mb-3 pb-2 border-bottom">
        <div class="d-flex align-items-center">
            <i class="fas fa-home text-primary me-2"></i>
            <div>
                <div class="small fw-bold">Bất động sản</div>
                <div>{{ $appointment->property->Title }}</div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Mô tả cuộc hẹn -->
    <div class="mb-0">
        <div class="small fw-bold text-muted mb-2">Mô tả chi tiết:</div>
        <div class="bg-light p-3 rounded" style="min-height: 60px; max-height: 200px; overflow-y: auto;">
            {{ $appointment->DescAppoint ?? 'Không có mô tả chi tiết' }}
        </div>
    </div>
</div>
