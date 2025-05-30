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
                                            <th>Ngày & Giờ</th>
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
                                                                {{ $appointment->user_agent ? substr($appointment->user_agent->Name, 0, 1) : 'N' }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            {{ $appointment->user_agent ? $appointment->user_agent->Name : 'N/A' }}
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
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary">Chi tiết</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-3">Không có lịch hẹn sắp tới</td>
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
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Tìm kiếm lịch hẹn..." id="searchAppointment">
                                        <button class="btn btn-outline-secondary" type="button"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="float-end">
                                        <div class="btn-group" role="group" aria-label="Appointment Filters">
                                            <button class="btn btn-outline-warning" data-filter="khoitao" data-count="{{ $appointments->where('Status', 'Khởi Tạo')->count() }}">
                                                Chờ Xác Nhận ({{ $appointments->where('Status', 'Khởi Tạo')->count() }})
                                            </button>
                                            <button class="btn btn-outline-info" data-filter="dangthuchien" data-count="{{ $appointments->where('Status', 'Đang Thực Hiện')->count() }}">
                                                Đang Thực Hiện ({{ $appointments->where('Status', 'Đang Thực Hiện')->count() }})
                                            </button>
                                            <button class="btn btn-outline-success" data-filter="hoanthanh" data-count="{{ $appointments->where('Status', 'Hoàn Thành')->count() }}">
                                                Hoàn Thành ({{ $appointments->where('Status', 'Hoàn Thành')->count() }})
                                            </button>
                                            <button class="btn btn-outline-danger" data-filter="huyhen" data-count="{{ $appointments->where('Status', 'Hủy Hẹn')->count() }}">
                                                Đã Hủy ({{ $appointments->where('Status', 'Hủy Hẹn')->count() }})
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Bất động sản</th>
                                            <th>Ngày & Giờ</th>
                                            <th>Môi giới</th>
                                            <th>Trạng thái</th>
                                            <th class="text-end">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($appointments as $appointment)
                                            <tr data-status="{{ 
                                                $appointment->Status == 'Khởi Tạo' ? 'khoitao' : 
                                                ($appointment->Status == 'Đang Thực Hiện' ? 'dangthuchien' : 
                                                ($appointment->Status == 'Hoàn Thành' ? 'hoanthanh' : 
                                                ($appointment->Status == 'Hủy Hẹn' ? 'huyhen' : 'unknown')))
                                            }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="property-thumb me-2">
                                                            @if($appointment->property && $appointment->property->images->first())
                                                                <img src="data:image/jpeg;base64,{{ base64_encode($appointment->property->images->first()->ImagePath) }}" 
                                                                     class="rounded" style="width: 40px; height: 40px; object-fit: cover;" 
                                                                     alt="Property">
                                                            @else
                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                    <i class="fa fa-home text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $appointment->property ? $appointment->property->Title : $appointment->TitleAppoint }}</div>
                                                            <small class="text-muted">{{ $appointment->property ? $appointment->property->Address : 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $appointment->AppointmentDateStart ? \Carbon\Carbon::parse($appointment->AppointmentDateStart)->format('d/m/Y') : 'N/A' }}</strong>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $appointment->AppointmentDateStart ? \Carbon\Carbon::parse($appointment->AppointmentDateStart)->format('H:i') : 'N/A' }} - 
                                                        {{ $appointment->AppointmentDateEnd ? \Carbon\Carbon::parse($appointment->AppointmentDateEnd)->format('H:i') : 'N/A' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="agent-avatar me-2">
                                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; font-size: 12px;">
                                                                {{ $appointment->user_agent ? strtoupper(substr($appointment->user_agent->Name, 0, 2)) : 'N/A' }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $appointment->user_agent ? $appointment->user_agent->Name : 'Chưa phân công' }}</div>
                                                            <small class="text-muted">{{ $appointment->user_agent ? $appointment->user_agent->Phone : '' }}</small>
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
                                                            <i class="fa fa-eye"></i> Chi tiết
                                                        </button>
                                                        @if($appointment->Status == 'Khởi Tạo')
                                                            <button class="btn btn-outline-success btn-sm" onclick="updateAppointmentStatus({{ $appointment->AppointmentID }}, 'Đang Thực Hiện')">
                                                                <i class="fa fa-check"></i> Xác nhận
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-sm" onclick="updateAppointmentStatus({{ $appointment->AppointmentID }}, 'Hủy Hẹn')">
                                                                <i class="fa fa-times"></i> Hủy
                                                            </button>
                                                        @endif
                                                    </div>
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
    </div>
</div>

<!-- Original Tab Content tạo trước đây - giữ lại nhưng ẩn đi -->
<div class="d-none">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="appointmentsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">
                                    Sắp tới <span class="badge bg-primary">{{ $upcomingAppointments->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">
                                    Đã hoàn thành <span class="badge bg-success">{{ $completedAppointments->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab" aria-controls="cancelled" aria-selected="false">
                                    Đã hủy <span class="badge bg-danger">{{ $cancelledAppointments->count() }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="appointmentsTabsContent">
                            <!-- Upcoming Appointments Tab -->
                            <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                                @if($upcomingAppointments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Mã hẹn</th>
                                                    <th>Bất động sản</th>
                                                    <th>Môi giới</th>
                                                    <th>Khách hàng</th>
                                                    <th>Tiêu đề</th>
                                                    <th>Ngày & Giờ bắt đầu</th>
                                                    <th>Ngày & Giờ kết thúc</th>
                                                    <th>Trạng thái</th>
                                                    <th>Mô tả</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($upcomingAppointments as $appointment)
                                                    <tr>
                                                        <td>{{ $appointment->AppointmentID }}</td>
                                                        <td>{{ $appointment->property ? $appointment->property->Title : 'N/A' }}</td>
                                                        <td>{{ $appointment->user_agent ? $appointment->user_agent->Name : 'N/A' }}</td>
                                                        <td>{{ $appointment->user_customer ? $appointment->user_customer->Name : 'N/A' }}</td>
                                                        <td>{{ $appointment->TitleAppoint }}</td>
                                                        <td>{{ $appointment->AppointmentDateStart ? date('d/m/Y H:i', strtotime($appointment->AppointmentDateStart)) : 'N/A' }}</td>
                                                        <td>{{ $appointment->AppointmentDateEnd ? date('d/m/Y H:i', strtotime($appointment->AppointmentDateEnd)) : 'N/A' }}</td>
                                                        <td>
                                                            @if($appointment->Status == 'Khởi Tạo')
                                                                <span class="badge bg-warning">Chờ xác nhận</span>
                                                            @elseif($appointment->Status == 'Hoàn Thành')
                                                                <span class="badge bg-info">Đã Xác Nhận</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $appointment->DescAppoint }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">Không có lịch hẹn sắp tới nào.</div>
                                @endif
                            </div>

                            <!-- Completed Appointments Tab -->
                            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                                @if($completedAppointments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Mã hẹn</th>
                                                    <th>Bất động sản</th>
                                                    <th>Môi giới</th>
                                                    <th>Khách hàng</th>
                                                    <th>Tiêu đề</th>
                                                    <th>Ngày & Giờ bắt đầu</th>
                                                    <th>Ngày & Giờ kết thúc</th>
                                                    <th>Trạng thái</th>
                                                    <th>Mô tả</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($completedAppointments as $appointment)
                                                    <tr>
                                                        <td>{{ $appointment->AppointmentID }}</td>
                                                        <td>{{ $appointment->property ? $appointment->property->Title : 'N/A' }}</td>
                                                        <td>{{ $appointment->user_agent ? $appointment->user_agent->Name : 'N/A' }}</td>
                                                        <td>{{ $appointment->user_customer ? $appointment->user_customer->Name : 'N/A' }}</td>
                                                        <td>{{ $appointment->TitleAppoint }}</td>
                                                        <td>{{ $appointment->AppointmentDateStart ? date('d/m/Y H:i', strtotime($appointment->AppointmentDateStart)) : 'N/A' }}</td>
                                                        <td>{{ $appointment->AppointmentDateEnd ? date('d/m/Y H:i', strtotime($appointment->AppointmentDateEnd)) : 'N/A' }}</td>
                                                        <td><span class="badge bg-success">Hoàn thành</span></td>
                                                        <td>{{ $appointment->DescAppoint }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">Không có lịch hẹn đã hoàn thành nào.</div>
                                @endif
                            </div>

                            <!-- Cancelled Appointments Tab -->
                            <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                                @if($cancelledAppointments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Mã hẹn</th>
                                                    <th>Bất động sản</th>
                                                    <th>Môi giới</th>
                                                    <th>Khách hàng</th>
                                                    <th>Tiêu đề</th>
                                                    <th>Ngày & Giờ bắt đầu</th>
                                                    <th>Ngày & Giờ kết thúc</th>
                                                    <th>Trạng thái</th>
                                                    <th>Mô tả</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cancelledAppointments as $appointment)
                                                    <tr>
                                                        <td>{{ $appointment->AppointmentID }}</td>
                                                        <td>{{ $appointment->property ? $appointment->property->Title : 'N/A' }}</td>
                                                        <td>{{ $appointment->user_agent ? $appointment->user_agent->Name : 'N/A' }}</td>
                                                        <td>{{ $appointment->user_customer ? $appointment->user_customer->Name : 'N/A' }}</td>
                                                        <td>{{ $appointment->TitleAppoint }}</td>
                                                        <td>{{ $appointment->AppointmentDateStart ? date('d/m/Y H:i', strtotime($appointment->AppointmentDateStart)) : 'N/A' }}</td>
                                                        <td>{{ $appointment->AppointmentDateEnd ? date('d/m/Y H:i', strtotime($appointment->AppointmentDateEnd)) : 'N/A' }}</td>
                                                        <td><span class="badge bg-danger">Đã hủy</span></td>
                                                        <td>{{ $appointment->DescAppoint }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">Không có lịch hẹn đã hủy nào.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('js/appointment-filters-owner.js') }}"></script>
@endsection
