@extends('_layout._layagent.app')

@section('title', 'Lịch Hẹn Xem Nhà')

@section('content')
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
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $property->appointments_count ?? 0 }}
                                    </span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Chưa có bất động sản nào được phân công</p>
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
                                <tr>
                                    <th>Khách hàng</th>
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
                <form>
                    <div class="mb-3">
                        <label class="form-label">Bất động sản</label>
                        <select class="form-select">
                            <option value="">Chọn bất động sản</option>
                            <option>Chung cư cao cấp The Sun Avenue</option>
                            <option>Biệt thự Vinhomes Central Park</option>
                            <option>Nhà phố Thảo Điền</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ngày hẹn</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Giờ hẹn</label>
                                <input type="time" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary">Tạo lịch hẹn</button>
            </div>
        </div>
    </div>
</div>
@endsection