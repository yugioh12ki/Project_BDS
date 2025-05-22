@extends('_layout._layagent.app')

@section('title', 'Lịch hẹn')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}">Trang quản lý</a></li>
                        <li class="breadcrumb-item active">Lịch hẹn</li>
                    </ol>
                </div>
                <h4 class="page-title">Lịch hẹn</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <div class="mt-2">
                                <h5>Danh sách lịch hẹn</h5>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Tìm kiếm..." id="search-input">
                                    <button class="btn btn-primary" type="button">Tìm kiếm</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(count($appointments) > 0)
                        <div class="table-responsive">
                            <table class="table table-centered table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Khách hàng</th>
                                        <th>Bất động sản</th>
                                        <th>Ngày hẹn</th>
                                        <th>Giờ hẹn</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointments as $index => $appointment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $appointment->customer_name ?? 'Không có thông tin' }}</td>
                                        <td>{{ $appointment->property->Title ?? 'Không có thông tin' }}</td>
                                        <td>{{ $appointment->date ?? '' }}</td>
                                        <td>{{ $appointment->time ?? '' }}</td>
                                        <td>
                                            @if(isset($appointment->status))
                                                @if($appointment->status == 'pending')
                                                    <span class="badge bg-warning">Chờ xác nhận</span>
                                                @elseif($appointment->status == 'confirmed')
                                                    <span class="badge bg-success">Đã xác nhận</span>
                                                @elseif($appointment->status == 'canceled')
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                @elseif($appointment->status == 'completed')
                                                    <span class="badge bg-info">Đã hoàn thành</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Không có trạng thái</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="mdi mdi-dots-horizontal"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#">Xem chi tiết</a>
                                                    <a class="dropdown-item" href="#">Xác nhận</a>
                                                    <a class="dropdown-item" href="#">Hủy lịch hẹn</a>
                                                    <a class="dropdown-item" href="#">Báo cáo kết quả</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center my-5">
                            <h4>Không có lịch hẹn nào</h4>
                            <p class="text-muted">Hiện tại bạn chưa có lịch hẹn nào được đặt.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Lịch trình hôm nay</h5>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 