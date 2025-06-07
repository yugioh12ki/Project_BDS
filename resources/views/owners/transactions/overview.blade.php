@extends('_layout._layowner.app')

@section('title', 'Quản lý giao dịch và hoa hồng')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Quản lý giao dịch và hoa hồng</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Quản lý giao dịch</h6>
                                </div>
                                <div class="card-body">
                                    <p>Quản lý tất cả các giao dịch bất động sản của bạn.</p>
                                    <ul>
                                        <li>Xem lịch sử giao dịch</li>
                                        <li>Theo dõi trạng thái giao dịch</li>
                                        <li>Xem chi tiết từng giao dịch</li>
                                        <li>In hóa đơn giao dịch</li>
                                    </ul>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('owner.transactions.index') }}" class="btn btn-primary">
                                            Quản lý giao dịch
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Quản lý hoa hồng</h6>
                                </div>
                                <div class="card-body">
                                    <p>Quản lý chi trả hoa hồng cho môi giới.</p>
                                    <ul>
                                        <li>Xem danh sách hoa hồng</li>
                                        <li>Cập nhật trạng thái thanh toán</li>
                                        <li>Thống kê chi phí hoa hồng</li>
                                        <li>Theo dõi hiệu suất của môi giới</li>
                                    </ul>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('owner.transactions.commissions') }}" class="btn btn-success">
                                            Quản lý hoa hồng
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Lịch sử giao dịch</h6>
                                </div>
                                <div class="card-body">
                                    <p>Xem lịch sử giao dịch chi tiết theo thời gian.</p>
                                    <ul>
                                        <li>Thống kê theo tháng</li>
                                        <li>Xem chi tiết từng giao dịch</li>
                                        <li>Tìm kiếm giao dịch</li>
                                        <li>Xuất báo cáo giao dịch</li>
                                    </ul>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('owner.transactions.history') }}" class="btn btn-info">
                                            Xem lịch sử
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Quản lý doanh thu</h6>
                                </div>
                                <div class="card-body">
                                    <p>Phân tích doanh thu từ giao dịch bất động sản.</p>
                                    <ul>
                                        <li>Biểu đồ doanh thu theo tháng</li>
                                        <li>Thống kê doanh thu</li>
                                        <li>So sánh doanh thu từ bán và cho thuê</li>
                                        <li>Phân tích chi phí hoa hồng</li>
                                    </ul>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('owner.transactions.revenue') }}" class="btn btn-warning">
                                            Quản lý doanh thu
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Tóm tắt giao dịch gần đây</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Tổng giao dịch</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-transactions">--</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Doanh thu tháng này</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="month-revenue">--</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Hoa hồng chưa thanh toán</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pending-commission">--</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Fetch transaction summary data
        $.ajax({
            url: '{{ route('owner.transactions.index') }}',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#total-transactions').text(data.transactionCount);
                $('#month-revenue').text(new Intl.NumberFormat('vi-VN').format(data.totalValue) + ' VNĐ');
                $('#pending-commission').text(new Intl.NumberFormat('vi-VN').format(data.commissionStats.pending_commission) + ' VNĐ');
            },
            error: function() {
                console.log('Error fetching transaction data');
            }
        });    });
</script>
@endpush
@endsection
