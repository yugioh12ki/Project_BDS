<!-- Modal Chi tiết lịch hẹn -->
<div class="modal fade" id="appointmentDetailModal{{ $appointment->AppointmentID }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="bi bi-calendar2-week text-primary me-2"></i>
                    Chi tiết lịch hẹn
                    
                    <span class="ms-2">
                        @switch($appointment->Status)
                            @case('Chờ xử lý')
                                <span class="badge bg-warning">Chờ xác nhận</span>
                                @break
                            @case('Thành công')
                                <span class="badge bg-success">Đã xác nhận</span>
                                @break
                            @case('Đã hủy')
                                <span class="badge bg-danger">Đã hủy</span>
                                @break
                            @case('Hoàn Thành')
                                <span class="badge bg-info">Hoàn thành</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ $appointment->Status }}</span>
                        @endswitch
                    </span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="mb-3">{{ $appointment->property->Title ?? 'Bất động sản không xác định' }}</h5>
                                <p class="text-muted mb-4">
                                    <i class="bi bi-geo-alt"></i> 
                                    {{ $appointment->property->Address ?? '' }}{{ isset($appointment->property->Ward) ? ', '.$appointment->property->Ward : '' }}{{ isset($appointment->property->District) ? ', '.$appointment->property->District : '' }}
                                </p>
                                
                                <div class="row mb-4">
                                    <div class="col-md-12 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-wrapper">
                                                <i class="bi bi-calendar3"></i>
                                            </div>
                                            <div class="ms-3">
                                                <div class="fw-bold">Ngày hẹn</div>
                                                <div>{{ date('d/m/Y', strtotime($appointment->AppointmentDateStart)) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-wrapper">
                                                <i class="bi bi-clock"></i>
                                            </div>
                                            <div class="ms-3">
                                                <div class="fw-bold">Thời gian</div>
                                                <div>
                                                    {{ date('H:i', strtotime($appointment->AppointmentDateStart)) }} - 
                                                    {{ date('H:i', strtotime($appointment->AppointmentDateEnd)) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-wrapper">
                                                <i class="bi bi-geo-alt"></i>
                                            </div>
                                            <div class="ms-3">
                                                <div class="fw-bold">Địa điểm</div>
                                                <div>Tại địa chỉ bất động sản</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <i class="bi bi-journal-text me-2"></i> Nội dung lịch hẹn
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="mb-2">{{ $appointment->TitleAppoint }}</h6>
                                        <p class="mb-0 text-muted">{{ $appointment->DescAppoint }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card person-card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <i class="bi bi-person me-2"></i> Thông tin chủ sở hữu
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($appointment->ownerUser)
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="icon-wrapper me-3">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $appointment->ownerUser->Name }}</h6>
                                                    @if($appointment->ownerUser->Email)
                                                        <p class="mb-1 small">
                                                            <i class="bi bi-envelope-fill me-1"></i>
                                                            {{ $appointment->ownerUser->Email }}
                                                        </p>
                                                    @endif
                                                    @if($appointment->ownerUser->Phone)
                                                        <p class="mb-0 small">
                                                            <i class="bi bi-telephone-fill me-1"></i>
                                                            {{ $appointment->ownerUser->Phone }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning mb-0">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                Không có thông tin chủ sở hữu
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="card person-card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <i class="bi bi-people me-2"></i> Thông tin khách hàng
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($appointment->cusUser)
                                            <div class="d-flex align-items-center">
                                                <div class="icon-wrapper me-3">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $appointment->cusUser->Name }}</h6>
                                                    @if($appointment->cusUser->Email)
                                                        <p class="mb-1 small">
                                                            <i class="bi bi-envelope-fill me-1"></i>
                                                            {{ $appointment->cusUser->Email }}
                                                        </p>
                                                    @endif
                                                    @if($appointment->cusUser->Phone)
                                                        <p class="mb-0 small">
                                                            <i class="bi bi-telephone-fill me-1"></i>
                                                            {{ $appointment->cusUser->Phone }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if(isset($appointment->cusUser->yeuCau) && !empty($appointment->cusUser->yeuCau))
                                                <div class="mt-3 pt-3 border-top">
                                                    <p class="fw-bold mb-1">Yêu cầu của khách:</p>
                                                    <p class="mb-0 text-muted">{{ $appointment->cusUser->yeuCau }}</p>
                                                </div>
                                            @endif
                                        @else
                                            <div class="alert alert-warning mb-0">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                Không có thông tin khách hàng
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                @if($appointment->Status == 'Chờ xử lý')
                    <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="Thành công">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Xác nhận lịch hẹn
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="Đã hủy">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-lg me-1"></i> Từ chối
                        </button>
                    </form>
                @elseif($appointment->Status == 'Thành công')
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="Hoàn Thành">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i> Đánh dấu hoàn thành
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                @endif
            </div>
        </div>
    </div>
</div>
