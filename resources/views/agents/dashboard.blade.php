@extends('_layout._layagent.app')

@section('title', 'Trang quản lý Agent')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Trang quản lý Agent</li>
                    </ol>
                </div>
                <h4 class="page-title">Trang quản lý Agent</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('agent.profile') }}" class="dropdown-item">Xem hồ sơ</a>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start">
                        <img src="{{ asset('assets/images/users/avatar-default.jpg') }}" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">
                        <div class="w-100 ms-3">
                            <h4 class="mt-0 mb-0">{{ $agent->name }}</h4>
                            <p class="text-muted">{{ $agent->email }}</p>
                            <a href="{{ route('agent.profile') }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-account-edit me-1"></i> Chỉnh sửa hồ sơ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <div class="float-end">
                                <i class="mdi mdi-home-city widget-icon"></i>
                            </div>
                            <h5 class="text-muted fw-normal mt-0" title="Số lượng BDS">BĐS đang quản lý</h5>
                            <h3 class="mt-3 mb-3">{{ $propertyCount }}</h3>
                            <p class="mb-0 text-muted">
                                <a href="{{ route('agent.listings') }}" class="text-primary">Xem tất cả <i class="mdi mdi-arrow-right"></i></a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <div class="float-end">
                                <i class="mdi mdi-calendar-check widget-icon"></i>
                            </div>
                            <h5 class="text-muted fw-normal mt-0" title="Số lượng cuộc hẹn">Cuộc hẹn sắp tới</h5>
                            <h3 class="mt-3 mb-3">0</h3>
                            <p class="mb-0 text-muted">
                                <a href="{{ route('agent.appointments') }}" class="text-primary">Xem tất cả <i class="mdi mdi-arrow-right"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Hoạt động gần đây</h4>
                            
                            @if(count($recentItems) > 0)
                                <div class="table-responsive">
                                    <table class="table table-centered table-nowrap table-hover mb-0">
                                        <tbody>
                                            @foreach($recentItems as $item)
                                                <tr>
                                                    <td>{!! $item->content ?? '' !!}</td>
                                                    <td class="text-end">{{ $item->created_at ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-center mt-3">Không có hoạt động nào gần đây.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 