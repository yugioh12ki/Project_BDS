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

<!-- Tab Navigation -->
<div class="container-fluid mb-4">
    <ul class="nav nav-tabs" id="viewTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                <i class="fa fa-tachometer-alt me-1"></i> Tổng Quan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar" type="button" role="tab">
                <i class="fa fa-calendar me-1"></i> Lịch
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab">
                <i class="fa fa-list me-1"></i> Danh Sách
            </button>
        </li>
    </ul>
</div>

<!-- Tab Content -->
<div class="tab-content" id="viewTabsContent">
    <!-- Dashboard View -->
    <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
        <div class="container-fluid">
            <!-- Stats Boxes -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h2 class="mb-0">{{ $appointments->count() }}</h2>
                                    <p class="mb-0 text-muted">Tổng Lịch Hẹn</p>
                                    <small class="text-muted">+5 so với tháng trước</small>
                                </div>
                                <div class="icon-box bg-light">
                                    <i class="fa fa-calendar-check text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h2 class="mb-0">{{ $upcomingAppointments->count() }}</h2>
                                    <p class="mb-0 text-muted">Sắp Tới</p>
                                    <small class="text-muted">Tiếp theo: Hôm nay lúc {{ $upcomingAppointments->first() ? date('H:i', strtotime($upcomingAppointments->first()->AppointmentDateStart)) : '--:--' }}</small>
                                </div>
                                <div class="icon-box bg-light">
                                    <i class="fa fa-hourglass-half text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h2 class="mb-0">{{ $appointments->where('Status', 'confirmed')->count() }}</h2>
                                    <p class="mb-0 text-muted">Đã Xác Nhận</p>
                                    <small class="text-muted">+2 kể từ hôm qua</small>
                                </div>
                                <div class="icon-box bg-light">
                                    <i class="fa fa-check-circle text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Lịch Hẹn Sắp Tới</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Bất động sản</th>
                                            <th>Khách Hàng</th>
                                            <th>Môi giới</th>
                                            <th>Trạng thái</th>
                                            <th class="text-end">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($upcomingAppointments->take(5) as $appointment)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="property-thumb me-2">
                                                            <div class="bg-light rounded" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fa fa-home text-muted"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            {{ $appointment->property ? $appointment->property->Title : $appointment->TitleAppoint }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        {{ $appointment->AppointmentDateStart ? date('d/m/Y', strtotime($appointment->AppointmentDateStart)) : 'N/A' }}
                                                    </div>
                                                    <small class="text-muted">{{ $appointment->AppointmentDateStart ? date('H:i', strtotime($appointment->AppointmentDateStart)) : 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="agent-avatar me-2">
                                                            <div class="bg-primary rounded-circle text-white" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                                                {{ $appointment->agentUser ? substr($appointment->agentUser->Name, 0, 1) : 'N' }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            {{ $appointment->agentUser ? $appointment->agentUser->Name : 'N/A' }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($appointment->Status == 'Khởi Tạo')
                                                        <span class="badge bg-warning">Chờ Xác Nhận</span>
                                                    @elseif($appointment->Status == 'Đang Thực Hiện')
                                                        <span class="badge bg-warning">Đang Thực Hiện</span>
                                                    @elseif($appointment->Status == 'Hoàn Thành')
                                                        <span class="badge bg-info">Đã Xác Nhận</span>
                                                    @elseif($appointment->Status == 'Hủy Hẹn')
                                                        <span class="badge bg-warning">Đã Hủy</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-3">Không có lịch hẹn sắp tới</td>
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

    <!-- Calendar View -->
    <div class="tab-pane fade" id="calendar" role="tabpanel">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-header bg-white">
                                            <h6 class="mb-0">tháng 5 năm 2025</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="calendar-wrapper">
                                                <table class="table table-bordered mb-0">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Su</th>
                                                            <th>Mo</th>
                                                            <th>Tu</th>
                                                            <th>We</th>
                                                            <th>Th</th>
                                                            <th>Fr</th>
                                                            <th>Sa</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Hàng 1 -->
                                                        <tr class="text-center">
                                                            <td class="text-muted">27</td>
                                                            <td class="text-muted">28</td>
                                                            <td class="text-muted">29</td>
                                                            <td class="text-muted">30</td>
                                                            <td>1</td>
                                                            <td>2</td>
                                                            <td>3</td>
                                                        </tr>
                                                        <!-- Hàng 2 -->
                                                        <tr class="text-center">
                                                            <td>4</td>
                                                            <td>5</td>
                                                            <td>6</td>
                                                            <td>7</td>
                                                            <td>8</td>
                                                            <td>9</td>
                                                            <td>10</td>
                                                        </tr>
                                                        <!-- Hàng 3 -->
                                                        <tr class="text-center">
                                                            <td>11</td>
                                                            <td>12</td>
                                                            <td>13</td>
                                                            <td>14</td>
                                                            <td>15</td>
                                                            <td>16</td>
                                                            <td>17</td>
                                                        </tr>
                                                        <!-- Hàng 4 -->
                                                        <tr class="text-center">
                                                            <td>18</td>
                                                            <td>19</td>
                                                            <td>20</td>
                                                            <td class="bg-light">21</td>
                                                            <td>22</td>
                                                            <td>23</td>
                                                            <td>24</td>
                                                        </tr>
                                                        <!-- Hàng 5 -->
                                                        <tr class="text-center">
                                                            <td>25</td>
                                                            <td>26</td>
                                                            <td>27</td>
                                                            <td>28</td>
                                                            <td>29</td>
                                                            <td>30</td>
                                                            <td>31</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="card">
                                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Thứ Tư, 21 tháng 5, 2025</h5>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-secondary"><i class="fa fa-chevron-left"></i></button>
                                                <button class="btn btn-outline-secondary"><i class="fa fa-chevron-right"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <!-- Lịch hẹn trong ngày -->
                                            <div class="appointment-item mb-3 p-3 border-start border-5 border-success" style="background-color: rgba(25, 135, 84, 0.1);">
                                                <div class="d-flex align-items-center">
                                                    <div class="time-badge me-3 text-center">
                                                        <div class="fw-bold">10:00</div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">Căn hộ cao cấp, Trung tâm</h6>
                                                        <div class="d-flex align-items-center mb-1">
                                                            <div class="me-3"><i class="fa fa-user me-1"></i> Môi giới: Nguyễn Thị B</div>
                                                            <div><i class="fa fa-user-tie me-1"></i> Khách hàng: Trần Văn C</div>
                                                        </div>
                                                        <span class="badge bg-success">Đã xác nhận</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="appointment-item mb-3 p-3 border-start border-5 border-warning" style="background-color: rgba(255, 193, 7, 0.1);">
                                                <div class="d-flex align-items-center">
                                                    <div class="time-badge me-3 text-center">
                                                        <div class="fw-bold">14:00</div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">Nhà gia đình ngoại ô</h6>
                                                        <div class="d-flex align-items-center mb-1">
                                                            <div class="me-3"><i class="fa fa-user me-1"></i> Môi giới: Lê Văn D</div>
                                                            <div><i class="fa fa-user-tie me-1"></i> Khách hàng: Phạm Thị E</div>
                                                        </div>
                                                        <span class="badge bg-warning text-dark">Đang chờ</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- List View (Tab danh sách) -->
    <div class="tab-pane fade" id="list" role="tabpanel">
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
                                                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center text-white"
                                                                style="width: 32px; height: 32px; font-size: 12px;"
                                                                name="{{ $appointment->cusUser ? $appointment->cusUser->UserID : '' }}"
                                                                data-customer-id="{{ $appointment->cusUser ? $appointment->cusUser->UserID : '' }}">
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
                                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; font-size: 12px;">
                                                                {{ $appointment->agentUser ? $appointment->agentUser->Name : 'Chưa phân công' }}
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

@push('scripts')
<script>
// Không cần script debug nào
</script>
@endpush
