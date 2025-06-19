@extends('_layout._layhome.home')

@section('title', 'Dashboard - Chủ sở hữu')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Chào mừng, {{ $user->Name }}</h2>
                    <p class="text-muted mb-0">Quản lý bất động sản của bạn</p>
                </div>
                <div>
                    <span class="badge bg-success fs-6">Chủ sở hữu</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-6 text-primary mb-2">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['total_properties'] }}</h3>
                    <p class="text-muted mb-0">Tổng BĐS</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-6 text-success mb-2">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['active_properties'] }}</h3>
                    <p class="text-muted mb-0">Đang hoạt động</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-6 text-warning mb-2">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['pending_properties'] }}</h3>
                    <p class="text-muted mb-0">Chờ duyệt</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-6 text-info mb-2">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="mb-1">{{ $stats['pending_appointments'] }}</h3>
                    <p class="text-muted mb-0">Lịch hẹn chờ</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Properties List -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2 text-primary"></i>Bất động sản của bạn
                    </h5>
                </div>
                <div class="card-body">
                    @if($properties->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tiêu đề</th>
                                        <th>Loại</th>
                                        <th>Giá</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày đăng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($properties as $property)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($property->images->first())
                                                    @php
                                                        $imageUrl = \App\Helpers\ImageHelper::getImageUrl($property->images->first()->ImagePath);
                                                    @endphp
                                                    <img src="{{ $imageUrl }}"
                                                         alt="Property" class="rounded me-2"
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ Str::limit($property->Title, 30) }}</div>
                                                    <small class="text-muted">{{ $property->Address }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $property->danhMuc->ten_pro ?? 'N/A' }}</span>
                                        </td>
                                        <td class="fw-semibold">
                                            {{ number_format($property->Price) }}
                                            @if($property->TypePro == 'Rent')
                                                VNĐ/tháng
                                            @else
                                                VNĐ
                                            @endif
                                        </td>
                                        <td>
                                            @if($property->Status == 'active')
                                                <span class="badge bg-success">Hoạt động</span>
                                            @elseif($property->Status == 'pending')
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $property->Status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $property->PostedDate->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $properties->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-home fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có bất động sản nào</h5>
                            <p class="text-muted">Liên hệ với admin để đăng bất động sản</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Appointments -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check me-2 text-success"></i>Lịch hẹn gần đây
                    </h5>
                </div>
                <div class="card-body">
                    @if($appointments->count() > 0)
                        @foreach($appointments as $appointment)
                        <div class="d-flex align-items-center border-bottom pb-2 mb-2">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-semibold">{{ $appointment->user_customer->Name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ Str::limit($appointment->property->Title, 25) }}</small>
                                <div class="small text-primary">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($appointment->AppointmentDateStart)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Không có lịch hẹn nào</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
