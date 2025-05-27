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
                            @case('Hoàn thành')
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
                <h5 class="mb-3">{{ $appointment->property->Title ?? 'Bất động sản không xác định' }}</h5>
                <p class="text-muted mb-4">
                    <i class="bi bi-geo-alt"></i> 
                    {{ $appointment->property->Address ?? '' }}{{ isset($appointment->property->Ward) ? ', '.$appointment->property->Ward : '' }}{{ isset($appointment->property->District) ? ', '.$appointment->property->District : '' }}
                </p>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
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
                    
                    <div class="col-md-6 mb-3">
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
                    
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div class="ms-3">
                                <div class="fw-bold">Địa điểm</div>
                                <div>Tại bất động sản</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card person-card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0 d-flex align-items-center">
                                    <i class="bi bi-person me-2"></i> Người mời gặp
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($appointment->ownerUser)
                                    <div class="person-name mb-1">{{ $appointment->ownerUser->Name }}</div>
                                    <div class="person-phone">
                                        <i class="bi bi-telephone-fill"></i> {{ $appointment->ownerUser->Phone ?? 'Không có SĐT' }}
                                    </div>
                                @else
                                    <div class="text-muted">Không xác định</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card person-card">
                            <div class="card-header">
                                <h6 class="mb-0 d-flex align-items-center">
                                    <i class="bi bi-people me-2"></i> Khách hàng tiềm năng
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($appointment->cusUser)
                                    <div class="person-name mb-1">{{ $appointment->cusUser->Name }}</div>
                                    <div class="person-phone">
                                        <i class="bi bi-telephone-fill"></i> {{ $appointment->cusUser->Phone ?? 'Không có SĐT' }}
                                    </div>
                                @else
                                    <div class="text-muted">Không xác định</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-0">
                    <div class="card-header">
                        <h6 class="mb-0 d-flex align-items-center">
                            <i class="bi bi-journal-text me-2"></i> Ghi chú
                        </h6>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-3">{{ $appointment->TitleAppoint }}</h5>
                        <p class="mb-0">{{ $appointment->DescAppoint }}</p>
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
                            <i class="bi bi-check-lg"></i> Xác nhận lịch hẹn
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('agent.appointments.update-status', $appointment->AppointmentID) }}" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="Đã hủy">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-lg"></i> Từ chối
                        </button>
                    </form>
                @elseif($appointment->Status == 'Thành công')
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                @else
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                @endif
            </div>
        </div>
    </div>
</div>
