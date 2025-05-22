@extends('_layout._layagent.app')

@section('title', 'Danh sách BĐS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}">Trang quản lý</a></li>
                        <li class="breadcrumb-item active">Danh sách BĐS</li>
                    </ol>
                </div>
                <h4 class="page-title">Danh sách BĐS</h4>
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
                                <h5>Danh sách bất động sản</h5>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Tìm kiếm bất động sản..." id="search-input">
                                    <button class="btn btn-primary" type="button">Tìm kiếm</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(count($properties) > 0)
                        <div class="table-responsive">
                            <table class="table table-centered table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Mã BĐS</th>
                                        <th>Hình ảnh</th>
                                        <th>Tiêu đề</th>
                                        <th>Địa chỉ</th>
                                        <th>Loại</th>
                                        <th>Giá</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($properties as $property)
                                    <tr>
                                        <td>{{ $property->PropertyID ?? '' }}</td>
                                        <td>
                                            @if(isset($property->images) && $property->images->count() > 0)
                                                @php
                                                    $thumbnail = $property->images->firstWhere('IsThumbnail', 1) ?? $property->images->first();
                                                @endphp
                                                <img src="{{ $thumbnail->ImageURL ?? asset('assets/images/no-image.jpg') }}" alt="{{ $property->Title }}" class="rounded" height="48">
                                            @else
                                                <img src="{{ asset('assets/images/no-image.jpg') }}" alt="No Image" class="rounded" height="48">
                                            @endif
                                        </td>
                                        <td>{{ $property->Title ?? '' }}</td>
                                        <td>{{ $property->Address ?? '' }}, {{ $property->Ward ?? '' }}, {{ $property->District ?? '' }}, {{ $property->Province ?? '' }}</td>
                                        <td>{{ $property->danhMuc->ten_pro ?? '' }}</td>
                                        <td>
                                            @if(isset($property->Price))
                                                @if($property->TypePro == 'Sale')
                                                    {{ number_format($property->Price, 0, ',', '.') }} VNĐ
                                                @else
                                                    {{ number_format($property->Price, 0, ',', '.') }} VNĐ/tháng
                                                @endif
                                            @else
                                                Liên hệ
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($property->Status))
                                                @if($property->Status == 1)
                                                    <span class="badge bg-success">Đang bán/cho thuê</span>
                                                @elseif($property->Status == 2)
                                                    <span class="badge bg-warning">Đang giao dịch</span>
                                                @elseif($property->Status == 0)
                                                    <span class="badge bg-danger">Đã bán/cho thuê</span>
                                                @endif
                                            @else
                                                <span class="badge bg-info">Chưa cập nhật</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="mdi mdi-dots-horizontal"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#">Xem chi tiết</a>
                                                    <a class="dropdown-item" href="#">Cập nhật</a>
                                                    <a class="dropdown-item" href="#">Lịch sử giao dịch</a>
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
                            <h4>Không có BĐS nào được tìm thấy</h4>
                            <p class="text-muted">Hiện tại bạn chưa có BĐS nào được giao quản lý.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 