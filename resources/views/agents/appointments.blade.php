@extends('_layout._layagent.app')

@section('title', 'Lịch Hẹn Xem Nhà')

@section('appointments')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-2">Lịch hẹn xem nhà</h1>
            <p class="text-muted">Quản lý lịch hẹn xem bất động sản</p>
        </div>
    </div>

    <div class="row">
        <!-- Left column - BDS list -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Bất động sản được phân công</h5>
                </div>
                <div class="card-body p-0">
                    @if($properties->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($properties as $property)
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ Str::limit($property->Title, 50) }}</h6>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i>
                                            {{ $property->Address }}, {{ $property->Ward }}, {{ $property->District }}
                                        </small>
                                    </div>
                                    <div class="badge bg-info">
                                        {{ $property->appointments->count() }} lịch hẹn
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info m-3">
                            <i class="bi bi-info-circle me-2"></i>
                            Chưa có bất động sản nào được phân công hoặc có lịch hẹn
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right column - Appointments -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <select class="form-select w-auto">
                    <option>Tất cả</option>
                    <option>Chờ xử lý</option>
                    <option>Thành công</option>
                    <option>Đã hủy</option>
                </select>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                    <i class="bi bi-plus-lg"></i> Tạo lịch hẹn mới
                </button>
            </div>
            
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table appointments-table mb-0">
                            <thead>
                                <tr></tr>
                                    <th>Chủ Sở Hữu</th>
                                    <th>Khách Hàng</th>
                                    <th>Chủ Đề</th>
                                    <th>Mô Tả</th>
                                    <th>Ngày hẹn</th>
                                    <th>Bắt đầu</th>
                                    <th>Kết thúc</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                <tr>
                                    <td class="customer-info">
                                        @if($appointment->ownerUser)
                                            <div class="customer-name">{{ $appointment->ownerUser->Name }}</div>
                                            <div class="customer-phone">
                                                <i class="bi bi-telephone"></i>
                                                {{ $appointment->ownerUser->Phone ?: 'Chưa cập nhật' }}
                                            </div>
                                        @else
                                            <div class="text-muted">Chưa có thông tin chủ sở hữu</div>
                                        @endif
                                    </td>
                                    <td class="customer-info">
                                        @if($appointment->cusUser)
                                            <div class="customer-name">{{ $appointment->cusUser->Name }}</div>
                                            <div class="customer-phone">
                                                <i class="bi bi-telephone"></i>
                                                {{ $appointment->cusUser->Phone ?: 'Chưa cập nhật' }}
                                            </div>
                                        @else
                                            <div class="text-muted">Chưa có thông tin</div>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($appointment->TitleAppoint, 30) }}</td>
                                    <td>{{ Str::limit($appointment->DescAppoint, 50) }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime($appointment->AppointmentDateStart)) }}</td>
                                    <td class="text-center">{{ date('H:i', strtotime($appointment->AppointmentDateStart)) }}</td>
                                    <td class="text-center">{{ date('H:i', strtotime($appointment->AppointmentDateEnd)) }}</td>
                                    <td class="status-cell">
                                        @switch($appointment->Status)
                                            @case('Chờ xử lý')
                                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                                @break
                                            @case('Thành công')
                                                <span class="badge bg-success">Thành công</span>
                                                @break
                                            @case('Đã hủy')
                                                <span class="badge bg-danger">Đã hủy</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $appointment->Status }}</span>
                                        @endswitch
                                    </td>
                                    <td class="actions-cell">
                                        @if($appointment->Status == 'Khởi tạo')
                                            <button class="btn btn-sm btn-success" title="Hoàn thành">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" title="Hủy">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-3">Không có lịch hẹn nào</td>
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

<!-- Create Appointment Modal -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo lịch hẹn mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="appointmentForm" action="{{ route('agent.appointments.create') }}" method="POST">
                    @csrf
                    <div class="mb-3 position-relative">
                        <label class="form-label">Tên bất động sản <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="propertySearch" 
                               placeholder="Nhập tên bất động sản..." required>
                        <input type="hidden" name="PropertyID" id="propertyID">
                        <ul id="propertyList" class="dropdown-menu w-100 show" style="display:none; position:absolute; inset: auto 0px 0px; transform: translate(0px, 40px);">
                        </ul>
                        <div id="ownerInfo" class="mt-2 alert alert-info" style="display: none;">
                            <i class="bi bi-info-circle me-2"></i>
                            Chủ sở hữu: <strong id="ownerName"></strong>
                        </div>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customerSearch" 
                               placeholder="Nhập tên khách hàng để tìm..." required>
                        <input type="hidden" name="CusID" id="customerID">
                        <ul id="customerList" class="dropdown-menu w-100 show" style="display:none; position:absolute; inset: auto 0px 0px; transform: translate(0px, 40px);">
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại khách hàng</label>
                        <input type="tel" class="form-control" name="CustomerPhone" readonly
                               placeholder="Số điện thoại sẽ tự động điền khi chọn khách hàng">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tiêu đề cuộc hẹn <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="TitleAppoint" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="DescAppoint" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hẹn <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="AppointmentDateStart" required 
                                       min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="AppointmentTimeStart" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Giờ kết thúc <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="AppointmentTimeEnd" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" form="appointmentForm" class="btn btn-primary">Tạo lịch hẹn</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
window.propertyList = {!! json_encode($properties->map(function($property) {
    return array(
        'id' => $property->PropertyID,
        'title' => $property->Title,
        'ownerId' => $property->OwnerID, 
        'ownerName' => optional($property->owner)->Name ?? 'Không xác định'
    );
})) !!};

// Debug để kiểm tra dữ liệu
console.log('Property List:', window.propertyList);
</script>
<script src="{{ asset('js/appointments.js') }}"></script>

<style>
.dropdown-menu.show {
    display: block !important;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    position: absolute;
    inset: auto 0px 0px;
    transform: translate(0px, 40px);
}

/* Thêm style để hiển thị rõ ràng owner name */
.property-item {
    padding: 8px 12px;
    border-bottom: 1px solid #eee;
}

.property-title {
    font-weight: 500;
    margin-bottom: 4px;
}

.owner-name {
    font-size: 0.875rem;
    color: #6c757d;
}
</style>
@endpush
@endsection