@extends('_layout._layowner.app')

@section('show')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Chi tiết Giao dịch #{{ $transaction->TransactionID }}</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('transaction.print', $transaction->TransactionID) }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-printer"></i> In hóa đơn
            </a>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Transaction Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin giao dịch</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Mã giao dịch</label>
                            <p class="fw-bold">{{ $transaction->TransactionID }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Ngày giao dịch</label>
                            <p class="fw-bold">{{ \Carbon\Carbon::parse($transaction->TransactionDate)->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Loại giao dịch</label>
                            <p class="fw-bold">
                                <span class="badge bg-{{ $transaction->TransactionType == 'Bán' ? 'primary' : 'success' }}">
                                    {{ $transaction->TransactionType }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Trạng thái</label>
                            <p class="fw-bold">
                                @php
                                    $statusClass = [
                                        'Hoàn thành' => 'success',
                                        'Đang xử lý' => 'warning',
                                        'Đã hủy' => 'danger',
                                    ][$transaction->Status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ $transaction->Status }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Giá trị giao dịch</label>
                            <p class="fw-bold">{{ number_format($transaction->Amount, 0) }} VND</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Hoa hồng</label>
                            <p class="fw-bold">{{ number_format($transaction->Commission ?? 0, 0) }} VND</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Phương thức thanh toán</label>
                            <p class="fw-bold">{{ $transaction->PaymentMethod ?? 'Chưa cập nhật' }}</p>
                        </div>
                    </div>
                    
                    @if($transaction->Description)
                    <hr>
                    <div class="mb-3">
                        <label class="text-muted">Mô tả giao dịch</label>
                        <p>{{ $transaction->Description }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Property Information -->
            @if($transaction->property)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Thông tin bất động sản</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex mb-3">
                        <div class="property-img me-3">
                            <img src="{{ asset('images/properties/' . ($transaction->property->featured_image ?? 'default.jpg')) }}" 
                                 alt="{{ $transaction->property->Title }}" 
                                 class="img-thumbnail" style="width: 120px; height: 80px; object-fit: cover;">
                        </div>
                        <div>
                            <h5>{{ $transaction->property->Title }}</h5>
                            <p>{{ $transaction->property->Address }}, {{ $transaction->property->Ward }}, {{ $transaction->property->District }}, {{ $transaction->property->Province }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="text-muted">Loại bất động sản</label>
                            <p>{{ $transaction->property->PropertyType }}</p>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="text-muted">Diện tích</label>
                            <p>{{ $transaction->property->chiTiet->Area ?? 'N/A' }} m²</p>
                        </div>
                    </div>
                    
                    <a href="{{ route('property.show', $transaction->property->PropertyID) }}" class="btn btn-sm btn-outline-primary">
                        Xem chi tiết BĐS
                    </a>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Parties Information -->
        <div class="col-md-4">
            <!-- Owner Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Chủ sở hữu</h5>
                </div>
                <div class="card-body">
                    @if($transaction->trans_owner)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar me-3">
                            <img src="{{ asset('images/avatars/' . ($transaction->trans_owner->Avatar ?? 'default-avatar.jpg')) }}" 
                                 alt="{{ $transaction->trans_owner->FullName }}" 
                                 class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $transaction->trans_owner->FullName }}</h6>
                            <small class="text-muted">{{ $transaction->trans_owner->Email }}</small>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <label class="text-muted">Số điện thoại</label>
                        <p>{{ $transaction->trans_owner->PhoneNumber }}</p>
                    </div>
                    @else
                    <p class="text-muted">Không có thông tin chủ sở hữu</p>
                    @endif
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Khách hàng</h5>
                </div>
                <div class="card-body">
                    @if($transaction->trans_cus)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar me-3">
                            <img src="{{ asset('images/avatars/' . ($transaction->trans_cus->Avatar ?? 'default-avatar.jpg')) }}" 
                                 alt="{{ $transaction->trans_cus->FullName }}" 
                                 class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $transaction->trans_cus->FullName }}</h6>
                            <small class="text-muted">{{ $transaction->trans_cus->Email }}</small>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <label class="text-muted">Số điện thoại</label>
                        <p>{{ $transaction->trans_cus->PhoneNumber }}</p>
                    </div>
                    @else
                    <p class="text-muted">Không có thông tin khách hàng</p>
                    @endif
                </div>
            </div>
            
            <!-- Agent Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Môi giới</h5>
                </div>
                <div class="card-body">
                    @if($transaction->trans_agent)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar me-3">
                            <img src="{{ asset('images/avatars/' . ($transaction->trans_agent->Avatar ?? 'default-avatar.jpg')) }}" 
                                 alt="{{ $transaction->trans_agent->FullName }}" 
                                 class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $transaction->trans_agent->FullName }}</h6>
                            <small class="text-muted">{{ $transaction->trans_agent->Email }}</small>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <label class="text-muted">Số điện thoại</label>
                        <p>{{ $transaction->trans_agent->PhoneNumber }}</p>
                    </div>
                    @else
                    <p class="text-muted">Không có thông tin môi giới</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 