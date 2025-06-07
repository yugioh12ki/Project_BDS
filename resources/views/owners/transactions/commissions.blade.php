@extends('_layout._layowner.app')

@section('title', 'Quản lý hoa hồng')

@section('styles')
<style>
    .commission-badge {
        font-size: 0.85em;
    }
    .action-buttons .btn {
        margin-right: 5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý hoa hồng cho môi giới</h1>
        <div>
            <a href="{{ route('owner.transactions.history') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-list fa-sm text-white-50"></i> Lịch sử giao dịch
            </a>
            <a href="{{ route('owner.transactions.revenue') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-chart-line fa-sm text-white-50"></i> Quản lý doanh thu
            </a>
            <div class="btn-group ml-2">
                <a href="{{ route('owner.transactions.commissions') }}" class="btn btn-sm btn-{{ $status == 'all' ? 'secondary' : 'outline-secondary' }}">Tất cả</a>
                <a href="{{ route('owner.transactions.commissions', ['status' => 'Pending']) }}" class="btn btn-sm btn-{{ $status == 'Pending' ? 'warning' : 'outline-warning' }}">Chờ thanh toán</a>
                <a href="{{ route('owner.transactions.commissions', ['status' => 'Success']) }}" class="btn btn-sm btn-{{ $status == 'Success' ? 'success' : 'outline-success' }}">Đã thanh toán</a>
                <a href="{{ route('owner.transactions.commissions', ['status' => 'Cancelled']) }}" class="btn btn-sm btn-{{ $status == 'Cancelled' ? 'danger' : 'outline-danger' }}">Đã hủy</a>
            </div>
        </div>
    </div>

    <!-- Thẻ thống kê -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng hoa hồng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($commissionStats['total']) }} VNĐ</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $commissionStats['count'] }} giao dịch</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Đã thanh toán</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($commissionStats['paid']) }} VNĐ</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $commissions->where('StatusCommission', 'Success')->count() }} giao dịch</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Chờ thanh toán</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($commissionStats['pending']) }} VNĐ</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $commissions->where('StatusCommission', 'Pending')->count() }} giao dịch</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Đã hủy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($commissionStats['cancelled']) }} VNĐ</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $commissions->where('StatusCommission', 'Cancelled')->count() }} giao dịch</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê theo loại -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê theo loại giao dịch</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="text-s font-weight-bold text-info text-uppercase mb-1">Hoa hồng cho thuê</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($typeStats['rent']['total']) }} VNĐ</div>
                                <div class="text-xs text-gray-500 mt-2">{{ $typeStats['rent']['count'] }} giao dịch</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="text-s font-weight-bold text-primary text-uppercase mb-1">Hoa hồng bán</div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($typeStats['sale']['total']) }} VNĐ</div>
                                <div class="text-xs text-gray-500 mt-2">{{ $typeStats['sale']['count'] }} giao dịch</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="progress" style="height: 25px;">
                        @if($commissionStats['total'] > 0)
                        <div class="progress-bar bg-info text-white" role="progressbar" style="width: {{ ($typeStats['rent']['total'] / $commissionStats['total']) * 100 }}%">
                            {{ round(($typeStats['rent']['total'] / $commissionStats['total']) * 100) }}% Cho thuê
                        </div>
                        <div class="progress-bar bg-primary text-white" role="progressbar" style="width: {{ ($typeStats['sale']['total'] / $commissionStats['total']) * 100 }}%">
                            {{ round(($typeStats['sale']['total'] / $commissionStats['total']) * 100) }}% Bán
                        </div>
                        @else
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê theo người môi giới</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Môi giới</th>
                                    <th>Tổng hoa hồng</th>
                                    <th>Đã thanh toán</th>
                                    <th>Chờ thanh toán</th>
                                    <th>Số GD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agentStats as $agentId => $stats)
                                <tr>
                                    <td>{{ $stats['name'] }}</td>
                                    <td>{{ number_format($stats['total']) }}</td>
                                    <td>{{ number_format($stats['paid']) }}</td>
                                    <td>{{ number_format($stats['pending']) }}</td>
                                    <td>{{ $stats['count'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách hoa hồng -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách hoa hồng</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="commissionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã GD</th>
                            <th>Ngày GD</th>
                            <th>Bất động sản</th>
                            <th>Loại GD</th>
                            <th>Giá trị GD</th>
                            <th>Người môi giới</th>
                            <th>Giá trị hoa hồng</th>
                            <th>%</th>
                            <th>Trạng thái</th>
                            <th>Ngày thanh toán</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissions as $commission)
                        <tr id="commission-{{ $commission->CommissionID }}">
                            <td>{{ $commission->CommissionID }}</td>
                            <td>
                                <a href="{{ route('owner.transactions.show', $commission->TransactionID) }}">
                                    {{ $commission->TransactionID }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($commission->TransactionDate)->format('d/m/Y') }}</td>
                            <td>{{ $commission->PropertyTitle }}</td>
                            <td>
                                @if($commission->TypeCom == 'Sale')
                                <span class="badge badge-primary">Bán</span>
                                @else
                                <span class="badge badge-info">Cho thuê</span>
                                @endif
                            </td>
                            <td>{{ number_format($commission->TotalPrice) }} VNĐ</td>
                            <td>{{ $commission->AgentName }}</td>
                            <td>{{ number_format($commission->Amount) }} VNĐ</td>
                            <td>{{ $commission->Percentage * 100 }}%</td>
                            <td class="status-col">
                                @if($commission->StatusCommission == 'Success')
                                <span class="badge badge-success">Đã thanh toán</span>
                                @elseif($commission->StatusCommission == 'Pending')
                                <span class="badge badge-warning">Chờ thanh toán</span>
                                @else
                                <span class="badge badge-danger">Đã hủy</span>
                                @endif
                            </td>
                            <td class="paid-date-col">
                                {{ $commission->PaidDate ? \Carbon\Carbon::parse($commission->PaidDate)->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                <div class="action-buttons">
                                    @if($commission->StatusCommission == 'Pending')
                                    <button class="btn btn-success btn-sm update-status" data-id="{{ $commission->CommissionID }}" data-status="Success">
                                        <i class="fas fa-check"></i> Thanh toán
                                    </button>
                                    <button class="btn btn-danger btn-sm update-status" data-id="{{ $commission->CommissionID }}" data-status="Cancelled">
                                        <i class="fas fa-ban"></i> Hủy
                                    </button>
                                    @elseif($commission->StatusCommission == 'Success')
                                    <button class="btn btn-warning btn-sm update-status" data-id="{{ $commission->CommissionID }}" data-status="Pending">
                                        <i class="fas fa-undo"></i> Hoàn lại
                                    </button>
                                    @elseif($commission->StatusCommission == 'Cancelled')
                                    <button class="btn btn-warning btn-sm update-status" data-id="{{ $commission->CommissionID }}" data-status="Pending">
                                        <i class="fas fa-redo"></i> Mở lại
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#commissionsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Vietnamese.json"
            },
            "pageLength": 25,
            "order": [[ 2, "desc" ]],
            "columnDefs": [
                { "width": "120px", "targets": 11 }
            ]
        });
        
        // Xử lý cập nhật trạng thái hoa hồng
        $('.update-status').click(function() {
            const commissionId = $(this).data('id');
            const newStatus = $(this).data('status');
            const row = $(`#commission-${commissionId}`);
            
            if (confirm(`Bạn có chắc muốn cập nhật trạng thái hoa hồng thành "${
                newStatus === 'Success' ? 'Đã thanh toán' : 
                newStatus === 'Pending' ? 'Chờ thanh toán' : 'Đã hủy'
            }"?`)) {
                $.ajax({
                    url: `{{ route('owner.transactions.commission.update', '') }}/${commissionId}`,
                    type: 'POST',
                    data: {
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Cập nhật hiển thị trạng thái
                            let statusBadge = '';
                            
                            if (response.new_status === 'Success') {
                                statusBadge = '<span class="badge badge-success">Đã thanh toán</span>';
                            } else if (response.new_status === 'Pending') {
                                statusBadge = '<span class="badge badge-warning">Chờ thanh toán</span>';
                            } else {
                                statusBadge = '<span class="badge badge-danger">Đã hủy</span>';
                            }
                            
                            row.find('.status-col').html(statusBadge);
                            row.find('.paid-date-col').html(response.paid_date || '-');
                            
                            // Cập nhật nút thao tác
                            let actionButtons = '';
                            
                            if (response.new_status === 'Pending') {
                                actionButtons = `
                                    <button class="btn btn-success btn-sm update-status" data-id="${commissionId}" data-status="Success">
                                        <i class="fas fa-check"></i> Thanh toán
                                    </button>
                                    <button class="btn btn-danger btn-sm update-status" data-id="${commissionId}" data-status="Cancelled">
                                        <i class="fas fa-ban"></i> Hủy
                                    </button>
                                `;
                            } else if (response.new_status === 'Success') {
                                actionButtons = `
                                    <button class="btn btn-warning btn-sm update-status" data-id="${commissionId}" data-status="Pending">
                                        <i class="fas fa-undo"></i> Hoàn lại
                                    </button>
                                `;
                            } else {
                                actionButtons = `
                                    <button class="btn btn-warning btn-sm update-status" data-id="${commissionId}" data-status="Pending">
                                        <i class="fas fa-redo"></i> Mở lại
                                    </button>
                                `;
                            }
                            
                            row.find('.action-buttons').html(actionButtons);
                            
                            // Hiển thị thông báo
                            alert(response.message);
                            
                            // Reload trang nếu đang xem theo trạng thái cụ thể
                            const currentStatus = '{{ $status }}';
                            if (currentStatus !== 'all' && currentStatus !== response.new_status) {
                                setTimeout(function() {
                                    location.reload();
                                }, 500);
                            }
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi cập nhật trạng thái hoa hồng');
                    }
                });
            }
        });
    });
</script>
@endpush
