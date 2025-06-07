@extends('_layout._layowner.app')

@section('title', 'Lịch sử giao dịch')

@section('content')
<div class="container-fluid">
    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Lịch sử giao dịch</h1>
        <div>
            <a href="{{ route('owner.transactions.revenue') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-chart-line fa-sm text-white-50"></i> Quản lý doanh thu
            </a>
            <a href="{{ route('owner.transactions.commissions') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-money-bill-wave fa-sm text-white-50"></i> Quản lý hoa hồng
            </a>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm" 
               data-toggle="modal" data-target="#filterModal">
                <i class="fas fa-filter fa-sm text-white-50"></i> Bộ lọc
            </a>
        </div>
    </div>

    <!-- Thống kê theo tháng -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê theo tháng</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Số giao dịch</th>
                                    <th>Tổng giá trị</th>
                                    <th>Đã thanh toán</th>
                                    <th>Giá trị bán</th>
                                    <th>Giá trị cho thuê</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyStats as $stat)
                                <tr>
                                    <td>{{ $stat->month }}/{{ $stat->year }}</td>
                                    <td>{{ $stat->total_transactions }}</td>
                                    <td>{{ number_format($stat->total_value) }} VNĐ</td>
                                    <td>{{ number_format($stat->paid_amount) }} VNĐ</td>
                                    <td>{{ number_format($stat->sale_value) }} VNĐ</td>
                                    <td>{{ number_format($stat->rental_value) }} VNĐ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê hoa hồng -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thống kê hoa hồng theo người môi giới</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Người môi giới</th>
                                    <th>Tổng hoa hồng</th>
                                    <th>Hoa hồng cho thuê</th>
                                    <th>Hoa hồng bán</th>
                                    <th>Đã thanh toán</th>
                                    <th>Chờ thanh toán</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commissionSummary as $commission)
                                <tr>
                                    <td>{{ $commission->AgentName }}</td>
                                    <td>{{ number_format($commission->TotalCommission) }} VNĐ</td>
                                    <td>{{ number_format($commission->RentCommission) }} VNĐ</td>
                                    <td>{{ number_format($commission->SaleCommission) }} VNĐ</td>
                                    <td>{{ number_format($commission->PaidCommission) }} VNĐ</td>
                                    <td>{{ number_format($commission->PendingCommission) }} VNĐ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách giao dịch -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách giao dịch</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="transactionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã GD</th>
                            <th>Ngày giao dịch</th>
                            <th>Bất động sản</th>
                            <th>Loại GD</th>
                            <th>Giá trị</th>
                            <th>Khách hàng</th>
                            <th>Người môi giới</th>
                            <th>Hoa hồng</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>
                                <a href="{{ route('owner.transactions.show', $transaction->TransactionID) }}">
                                    {{ $transaction->TransactionID }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($transaction->TransactionDate)->format('d/m/Y') }}</td>
                            <td>{{ $transaction->PropertyTitle }}</td>
                            <td>
                                @if($transaction->TransactionType == 'Sale')
                                    <span class="badge badge-primary">Bán</span>
                                @else
                                    <span class="badge badge-info">Cho thuê</span>
                                @endif
                            </td>
                            <td>{{ number_format($transaction->TotalPrice) }} VNĐ</td>
                            <td>{{ $transaction->CustomerName }}</td>
                            <td>{{ $transaction->AgentName }}</td>
                            <td>
                                @if($transaction->CommissionAmount)
                                    {{ number_format($transaction->CommissionAmount) }} VNĐ
                                    <small class="d-block">({{ $transaction->CommissionPercentage * 100 }}%)</small>
                                    
                                    @if($transaction->StatusCommission == 'Success')
                                        <span class="badge badge-success">Đã thanh toán</span>
                                    @elseif($transaction->StatusCommission == 'Pending')
                                        <span class="badge badge-warning">Chờ thanh toán</span>
                                    @else
                                        <span class="badge badge-danger">Đã hủy</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary">Chưa thiết lập</span>
                                @endif
                            </td>
                            <td>
                                @if($transaction->TranStatus == 'Paid')
                                    <span class="badge badge-success">Đã thanh toán</span>
                                @elseif($transaction->TranStatus == 'Pending')
                                    <span class="badge badge-warning">Chờ thanh toán</span>
                                @else
                                    <span class="badge badge-danger">Đã hủy</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Filter -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('owner.transactions.history') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Lọc giao dịch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="from_date">Từ ngày:</label>
                        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $fromDate }}">
                    </div>
                    <div class="form-group">
                        <label for="to_date">Đến ngày:</label>
                        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $toDate }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#transactionsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Vietnamese.json"
            },
            "pageLength": 25,
            "order": [[ 1, "desc" ]]
        });    });
</script>
@endpush
