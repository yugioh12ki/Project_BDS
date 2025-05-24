@extends('_layout._layagent.app')

@section('title', 'Trang quản lý Agent')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h3 mb-2">Bảng điều khiển</h1>
    <p class="text-muted">Chào mừng trở lại, {{ auth()->user()->name ?? 'Nguyễn Văn A' }}</p>

    <div class="row mt-4">
        <!-- Phân công môi giới -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded p-2 me-3">
                            <i class="bi bi-person-workspace text-primary fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">Phân công môi giới</h5>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Đang hoạt động</span>
                            <span class="text-success">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Chờ xử lý</span>
                            <span class="text-warning">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng số</span>
                            <span class="text-primary fw-bold">0</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('agent.brokers') }}" class="text-primary text-decoration-none">
                            Xem chi tiết <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch hẹn xem nhà -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded p-2 me-3">
                            <i class="bi bi-calendar-event text-primary fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">Lịch hẹn xem nhà</h5>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Chờ xử lý</span>
                            <span class="text-warning">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Thành công</span>
                            <span class="text-success">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Đã hủy</span>
                            <span class="text-danger">0</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('agent.appointments') }}" class="text-primary text-decoration-none">
                            Xem chi tiết <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Giao dịch -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded p-2 me-3">
                            <i class="bi bi-currency-exchange text-primary fs-4"></i>
                        </div>
                        <h5 class="card-title mb-0">Giao dịch</h5>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Hoàn thành</span>
                            <span class="text-success">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Đang xử lý</span>
                            <span class="text-warning">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng số</span>
                            <span class="text-primary fw-bold">0</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('agent.transactions') }}" class="text-primary text-decoration-none">
                            Xem chi tiết <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch hẹn hôm nay -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Lịch hẹn hôm nay</h5>
                    
                    <div class="text-center py-5 text-muted">
                        <p>Không có lịch hẹn nào hôm nay</p>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('agent.appointments') }}" class="text-primary text-decoration-none">
                            Xem tất cả lịch hẹn <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection