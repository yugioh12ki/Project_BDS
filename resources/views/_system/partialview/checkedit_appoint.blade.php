<div>
    <div>
        <div class="mb-3">
            <label class="text-muted small">Người tạo lịch hẹn</label>
            <div class="form-control-plaintext border-bottom">{{ $appointment->user_agent->Name ?? 'Chưa xác định' }}</div>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Người hẹn</label>
            <div class="form-control-plaintext border-bottom">{{ empty($appointment->user_owner) ? ($appointment->user_customer->Name ?? 'Chưa xác định') : ($appointment->user_owner->Name ?? 'Chưa xác định') }}</div>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Tiêu đề cuộc hẹn</label>
            <div class="form-control-plaintext border-bottom">{{ $appointment->TitleAppoint ?? '' }}</div>
        </div>
        <div class="mb-3">
            <label class="text-muted small">Nội dung cuộc hẹn</label>
            <div class="form-control-plaintext border-bottom" style="min-height: 60px;">{{ $appointment->DescAppoint ?? '' }}</div>
        </div>
            <div class="mb-3">
                <label class="text-muted small">Ngày bắt đầu</label>
                <div class="form-control-plaintext border-bottom">{{ $appointment->AppointmentDateStart ? date('d/m/Y H:i', strtotime($appointment->AppointmentDateStart)) : '' }}</div>
            </div>
            <div class="mb-3">
                <label class="text-muted small">Ngày kết thúc</label>
                <div class="form-control-plaintext border-bottom">{{ $appointment->AppointmentDateEnd ? date('d/m/Y H:i', strtotime($appointment->AppointmentDateEnd)) : '' }}</div>
            </div>
            <div class="mb-3">
                <label class="text-muted small">Trạng thái</label>
                <div class="form-control-plaintext">
                    <span class="badge {{ strtolower($appointment->Status) === 'hoàn thành' ? 'bg-success' : (strtolower($appointment->Status) === 'đang chờ' ? 'bg-warning' : 'bg-secondary') }}">
                        {{ $appointment->Status ?? 'ĐANG CHỜ' }}
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label class="text-muted small">Bất động sản liên quan</label>
                <div class="form-control-plaintext border-bottom">
                    @if($appointment->property)
                        {{ $appointment->property->Title }}
                    @else
                        <span class="text-muted">Không có</span>
                    @endif
                </div>
            </div>
    </div>
</div>
