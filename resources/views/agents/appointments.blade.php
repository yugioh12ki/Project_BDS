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
            <input type="text" class="form-control mb-3" placeholder="Tìm kiếm bất động sản...">
            <div class="card mb-3">
                <div class="list-group list-group-flush">
                    @foreach($properties as $property)
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="fw-bold">{{ $property->Title }}</div>
                        <small class="text-muted">{{ $property->Address }}, {{ $property->Ward }}, {{ $property->District }}</small>
                    </a>
                    @endforeach
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
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Chủ Đề Cuộc Hẹn</th>
                                    <th>Mô Tả Cuộc Hẹn</th>
                                    <th>Ngày hẹn</th>
                                    <th>Giờ Bắt Đầu Cuộc Hẹn</th>
                                    <th>Giờ Kết Thúc Cuộc Hẹn</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                <tr>
                                    <td>
                                        @if($appointment->cusUser)
                                            <div class="fw-bold">{{ $appointment->cusUser->Name }}</div>
                                            <small class="text-muted">Điện thoại: {{ $appointment->cusUser->Phone ?: 'Chưa cập nhật' }}</small>
                                        @else
                                            <div class="text-muted">Chưa có thông tin</div>
                                        @endif
                                    </td>
                                    <td>{{ $appointment->TitleAppoint }}</td>
                                    <td>{{ $appointment->DescAppoint }}</td>
                                    <td>{{ date('d/m/Y', strtotime($appointment->AppointmentDateStart)) }}</td>
                                    <td>{{ date('H:i', strtotime($appointment->AppointmentDateStart)) }}</td>
                                    <td>{{ date('H:i', strtotime($appointment->AppointmentDateEnd)) }}</td>
                                    <td>
                                        @switch($appointment->Status)
                                            @case('Chờ xử lý')
                                                <span class="badge bg-warning">Chờ xử lý</span>
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
                                    <td>
                                        @if($appointment->Status == 'Khởi tạo')
                                            <button class="btn btn-sm btn-success me-1" title="Hoàn thành">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" title="Hủy">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-primary" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3">Không có lịch hẹn nào</td>
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