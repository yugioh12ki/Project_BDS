@extends('_layout._layowner.app')

@section('title', 'Quản lý doanh thu')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
<style>
    .stat-card {
        transition: transform .3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Tiêu đề trang -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý doanh thu</h1>
        <div>
            <a href="{{ route('owner.transactions.history') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-list fa-sm text-white-50"></i> Lịch sử giao dịch
            </a>
            <a href="{{ route('owner.transactions.commissions') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-money-bill-wave fa-sm text-white-50"></i> Quản lý hoa hồng
            </a>
            <div class="d-inline-block ml-2">
                <form action="{{ route('owner.transactions.revenue') }}" method="GET" class="d-flex">
                    <select name="year" class="form-control form-control-sm">
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ $year == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary ml-2">Xem</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Thẻ thống kê -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng doanh thu ({{ $year }})</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($annualStats->annual_revenue ?? 0) }} VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Số giao dịch</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $annualStats->annual_transaction_count ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Doanh thu từ cho thuê
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($annualStats->annual_rental_revenue ?? 0) }} VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Doanh thu từ bán</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($annualStats->annual_sale_revenue ?? 0) }} VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Tổng chi trả hoa hồng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($annualCommission->annual_commission ?? 0) }} VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Hoa hồng đã thanh toán</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($annualCommission->paid_commission ?? 0) }} VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Hoa hồng chờ thanh toán</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($annualCommission->pending_commission ?? 0) }} VNĐ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Lợi nhuận (sau hoa hồng)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format(($annualStats->annual_revenue ?? 0) - ($annualCommission->annual_commission ?? 0)) }} VNĐ
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu theo tháng -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Doanh thu theo tháng ({{ $year }})</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ hoa hồng theo tháng -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hoa hồng theo tháng ({{ $year }})</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="commissionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết giao dịch theo tháng (nếu đã chọn tháng) -->
    @if($month && $monthDetail && $monthDetail->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Chi tiết giao dịch tháng {{ $month }}/{{ $year }}</h6>
            <div class="dropdown no-arrow">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="monthSelector" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Chọn tháng
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="monthSelector">
                    @for($i = 1; $i <= 12; $i++)
                        <a class="dropdown-item" href="{{ route('owner.transactions.revenue', ['year' => $year, 'month' => $i]) }}">Tháng {{ $i }}</a>
                    @endfor
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã GD</th>
                            <th>Ngày GD</th>
                            <th>BĐS</th>
                            <th>Loại GD</th>
                            <th>Giá trị</th>
                            <th>Khách hàng</th>
                            <th>Người môi giới</th>
                            <th>Hoa hồng</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthDetail as $transaction)
                        <tr>
                            <td>
                                <a href="{{ route('owner.transactions.show', $transaction->TransactionID) }}">{{ $transaction->TransactionID }}</a>
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
    @elseif($month)
    <div class="alert alert-info">
        Không có giao dịch nào trong tháng {{ $month }}/{{ $year }}.
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    $(document).ready(function() {
        // Dữ liệu cho biểu đồ doanh thu
        const months = ['Th 1', 'Th 2', 'Th 3', 'Th 4', 'Th 5', 'Th 6', 'Th 7', 'Th 8', 'Th 9', 'Th 10', 'Th 11', 'Th 12'];
        const revenueData = Array(12).fill(0);
        const rentalData = Array(12).fill(0);
        const saleData = Array(12).fill(0);
        
        @foreach($monthlyRevenue as $item)
            revenueData[{{ $item->month - 1 }}] = {{ $item->total_revenue }};
            rentalData[{{ $item->month - 1 }}] = {{ $item->rental_revenue }};
            saleData[{{ $item->month - 1 }}] = {{ $item->sale_revenue }};
        @endforeach
        
        // Biểu đồ doanh thu
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Tổng doanh thu',
                        data: revenueData,
                        backgroundColor: 'rgba(78, 115, 223, 0.2)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Doanh thu cho thuê',
                        data: rentalData,
                        backgroundColor: 'rgba(54, 185, 204, 0.2)',
                        borderColor: 'rgba(54, 185, 204, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(54, 185, 204, 1)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Doanh thu bán',
                        data: saleData,
                        backgroundColor: 'rgba(246, 194, 62, 0.2)',
                        borderColor: 'rgba(246, 194, 62, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(246, 194, 62, 1)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 }).format(value);
                            }
                        }
                    }
                }
            }
        });
        
        // Dữ liệu cho biểu đồ hoa hồng
        const commissionData = Array(12).fill(0);
        const rentCommissionData = Array(12).fill(0);
        const saleCommissionData = Array(12).fill(0);
        
        @foreach($monthlyCommission as $item)
            commissionData[{{ $item->month - 1 }}] = {{ $item->total_commission }};
            rentCommissionData[{{ $item->month - 1 }}] = {{ $item->rent_commission }};
            saleCommissionData[{{ $item->month - 1 }}] = {{ $item->sale_commission }};
        @endforeach
        
        // Biểu đồ hoa hồng
        const commissionCtx = document.getElementById('commissionChart').getContext('2d');
        const commissionChart = new Chart(commissionCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Tổng hoa hồng',
                        data: commissionData,
                        backgroundColor: 'rgba(231, 74, 59, 0.7)',
                        borderColor: 'rgba(231, 74, 59, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Hoa hồng cho thuê',
                        data: rentCommissionData,
                        backgroundColor: 'rgba(28, 200, 138, 0.7)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Hoa hồng bán',
                        data: saleCommissionData,
                        backgroundColor: 'rgba(78, 115, 223, 0.7)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND', maximumFractionDigits: 0 }).format(value);
                            }
                        }
                    }
                }
            }
        });    });
</script>
@endpush
