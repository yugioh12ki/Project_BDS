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
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="fw-bold">Chung cư cao cấp The Sun Avenue</div>
                        <small class="text-muted">28 Mai Chí Thọ, An Phú, Quận 2</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="fw-bold">Biệt thự Vinhomes Central Park</div>
                        <small class="text-muted">720A Điện Biên Phủ, Quận Bình Thạnh</small>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="fw-bold">Nhà phố Thảo Điền</div>
                        <small class="text-muted">25 Xuân Thủy, Thảo Điền, Quận 2</small>
                    </a>
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
                                    <th>Mã</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày hẹn</th>
                                    <th>Giờ hẹn</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>APP001</td>
                                    <td>
                                        <div>Võ Anh Tuấn</div>
                                        <small class="text-muted">0901234567</small>
                                    </td>
                                    <td>2025-06-05</td>
                                    <td>09:00</td>
                                    <td><span class="badge bg-warning">Chờ xử lý</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-success me-1" title="Hoàn thành">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" title="Hủy">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>APP002</td>
                                    <td>
                                        <div>Ngô Thị Hương</div>
                                        <small class="text-muted">0909876543</small>
                                    </td>
                                    <td>2025-06-06</td>
                                    <td>14:00</td>
                                    <td><span class="badge bg-success">Thành công</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>APP003</td>
                                    <td>
                                        <div>Đặng Minh Khôi</div>
                                        <small class="text-muted">0908765432</small>
                                    </td>
                                    <td>2025-06-07</td>
                                    <td>10:30</td>
                                    <td><span class="badge bg-danger">Đã hủy</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
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